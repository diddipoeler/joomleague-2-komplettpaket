<?php
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once ( JPATH_COMPONENT . DS . 'models' . DS . 'item.php' );

/**
 * Joomleague Component Prediction template Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100625
 */
class JoomleagueModelPredictionTemplate extends JoomleagueModelItem
{
	/**
	 * Method to load content template data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_data ) )
		{
			$query = '	SELECT *
						FROM #__joomleague_prediction_template
						WHERE id = ' . (int) $this->_id;

			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the template data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_data ) )
		{
			$template					= new stdClass();
			$template->id				= 0;
			$template->title			= '';
			$template->prediction_id	= 0;
			$template->template			= '';
			$template->params			= null;
			$template->published		= 1;
			$template->checked_out		= 0;
			$template->checked_out_time	= 0;
			$this->_data				= $template;

			return (boolean) $this->_data;
		}
		return true;
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
	
	/**
	 * Method to (un)publish a template
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5.0a
	 */
	function publish( $cid = array(), $publish = 1 )
	{
		$user =& JFactory::getUser();
		if ( count( $cid ) )
		{
			JArrayHelper::toInteger( $cid );
			$cids = implode( ',', $cid );

			$query =	'	UPDATE #__joomleague_prediction_template
							SET published = ' . (int) $publish . '
							WHERE id IN ( ' . $cids . ' )
							AND ( checked_out = 0 OR ( checked_out = ' . (int) $user->get( 'id' ) . ' ) )';

			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to remove selected items
	 * from #__joomleague_prediction_template
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */

	function delete( $cid = array() )
	{
		if ( count( $cid ) )
		{
			JArrayHelper::toInteger( $cid );
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__joomleague_prediction_template WHERE id IN ( ' . $cids . ' )';
//echo $query . '<br />'; return true;
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
		}
		return true;
	}

	/**
	* Method to return a prediction game item array
	*
	* @access  public
	* @return  object
	*/
	function getPredictionGame( $id )
	{
		$query = '	SELECT	*
					FROM #__joomleague_prediction_game
					WHERE id = ' . (int) $id;

		$this->_db->setQuery( $query );
		if ( !$result = $this->_db->loadObject() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		else
		{
			return $result;
		}
	}

}
?>