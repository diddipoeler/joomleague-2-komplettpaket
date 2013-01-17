<?php
/**
 * @package: 	Google Map Class
 * @author: 	Mitchelle C. Pascual (mitch.pascual at gmail dot com)
 *				http://ordinarywebguy.wordpress.com
 * @date: 		March 27, 2007
 * @warning:	Use this class at your own risk. Not recommended to set more than 20 addresses at a time.
 */
class EasyGoogleMap {

	/**
	 * @desc: 	Google Map Key
	 * @type: 	string
	 * @access: private
	 */
	var $mMapKey;

	/**
	 * @desc: 	Map Place Holder Sizes
	 * @type: 	int
	 * @access:	private
	 */
	var $mMapWidth;
	var $mMapHeight;

	/**
	 * @desc: 	Map Zoom Value
	 * @type: 	int
	 * @access:	private
	 */
	var $mMapZoom;
	
	/**
	 * @desc: 	Address Data Array Holder
	 * @type: 	array
	 * @access: private
	 */
	var $mAddressArr =  array();

	/**
	 * @desc: 	Info Window Array Holder
	 * @type: 	array
	 * @access: private
	 */
	var $mInfoWindowTextArr = array();

	/**
	 * @desc: 	Side Click Array Holder
	 * @type: 	array
	 * @access: private
	 */
	var $mSideClickArr = array();

	/**
	 * @desc: 	Var Holder of Marker Icon Color Scheme
	 * @type: 	string
	 * @access: private
	 */
	var $mDefColor;
	
	/**
	 * @desc: 	Arrays of Marker Icon Color Scheme
	 * @type: 	array
	 * @access: private
	 */
	var $mIconColor = array(
							'PACIFICA'		=>'pacifica',
							'YOSEMITE'		=>'yosemite',
							'MOAB'			=>'moab',
							'GRANITE_PINE'	=>'granitepine',
							'DESERT_SPICE'	=>'desertspice',
							'CABO_SUNSET'	=>'cabosunset',
							'TAHITI_SEA'	=>'tahitisea',
							'POPPY'			=>'poppy',
							'NAUTICA'		=>'nautica',
							'DEEP_JUNGLE'	=>'deepjungle',
							'SLATE'			=>'slate'
							);

	/**
	 * @desc: 	Var Holder of Marker Icon
	 * @type: 	string
	 * @acess: 	private
	 */
	var $mDefStyle;
	
	/**
	 * @desc: 	Arrays of Marker Icon Scheme
	 * @type: 	array
	 * @access: private
	 */
	var $mIconStyle = array(
							'FLAG'		=>array(
											'DIR'				=>'flag', 
											'ICON_W'			=>31, 
											'ICON_H'			=>35, 
											'ICON_ANCHR_W'		=>4, 
											'ICON_ANCHR_H'		=>27, 
											'INFO_WIN_ANCHR_W'	=>8, 
											'INFO_WIN_ANCHR_H'	=>3
											),
											
							'GT_FLAT'	=>array(
											'DIR'				=>'traditionalflat', 
											'ICON_W'			=>34, 
											'ICON_H'			=>35, 
											'ICON_ANCHR_W'		=>9, 
											'ICON_ANCHR_H'		=>23, 
											'INFO_WIN_ANCHR_W'	=>19, 
											'INFO_WIN_ANCHR_H'	=>0
											),
											
							'GT_PILLOW'	=>array(
											'DIR'				=>'traditionalpillow', 
											'ICON_W'			=>34, 
											'ICON_H'			=>35, 
											'ICON_ANCHR_W'		=>9, 
											'ICON_ANCHR_H'		=>23, 
											'INFO_WIN_ANCHR_W'	=>19, 
											'INFO_WIN_ANCHR_H'	=>0
											),
											
							'HOUSE'		=>array(
											'DIR'				=>'house', 
											'ICON_W'			=>24, 
											'ICON_H'			=>14, 
											'ICON_ANCHR_W'		=>9, 
											'ICON_ANCHR_H'		=>13, 
											'INFO_WIN_ANCHR_W'	=>9, 
											'INFO_WIN_ANCHR_H'	=>0
											),
											
							'PIN'		=>array(
											'DIR'				=>'pin', 
											'ICON_W'			=>31, 
											'ICON_H'			=>24, 
											'ICON_ANCHR_W'		=>17, 
											'ICON_ANCHR_H'		=>22, 
											'INFO_WIN_ANCHR_W'	=>17, 
											'INFO_WIN_ANCHR_H'	=>0
											),
											
							'PUSH_PIN'	=>array(
											'DIR'				=>'pushpin', 
											'ICON_W'			=>40, 
											'ICON_H'			=>41, 
											'ICON_ANCHR_W'		=>7, 
											'ICON_ANCHR_H'		=>38, 
											'INFO_WIN_ANCHR_W'	=>26, 
											'INFO_WIN_ANCHR_H'	=>1
											),
											
							'STAR'		=>array(
											'DIR'				=>'star', 
											'ICON_W'			=>29, 
											'ICON_H'			=>39, 
											'ICON_ANCHR_W'		=>15, 
											'ICON_ANCHR_H'		=>15, 
											'INFO_WIN_ANCHR_W'	=>19, 
											'INFO_WIN_ANCHR_H'	=>7
											)
							);

