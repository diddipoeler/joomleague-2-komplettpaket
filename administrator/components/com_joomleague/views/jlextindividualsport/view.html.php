<?php
/**
 * @copyright	Copyright (C) 2006-2012 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomleague component
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */

class JoomleagueViewjlextindividualsport extends JLGView
{
	function display($tpl=null)
	{
		$mainframe =& JFactory::getApplication();

		if ($this->getLayout()=='default')
		{
			$this->_displayDefault($tpl);
			return;
		}

		parent::display($tpl);
	}

	function _displayDefault($tpl)
	{
		$option = JRequest::getCmd('option');
		$mainframe =& JFactory::getApplication();
		$uri =& JFactory::getURI();
    $cid = JRequest::getCmd('cid');
		$match_id = $cid[0];
    $model		=& $this->getModel();
    /*
		$filter_state		= $mainframe->getUserStateFromRequest($option.'mc_filter_state',	'filter_state', 	'', 'word');
		$filter_order		= $mainframe->getUserStateFromRequest($option.'mc_filter_order',	'filter_order', 	'mc.match_number', 'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'mc_filter_order_Dir','filter_order_Dir', '', 'word');
		$search				= $mainframe->getUserStateFromRequest($option.'mc_search', 'search',					'', 'string');
		$search_mode		= $mainframe->getUserStateFromRequest($option.'mc_search_mode',		'search_mode',		'', 'string');
		$division			= $mainframe->getUserStateFromRequest($option.'mc_division',		'division',			'',	'string');
		$project_id			= $mainframe->getUserState( $option . 'project' );
		$search				= JString::strtolower($search);
*/

		$matches	=& $this->get('Data');
		$total		=& $this->get('Total');
		$pagination	=& $this->get('Pagination');
		

    $match = $model->getMatchData($match_id);
    
    echo 'getModel<pre>'.print_r($model,true).'</pre><br>';
    
    
    $this->assignRef('homeplayer' , $model->HomeTeamPlayer() );
    $this->assignRef('awayplayer' , $model->AwayTeamPlayer() );
    
    $this->assignRef('matches',$matches);
    $this->assignRef('match',$match);
    $this->assignRef('pagination',$pagination);
    $this->assignRef('lists',$lists);
    $this->assignRef('request_url',$uri->toString());
    
		parent::display($tpl);
	}

}
?>