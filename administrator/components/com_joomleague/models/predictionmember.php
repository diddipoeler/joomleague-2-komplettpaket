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


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'item.php');

/**
 * Joomleague Component prediction member Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100625
 */
class JoomleagueModelpredictionmember extends JoomleagueModelItem
{
	/**
	 * Method to load content member data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_data ) )
		{
		  /*
			$query = '	SELECT *
						FROM #__joomleague_prediction_member
						WHERE id = ' . (int) $this->_id;
                        */

			$query		=	'	SELECT	tmb.*,
									u.name AS realname,
									u.username AS username,
									p.name AS predictionname
							FROM	#__joomleague_prediction_member AS tmb
							LEFT JOIN #__joomleague_prediction_game AS p ON p.id = tmb.prediction_id
							LEFT JOIN #__users AS u ON u.id = tmb.user_id 
                            WHERE tmb.id = ' . (int) $this->_id
							
							;
            $this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the member data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$member						= new stdClass();
			$member->id					= 0;
			$member->prediction_id		= 0;
			$member->user_id			= 0;
            $member->group_id			= 0;

			$member->registerDate		= '0000-00-00 00:00:00';
			$member->approved			= 0;
			$member->show_profile		= 1;
			$member->fav_team			= '';
			$member->champ_tipp			= '';
			$member->slogan				= null;
			$member->aliasName			= null;
			$member->reminder			= 0;
			$member->receipt			= 0;
			$member->admintipp			= 0;
			$member->picture			= null;
			$member->last_tipp			= null;
			$this->_data				= $member;

