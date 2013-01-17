<?php defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.DS.'controllers'.DS.'results.php';
require_once JPATH_COMPONENT.DS.'controllers'.DS.'matrix.php';

class JoomleagueControllerResultsMatrix extends JoomleagueController
{
    function display( )
    {
        $this->showprojectheading();
        JoomleagueControllerResults::showResults( );
        JoomleagueControllerMatrix::showMatrix( );
        $this->showbackbutton();
        $this->showfooter();
    }
}

?>
