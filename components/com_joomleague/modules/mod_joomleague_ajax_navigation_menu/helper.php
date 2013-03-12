<?php
/**
* @version $Id: helper.php 4905 2010-01-30 08:51:33Z and_one $
* @package Joomleague
* @subpackage navigation_menu
* @copyright Copyright (C) 2009  JoomLeague
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see _joomleague_license.txt
*/

// no direct access

defined('_JEXEC') or die('Restricted access');


class modJoomleagueAjaxNavigationMenuHelper {
	
	protected $_params;
	protected $_db;

// 	protected $_project_id;
// 	protected $_team_id;
// 	protected $_division_id = 0;
// 	protected $_round_id = null;

	var $_project_id;
    var $_league_id;
	var $_team_id;
	var $_club_id;
	var $_division_id = 0;
	var $_round_id = null;
	
	protected $_teamoptions;
	
	protected $_project;
	
	public function __construct($params)
	{
		$this->_params = $params;
		$this->_db = Jfactory::getDBO();
		
//		$this->_division_id   	= JRequest::getVar('jlamdivisionid',0,'default','POST');
		
		/*
		if (JRequest::getCmd('option') == 'com_joomleague') {
			$p = JRequest::getInt('p', $params->get('default_project_id'));
		}
		else {
			$p = $params->get('default_project_id');
		}
		*/
		
		//$this->_project_id 		= intval($p);
		
		/*
		$this->_project_id 		= JRequest::getInt('p');
		$this->_project 		= $this->getProject();
		$this->_round_id   		= JRequest::getInt('r');
		$this->_division_id   	= JRequest::getInt('division',0);
		$this->_team_id   		= JRequest::getInt('tid',0);
		*/
		
		/*
    echo 'project int -> <pre>'.print_r(JRequest::getInt('p'),true).'</pre><br>';
		echo 'project var -> <pre>'.print_r(JRequest::getVar('p'),true).'</pre><br>';
		echo 'project view -> <pre>'.print_r(JRequest::getVar('view'),true).'</pre><br>';
		echo 'project tid -> <pre>'.print_r(JRequest::getVar('tid'),true).'</pre><br>';	
		
		echo 'get -> <pre>'.print_r($_GET,true).'</pre><br>';	
		echo 'post -> <pre>'.print_r($_POST,true).'</pre><br>';	
		echo 'request -> <pre>'.print_r($_REQUEST,true).'</pre><br>';
		*/
    
    
        
    if ( $this->_project_id )
		{
    JRequest::setVar( 'jlamseason', $this->getSeasonId() );
    JRequest::setVar( 'jlamleague', $this->getLeagueId() );
    JRequest::setVar( 'jlamproject', $this->_project_id );
    JRequest::setVar( 'jlamdivisionid', $this->_division_id );
    }
    
		
		
		
	}
	
	public function getQueryValues()
	{
	// diddipoeler
	/*
	muss ich erstmal so machen, da die request variablen falsch 
	uebernommen werden.
	kommt vor, wenn das modul nach einen anderen modul dargestellt wird
	*/
	
	$url = $_SERVER["REQUEST_URI"]; 
    $parsearray = parse_url($url);
    $startseo = 0;
    $jltemplate = '';
    
    if ( $parsearray['query'] )
    {
    $varAdd = explode('&', $parsearray['query']);
        foreach($varAdd as $varOne)
        {
            $name_value = explode('=', $varOne);
            
            $varAdd_array[$name_value[0]] = $name_value[1];
       }
       
    }
    else
    {
    $varAdd = explode('/', $parsearray['path']);    
    
    foreach( $varAdd as $key => $value )
    {
    
    if ( $value == 'joomleague' )
    {
    $startseo = $key + 1;
    $jltemplate = $varAdd[$startseo];
    
    //echo 'jltemplaterequest queries -> <pre>'.print_r($jltemplate,true).'</pre><br>';
    
    switch ($jltemplate)
    {
    case 'clubinfo':
    case 'clubplan':
    $varAdd_array['p'] = $varAdd[$startseo + 1];
    $varAdd_array['cid'] = $varAdd[$startseo + 2];
    break;
    
    case 'roster':
    case 'teamplan':
    case 'teaminfo':
    case 'teamstats':
    case 'curve':
    $varAdd_array['p'] = $varAdd[$startseo + 1];
    $varAdd_array['tid'] = $varAdd[$startseo + 2];
    break;
    
    case 'ranking':
    $varAdd_array['p'] = $varAdd[$startseo + 1];
    $varAdd_array['r'] = $varAdd[$startseo + 3];
    break;
    
    case 'results':
    case 'resultsmatrix':
    case 'resultsranking':
    case 'matchreport':
    $varAdd_array['p'] = $varAdd[$startseo + 1];
    $varAdd_array['r'] = $varAdd[$startseo + 2];
    break;
    
    case 'eventsranking':
    case 'matrix':
    case 'referees':
    case 'stats':
    case 'statsranking':
    $varAdd_array['p'] = $varAdd[$startseo + 1];
    break;
        
    }   
     
    }   
     
    }   
     
    }  
       
//     echo 'request queries -> <pre>'.print_r($varAdd_array,true).'</pre><br>';
	  
      
      
      return $varAdd_array;
	}
	