	/**
	 * @desc: Var Holder of Map Control 
	 * @type: string
	 * @access: private
	 */
	var $mDefControl;

	/**
	 * @desc: 	Arrays of Map Control Scheme
	 * @type: 	array
	 * @access: private
	 */
	var $mControl = array(
							'NONE',
							'SMALL_PAN_ZOOM',
							'LARGE_PAN_ZOOM',
							'SMALL_ZOOM'
						);

	/**
	 * @desc: 	Enable/Disable Map Continuous Zooming
	 * @type: 	boolean
	 * @acess: 	public
	 */
	var $mContinuousZoom = FALSE;
	var $mShowMarker = TRUE;
	/**
	 * @desc: 	
	 * @type: 	booleanEnable/Disable Map Double Click Zooming
	 * @access: public
	 */
	var $mDoubleClickZoom = FALSE;

	/**
	 * @desc: 	Enable/Disable Map Scale (MI/KM)
	 * @type: 	boolean
	 * @access: public
	 */
	var $mScale = TRUE;

	/**
	 * @desc: 	Enable/Disable Map Inset
	 * @type: 	boolean
	 * @acess: 	public
	 */
	var $mInset = FALSE;

	/**
	 * @desc: 	Enable/Disable Map Type (Map/Satellite/Hybrid)
	 * @type: 	boolean
	 * @acess: 	public
	 */
	var $mMapType = FALSE;

	/**
	 * @desc: 	Enable/Disable Info Window Direction Option
	 * @type: 	boolean
	 * @access: public
	 */
	#var $mDirection = TRUE;

	/**
	 * @desc: 	Index Array
	 * @type: 	int
	 * @access: private
	 */
	var $mIndex;

	/**
	 * @desc:	Constructor
	 * @param: 	string (Google Map Key)
	 * @access: public
	 * @return: void
	 */
	function EasyGoogleMap($mapKey, $mapname) {
	  $this->mMapName = $mapname;
		$this->mMapKey = $mapKey;
		$this->SetMapWidth();
		$this->SetMapHeight();
		$this->SetMapDefaultType();
		$this->SetMapZoom();
		$this->SetMarkerIconColor();
		$this->SetMarkerIconStyle();
		$this->SetMapControl();
		$this->mIndex = -1;
	} # end function

	/**
	 * @desc: 	Set Address(es)
	 * @param: 	string 
	 * @access: public
	 * @return: void
	 */
	function SetAddress($address) {
		$this->mIndex++;
		$this->mAddressArr[$this->mIndex] = $address;
		$this->mInfoWindowTextArr[$this->mIndex] = $address;
		$this->mSideClickArr[$this->mIndex] = $address;
	} # end function

	/**
	 * @desc: 	Set Info Window Text
	 * @param: 	string
	 * @access:	public
	 * @return: void
	 */
	function SetInfoWindowText($info) {
		$this->mInfoWindowTextArr[$this->mIndex] = $info;
	} # end function

	/**
	 * @desc: 	Set Side Click for Multiple Addresses
	 * @param: 	string
	 * @access:	public
	 * @return: void
	 */
	function SetSideClick($str) {
		$this->mSideClickArr[$this->mIndex] = $str;
	} # end function

	/**
	 * @desc: 	Set Map Width
	 * @param: 	int 
	 * @access:	public
	 * @return: void
	 */
	function SetMapWidth($width=300) {
		$this->mMapWidth = $width;
	} # end function

