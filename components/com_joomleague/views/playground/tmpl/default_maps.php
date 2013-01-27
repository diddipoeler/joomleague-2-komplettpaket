<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<div style="width: 100%; float: left">
	<div class="contentpaneopen">
		<div class="contentheading">
			<?php echo JText::_('COM_JOOMLEAGUE_GMAP_DIRECTIONS'); ?>
		</div>
	</div>
<?php


    

		$arrPluginParams = array();
		
		$arrPluginParams[] = "zoomWheel='1'";
		
		$param = 'default_map_type';
		if($this->mapconfig[$param]) {
			$arrPluginParams[] = "mapType='".$this->mapconfig[$param]."'";
		}
		$param = 'map_control';
		if($this->mapconfig[$param]) {
			$arrPluginParams[] = "zoomType='".$this->mapconfig[$param]."'";
		}
		$param = 'width';
		if($this->mapconfig[$param]) {
			$arrPluginParams[] = "$param='".$this->mapconfig[$param]."'";
		}
		$param = 'height';
		if($this->mapconfig[$param]) {
			$arrPluginParams[] = "$param='".$this->mapconfig[$param]."'";
		}
		
		if($this->address_string != '') {
			$arrPluginParams[] = "address='" .$this->address_string. "'";
			$arrPluginParams[] = "text='<div style=width:250px;height=30px;>".$this->address_string."</div>'";
		}
		$icon = '';
		if($this->club->logo_small != '')
		{
			$arrPluginParams[] = "tooltip='". $this->playground->name . "'";
			$icon= $this->club->logo_small;
		}
		if($icon!='') {
			$arrPluginParams[] = "icon='".$icon."'";
		}
		$params  = '{mosmap ';
		$params .= implode('|', $arrPluginParams);
		$params .= "}";
		echo JHTML::_('content.prepare', $params);
	    
?>
</div>