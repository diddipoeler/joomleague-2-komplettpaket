<?php 
/**
 * @see JLGView
 * @see JLGModel
 * @author		Wolfgang Pinitsch <andone@mfga.at>
 * @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die;
$app			= JFactory::getApplication();
$arrExtensions 	= JoomleagueHelper::getExtensions(JRequest::getInt('p'));
$model_pathes[]	= array();
$view_pathes[]	= array();
$lang 			= JFactory::getLanguage();
for ($e = 0; $e < count($arrExtensions); $e++) {
	$extension = $arrExtensions[$e];
	$extensionpath = JLG_PATH_SITE.DS.'extensions'.DS.$extension;
	// include file named after the extension for specific includes for examples
	if ( file_exists( $extensionpath.DS.$extension.'.php') )  {
		//e.g example.php
		require_once $extensionpath.DS.$extension.'.php';
	}
	if($app->isAdmin()) {
		$base_path = $extensionpath.DS.'admin';
		// language file
		$lang->load('com_joomleague_'.$extension, $base_path);
	} else {
		$base_path = $extensionpath;
		//language file
		$lang->load('com_joomleague_'.$extension, $base_path);
	}
	//set the base_path to the extension controllers directory
	$params = array('base_path'=>$base_path);
	/* own controllers currently not supported in 2.0
	if (!file_exists($base_path.DS.'controller.php')) {
		$extension = "joomleague";
		$params = array();
	} elseif (!file_exists($base_path.DS.$extension.'.php')) {
		$extension = "joomleague";
		$params = array();
	}
	*/
	$extension = "joomleague";
	$params = array();
	$controller = JLGController::getInstance($extension, $params);
	$model_pathes[] = $base_path.DS.'models';
	$view_pathes[] = $base_path.DS.'views';
}
if(is_null($controller) && !($controller instanceof JController)) {
	//fallback if no extensions controller has been initialized
	$controller	= JLGController::getInstance('joomleague');
}
foreach ($model_pathes as $path) {
	$controller->addModelPath($path);		
}
foreach ($view_pathes as $path) {
	$controller->addViewPath($path);		
}