			return (boolean) $this->_data;
		}
		return true;
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'predictionmember', $prefix = 'table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.7
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
				array('load_data' => $loadData) );
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.7
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomleague.edit.'.$this->name.'.data', array());
		if (empty($data))
		{
			$data = $this->getData();
		}
		return $data;
	}
	
	function store($post)
    {
        $mainframe = JFactory::getApplication();
        //$mainframe->enqueueMessage(JText::_('JoomleagueModelpredictionmember store -> '.'<pre>'.print_r($post,true).'</pre>'),'');
        $member_id = $post['id'];
        $record =& JTable::getInstance('predictionmember','Table');
		$record->load($member_id);
		$record->group_id = $post['group_id'];
        
        $record->reminder = $post['reminder'];
        $record->receipt = $post['receipt'];
        $record->show_profile = $post['show_profile'];
        $record->admintipp = $post['admintipp'];
        $record->approved = $post['approved'];
        
		if (!$record->store())
					{
						$this->setError($record->getError());
						return false;
					}
        else
        {
            return true;
        }
        
    }
    
    
  function sendEmailtoMembers($cid,$prediction_id)
  {
  
  foreach ( $cid as $key => $value )
    {
    $member_email = $this->getPredictionMemberEMailAdress( $value );
    
    //echo '<br />member_email<pre>~' . print_r( $member_email, true ) . '~</pre><br />'; 

    $subject = addslashes(
				sprintf(
				JText::_( "COM_JOOMLEAGUE_EMAIL_PREDICTION_REMINDER_TIPS_RESULTS" ),
				'perdictionname' ) );
				
    $message = 'Tip-Results';


    JUtility::sendMail( '', '', $member_email, $subject, $message );

    }
  
  }



	function getSystemAdminsEMailAdresses()
	{
		$query =	'	SELECT u.email
						FROM #__users AS u
						WHERE	u.sendEmail = 1 AND
								u.block = 0 AND
								u.usertype = "Super Administrator"
						ORDER BY u.email';
//echo $query . '<br />';
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getPredictionGameAdminsEMailAdresses( $predictionGameID )
	{
		$query =	'	SELECT u.email
						FROM #__users AS u
						INNER JOIN #__joomleague_prediction_admin AS pa ON	pa.prediction_id = ' . (int) $predictionGameID . ' AND
																			pa.user_id = u.id
						WHERE	u.sendEmail = 1 AND
								u.block = 0
						ORDER BY u.email';
//echo $query . '<br />';
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getPredictionMembersEMailAdresses( $cids )
	{
		//echo '<br /><pre>~' . print_r( $cids, true ) . '~</pre><br />';
		$query =	'	SELECT user_id
						FROM #__joomleague_prediction_member
						WHERE	id IN (' . $cids . ')';
		//echo $query . '<br />';
		$this->_db->setQuery( $query );
		if ( !$cids = $this->_db->loadResultArray() ) { return false; }
		//echo '<br /><pre>~' . print_r( $cids, true ) . '~</pre><br />';

		JArrayHelper::toInteger( $cids );
		$cids = implode( ',', $cids );
		$query =	'	SELECT u.email
						FROM #__users AS u
						WHERE	
								u.block = 0 AND
								u.id IN (' . $cids . ')
						ORDER BY u.email';
		//echo $query . '<br />';
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getPredictionMemberEMailAdress( $predictionMemberID )
	{
		
    //echo '<br />predictionMemberID<pre>~' . print_r( $predictionMemberID, true ) . '~</pre><br />';
		
    $query =	'	SELECT user_id
						FROM #__joomleague_prediction_member
						WHERE	id = ' . $predictionMemberID;
		
    echo $query . '<br />';
		
    $this->_db->setQuery( $query );
		if ( !$user_id = $this->_db->loadResult() ) { return false; }
		
    echo '<br />user_id<pre>~' . print_r( $user_id, true ) . '~</pre><br />';

		$query =	'	SELECT u.email
						FROM #__users AS u
						WHERE	
								u.block = 0 AND
								u.id = ' . $user_id . '
						ORDER BY u.email';
		
    echo $query . '<br />';
		
    $this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}
    
    function getPredictionGroups()
    {
        
        $query = 'SELECT id, name as text FROM #__joomleague_prediction_groups ORDER BY name ASC ';
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return array();
		}
		return $result;
    }

	/**
	 * Method to (un)publish / (un)approve a prediction member
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5.0a
	 */
	function publish( $cid = array(), $publish = 1, $predictionGameID )
	{
		$user =& JFactory::getUser();
		if ( count( $cid ) )
		{
			$cids = implode( ',', $cid );

			$query =	'	UPDATE #__joomleague_prediction_member
							SET approved = ' . (int) $publish . '
							WHERE id IN ( ' . $cids . ' )
							AND ( checked_out = 0 OR ( checked_out = ' . (int) $user->get( 'id' ) . ' ) )';

			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}

			// create and send mail about approving member here

			$systemAdminsMails = $this->getSystemAdminsEMailAdresses();
			//echo '<br /><pre>~' . print_r( $systemAdminsMails, true ) . '~</pre><br />';

			$predictionGameAdminsMails = $this->getPredictionGameAdminsEMailAdresses( $predictionGameID );
			//echo '<br /><pre>~' . print_r( $predictionGameAdminsMails, true ) . '~</pre><br />';

			$predictionGameMembersMails = $this->getPredictionMembersEMailAdresses( $cids );
			//echo '<br /><pre>~' . print_r( $predictionGameMembersMails, true ) . '~</pre><br />';

			foreach ( $cid as $predictionMemberID )
			{
				//echo '<br /><pre>~' . print_r( $predictionMemberID, true ) . '~</pre><br />';

				$predictionGameMemberMail = $this->getPredictionMemberEMailAdress( $predictionMemberID );
				//echo '<br /><pre>~' . print_r( $predictionGameMemberMail, true ) . '~</pre><br />';

				if ( count( $predictionGameMemberMail ) > 0 )
				{
					//Fetch the mail object
					$mailer =& JFactory::getMailer();

					//Set a sender
					$config =& JFactory::getConfig();
					$sender = array( $config->getValue( 'config.mailfrom' ), $config->getValue( 'config.fromname' ) );
					//echo '<br /><pre>~' . print_r( $sender, true ) . '~</pre><br />';
					$mailer->setSender( $sender );

					//set Member as recipient
					$lastMailAdress = '';
					$recipient = array();
					foreach ( $predictionGameMemberMail AS $predictionGameMember_EMail )
					{
						if ( $lastMailAdress != $predictionGameMember_EMail )
						{
							$recipient[] = $predictionGameMember_EMail;
							$lastMailAdress = $predictionGameMember_EMail;
						}
					}
					//echo '<br />recipient<pre>~' . print_r( $recipient, true ) . '~</pre><br />';
					$mailer->addRecipient( $recipient );
					//unset( $recipient );

					//set system admins as BCC recipients
					$lastMailAdress = '';
					$recipientAdmins = array();
					foreach ( $systemAdminsMails AS $systemAdminMail )
					{
						if ( $lastMailAdress != $systemAdminMail )
						{
							$recipientAdmins[] = $systemAdminMail;
							$lastMailAdress = $systemAdminMail;
						}
					}
					$lastMailAdress = '';
					//echo '<br />recipientAdmins<pre>~' . print_r( $recipientAdmins, true ) . '~</pre><br />';

					//set predictiongame admins as BCC recipients
					foreach ( $predictionGameAdminsMails AS $predictionGameAdminMail )
					{
						if ( $lastMailAdress != $predictionGameAdminMail )
						{
							$recipientAdmins[] = $predictionGameAdminMail;
							$lastMailAdress = $predictionGameAdminMail;
						}
					}
					//echo '<br />recipientAdmins<pre>~' . print_r( $recipientAdmins, true ) . '~</pre><br />';
					$mailer->addBCC( $recipientAdmins );
					unset( $recipientAdmins );

					//Create the mail
					//$body = "Your body string\nin double quotes if you want to parse the \nnewlines etc";
					if ( $publish == 1 )
					{
						$mailer->setSubject( JText::_('JL_ADMIN_PMEMBER_MODEL_APPROVED') );
						$body = JText::_('JL_ADMIN_PMEMBER_MODEL_REQ_APPROVED');
					}
					else
					{
						$mailer->setSubject( JText::_('JL_ADMIN_PMEMBER_MODEL_REJECTED') );
						$body = JText::_('JL_ADMIN_PMEMBER_MODEL_APPROVEMENT_REJECTED');
					}
					$mailer->setBody( $body );
					echo '<br /><pre>~' . print_r( $mailer, true ) . '~</pre><br />';

					// Optional file attached
					//$mailer->addAttachment(PATH_COMPONENT.DS.'assets'.DS.'document.pdf');
					//echo '<br /><pre>~' . print_r( $mailer, true ) . '~</pre><br />';

					//Sending the mail
					$send =& $mailer->Send();
					if ( $send !== true )
					{
						echo JText::_('JL_ADMIN_PMEMBER_MODEL_ERROR_SEND') . print_r( $recipient, true ) . '<br />';
						echo JText::_('JL_ADMIN_PMEMBER_MODEL_ERROR_MSG') . $send->message;
					}
					else
					{
						echo JText::_('JL_ADMIN_PMEMBER_MODEL_MAIL_SENT');
					}
					echo '<br /><br />';
				}
				else
				{
					// joomla_user is blocked or has set sendEmail to off
					// can't send email
					//return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to remove selected items
	 * from #__joomleague_prediction_member
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */

	function deletePredictionMembers( $cid = array() )
	{
		if ( count( $cid ) )
		{
			JArrayHelper::toInteger( $cid );
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__joomleague_prediction_member WHERE id IN (' . $cids . ')';
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to remove selected items
	 * from #__joomleague_prediction_result
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */

	function deletePredictionResults($cid=array(),$prediction_id=0)
	{
		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode(',',$cid);
			$query = 'SELECT user_id FROM #__joomleague_prediction_member WHERE id IN (' . $cids . ') AND prediction_id = ' . $prediction_id;
			//echo $query . '<br />';
			$this->_db->setQuery($query);
			$this->_db->query();

			if (!$result = $this->_db->loadResultArray())
			{
				return true;
			}
			//echo '<pre>'; print_r($result); echo '</pre>';

			JArrayHelper::toInteger($result);
			$cids = implode(',',$result);
			$query = 'DELETE FROM #__joomleague_prediction_result WHERE user_id IN (' . $cids . ') AND prediction_id = ' . $prediction_id;
			//echo $query . '<br />'; return true;
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

}
?>