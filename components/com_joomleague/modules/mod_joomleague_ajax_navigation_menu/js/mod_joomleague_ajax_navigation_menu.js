var jlcinjectcontainer = new Array();
var jlcmodal = new Array();

window.addEvent('domready', function() {
	SqueezeBox.initialize({});
});

function jlcnewAjax() {
	/* THIS CREATES THE AJAX OBJECT */
	var xmlhttp = false;
	try {
		// ajax object for non IE navigators
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			// ajax object for IE
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
		}
	}
	if (!xmlhttp && typeof XMLHttpRequest != "undefined") {
		xmlhttp = new XMLHttpRequest();
	}

	return xmlhttp;
}

function jlamteam(modid)
{
//alert('jlamteam_modid=' + modid);

var seasonid = 0;
var leagueid = 0;
var projectid = 0;
var divisionid = 0;
var teamid = 0;

seasonid = document.getElementById("jlamseason" + modid).options[document.getElementById("jlamseason" + modid).selectedIndex].value;
//alert('jlamteam seasonid=' + seasonid);

leagueid = document.getElementById("jlamleague" + modid).options[document.getElementById("jlamleague" + modid).selectedIndex].value;
//alert('jlamteam leagueid=' + leagueid);

projectid = document.getElementById("jlamproject" + modid).options[document.getElementById("jlamproject" + modid).selectedIndex].value;
//alert('jlamteam projectid=' + projectid);

var element = document.getElementById("jlamdivisions" + modid);
if (element != null && element.value == '') 
{
divisionid = document.getElementById("jlamdivisions" + modid).options[document.getElementById("jlamdivisions" + modid).selectedIndex].value;
//alert('jlamteam divisionid=' + divisionid);
}


teamid = document.getElementById("jlamteams" + modid).options[document.getElementById("jlamteams" + modid).selectedIndex].value;
//alert('jlamteam teamid=' + teamid);

loadHtml = "<p id='loadingDiv-"
			+ modid
			+ "' style='margin-left: 10px; margin-top: -10px; margin-bottom: 10px;'>";
	loadHtml += "<img src='" + ajaxmenu_baseurl + 
				"modules/mod_joomleague_ajax_navigation_menu/img/loading.gif'>";
	loadHtml += "</p>";
	$('jlajaxmenu-' + modid).innerHTML += loadHtml;
    
var ajax = jlcnewAjax();
ajax.open("POST", location.href, true);
ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
ajax.send('jlamseason=' + seasonid + '&ajaxmodid=' + modid + '&jlamleague=' + leagueid + '&jlamproject=' + projectid + '&jlamdivisionid=' + divisionid + '&jlamteamid=' + teamid );
ajax.onreadystatechange = function() {

if (ajax.readyState == 4) {

var response = ajax.responseText;
			var start = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' start-->');
			var finish = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' end-->');

			justTheCalendar = response.substring(start, finish);
      $('jlajaxmenu-' + modid).innerHTML = justTheCalendar;

}

}    
    
}

function jlamdivision(modid)
{
//alert('jlamdivision_modid=' + modid);

var seasonid = 0;
var leagueid = 0;
var projectid = 0;
var divisionid = 0;
//var teamid = 0;


seasonid = document.getElementById("jlamseason" + modid).options[document.getElementById("jlamseason" + modid).selectedIndex].value;
//alert('jlamdivision seasonid=' + seasonid);

leagueid = document.getElementById("jlamleague" + modid).options[document.getElementById("jlamleague" + modid).selectedIndex].value;
//alert('jlamdivision leagueid=' + leagueid);

projectid = document.getElementById("jlamproject" + modid).options[document.getElementById("jlamproject" + modid).selectedIndex].value;
//alert('jlamdivision projectid=' + projectid);

divisionid = document.getElementById("jlamdivisions" + modid).options[document.getElementById("jlamdivisions" + modid).selectedIndex].value;
//alert('jlamdivision divisionid=' + divisionid);

teamid = document.getElementById("jlamteams" + modid).options[document.getElementById("jlamteams" + modid).selectedIndex].value;
//alert('jlamdivision teamid=' + teamid);


loadHtml = "<p id='loadingDiv-"
			+ modid
			+ "' style='margin-left: 10px; margin-top: -10px; margin-bottom: 10px;'>";
	loadHtml += "<img src='" + ajaxmenu_baseurl + 
				"modules/mod_joomleague_ajax_navigation_menu/img/loading.gif'>";
	loadHtml += "</p>";
	$('jlajaxmenu-' + modid).innerHTML += loadHtml;
    
var ajax = jlcnewAjax();
ajax.open("POST", location.href, true);
ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
ajax.send('jlamseason=' + seasonid + '&ajaxmodid=' + modid + '&jlamleague=' + leagueid + '&jlamproject=' + projectid + '&jlamdivisionid=' + divisionid + '&jlamteamid=' + teamid  );
ajax.onreadystatechange = function() {

if (ajax.readyState == 4) {

var response = ajax.responseText;
			var start = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' start-->');
			var finish = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' end-->');

			justTheCalendar = response.substring(start, finish);
      $('jlajaxmenu-' + modid).innerHTML = justTheCalendar;

}

}    
    
}


