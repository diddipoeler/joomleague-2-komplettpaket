<?php
/**
* @copyright	Copyright (C) 2007-2013 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

class JFormFieldClub extends JFormField
{

	protected $type = 'club';
	
	function getInput() {
		$db = &JFactory::getDBO();
		$lang = JFactory::getLanguage();
		$extension = "com_joomleague";
		$source = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		$query = 'SELECT c.id, c.name FROM #__joomleague_club c ORDER BY name';
		$db->setQuery( $query );
		$clubs = $db->loadObjectList();
		$mitems = array(JHtml::_('select.option', '', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT')));

		foreach ( $clubs as $club ) {
			$mitems[] = JHtml::_('select.option',  $club->id, '&nbsp;'.$club->name. ' ('.$club->id.')' );
		}
		
		$output= JHtml::_('select.genericlist',  $mitems, $this->name, 'class="inputbox" size="1"', 'value', 'text', $this->value, $this->id );
		return $output;
	}
}
 