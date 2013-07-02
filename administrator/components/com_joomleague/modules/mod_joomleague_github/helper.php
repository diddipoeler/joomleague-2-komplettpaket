<?php
/**
 * GitHub Module for Joomla!
 *
 * @package    GitHubModule
 *
 * @copyright  Copyright (C) 2011 Michael Babker. All rights reserved.
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

/**
 * Helper class for the GitHub Module
 *
 * @package  GitHubModule
 * @since    1.0
 */
class ModGithubHelper
{
	/**
	 * Function to compile the data to render a formatted object
	 *
	 * @param   object  $params  The module parameters
	 *
	 * @return  mixed  An array of data on success, an object with a failure notice otherwise.
	 *
	 * @since   1.0
	 */
	static function compileData($params)
	{
		// Load the parameters
		$uname		= $params->get('username', '');
		$repo		= $params->get('repo', '');

		// Convert the list name to a useable string for the JSON
		if ($repo)
		{
			$frepo	= self::toAscii($repo);
		}

		// Initialize the array
		$github	= array();

		$req = 'https://api.github.com/repos/'.$uname.'/'.$frepo.'/commits';

		// Fetch the decoded JSON
		$obj = self::getJSON($req);

		if (is_null($obj))
		{
			$github->error	= 'Error';
			return $github;
		}

		// Process the filtering options and render the feed
		$github = self::processData($obj, $params);

		return $github;
	}

	/**
	 * Function to fetch a JSON feed
	 *
	 * @param   string  $req  The URL of the feed to load
	 *
	 * @return  array  The decoded JSON query
	 *
	 * @since	1.0
	 */
	static function getJSON($req)
	{
		// Create a new CURL resource
		$ch = curl_init($req);

		// Set options
		curl_setopt($ch, CURLOPT_HEADER, false);
        $t_vers = curl_version();
        curl_setopt( $ch, CURLOPT_USERAGENT, 'curl/' . $t_vers['version'] );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// Grab URL and pass it to the browser and store it as $json
		$json = curl_exec($ch);

		// Close CURL resource
		curl_close($ch);

		// Decode the fetched JSON
		$obj = json_decode($json, true);

		return $obj;
	}

	/**
	 * Function to process the GitHub data into a formatted object
	 *
	 * @param   array   $obj     The JSON data
	 * @param   object  $params  The module parameters
	 *
	 * @return  array  An array of data for output
	 *
	 * @since   1.0
	 */
	static function processData($obj, $params)
	{
		// Initialize
		$github = array();
		$i = 0;

		// Load the parameters
		$uname		= $params->get('username', '');
		$repo		= $params->get('repo', '');
		$count		= $params->get('count', 3) - 1;

		// Convert the list name to a useable string for the JSON
		if ($repo)
		{
			$frepo	= self::toAscii($repo);
		}

		// Process the feed
		foreach ($obj as $o)
		{
			if ($i <= $count)
			{
				// Initialize a new object
				$github[$i]->commit	= new stdClass;

				// The commit message linked to the commit
				$github[$i]->commit->message = '<a href="https://github.com/'.$uname.'/'.$frepo.'/commit/'.$o['sha'].'" target="_blank" rel="nofollow">'.substr($o['sha'], 0, 7).'</a> - ';
				$github[$i]->commit->message .= preg_replace("/#(\w+)/", '#<a href="https://github.com/'.$uname.'/'.$frepo.'/issues/\\1" target="_blank" rel="nofollow">\\1</a>', htmlspecialchars($o['commit']['message']));

				// Check if the committer information
				if ($o['author']['id'] != $o['committer']['id'])
				{
					// The committer name formatted with link
					$github[$i]->commit->committer	= JText::_('MOD_GITHUB_AND_COMMITTED_BY').'<a href="https://github.com/'.$o['committer']['login'].'" target="_blank" rel="nofollow">'.$o['commit']['committer']['name'].'</a>';

					// The author wasn't the committer
					$github[$i]->commit->author		= JText::_('MOD_GITHUB_AUTHORED_BY');
				}
				else
				{
					// The author is also the committer
					$github[$i]->commit->author		= JText::_('MOD_GITHUB_COMMITTED_BY');
				}

				// The author name formatted with link
				$github[$i]->commit->author .= '<a href="https://github.com/'.$o['author']['login'].'" target="_blank" rel="nofollow">'.$o['commit']['author']['name'].'</a>';

				// The time of commit
				$date = date_create($o['commit']['committer']['date']);
				$date = date_format($date, 'r');
				if ($params->get('relativeTime', '1') == '1')
				{
					$ISOtime = JHTML::date($date, 'Y-m-d H:i:s');

					// Load the JavaScript; first ensure we have MooTools Core
					JHtml::_('behavior.framework');
					JHtml::script('administrator/modules/mod_joomleague_github/media/js/prettydate.js', false, false);
					$github[$i]->commit->time = ' <span class="commit-time" title="'.$ISOtime.'">'.JHtml::date($date, 'D M d H:i:s O Y').'</span>';
				}
				else
				{
					$github[$i]->commit->time = ' '.JHtml::date($date);
				}

				$i++;
			}
		}
		return $github;
	}

	/**
	 * Function to convert a formatted repo name into it's URL equivalent
	 *
	 * @param   string  $repo  The user inputted repo name
	 *
	 * @return  string  The repo name converted
	 *
	 * @since   1.0
	 */
	static function toAscii($repo)
	{
		$clean = preg_replace("/[^a-z'A-Z0-9\/_|+ -]/", '', $repo);
		$clean = strtolower(trim($clean, '-'));
		$repo  = preg_replace("/[\/_|+ -']+/", '-', $clean);

		return $repo;
	}
}
