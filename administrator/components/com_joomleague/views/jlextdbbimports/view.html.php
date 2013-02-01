<?php
/**
 * @copyright	Copyright (C) 2006-2009 Joomleague.de. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * HTML View class for the Joomleague component
 *
 * @static
 * @package		Joomleague
 * @since 0.1
 */
class JoomleagueViewjlextdbbimports extends JLGView
{


function display( $tpl = null )
	{
		global $mainframe;
    $option='com_joomleague';
		$mainframe =& JFactory::getApplication();
		$revisionDate='2011-04-15 - 12:00';
		$this->assignRef('revisionDate',$revisionDate);
    /*
    echo '<pre>';
    print_r($this->getLayout());
    echo '</pre>';
    */
   
		
    if ( $this->getLayout() == 'default')
		{
			$this->_displayDefault( $tpl );
			return;
		}
		
		
		
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'JL_ADMIN_EXT_DBB_TITLE_1')  , 'extension.png' );
		

		$uri =& JFactory::getURI();
		$config =& JComponentHelper::getParams('com_media');
		$post=JRequest::get('post');
		$files=JRequest::get('files');

		$this->assignRef('request_url',$uri->toString());
		$this->assignRef('config',$config);
	
		parent::display( $tpl );
		
	}

  
	
function _displayDefault( $tpl )
	{
		global $mainframe, $option;

		$db		=& JFactory::getDBO();
		$uri 	=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();
        $document	= & JFactory::getDocument();
    $dbblink = '';
		
		
		
		$project = $mainframe->getUserState( $option . 'project' );
		$this->assignRef( 'project',		$project );
		$config =& JComponentHelper::getParams('com_media');

$stylelink = '<link rel="stylesheet" href="'.JURI::root().'administrator/components/com_joomleague/assets/css/jlextusericons.css'.'" type="text/css" />' ."\n";
    $document->addCustomTag($stylelink);
    
		JToolBarHelper::title( JText::_( 'JL_ADMIN_EXT_DBB_TITLE_2')  , 'dbb-cpanel' );
		
    $teamart = '';
    $teamarten['Herren'] = 'Herren';
    $teamarten['U18m'] = 'U18m';
    $teamarten['U18'] = 'U18';
    $teamarten['U16m'] = 'U16m';
    $teamarten['U16'] = 'U16';
//     $teamarten[] = '';
//     $teamarten[] = '';
//     $teamarten[] = '';
    
    $teamarthtml[]=JHTML::_('select.option','',JText::_('JL_ADMIN_XML_IMPORT_DBB_SELECT_TEAMART'));
		foreach ( $teamarten as $key => $value  )
		{
    $teamarthtml[]=JHTML::_('select.option',$value,$value);    
    }
		$lists['teamart']=JHTML::_(	'select.genericlist',
										$teamarthtml,
										'teamart',
										'class="inputbox" size="1"',
										'value',
										'text',
										$teamart);
    $this->assignRef('lists',$lists);                
		$this->assignRef( 'request_url',	$uri->toString() );
		$this->assignRef( 'config',		$config);
		$this->assignRef( 'dbblink',	$dbblink);
    
		parent::display( $tpl );
		
}



}

?>