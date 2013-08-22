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
 * Joomleague Component prediction Results Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueModelPredictionResults extends JoomleagueModelPrediction
{

  var $predictionGameID = 0;
    
   /**
   * Items total
   * @var integer
   */
  var $_total = null;
 
  /**
   * Pagination object
   * @var object
   */
  var $_pagination = null;
  
  var $config = array();
  var $configavatar = array();

  
	function __construct()
	{
		parent::__construct();
		
        $this->pggrouprank			= JRequest::getInt('pggrouprank',		0);
		$option = JRequest::getCmd('option');    
    $mainframe = JFactory::getApplication();
    $this->predictionGameID	= JRequest::getInt('prediction_id',0);

if ( JRequest::getVar( "view") == 'predictionresults' )
{    
	// Get pagination request variables
	$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
 
	// In case limit has been changed, adjust it
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
	$this->setState('limit', $limit);
	$this->setState('limitstart', $limitstart);
}
//$mainframe->enqueueMessage(JText::_('PredictionResults __construct limit -> '.'<pre>'.print_r($limit ,true).'</pre>' ),'');
//$mainframe->enqueueMessage(JText::_('PredictionResults __construct view-> '.'<pre>'.print_r(JRequest::getVar( "view"),true).'</pre>' ),''); 
    
	}

  function getPagination()
  {
 	// Load the content if it doesn't already exist
 	if (empty($this->_pagination)) 
     {
 	    jimport('joomla.html.pagination');
 	    $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
 	}
 	return $this->_pagination;
  }    
  
  
  function getTotal()
  {
 	// Load the content if it doesn't already exist
 	if (empty($this->_total)) 
     {
 	    //$query = $this->_buildQuery();
        $query = $this->getPredictionMembersList($this->config,$this->configavatar,true);
 	    $this->_total = $this->_getListCount($query);	
 	}
 	return $this->_total;
  }
  
  function getData() 
  {
 	// if data hasn't already been obtained, load it
 	if (empty($this->_data)) 
     {
 	    //$query = $this->_buildQuery();
        $query = $this->getPredictionMembersList($this->config,$this->configavatar,true);
 	    $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));	
 	}
 	return $this->_data;
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
  
	function getMatches($roundID,$project_id,$match_ids)
	{
	  global $mainframe, $option;
    $document	=& JFactory::getDocument();
    $mainframe	=& JFactory::getApplication();
    
		if ($roundID==0){
			$roundID=1;
		}
		$query = 	"	SELECT	m.id AS mID,
								m.match_date,
								m.team1_result AS homeResult,
								m.team2_result AS awayResult,
								m.team1_result_decision AS homeDecision,
								m.team2_result_decision AS awayDecision,
								t1.name AS homeName,
								t2.name AS awayName,
                                t1.short_name AS homeShortName,
                                t2.short_name AS awayShortName,
								c1.logo_small AS homeLogo,
								c2.logo_small AS awayLogo,
								c1.country AS homeCountry,
								c2.country AS awayCountry

						FROM #__joomleague_match AS m

						INNER JOIN #__joomleague_round AS r ON	r.id=m.round_id AND
																r.project_id=$project_id AND
																r.id=$roundID
						LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id
						LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id
						LEFT JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id
						LEFT JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id
						LEFT JOIN #__joomleague_club AS c1 ON c1.id=t1.club_id
						LEFT JOIN #__joomleague_club AS c2 ON c2.id=t2.club_id
						WHERE (m.cancel IS NULL OR m.cancel = 0)
						AND m.published=1 ";
						
    
    if ( $match_ids )
    {
    $convert = array (
      '|' => ','
        );
    $match_ids = str_replace(array_keys($convert), array_values($convert), $match_ids );
    $query .= "AND m.id IN (" . $match_ids . ")";    
    }
    
    $query .= " ORDER BY m.match_date, m.id ASC";
    
    //$mainframe->enqueueMessage(JText::_('query -> <pre> '.print_r($query,true).'</pre><br>' ),'Notice');
    						
		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		return $results;
	}

	function showClubLogo($clubLogo,$teamName)
	{
	  $mainframe = JFactory::getApplication();
		$document	=& JFactory::getDocument();
		$uri = JFactory :: getURI();
    $option = JRequest::getCmd('option');
    $optiontext = strtoupper(JRequest::getCmd('option').'_');
    
		$output = '';
		if ((!isset($clubLogo)) || ($clubLogo=='') || (!file_exists($clubLogo)))
		{
			$clubLogo='images/com_joomleague/database/placeholders/placeholder_small.gif';
		}
		$imgTitle = JText::sprintf('COM_JOOMLEAGUE_PRED_RESULTS_LOGO_OF',$teamName);
		$output .= JHTML::image($clubLogo,$imgTitle,array(' height' => 17, ' title' => $imgTitle));
		return $output;
	}

}
?>