<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');

//JToolBarHelper::title( JText::_('JL_SIS_XML_INPORT_NEW_TEAMS') );
$url = 'components/com_joomleague/extensions/jlextsisxmlimport/admin/assets/images/sislogo.png';
$alt = 'SIS Logo';
$attribs['width'] = '170px';
$attribs['height'] = '26px';
$logo = JHtml::_('image', $url, $alt, $attribs);
JToolBarHelper::title( JText::sprintf('JL_SIS_XML_INPORT_NEW_TEAMS', $logo) );

JToolBarHelper::save();



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
<?PHP echo JText::_( 'JL_EXT_SIS_TEAM_NUMBER' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_TEAM_NAME' ); ?>
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_NEW_CLUB_NAME' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_TEAM_ASSIGN' ); ?>
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_EXT_SIS_CLUB_ASSIGN' ); ?>
</th>

</tr>

<tr>
<th width="20" style="vertical-align: top; ">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->sisteams ); ?>);" />
					</th>
</tr>

</thead>

<?PHP

//$lfdnummer = 1;
$k = 0;
$i = 0;

foreach ( $this->sisteams as $row )
{
$checked = JHTML::_( 'grid.id', 'oldteamid'.$i, $row->Teamnummer, $checkedOut[$i], 'oldteamid' );
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
								echo $row->Teamnummer;
								?>
						</td>
						<td style="text-align:center;">
								<?php
								echo $row->TeamName;
								?>
								<input type="hidden" name="teamname[<?php echo $row->Teamnummer;?>]"	value="<?php echo $row->TeamName;?>" />
						</td>
						
						<td style="text-align:center;">
						<input type="" size="50" name="newclubname[<?php echo $row->Teamnummer;?>]"	value="<?php echo $row->TeamName;?>" />
						</td>

						<td nowrap="nowrap" style="text-align:center; ">
						<?php
						echo JHTML::_( 'select.genericlist', $this->lists['teams'], 'newteamid[' . $row->Teamnummer . ']', $inputappend . 'class="inputbox" size="1" onchange="document.getElementById(\'cboldteamid' . $i . '\').checked=true"' . $append, 'value', 'text', $selectedvalue );
						?>
						</td>
						
						<td nowrap="nowrap" style="text-align:center; ">
						<?php
						echo JHTML::_( 'select.genericlist', $this->lists['clubs'], 'newclubid[' . $row->Teamnummer . ']', $inputappend . 'class="inputbox" size="1" onchange="document.getElementById(\'cboldteamid' . $i . '\').checked=true"' . $append, 'value', 'text', $selectedvalue );
						?>
						</td>
						
</tr>						
<?PHP

$i++;

}


?>

</table>
<input type="hidden" name="projectteamkind"			value="<?php echo $this->projectteamkind;?>" />
<input type="hidden" name="sent"			value="2" />
<input type="hidden" name="controller"		value="jlextsisxmlimport" />
<input type="hidden" name="task"			value="" />
                			
</form>

