<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<table width='100%' class='contentpaneopen'>
	<tr>
		<td class='contentheading'>
			<?php
			$pageTitle = JText::_( 'COM_JOOMLEAGUE_CLUBS_PAGE_TITLE' );
			if ( isset( $this->project ) )
			{
				$pageTitle .= ' - ' . $this->project->name;
				if ( isset( $this->division ) )
				{
					$pageTitle .= ' : ' . $this->division->name;
				}
			}

			echo $pageTitle;
			?>
		</td>
	</tr>
</table>
<br />