<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<table class="contentpaneopen">
	<tr>
		<td class="contentheading">
		<?php
		if ( $this->config['show_team_shortform'] == 1 && !empty($this->team->short_name))
		{
			echo '&nbsp;' . JText::sprintf( 'COM_JOOMLEAGUE_ROSTER_TITLE2', $this->team->name, $this->team->short_name );
		}
		else
		{
			echo '&nbsp;' . JText::sprintf( 'COM_JOOMLEAGUE_ROSTER_TITLE', $this->team->name );
		}
		?>
		</td>
        <td>
        <?php
        echo $this->lists['type'];
        
        ?>
        </td>
	</tr>
</table>
<br />
