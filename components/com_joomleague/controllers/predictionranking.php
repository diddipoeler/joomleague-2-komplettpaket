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

jimport('joomla.application.component.controller');

/**
 * Joomleague Component prediction Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueControllerPredictionRanking extends JoomleagueController
{
	function display()
	{
	  // Get the view name from the query string
        $viewName = JRequest::getVar( "view", "predictionranking" );

        // Get the view
        $view = & $this->getView( $viewName );

        // Get the joomleague model
        $jl = $this->getModel( "joomleague", "JoomleagueModel" );
        $jl->set( "_name", "joomleague" );
        if (!JError::isError( $jl ) )
        {
            $view->setModel ( $jl );
        }
    // Get the joomleague model
		$sr = $this->getModel( 'prediction', 'JoomleagueModel' );
		$sr->set( '_name', 'prediction' );
		if ( !JError::isError( $sr ) )
		{
			$view->setModel ( $sr );
		}
		
		// Get the joomleague model
		$jl = $this->getModel( 'project', 'JoomleagueModel' );
		$jl->set( '_name', 'project' );
		if ( !JError::isError( $jl ) )
		{
			$view->setModel ( $jl );
		}
		
		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

	function selectprojectround()
	{
		JRequest::checkToken() or jexit(JText::_('JL_PRED_INVALID_TOKEN_REFUSED'));
		$post	= JRequest::get('post');
		//echo '<br /><pre>~' . print_r($post,true) . '~</pre><br />';
		$pID	= JRequest::getVar('prediction_id',	'',	'post',	'int');
		$pggroup	= JRequest::getVar('pggroup',	null,	'post',	'int');
        $pggrouprank= JRequest::getVar('pggrouprank',null,	'post',	'int');
        $pjID	= JRequest::getVar('p',	'',	'post',	'int');
        
		$rID	= JRequest::getVar('round_id',		'',	'post',	'int');
		$set_pj	= JRequest::getVar('set_pj',		'',	'post',	'int');
		$set_r	= JRequest::getVar('set_r',			'',	'post',	'int');

		$link = PredictionHelperRoute::getPredictionRankingRoute($pID,$pjID,$rID,'',$pggroup,$pggrouprank);
        
		//echo '<br />' . $link . '<br />';
		$this->setRedirect($link);
	}

}
?>