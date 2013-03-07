<?php
/**
* @version $Id: default.php 4905 2010-01-30 08:51:33Z and_one $
* @package Joomleague
* @subpackage navigation_menu
* @copyright Copyright (C) 2009  JoomLeague
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see _joomleague_license.txt
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div
	id="jlajaxmenu-<?php echo $module->id ?>"><!--jlajaxmenu-<?php echo $module->id?> start-->

<div style="margin: 0 auto;">
<?php
echo JHTML::_('select.genericlist', $seasonselect['seasons'], 'jlamseason'.$module->id, 'class="inputbox" style="width:100%;visibility:show;" size="1" onchange="jlamnewleagues('.$module->id.');"',  'value', 'text', JRequest::getVar('jlamseason',0,'default','POST'));
?>
</div>

<?php if ( $leagueselect['leagues'] ) { ?>
<div style="margin: 0 auto;">
<?php
echo JHTML::_('select.genericlist', $leagueselect['leagues'], 'jlamleague'.$module->id, 'class="inputbox" style="width:100%;visibility:show;" size="1" onchange="jlamnewprojects('.$module->id.');"',  'value', 'text', JRequest::getVar('jlamleague',0,'default','POST'));
?>
</div>
<?php } ?>

<?php if ( $projectselect['projects'] ) { ?>
<div style="margin: 0 auto;">
<?php
echo JHTML::_('select.genericlist', $projectselect['projects'], 'jlamproject'.$module->id, 'class="inputbox" style="width:100%;visibility:show;" size="1" onchange="jlamnewdivisions('.$module->id.');"',  'value', 'text', JRequest::getVar('jlamproject',0,'default','POST'));
?>
</div>
<?php } ?>

<?php if ( $divisionsselect['divisions'] ) { ?>
<div style="margin: 0 auto;">
<?php
echo JHTML::_('select.genericlist', $divisionsselect['divisions'], 'jlamdivision'.$module->id, 'class="inputbox" style="width:100%;visibility:show;" size="1" onchange="jlamdivision('.$module->id.');"',  'value', 'text', JRequest::getVar('jlamdivision',0,'default','POST'));
?>
</div>
<?php } ?>

<?php if ( $projectselect['teams'] ) { ?>
<div style="margin: 0 auto;">
<?php
echo JHTML::_('select.genericlist', $projectselect['teams'], 'jlamteams'.$module->id, 'class="inputbox" style="width:100%;visibility:show;" size="1" onchange="jlamteam('.$module->id.');"',  'value', 'text', JRequest::getVar('jlamteam',0,'default','POST'));
?>
</div>
<?php } ?>

<?php if ( $project_id ) { ?>
<div style="margin: 0 auto;">
<fieldset class="">
<legend><?php echo JText::_('MOD_PROJECT_VIEWS'); ?>
	</legend>
<ul class="nav-list">
<?php if ($params->get('show_nav_links')): ?>
	
		<?php for ($i = 1; $i < 17; $i++): ?>
			<?php if ($params->get('navpoint'.$i) && $link = $helper->getLink($params->get('navpoint'.$i))): ?>
				<li class="nav-item"><?php echo JHTML::link(JRoute::_($link), $params->get('navpoint_label'.$i)); ?></li>
			<?php elseif ($params->get('navpoint'.$i) == "separator"): ?>
				<li class="nav-item separator"><?php echo $params->get('navpoint_label'.$i); ?></li>
			<?php endif; ?>
		<?php endfor; ?>		
		
	<?php endif; ?>
</ul> 
</fieldset>	   
</div>
<?php } ?>
<!--jlajaxmenu-<?php echo $module->id?> end-->
</div>

<?php
if($ajax && $ajaxmod==$module->id){ exit(); } ?>