	/**
	 * @desc: 	Set Map Zoom
	 * @param: 	int
	 * @access:	public
	 * @return:	void
	 */
	function SetMapZoom($zoom=13) {
		$this->mMapZoom = $zoom;
	} # end function

	/**
	 * @desc: 	Set Map Height
	 * @param: 	int
	 * @access:	public
	 * @return:	void
	 */
	function SetMapHeight($height=300) {
		$this->mMapHeight = $height;
	} # end function

	/**
	 * @desc: 	Set Marker Icon Color Scheme
	 * @param: 	string [options('PACIFICA','YOSEMITE','MOAB','GRANITE_PINE','DESERT_SPICE','CABO_SUNSET','TAHITI_SEA','POPPY','NAUTICA','SLATE')]
	 * @access:	public
	 * @return: void
	 */
	function SetMarkerIconColor($colorScheme="PACIFICA") {
		$this->mDefColor = $colorScheme;
	} # end function

	/**
	 * @desc: 	Set Marker Icon Style Scheme
	 * @param: 	string [options('FLAG','GT_FLAT','GT_PILLOW','HOUSE','PIN','PUSH_PIN','STAR')]
	 * @access:	public
	 * @return: void
	 */
	function SetMarkerIconStyle($style="GT_FLAT") {
		$this->mDefStyle = $style;
	} # end function

