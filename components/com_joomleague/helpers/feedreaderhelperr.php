<?php
/**
 * @version		2.3
 * @package		Simple RSS Feed Reader (module)
 * @author    JoomlaWorks - http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

//include_once JPATH_COMPONENT . DS . 'helpers' . DS . 'simplepie.php';
include_once JLG_PATH_SITE . DS . 'helpers' . DS . 'simplepie.php';

// no direct access
defined('_JEXEC') or die('Restricted access');

class SimpleRssFeedReaderHelper {

	function getFeeds($feedsArray,$totalFeedItems,$perFeedItems,$feedTimeout,$dateFormat,$wordLimit,$cacheLocation,$cacheTime,$imageHandling,$riWidth,$riQuality,$feedFavicon)
  {

		/*
			Legend for '$imageHandling':
			0 - no images
			1 - fetch first image only and hide others
			2 - fetch and resize first image only and hide others
		*/

		// API
		$mainframe = &JFactory::getApplication();

		// Includes
//		require_once(dirname(__FILE__).DS.'includes'.DS.'simplepie.php');

		// Check if the cache folder exists
		$cacheFolderPath = JPATH_SITE.DS.$cacheLocation;
		if(file_exists($cacheFolderPath) && is_dir($cacheFolderPath)){
			// all OK
		} else {
			mkdir($cacheFolderPath);
		}

		// Grab the feed contents
		$sourceFeed = new SimplePie();
		$sourceFeed->set_feed_url($feedsArray);
		$sourceFeed->set_timeout($feedTimeout); // in seconds
		$sourceFeed->set_item_limit(intval($perFeedItems));
		$sourceFeed->set_useragent('Mozilla/5.0 '.SIMPLEPIE_USERAGENT);
		$sourceFeed->enable_order_by_date(true);
		$sourceFeed->set_cache_duration($cacheTime*60);
		$sourceFeed->set_cache_location($cacheFolderPath);
		$sourceFeed->init();

		// Loop through all feed items and pass them to an array.

		$feedItemsArray = array();

		foreach($sourceFeed->get_items() as $key=>$item){
			// Let's give ourselves a reference to the parent $sourceFeed object for this particular item.
			$sourceFeed = $item->get_feed();

			// Create an object to store feed elements
			$feedElements[$key] = new JObject;

			$feedElements[$key]->itemTitle 				= $item->get_title();
			$feedElements[$key]->itemLink 				= $item->get_permalink();
			$feedElements[$key]->itemDate 				= $item->get_date($dateFormat);
			$feedElements[$key]->feedTitle 				= $sourceFeed->get_title();
			$feedElements[$key]->itemDescription 	= $item->get_content();
			$feedElements[$key]->feedURL					= $item->get_feed()->subscribe_url();
			$feedElements[$key]->feedImageSrc			= '';
			if($feedFavicon) $feedElements[$key]->feedFaviconFile	= $item->get_feed()->get_favicon();
			$feedElements[$key]->siteURL					= $item->get_feed()->get_link();

			// Give each feed an index based on date
			$itemDateIndex = $item->get_date('YmdHi');

			// Pass all feed objects to an array
			$feedItemsArray[$itemDateIndex] = $feedElements[$key];
		}

		// Reverse sort by key (=feed date)
		krsort($feedItemsArray);

		// Limit output
		$outputArray = array();
		$counter = 0;
		foreach($feedItemsArray as $feedItem){
			if($counter>=$totalFeedItems) continue;

			// Determine if an image reference exists in the feed description
			if($imageHandling==1 || $imageHandling==2){
				$feedImage = SimpleRssFeedReaderHelper::getFirstImage($feedItem->itemDescription);

				// If it does, copy, resize and store it locally
				if(isset($feedImage) && $feedImage['width']>10){

					// first delete the img tag from the description
					$feedItem->itemDescription = str_replace($feedImage['tag'],'',trim($feedItem->itemDescription));

					// then process and store it
					if($riWidth<$feedImage['width'] && $imageHandling==2){
						$feedItem->feedImageSrc = SimpleRssFeedReaderHelper::generateResizedImage($feedImage['src'],$riWidth,$riQuality,'cache_img_',$cacheTime,$cacheLocation);
					} else {
						$feedItem->feedImageSrc = $feedImage['src'];
					}
				} else {
					$feedItem->feedImageSrc = '';
				}
			}

			// Strip out images from the description
			$feedItem->itemDescription = preg_replace("#<img.+?>#s","",$feedItem->itemDescription);

			// Word limit
			if($wordLimit){
				$feedItem->itemDescription = SimpleRssFeedReaderHelper::wordLimiter(strip_tags($feedItem->itemDescription),$wordLimit);
			}

			// Favicon
			if($feedFavicon) $feedItem->feedFavicon = SimpleRssFeedReaderHelper::writeFile($feedItem->feedFaviconFile,'mod_jw_srfr','favicons',$cacheTime);

			// Feed URL: $feedItem->feedURL
			// Site URL: $feedItem->siteURL

			$outputArray[] = $feedItem;
			$counter++;
		}

		return $outputArray;
	}

	// Word Limiter
	function wordLimiter($str,$limit=100,$end_char='[&#8230;]'){
		if (trim($str) == '') return $str;
		preg_match('/\s*(?:\S*\s*){'. (int) $limit .'}/', $str, $matches);
		if (strlen($matches[0]) == strlen($str)) $end_char = '';
		return rtrim($matches[0]).$end_char;
	}

	// Grab the first image in a string
	function getFirstImage($string,$minDimension=80,$maxDimension=140){
		// find images
		$regex = "#<img.+?>#s";
		if (preg_match_all($regex, $string, $matches, PREG_PATTERN_ORDER) > 0){
			$img = array();
			$img['tag'] = $matches[0][0];
			$srcPattern = "#src=\".+?\"#s";
			// grab the src of the first image
			if(preg_match($srcPattern,$matches[0][0],$match)){
				$img['src'] = str_replace('src="','',$match[0]);
				$img['src'] = str_replace('"','',$img['src']);
				list($img['width'], $img['height'], $img['type'], $img['attr']) = @ getimagesize($img['src']);
				return $img;
			}
		}
	}

	// Grab local or remote image and resize/resample it
	function generateResizedImage($url,$riWidth,$riQuality,$riPrefix,$cacheTime,$cacheFolder){

		/* legend:
		si = source image
		ri = resized image
		*/

		// TO DO: add GD check here
		jimport('joomla.filesystem.file');

		$site_absolutepath = JPATH_SITE;
		$site_httppath = JURI::base();

		// Define the directory separator
		$ds = (strtoupper(substr(PHP_OS,0,3)=='WIN')) ? '\\' : '/';

		// Cache
		$cacheTime = $cacheTime*60;
		$cacheFolderPath = $site_absolutepath.$ds.str_replace('/',$ds,$cacheFolder);
		if(file_exists($cacheFolderPath) && is_dir($cacheFolderPath)){
			// all OK
		} else {
			mkdir($cacheFolderPath);
		}

		// Get the remote filename
		$grabUrl = parse_url($url);
		$grabUrlPath = explode("/",$grabUrl['path']);
		$grabUrlPath = array_reverse($grabUrlPath);

		// Define source and target images
		$siFilename = 'temp_'.$grabUrlPath[0];
		$siPath = $cacheFolderPath.$ds.$siFilename;

		$riFilename = $riPrefix.substr(md5($siFilename),0,10).'.jpg';
		$riPath = $cacheFolderPath.$ds.$riFilename;
		$riHttpPath = $site_httppath.$cacheFolder.'/'.$riFilename;

		// Check if thumb image exists otherwise create it
		if(file_exists($riPath) && is_readable($riPath) && (filemtime($riPath)+$cacheTime) > time()){
			// do nothing
		} else {
			// Grab the local or remote image
			//$siTemp = imagecreatefromstring(file_get_contents($url));
			$siTemp = imagecreatefromstring(SimpleRssFeedReaderHelper::readFile($url));

			if ($siTemp !== false){
				// create source image locally
				imagejpeg($siTemp,$siPath);

				// grab local source image details
				list($siWidth, $siHeight, $siType) = getimagesize($siPath);

				// create an image resource for the original
				$source = imagecreatefromjpeg($siPath);

				// create an image resource for the resized image
				if($riWidth>=$siWidth){
					$riWidth = $siWidth;
					$riHeight = $siHeight;
				} else {
					$riHeight = $riWidth*$siHeight/$siWidth;
				}
				$resized = imagecreatetruecolor($riWidth,$riHeight);

				// create the resized copy
				imagecopyresampled($resized, $siTemp, 0, 0, 0, 0, $riWidth, $riHeight, $siWidth, $siHeight);

				// save the resized copy
				imagejpeg($resized,$riPath,$riQuality);

				// delete temp source
				unlink($siPath);

				// cleanup resources
				imagedestroy($source);
				imagedestroy($resized);
			}
		}

		// output
		return $riHttpPath;
	}

	// Read remote file
	function readFile($url,$extensionName='mod_jw_srfr',$subFolderName=''){

		jimport('joomla.filesystem.file');

		// Check cache folder
		if($subFolderName){
			$cacheFolderPath = JPATH_SITE.DS.'cache'.DS.$extensionName.DS.$subFolderName;
		} else {
			$cacheFolderPath = JPATH_SITE.DS.'cache'.DS.$extensionName;
		}

		if(file_exists($cacheFolderPath) && is_dir($cacheFolderPath)){
			// all OK
		} else {
			mkdir($cacheFolderPath);
		}

		// Get file
		if(substr($url,0,4)=="http"){
			// remote file
			if(ini_get('allow_url_fopen')){

				// file_get_contents
				$result = JFile::read($url);

			} elseif(in_array('curl',get_loaded_extensions())) {

				// cURL
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$chOutput = curl_exec($ch);
				curl_close($ch);
				$tmpFile = $cacheFolderPath.DS.'curl_tmp_'.substr(md5($url),0,10);
				JFile::write($tmpFile,$chOutput);
				$result = JFile::read($tmpFile);

			} else {

				// fsockopen
				$readURL = parse_url($url);
				$relativePath = (isset($readURL['query'])) ? $readURL['path']."?".$readURL['query'] : $readURL['path'];

				$fp = fsockopen($readURL['host'], 80, $errno, $errstr, 5);
				if (!$fp) {
					$result = "";
				} else {
					$out = "GET ".$relativePath." HTTP/1.1\r\n";
					$out .= "Host: ".$readURL['host']."\r\n";
					$out .= "Connection: Close\r\n\r\n";
					fwrite($fp, $out);
					$header = '';
					$body = '';
					do { $header .= fgets($fp,128); } while (strpos($header,"\r\n\r\n")=== false); // get the header data
					while (!feof($fp)) $body .= fgets($fp,128); // get the actual content
					fclose($fp);
					$tmpFile = $cacheFolderPath.DS.'fsockopen_tmp_'.substr(md5($url),0,10);
					JFile::write($tmpFile,$body);
					$result = JFile::read($tmpFile);
				}

			}
		} else {
			// local file
			$result = JFile::read($url);
		}

		return $result;
	}

	// Write remote file
	function writeFile($url,$extensionName='mod_jw_srfr',$subFolderName='',$cacheTime=30){

		jimport('joomla.filesystem.file');

		$cacheTime = $cacheTime*60;

		// Check cache folder
		if($subFolderName){
			$cacheFolderPath = JPATH_SITE.DS.'cache'.DS.$extensionName.DS.$subFolderName;
			$cacheFolderUrl = JURI::base().'cache'.'/'.$extensionName.'/'.$subFolderName;
		} else {
			$cacheFolderPath = JPATH_SITE.DS.'cache'.DS.$extensionName;
			$cacheFolderUrl = JURI::base().'cache'.'/'.$extensionName;
		}

		if(file_exists($cacheFolderPath) && is_dir($cacheFolderPath)){
			// all OK
		} else {
			mkdir($cacheFolderPath);
		}

		// Get the file extension
		$grabUrl = parse_url($url);
		$grabUrlPath = explode("/",$grabUrl['path']);
		$grabUrlPath = array_reverse($grabUrlPath);
		$urlFileType = substr($grabUrlPath[0],-3);
		$urlFileName = 'remote_'.substr(md5($url),0,10).'.'.$urlFileType;

		$fileURL = $cacheFolderUrl.'/'.$urlFileName;
		$tmpFile = $cacheFolderPath.DS.$urlFileName;

		// Check if the file exists otherwise create it
		if(file_exists($tmpFile) && is_readable($tmpFile) && (filemtime($tmpFile)+$cacheTime) > time()){
			// do nothing
		} else {
			$getUrlHeaders = get_headers($url);
			if(stristr($getUrlHeaders[0],'200')){
				$readURL = SimpleRssFeedReaderHelper::readFile($url,$extensionName,$subFolderName);
				if($readURL) JFile::write($tmpFile,$readURL);
			} else {
				// do nothing
			}
		}

		if(file_exists($tmpFile) && is_readable($tmpFile)) $result = $fileURL; else $result = '';

		return $result;

	}

} // END CLASS
