<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgContentJoomleague_Person extends JPlugin
{

	public function plgContentJoomleague_Person(&$subject, $params)
	{
		parent::__construct($subject, $params);
		// load language file for frontend
		JPlugin::loadLanguage( 'plg_content_joomleague_person', JPATH_ADMINISTRATOR );
	}

	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		$db = JFactory::getDBO();

		if ( JString::strpos( $row->text, 'jl_player' ) === false )
		{
			return true;
		}
			
		$regex = "#{jl_player}(.*?){/jl_player}#s";

		if (preg_match_all( $regex, $row->text, $matches ) > 0 )
		{
			foreach ($matches[0] as $match)
			{
				$name = preg_replace("/{.+?}/", "", $match);

				$aname = explode(" ", html_entity_decode($name) );
				$firstname = $aname[0];
				$lastname = $aname[1];

				//build query to select player id
				$query = "SELECT p.id
							FROM #__joomleague_person p
							WHERE p.firstname = '$firstname' AND
							p.lastname = '$lastname' AND
							p.published = '1'
							ORDER BY p.id DESC";

				// run query
				$db->setQuery($query);
				$rows = $db->loadObjectList();

				// get result
				// replace only if project id set
				if (isset($rows[0]->id))
				{
					$pid = $rows[0]->id;
					$url = 'index.php?option=com_joomleague&view=person&pid='.$pid;
					$link = '<a href = "' . JRoute::_( $url ). '">';
					$row->text = preg_replace("#{jl_player}" . $name . "{/jl_player}#s", $link . $name . "</a>", $row->text);
				}
				else
				{
					$row->text = preg_replace("#{jl_player}" . $name . "{/jl_player}#s", $name, $row->text);
				}
			}
			return true;
		}
	}
}
?>