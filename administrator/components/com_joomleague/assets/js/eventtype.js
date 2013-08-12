/**
* @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
* @license	GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

Joomla.submitbutton = function(pressbutton) {
	var res = true;
	var validator = document.formvalidator;
	var form = $('adminForm');
	
	if (pressbutton == 'eventtype.cancel') {
		Joomla.submitform(pressbutton);
		return;
	}

	// do field validation
	if (validator.validate(form.name) === false) {
		alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_EVENTTYPE_CSJS_NAME_REQUIRED'));
		form.name.focus();			
		res = false;
	}
	
	if (res) {
		Joomla.submitform(pressbutton);
	} else {
		return false;
	}
}

function updateEventIcon(path) {
	var icon = $('image');
	icon.src = '<?php echo JUri::root(); ?>' + path;
	icon.alt = path;
	icon.value = path;
	var logovalue = $('icon');
	logovalue.value = path;
}