	/**
	 * @desc: 	Set Map Control
	 * @param: 	string [options('G_NORMAL_MAP','G_SATELLITE_MAP','G_HYBRID_MAP')]
	 * @access:	public
	 * @return: void
	 */
	function SetMapDefaultType($control="G_HYBRID_MAP") {
		$this->mDefaultType = $control;
	} # end function
  /**
	 * @desc: 	Set Map Default Type
	 * @param: 	string [options('NONE','SMALL_PAN_ZOOM','LARGE_PAN_ZOOM','SMALL_ZOOM')]
	 * @access:	public
	 * @return: void
	 */
	function SetMapControl($control="SMALL_PAN_ZOOM") {
		$this->mDefControl = $control;
	} # end function
	/**
	 * @desc: 	Generate JS Code 
	 * @param: 	string 
	 * @access: public
	 * @return: string
	 */
	function InitJs($headtag=0) {
        $ret = "";
		# show error if misconfigured
		$is_error = $this->CheckConf();
		if ($is_error) { 
			$ret = $is_error; 
		} else {		
			$cnt_add = count($this->mAddressArr);
			
			$color = $this->mIconColor[$this->mDefColor];
			$dir = $this->mIconStyle[$this->mDefStyle]['DIR'];
	
			$icon_w  = $this->mIconStyle[$this->mDefStyle]['ICON_W'];
			$icon_h  = $this->mIconStyle[$this->mDefStyle]['ICON_H'];
	
			$icon_anchr_w  = $this->mIconStyle[$this->mDefStyle]['ICON_ANCHR_W'];
			$icon_anchr_h  = $this->mIconStyle[$this->mDefStyle]['ICON_ANCHR_H'];
	
			$info_win_anchr_w  = $this->mIconStyle[$this->mDefStyle]['INFO_WIN_ANCHR_W'];
			$info_win_anchr_h  = $this->mIconStyle[$this->mDefStyle]['INFO_WIN_ANCHR_H'];
			
			# start of JS SCRIPT		
      $ret .= "<script type=\"text/javascript\">\n";
      $ret .= "// <![CDATA[ \n";
      
			$ret .= "var gmarkers = [];\n";
			$ret .= "var address = [];\n";
			$ret .= "var points = [];\n";
				
			$ret .= "if(GBrowserIsCompatible()) { \n";
			$ret .= "	var ". $this->mMapName . " = new GMap2(document.getElementById('".$this->mMapName."')); \n";
			//$ret .= "	". $this->mMapName . ".setMapType(". $this->mDefaultType . "); \n";
	    $ret .= " var dirn = new GDirections(".$this->mMapName.");  \n";
			# handle map continuous zooming
			$ret .= ($this->mContinuousZoom==TRUE)?"	".$this->mMapName.".enableContinuousZoom(); \n":"";
	
			# handle map double click zooming
			$ret .= ($this->mDoubleClickZoom==TRUE)?"	".$this->mMapName.".enableDoubleClickZoom(); \n":"";
	
			# handle map controls
			$mapCtrl = "";
			switch ($this->mDefControl) {
				case 'NONE':
					$mapCtrl = "";
					break;
					
				case 'SMALL_PAN_ZOOM':
					$mapCtrl = $this->mMapName.".addControl(new GSmallMapControl()); \n";
					break;
					
				case 'LARGE_PAN_ZOOM':
					$mapCtrl = $this->mMapName.".addControl(new GLargeMapControl()); \n";
					break;
	
				case 'SMALL_ZOOM':
					$mapCtrl = $this->mMapName.".addControl(new GSmallZoomControl()); \n";
					break;
				
				default;
					break;
			
			} # end switch
			$ret .= "	$mapCtrl";
			
			# handle map scale (mi/km)
			$ret .= ($this->mScale==TRUE)?"	".$this->mMapName.".addControl(new GScaleControl()); \n":"";
	
			# handle map type (map/satellite/hybrid)
			$ret .= ($this->mMapType==TRUE)?"	".$this->mMapName.".addControl(new GMapTypeControl()); \n":"";
	
			# handle map inset
			$ret .= ($this->mInset==TRUE)?"	".$this->mMapName.".addControl(new GOverviewMapControl()); \n":"";
	    $ret .= " ".$this->mMapName.".setCenter(new GLatLng(43.907787,-79.359741), 9, ".$this->mDefaultType."); \n";
			$ret .= "	var geocoder = new GClientGeocoder(); \n";
			$ret .= "	var icon = new GIcon(); \n";
			$ret .= "	icon.image = 'http://google.webassist.com/google/markers/$dir/$color.png'; \n";
			$ret .= "	icon.shadow = 'http://google.webassist.com/google/markers/$dir/shadow.png'; \n";
			$ret .= "	icon.iconSize = new GSize($icon_w,$icon_h); \n";
			$ret .= "	icon.shadowSize = new GSize($icon_w,$icon_h); \n";
			$ret .= "	icon.iconAnchor = new GPoint($icon_anchr_w,$icon_anchr_h); \n";
			$ret .= "	icon.infoWindowAnchor = new GPoint($info_win_anchr_w,$info_win_anchr_h); \n";
			$ret .= "	icon.printImage = 'http://google.webassist.com/google/markers/$dir/$color.gif'; \n";
			$ret .= "	icon.mozPrintImage = 'http://google.webassist.com/google/markers/$dir/{$color}_mozprint.png'; \n";
			$ret .= "	icon.printShadow = 'http://google.webassist.com/google/markers/$dir/shadow.gif'; \n";
			$ret .= "	icon.transparent = 'http://google.webassist.com/google/markers/$dir/{$color}_transparent.png'; \n\n";

			# loop set address(es)
			if ($cnt_add > 0) {
			for ($i=$cnt_add-1; $i>=0; $i--) {
				$ret .= "	var address_$i = {\n";
				$ret .= "	  infowindowtext: ".json_encode(str_replace(array("\r\n", "\n"), "<br/>", $this->mInfoWindowTextArr[$i])).",\n";
				$ret .= "	  full: ".json_encode(str_replace(array("\r\n", "\n"), ",", $this->mAddressArr[$i]))."\n";
				$ret .= "	};\n\n";

				$ret .= "	address[$i] = address_$i.infowindowtext;\n\n";
				
				$ret .= "	geocoder.getLatLng (\n";
				$ret .= "	  address_$i.full,\n";
				$ret .= "	  function(point) {\n";
				$ret .= "		if(point) {\n";
				$ret .= "		  points[$i] = point; \n";	
				$ret .= "		  ".$this->mMapName.".setCenter(point, {$this->mMapZoom}, null);\n";
				$ret .= "		  var marker = new GMarker(point, icon);\n";
				$ret .= "		  GEvent.addListener(marker, 'click', function() {\n";
				$ret .= "			marker.openInfoWindowHtml(address_$i.infowindowtext);\n";
				$ret .= "			var CopyrightDiv = ".$this->mMapName.".firstChild.nextSibling;\n";
				$ret .= "			var CopyrightImg = ".$this->mMapName.".firstChild.nextSibling.nextSibling;\n";
				$ret .= "			CopyrightDiv.style.display = 'none'; \n";
				$ret .= "			CopyrightImg.style.display = 'none'; \n";
				$ret .= "		  });\n";

				$ret .= "		  ".$this->mMapName.".addOverlay(marker);\n";

				# show only info window to the first set address
				if ($i===0) 
					$ret .= ($this->mShowMarker==TRUE) ? "		  marker.openInfoWindowHtml(address_$i.infowindowtext);\n":"";
				
				$ret .= "		  gmarkers[$i] = marker;\n";

				$ret .= "		}\n";
				$ret .= "		else {\n";
				$ret .= "		  ".$this->mMapName.".setCenter(new GLatLng(37.4419, -122.1419), {$this->mMapZoom});\n";
				$ret .= "		}\n";
				$ret .= "	  }\n";
				$ret .= "	); // end geocoder.getLatLng\n\n";
			
		}} # end for
			$ret .= "} // end if\n\n";
			
			$ret .= "function sideClick(i) {\n";
			$ret .= "   if (gmarkers[i]) {\n";
			$ret .= "	  gmarkers[i].openInfoWindowHtml(address[i]);\n";
			$ret .= "	  ".$this->mMapName.".setCenter(points[i],{$this->mMapZoom});\n";
			$ret .= "   } else {\n";
			$ret .= "	  var htstring = address[i];\n";
			$ret .= "	  var stripped = htstring.replace(/(<([^>]+)>)/ig,'');\n";
			$ret .= "	  alert('Location not found: ' +  stripped);\n";
			$ret .= "   } /*endif*/\n";
			$ret .= "} /*end function */\n";
			$ret .= "// ]]>\n";
			$ret .= "</script>\n";
		} # end if
	if ($headtag == 1) {
		$mainframe	= JFactory::getApplication();
		$mainframe->addCustomHeadTag( $ret );
	}
		else return $ret;
	} # end function

