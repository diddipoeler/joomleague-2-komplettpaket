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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Joomleague Component import Controller
 *
 * @package		Joomla
 * @subpackage	JoomLeague
 * @since		1.5
 */
class JoomleagueControllerImport extends JoomleagueController
{
	/**
	 * Constructor
	 *
	 *@since 0.9
	 */
	function __construct()
	{
		parent :: __construct();

		// Register Extra tasks
		$this->registerTask('csvpersonimport',			'dispatch');
		$this->registerTask('csvseasonimport',			'dispatch');
		$this->registerTask('csvleagueimport',			'dispatch');
		$this->registerTask('csvprojectimport',			'dispatch');
		$this->registerTask('csvclubimport',			'dispatch');
		$this->registerTask('csvteamimport',			'dispatch');
		$this->registerTask('csvsports_typeimport',		'dispatch');
		$this->registerTask('csveventtypeimport',		'dispatch');
		$this->registerTask('csvpositionimport',		'dispatch');
		$this->registerTask('csvplaygroundimport',		'dispatch');
	}

	function dispatch()
	{
		switch($this->getTask())
		{
			case 'csvseasonimport'	:
			{
				$table = "Season";
				$view  = "seasons";
			} break;
			case 'csvpersonimport'	:
			{
				$table = "Person";
				$view  = "persons";
			} break;
			case 'csvleagueimport'	:
			{
				$table = "League";
				$view  = "leagues";
			} break;
			case 'csvprojectimport'	:
			{
				$table = "Project";
				$view  = "projects";
			} break;
			case 'csvclubimport'	:
			{
				$table = "Club";
				$view  = "clubs";
			} break;
			case 'csvteamimport'	:
			{
				$table = "Team";
				$view  = "teams";
			} break;
			case 'csvsports_typeimport'	:
			{
				$table = "SportsType";
				$view  = "sportstypes";
			} break;
			case 'csveventtypeimport'	:
			{
				$table = "Eventtype";
				$view  = "eventtypes";
			} break;
			case 'csvpositionimport'	:
			{
				$table = "Position";
				$view  = "positions";
			} break;
			case 'csvplaygroundimport'	:
			{
				$table = "Playground";
				$view  = "playgrounds";
			} break;

			default:
				$msg = JText :: _('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_NOT_EXIST');
				$this->setRedirect('index.php?option=com_joomleague&view=projects',$msg,'error');
				return;

		}
		$this->import($table,$view);
	}

	function import($table,$view)
	{
		JToolBarHelper::back('Back','index.php?option=com_joomleague&view='.$view);

		$msg = array();
		$replace = JRequest :: getVar('replace',0,'post','int');
		$delimiter = JRequest :: getVar('csvdelimiter',",",'post');
		$object =& JTable :: getInstance($table, 'Table');
		$object_fields = get_object_vars($object);
		$filename = '';
		$csvimport = false;

		$file = JRequest::getVar('FileCSV',null,'files','array');
		if (isset($file['tmp_name']) && trim($file['tmp_name']) != '')
		{
			$filename = $file['tmp_name'];
			$csvimport = true;
		}

		if ($csvimport)
		{
			$handle = fopen($filename,'r');

			if (!$handle)
			{
				$msg = JText :: _('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_CANNOT_OPEN');
				$this->setRedirect('index.php?option=com_joomleague&view='.$view,$msg,'error');
				return;
			}

			// get fields, on first row of the file
			$fields = array ();
			if (($data = fgetcsv($handle,1000,$delimiter,'"')) !== FALSE)
			{
				$numfields = count($data);
				for ($c = 0; $c < $numfields; $c++)
				{
					// here, we make sure that the field match one of the fields of table or special fields,
					// otherwise, we don't add it
					if (array_key_exists(trim($data[$c]),$object_fields))
					{
						$fields[$c] = trim($data[$c]);
					}
				}
			}

			// If there is no validated fields, there is a problem...
			if (!count($fields))
			{
				$msg = JText :: _('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_ERROR_PARSING');
				$this->setRedirect('index.php?option=com_joomleague&view='.$view,$msg,'error');
				return;
			}
			else
			{
				$msg[] = $numfields." fields found in first row";
				$msg[] = count($fields)." fields were kept";
			}

			// Now get the records, meaning the rest of the rows.
			$records = array ();
			$row = 1;
			while (($data = fgetcsv($handle,10000,$delimiter,'"')) !== FALSE)
			{
				$num = count($data);
				if ($numfields != $num)
				{
					$msg[] = JText :: _('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_WRONG_NUMBER_OF_FIELDS');
				}
				else
				{
					$r = array ();
					// only extract columns with validated header, from previous step.
					foreach ($fields as $k => $v)
					{
						$r[$k] = $this->_formatcsvfield($v, $data[$k]);
					}
					$records[] = $r;
				}
				$row++;
			}
			fclose($handle);

			$msg[] = JText :: _('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_TOTAL_RECORDS_FOUND') . count($records);

			// database update
			if (count($records))
			{
				$model = $this->getModel('import');
				$result = $model->import($fields,$records,$replace,$table);
				$msg[]= $result['errormsg'];
				$msg[] = JText :: _('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_TOTAL_ADDED_RECORDS') . ' ' . $result['added'];
				$msg[] = JText :: _('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_TOTAL_UPDATED_RECORDS').' ' . $result['updated'];
				$msg[] = JText :: _('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_TOTAL_EXISTS_RECORDS'). ' ' . $result['exists'];
			}
			$this->setRedirect('index.php?option=com_joomleague&view='.$view, implode('<p>', $msg));
		}
	}

	/**
	 * handle specific fields conversion if needed
	 *
	 * @param string column name
	 * @param string $value
	 * @return string
	 */
	function _formatcsvfield($type,$value)
	{
		switch ($type)
		{
			// here we should check some consistency...
			case 'birthday' :
				if ($value != '')
				{
					//strtotime does a good job in converting various date formats...
					$date = strtotime($value);
					$field = strftime('%Y-%m-%d',$date);
				}
				else
				{
					$field = null;
				}
				break;
			default :
				$field = $value;
				break;
		}
		return $field;
	}

}
?>