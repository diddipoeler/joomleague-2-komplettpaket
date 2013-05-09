<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * View class for tracks imageselect screen
 * Based on the Joomla! media component
 *
 * @package Joomla
 * @subpackage Tracks
 * @since 0.9
 */
class JoomLeagueViewImagehandler extends JLGView  {

	/**
	 * Image selection List
	 *
	 * @since 0.9
	 */
	function display($tpl = null)
	{
		global $option;
		$mainframe	= JFactory::getApplication();
		$document = JFactory::getDocument();


		if($this->getLayout() == 'upload') {
			$this->_displayupload($tpl);
			return;
		}

		//get vars
		$type     = JRequest::getVar( 'type' );
		$folder = ImageSelectJL::getfolder($type);
		$field = JRequest::getVar( 'field' );
		$search 	= $mainframe->getUserStateFromRequest( $option.'.imageselect', 'search', '', 'string' );
		$search 	= trim(JString::strtolower( $search ) );

		//add css
		$version = urlencode(JoomleagueHelper::getVersion());
		$css = 'components/com_joomleague/assets/css/imageselect.css?v='.$version;
		$document->addStyleSheet($css);

		JRequest::setVar( 'folder', $folder );

		// Do not allow cache
		JResponse::allowCache(false);

		//get images
		$images 	= $this->get('Images');
		$pageNav 	= & $this->get('Pagination');

		if (count($images) > 0 || $search) {
			$this->assignRef('images', 	$images);
			$this->assignRef('type',  $type);
			$this->assignRef('folder', 	$folder);
			$this->assignRef('search', 	$search);
			$this->assignRef('state', 	$this->get('state'));
			$this->assignRef('pageNav', $pageNav);
			$this->assignRef('field',   $field);
			parent::display($tpl);
		} else {
			//no images in the folder, redirect to uploadscreen and raise notice
			JError::raiseNotice('SOME_ERROR_CODE', JText::_('COM_JOOMLEAGUE_IMAGEHANDLER_NO_IMAGES'));
			$this->setLayout('upload');
			$this->_displayupload($tpl);
			return;
		}
	}

	function setImage($index = 0)
	{
		if (isset($this->images[$index])) {
			$this->_tmp_img = &$this->images[$index];
		} else {
			$this->_tmp_img = new JObject;
		}
	}

	/**
	 * Prepares the upload image screen
	 *
	 * @param $tpl
	 *
	 * @since 0.9
	 */
	function _displayupload($tpl = null)
	{
		global  $option;
		$mainframe	= JFactory::getApplication();
		//initialise variables
		$document	= JFactory::getDocument();
		$uri 		= JFactory::getURI();
		$params = & JComponentHelper::getParams('com_joomleague');
		$type     = JRequest::getVar( 'type' );
		$folder = ImageSelectJL::getfolder($type);
		$field  = JRequest::getVar( 'field' );
		$menu = JRequest::setVar( 'hidemainmenu', 1 );
		//get vars
		$task 		= JRequest::getVar( 'task' );

		jimport('joomla.client.helper');
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');

		//assign data to template
		$this->assignRef('params'  	, $params);
		$this->assignRef('request_url'	, $uri->toString());
		$this->assignRef('ftp'			, $ftp);
		$this->assignRef('folder'      , $folder);
		$this->assignRef('field',   $field);
		$this->assignRef('menu',   $menu);
		parent::display($tpl);
	}
}
?>