	/**
	 * @desc: 	Generate JS for Map Key (static)
	 * @access: public
	 * @return: string
	 */
	function GmapsKey() {
		return "<script type=\"text/javascript\" src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;key={$this->mMapKey}\"></script>\n";	
	} # end function

	/**
	 * @desc: 	Generate Links for Multiple Addresses (static)
	 * @access: public
	 * @return: string
	 */
	function GetSideClick() {
		$ret = "";
		$loop = count($this->mAddressArr);
		for ($i=0; $i<$loop; $i++) {
			$ret .=	"<a href=\"javascript:void($i);\" onclick=\"javascript:sideClick($i);\">{$this->mSideClickArr[$i]}</a><br />\n";
		} # end for

		return $ret;
	} # end function

	/**
	 * @desc: 	Generate Map Holder/Container (static)
	 * @access: public
	 * @return: string
	 */
	function MapHolder() {
		return "<div id=\"".$this->mMapName."\" style=\"width:".$this->mMapWidth.";height:".$this->mMapHeight."px;margin:auto;\"></div>";
	} # end function

	/**
	 * @desc: 	Generate Unloading Script for Google Map (static)
	 * @access: public
	 * @return: string
	 */
	function UnloadMap() {
		return '<script type="text/javascript">window.onunload = function() { GUnload(); }</script>';
	} # end function

