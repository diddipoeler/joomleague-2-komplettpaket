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
	   $mainframe =& JFactory::getApplication();
		// $parent is the class calling this method
		//echo '<p>' . JText::_('COM_JOOMLEAGUE_UPDATE_TEXT') . $parent->get('manifest')->version . '</p>';
        $mainframe->enqueueMessage( JText::_(' Joomleague ').'Update '.JText::_(' Version: ').$parent->get('manifest')->version,'');
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
	   $mainframe =& JFactory::getApplication();
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		//echo '<p>' . JText::_('COM_JOOMLEAGUE_PREFLIGHT_' . $type . '_TEXT' ) . $parent->get('manifest')->version . '</p>';
        $mainframe->enqueueMessage( JText::_(' Joomleague ').$type.JText::_(' Version: ').$parent->get('manifest')->version,'');
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
		//echo '<p>' . JText::_('COM_JOOMLEAGUE_POSTFLIGHT_' . $type . '_TEXT' ) . $parent->get('manifest')->version . '</p>';
        $mainframe->enqueueMessage( JText::_(' Joomleague ').$type.JText::_(' Version: ').$parent->get('manifest')->version,'');


    
$db->setQuery('SELECT params FROM #__extensions WHERE name = "joomleague" and type ="component" ');
$paramsdata = json_decode( $db->loadResult(), true );
//$mainframe->enqueueMessage(JText::_('postflight paramsdata<br><pre>'.print_r($paramsdata,true).'</pre>'   ),'');
  
$params = JComponentHelper::getParams('com_joomleague');

$xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'config.xml';  
$jRegistry = new JRegistry;
$jRegistry->loadString($params->toString('ini'), 'ini');
//$form =& JForm::getInstance('com_joomleague', $xmlfile, array('control'=> 'params'), false, "/config");
//$form =& JForm::getInstance('com_joomleague', $xmlfile);
$form =& JForm::getInstance('com_joomleague', $xmlfile, array('control'=> ''), false, "/config");
$form->bind($jRegistry);

$newparams = array();
foreach($form->getFieldset($fieldset->name) as $field)
        {
         //echo 'name -> '. $field->name.'<br>';
         //echo ' -> '. $field->type.'<br>';
         //echo ' -> '. $field->input.'<br>';
         //echo 'value -> '. $field->value.'<br>';
        $newparams[$field->name] = $field->value;
        
        }

//$mainframe->enqueueMessage(JText::_('postflight newparams<br><pre>'.print_r($newparams,true).'</pre>'   ),'');        
//$paramsString = json_encode( $newparams );
//$mainframe->enqueueMessage(JText::_('postflight paramsString<br><pre>'.print_r($paramsString,true).'</pre>'   ),'');


//$mainframe->enqueueMessage(JText::_('postflight jRegistry<br><pre>'.print_r($jRegistry,true).'</pre>'   ),'');

//$mainframe->enqueueMessage(JText::_('postflight form<br><pre>'.print_r($form,true).'</pre>'   ),'');

