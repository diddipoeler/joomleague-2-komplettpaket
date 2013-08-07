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
 * Joomleague Component LMO-Import Controller
 *
 * @author	Dieter Plöger
 * @package	Joomleague
 * @since	1.5.0a
 */
class JoomleagueControllerjlextdfbnetplayerimport extends JoomleagueController
{

function __construct()
    {
        parent::__construct();
        //$this->registerTask( 'save' , 'Save' );
        $this->registerTask( 'update' , 'display' );
        $this->registerTask( 'cancel' , 'display' );
        $this->registerTask('edit','display');
        $this->registerTask('insert','display');
        $this->registerTask('insertplayer','display');
        $this->registerTask('insertmatch','display');
        $this->registerTask('selectpage','display');
    }
    
    
function display()  
{
global $mainframe,$option;

$document	=& JFactory::getDocument();
$mainframe	=& JFactory::getApplication();
$mainframe->enqueueMessage('jlextdfbnetplayerimport task ->'.$this->getTask(),'Notice');

		$model		= $this->getModel ( 'modeljlextdfbnetplayerimport' );
		
		
		$this->setMessage( JText::_( 'project selected' ) );
		
		switch ($this->getTask())
		{
			case 'edit':
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','default_edit');
				JRequest::setVar('view','jlextdfbnetplayerimport');
				JRequest::setVar('edit',true);
				break;
				
			case 'cancel':
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','default');
				JRequest::setVar('view','jlextdfbnetplayerimport');
				JRequest::setVar('edit',true);
				break;
        
      case 'update':
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','default_update');
				JRequest::setVar('view','jlextdfbnetplayerimport');
				JRequest::setVar('edit',true);
				break;	  	

			case 'insert':
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','info');
        JRequest::setVar('view','jlxmlimports');
				//JRequest::setVar('view','jlextdfbnetplayerimport');
				JRequest::setVar('edit',true);
				break;
        
      case 'insertmatch':
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','info');
        JRequest::setVar('view','jlxmlimports');
				JRequest::setVar('edit',true);
				break;
        
      case 'insertplayer':
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','info');
				JRequest::setVar('view','jlextdfbnetplayerimport');
				JRequest::setVar('edit',true);
				break;         
		}
		
		
parent::display();
}

function select()
	{
		$mainframe =& JFactory::getApplication();
		$selectType=JRequest::getVar('type',0,'get','int');
		$recordID=JRequest::getVar('id',0,'get','int');
		$mainframe->setUserState('com_joomleague'.'selectType',$selectType);
		$mainframe->setUserState('com_joomleague'.'recordID',$recordID);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','selectpage');
		JRequest::setVar('view','jlextdfbnetplayerimport');

		parent::display();
	}
	
	function save()
	{
		$mainframe =& JFactory::getApplication();
  $document	=& JFactory::getDocument();
        // Check for request forgeries
		JRequest::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$msg='';
		JToolBarHelper::back(JText::_('JPREV'),JRoute::_('index.php?option=com_joomleague&view=jldfbnetimport&controller=jldfbnetimport'));
		$mainframe =& JFactory::getApplication();
		$model = $this->getModel('jlextdfbnetplayerimport');
		$post = JRequest::get('post');

    $delimiter = JRequest::getVar('delimiter',null);
    $whichfile = JRequest::getVar('whichfile',null);
    
    $mainframe->enqueueMessage(JText::_('delimiter '.$delimiter.''),'');
    $mainframe->enqueueMessage(JText::_('whichfile '.$whichfile.''),'');
    
    if ( $whichfile == 'playerfile' )
    {
    JError::raiseNotice(500,JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_PLAYERFILE'));
    }
    elseif ( $whichfile == 'matchfile' )
    {
    JError::raiseNotice(500,JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_MATCHFILE'));
    
    if (isset($post['dfbimportupdate']) )
		{
		JError::raiseNotice(500,JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_MATCHFILE_UPDATE'));
		}
    
    }
    
    
		// first step - upload
		if (isset($post['sent']) && $post['sent']==1)
		{
			$upload=JRequest::getVar('import_package',null,'files','array');


			$lmoimportuseteams=JRequest::getVar('lmoimportuseteams',null);
						
			$mainframe->setUserState('com_joomleague'.'lmoimportuseteams',$lmoimportuseteams);
			$mainframe->setUserState('com_joomleague'.'whichfile',$whichfile);
			$mainframe->setUserState('com_joomleague'.'delimiter',$delimiter);
			
			$tempFilePath=$upload['tmp_name'];
			$mainframe->setUserState('com_joomleague'.'uploadArray',$upload);
			$filename='';
			$msg='';
			$dest=JPATH_SITE.DS.'tmp'.DS.$upload['name'];
			$extractdir=JPATH_SITE.DS.'tmp';
			$importFile=JPATH_SITE.DS.'tmp'. DS.'joomleague_import.csv';
			if (JFile::exists($importFile))
			{
				JFile::delete($importFile);
			}
			if (JFile::exists($tempFilePath))
			{
					if (JFile::exists($dest))
					{
						JFile::delete($dest);
					}
					if (!JFile::upload($tempFilePath,$dest))
					{
						JError::raiseWarning(500,JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_CTRL_CANT_UPLOAD'));
						return;
					}
					else
					{
						if (strtolower(JFile::getExt($dest))=='zip')
						{
							$result=JArchive::extract($dest,$extractdir);
							if ($result === false)
							{
								JError::raiseWarning(500,JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_CTRL_EXTRACT_ERROR'));
								return false;
							}
							JFile::delete($dest);
							$src=JFolder::files($extractdir,'l98',false,true);
							if(!count($src))
							{
								JError::raiseWarning(500,'COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_CTRL_EXTRACT_NOJLG');
								//todo: delete every extracted file / directory
								return false;
							}
							if (strtolower(JFile::getExt($src[0]))=='csv')
							{
								if (!@ rename($src[0],$importFile))
								{
									JError::raiseWarning(21,JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_CTRL_ERROR_RENAME'));
									return false;
								}
							}
							else
							{
								JError::raiseWarning(500,JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_CTRL_TMP_DELETED'));
								return;
							}
						}
						else
						{
							if (strtolower(JFile::getExt($dest))=='csv' || strtolower(JFile::getExt($dest))=='ics' )
							{
								if (!@ rename($dest,$importFile))
								{
									JError::raiseWarning(21,JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_CTRL_RENAME_FAILED'));
									return false;
								}
							}
							else
							{
								JError::raiseWarning(21,JText::_('COM_JOOMLEAGUE_ADMIN_DFBNET_IMPORT_CTRL_WRONG_EXTENSION'));
								return false;
							}
						}
					}
			}
		}
		
		if (isset($post['dfbimportupdate']) )
		{
    $link='index.php?option=com_joomleague&view=jlextdfbnetplayerimport&task=jlextdfbnetplayerimport.update';
    }
    else
    {
    
    if ( $whichfile == 'matchfile' )
    {
    $xml_file = $model->getData();
    $link='index.php?option=com_joomleague&task=jlxmlimport.edit';
    }
    else
    {
    $xml_file = $model->getData();    
    $link='index.php?option=com_joomleague&task=jlxmlimport.edit';
    //$link='index.php?option=com_joomleague&view=jlextdfbnetplayerimport&controller=jlextdfbnetplayerimport&task=edit';
    }
    
    }
		
		
    
    #echo '<br />Message: '.$msg.'<br />';
		#echo '<br />Redirect-Link: '.$link.'<br />';
		
		
		$this->setRedirect($link,$msg);
	}










}


?>