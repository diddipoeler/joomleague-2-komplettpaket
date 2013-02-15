<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

$url = 'components/com_joomleague/extensions/jlextsisxmlimport/admin/assets/images/sislogo.png';
$alt = 'SIS Logo';
$attribs['width'] = '170px';
$attribs['height'] = '26px';
$logo = JHtml::_('image', $url, $alt, $attribs);
JToolBarHelper::title( JText::sprintf('JL_SIS_XML_INPORT_READ_FILE', $logo) );

//JToolBarHelper::title( JText::_( 'JL_SIS_XML_INPORT_READ_FILE' ), $url );
//JToolBarHelper::title( JText::_( 'JL_SIS_XML_INPORT_READ_FILE' ), 'sislogo.png' );
//JToolBarHelper::title( JText::_( 'JL_SIS_XML_INPORT_READ_FILE' ), 'components/com_joomleague/extensions/jlextsisxmlimport/admin/assets/images/sislogo.png' );

//echo 'der link -> '.$this->sislink.'<br>';

/*
echo '<pre>';
print_r($this->extendedfiles);
echo '</pre>';
*/


echo '<pre>';
print_r($this->spielplan);
echo '</pre>';

/*
foreach ($this->spielplan as $row)
   {
   echo $row->nummer . '<br>';
   }
*/

?>

	<div id="editcell">

<fieldset class="adminform">
<legend>
				<?php
				echo JText::sprintf( 'JL_SIS_XML_INPORT_PROJECT_NAME [%s]', '<i>' . $this->name . '</i>' );
				?>
			</legend>

<legend>
				<?php
				echo JText::sprintf( 'JL_SIS_XML_INPORT_PROJECT_ID [%s]', '<i>' . $this->projektid . '</i>' );
				?>
			</legend>
			
<legend>
				<?php
				echo JText::sprintf( 'JL_SIS_XML_INPORT_PROJECT_LINK [%s]', '<i>' . $this->sislink . '</i>' );
				?>
			</legend>			

</fieldset>	
	
</div>



<table class="adminlist">
<thead>
<tr>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_ROUND_NAME' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_MATCH_NUMBER' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_MATCH_DATE' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_MATCH_TIME' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_PLAYGROUND_NAME' ); ?>
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_PLAYGROUND_NUMBER' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_HOME_TEAM_NAME' ); ?>
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_HOME_TEAM_NUMBER' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_AWAY_TEAM_NAME' ); ?>
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_AWAY_TEAM_NUMBER' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_HOME_TEAM_GOALS' ); ?>
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top; ">
<?PHP echo JText::_( 'JL_SIS_XML_INPORT_AWAY_TEAM_GOALS' ); ?>
</th>

</tr>
</thead>

<?PHP

foreach ($this->spielplan as $row)
   {
?>   
<tr>

<td>
<?PHP
echo $row->round_name;
?>
</td>

<td>
<?PHP
echo $row->match_number;
?>
</td>

<td>
<?PHP
echo $row->Datum;
?>
</td>

<td>
<?PHP
echo $row->vonUhrzeit;
?>
</td>

<td>
<?PHP
echo $row->HallenName;
?>
</td>
<td>
<?PHP
echo $row->Halle;
?>
</td>

<td>
<?PHP
echo $row->Heim;
?>
</td>
<td>
<?PHP
echo $row->HeimNr;
?>
</td>

<td>
<?PHP
echo $row->Gast;
?>
</td>
<td>
<?PHP
echo $row->GastNr;
?>
</td>

<td>
<?PHP
echo $row->Tore1;
?>
</td>
<td>
<?PHP
echo $row->Tore2;
?>
</td>

</tr>

<?PHP   
   }

?>

</table>