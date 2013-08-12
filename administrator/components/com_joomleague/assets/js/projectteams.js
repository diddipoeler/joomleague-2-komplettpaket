/**
* @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
* @license	GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

function handleMoveLeftToRight() {
	$('teamschanges_check').value = 1;
	move($('teamslist'), $('project_teamslist'));
	selectAll($('project_teamslist'));
}

function handleMoveRightToLeft() {
	$('teamschanges_check').value = 1;
	move($('project_teamslist'), $('teamslist'));
	selectAll($('project_teamslist'));
}