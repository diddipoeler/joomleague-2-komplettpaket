<script type="text/javascript" language="JavaScript">
	jlhide<?php echo $params->get( 'moduleclass_sfx' ); ?>=1;
	function jlticker<?php echo $params->get( 'moduleclass_sfx' ); ?>() {
	    if(jlhide<?php echo $params->get( 'moduleclass_sfx' ); ?>><?php echo $resultsmatch; ?>) {
	    	var id_hide = (jlhide<?php echo $params->get( 'moduleclass_sfx' ) ?>-1)+'<?php echo $params->get( 'moduleclass_sfx' ); ?>';
			if(document.getElementById('jlticker'+id_hide)) {
				document.getElementById('jlticker'+id_hide).style.display="none";
			}
			jlhide<?php echo $params->get( 'moduleclass_sfx' ) ?>=1;
	    }
		var id_hide = (jlhide<?php echo $params->get( 'moduleclass_sfx' ) ?>-1)+'<?php echo $params->get( 'moduleclass_sfx' ); ?>';
		var id_show = (jlhide<?php echo $params->get( 'moduleclass_sfx' ) ?>)+'<?php echo $params->get( 'moduleclass_sfx' ); ?>';
		if(document.getElementById('jlticker'+id_hide)) {
			document.getElementById('jlticker'+id_hide).style.display="none";
		}
		if(document.getElementById('jlticker'+id_show)) {
			document.getElementById('jlticker'+id_show).style.display="block";
		}
	    jlhide<?php echo $params->get( 'moduleclass_sfx' ); ?>++;
	}

	window.setTimeout("jlticker<?php echo $params->get( 'moduleclass_sfx' ); ?>()",100);
	window.setInterval("jlticker<?php echo $params->get( 'moduleclass_sfx' ); ?>()", <?php echo $tickerpause ?>000);
</script>
