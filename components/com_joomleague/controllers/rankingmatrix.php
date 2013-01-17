<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.DS.'controllers'.DS.'ranking.php';
require_once JPATH_COMPONENT.DS.'controllers'.DS.'matrix.php';

class JoomleagueControllerResultsRankingMatrix extends JoomleagueController
{
    function display( )
    {
        $this->showprojectheading();
        JoomleagueControllerRanking::showRanking( );
        JoomleagueControllerMatrix::showMatrix( );
        $this->showbackbutton();
        $this->showfooter();
    }
}

?>
