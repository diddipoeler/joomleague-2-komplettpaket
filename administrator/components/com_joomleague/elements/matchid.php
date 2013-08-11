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

defined('_JEXEC') or die('Restricted access');

class JFormFieldmatchid extends JFormField
{

	protected $type = 'matchid';

	function getInput()
  {
		$db = &JFactory::getDBO();
    $mainframe			=& JFactory::getApplication();
		$option				= 'com_joomleague';
		$prediction_id		= (int) $mainframe->getUserState( $option . 'prediction_id' );

//$mainframe->enqueueMessage(JText::_('prediction_id -> <pre> '.print_r($prediction_id,true).'</pre><br>' ),'Notice');		

    $query=	"	SELECT	m.id AS id,
								m.match_date,
								r.roundcode,
								r.name as roundname,
								t1.name as home,
								t2.name as away

						FROM #__joomleague_match AS m
						INNER JOIN #__joomleague_round AS r 
            ON r.id = m.round_id
            inner join #__joomleague_prediction_project as prepro
            on prepro.project_id = r.project_id
            LEFT JOIN #__joomleague_project_team AS tt1 ON m.projectteam1_id=tt1.id
			LEFT JOIN #__joomleague_project_team AS tt2 ON m.projectteam2_id=tt2.id
			LEFT JOIN #__joomleague_team AS t1 ON t1.id=tt1.team_id
			LEFT JOIN #__joomleague_team AS t2 ON t2.id=tt2.team_id
            where prepro.prediction_id = " . $prediction_id;
					
		//$query = 'SELECT t.id, t.name FROM #__joomleague_team t ORDER BY name';
		$db->setQuery( $query );
		$teams = $db->loadObjectList();

//$mainframe->enqueueMessage(JText::_('teams -> <pre> '.print_r($teams,true).'</pre><br>' ),'Notice');
		
    //$mitems = array(JHTML::_('select.option', '-1', '- '.JText::_('Do not use').' -'));
    $mitems = array();
    
		foreach ( $teams as $team ) {
			$mitems[] = JHTML::_('select.option',  $team->id, '&nbsp;'.$team->match_date. ' ( '.$team->roundname.' ) ' . ' -> [ ' .$team->home .' - '.  $team->away . ' ] ' );
		}
		
		$output= JHTML::_('select.genericlist',  $mitems, ''.$control_name.'['.$name.'][]', 'class="inputbox" size="50" multiple="multiple" ', 'value', 'text', $value );
		return $output;
	}
}
 