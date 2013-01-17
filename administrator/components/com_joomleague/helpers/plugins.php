<?php
//load com_joomleague_sport_types.ini
$extension 	= "com_joomleague_sport_types";
$lang 		= JFactory::getLanguage();
$source 	= JPATH_ADMINISTRATOR . '/components/' . $extension;
$lang->load("$extension", JPATH_ADMINISTRATOR, null, false, false)
||	$lang->load($extension, $source, null, false, false)
||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
||	$lang->load($extension, $source, $lang->getDefault(), false, false);


JPluginHelper::importPlugin('extension', 'joomleague_esport');
