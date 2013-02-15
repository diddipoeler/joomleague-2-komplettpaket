<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');

//JToolBarHelper::title(JText::_('JL_SIS_XML_INPORT_NEW_PLAYGROUND'));

$url = 'components/com_joomleague/extensions/jlextsisxmlimport/admin/assets/images/sislogo.png';
$alt = 'SIS Logo';
$attribs['width'] = '170px';
$attribs['height'] = '26px';
$logo = JHtml::_('image', $url, $alt, $attribs);
JToolBarHelper::title( JText::sprintf('JL_SIS_XML_INPORT_NEW_PLAYGROUND', $logo) );

JToolBarHelper::apply();

/*
echo '<pre>';
print_r($this->sisplayground);
echo '</pre>';
*/

/*
$sisplayground = JRequest::get('sisplayground');
echo '<pre>';
print_r($sisplayground);
echo '</pre>';
*/

?>


<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm">


<table class="adminlist">
<thead>
<tr>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_PLAYGROUND_NUMBER' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_PLAYGROUND_NAME' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_PLAYGROUND_STREET' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_PLAYGROUND_ZIPCODE' ); ?>
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_PLAYGROUND_CITY' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_PLAYGROUND_ASSIGN' ); ?>
</th>


</tr>

<tr>
<th width="20" style="vertical-align: top; ">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->sisplayground ); ?>);" />
					</th>
</tr>

</thead>

<?PHP

//$lfdnummer = 1;
$k = 0;
$i = 0;

foreach ( $this->sisplayground as $row )
{
$checked = JHTML::_( 'grid.id', 'oldteamid'.$i, $row->Halle, $checkedOut[$i], 'oldteamid' );
$append=' style="background-color:#bbffff"';
$inputappend	= '';
$selectedvalue = 0;

?>
<tr class="<?php echo "row$k"; ?>">

<td style="text-align:center; ">
								<?php
								echo $i;
								?>
							</td>
            <td style="text-align:center;">
								<?php
								echo $checked;
								?>
						</td>
						<td style="text-align:center;">
								<?php
								echo $row->Halle;
								?>
						</td>
						<td style="text-align:center;">
								<?php
								echo $row->HallenName;
								?>
								<input type="hidden" name="hallenname[<?php echo $row->Halle;?>]"	value="<?php echo $row->HallenName;?>" />
						</td>
						<td style="text-align:center;">
								<?php
								echo $row->HallenStrasse;
								?>
								<input type="hidden" name="hallenstrasse[<?php echo $row->Halle;?>]"	value="<?php echo $row->HallenStrasse;?>" />
						</td>
						<td style="text-align:center;">
								<?php
								echo $row->zipcode;
								?>
								<input type="hidden" name="hallenplz[<?php echo $row->Halle;?>]"	value="<?php echo $row->zipcode;?>" />
						</td>
						<td style="text-align:center;">
								<?php
								echo $row->city;
								?>
								<input type="hidden" name="hallenort[<?php echo $row->Halle;?>]"	value="<?php echo $row->city;?>" />
						</td>
						
						<td nowrap="nowrap" style="text-align:center; ">
						<?php
						echo JHTML::_( 'select.genericlist', $this->lists['playgrounds'], 'newteamid[' . $row->Halle . ']', $inputappend . 'class="inputbox" size="1" onchange="document.getElementById(\'cboldteamid' . $i . '\').checked=true"' . $append, 'value', 'text', $selectedvalue );
						?>
						
						</td>
						
</tr>						
<?PHP

$i++;

}


?>

</table>

<input type="hidden" name="sent"			value="1" />
<input type="hidden" name="controller"		value="jlextsisxmlimport" />
<input type="hidden" name="task"			value="" />
                			
</form>

