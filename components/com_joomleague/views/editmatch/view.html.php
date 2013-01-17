<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Joomleague Component editmatch View
 *
 * @author	JoomLeague Team
 * @package	JoomLeague
 * @since	1.5.0a
 */
class JoomleagueViewEditMatch extends JLGView
{
	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$css = 'components/com_joomleague/assets/css/tabs.css?v='.$version;
		$document->addStyleSheet($css);

		// Joomleague model
		$model	= $this->getModel();
		$user	= JFactory::getUser();
		$rankingconfig = $model->getTemplateConfig( "ranking" );

		$this->assignRef( 'project',		$model->getProject() );
		$this->assignRef( 'overallconfig',	$model->getTemplateConfig('overall') );
		$this->assignRef( 'rankingconfig',	$rankingconfig );
		$this->assignRef( 'playgrounds',	$model->getPlaygrounds( ) );
		$this->assignRef( 'match',			$model->getMatch() );
		$this->assignRef( 'team1',			$model->getTeaminfo( $this->match->projectteam1_id ) );
		$this->assignRef( 'team2',			$model->getTeaminfo( $this->match->projectteam2_id ) );
		$isAllowed = (($model->isAllowed()) || ($model->isMatchAdmin( $this->match->id, $user->id )));
		$this->assignRef( 'showediticon', 	$isAllowed );
		//echo '<br /><pre>~' . print_r( $this->match, true ) . '~</pre><br />';

		// extended match data
		$xmlfile = JLG_PATH_ADMIN . DS . 'assets' . DS . 'extended' . DS . 'match.xml';
		$jRegistry = new JRegistry;
		$jRegistry->loadString($this->match->extended, 'ini');
		$extended =& JForm::getInstance('extended', $xmlfile, array('control'=> 'extended'), false, '/config');
		$extended->bind($jRegistry);
		
		$lists = array();

		// build the html select booleanlist for cancel
		$lists['cancel'] = JHTML::_( 'select.booleanlist', 'cancel', 'class="inputbox"', $this->match->cancel );

		$playgrounds[] = JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_PLAYGROUND' ));
		if (!empty($this->playgrounds))
		{
			$playgrounds = array_merge( $playgrounds, $this->playgrounds );
		}
		$lists['playgrounds'] = JHTML::_(	'select.genericlist',
											$playgrounds,
											'playground_id',
											'class="inputbox" size="1"',
											'value',
											'text',
											$this->match->playground_id );

		//match relation lists
		//$mdlMatch = $model->getMatch();
		$oldmatches[] = JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_EDITMATCH_SELECT_PREV_MATCH'));
		$res = array();
		$new_match_id = ($this->match->new_match_id) ? $this->match->new_match_id : 0;
		if ( $res =& $model->getMatchRelationsOptions( $this->project->id, $this->match->id . "," . $new_match_id ) )
		{
			$oldmatches = array_merge( $oldmatches, $res );
		}
		unset($res);
		$lists['old_match'] = JHTML::_(	'select.genericlist',
										$oldmatches,
										'old_match_id',
										'class="inputbox" size="1"',
										'value',
										'text',
										$this->match->old_match_id );

		$newmatches[] = JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_EDITMATCH_SELECT_NEW_MATCH'));
		$res = array();
		$old_match_id = ($this->match->old_match_id) ? $this->match->old_match_id : 0;
		if ( $res =& $model->getMatchRelationsOptions( $this->project->id, $this->match->id . "," . $old_match_id ))
		{
			$newmatches = array_merge( $newmatches, $res );
		}
		unset($res);
		$lists['new_match'] = JHTML::_(	'select.genericlist',
										$newmatches,
										'new_match_id',
										'class="inputbox" size="1"',
										'value',
										'text',
										$this->match->new_match_id );

		$this->assignRef( 'form', $this->get('form'));
		$this->assignRef( 'extended', $extended );
		$this->assignRef( 'lists', $lists );

		$pageTitle = JText::_('COM_JOOMLEAGUE_EDITMATCH_MATCHDETAILS');
		$document->setTitle($pageTitle);

		parent::display($tpl);
	}

}
?>