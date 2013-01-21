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

//jimport( 'joomla.application.component.controller' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
//require_once ( JPATH_COMPONENT . DS . 'controllers' . DS . 'joomleague.php' );


/**
 * Joomleague Component LMO-Import Controller
 *
 * @author	Dieter Pl�ger
 * @package	Joomleague
 * @since	1.5.0a
 */
class jlextlmoimportController extends JLGController
{

function __construct()
    {
        parent::__construct();
        //$this->registerTask( 'save' , 'Save' );
        //$this->registerTask( 'apply' , 'Apply' );
        //$this->registerTask( 'cancel' , 'Close' );
        $this->registerTask('edit','display');
        $this->registerTask('insert','display');
        $this->registerTask('selectpage','display');
    }
    
    
function display()  
{
global $mainframe,$option;
echo "jlextlmoimport controller loaded";
$document	=& JFactory::getDocument();
		$mainframe	=& JFactory::getApplication();

		$model		= $this->getModel ( 'modeljlextlmoimport' );
		
		
		$this->setMessage( JText::_( 'project selected' ) );
		
		switch ($this->getTask())
		{
			case 'edit':
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','default_edit');
				JRequest::setVar('view','jlextlmoimport');
				JRequest::setVar('edit',true);
				break;

			case 'insert':
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','info');
        JRequest::setVar('view','jlxmlimports');
				//JRequest::setVar('view','jlextlmoimport');
				JRequest::setVar('edit',true);
				break;
		}
		
		
parent::display();
}

/*
function select()
	{
		$mainframe =& JFactory::getApplication();
		$selectType=JRequest::getVar('type',0,'get','int');
		$recordID=JRequest::getVar('id',0,'get','int');
		$mainframe->setUserState('com_joomleague'.'selectType',$selectType);
		$mainframe->setUserState('com_joomleague'.'recordID',$recordID);

		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar('layout','selectpage');
		JRequest::setVar('view','jlextlmoimport');

		parent::display();
	}
*/
	
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');
		$msg='';
		JToolBarHelper::back(JText::_('JL_GLOBAL_BACK'),JRoute::_('index.php?option=com_joomleague&view=jllmoimport&controller=jllmoimport'));
		$mainframe =& JFactory::getApplication();
		$post=JRequest::get('post');
        //$model		= $this->getModel ( 'modeljlextlmoimport' );
        $model=$this->getModel('jlextlmoimport');

/*
$cid = JRequest::getVar( 'lmoimportuseteams', array(), 'post', 'array' );
echo 'cid <br>';
echo '<pre>';
print_r($cid);
echo '</pre>';
*/

		// first step - upload
		if (isset($post['sent']) && $post['sent']==1)
		{
			$upload=JRequest::getVar('import_package',null,'files','array');

/*			
echo 'save <br>';    
echo '<pre>';
print_r($upload);
echo '</pre>';
*/
			$lmoimportuseteams=JRequest::getVar('lmoimportuseteams',null);
			$mainframe->setUserState('com_joomleague'.'lmoimportuseteams',$lmoimportuseteams);
			
			$tempFilePath=$upload['tmp_name'];
			$mainframe->setUserState('com_joomleague'.'uploadArray',$upload);
			$filename='';
			$msg='';
			$dest=JPATH_SITE.DS.'tmp'.DS.$upload['name'];
			$extractdir=JPATH_SITE.DS.'tmp';
			$importFile=JPATH_SITE.DS.'tmp'. DS.'joomleague_import.l98';
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
						JError::raiseWarning(500,JText::_('JL_ADMIN_LMO_IMPORT_CTRL_CANT_UPLOAD'));
						return;
					}
					else
					{
						if (strtolower(JFile::getExt($dest))=='zip')
						{
							$result=JArchive::extract($dest,$extractdir);
							if ($result === false)
							{
								JError::raiseWarning(500,JText::_('JL_ADMIN_LMO_IMPORT_CTRL_EXTRACT_ERROR'));
								return false;
							}
							JFile::delete($dest);
							$src=JFolder::files($extractdir,'l98',false,true);
							if(!count($src))
							{
								JError::raiseWarning(500,'JL_ADMIN_LMO_IMPORT_CTRL_EXTRACT_NOJLG');
								//todo: delete every extracted file / directory
								return false;
							}
							if (strtolower(JFile::getExt($src[0]))=='l98')
							{
								if (!@ rename($src[0],$importFile))
								{
									JError::raiseWarning(21,JText::_('JL_ADMIN_LMO_IMPORT_CTRL_ERROR_RENAME'));
									return false;
								}
							}
							else
							{
								JError::raiseWarning(500,JText::_('JL_ADMIN_LMO_IMPORT_CTRL_TMP_DELETED'));
								return;
							}
						}
						else
						{
							if (strtolower(JFile::getExt($dest))=='l98')
							{
								if (!@ rename($dest,$importFile))
								{
									JError::raiseWarning(21,JText::_('JL_ADMIN_LMO_IMPORT_CTRL_RENAME_FAILED'));
									return false;
								}
							}
							else
							{
								JError::raiseWarning(21,JText::_('JL_ADMIN_LMO_IMPORT_CTRL_WRONG_EXTENSION'));
								return false;
							}
						}
					}
			}
		}
        $create_jlg = $model->getData();
		//$link='index.php?option=com_joomleague&task=jlextlmoimport.edit';
        $link='index.php?option=com_joomleague&task=jlxmlimport.edit';
		#echo '<br />Message: '.$msg.'<br />';
		#echo '<br />Redirect-Link: '.$link.'<br />';
		$this->setRedirect($link,$msg);
	}










}


