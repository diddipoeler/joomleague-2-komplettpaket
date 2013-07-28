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
        $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
        
        if ( $this->overallconfig['show_project_sporttype_picture'] )
        {
        /*
        [sport_type_name] => COM_JOOMLEAGUE_ST_SOCCER
        [fs_sport_type_name] => soccer
        [sport_type_id] => 5
        */
        }
        parent::display($tpl);
    }
}
?>