	/**
	 * @desc: 	Check Passed Method Parameters
	 * @access: private
	 * @return: string
	 */
	function CheckConf() {
		$ret = "";
		# map height and width
		if (!is_numeric($this->mMapHeight)) 
			$ret .= "<h1>INVALID SetMapWidth() OR SetMapHeight() PARAMETER</h1><br />\n";		
		
		# map control
		if (!in_array($this->mDefControl, $this->mControl)) {
			$ret .= "<h1>INVALID setMapControl() PARAMETER:  $this->mDefControl</h1><br />\n";
			$ret .= "<b>POSSIBLE PARAMETER VALUES: </b><br />\n";
			foreach ($this->mControl as $option=>$value) {
				$ret .= "=>'$option' <br />\n";
			} # end foreach
		} # end if

		# color
		if (!array_key_exists($this->mDefColor, $this->mIconColor)) {
			$ret .= "<h1>INVALID setMarkerIconColor() PARAMETER:  $this->mDefColor</h1><br />\n";
			$ret .= "<b>POSSIBLE PARAMETER VALUES: </b><br />\n";
			foreach ($this->mIconColor as $option=>$value) {
				$ret .= "=>'$option' <br />\n";
			} # end foreach
		} # end if
			
		# style
		if (!array_key_exists($this->mDefStyle, $this->mIconStyle)) {
			$ret .= "<h1>INVALID setMarkerIconStyle() PARAMETER: $this->mDefStyle</h1><br />\n";
			$ret .= "<b>POSSIBLE PARAMETER VALUES: </b><br />\n";
			foreach ($this->mIconStyle as $option=>$value) {
				$ret .= "=>'$option' <br />\n";
			} # end foreach
		} # end if
	
		return $ret;
	} # end function
	function InitRoutePopupJs ($title) {
    ?>
    <script type="text/javascript">
function popupRoute(myname, w, h, scroll) {
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	var content = $('path').innerHTML;
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable'
	msgWindow = window.open('', myname, winprops)
	msgWindow.document.open();
	msgWindow.document.write('<html>');
  msgWindow.document.write('<head>');
  msgWindow.document.write('<title>Anfahrt <?php echo $title;?></title>');
  
  msgWindow.document.write('<style type="text/css">');
  msgWindow.document.write('<!--');
  msgWindow.document.write('body{font-family:verdana,arial,sans-serif;font-size:12px;background-color:#F2F3F5;}');
  msgWindow.document.write('@media print { a { display:none;}');

  msgWindow.document.write('-->');
  msgWindow.document.write('</style>');
  msgWindow.document.write('</head>');
  msgWindow.document.write('<body onload="window.print()" onerror="return true;">');
  msgWindow.document.write('<div class="noprint" style="text-align:right;"><a href="javascript:void(0)" onclick="self.close()">x</a></div>');
  msgWindow.document.write('<div class="noprint" style="text-align:right"><a href="javascript:void(0)" onclick="window.print()">print it now baby</a></div>');
  msgWindow.document.write(content);
  msgWindow.document.write('<div class="noprint" style="text-align:right"><a href="javascript:void(0)" onclick="window.print()">print it now baby</a></div>');
  msgWindow.document.write('<div class="noprint" style="text-align:right"><a href="javascript:void(0)" onclick="self.close()">x</a></div>');
  msgWindow.document.write('</body>');
  msgWindow.document.write('</html>');
  msgWindow.document.close();

	if (parseInt(navigator.appVersion) >= 4) { msgWindow.window.focus(); }
}
</script>
<?php
  }
  function RouteForm () {
    
    ?>
    <form onsubmit="if($('saddr').value!='') {loadRoute();return false;} else {alert('INSERT ADDRESS');return false;}">
     <input type="hidden" id="daddr" name="daddr" value="<?php echo $this->mAddressArr[0];?>" />
     <?php echo JText::_('COM_JOOMLEAGUE_GMAP_DIRECTIONS_TO');?>: <input type="text" class="inputbox" id="saddr" name="saddr" value="" />
     <input class="inputbox" type="button" onclick="if($('saddr').value!='') loadRoute()" value="<?php echo JText::_('COM_JOOMLEAGUE_GMAP_DIRECTIONS_SHOW');?>" />
     </form><span id="print"></span>
     <div id="path" style="overflow:auto;height:auto;"></div>
  <?php
  }
  
