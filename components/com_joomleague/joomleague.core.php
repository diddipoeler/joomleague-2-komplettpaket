<?php

/**
 * @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/**
 * this file perform the basic init and includes for joomleague
 */

defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
define('JLG_PATH_SITE',  JPATH_SITE.DS.'components'.DS .'com_joomleague');

define('JLG_VAR_LANGUAGE',  'COM_JOOMLEAGUE');
define('JLG_VAR_DB_TABLES',  'joomleague');

define('JLG_PATH_ADMIN', JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_joomleague');
require_once( JLG_PATH_ADMIN .DS . 'defines.php' );

require_once( JLG_PATH_SITE.DS.'assets'.DS.'classes'.DS.'jlgcontroller.php'  );
require_once( JLG_PATH_SITE.DS.'assets'.DS.'classes'.DS.'jlgmodel.php'  );
require_once( JLG_PATH_SITE.DS.'assets'.DS.'classes'.DS.'jlgview.php'  );

require_once( JLG_PATH_SITE.DS.'helpers'.DS.'route.php' );
require_once( JLG_PATH_SITE.DS.'helpers'.DS.'predictionroute.php' );
require_once( JLG_PATH_SITE.DS.'helpers'.DS.'countries.php' );
require_once( JLG_PATH_SITE.DS.'helpers'.DS.'extraparams.php' );
require_once( JLG_PATH_SITE.DS.'helpers'.DS.'ranking.php' );
require_once( JLG_PATH_SITE.DS.'helpers'.DS.'html.php' );

// Include neccesary file for operation JSON
if(!defined('SERVICES_JSON_SLICE')) {
require_once( JLG_PATH_SITE.DS.'helpers'.DS.'JSON.php' );
}


// google map
require_once( JLG_PATH_SITE.DS.'helpers'.DS.'simpleGMapAPI.php' );
require_once( JLG_PATH_SITE.DS.'helpers'.DS.'simpleGMapGeocoder.php' );
//include_once( JLG_PATH_SITE . DS . 'helpers' . DS . 'feedreaderhelperr.php' );
require_once( JLG_PATH_ADMIN.DS.'helpers'.DS.'jlcommon.php' );
require_once( JLG_PATH_ADMIN.DS.'helpers'.DS.'imageselect.php' );
require_once( JLG_PATH_ADMIN.DS.'tables'.DS.'jltable.php' );

JTable::addIncludePath( JLG_PATH_ADMIN.DS.'tables' );

require_once (JLG_PATH_ADMIN.DS.'helpers'.DS.'plugins.php');

$task = JRequest::getCmd('task');
$option = JRequest::getCmd('option');
if($task != '' && $option == 'com_joomleague')  {
	if (!JFactory::getUser()->authorise($task, 'com_joomleague')) {
		//display the task which is not handled by the access.xml
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
	}
}

?>

	
<?PHP 
// No conflict
$document = JFactory::getDocument();
$mainframe = JFactory::getApplication();
//$document->addScript(JURI::root(true).'/administrator/components/'.$option.'/assets/js/jl2.noconflict.js');

if($task == '' && $option == 'com_joomleague') 
{
$js ="registerhome('".JURI::base()."','JoomLeague 2.0 Complete Installation','".$mainframe->getCfg('sitename')."');". "\n";
$document->addScriptDeclaration( $js );
}