	public function setProject($project_id,$team_id,$division_id)
	{
	$this->_project_id 		= $project_id;
	//$this->_division_id = $division_id;
	//$this->_team_id 		= $team_id;
	$this->_project 		= $this->getProject();
	$this->_round_id   		= $this->getCurrentRoundId();
  JRequest::setVar( 'jlamseason', $this->getSeasonId() );
  JRequest::setVar( 'jlamleague', $this->getLeagueId() );
  JRequest::setVar( 'jlamproject', $this->_project_id );
  //JRequest::setVar( 'jlamteamid', $this->_team_id );
  //JRequest::setVar( 'jlamdivisionid', $this->_division_id );
	}
	
	public function setDivisionID($division_id)
	{
	
	$this->_division_id = $division_id;

// echo 'setDivisionID <br>';
// echo '_division_id ->'.$this->_division_id.'<br>';
	
	}
	
	public function setTeamID($team_id)
	{
	
	$this->_team_id = $team_id;

// echo 'setTeamID <br>';
// echo '_team_id ->'.$this->_team_id.'<br>';
	
	}
	
	
  public function getCurrentRoundId()
	{
		if ($this->getProject()) {
			return $this->getProject()->current_round;
		}
		else {
			return 0;
		}
	}
  
  public function getSeasonId()
	{
		if ($this->getProject()) {
			return $this->getProject()->season_id;
		}
		else {
			return 0;
		}
	}
	
	public function getLeagueId()
	{
		if ($this->getProject()) {
		  $this->_league_id = $this->getProject()->league_id;
			return $this->getProject()->league_id;
		}
		else {
			return 0;
		}
	}

	public function getDivisionId()
	{
		return $this->_division_id;
	}

  public function getClubId()
	{
		$query = ' SELECT club_id  '
				. ' FROM #__joomleague_team where id = '. $this->_team_id
				;
		$this->_db->setQuery($query);
		$res = $this->_db->loadResult();
    
    if ( $res )
    {
    $this->_club_id = $res;
    return $this->_club_id;
    }
    else
    {
    return false;
    }
    
    
	}
	
	public function getFavTeams($project_id)
	{
  
  $query = ' SELECT fav_team  '
				. ' FROM #__joomleague_project where id = '. $project_id 
				;
		$this->_db->setQuery($query);
		$teams = $this->_db->loadResult();
		
    //echo 'teams -><pre>'.print_r($teams,true).'</pre><br>';
		
		if ( $teams )
		{
    $query = " SELECT t.id as team_id, t.name, t.club_id  "
				. " FROM #__joomleague_team as t
         where t.id in (". $teams . ")"
				;
		
    //echo 'query -><pre>'.print_r($query,true).'</pre><br>';	
    	
				$this->_db->setQuery($query);
				$res = $this->_db->loadObjectList();
				return $res;
    }
    else
    {
    return false;
    }
    
    
  }
  
  
	public function getTeamId($project_id,$club_id)
	{
		$query = ' SELECT pt.team_id  '
				. ' FROM #__joomleague_project_team as pt
        inner join #__joomleague_team as t
        on t.id = pt.team_id
         where pt.project_id = '. $project_id .' and t.club_id = '.$club_id
				;
		$this->_db->setQuery($query);
		$res = $this->_db->loadResult();
    
    if ( $res )
    {
    $this->_team_id = $res;
    return $this->_team_id;
    }
    else
    {
    return false;
    }
    
    
//     return $this->_team_id;
	}
	