  function InitRouteJs() {
    ?>
    <script type="text/javascript">
    //<![CDATA[
    var reasons=[];
 
      reasons[G_GEO_SUCCESS]            = "Success";
      reasons[G_GEO_MISSING_ADDRESS]    = "Missing Address: The address was either missing or had";
      reasons[G_GEO_UNKNOWN_ADDRESS]    = "Unknown Address:  No corresponding geographic location";
      reasons[G_GEO_UNAVAILABLE_ADDRESS]= "Unavailable Address:  The geocode for the given address";
      reasons[G_GEO_BAD_KEY]            = "Bad Key: The API key is either invalid or does not match";
      reasons[G_GEO_TOO_MANY_QUERIES]   = "Too Many Queries: The daily geocoding quota for this site";
      reasons[G_GEO_SERVER_ERROR]       = "Server error: The geocoding request could not be successfully processed.";
    // === create a GDirections Object ===
      if (GBrowserIsCompatible()) {
     var printbutton = '<a href="javascript:void(0)" onclick="if ($(\'path\').innerHTML!=\'\') popupRoute(\'route\', 600, 400, 1)">ausdrucken</a>';

     function loadRoute(saddr) {      
       $('print').innerHTML = '';
       $('path').innerHTML = '';
      GEvent.addListener(dirn,"error", function() {
        $('print').innerHTML = '';
        
        alert("Directions Failed: "+reasons[dirn.getStatus().code]);
      });
      dirn.load("from: " + $('saddr').value + " to: " + $('daddr').value, {getSteps:true});
      
     }
      
      // ============ custom direction panel ===============
      function customPanel(map,mapname,dirn,div) {
        var html = "";
      
        // ===== local functions =====

        // === waypoint banner ===
        function waypoint(point, type, address) {
          var target = '"' + mapname+".showMapBlowup(new GLatLng("+point.toUrlValue(6)+"))"  +'"';
          html += '<table style="border: 1px solid silver; margin: 10px 0px; background-color: rgb(238, 238, 238); border-collapse: collapse; color: rgb(0, 0, 0);">';
          html += '  <tr style="cursor: pointer;" onclick='+target+'>';
          html += '    <td style="padding: 4px 15px 0px 5px; vertical-align: middle; width: 20px;">';
          html += '      <img src="http://www.google.com/intl/en_ALL/mapfiles/icon-dd-' +type+ '-trans.png">'
          html += '    </td>';
          html += '    <td style="vertical-align: middle; width: 100%;">';
          html +=        address;
          html += '    </td>';
          html += '  </tr>';
          html += '</table>';
        }

        // === route distance ===
        function routeDistance(dist) {
          html += '<div style="text-align: right; padding-bottom: 0.3em;">' + dist + '</div>';
        }      

        // === step detail ===
        function detail(point, num, description, dist) {
          var target = '"' + mapname+".showMapBlowup(new GLatLng("+point.toUrlValue(6)+"))"  +'"';
          html += '<table style="margin: 0px; padding: 0px; border-collapse: collapse;">';
          html += '  <tr style="cursor: pointer;" onclick='+target+'>';
          html += '    <td style="border-top: 1px solid rgb(205, 205, 205); margin: 0px; padding: 0.3em 3px; vertical-align: top; text-align: right;">';
          html += '      <a href="javascript:void(0)"> '+num+'. </a>';
          html += '    </td>';
          html += '    <td style="border-top: 1px solid rgb(205, 205, 205); margin: 0px; padding: 0.3em 3px; vertical-align: top; width: 100%;">';
          html +=        description;
          html += '    </td>';
          html += '    <td style="border-top: 1px solid rgb(205, 205, 205); margin: 0px; padding: 0.3em 3px 0.3em 0.5em; vertical-align: top; text-align: right;">';
          html +=        dist;
          html += '    </td>';
          html += '  </tr>';
          html += '</table>';
        }

        // === Copyright tag ===
        function copyright(text) {
          html += '<div style="font-size: 0.86em;">' + text + "</div>";
        }
        

        // === read through the GRoutes and GSteps ===

        for (var i=0; i<dirn.getNumRoutes(); i++) {
          if (i==0) {
            var type="play";
          } else {
            var type="pause";
          }
          var route = dirn.getRoute(i);
          var geocode = route.getStartGeocode();
          var point = route.getStep(0).getLatLng();
          // === Waypoint at the start of each GRoute
          waypoint(point, type, geocode.address);
          routeDistance(route.getDistance().html+" (~ "+route.getDuration().html+")");

          for (var j=0; j<route.getNumSteps(); j++) {
            var step = route.getStep(j);
            // === detail lines for each step ===
            detail(step.getLatLng(), j+1, step.getDescriptionHtml(), step.getDistance().html);
          }
        }
        
        // === the final destination waypoint ===   
        var geocode = route.getEndGeocode();
        var point = route.getEndLatLng();
        waypoint(point, "stop", geocode.address);
                 
        // === the copyright text ===
        copyright(dirn.getCopyrightsHtml());

        // === drop the whole thing into the target div
        div.innerHTML = html;
        $('print').innerHTML = printbutton;
      } // ============ end of customPanel function ===========


      // ========== launch the custom Panel creator a millisecond after the GDirections finishes loading ==========
      // == The delay is required in case we rely on GDirections to perform the initial setCenter ==
      GEvent.addListener(dirn,"load", function() {
        setTimeout('customPanel(<?php echo $this->mMapName;?>,"<?php echo $this->mMapName;?>",dirn,document.getElementById("path"))', 1);
      });

    }
    //]]>
    </script>
    <?php
  }
} # end class
?>