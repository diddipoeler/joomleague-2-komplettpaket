<?php defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JoomleagueControllerClubPlan extends JoomleagueController
{
	function display()
	{
		// Get the view name from the query string
		$viewName=JRequest::getVar("view","clubplan");
		$startdate=JRequest::getVar("startdate",null);
		$enddate=JRequest::getVar("enddate",null);

		// Get the view
		$view =& $this->getView($viewName);

		// Get the joomleague model
		$jl = $this->getModel("joomleague","JoomleagueModel");
		$jl->set("_name","joomleague");
		if (!JError::isError($jl)){$view->setModel($jl);}

		$mdlClubPlan = $this->getModel("clubplan","JoomleagueModel");
		$mdlClubPlan->set("_name","clubplan");
		$mdlClubPlan->setStartDate($startdate);
		$mdlClubPlan->setEndDate($enddate);
		if (!JError::isError($mdlClubPlan)){$view->setModel($mdlClubPlan);}

		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

}
?>