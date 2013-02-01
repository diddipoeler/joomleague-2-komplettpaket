<?php
/**
* @copyright	Copyright (C) 2005-2010 JoomLeague.de. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
//require_once ( JPATH_COMPONENT . DS . 'controllers' . DS . 'joomleague.php' );


/**
 * Joomleague Component DBB-Import Controller
 *
 * @author	Dieter Plöger
 * @package	Joomleague
 * @since	1.5.0a
 */
class JoomleagueControllerjlextdbbimports extends JoomleagueController
{

function __construct()
    {
        parent::__construct();
     
    }

function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');
		$msg='';
		JToolBarHelper::back(JText::_('JL_GLOBAL_BACK'),JRoute::_('index.php?option=com_joomleague&view=jlextdbbimports'));
		$mainframe =& JFactory::getApplication();
		$model = $this->getModel('jlextdbbimports');
		$post = JRequest::get('post');
    $xml_file = $model->getData();
    $link='index.php?option=com_joomleague&view=jlxmlimports&task=jlxmlimport.edit';
    $this->setRedirect($link,$msg);
    
  }
        
    
}


?>    