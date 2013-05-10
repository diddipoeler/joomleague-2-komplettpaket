<?php defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'pagination.php');

jimport('joomla.application.component.view');

class JoomleagueViewRosteralltime extends JLGView
{

	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();
		$model = $this->getModel();
		$config = $model->getTemplateConfig($this->getName());

		$this->assignRef('config',$config);
    $this->assignRef('rows',$model->getTeamPlayers());
		
		parent::display($tpl);
	}

}
?>