<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Script file of com_sportsmanagement component
 */
class com_joomleagueInstallerScript
{
	/*
     * The release value would ideally be extracted from <version> in the manifest file,
     * but at preflight, the manifest file exists only in the uploaded temp folder.
     */
    //private $release = '1.0.00';
    
    /**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		// $parent is the class calling this method
		//$parent->getParent()->setRedirectURL('index.php?option=com_joomleague');
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_JOOMLEAGUE_UNINSTALL_TEXT') . '</p>';
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_JOOMLEAGUE_UPDATE_TEXT') . $parent->get('manifest')->version . '</p>';
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_JOOMLEAGUE_PREFLIGHT_' . $type . '_TEXT' ) . $parent->get('manifest')->version . '</p>';
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
	$mainframe =& JFactory::getApplication();
    $db = JFactory::getDbo();
        // $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_JOOMLEAGUE_POSTFLIGHT_' . $type . '_TEXT' ) . $parent->get('manifest')->version . '</p>';


    
$db->setQuery('SELECT params FROM #__extensions WHERE name = "joomleague" and type ="component" ');
$paramsdata = json_decode( $db->loadResult(), true );
$mainframe->enqueueMessage(JText::_('postflight paramsdata<br><pre>'.print_r($paramsdata,true).'</pre>'   ),'');
  
$params = JComponentHelper::getParams('com_joomleague');
$xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'config.xml';  
$jRegistry = new JRegistry;
$jRegistry->loadString($params->toString('ini'), 'ini');
$form =& JForm::getInstance('com_joomleague', $xmlfile, array('control'=> 'params'), false, "/config");
$form->bind($jRegistry);
$mainframe->enqueueMessage(JText::_('postflight paramsdata<br><pre>'.print_r($form,true).'</pre>'   ),'');

$params = $form->getFieldsets('params');
$mainframe->enqueueMessage(JText::_('postflight params<br><pre>'.print_r($params,true).'</pre>'   ),'');

    switch ($type)        
    {
    case "install":
    //self::installComponentLanguages();
    break;
    case "update":
    //self::installComponentLanguages();
    break;
    case "discover_install":
    break;
        
    }
    
        
	}
    
    /*
    * sets parameter values in the component's row of the extension table
    */
    function setParams($param_array) 
    {
        $mainframe =& JFactory::getApplication();
        $db = JFactory::getDbo();
                        
                /*                
                if ( count($param_array) > 0 ) {
                        // read the existing component value(s)
                        $db = JFactory::getDbo();
                        $db->setQuery('SELECT params FROM #__extensions WHERE name = "joomleague"');
                        $params = json_decode( $db->loadResult(), true );
                        $mainframe->enqueueMessage(JText::_('setParams params<br><pre>'.print_r($params,true).'</pre>'   ),'');
                        // add the new variable(s) to the existing one(s)
                        foreach ( $param_array as $name => $value ) {
                                $params[ (string) $name ] = (string) $value;
                        }
                        // store the combined new and existing values back as a JSON string
                        $paramsString = json_encode( $params );
                        $db->setQuery('UPDATE #__extensions SET params = ' .
                                $db->quote( $paramsString ) .
                                ' WHERE name = "joomleague"' );
                                $db->query();
                }
                */
        }
        
   /**
	 * method to install the component languages
	 *
	 * @return void
	 */
	public function installComponentLanguages()
	{
	$mainframe =& JFactory::getApplication();
	//$lang = JFactory::getLanguage(); 
	
  $db =& JFactory::getDBO();
  $query = $db->getQuery(true);
  $type = "language";
  $query->select('a.element');
  $query->from('#__extensions AS a');
  $type = $db->Quote($type);
	$query->where('(a.type = '.$type.')');
	$query->group('a.element');
  $db->setQuery($query);
  $langlist = $db->loadObjectList();
	//echo 'Language -> <pre>'.print_r($langlist,true).'</pre><br>';
	
	//$languages = JLanguageHelper::getLanguages('lang_code');
//    echo 'Copy Administration language(s) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
	
  	//echo 'Language -> <pre>'.print_r($languages,true).'</pre><br>';
  
    foreach ( $langlist as $key )
    {
    //$mainframe->enqueueMessage('Language installed -> ' . $key,'Notice');
    echo 'Copy Administration language( '.$key->element.' ) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
    
    $src=JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS;
		$dest=JPATH_ADMINISTRATOR.DS.'modules';
		JFolder::copy($src.DS.'language'.DS.$key->element, JPATH_ADMINISTRATOR.DS.'language'.DS.$key->element, '', true);
		
		echo ' - <span style="color:green">'.JText::_('Success-> '.$key->element).'</span><br />';
		echo 'Copy Site language( '.$key->element.' ) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
		$src=JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS;
		$dest=JPATH_SITE.DS;
		JFolder::copy($src.DS.'language'.DS.$key->element, JPATH_SITE.DS.'language'.DS.$key->element, '', true);
		echo ' - <span style="color:green">'.JText::_('Success -> '.$key->element).'</span><br />';
		}
		
	}           
}
