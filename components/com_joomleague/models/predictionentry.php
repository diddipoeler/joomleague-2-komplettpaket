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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once('prediction.php');

/**
 * Joomleague Component prediction Entry Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100628
 */
class JoomleagueModelPredictionEntry extends JoomleagueModelPrediction
{

	function __construct()
	{
		parent::__construct();
	}

  function getDebugInfo()
  {
  $show_debug_info = JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0);
  if ( $show_debug_info )
  {
  return true;
  }
  else
  {
  return false;
  }
  
  }
  
	function newMemberCheck()
	{
		if ($this->isNewMember==0){return false;}else{return true;}
	}

	function tippEntryDoneCheck()
	{
		if ($this->tippEntryDone==0){return false;}else{return true;}
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
		$app = JFactory::getApplication('site');
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
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'predictionentry', $prefix = 'table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	function store($data)
{
        $option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
        
        //$mainframe->enqueueMessage(JText::_('PredictionEntry Task -> <pre>'.print_r($data,true).'</pre>'),'');
        
        // get the table
        $row =& $this->getTable();
 
        // Bind the form fields to the hello table
        if (!$row->bind($data)) {
                $this->setError($this->_db->getErrorMsg());
                return false;
        }
 
        // Make sure the hello record is valid
        if (!$row->check()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
        }
 
        // Store the web link table to the database
        if (!$row->store()) {
                $this->setError( $row->getErrorMsg() );
                return false;
        }
 
        return true;         
}

	function createHelptText($gameMode=0)
	{
  //$option = JRequest::getCmd('option').'_';
		$gameModeStr = ($gameMode==0) ? JText::_('COM_JOOMLEAGUE_PRED_ENTRY_STANDARD_MODE') : JText::_($option.'JL_PRED_ENTRY_TOTO_MODE');

		$helpText = '<hr><h3>'.JText::_('COM_JOOMLEAGUE_PRED_ENTRY_HELP_TITLE').'</h3>';

		$helpText .= '<ul>';
			$helpText .= '<li>';
				$helpText .= JText::sprintf('COM_JOOMLEAGUE_PRED_ENTRY_HELP_01','<b>'.$gameModeStr.'</b>');
			$helpText .= '</li>';
			$helpText .= '<li>';
				$helpText .= JText::_('COM_JOOMLEAGUE_PRED_ENTRY_HELP_02');
			$helpText .= '</li>';
			$helpText .= '<li>';
				$helpText .= JText::_('COM_JOOMLEAGUE_PRED_ENTRY_HELP_03');
			$helpText .= '</li>';
			$helpText .= '<li>';
				$helpText .= JText::_('COM_JOOMLEAGUE_PRED_ENTRY_HELP_04');
			$helpText .= '</li>';
		$helpText .= '</ul>';
		$helpText .= '<hr>';

		return $helpText;
	}

	function getTippCountHome($predictionProjectID,$matchID)
	{
		$query = 'SELECT count(tipp) FROM #__joomleague_prediction_result
					WHERE	prediction_id = ' . intval( $this->predictionGameID ) . ' AND
							project_id = ' . intval( $predictionProjectID ) . ' AND
							match_id = ' . intval( $matchID ) . ' AND
							tipp = 1';
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		return $result;
	}

	function getTippCountDraw($predictionProjectID,$matchID)
	{
		$query = 'SELECT count(tipp) FROM #__joomleague_prediction_result
					WHERE	prediction_id = ' . intval( $this->predictionGameID ) . ' AND
							project_id = ' . intval( $predictionProjectID ) . ' AND
							match_id = ' . intval( $matchID ) . ' AND
							tipp = 0';
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		return $result;
	}

	function getTippCountAway($predictionProjectID,$matchID)
	{
		$query = 'SELECT count(tipp) FROM #__joomleague_prediction_result
					WHERE	prediction_id = ' . intval( $this->predictionGameID ) . ' AND
							project_id = ' . intval( $predictionProjectID ) . ' AND
							match_id = ' . intval( $matchID ) . ' AND
							tipp = 2';
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		return $result;
	}

	function getTippCountTotal($predictionProjectID,$matchID)
	{
		$query = 'SELECT count(tipp) FROM #__joomleague_prediction_result
					WHERE	prediction_id = ' . intval( $this->predictionGameID ) . ' AND
							project_id = ' . intval( $predictionProjectID ) . ' AND
							match_id = ' . intval( $matchID );
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		return $result;
	}

	function getMatchesDataForPredictionEntry($predictionGameID,$predictionProjectID,$projectRoundID,$userID,$match_ids)
	{
		    
    $query = 	"	SELECT	m.id,
								m.round_id,
								m.match_date,
								m.projectteam1_id,
								m.projectteam2_id,
								m.team1_result,
								m.team2_result,
								m.team1_result_decision,
								m.team2_result_decision,
								r.id AS roundcode,
								pr.tipp,
								pr.tipp_home,
								pr.tipp_away,
								pr.joker,
								pr.id AS prid

						FROM #__joomleague_match AS m
						INNER JOIN #__joomleague_round AS r ON	r.id=m.round_id AND
																r.project_id=$predictionProjectID AND
																r.id=$projectRoundID

						LEFT JOIN #__joomleague_prediction_game AS pg ON pg.id=$predictionGameID

						LEFT JOIN #__joomleague_prediction_result AS pr ON	pr.prediction_id=$predictionGameID AND
																			pr.user_id=$userID AND
																			pr.project_id=$predictionProjectID AND
																			pr.match_id=m.id
						WHERE	m.published=1 AND
								m.match_date <> '0000-00-00 00:00:00'
						AND (m.cancel IS NULL OR m.cancel = 0)";
						
		if ( $match_ids )
    {
    $convert = array (
      '|' => ','
        );
    $match_ids = str_replace(array_keys($convert), array_values($convert), $match_ids );
    $query .= "AND m.id IN (" . $match_ids . ")";    
    }
    
    $query .= "ORDER BY m.match_date ASC";
    				
		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();
		return $results;
	}

	function savePredictions($allowedAdmin=false)
	{
		global $mainframe, $option;
    $document	=& JFactory::getDocument();
    $mainframe	=& JFactory::getApplication();

    $result	= true;
		
		$show_debug = $this->getDebugInfo();
		

		$post	= JRequest::get('post');
		
		if ( $show_debug )
		{
    echo '<br />savePredictions post<pre>~' . print_r($post,true) . '~</pre><br />';
    }
    
    //$mainframe->enqueueMessage(JText::_('post -> <pre> '.print_r($post,true).'</pre><br>' ),'Notice');

		$pids	= JRequest::getVar('pids',array(),'post','array');
		JArrayHelper::toInteger($pids);

		$cids	= JRequest::getVar('cids',	array(),'post','array');
		$prids	= JRequest::getVar('prids',	array(),'post','array');
		$homes	= JRequest::getVar('homes',	array(),'post','array');
		$aways	= JRequest::getVar('aways',	array(),'post','array');
		$tipps	= JRequest::getVar('tipps',	array(),'post','array');
		$jokers	= JRequest::getVar('jokers',array(),'post','array');
		$mID	= JRequest::getVar('memberID',0,'post','int');
		
    $RoundID	= JRequest::getVar('r',0,'post','int');
    $ProjectID	= JRequest::getVar('pjID',0,'post','int');
		
		
		//echo '<br /><pre>~' . print_r($jokers,true) . '~</pre><br />';

		$predictionGameID	= JRequest::getVar('prediction_id',	'','post','int');
		$joomlaUserID		= JRequest::getVar('user_id',		'','post','int');

    //$mainframe->enqueueMessage(JText::_('predictionGameID -> '.$predictionGameID),'');
    //$mainframe->enqueueMessage(JText::_('joomlaUserID -> '.$joomlaUserID),'');
    //$mainframe->enqueueMessage(JText::_('predictionMemberID -> '.$mID),'');
    
    
    // _predictionMember
    $configavatar			= JoomleagueModelPrediction::getPredictionTemplateConfig('predictionusers');
    $predictionMemberInfo = $this->getPredictionMember($configavatar);
    //$mainframe->enqueueMessage(JText::_('predictionMemberInfo -> <pre> '.print_r($predictionMemberInfo,true).'</pre><br>' ),'Notice');
    
    //$mainframe->enqueueMessage(JText::_('predictionMember reminder -> '.$predictionMemberInfo->reminder),'');
    //$mainframe->enqueueMessage(JText::_('predictionMember email -> '.$predictionMemberInfo->email),'');
    
		$changedResultArray	= array();

		for ($x=0; $x < count($pids); $x++)
		{
			for ($y=0; $y < count($cids[$pids[$x]]); $y++)
			{
				//echo 'PredictionGameID:~'.$predictionGameID.'~ ';

				$dProjectID = $pids[$x];
				//echo 'PredictionProjectID:~'.$dProjectID.'~ ';

				$dMatchID = $cids[$pids[$x]][$y];
				//echo 'MatchID:~'.$dMatchID.'~ ';

				$dprID = $prids[$pids[$x]][$dMatchID];
				//echo 'prID:~'.$dprID.'~ ';

				$dHome = $homes[$pids[$x]][$cids[$pids[$x]][$y]]; $tmp_dHome = $dHome;
				if ((!isset($homes[$pids[$x]][$cids[$pids[$x]][$y]]))||(trim($dHome==''))){$dHome = "NULL";}else{$dHome = "'".$dHome."'";}
				//echo 'Home:~'.$dHome.'~ ';

				$dAway = $aways[$pids[$x]][$cids[$pids[$x]][$y]]; $tmp_dAway = $dAway;
				if ((!isset($aways[$pids[$x]][$cids[$pids[$x]][$y]]))||(trim($dAway==''))){$dAway = "NULL";}else{$dAway = "'".$dAway."'";}
				//echo 'Away:~'.$dAway.'~ ';

				/*
				$dJoker = (	isset($jokers[$pids[$x]][$cids[$pids[$x]][$y]]) &&
							!empty($jokers[$pids[$x]][$cids[$pids[$x]][$y]])) ? "'1'" : 'NULL';
				*/
				$dJoker = (isset($jokers[$pids[$x]][$cids[$pids[$x]][$y]])) ? "'1'" : 'NULL';
				//echo 'Joker:~'.$dJoker.'~ ';

				$dTipp = $tipps[$pids[$x]][$cids[$pids[$x]][$y]]; $tmp_dTipp = $dTipp;
				if ((!isset($tipps[$pids[$x]][$cids[$pids[$x]][$y]]))||(trim($dTipp==''))){$dTipp = "NULL";}else{$dTipp = "'".$dTipp."'";}
				//echo 'Tipp:~'.$dTipp.'~ ';
				//echo '<br />';

				if 	(
						(
							(isset($homes[$pids[$x]][$cids[$pids[$x]][$y]])) &&
							(trim($dHome)!="NULL") &&

							(isset($aways[$pids[$x]][$cids[$pids[$x]][$y]])) &&
							(trim($dAway) != "NULL")
						) ||
						($dTipp!="NULL")
					)
				{

					if ($dTipp=="NULL")
					{
						if ($tmp_dHome > $tmp_dAway){$dTipp = "'1'";}elseif($tmp_dHome < $tmp_dAway){$dTipp = "'2'";}else{$dTipp = "'0'";}
					}

					if (!empty($dprID))
					{
						$query =	"	UPDATE #__joomleague_prediction_result
										SET
											tipp=$dTipp,
											tipp_home=$dHome,
											tipp_away=$dAway,
											joker=$dJoker
										WHERE id='$dprID'
									";
					}
					else
					{
						$query = "INSERT IGNORE INTO #__joomleague_prediction_result
									(
										prediction_id,
										user_id,
										project_id,
										match_id,
										tipp,
										tipp_home,
										tipp_away,
										joker
									)
									VALUES
									(
										'$predictionGameID',
										'$joomlaUserID',
										'$dProjectID',
										'$dMatchID',
										$dTipp,
										$dHome,
										$dAway,
										$dJoker
									)";
					}
					//echo $query . '<br />';
					/**/
					$this->_db->setQuery( $query );
					if ( !$this->_db->query() )
					{
						$this->setError( $this->_db->getErrorMsg() );
						$result= false;
						//echo '<br />ERROR~' . $query . '~<br />';
					}
					/**/
				}
				else
				{
					$query = 'DELETE FROM #__joomleague_prediction_result WHERE prediction_id=' . $predictionGameID;
					$query .= ' AND user_id=' . $joomlaUserID;
					$query .= ' AND project_id=' . $pids[$x];
					$query .= ' AND match_id=' . $cids[$pids[$x]][$y];
					//echo '<br />~' . $query . '~<br />';
					/**/
					$this->_db->setQuery( $query );
					if( !$this->_db->query() )
					{
						$this->setError( $this->_db->getErrorMsg() );
						$result = false;
						//echo '<br />ERROR~' . $query . '~<br />';
					}
					/**/
				}
			}
		}

		$query = "UPDATE #__joomleague_prediction_member SET last_tipp='" . date('Y-m-d H:i:s') . "' WHERE id=$mID";
		//echo $query . '<br />';
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			$result = false;
			//echo '<br />ERROR~' . $query . '~<br />';
		}

    // email mit tippergebnissen senden
    if ( $predictionMemberInfo->reminder )
    {
    $this->sendMemberTipResults($mID,$predictionGameID,$RoundID,$ProjectID,$joomlaUserID);
    }
    
		return $result;
	}

}
?>