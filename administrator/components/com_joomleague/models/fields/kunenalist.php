<?php
/**
 * @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');

//require_once( KPATH_ADMIN . DS . 'models' . DS . 'categories.php' );
require_once( KPATH_ADMIN . DS .'libraries' . DS .'forum' . DS . 'category' . DS . 'helper.php' );

/**
 * Session form field class
 */
class JFormFieldKunenalist extends JFormFieldList
{
	/**
	 * field type
	 * @var string
	 */
	public $type = 'Kunenalist';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		$mdlKunena = JModel::getInstance("KunenaAdminModelCategories", "KunenaModel");
        $Categories = $mdlKunena->getAdminCategories();
echo 'getAdminCategories<br /><pre>~' . print_r($Categories,true) . '~</pre><br />';
		return $options;
	}
}
