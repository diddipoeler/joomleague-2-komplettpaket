<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');


?><p><?php
echo JText::_($this->optiontext.'JL_PRED_ENTRY_DENY_INFO_01');
?></p><p><?php
echo JText::sprintf($this->optiontext.'JL_PRED_ENTRY_DENY_INFO_02','<a href="index.php?option=com_users&view=login"><b><i>','</i></b></a>'
					);
?></p><p><?php
echo JText::sprintf($this->optiontext.'JL_PRED_ENTRY_DENY_INFO_03','<a href="index.php?option=com_users&view=register"><b><i>','</i></b></a>');
?></p><br />