	/**
	 * returns the selector for season
	 *
	 * @return string html select
	 */
	public function getSeasonSelect()
	{
		$options = array(JHTML::_('select.option', 0, JText::_($this->getParam('seasons_text'))));
		$query = ' SELECT s.id AS value, s.name AS text '
				. ' FROM #__joomleague_season AS s ORDER by s.name'
				;
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		if ($res) {
			$options = array_merge($options, $res);
		}
		//return JHTML::_('select.genericlist', $options, 's', 'class="jlajaxmenu-select" onchange="jlamnewproject('.$module->id.');"', 'value', 'text', $this->getSeasonId());
		return $options;
	}	
	
	/**
	 * returns the selector for division
	 * 
	 * @return string html select
	 */
	public function getDivisionSelect($project_id)
	{		
//		$project = $this->getProject();
//		if(!is_object($project)) return false;
//		if(!$this->_project_id && !($this->_project_id>0) && $project->project_type!='DIVISION_LEAGUE') {
//			return false;
//		}
//		$options = array(JHTML::_('select.option', 0, JText::_($this->getParam('divisions_text'))));
		$query = ' SELECT d.id AS value, d.name AS text ' 
		       . ' FROM #__joomleague_division AS d ' 
		       . ' WHERE d.project_id = ' .  $project_id 
		       . ($this->getParam("show_only_subdivisions", 0) ? ' AND parent_id > 0' : '') 
		       ;
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		if ($res) 
    {
    $options = array(JHTML::_('select.option', 0, JText::_($this->getParam('divisions_text'))));
		$options = array_merge($options, $res);
		}
//		return JHTML::_('select.genericlist', $options, 'd', 'class="jlnav-division"', 'value', 'text', $this->getDivisionId());
    return $options;
	}
	
	/**
	 * returns the selector for league
	 * 
	 * @return string html select
	 */
	public function getLeagueSelect($season)
	{		
//		$options = array(JHTML::_('select.option', 0, JText::_($this->getParam('leagues_text'))));
		$query = ' SELECT l.id AS value, l.name AS text ' 
		       . ' FROM #__joomleague_league AS l ' 
		       . ' inner join #__joomleague_project AS p '
		       . ' on p.league_id = l.id '
		       . ' inner join #__joomleague_season AS s '
		       . ' on p.season_id = s.id '
		       . ' where s.id = ' . $season .' GROUP BY l.name	ORDER BY	l.name '
		       ;
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		if ($res) 
        {
        $options = array(JHTML::_('select.option', 0, JText::_($this->getParam('leagues_text'))));
			$options = array_merge($options, $res);
		}
//		return JHTML::_('select.genericlist', $options, 'l', 'class="jlnav-select"', 'value', 'text', $this->getLeagueId());
		return $options;
	}

	/**
	 * returns the selector for project
	 * 
	 * @return string html select
	 */
	public function getProjectSelect($season_id,$league_id)
	{
//		$options = array(JHTML::_('select.option', 0, JText::_($this->getParam('text_project_dropdown'))));
		$query_base = ' SELECT p.id AS value, p.name AS text ' 
		       . ' FROM #__joomleague_project AS p ' 
		       . ' INNER JOIN #__joomleague_season AS s on s.id = p.season_id '
		       . ' INNER JOIN #__joomleague_league AS l on l.id = p.league_id '
		       . ' WHERE p.published = 1 ';
		       
      $query = $query_base;
//		if ($this->getParam('show_project_dropdown') == 'season' && $this->getProject()) 
//		{
			$query .= ' AND p.season_id = '. $season_id;
			$query .= ' AND p.league_id = '. $league_id;
//		}
		
//		switch ($this->getParam('project_ordering', 0)) 
//		{
//			case 0:
//				$query .= ' ORDER BY p.ordering ASC';				
//			break;
//			
//			case 1:
//				$query .= ' ORDER BY p.ordering DESC';				
//			break;
//			
//			case 2:
//				$query .= ' ORDER BY s.ordering ASC, l.ordering ASC, p.ordering ASC';				
//			break;
//			
//			case 3:
//				$query .= ' ORDER BY s.ordering DESC, l.ordering DESC, p.ordering DESC';				
//			break;
//			
//			case 4:
				$query .= ' ORDER BY p.name ASC';				
//			break;
//			
//			case 5:
//				$query .= ' ORDER BY p.name DESC';				
//			break;
//		}
		
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		
		if ($res) 
		{
//			switch ($this->getParam('project_include_season_name', 0))
//			{
//				case 2:
//					foreach ($res as $p)
//					{
//						$options[] = JHTML::_('select.option', $p->value, $p->text.' - '.$p->season_name);
//					}
//					break;
//				case 1:
//					foreach ($res as $p)
//					{
//						$options[] = JHTML::_('select.option', $p->value, $p->season_name .' - '. $p->text);
//					}
//					break;
//				case 0:
//				default:

$options = array(JHTML::_('select.option', 0, JText::_($this->getParam('text_project_dropdown'))));
					$options = array_merge($options, $res);
//				}
		}

//		return JHTML::_('select.genericlist', $options, 'p', 'class="jlnav-project"', 'value', 'text', $this->_project_id);
        return $options;		
	}

