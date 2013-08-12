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

jimport('joomla.application.component.view');

require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php');

/**
 * Joomleague Component prediction View
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100628
 */
class JoomleagueViewPredictionEntry extends JLGView
{

	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document	=& JFactory::getDocument();
    $option = JRequest::getCmd('option');
    $optiontext = strtoupper(JRequest::getCmd('option').'_');
    $this->assignRef( 'optiontext',			$optiontext );
    
		$mainframe = JFactory::getApplication();
		$model		=& $this->getModel();
    $model->checkStartExtension();
    
		$this->assignRef('predictionGame',$model->getPredictionGame());
		if (isset($this->predictionGame))
		{
			//echo '<br /><pre>~' . print_r($this->getName(),true) . '~</pre><br />';
			$config			= $model->getPredictionTemplateConfig($this->getName());
			$overallConfig	= $model->getPredictionOverallConfig();

      $this->assignRef('debuginfo',	$model->getDebugInfo());
      
			$this->assignRef('model',				$model);
			$this->assignRef('config',				array_merge($overallConfig,$config));
      $configavatar			= $model->getPredictionTemplateConfig('predictionusers');
      $this->assignRef('configavatar',				$configavatar );
			$this->assignRef('predictionMember',	$model->getPredictionMember($configavatar));
			$this->assignRef('predictionProjectS',	$model->getPredictionProjectS());
			$this->assignRef('actJoomlaUser',		JFactory::getUser());
			$this->assignRef('allowedAdmin',		$model->getAllowed());

			$this->assignRef('isPredictionMember',	$model->checkPredictionMembership());
			$this->assignRef('isNotApprovedMember',	$model->checkIsNotApprovedPredictionMember());
			$this->assignRef('isNewMember',			$model->newMemberCheck());
			$this->assignRef('tippEntryDone',		$model->tippEntryDoneCheck());

			$this->assignRef('websiteName',			JFactory::getConfig()->getValue('config.sitename'));
			
			//echo $this->loadTemplate( 'assignRefs' );
			//echo '<br /><pre>~' . print_r($this->predictionMember,true) . '~</pre><br />';

			if ($this->allowedAdmin)
			{
				$lists = array();
				if ($this->predictionMember->pmID > 0){$dMemberID=$this->predictionMember->pmID;}else{$dMemberID=0;}
				$predictionMembers[] = JHTML::_('select.option','0',JText::_('COM_JOOMLEAGUE_PRED_SELECT_MEMBER'),'value','text');
				if ($res=&$model->getPredictionMemberList($this->config)){$predictionMembers=array_merge($predictionMembers,$res);}
				$lists['predictionMembers']=JHTML::_('select.genericList',$predictionMembers,'uid','class="inputbox" onchange="this.form.submit(); "','value','text',$dMemberID);
				unset($res);
				unset($predictionMembers);
				$this->assignRef('lists',$lists);
			}

      $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
			// Set page title
			$pageTitle = JText::_('COM_JOOMLEAGUE_PRED_ENTRY_TITLE');

			$document->setTitle($pageTitle);

			parent::display($tpl);
		}
		else
		{
			JError::raiseNotice(500,JText::_('COM_JOOMLEAGUE_PRED_PREDICTION_NOT_EXISTING'));
		}
	}
	
	function createStandardTippSelect($tipp_home=NULL,$tipp_away=NULL,$tipp=NULL,$pid='0',$mid='0',$seperator,$allow)
	{
		if (!$allow){
			$disabled=' disabled="disabled" ';
			$css = "readonly";
		} else {
			$disabled='';
			$css = "inputbox";
		}
		$output = '';
		$output .= '<input type="hidden" name="tipps[' . $pid . '][' . $mid . ']" value="' . $tipp . '" />';
		$output .= '<input name="homes[' . $pid . '][' . $mid . ']" class="'.$css.'" style="text-align:center; " size="2" value="' . $tipp_home . '" tabindex="1" type="text" ' . $disabled . '/>';
		$output .= ' <b>' . $seperator . '</b> ';
		$output .= '<input name="aways[' . $pid . '][' . $mid . ']" class="'.$css.'" style="text-align:center; " size="2" value="' . $tipp_away . '" tabindex="1" type="text" ' . $disabled . '/>';
		if (!$allow)
		{
			$output .= '<input type="hidden" name="homes[' . $pid . '][' . $mid . ']" value="' . $tipp_home . '" />';
			$output .= '<input type="hidden" name="aways[' . $pid . '][' . $mid . ']" value="' . $tipp_away . '" />';
		}
		return $output;
	}

	function createTotoTippSelect($tipp_home=NULL,$tipp_away=NULL,$tipp=NULL,$pid='0',$mid='0',$allow)
	{
		
if ( $this->debuginfo )
{
echo 'tipp_home -> ' . $tipp_home. '<br>';
echo 'tipp_away -> ' . $tipp_away. '<br>';
echo 'tipp -> ' . $tipp. '<br>';
echo 'pid -> ' . $pid. '<br>';
echo 'mid -> ' . $mid. '<br>';
echo 'allow -> ' . $allow. '<br>';
}
    
    
    if (!$allow){$disabled=' disabled="disabled" ';}else{$disabled='';}
		$output = '';
		$output .= '<input type="hidden" name="homes[' . $pid . '][' . $mid . ']" value="' . $tipp_home . '" />';
		$output .= '<input type="hidden" name="aways[' . $pid . '][' . $mid . ']" value="' . $tipp_away . '" />';
		$outputArray = array	(
									JHTML::_('select.option','',	JText::_('JL_PRED_ENTRY_NO_TIPP'),	'id','name'),
									JHTML::_('select.option','1',	JText::_('JL_PRED_ENTRY_HOME_WIN'),	'id','name'),
									JHTML::_('select.option','0',	JText::_('JL_PRED_ENTRY_DRAW'),		'id','name'),
									JHTML::_('select.option','2',	JText::_('JL_PRED_ENTRY_AWAY_WIN'),	'id','name')
								);
		$output .= JHTML::_('select.genericlist',$outputArray,'tipps['.$pid.']['.$mid.']','class="inputbox" size="1" ' . $disabled,'id','name',$tipp);
		unset($outputArray);
		if (!$allow)
		{
			$output .= '<input type="hidden" name="tipps['.$pid.']['.$mid.']" value="' . $tipp . '" />';
		}
		return $output;
	}

}
?>