function jlamnewdivisions(modid)
{
//alert(modid);
var seasonid = 0;
var leagueid = 0;
var projectid = 0;
seasonid = document.getElementById("jlamseason" + modid).options[document.getElementById("jlamseason" + modid).selectedIndex].value;
leagueid = document.getElementById("jlamleague" + modid).options[document.getElementById("jlamleague" + modid).selectedIndex].value;
projectid = document.getElementById("jlamproject" + modid).options[document.getElementById("jlamproject" + modid).selectedIndex].value;

//alert(seasonid);

loadHtml = "<p id='loadingDiv-"
			+ modid
			+ "' style='margin-left: 10px; margin-top: -10px; margin-bottom: 10px;'>";
	loadHtml += "<img src='" + ajaxmenu_baseurl + 
				"modules/mod_joomleague_ajax_navigation_menu/img/loading.gif'>";
	loadHtml += "</p>";
	$('jlajaxmenu-' + modid).innerHTML += loadHtml;
    
var ajax = jlcnewAjax();
ajax.open("POST", location.href, true);
ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
ajax.send('jlamseason=' + seasonid + '&ajaxmodid=' + modid + '&jlamleague=' + leagueid + '&jlamproject=' + projectid );
ajax.onreadystatechange = function() {

if (ajax.readyState == 4) {

var response = ajax.responseText;
			var start = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' start-->');
			var finish = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' end-->');

			justTheCalendar = response.substring(start, finish);
      $('jlajaxmenu-' + modid).innerHTML = justTheCalendar;

}

}    
    
}

    
function jlamnewprojects(modid)
{
//alert(modid);
var seasonid = 0;
var leagueid = 0;
seasonid = document.getElementById("jlamseason" + modid).options[document.getElementById("jlamseason" + modid).selectedIndex].value;
leagueid = document.getElementById("jlamleague" + modid).options[document.getElementById("jlamleague" + modid).selectedIndex].value;
//alert(seasonid);

loadHtml = "<p id='loadingDiv-"
			+ modid
			+ "' style='margin-left: 10px; margin-top: -10px; margin-bottom: 10px;'>";
	loadHtml += "<img src='" + ajaxmenu_baseurl + 
				"modules/mod_joomleague_ajax_navigation_menu/img/loading.gif'>";
	loadHtml += "</p>";
	$('jlajaxmenu-' + modid).innerHTML += loadHtml;
    
var ajax = jlcnewAjax();
ajax.open("POST", location.href, true);
ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
ajax.send('jlamseason=' + seasonid + '&ajaxmodid=' + modid + '&jlamleague=' + leagueid);
ajax.onreadystatechange = function() {

if (ajax.readyState == 4) {

var response = ajax.responseText;
			var start = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' start-->');
			var finish = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' end-->');

			justTheCalendar = response.substring(start, finish);
      $('jlajaxmenu-' + modid).innerHTML = justTheCalendar;

}

}    
    
}


function jlamnewleagues(modid)
{
//alert('jlamnewleagues_modid=' + modid);

var seasonid = 0;
seasonid = document.getElementById("jlamseason" + modid).options[document.getElementById("jlamseason" + modid).selectedIndex].value;

//alert('jlamnewleagues_season_id=' + seasonid);

loadHtml = "<p id='loadingDiv-"
			+ modid
			+ "' style='margin-left: 10px; margin-top: -10px; margin-bottom: 10px;'>";
	loadHtml += "<img src='" + ajaxmenu_baseurl + 
				"modules/mod_joomleague_ajax_navigation_menu/img/loading.gif'>";
	loadHtml += "</p>";
	$('jlajaxmenu-' + modid).innerHTML += loadHtml;
    
var ajax = jlcnewAjax();
ajax.open("POST", location.href, true);
ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
ajax.send('jlamseason=' + seasonid + '&ajaxmodid=' + modid);
ajax.onreadystatechange = function() {

if (ajax.readyState == 4) {

var response = ajax.responseText;
			var start = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' start-->');
			var finish = response.indexOf('<!--jlajaxmenu-' + modid
					+ ' end-->');

			justTheCalendar = response.substring(start, finish);
      $('jlajaxmenu-' + modid).innerHTML = justTheCalendar;

}

}


}