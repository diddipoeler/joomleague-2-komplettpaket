<?php
/**
* Module mod_jl_clubicons For Joomla 1.5 and joomleague 1.5b.2
* Version: 1.5b.2
* Created by: johncage
* Created on: 21 June 2011
* 
* URL: www.yourlife.de
* License http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'joomleague.core.php');

class modJLClubiconsHelper
{
	var $project;
	var $ranking;
	var $teams = array ();
	var $params;
	var $placeholders = array (
			'logo_small' => 'images/com_joomleague/database/placeholders/placeholder_small.png',
			'logo_middle' => 'images/com_joomleague/database/placeholders/placeholder_50.png',
			'logo_big' => 'images/com_joomleague/database/placeholders/placeholder_150.png'
		);
	function __construct( &$params )
	{
		$this->params = $params;
		$this->_getData();
	}
	/**
	 * Method to get the list
	 *
	 * @access private
	 * @return array
	 */
	private function _getData()
	{
		$mainframe = JFactory::getApplication();
        if (!class_exists('JoomleagueModelRanking')) {
			require_once(JLG_PATH_SITE.DS.'models'.DS.'ranking.php');
		}
		$project_id = (JRequest::getVar('option','') == 'com_joomleague' AND 
									JRequest::getInt('p',0) > 0 AND 
									$this->params->get('usepfromcomponent',0) == 1 ) ? 
									JRequest::getInt('p') : $this->params->get('project_ids');
		if (is_array($project_id)) { $project_id = $project_id[0];}
		
        if ( $project_id )
        {
        $model = &JLGModel::getInstance('project', 'JoomleagueModel');
		$model->setProjectId($project_id);

		$this->project = &$model->getProject();

		$ranking = &JLGRanking::getInstance($this->project);
		$ranking->setProjectId($project_id);
		$divisionid = explode(':', $this->params->get('division_id', 0));
		$divisionid = $divisionid[0];
		$this->ranking   = $ranking->getRanking(null, null, $divisionid);

		if ($this->params->get( 'logotype' ) == 'logo_small')
		{
			$teams = $model->getTeamsIndexedByPtid();
		}
		else //get the teams cause we don't have logo_middle and big in ranking model's getTeams:
		{
			
			unset($model);
			if (!class_exists('JoomleagueModelTeams')) {
				require_once(JLG_PATH_SITE.DS.'models'.DS.'teams.php');
			}
			
			$model = &JLGModel::getInstance('teams', 'JoomleagueModel');
			$model->setProjectId($project_id);
			$teams = $model->getTeams($divisionid);
		}
		
		$this->buildData($teams);
		unset($teams);
		unset($model);
        }

	}
	function buildData( &$result )
	{
		if (count($result))
		{
			foreach($result as $r)
			{
				$this->teams[$r->projectteamid] = array();
				$this->teams[$r->projectteamid]['link'] = $this->getLink( $r );
				$class = (!empty($this->teams[$r->projectteamid]['link'])) ? 'smstarticon' : 'smstarticon nolink';
				$this->teams[$r->projectteamid]['logo'] = $this->getLogo( $r, $class );
			}
		}
	}
	function getLogo( & $item, $class )
	{
		$imgtype = $this->params->get( 'logotype','logo_middle' );
		$logourl = (!empty($item->$imgtype) AND file_exists(JPATH_ROOT.DS.str_replace('/', DS, $item->$imgtype)))
			? $item->$imgtype : $this->placeholders[$imgtype];
		//echo $logourl.'<br />';
		$imgtitle = JText::_('View ') . $item->team_name;
		return JHTML::image($logourl, $item->team_name,'border="0" class="'.$class.'" title="'.$imgtitle.'"');
	}
	function getLink( &$item )
	{
		switch ($this->params->get('teamlink'))
		{
			case 0:
				return '';
			case 1:
				return JoomleagueHelperRoute::getTeamInfoRoute($this->project->slug, $item->team_slug);
			case 2:
				return JoomleagueHelperRoute::getPlayersRoute($this->project->slug, $item->team_slug);
			case 3:
				return JoomleagueHelperRoute::getTeamPlanRoute($this->project->slug, $item->team_slug);
			case 4:
				return JoomleagueHelperRoute::getClubInfoRoute($this->project->slug, $item->club_slug);
			case 5:
				return (isset($item->club_www)) ? $item->club_www : $item->website;
		}
	}
}