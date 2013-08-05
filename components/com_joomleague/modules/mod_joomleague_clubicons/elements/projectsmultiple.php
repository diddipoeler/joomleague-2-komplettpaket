<?php
/**
* Module mod_jl_clubicons For Joomla 1.5 and joomleague 1.5b.2
* Version: 1.5b.2
* Created by: johncage
* Created on: 21 June 2011
* 
* URL: www.yourlife.de
* License http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class JElementProjectsmultiple extends JElement
{

  var $_name = 'projectsmultiple';

  function fetchElement($name, $value, &$node, $control_name){
    $db = &JFactory::getDBO();
    $query = 'SELECT p.id, p.name FROM #__joomleague_project p WHERE published=1 ORDER BY id DESC';
    $db->setQuery( $query );
    $projects = $db->loadObjectList();
    $mitems = array(JHTML::_('select.option', '', '- '.JText::_('Do not use').' -'));

    foreach ( $projects as $project ) {
      $mitems[] = JHTML::_('select.option',  $project->id, '&nbsp;&nbsp;&nbsp;'.$project->name );
    }
    

    $output= JHTML::_('select.genericlist',  $mitems, ''.$control_name.'['.$name.'][]', 'class="inputbox" style="width:90%;" multiple="multiple" size="10"', 'value', 'text', $value );
    return $output;
  }
}
