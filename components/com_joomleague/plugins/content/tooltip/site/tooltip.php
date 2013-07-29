<?php
defined( '_JEXEC' ) or die( 'Restricted Access');
jimport('joomla.plugin.plugin');
//--Include MooTools

class plgContenttooltip extends JPlugin {
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	function tooltip($id,$kategorie_id)
	{
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__content WHERE catid='$kategorie_id' AND id='$id'";
		$db->setQuery( $query );
		$artikel = $db->loadObject();
		
		$ausgabe = $artikel->introtext;
		
			$arr = preg_split("/src=\"/", $ausgabe);
			$ausgabe = $arr[0];
			if (count($arr)>= 2) {
			$ausgabe.='src= "'.JURI::root();
			}
			$ausgabe.= $arr[1];
		$ausgabe = strtr($ausgabe, '"', '\'');
		return $ausgabe;
	}// function
	
	function onContentPrepare( $context, &$row, &$params, $page = 0) {
		JHTML::_('behavior.mootools');
		$document =& JFactory::getDocument();
		$js = "
				var TipX = ".$this->params->get('tipx', '0').";
				var TipY = ".$this->params->get('tipy', '0').";
				var OpaCity = ".$this->params->get('opacity', '0').";
				var Fixed = ".$this->params->get('fixed', 'true')."e;
				";
		
		$document->addScriptDeclaration( $js );

		
		$kategorie_id =$this->params->get('kategorie_id', '1');
		$count =$this->params->get('count', '-1');
		$css = ".tip {".$this->params->get('tooltip_fenster')."}";
		$css .= ".tip-title {".$this->params->get('tooltip_titel')."}";
		$css .= ".tip-text {".$this->params->get('tooltip_text')."}";
		$css .= ".zoomTip {".$this->params->get('tooltip_link')."}";
		//$document->addStyleDeclaration( $css);
		
		$document->addScript( JURI::root(true).'/plugins/content/tooltip/tooltip.js' );

		$db			=& JFactory::getDBO();
		$query = "SELECT * FROM #__content WHERE catid='$kategorie_id'";
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		
		if ($rows) {
			foreach ($rows as $trow) {
				$titel = $trow->title;
				$id = $trow->id;
				
		$regex ="/$titel\b/";
		
			
		$repl = '<span class="zoomTip" title="'
				.$titel
				.'" rel="'
				.$ausgabe=$this->tooltip($id,$kategorie_id)
				.'">'
				.$titel
				.'</span>';
				   
		$row->text = preg_replace($regex, $repl, $row->text, $count);
		}
		}	
	$row->text .='<style type="text/css">
    					<!--'
					.$css
					.' -->
 					 </style>';
	}
}
?>
