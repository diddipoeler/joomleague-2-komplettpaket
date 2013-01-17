<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class JoomleagueViewPerson extends JLGView
{

	function display( $tpl = null )
	{
		// Get a refrence of the page instance in joomla
		$document = JFactory::getDocument();

		$model	= $this->getModel();
		$config = $model->getTemplateConfig("player");

		// Get the type of persondata to be shown from the query string
		// pt==1 ==> as player // pt==2 ==> as staffmember  // pt==3 ==> as referee // pt==4 ==> as club-staffmember
		$showType = JRequest::getVar( 'pt', '1', 'default', 'int' ); if ($showType > 3) { $showType = 1; }
		$person = $model->getPerson();
		$this->assignRef('showType',		$showType );
		$this->assignRef('project',			$model->getProject() );
		$this->assignRef('overallconfig',	$model->getOverallConfig() );
		$this->assignRef('config',			$config );
		$this->assignRef('person',			$person );
		//$extended = $this->getExtended($person->extended, 'person');
		//$this->assignRef( 'extended', $extended );
		switch ($showType) 
		{
			case '4':
				$titleStr = 'About %1$s %2$s as a Club staff';
				$this->assignRef( 'historyClubStaff',	$model->getPersonHistory('ASC') );
				break;
			case '3':
				$titleStr = 'About %1$s %2$s as a Referee';
				$this->assignRef('inprojectinfo',	$model->getReferee() );
				$this->assignRef('historyReferee',	$model->getRefereeHistory('ASC') );
				break;
			case '2':
				$titleStr = 'About %1$s %2$s as a Staff member';
				$this->assignRef('inprojectinfo',	$model->getTeamStaff() );
				$this->assignRef('historyStaff',	$model->getStaffHistory('ASC') );
				break;
			case '1':
				$titleStr = 'About %1$s %2$s as a Player';
				$this->assignRef('inprojectinfo',	$model->getTeamPlayer() );
  				$this->assignRef('historyPlayer',	$model->getPlayerHistory('ASC') );
  				//$this->assignRef('historyStaff',	$model->getStaffHistory('ASC') );
				$this->assignRef('AllEvents',		$model->getAllEvents() );
				break;
			default:
				break;
		}

		$document->setTitle( JText::sprintf( $titleStr, $this->person->firstname, $this->person->lastname ) );

		parent::display( $tpl );
	}

}
?>