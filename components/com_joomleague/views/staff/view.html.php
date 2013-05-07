<?php defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JoomleagueViewStaff extends JLGView
{

	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();

		$model = $this->getModel();
		$config=$model->getTemplateConfig($this->getName());
		$person=$model->getPerson();

		$this->assignRef('project',$model->getProject());
		$this->assignRef('overallconfig',$model->getOverallConfig());
		$this->assignRef('config',$config);
		$this->assignRef('person',$person);
		$this->assignRef('showediticon',$model->getAllowed($config['edit_own_player']));
		
		$staff=&$model->getTeamStaff();
		$titleStr=JText::sprintf('COM_JOOMLEAGUE_STAFF_ABOUT_AS_A_STAFF', JoomleagueHelper::formatName(null, $this->person->firstname, $this->person->nickname, $this->person->lastname, $this->config["name_format"]));		
		
		$this->assignRef('inprojectinfo',$staff);
		$this->assignRef('history',$model->getStaffHistory('ASC'));

		$this->assignRef('stats',$model->getStats());
		$this->assignRef('staffstats',$model->getStaffStats());
		$this->assignRef('historystats',$model->getHistoryStaffStats());
		$this->assign('title',$titleStr);

		$extended = $this->getExtended($person->extended, 'staff');
		$this->assignRef( 'extended', $extended);
		$document->setTitle($titleStr);

		parent::display($tpl);
	}

}
?>