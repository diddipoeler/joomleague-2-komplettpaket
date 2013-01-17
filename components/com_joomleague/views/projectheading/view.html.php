<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class JoomleagueViewProjectHeading extends JLGView
{
    function display( $tpl = null )
    {
        $model = $this->getModel();
        $this->assignRef( 'project', $model->getProject() );
		$this->assignRef( 'division', $model->getDivision(JRequest::getInt('division', 10)) );
        $this->assignRef( 'overallconfig', $model->getOverallConfig() );
        parent::display($tpl);
    }
}
?>