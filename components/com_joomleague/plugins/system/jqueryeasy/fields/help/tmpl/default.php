<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die;
?>

<style>
	.help {}
	
	.help span {
		margin: 0 0 10px 0;
		display: block;
	}
	
	.help ul {
		margin: 10px 0 10px 10px !important;
	}
	
	.help ul li {
		list-style: square inside !important;
	}
</style>

<div class="help">
	<span>Although jQuery Easy can solve a certain number of jQuery issues (code conflicts, multiple jQuery libraries load...), some problems can still occur.</span>
	<span>These problems can come from different sources:</span>
	<ul>
		<li>some components installed are not compatible with the version of jQuery you set through the plugin,</li>
		<li>some jQuery code may need to be modified,</li>
		<li>...</li>
	</ul>
	<span>Note: using 'no conflict' script over script declaration ensures it is loaded just after the jQuery library</span>

	<span>More information can be found here:</span>
	<span><a href="http://docs.jquery.com/Using_jQuery_with_Other_Libraries" target="_blank">Using jQuery with other libraries</a><br />
	<a href="http://api.jquery.com/jQuery.noConflict/" target="_blank">jQuery no conflict API</a></span>
	
	<span>Check this article on <a href="http://www.simplifyyourweb.com/index.php/developers-corner/90-solving-jquery-jquery-and-jquery-mootools-conflict-issues-with-the-jquery-easy-plugin" target="_blank">solving jQuery/jQuery and jQuery/MooTools conflict issues with the jQuery Easy plugin</a>.</span>
	<span>A <a href="http://www.simplifyyourweb.com/index.php/developers-corner/110-solving-jquery-issues-with-jquery-easy-a-case-study" target="_blank">case study</a> is also available in order to help understanding the plugin's process.</span>
	
	<span>More answers in the <a href="http://www.simplifyyourweb.com/index.php/forum/19-jquery-easy" target="_blank">jQuery Easy forum</a>.</span>
	
	<span>DISABLE MOOTOOLS AT YOUR OWN RISK</span>
	<span>If you disable MooTools you may break some features (search filters functionality and frontend article submission for instance).</span>
	<span>If your Joomla! website does not use MooTools, you will benefit from disabling it:</span>
	<ul>
		<li>increase the page load speed by removing un-needed javascript code,</li>
		<li>eliminate conflicts between MooTools and jQuery.</li>
	</ul>
</div>