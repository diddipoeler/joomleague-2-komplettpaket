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

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');

class com_joomleagueInstallerScript
{
	private function _install($update=false) {
	?>
		<?php
		self::createImagesFolder();
		self::installComponentLanguages();
		if($update) {
			self::migratePicturePath();
		}
		$updateArray = array();
		include_once(JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'joomleague.core.php');
		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'assets'.DS.'updates'.DS.'jl_install.php');
		self::installModules();
		self::installPlugins();
		if($update) {
			self::updateDatabase();
		}
        else
        {
            self::insertVersionString();
        }
		?>
		
		<h2>Welcome to JoomLeague!</h2>
		<img
			src="../media/com_joomleague/jl_images/joomleague_logo.png"
			alt="JoomLeague" title="JoomLeague" />
		<hr />
		<br>Click on <a href="index.php?option=com_installer&view=discover&task=discover.refresh">Discover->Refresh</a> to discover new or updated Modules and Plugins.
	<?php
	}

	/**
	 * method to install the component languages
	 *
	 * @return void
	 */
	public function installComponentLanguages()
	{
	$mainframe =& JFactory::getApplication();
	$lang = JFactory::getLanguage(); 
  $languages = JLanguageHelper::getLanguages('lang_code');
//    echo 'Copy Administration language(s) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
	
  	//echo 'Language -> <pre>'.print_r($languages,true).'</pre><br>';
  
    foreach ( $languages as $key => $value )
    {
    //$mainframe->enqueueMessage('Language installed -> ' . $key,'Notice');
    echo 'Copy Administration language( '.$key.' ) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
    
    $src=JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS;
		$dest=JPATH_ADMINISTRATOR.DS.'modules';
		JFolder::copy($src.DS.'language'.DS.$key, JPATH_ADMINISTRATOR.DS.'language'.DS.$key, '', true);
		
		echo ' - <span style="color:green">'.JText::_('Success-> '.$key).'</span><br />';
		echo 'Copy Site language( '.$key.' ) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
		$src=JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS;
		$dest=JPATH_SITE.DS;
		JFolder::copy($src.DS.'language'.DS.$key, JPATH_SITE.DS.'language'.DS.$key, '', true);
		echo ' - <span style="color:green">'.JText::_('Success -> '.$key).'</span><br />';
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
	$lang = JFactory::getLanguage(); 
  $languages = JLanguageHelper::getLanguages('lang_code');
  
    echo 'Copy Administration Modules language(s) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
		$src=JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'modules';
		$dest=JPATH_ADMINISTRATOR.DS.'modules';
		$modules = JFolder::folders($src);
		
 		foreach ( $languages as $key => $value )
    {
    echo 'Copy Administration Modules language( '.$key.' ) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';    
    foreach ($modules as $module)
		{
		if ( JFolder::exists($src.DS.$module.DS.'language'.DS.$key) )
		{
			JFolder::copy($src.DS.$module.DS.'language'.DS.$key, JPATH_ADMINISTRATOR.DS.'language'.DS.$key, '', true);
		echo ' - <span style="color:green">'.JText::_('Success -> '.$module.' - '.$key).'</span><br />';
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
		
		foreach ( $languages as $key => $value )
    {
    echo 'Copy Site Module(s) language( '.$key.' ) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br>';
    foreach ($modules as $module)
		{
		if ( JFolder::exists($src.DS.$module.DS.'language'.DS.$key) )
		{
			JFolder::copy($src.DS.$module.DS.'language'.DS.$key, JPATH_SITE.DS.'language'.DS.$key, '', true);
      echo ' - <span style="color:green">'.JText::_('Success -> '.$module.' - '.$key).'</span><br />';
		}
    }
		}

		//echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
		echo 'Copy Site Module(s)';
		JFolder::copy($src, $dest, '', true);
		echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
	}

	/**
	 * method to install the plugins
	 *
	 * @return void
	 */
	public function installPlugins()
	{
  $mainframe =& JFactory::getApplication();
	$lang = JFactory::getLanguage(); 
  $languages = JLanguageHelper::getLanguages('lang_code');
  
//		echo 'Copy Plugin(s) language(s) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a>';
		$src=JPATH_SITE.DS.'components'.DS.'com_joomleague'.DS.'plugins';
		$dest=JPATH_SITE.DS.'plugins';
		$groups = JFolder::folders($src);
    
    foreach ( $languages as $key => $value )
    {
    echo 'Copy Plugin(s) language(s) provided by <a href="https://opentranslators.transifex.com/projects/p/joomleague/">Transifex</a><br />';
		foreach ($groups as $group)
		{
			$plugins = JFolder::folders($src.DS.$group);
			foreach ($plugins as $plugin)
			{
      if ( JFolder::exists($src.DS.$group.DS.$plugin.DS.'language'.DS.$key) )
		{
				JFolder::copy($src.DS.$group.DS.$plugin.DS.'language'.DS.$key, JPATH_ADMINISTRATOR.DS.'language'.DS.$key, '', true);
		echo ' - <span style="color:green">'.JText::_('Success -> '.$group.' - '.$plugin.' - '.$key).'</span><br />';
    }
    	}
		}
    }
		//echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
		echo 'Copy Plugin(s)';
		JFolder::copy($src, $dest, '', true);
		echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
	}
    
    public function insertVersionString()
    {
    $mainframe =& JFactory::getApplication();    
    
	$tblVersion = JTable::getInstance("Version", "Table");
    $teile = explode(".", $this->release);
    $tblVersion->major		= $teile[0];
	$tblVersion->minor		= $teile[1];
	$tblVersion->build		= $teile[2];
	$tblVersion->revision	= '-diddipoeler';
	$tblVersion->date		= NULL;
	$tblVersion->version	= 'a';
	$tblVersion->file		= 'joomleague';
    if (!$tblVersion->store())
	{
		echo($tblVersion->getError());
	}
        
    }


	public function updateDatabase() 
    {
	$mainframe =& JFactory::getApplication();
    $to = 'diddipoeler@gmx.de';
    $subject = 'JoomLeague 2.0 Complete Installation';
    $message = 'JoomLeague 2.0 Complete Installation wurde auf der Seite : '.JURI::base().' gestartet.';
    JUtility::sendMail( '', JURI::base(), $to, $subject, $message );
    
    if ( $this->release == $this->getParam('version') )
    {
// kein update, da die selbe version installiert wird
}
else
{
  $db =& JFactory::getDBO();
  $db_table = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'sql'.DS.'updates'.DS.$this->release.'.sql';
  $result = JInstallationHelper::populateDatabase($db, $db_table, $errors);
}   

//   $params = JComponentHelper::getParams('com_joomleague');
//   $xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'config.xml';
//   $jRegistry = new JRegistry;
// 	$jRegistry->loadString($params->toString('ini'), 'ini');
// 	$form =& JForm::getInstance('com_joomleague', $xmlfile, array('control'=> 'params'), false, "/config");
// 	$form->bind($jRegistry);
	
                                	
// diddipoeler
// erstmal keine weiterleitung  
  
/*   
	$mainframe->enqueueMessage(JText::_('Sie werden gleich zum Tabellenupdate weitergeleitet !'),'Notice');
    $restart_link = JURI::base() . 'index.php?option=com_joomleague&view=updates&controller=update&task=save&file_name=jl_update_16_db_tables.php';
    echo '<meta http-equiv="refresh" content="6; URL='.$restart_link.'">';
*/  
      
//		echo '<iframe height="400" scrolling="auto" width="100%" src="index.php?option=com_joomleague&view=updates&task=update.save&file_name=jl_update_db_tables.php&tmpl=component&print=1" frameborder="0" ></iframe>';

	}
	
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	public function install($parent)
	{
		?>
		<hr>
		<h1>JoomLeague Installation</h1>
		<?php 
		
		self::_install(false);
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	public function update($parent)
	{
	$db = JFactory::getDBO();
    $result = $db->getTableFields( '#__joomleague_version_history' );
    //echo '<pre>'.print_r( $result, true ).'</pre><br>';
    
    if ( array_key_exists('version', $result['#__joomleague_version_history'] ) )
    {
        //echo 'vorhanden<br>';
    }
    else
    {
        //echo 'nicht vorhanden<br>';
        $query2 = "ALTER TABLE `#__joomleague_version_history` ADD `version` VARCHAR(255) NOT NULL DEFAULT '' AFTER `text`;";
  $db->setQuery($query2); 
  if (!$db->query())
				{
				} else {
					//echo "-> done !<br>";		
				}
        
    }
		?>
		<hr>
		<h1>JoomLeague Update</h1>
		<?php
    
    /*
    // eine tabelle mit allen spalten
    $query = "SELECT ordinal_position
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE table_name = '#__joomleague_version_history'
  and column_name = 'version' ";
  $db->setQuery($query);  
  $table_field = $db->loadResult();
  
  if ( !$table_field )
  {
  $query2 = "ALTER TABLE `#__joomleague_version_history` ADD `version` VARCHAR(255) NOT NULL DEFAULT '' AFTER `text`;";
  $db->setQuery($query2); 
  if (!$db->query())
				{
// 					echo '-> '.JText::_('Failed').'! <br>';
// 					$this->setError($db->getErrorMsg());
// 					echo $db->getErrorMsg();
					//return false;
				} else {
					//echo "-> done !<br>";		
				}
  
  }
  */
    
		self::_install(true);
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

	private function _dropTables()
	{
		$query="SHOW TABLES LIKE '%_joomleague%'";
		$db = JFactory::getDBO();
			
		$db->setQuery($query);
		$results = $db->loadColumn();
		foreach ($results as $result)
		{
			echo JText::_('removing database tables of JoomLeague -> '.$result).'<br>';
      $query='DROP TABLE IF EXISTS `'.$result.'`';
			$db->setQuery($query);
		  if (!$db->query())
		  {
			$this->setError($db->getErrorMsg());
			return false;
		  }
		
    }

    /*
    // leider falscher zeitpunkt
		if (!$db->query())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		*/
		return true;
	}
	
	
  
  
  public function preflight($route, $adapter) 
  {
  $mainframe =& JFactory::getApplication();
//   echo '<br>route -> '.$route.'<br>';
//   echo 'adapter -> '.$adapter.'<br>';
  
		//-----------------------------------------------------
		//Table `#__extensions` Bugfix needed due a wrong client_id for the update system
		//-----------------------------------------------------
		
		// diddipoeler
		$jversion = new JVersion();
 
    // Installing component manifest file version
    $this->release = $adapter->get( "manifest" )->version;

    // Manifest file minimum Joomla version
    $this->minimum_joomla_release = $adapter->get( "manifest" )->attributes()->version;   

    $mainframe->enqueueMessage('<p>Installing component manifest file version = ' . $this->release,'Notice');
    $mainframe->enqueueMessage('Current manifest cache commponent version = ' . $this->getParam('version'),'Notice');
    $mainframe->enqueueMessage('Installing component manifest file minimum Joomla version = ' . $this->minimum_joomla_release,'Notice');
    $mainframe->enqueueMessage('Current Joomla version = ' . $jversion->getShortVersion(),'Notice');
//     $mainframe->enqueueMessage(,'Notice');
//     $mainframe->enqueueMessage(,'Notice');
    
    // Show the essential information at the install/update back-end
    //echo '<p>Installing component manifest file version = ' . $this->release;
    //echo '<br />Current manifest cache commponent version = ' . $this->getParam('version');
    //echo '<br />Installing component manifest file minimum Joomla version = ' . $this->minimum_joomla_release;
    //echo '<br />Current Joomla version = ' . $jversion->getShortVersion();
    
    // abort if the current Joomla release is older
        if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
                Jerror::raiseWarning(null, 'Cannot install com_democompupdate in a Joomla release prior to '.$this->minimum_joomla_release);
                return false;
        }
 
        // abort if the component being installed is not newer than the currently installed version
        if ( $type == 'update' ) {
                $oldRelease = $this->getParam('version');
                $rel = $oldRelease . ' to ' . $this->release;
                if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
                        Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
                        return false;
                }
        }
        else { $rel = $this->release; }
 
        //echo '<p>' . JText::_('COM_DEMOCOMPUPDATE_PREFLIGHT_' . $type . ' ' . $rel) . '</p>';
        $mainframe->enqueueMessage(JText::_('Joomleague Preflight : ' . $route . ' ' . $rel. '</p>'),'Notice');
        
        
                    
		
	}
  
  public function postflight($route, $adapter) 
  {
  $mainframe =& JFactory::getApplication();
  $db = JFactory::getDbo();
//   echo '<br>route -> '.$route.'<br>';
//   echo 'adapter -> '.$adapter.'<br>';
  
  /*
	// always create or modify these parameters
        $params['my_param0'] = 'Component version ' . $this->release;
        $params['my_param1'] = 'Another value';
 
        // define the following parameters only if it is an original install
        if ( $type == 'install' ) {
                $params['my_param2'] = '4';
                $params['my_param3'] = 'Star';
        }
  */
  
  /*
  $params = JComponentHelper::getParams('com_joomleague');
  $xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'config.xml';
  $jRegistry = new JRegistry;
	$jRegistry->loadString($params->toString('ini'), 'ini');
	$form =& JForm::getInstance('com_joomleague', $xmlfile, array('control'=> 'params'), false, "/config");
	$form->bind($jRegistry);
	*/
	
	$paramsdata = JComponentHelper::getParams('com_joomleague');
	$paramsdefs = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomleague'.DS.'config.xml';
	$params = new JParameter( $paramsdata, $paramsdefs );
	
	$jRegistry = new JRegistry();
  //$jRegistry->loadArray($params);
  $jRegistry->loadString($params->toString('ini'), 'ini');
  $newparams = $jRegistry->toString();
	// store the combined new and existing values back as a JSON string
                                  /*
                        $db->setQuery('UPDATE #__extensions SET params = ' .
                                $db->quote( $newparams ) .
                                ' WHERE name = "joomleague"' );
                                $db->query();
                                */
	//$mainframe->enqueueMessage(JText::_('newparams<br><pre>'.print_r($newparams,true).'</pre>'   ),'');
  
  //$this->setParams( $form );
 
//        echo '<p>' . JText::_('COM_DEMOCOMPUPDATE_POSTFLIGHT ' . $route . ' to ' . $this->release) . '</p>';	
        $mainframe->enqueueMessage(JText::_('Joomleague Postflight : ' . $route . ' to ' . $this->release),'Notice');
    
        
        
                    
		$db = JFactory::getDBO();
		$query="UPDATE `#__extensions` SET client_id=1 WHERE name='joomleague'";
		$db->setQuery($query);
		if (!$db->query()) {
			echo $db->getErrorMsg();
		}
	}
	
	/*
  * get a variable from the manifest file (actually, from the manifest cache).
  */
  function getParam( $name ) {
                $db = JFactory::getDbo();
                $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "joomleague" and type = "component" ');
                $manifest = json_decode( $db->loadResult(), true );
                return $manifest[ $name ];
        }
        
  /*
         * sets parameter values in the component's row of the extension table
         */
        function setParams($param_array) {
                        $db = JFactory::getDbo();
                        
                                
                if ( count($param_array) > 0 ) {
                        // read the existing component value(s)
                        $db = JFactory::getDbo();
                        $db->setQuery('SELECT params FROM #__extensions WHERE name = "joomleague"');
                        $params = json_decode( $db->loadResult(), true );
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
                
        }      
        
	public function uninstall($adapter)
	{
		$params =& JComponentHelper::getParams('com_joomleague');
		//Also uninstall db tables of JoomLeague?
		$uninstallDB = $params->get('cfg_drop_joomleague_tables_when_uninstalled',0); 
		
		if ($uninstallDB)
		{
			echo JText::_('Also removing database tables of JoomLeague');
			self::_dropTables();
		}
		else
		{
			echo JText::_('Database tables of JoomLeague are not removed');
		}
		?>
		<div class="header">JoomLeague now has been removed from your system!</div>
		<p>We're sorry to see you go!</p>
		<p>To completely remove Joomleague from your system, be sure to also
			uninstall the JoomLeague modules and languages.</p>

		<?php
		return true;
	}
}
