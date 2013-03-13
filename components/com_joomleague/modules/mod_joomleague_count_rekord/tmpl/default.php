<?php

/**
 * @version	 $Id$
 * @package	 Joomla
 * @subpackage  Joomleague ranking module
 * @copyright   Copyright (C) 2008 Open Source Matters. All rights reserved.
 * @license	 GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

defined('_JEXEC') or die('Restricted access');

$list  = modJLGStatistikRekordHelper::getData($params);

/*
echo '<pre>';
print_r($list);
echo '</pre>';
*/

?>


<table width ="100%" class ="">

<?PHP

if ( $list )
{
foreach ( $list as $row )
{
?>
<tr>
<td width ="50%" align ="left" ><?PHP echo $row->text; ?></td>
</tr>
<?PHP

}

}

?>

</table>