//$params = $form->getFieldsets('params');
//$mainframe->enqueueMessage(JText::_('postflight params<br><pre>'.print_r($params,true).'</pre>'   ),'');

    switch ($type)        
    {
    case "install":
    self::installComponentLanguages();
    self::installModules();
	self::installPlugins();
    self::createImagesFolder();
    self::migratePicturePath();
    self::deleteInstallFolders();
    self::sendInfoMail();
    $parent->getParent()->setRedirectURL('index.php?option=com_joomleague');
    break;
    case "update":
    self::installComponentLanguages();
    self::installModules();
    self::installPlugins();
    self::createImagesFolder();
    self::migratePicturePath();
    self::setParams($newparams);
    self::deleteInstallFolders();
    self::sendInfoMail();
    $parent->getParent()->setRedirectURL('index.php?option=com_joomleague');
    break;
    case "discover_install":
    break;
        
    }
    
        
	}
    
    public function deleteInstallFolders()
	{
	$mainframe =& JFactory::getApplication();
    // ueberfluessige module admin
    $src = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'modules';
    if( JFolder::delete($src) )
    {
    $mainframe->enqueueMessage(JText::_('Admin Installationsverzeichnis: Module gelöscht'),'');
    }
    // ueberfluessige sprachdateien admin
	$src = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'language';
    if( JFolder::delete($src) )
    {
    $mainframe->enqueueMessage(JText::_('Admin Installationsverzeichnis: Sprachen gelöscht'),'');
    }	
    
    // ueberfluessige module side
    $src = JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'modules';
    if( JFolder::delete($src) )
    {
    $mainframe->enqueueMessage(JText::_('Side Installationsverzeichnis: Module gelöscht'),'');
    }
    // ueberfluessige sprachdateien side
	$src = JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'language';
    if( JFolder::delete($src) )
    {
    $mainframe->enqueueMessage(JText::_('Side Installationsverzeichnis: Sprachen gelöscht'),'');
    }
    // ueberfluessige plugins side
	$src = JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'plugins';
    if( JFolder::delete($src) )
    {
    $mainframe->enqueueMessage(JText::_('Side Installationsverzeichnis: Plugins gelöscht'),'');
    }
    
    
    
    }
    
    public function sendInfoMail()
	{
	$mainframe =& JFactory::getApplication();
    $to = 'diddipoeler@gmx.de';
    $subject = 'JoomLeague 2.0 Complete Installation';
    $message = 'JoomLeague 2.0 Complete Installation wurde auf der Seite : '.JURI::base().' gestartet.';
    JUtility::sendMail( '', JURI::base(), $to, $subject, $message );
    }
    
    /*
    * sets parameter values in the component's row of the extension table
    */
    function setParams($param_array) 
    {
        $mainframe =& JFactory::getApplication();
        $db = JFactory::getDbo();
        if ( count($param_array) > 0 )
        {
            // store the combined new and existing values back as a JSON string
                        $paramsString = json_encode( $param_array );
                        $db->setQuery('UPDATE #__extensions SET params = ' .
                                $db->quote( $paramsString ) .
                                ' WHERE name = "joomleague" and type ="component"' );
                                $db->query();
        $mainframe->enqueueMessage(JText::_('Joomleague Konfiguration gesichert'),'');
        }
                        
                /*                
                if ( count($param_array) > 0 ) {
                        // read the existing component value(s)
                        $db = JFactory::getDbo();
                        $db->setQuery('SELECT params FROM #__extensions WHERE name = "joomleague" and type ="component" ');
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
    
	/**
	 * method to install the modules
	 *
	 * @return void
	 */
	public function installModules()
	{
	$mainframe =& JFactory::getApplication();
// 	$lang = JFactory::getLanguage(); 
//   $languages = JLanguageHelper::getLanguages('lang_code');
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
  
    echo 'Copy Administration Modules language(s) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
		$src=JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'modules';
		$dest=JPATH_ADMINISTRATOR.DS.'modules';
		$modules = JFolder::folders($src);
		
 		foreach ( $langlist as $key )
    {
    echo 'Copy Administration Modules language( '.$key->element.' ) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';    
    foreach ($modules as $module)
		{
		if ( JFolder::exists($src.DS.$module.DS.'language'.DS.$key->element) )
		{
			JFolder::copy($src.DS.$module.DS.'language'.DS.$key->element, JPATH_ADMINISTRATOR.DS.'language'.DS.$key->element, '', true);
		echo ' - <span style="color:green">'.JText::_('Success -> '.$module.' - '.$key->element).'</span><br />';
    }
    }
		}

		//echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
		echo 'Copy Administration Module(s)';
		JFolder::copy($src, $dest, '', true);
		echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
//		echo 'Copy Site Module(s) language(s) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a>';
		$src=JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'modules';
		$dest=JPATH_SITE.DS.'modules';
		$modules = JFolder::folders($src);
		
		foreach ( $langlist as $key )
    {
    echo 'Copy Site Module(s) language( '.$key->element.' ) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
    foreach ($modules as $module)
		{
		if ( JFolder::exists($src.DS.$module.DS.'language'.DS.$key->element) )
		{
			JFolder::copy($src.DS.$module.DS.'language'.DS.$key->element, JPATH_SITE.DS.'language'.DS.$key->element, '', true);
      echo ' - <span style="color:green">'.JText::_('Success -> '.$module.' - '.$key->element).'</span><br />';
		}
    }
		}

		//echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
		echo 'Copy Site Module(s)';
		JFolder::copy($src, $dest, '', true);
		echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
        
        
        $title = 'JL2 diddipoeler GitHub Commits Module';
		$tblModules = JTable::getInstance('module');
		$tblModules->load(array('title'=>$title));
		$tblModules->title			= $title;
		$tblModules->module			= 'mod_joomleague_github';
		$tblModules->position 		= 'cpanel';
		$tblModules->published 		= 1;
		$tblModules->client_id 		= 1;
		$tblModules->ordering 		= 1;
		$tblModules->access 		= 3;
		$tblModules->language 		= '*';
		$tblModules->publish_up		= '2000-00-00 00:00:00';
		$tblModules->params 		= '{"username":"diddipoeler","repo":"joomleague-2-komplettpaket","count":15,"relativeTime":"1","layout":"_:default","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"static"}';

		if (!$tblModules->store()) {
			echo $tblModules->getError().'<br />';
		}
		$db = JFactory::getDBO();
		$query = 'INSERT INTO #__modules_menu (moduleid,menuid) VALUES ('.$tblModules->id.',0)';
		$db->setQuery($query);
		if (!$db->query())
		{
			//echo $db->getErrorMsg().'<br />';
		}
	
		// Initialise variables
		$conf = JFactory::getConfig();
		$dispatcher = JDispatcher::getInstance();

		$options = array(
			'defaultgroup' => ($tblModules->module) ? $tblModules->module : (isset($this->option) ? $this->option : JRequest::getCmd('option')),
			'cachebase' => ($tblModules->client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'));

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();

		// Trigger the onContentCleanCache event.
		$dispatcher->trigger('onContentCleanCache', $options);
    
        
        
        
	}
    
	/**
	 * method to install the plugins
	 *
	 * @return void
	 */
	public function installPlugins()
	{
  $mainframe =& JFactory::getApplication();
// 	$lang = JFactory::getLanguage(); 
//   $languages = JLanguageHelper::getLanguages('lang_code');
  
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
  
//		echo 'Copy Plugin(s) language(s) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a>';
		$src=JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'plugins';
		$dest=JPATH_SITE.DS.'plugins';
		$groups = JFolder::folders($src);
    
    foreach ( $langlist as $key )
    {
    echo 'Copy Plugin(s) language( '.$key->element.' ) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br />';
		foreach ($groups as $group)
		{
			$plugins = JFolder::folders($src.DS.$group);
			foreach ($plugins as $plugin)
			{
      if ( JFolder::exists($src.DS.$group.DS.$plugin.DS.'language'.DS.$key->element) )
		{
				JFolder::copy($src.DS.$group.DS.$plugin.DS.'language'.DS.$key->element, JPATH_ADMINISTRATOR.DS.'language'.DS.$key->element, '', true);
		echo ' - <span style="color:green">'.JText::_('Success -> '.$group.' - '.$plugin.' - '.$key->element).'</span><br />';
    }
    	}
		}
    }
		//echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
		echo 'Copy Plugin(s)';
		JFolder::copy($src, $dest, '', true);
		echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
	}
    
public function createImagesFolder()
	{
		echo JText::_('Creating new Image Folder structure');
		$dest = JPATH_ROOT.'/images/com_joomleague';
		$update = JFolder::exists($dest);
		$folders = array('clubs', 'clubs/large', 'clubs/medium', 'clubs/small', 'clubs/trikot_home', 'clubs/trikot_away','events','leagues','divisions','person_playground',
							'flags_associations','persons', 'placeholders', 'predictionusers','playgrounds', 'projects','projectreferees','projectteams','projectteams/trikot_home', 'projectteams/trikot_away',
              'associations','rosterground','matchreport','seasons','sport_types', 'teams','flags','teamplayers','teamstaffs','venues', 'statistics');
		JFolder::create(JPATH_ROOT.'/images/com_joomleague');
		JFile::copy(JPATH_ROOT.'/images/index.html', JPATH_ROOT.'/images/com_joomleague/index.html');
		JFolder::create(JPATH_ROOT.'/images/com_joomleague/database');
		JFile::copy(JPATH_ROOT.'/images/index.html', JPATH_ROOT.'/images/com_joomleague/database/index.html');
		foreach ($folders as $folder) {
			JFolder::create(JPATH_ROOT.'/images/com_joomleague/database/'.$folder);
			JFile::copy(JPATH_ROOT.'/images/index.html', JPATH_ROOT.'/images/com_joomleague/database/'.$folder.'/index.html');
		}
		foreach ($folders as $folder) {
			$from = JPath::clean(JPATH_ROOT.'/media/com_joomleague/'.$folder);
			if(JFolder::exists($from)) {
				$to = JPath::clean($dest.'/database/'.$folder);
				if(!JFolder::exists($to)) {
					$ret = JFolder::move($from, $to);
				} else {
					$ret = JFolder::copy($from, $to, '', true);
					//$ret = JFolder::delete($from);
				}
			}
		}
		echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
	}
    
public function migratePicturePath() {
		$arrQueries = array();
		
		$query = "update #__joomleague_club set logo_big = replace(logo_big, 'media/com_joomleague/clubs/large', 'images/com_joomleague/database/clubs/large')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_club set logo_middle = replace(logo_middle, 'media/com_joomleague/clubs/medium', 'images/com_joomleague/database/clubs/medium')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_club set logo_small = replace(logo_small, 'media/com_joomleague/clubs/small', 'images/com_joomleague/database/clubs/small')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_eventtype set icon = replace(icon, 'media/com_joomleague/event_icons', 'images/com_joomleague/database/events')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_person set picture = replace(picture, 'media/com_joomleague/persons', 'images/com_joomleague/database/persons')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_team_player set picture = replace(picture, 'media/com_joomleague/persons', 'images/com_joomleague/database/persons')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_project set picture = replace(picture, 'media/com_joomleague/projects', 'images/com_joomleague/database/projects')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_playground set picture = replace(picture, 'media/com_joomleague/playgrounds', 'images/com_joomleague/database/playgrounds')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_sports_type set icon = replace(icon, 'media/com_joomleague/sportstypes', 'images/com_joomleague/database/sport_types')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_team set picture = replace(picture, 'media/com_joomleague/teams', 'images/com_joomleague/database/teams')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_project_team set picture = replace(picture, 'media/com_joomleague/teams', 'images/com_joomleague/database/teams')";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_statistic set icon = replace(icon, 'media/com_joomleague/statistics', 'images/com_joomleague/database/statistics')";
		array_push($arrQueries, $query);
		
		$db = JFactory::getDBO();
		$query="SHOW TABLES LIKE '%_joomleague%'";
			
		$db->setQuery($query);
		$results = $db->loadColumn();
		if(is_array($results)) {
			echo JText::_('Database Tables Picture Path Migration');
			foreach ($arrQueries as $key=>$value) {
				$db->setQuery($value);
				if (!$db->query())
				{
					echo '-> '.JText::_('Failed').'! <br>';
					$this->setError($db->getErrorMsg());
					echo $db->getErrorMsg();
					//return false;
				} else {
					//echo "-> done !<br>";		
				}
				
			}
			echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
		} else {
			echo JText::_('No Picture Path Migration neccessary!'); 
		}
	}
                              
}
