/**
* @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
* @license	GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

window.addEvent('domready',function(){
	if($('populate_enter_division')) {
		$('populate_enter_division').hide();
		$$('table.adminlist tr').each(function(el){
			var cb;
			if (cb=el.getElement("input[name^=cid]")) {
				el.getElement("input[name^=roundcode]").addEvent('change',function(){
					if (isNaN(this.value)) {
						alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_ROUNDS_CSJS_MSG_NOTANUMBER'));
						return false;
					}
				});
			}
		});
	}
	if($('buttonup')) {
		$('buttonup').addEvent('click', function(){
			moveOptionUp('teamsorder');
		});
		$('buttondown').addEvent('click', function(){
			moveOptionDown('teamsorder');
		});
	}
});

Joomla.submitbutton = function(pressbutton) {
	var ret = true;
	var validator = document.formvalidator;
	var form = $('adminForm');

	if (pressbutton == 'round.populate') {
		if($('populate_enter_division')) {
			$('populate_enter_division').show();
			ret = false;
		}
	}

	if(pressbutton == 'round.startpopulate') {
		$('teamsorder').getElements('option').each(function(el) {
			el.setProperty('selected', 'selected');
		});
		Joomla.submitform(pressbutton);
		return;
	}

	if (ret) {
		Joomla.submitform(pressbutton);
	} else {
		return false;
	}
}