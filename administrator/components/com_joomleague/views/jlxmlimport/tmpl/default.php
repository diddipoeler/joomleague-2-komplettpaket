<?php defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

$model = $this->getModel('jlxmlimport');
echo $model->getXml;
?>