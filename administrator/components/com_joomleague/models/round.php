<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once (JPATH_COMPONENT.DS.'models'.DS.'item.php');

/**
 * Joomleague Component Matchday Model
 *
 * @author Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelRound extends JoomleagueModelItem
{

	/**
	 * Method to remove rounds of only one project
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function deleteOne($project_id)
	{
		if ($project_id > 0)
		{
			$query='DELETE FROM #__joomleague_round WHERE project_id='.$project_id;
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to remove a matchday
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function deleteMatches($cid=array(),$mdlMatches,$mdlMatch,$onlyMatches=false)
	{
		$result=false;
		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids=implode(',',$cid);
			for ($r=0; $r < count($cid); $r++)
			{
				//echo "Deleting Round: ".$cid[$r]."<br>";
				$matches=$mdlMatches->getMatchesByRound($cid[$r]);
				$matchids=array();
				for ($m=0; $m < count($matches); $m++)
				{
					$matchids[]=$matches[$m]->id;
					//echo "  Deleting Match: ".$matches[$m]->id."<br>";
				}
				$mdlMatch->delete($matchids);
			}
			if (!$onlyMatches)
			{
				return parent::delete($cids);
			}
		}
		return true;
	}

	/**
	 * Method to load content matchday data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query='SELECT * FROM #__joomleague_round WHERE id='.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_data=$this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the matchday data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$matchday					= new stdClass();
			$matchday->id				= 0;
			$matchday->name				= null;
			$matchday->roundcode		= 0;
			$matchday->round_date_first	= null;
			$matchday->round_date_last	= null;
			$matchday->project_id		= null;
			$matchday->checked_out		= 0;
			$matchday->checked_out_time	= 0;
			$matchday->modified			= null;
			$matchday->modified_by		= null;
			
			$this->_data				= $matchday;

			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	* Method to assign teams of an existing project to a copied project
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function cpCopyRounds($post)
	{
		$old_id=(int)$post['old_id'];
		$project_id=(int)$post['id'];

		$query='SELECT * 
				FROM #__joomleague_round 
				WHERE project_id='.$old_id.'
				ORDER BY id ASC';
		$this->_db->setQuery($query);
		if ($results=$this->_db->loadAssocList())
		{
			foreach($results as $result)
			{
				$p_round =& $this->getTable();
				$p_round->bind($result);
				$p_round->set('id',NULL);
				$p_round->set('project_id',$project_id);
				if (!$p_round->store())
				{
					echo $this->_db->getErrorMsg();
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Method to update checked rounds
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function storeshort($cid,$post)
	{
		$result=true;
		for ($x=0; $x < count($cid); $x++)
		{
			$query="	UPDATE	#__joomleague_round
						SET roundcode='" .		$post['roundcode' .			$cid[$x]]."',
							name='" .		$post['name' .				$cid[$x]]."',
							round_date_first='" .	$post['round_date_first' .	$cid[$x]]."',
							round_date_last='" .	$post['round_date_last' .	$cid[$x]]."',
							checked_out=		0,
							checked_out_time =	0
							WHERE id=".$cid[$x];

			$this->_db->setQuery($query);
			if(!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				$result=false;
			}
		}
		return $result;
	}

	function getMaxRound($project_id)
	{
		$result=0;
		if ($project_id > 0)
		{
			$query='SELECT COUNT(roundcode) 
					FROM #__joomleague_round 
					WHERE project_id='.(int) $project_id;
			$this->_db->setQuery($query);
			$result=$this->_db->loadResult();
		}
		return $result;
	}
	
	/**
	 * 
	 * @param $roundid
	 */
	function getRoundcode($roundid)
	{
		$query='SELECT roundcode
				FROM #__joomleague_round 
				WHERE id='.(int) $roundid;
		$this->_db->setQuery($query);
		$result=$this->_db->loadResult();
		return $result;
	}
	
	/**
	 * 
	 * @param $roundcode
	 * @param $project_id
	 */
	function getRoundId($roundcode, $project_id)
	{
		$query='SELECT id
				FROM #__joomleague_round 
				WHERE roundcode='.$roundcode.'
				  AND project_id='.(int) $project_id;
		$this->_db->setQuery($query);
		$result=$this->_db->loadResult();
		return $result;
	}
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'round', $prefix = 'table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.7
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
				array('load_data' => $loadData) );
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.7
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomleague.edit.'.$this->name.'.data', array());
		if (empty($data))
		{
			$data = $this->getData();
		}
		return $data;
	}
}
?>