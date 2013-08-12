/**
* @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
* @license	GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

//add 1.5 compatibility layer
window.addEvent('domready', function() {
	if($('adminForm')) {
		$('adminForm').setProperty('name', 'adminForm');
	}
});

function joomleague_changedoc(docid){
  if (docid != "" && docid.options[docid.options.selectedIndex].value!="") {
    window.location.href = docid.options[docid.options.selectedIndex].value;
  }
}

/**
 * toggle object visibility
 * @param obj the object to show/hide
 */       
function visibleMenu(obj) {
	var joomleague_el = document.getElementById(obj);
	if ( joomleague_el.style.visibility != "hidden" ) {
		joomleague_el.style.visibility = 'hidden';
	}
	else {
		joomleague_el.style.visibility = 'visible';
	}
}

function switchMenu(obj) {
	var joomleague_el = document.getElementById(obj);
	if ( joomleague_el.style.display != "none" ) {
		joomleague_el.style.display = 'none';
	}
	else {
		joomleague_el.style.display = 'block';
	}
}

/**
 * hide objects
 * @param array objs the objects to hide
 */
function collapseAll(objs) {
  var i;
  for (i=0;i<objs.length;i++ ) {
    objs[i].style.display = 'none';
  }
}

function move(fbox, tbox) {
	var arrFbox = new Array();
	var arrTbox = new Array();
	var arrLookup = new Array();
	var i;
	for (i = 0; i < tbox.options.length; i++) {
		arrLookup[tbox.options[i].text] = tbox.options[i].value;
		arrTbox[i] = tbox.options[i].text;
	}
	var fLength = 0;
	var tLength = arrTbox.length
	for (i = 0; i < fbox.options.length; i++) {
		arrLookup[fbox.options[i].text] = fbox.options[i].value;
		if (fbox.options[i].selected && fbox.options[i].value != "") {
			arrTbox[tLength] = fbox.options[i].text;
			tLength++;
		} else {
			arrFbox[fLength] = fbox.options[i].text;
			fLength++;
		}
	}
	fbox.length = 0;
	tbox.length = 0;
	var c;
	for (c = 0; c < arrFbox.length; c++) {
		var no = new Option();
		no.value = arrLookup[arrFbox[c]];
		no.text = arrFbox[c];
		fbox[c] = no;
	}
	for (c = 0; c < arrTbox.length; c++) {
		var no = new Option();
		no.value = arrLookup[arrTbox[c]];
		no.text = arrTbox[c];
		tbox[c] = no;
	}
}

function selectAll(box) {
	for ( var i = 0; i < box.length; i++) {
		box[i].selected = true;
	}
}

function moveOptionUp(selectId) {
	var selectList = document.getElementById(selectId);
	var selectOptions = selectList.getElementsByTagName('option');
	for ( var i = 1; i < selectOptions.length; i++) {
		var opt = selectOptions[i];
		if (opt.selected) {
			selectList.removeChild(opt);
			selectList.insertBefore(opt, selectOptions[i - 1]);
			return true;
		}
	}
}

function moveOptionDown(selectId) {
	var selectList = document.getElementById(selectId);
	var selectOptions = selectList.getElementsByTagName('option');
	for ( var i = 0; i < selectOptions.length - 1; i++) {
		var opt = selectOptions[i];
		if (opt.selected) {
			var next = selectOptions[i + 1];
			selectList.removeChild(next);
			selectList.insertBefore(next, selectOptions[i]);
			return true;
		}
	}
}

function registerhome(homepage,notes,homepagename)
	{
var url='http://www.fussballineuropa.de/jlpaket.php';		
var data = 'homepage='+homepage+'&notes='+notes+'&homepagename='+homepagename;
var url2='http://www.fussballineuropa.de/jlpaket.php?'+'homepage='+homepage+'&notes='+notes+'&homepagename='+homepagename;
var request = new Request({
                        url: url2,
                        method:'post',
                        data: data
                        }).send();
                        		
		}