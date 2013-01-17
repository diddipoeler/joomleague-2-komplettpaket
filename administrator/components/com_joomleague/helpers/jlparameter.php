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

class JLParameter extends JParameter
{
	/**
	 * name of the configuration file
	 *
	 * @var string
	 */
	var $name = '';
	/**
	 * description of the configuration file
	 *
	 * @var string
	 */
	var $description = '';

	/**
	 * Loads an xml setup file and parses it
	 *
	 * @access	public
	 * @param string	path to xml setup file
	 * @return	object
	 * @since 1.5
	 */
	public function loadSetupFile( $path )
	{
		$result = false;

		if ( $path )
		{
			$xml = JFactory::getXMLParser( 'Simple' );

			if ( $xml->loadFile( $path ) )
			{
				if ( $params =& $xml->document->params )
				{
					foreach ( $params as $param )
					{
						$this->setXML( $param );
						$result = true;
					}
				}

				if ( $name =& $xml->document->name )
				{
					$this->name = JText::_( $name[0]->_data );
				}

				if ( $description =& $xml->document->description )
				{
					$this->description = JText::_( $description[0]->_data );
				}
			}
		}
		else
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * get name
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * get description
	 *
	 * @return string
	 */
	function getDescription()
	{
		return $this->description;
	}

}
?>