	/**
	 * returns the selector for teams
	 * 
	 * @return string html select
	 */
	public function getTeamSelect($project_id)
	{
//		if (!$this->_project_id) {
//			return false;
//		}
//		$options = array(JHTML::_('select.option', 0, JText::_($this->getParam('text_teams_dropdown'))));
		$res = $this->getTeamsOptions($project_id);
		if ($res) 
		{
		$options = array(JHTML::_('select.option', 0, JText::_($this->getParam('text_teams_dropdown'))));
			$options = array_merge($options, $res);
		}
//		return JHTML::_('select.genericlist', $options, 'tid', 'class="jlnav-team"', 'value', 'text', $this->getTeamId());
        return $options;		
	}
	
	/**
	 * returns select for project teams
	 * 
	 * @return string html select
	 */
	protected function getTeamsOptions($project_id)
	{
		if (empty($this->_teamoptions))
		{
//			if (!$this->_project_id) {
//				return false;
//			}
			$query = ' SELECT t.id AS value, t.name AS text ' 
		       . ' FROM #__joomleague_project_team AS pt ' 
		       . ' INNER JOIN #__joomleague_team AS t ON t.id = pt.team_id '
		       . ' WHERE pt.project_id = '.intval($project_id)
//		       . ' AND pt.division_id = '.intval($this->_division_id)
		       . ' ORDER BY t.name ASC '
		       ;
			$this->_db->setQuery($query);
			$res = $this->_db->loadObjectList();
			
			if (!$res) {
				Jerror::raiseWarning(0, $this->_db->getErrorMsg());
			}
			$this->_teamoptions = $res;			
		}
		return $this->_teamoptions;
	}
	
	/**
	 * return info for current project
	 * 
	 * @return object
	 */
	public function getProject()
	{
		if (!$this->_project)
		{
			if (!$this->_project_id) {
				return false;
			}
			
			$query = ' SELECT p.id, p.name, p.season_id, p.league_id, p.current_round ' 
			       . ' FROM #__joomleague_project AS p ' 
			       . ' WHERE id = ' . $this->_project_id;
			$this->_db->setQuery($query);
			$this->_project = $this->_db->loadObject();
		}
		return $this->_project;
	}
	
	
  public function getLinkFavTeam($view,$team_id,$club_id)
	{
	switch ($view)
		{								
				
				
			case "roster":
				
				$link = JoomleagueHelperRoute::getPlayersRoute( $this->_project_id, $team_id );
				break;
				
				
			case "teaminfo":
				
				$link = JoomleagueHelperRoute::getTeamInfoRoute( $this->_project_id, $team_id );
				break;				
				
			case "teamplan":
				
				$link = JoomleagueHelperRoute::getTeamPlanRoute( $this->_project_id, $team_id, $this->_division_id );
				break;		
				
			case "clubinfo":
				
				$this->getClubId();
				$link = JoomleagueHelperRoute::getClubInfoRoute( $this->_project_id, $club_id );
				break;
      case "clubplan":
				
				$this->getClubId();
				$link = JoomleagueHelperRoute::getClubPlanRoute( $this->_project_id, $club_id );
				break;	
      
        	
			case "teamstats":
				
				$link = JoomleagueHelperRoute::getTeamStatsRoute( $this->_project_id, $team_id );
				break;
				
			
								
			
		}
		return $link;
	
	}
  
