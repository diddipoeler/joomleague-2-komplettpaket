<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.DS.'controllers'.DS.'results.php';
require_once JPATH_COMPONENT.DS.'controllers'.DS.'ranking.php';

class JoomleagueControllerResultsRanking extends JoomleagueController
{
    function display( )
    {
        $this->showprojectheading();
        JoomleagueControllerResults::showResults( );
        JoomleagueControllerRanking::showRanking( );
        $this->showbackbutton();
        $this->showfooter();
    }
}

?>
