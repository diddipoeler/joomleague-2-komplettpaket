<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die();

jimport( 'joomla.application.component.view' );

/**
 * AJAX View class for the Joomleague component
 *
 * @static
 * @package	Joomleague
 * @since	1.5
 */
class JoomleagueViewProjectteams extends JLGView
{
	/**
	* view AJAX display method
	* @return void
	**/
	function display( $tpl = null )
	{
		// Get some data from the model
		$db	= JFactory::getDBO();
		$db->setQuery(	"	SELECT CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(':', t.id, t.alias) ELSE t.id END AS value,
									t.name AS text
							FROM #__joomleague_project_team tt
							JOIN #__joomleague_team t ON t.id = tt.team_id
							JOIN #__joomleague_project p ON p.id = tt.project_id
							WHERE tt.project_id = " . JRequest::getInt( 'p' ) . "
							ORDER BY t.name" );

		echo '[';
		foreach ((array)$db->loadObjectList() as $option)
		{
			echo "{ value: '$option->value', text: '$option->text'},";
		}
		echo ']';
	}

}
?>