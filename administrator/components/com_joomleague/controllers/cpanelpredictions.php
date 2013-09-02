<?php
/**
 * @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

/**
 * Joomleague Component cpanelpredictions Controller
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllercpanelpredictions extends JoomleagueController
{
	protected $view_list = 'cpanelpredictions';
    function __construct()
	{
		parent::__construct();


	}
    
    function display()
	{
	
		parent::display();
	}

}

?>    