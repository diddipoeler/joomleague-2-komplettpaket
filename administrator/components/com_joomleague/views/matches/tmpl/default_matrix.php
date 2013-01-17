<?php defined('_JEXEC') or die('Restricted access');
?>
<div class="clr"></div>
<form method="post" name="matrixForm" id="matrixForm">

<fieldset class="adminform"><legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MATRIX_TITLE'); ?></legend>
<fieldset class="adminform">
	<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MATRIX_HINT'); ?>
</fieldset>
<?php

$model = $this->getModel();
$teams= $model->getProjectTeams();
$matrix ='';

if (count($teams) <= 20) {
	$matrix = "<table width=\"100%\" class=\"adminlist\">";

	$k = 0;
	for($rows = 0; $rows <= count($teams); $rows++){
		if($rows == 0) $trow = $teams[0];
		else $trow = $teams[$rows-1];
		$matrix .= "<tr class=\"row$k\">";
		for($cols = 0; $cols <= count($teams); $cols++){
			$text = '';
			$checked = '';
			$color = 'white';
			if( $cols == 0 ) $tcol = $teams[0];
			else $tcol = $teams[$cols-1];
			$match = $trow->value.'_'.$tcol->value;
			$onClick = sprintf("onClick=\"javascript:SaveMatch('%s','%s');\"", $trow->value, $tcol->value);
			if($rows == 0 && $cols == 0) $text = "<th align=\"center\"></th>";
			else if($rows == 0) $text = sprintf("<th width=\"200\" align=\"center\" title=\"%s\">%s</th>",$tcol->text,$tcol->short_name); //picture columns
			else if($cols == 0) $text = sprintf("<td align=\"left\" nowrap>%s</td>",$trow->text); // named rows
			else if($rows == $cols) $text = "<td align=\"center\"><input type=\"radio\" DISABLED></td>"; //impossible matches
			else{
				if(count($this->matches) >0) {
					for ($i=0,$n=count($this->matches); $i < $n; $i++)
					{
						$row =& $this->matches[$i];
						if($row->projectteam1_id == $trow->value 
							&& $row->projectteam2_id == $tcol->value
						){
							$checked = 'checked';
							$color = 'teal';
							$onClick = '';
							break;
						} else {
							$checked = '';
							$color = 'white';
							$onClick = sprintf("onClick=\"javascript:SaveMatch('%s','%s');\"", $trow->value, $tcol->value);
						}
					}
				}	
				$text = sprintf("<td align=\"center\" title=\"%s - %s\" bgcolor=\"%s\"><input type=\"radio\" name=\"match_%s\" %s %s></td>\n",$trow->text,$tcol->text,$color,$trow->value.$tcol->value, $onClick, $checked);
			}
			$matrix .= $text;
		}
		$k = 1 - $k;
	}
	$matrix .= "</table>";
}
//show the matrix
echo $matrix;
?></fieldset>
<?php $dValue=$this->roundws->round_date_first.' '.$this->projectws->start_time; ?>
<input type='hidden' name='match_date' value='<?php echo $dValue; ?>' />
<input type='hidden' name='projectteam1_id' value='' />
<input type='hidden' name='projectteam2_id' value='' />
<input type='hidden' name='published' value='1' />
<input type='hidden' name='task' value='match.addmatch' />
<?php echo JHTML::_('form.token')."\n"; ?>
</form>
