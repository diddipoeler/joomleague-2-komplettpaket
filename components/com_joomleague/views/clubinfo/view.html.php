<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

require_once( JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php' );

class JoomleagueViewClubInfo extends JLGView
{

	function display( $tpl = null )
	{
		// Get a refrence of the page instance in joomla
		$document		= JFactory::getDocument();
        $mainframe = JFactory::getApplication();
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
        $this->assignRef( 'checkextrafields', $model->checkUserExtraFields() );
        //$mainframe->enqueueMessage(JText::_('clubinfo checkextrafields -> '.'<pre>'.print_r($this->checkextrafields,true).'</pre>' ),'');
		
        if ( $this->checkextrafields )
        {
            $this->assignRef( 'extrafields', $model->getUserExtraFields($club->id) );
        }
        
		$lat ='';
    $lng ='';
		
		$this->assignRef( 'project',		$project );
		$this->assignRef( 'overallconfig',	$overallconfig );
		$this->assignRef( 'config',			$config );

		$this->assignRef( 'showclubconfig',	$showclubconfig );
		$this->assignRef( 'club',			$club);
		$clubassoc			= $model->getClubAssociation($this->club->associations) ;
		$this->assignRef( 'clubassoc',			$clubassoc);

		$extended = $this->getExtended($club->extended, 'club');
		$this->assignRef( 'extended', $extended );
        $this->assignRef('model',				$model);

		$this->assignRef( 'teams',			$teams );
		$this->assignRef( 'stadiums',		$stadiums );
		$this->assignRef( 'playgrounds',	$playgrounds );
		$this->assignRef( 'showediticon',	$isEditor );

		$this->assignRef( 'address_string', $address_string);
		$this->assignRef( 'mapconfig',		$map_config ); // Loads the project-template -settings for the GoogleMap

		$this->assignRef( 'gmap',			$google_map );

    if ( ($this->config['show_club_rssfeed']) == 1 )
	  {
    $mod_name               = "mod_jw_srfr";
    $rssfeeditems = '';
    $rssfeedlink = $this->extended->getValue('COM_JOOMLEAGUE_CLUB_RSS_FEED');
    
    //echo 'rssfeed<br><pre>'.print_r($rssfeedlink,true).'</pre><br>';
    if ( $rssfeedlink )
    {
    $this->assignRef( 'rssfeeditems', $model->getRssFeeds($rssfeedlink,$this->overallconfig['rssitems']) );
    }
    else
    {
    $this->assignRef( 'rssfeeditems', $rssfeeditems );
    }
    /*
    if ( $rssfeedlink )
    {
    $srfrFeedsArray 							= explode(",",$rssfeedlink);
    $perFeedItems 								= $this->overallconfig['perFeedItems'];
    $totalFeedItems 							= $this->overallconfig['totalFeedItems'];
    $feedTimeout									= $this->overallconfig['feedTimeout'];
    $this->assignRef( 'feedTitle' , $this->overallconfig['feedTitle'] );
    $this->assignRef( 'feedFavicon' , $this->overallconfig['feedFavicon'] );
    $this->assignRef( 'feedItemTitle' , $this->overallconfig['feedItemTitle'] );
    $this->assignRef( 'feedItemDate' , $this->overallconfig['feedItemDate'] );
    $feedItemDateFormat						= $this->overallconfig['feedItemDateFormat'];
    $this->assignRef( 'feedItemDescription' , $this->overallconfig['feedItemDescription'] );
    $feedItemDescriptionWordlimit	= $this->overallconfig['feedItemDescriptionWordlimit'];
    $feedItemImageHandling				= $this->overallconfig['feedItemImageHandling'];
    $feedItemImageResizeWidth			= $this->overallconfig['feedItemImageResizeWidth'];
    $feedItemImageResampleQuality	= $this->overallconfig['feedItemImageResampleQuality'];
    $this->assignRef( 'feedItemReadMore' , $this->overallconfig['feedItemReadMore'] );
    
    $this->assignRef( 'feedsBlockPreText' ,	$this->overallconfig['feedsBlockPreText'] );
    $this->assignRef( 'feedsBlockPostText' , $this->overallconfig['feedsBlockPostText'] );
    $this->assignRef( 'feedsBlockPostLink' , $this->overallconfig['feedsBlockPostLink'] );
    $feedsBlockPostLinkURL				= $this->overallconfig['feedsBlockPostLinkURL'];
    $feedsBlockPostLinkTitle			= $this->overallconfig['feedsBlockPostLinkTitle'];
    $srfrCacheTime								= $this->overallconfig['srfrCacheTime'];
    $cacheLocation								= 'cache'.DS.$mod_name;
    $this->assignRef( 'rssfeedoutput',SimpleRssFeedReaderHelper::getFeeds($srfrFeedsArray,$totalFeedItems,$perFeedItems,$feedTimeout,$feedItemDateFormat,$feedItemDescriptionWordlimit,$cacheLocation,$srfrCacheTime,$feedItemImageHandling,$feedItemImageResizeWidth,$feedItemImageResampleQuality,$this->feedFavicon) );
    $css = JURI::root().'components/com_joomleague/assets/css/rssfeedstyle.css';
		$document->addStyleSheet($css); 
		}
        */
    
    }
    
    
    
    
    
    if (($this->config['show_maps'])==1)
	  {
	/*	
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
  */
	}
	
        if (($this->config['show_maps'])==1)
	  {
            // diddipoeler
        $this->geo = new simpleGMapGeocoder();
        $this->geo->genkml3file($this->club->id,$this->address_string,'club',$this->club->logo_big,$this->club->name);  
}

    $this->assign('show_debug_info', JComponentHelper::getParams('com_joomleague')->get('show_debug_info',0) );
    $this->assign('use_joomlaworks', JComponentHelper::getParams('com_joomleague')->get('use_joomlaworks',0) );
    
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