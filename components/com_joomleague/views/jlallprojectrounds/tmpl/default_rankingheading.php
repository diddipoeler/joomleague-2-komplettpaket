<?php defined( '_JEXEC' ) or die( 'Restricted access' );

$type	=& $this->type;
$config	=& $this->tableconfig;

$columns		= explode( ',', $config['ordered_columns'] );
$column_names	= explode( ',', $config['ordered_columns_names'] );
//$colspanSingleTab = count( $columns );
$colspanHead = 4;
$colspanSingleTab = count($columns);
?>

<thead>
	<tr class="sectiontableheader">
		<th class="rankheader" colspan="3">
			<?php JoomleagueHTML::printColumnHeadingSort( JText::_( 'JL_RANKING_POSITION' ), "rank", $config, "ASC" ); ?>
		</th>
		
		<?php
		if ( $this->tableconfig['show_logo_small_table'] > 0 )
		{
			echo '<th align="center" style="text-align: center" width="50">&nbsp;</th>';
		}
		?>
		
		<th class="teamheader">	
		<?php JoomleagueHTML::printColumnHeadingSort( JText::_( 'JL_RANKING_TEAM' ), "name", $config, "ASC" ); ?>
		</th>
		
		<?php
if($type==3)
{
	$tabcols=3;
}
else
{
	$tabcols=1;
}

for ( $tabs = 0; $tabs < $tabcols; $tabs++ )
{
	foreach ( $columns as $k => $column )
	{
		if (empty($column_names[$k])){$column_names[$k]='???';}
		switch ( trim( strtoupper( $column ) ) )
		{
			case 'JL_PLAYED':
				$border = "";
				if( $type == 3 )
				{
					$border = 'style="border-left: 1px solid"';
				}
				echo '<th class="headers" ' . $border . '>';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "played", $config );
				echo '</th>';
				break;

			case 'JL_WINS':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "won", $config );
				echo '</th>';
				break;

			case 'JL_TIES':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "draw", $config );
				echo '</th>';
				break;

			case 'JL_LOSSES':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "loss", $config );
				echo '</th>';
				break;

			case 'JL_WINPCT':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "winpct", $config );
				echo '</th>';
				break;

			case 'JL_GB':
				echo '<th class="headers">';
				echo $column_names[$k];
				echo '</th>';
				break;

			case 'JL_LEGS':
				echo '<th class="headers">';
				echo $column_names[$k];
				echo '</th>';
				break;

			case 'JL_LEGS_DIFF':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "legsdiff", $config );
				echo '</th>';
				break;

			case 'JL_LEGS_RATIO':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "legsratio", $config );
				echo '</th>';
				break;				
				
			case 'JL_SCOREFOR':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "goalsfor", $config );
				echo '</th>';
				break;				
				
			case 'JL_SCOREAGAINST':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "goalsagainst", $config );
				echo '</th>';
				break;

			case 'JL_SCOREPCT':
				echo '<th class="headers">';
				echo $column_names[$k];
				echo '</th>';
				break;
				
			case 'JL_RESULTS':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "goalsp", $config );
				echo '</th>';
				break;

			case 'JL_DIFF':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "diff", $config );
				echo '</th>';
				break;

			case 'JL_POINTS':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "points", $config );
				echo '</th>';
				break;

			case 'JL_NEGPOINTS':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "negpoints", $config );
				echo '</th>';
				break;

			case 'JL_OLDNEGPOINTS':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "negpoints", $config );
				echo '</th>';
				break;
				
			case 'JL_POINTS_RATIO':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "pointsratio", $config );
				echo '</th>';
				break;				

			case 'JL_BONUS':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "bonus", $config );
				echo '</th>';
				break;

			case 'JL_START':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "start", $config );
				echo '</th>';
				break;

			case 'JL_QUOT':
				echo '<th class="headers">';
				JoomleagueHTML::printColumnHeadingSort( $column_names[$k], "quot", $config );
				echo '</th>';
				break;

			case 'JL_TADMIN':
				echo '<th class="headers">';
				echo $column_names[$k];
				echo '</th>';
				break;
				
			default:
				echo '<th class="headers">';
				echo JText::_($column);
				echo '</th>';
				break;
		}
	}
}
?>
	</tr>
</thead>