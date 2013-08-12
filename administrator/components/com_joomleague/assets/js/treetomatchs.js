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
	var form = $('adminForm');
	if($('node_matcheslist')) {
		var mylist = $('node_matcheslist');
		for ( var i = 0; i < mylist.length; i++) {
			mylist[i].selected = true;
		}
	}
	Joomla.submitform(pressbutton);
}

function handleLeftToRight() {
	$('matcheschanges_check').value = 1;
	move($('matcheslist'), $('node_matcheslist'));
	selectAll($('node_matcheslist'));
}

function handleRightToLeft() {
	$('matcheschanges_check').value = 1;
	move($('node_matcheslist'), $('matcheslist'));
	selectAll($('node_matcheslist'));
}
