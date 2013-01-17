<?php
/**
* @version		$Id$
* @package		JoomLeague 1.5
* @copyright	Copyright (C) JoomLeague. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.parameter');

/**
 * override JParameters to output the params values in frontend
 * @author julien
 *
 */
abstract class JLGExtraParams extends JFormField {
	
	public function getElements($group = '_default')
	{
		if (!isset($this->element[$group])) {
			return false;
		}
		$results = array();
		foreach ($this->element[$group]->children() as $param)  {
			$results[] = $this->getParamObject($param);
		}
		return $results;
	}	

	/**
	 * Render a parameter type
	 *
	 * @param	object	A param tag node
	 * @param	string	The control name
	 * @return	array	Any array of the label, the form element and the tooltip
	 * @since	1.5
	 */
	protected function getParamObject(&$node, $group = '_default')
	{
		$obj = new JLGExtraParamElement();
		$obj->backendonly = $node->element['backendonly'];
		$obj->name        = $node->element['name'];
		$obj->type        = $node->element['type'];
		$obj->label       = JText::_($node->element['label']);
		$obj->description = JText::_($node->element['description']);
		$cssclass = $node->element['cssclass'];
		$cssclass = !empty($cssclass) ? 'class="'. $node->element['cssclass'] .'"' : "";
		$value = $this->get($node->element['name'], $node->element['default'], $group);
		if(!empty($value)) {
			if($obj->type == 'link') {
				$obj->value = '<a '.$cssclass.'
								href="'.$this->get($node->element['name'], 
												$node->element['default'], $group).
									'">'.$this->get($node->element['name'], 
												$node->element['default'], $group).'</a>';
			} else { 
				$obj->value = $this->get($node->element['name'], $node->element['default'], $group);
			} 
		}
		return $obj;
	}
}

class JLGExtraParamElement {
	var $name;
	var $label;
	var $description;
	var $type;
	var $value;
	var $backendonly;
	var $cssclass;
}