  /**
	 * return link for specified view - allow seo consistency
	 * 
	 * @param string $view
	 * @return string url
	 */
	public function getLink($view)
	{
		if (!$this->_project_id) {
			return false;
		}
		
// echo 'getLink <br>';
// echo 'round_id ->'.$this->_round_id.'<br>';
// echo 'project_id ->'.$this->_project_id.'<br>';
// echo 'division_id ->'.$this->_division_id.'<br>';
// echo 'team_id ->'.$this->_team_id.'<br>';		
		
		switch ($view)
		{								
			case "calendar":
				$link = JoomleagueHelperRoute::getTeamPlanRoute( $this->_project_id, $this->_team_id, $this->_division_id );
				break;	
				
			case "curve":
				$link = JoomleagueHelperRoute::getCurveRoute( $this->_project_id, $this->_team_id, 0, $this->_division_id );
				break;
				
			case "eventsranking":				
				$link = JoomleagueHelperRoute::getEventsRankingRoute( $this->_project_id, $this->_division_id, $this->_team_id );
				break;

			case "matrix":
				$link = JoomleagueHelperRoute::getMatrixRoute( $this->_project_id, $this->_division_id );
				break;
				
			case "referees":
				$link = JoomleagueHelperRoute::getRefereesRoute( $this->_project_id );
				break;
				
			case "results":
				$link = JoomleagueHelperRoute::getResultsRoute( $this->_project_id, $this->_round_id, $this->_division_id );
				break;
				
			case "resultsmatrix":
				$link = JoomleagueHelperRoute::getResultsMatrixRoute( $this->_project_id, $this->_round_id, $this->_division_id  );
				break;

			case "resultsranking":
				$link = JoomleagueHelperRoute::getResultsRankingRoute( $this->_project_id, $this->_round_id, $this->_division_id  );
				break;
			
            case "rankingalltime":
            $link = JoomleagueHelperRoute::getRankingAllTimeRoute( $this->_league_id, $this->getParam('show_alltimetable_points') );
 		         break;
                 
			case "resultsrankingmatrix":
				$link = JoomleagueHelperRoute::getResultsRankingMatrixRoute( $this->_project_id, $this->_round_id, $this->_division_id  );
				break;
				
			case "roster":
				if (!$this->_team_id) {
					return false;
				}
				$link = JoomleagueHelperRoute::getPlayersRoute( $this->_project_id, $this->_team_id );
				break;
				
			case "stats":
				$link = JoomleagueHelperRoute::getStatsRoute( $this->_project_id, $this->_division_id );
				break;
				
			case "statsranking":
				$link = JoomleagueHelperRoute::getStatsRankingRoute( $this->_project_id, $this->_division_id );
				break;
				
			case "teaminfo":
				if (!$this->_team_id) {
					return false;
				}
				$link = JoomleagueHelperRoute::getTeamInfoRoute( $this->_project_id, $this->_team_id );
				break;				
				
			case "teamplan":
				if (!$this->_team_id) {
					return false;
				}
				$link = JoomleagueHelperRoute::getTeamPlanRoute( $this->_project_id, $this->_team_id, $this->_division_id );
				break;		
				
			case "clubinfo":
				if (!$this->_team_id) 
        {
					return false;
				}
				$this->getClubId();
				$link = JoomleagueHelperRoute::getClubInfoRoute( $this->_project_id, $this->_club_id );
				break;
      case "clubplan":
				if (!$this->_team_id) {
					return false;
				}
				$this->getClubId();
				$link = JoomleagueHelperRoute::getClubPlanRoute( $this->_project_id, $this->_club_id );
				break;	
        
        	
			case "teamstats":
				if (!$this->_team_id) {
					return false;
				}
				$link = JoomleagueHelperRoute::getTeamStatsRoute( $this->_project_id, $this->_team_id );
				break;
				
			case "treetonode":
				$link = JoomleagueHelperRoute::getBracketsRoute( $this->_project_id );
				break;
                
            case "jltournamenttree":
				$link = JoomleagueHelperRoute::getTournamentRoute( $this->_project_id, $this->_round_id );
				break;    
				
			case "separator":
				return false;
								
			default:
			case "ranking":
				$link = JoomleagueHelperRoute::getRankingRoute( $this->_project_id, $this->_round_id,null,null,0,$this->_division_id );
		}
		return $link;
	}
	
	/**
	 * return param value or default if not found
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	protected function getParam($name, $default = null)
	{
		return $this->_params->get($name, $default);
	}
	
	
}
?>