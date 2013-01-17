<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for the import screen
 *
 * @package		Joomla
 * @subpackage	JoomLeague
 * @since		1.5
 */
class JoomleagueViewImport extends JLGView
{

	function display($tpl = null)
	{
		$table = JRequest::getVar('table');
		//initialise variables
		$document	= JFactory::getDocument();
		$user 		= JFactory::getUser();

		//build toolbar
		#JToolBarHelper::title(JText::_('IMPORT'), 'home');
		JToolBarHelper::title(JText::_('JoomLeague CSV-Import - Step 1 of 2'), 'generic.png');
		JToolBarHelper::back();
		JToolBarHelper::help('joomleague.import',true);

		// Get data from the model
		$model = $this->getModel("import");
		$tablefields = & $model->getTablefields('#__joomleague_' . $table);

		//assign vars to the template
		$this->assignRef('tablefields',	$tablefields);
		$this->assignRef('table',		$table);
		parent::display($tpl);
	}

}
?>