<?php

/**
 * @version	 $Id$
 * @package	 Joomla
 * @subpackage  Joomleague logo module
 * @copyright   Copyright (C) 2008 Open Source Matters. All rights reserved.
 * @license	 GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

defined('_JEXEC') or die('Restricted access');

// check if any results returned
$items = count($list['teams']);
if (!$items) {
	echo '<p class="modjlglogo">' . JText::_('MOD_JOOMLEAGUE_LOGO_NO_ITEMS') . '</p>';
	return;
}
$nametype = $params->get('nametype');
$typel = $params->get('show_logo', 0);
?>

<!--[if IE 7]>
<style type="text/css">
#modjlglogo li.logo0, li.logo1, li.logo2 {
    display: inline;
}
</style>
<![endif]-->

<script type="text/javascript">
	window.addEvent('domready', function(){
	var Tips1 = new Tips($$('.logo'));
}); 
</script>



<div class="modjlglogo"><?php if ($params->get('show_project_name', 0)):?>
<p class="projectname"><?php echo $list['project']->name; ?></p>
<?php endif; ?>

<ul id="modjlglogo">
<?php foreach (array_slice($list['teams'], 0, $params->get('limit', 12)) as $item) :  ?>
	<li class="logo<?php echo $typel; ?>">
	<?php $link = JHTML::link(modJLGLogoHelper::getTeamLink($item, 
															$params, 
															$list['project']), 
															isset($item->team->$nametype));
	$link1 = explode(">", $link);
	echo $link1[0].'>';
	echo modJLGLogoHelper::getLogo($item, $typel)."</a></li>"; ?>
	<?php endforeach; ?>
</ul>
</div>
