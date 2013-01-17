<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class JoomleagueControllerResults extends JoomleagueController
{

	function display()
	{
		$this->showprojectheading();
		$this->showresults();
		$this->showbackbutton();
		$this->showfooter();
	}

	function showresults()
	{
		// Get the view name from the query string
		$viewName = JRequest::getVar( 'view', 'results' );

		// Get the view
		$view =& $this->getView( $viewName );

		// Get the joomleague model
		$jl = $this->getModel( 'project', 'JoomleagueModel' );
		$jl->set( '_name', 'project' );
		if (!JError::isError( $jl ) )
		{
			$view->setModel ( $jl );
		}

		// Get the joomleague model
		$sm = $this->getModel( 'results', 'JoomleagueModel' );
		$sm->set( '"_name', 'results' );
		if (!JError::isError( $sm ) )
		{
			$view->setModel ( $sm );
		}

		$view->display();
	}

	function edit()
	{
		require_once( JPATH_COMPONENT . DS . 'js' . DS . 'frontediting.js.php' );

		$viewName = JRequest::getVar( 'view', 'results' );

		// Get the view
		$view =& $this->getView( $viewName );

		// Get the joomleague model
		$jl = $this->getModel( 'project', 'JoomleagueModel' );
		$jl->set( '_name', 'project' );
		if (!JError::isError( $jl ) )
		{
			$view->setModel ( $jl );
		}

		// Get the model
		$sc = $this->getModel( 'results', 'JoomleagueModel' );
		$sc->set( '_name', 'results' );
		if (!JError::isError( $sc ) )
		{
			$view->setModel ( $sc );
		}

		$this->showprojectheading();
		$view->setLayout( 'form' );
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

	function saveshort()
	{
		JRequest::checkToken() or jexit('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');

		$msg		= '';
		$post		= JRequest::get('post');
		$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$project_id	= JRequest::getVar('p', '', 'post', 'int');
		$round_id	= JRequest::getVar('r', '', 'post', 'int');
		$model = $this->getModel('results');
		$user = JFactory::getUser();
		$allowed = $model->isAllowed();
		$isMatchAdmin = $model->isMatchAdmin($cid, $user->id);
		if ((!$allowed) && (!$isMatchAdmin))
		{
			$link = JoomleagueHelperRoute::getResultsRoute($project_id, $round_id );
			$msg = JText::_('COM_JOOMLEAGUE_SAVE_RESULTS_NO_PRIVILEGES');
			$this->setRedirect($link, $msg);
		}

		for ($x = 0; $x < count($cid); $x++)
		{
			if (!isset($post['published' . $cid[$x]])){$post['published' . $cid[$x]]='0';}
			$post['match_date' . $cid[$x]] = JoomleagueHelper::convertDate($post['match_date' . $cid[$x]], 0);
			
			//clear ranking cache
			$cache = JFactory::getCache('joomleague.project'.$project_id);
			$cache->clean();
			
			if ($model->save_array($cid[$x], $post, true, $project_id))
			{
				$msg = JText::_('COM_JOOMLEAGUE_SAVE_RESULTS_SUCCESS');
			}
			else
			{
				$msg = JText::_('COM_JOOMLEAGUE_SAVE_RESULTS_ERROR');
			}
		}
		$link = JoomleagueHelperRoute::getResultsRoute($project_id, $round_id);
		echo '#'.$msg.'#';
		$this->setRedirect($link, $msg);
	}

}
?>
