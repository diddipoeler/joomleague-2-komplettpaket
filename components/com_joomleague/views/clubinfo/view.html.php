<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

require_once( JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php' );

class JoomleagueViewClubInfo extends JLGView
{

	function display( $tpl = null )
	{
		// Get a refrence of the page instance in joomla
		$document		= JFactory::getDocument();
		$model			= $this->getModel();
		$club			= $model->getClub() ;
		$config			= $model->getTemplateConfig( $this->getName() );	
		$project 		= $model->getProject();
		$overallconfig	= $model->getOverallConfig();
		$teams			= $model->getTeamsByClubId();
		$stadiums	 	= $model->getStadiums();
		$playgrounds	= $model->getPlaygrounds();
		$isEditor		= $model->hasEditPermission('club.edit');
		$address_string = $model->getAddressString();
		$map_config		= $model->getMapConfig();
		$google_map		= $model->getGoogleMap( $map_config, $address_string );
		
		$lat ='';
    $lng ='';
		
		$this->assignRef( 'project',		$project );
		$this->assignRef( 'overallconfig',	$overallconfig );
		$this->assignRef( 'config',			$config );

		$this->assignRef( 'showclubconfig',	$showclubconfig );
		$this->assignRef( 'club',			$club);

		$extended = $this->getExtended($club->extended, 'club');
		$this->assignRef( 'extended', $extended );

		$this->assignRef( 'teams',			$teams );
		$this->assignRef( 'stadiums',		$stadiums );
		$this->assignRef( 'playgrounds',	$playgrounds );
		$this->assignRef( 'showediticon',	$isEditor );

		$this->assignRef( 'address_string', $address_string);
		$this->assignRef( 'mapconfig',		$map_config ); // Loads the project-template -settings for the GoogleMap

		$this->assignRef( 'gmap',			$google_map );

    if (($this->config['show_maps'])==1)
	  {
	  
	  /*
	  foreach ( $extended->getGroups() as $key => $groups )
		{
		$lat = $extended->get('JL_ADMINISTRATIVE_AREA_LEVEL_1_LATITUDE');
    $lng = $extended->get('JL_ADMINISTRATIVE_AREA_LEVEL_1_LONGITUDE');
		}
		*/
		
	  $this->map = new simpleGMapAPI();
  $this->geo = new simpleGMapGeocoder();
  $this->map->setWidth($this->mapconfig['width']);
  $this->map->setHeight($this->mapconfig['height']);
  $this->map->setZoomLevel($this->mapconfig['map_zoom']); 
  $this->map->setMapType($this->mapconfig['default_map_type']);
  $this->map->setBackgroundColor('#d0d0d0');
  $this->map->setMapDraggable(true);
  $this->map->setDoubleclickZoom(false);
  $this->map->setScrollwheelZoom(true);
  $this->map->showDefaultUI(false);
  $this->map->showMapTypeControl(true, 'DROPDOWN_MENU');
  $this->map->showNavigationControl(true, 'DEFAULT');
  $this->map->showScaleControl(true);
  $this->map->showStreetViewControl(true);
  $this->map->setInfoWindowBehaviour('SINGLE_CLOSE_ON_MAPCLICK');
  $this->map->setInfoWindowTrigger('CLICK');
  
  $this->map->addMarkerByAddress($this->address_string, $this->club->name, $this->address_string, "http://maps.google.com/mapfiles/kml/pal2/icon49.png");  
  if ( $lat && $lng )
  {
  //$this->map->addMarker($lat, $lng, $this->club->name, $this->address_string,JURI::root().'media/com_joomleague/map_icons/'.'icon49.png' );
  //$this->map->addMarker($lat, $lng, $this->club->name, $this->address_string,JURI::root().'media/com_joomleague/placeholders/'.'placeholder_150.png' );  
  }
  
  $document->addScript($this->map->JLprintGMapsJS());
  $document->addScriptDeclaration($this->map->JLshowMap(false));
  
	}
	  
    $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
		$pageTitle = JText::_( 'COM_JOOMLEAGUE_CLUBINFO_PAGE_TITLE' );
		if ( isset( $this->club ) )
		{
			$pageTitle .= ': ' . $this->club->name;
		}
		$document->setTitle( $pageTitle );
		parent::display( $tpl );
	}
}
?>