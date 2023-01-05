<?php error_reporting(0);
ini_set('memory_limit', '256M');
define ('FILE_CACHE_DIRECTORY', './storage/cache/thumbs');
/*
mThumb
URI: https://github.com/mindsharestudios/mthumb
Description: A secure PHP image resize script.
Version: 3.0.1
Author: Mindshare Studios, Inc.
Author URI: https://mind.sh/are/
License: GNU General Public License
License URI: LICENSE
*/

/**
 *
 * @created   12/21/13 2:02 PM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013-2015
 * @link      https://mindsharelabs.com/
 *
 * Copyright 2013-2016  Mindshare Studios, Inc. (https://mind.sh/are/)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * Credits: based on TimThumb by Ben Gillbanks, Mark Maunder, Tim McDaniels and Darren Hoyt
 *
 */

/*
 * --- mThumb CONFIGURATION ---
 * To edit the configs it is best to create a file called mthumb-config.php
 * and define variables you want to customize in there. It will automatically be
 * loaded by mthumb. This will save you having to re-edit these variables
 * every time you download a new version.
*/

/**
 * Version of this script *
 */
define('VERSION', '3.0.1');

//Load a config file if it exists. Otherwise, use the values below
if (file_exists(dirname(__FILE__) . '/mthumb-config.php')) {
	require_once('mthumb-config.php');
}

if (!defined('DEBUG_ON')) {
	/**
	 * Enable debug logging to web server error log (STDERR)
	 */
	define('DEBUG_ON', FALSE);
}

if (!defined('DEBUG_LEVEL')) {
	/**
	 * Debug level 1 is less noisy and 3 is the most noisy
	 *
	 */
	define('DEBUG_LEVEL', 1);
}

if (!defined('DISPLAY_ERROR_MESSAGES')) {
	/**
	 * Display error messages. Set to false to turn off errors (good for production websites)
	 */
	define('DISPLAY_ERROR_MESSAGES', FALSE);
}

if (!defined('ALLOW_EXTERNAL')) {
	/**
	 *  Allow image fetching from external websites. Will check against ALLOWED_SITES always.     *
	 */
	define('ALLOW_EXTERNAL', FALSE);
}

if (!isset($ALLOWED_SITES)) {
	/**
	 *  If ALLOW_EXTERNAL is true then external images will only be fetched from these domains and their subdomains.
	 */
	$ALLOWED_SITES = array(
		'flickr.com',
		'staticflickr.com',
		'img.youtube.com',
		'upload.wikimedia.org',
		'imgur.com',
		'imageshack.us',
		'tinypic.com',
	);
}

if (!defined('FILE_CACHE_ENABLED')) {
	/**
	 * Should we store resized/modified images on disk to speed things up?
	 */
	define('FILE_CACHE_ENABLED', TRUE);
}

if (!defined('DAY_IN_SECONDS')) {
	define('DAY_IN_SECONDS', 3 * 24 * 60 * 60);
}

if (!defined('FILE_CACHE_TIME_BETWEEN_CLEANS')) {
	/**
	 * How often the cache is cleaned
	 */
	define('FILE_CACHE_TIME_BETWEEN_CLEANS', DAY_IN_SECONDS * 30);
}

if (!defined('FILE_CACHE_MAX_FILE_AGE')) {
	/**
	 *  How old does a file have to be to be deleted from the cache
	 */
	define('FILE_CACHE_MAX_FILE_AGE', DAY_IN_SECONDS * 60);
}

if (!defined('FILE_CACHE_SUFFIX')) {
	/**
	 * What to put at the end of all files in the cache directory so we can identify them
	 */
	define('FILE_CACHE_SUFFIX', '.txt');
}

if (!defined('FILE_CACHE_PREFIX')) {
	/**
	 * What to put at the beg of all files in the cache directory so we can identify them
	 */
	define('FILE_CACHE_PREFIX', 'ig');
}

if (!defined('FILE_CACHE_DIRECTORY')) {
	/**
	 * Directory where images are cached. Left blank it will use the system temporary directory (which is better for security)
	 *
	 */
	//define ('FILE_CACHE_DIRECTORY', './cache');
	define('FILE_CACHE_DIRECTORY', FALSE); // @todo test on more deployments
}

if (!defined('TEN_MB_IN_BTYES')) {
	define('TEN_MB_IN_BTYES', 100485760);
}
//
if (!defined('MAX_FILE_SIZE')) {
	/**
	 * This is the max internal or external file size that we'll process.
	 */
	define('MAX_FILE_SIZE', TEN_MB_IN_BTYES * 2);
}

if (!defined('CURL_TIMEOUT')) {
	/**
	 * Timeout duration for Curl. This only applies if you have Curl installed and aren't using PHP's default URL fetching mechanism.
	 *
	 */
	define('CURL_TIMEOUT', 20);
}

if (!defined('WAIT_BETWEEN_FETCH_ERRORS')) {
	/**
	 * Time to wait between errors fetching remote file
	 */
	define('WAIT_BETWEEN_FETCH_ERRORS', 3600);
}

if (!defined('BROWSER_CACHE_MAX_AGE')) {
	/**
	 * Time to cache in the browser
	 *
	 */
	define('BROWSER_CACHE_MAX_AGE', DAY_IN_SECONDS * 30);
}

if (!defined('BROWSER_CACHE_DISABLE')) {
	/**
	 *  Use for testing if you want to disable all browser caching
	 */
	define('BROWSER_CACHE_DISABLE', FALSE);
}

if (!defined('MAX_WIDTH')) {
	define('MAX_WIDTH', 1200);
}
if (!defined('MAX_HEIGHT')) {
	define('MAX_HEIGHT', 1200);
}

if (!defined('PNG_IS_TRANSPARENT')) {
	/**
	 * Define if a png image should have a transparent background color. Use False value if you want to display a custom coloured canvas_colour
	 *
	 */
	define('PNG_IS_TRANSPARENT', TRUE);
}

if (!defined('DEFAULT_Q')) {
	/**
	 * Default image quality. Allows override in mthumb-config.php
	 *
	 */
	define('DEFAULT_Q', 85);
}

if (!defined('DEFAULT_ZC')) {
	/**
	 * Default zoom/crop setting. Allows override in mthumb-config.php
	 */
	define('DEFAULT_ZC', 1);
}

if (!defined('DEFAULT_F')) {
	/**
	 * Default image filters. Allows override in mthumb-config.php
	 */
	define('DEFAULT_F', '');
}

//
if (!defined('DEFAULT_S')) {
	/**
	 * Default sharpen value. Allows override in mthumb-config.php
	 */
	define('DEFAULT_S', 0);
}

if (!defined('DEFAULT_CC')) {
	/**
	 * Default canvas colour. Allows override in mthumb-config.php
	 */
	define('DEFAULT_CC', 'ffffff');
}

if (!defined('DEFAULT_WIDTH')) {
	/**
	 * Default thumbnail width. Allows override in mthumb-config.php
	 */
	define('DEFAULT_WIDTH', 225);
}

if (!defined('DEFAULT_HEIGHT')) {
	/**
	 * Default thumbnail height. Allows override in mthumb-config.php
	 */
	define('DEFAULT_HEIGHT', 225);
}

/**
 * Additional Parameters:
 * LOCAL_FILE_BASE_DIRECTORY = Override the DOCUMENT_ROOT. This is best used in mthumb-config.php
 */

if (!defined('OPTIPNG_ENABLED')) {
	/**
	 * Image compression is enabled if either of these point to valid paths. They only work for PNGs. GIFs and JPEGs are not affected.
	 */
	define('OPTIPNG_ENABLED', TRUE);
}

if (!defined('OPTIPNG_PATH')) {
	/**
	 * This will run first because it gives better compression than pngcrush.
	 *
	 */
	define('OPTIPNG_PATH', '/usr/bin/optipng');
}

if (!defined('PNGCRUSH_ENABLED')) {
	define('PNGCRUSH_ENABLED', TRUE);
}

if (!defined('PNGCRUSH_PATH')) {
	/**
	 * This will only run if OPTIPNG_PATH is not set or is not valid
	 */
	define('PNGCRUSH_PATH', '/usr/bin/pngcrush');
}

// -------------- STOP EDITING CONFIGURATION HERE --------------

if (!class_exists('mthumb')) : /**
 * Class mthumb
 */ {
	class mthumb {

		/**
		 * @var mixed|string
		 */
		protected $src = "";
		/**
		 * @var bool
		 */
		protected $is404 = FALSE;
		/**
		 * @var string
		 */
		protected $docRoot = "";
		/**
		 * @var bool
		 */
		protected $lastURLError = FALSE;
		/**
		 * @var bool|string
		 */
		protected $localImage = "";
		/**
		 * @var int
		 */
		protected $localImageMTime = 0;
		/**
		 * @var bool|mixed
		 */
		protected $url = FALSE;
		/**
		 * @var mixed|string
		 */
		protected $myHost = "";
		/**
		 * @var bool
		 */
		protected $isURL = FALSE;
		/**
		 * @var string
		 */
		protected $cachefile = '';
		/**
		 * @var array
		 */
		protected $errors = array();
		/**
		 * @var array
		 */
		protected $toDeletes = array();
		/**
		 * @var string
		 */
		protected $cacheDirectory = '';
		/**
		 * @var int|mixed
		 */
		protected $startTime = 0;
		/**
		 * @var int
		 */
		protected $lastBenchTime = 0;
		/**
		 * @var bool
		 */
		protected $cropTop = FALSE;
		/**
		 * @var string
		 */
		protected $salt = "";
		/**
		 * Generally if mthumb.php is modified (upgraded) then the salt changes and all cache files are recreated. This is a backup mechanism to force regen.
		 *
		 * @var int
		 */
		protected $fileCacheVersion = 1;
		/**
		 *
		 * Designed to have three letter mime type, space, question mark and greater than symbol appended. 6 bytes total.
		 *
		 * @var string
		 */
		protected $filePrependSecurityBlock = "<?php die('Execution denied!'); //";
		/**
		 * @var int
		 */
		protected static $curlDataWritten = 0;
		/**
		 * @var bool
		 */
		protected static $curlFH = FALSE;

		/**
		 *
		 */
		public static function start() {
			$mthumb = new mthumb();
			$mthumb->handleErrors();

			if ($mthumb->tryBrowserCache()) {
				exit(0);
			}
			$mthumb->handleErrors();
			if (FILE_CACHE_ENABLED && $mthumb->tryServerCache()) {
				exit(0);
			}
			$mthumb->handleErrors();
			$mthumb->run();
			$mthumb->handleErrors();
			exit(0);
		}

		/**
		 *
		 */
		public function __construct() {
			global $ALLOWED_SITES;
			$this->startTime = microtime(TRUE);
			date_default_timezone_set('UTC');
			$this->debug(1, "Starting new request from " . $this->getIP() . " to " . $_SERVER[ 'REQUEST_URI' ]);
			$this->calcDocRoot();
			//On windows systems I'm assuming fileinode returns an empty string or a number that doesn't change. Check this.
			$this->salt = @filemtime(__FILE__) . '-' . @fileinode(__FILE__);
			$this->debug(3, "Salt is: " . $this->salt);
			if (FILE_CACHE_DIRECTORY) {
				if (!is_dir(FILE_CACHE_DIRECTORY)) {
					@mkdir(FILE_CACHE_DIRECTORY);
					if (!is_dir(FILE_CACHE_DIRECTORY)) {
						$this->error("Could not create the file cache directory.");

						return FALSE;
					}
				}
				$this->cacheDirectory = FILE_CACHE_DIRECTORY;
				if (!touch($this->cacheDirectory . '/index.html')) {
					$this->error("Could not create the index.html file - to fix this create an empty file named index.html file in the cache directory.");
				}
			} else {
				$this->cacheDirectory = sys_get_temp_dir();
			}
			// Clean the cache before we do anything because we don't want the first visitor after FILE_CACHE_TIME_BETWEEN_CLEANS expires to get a stale image.
			$this->cleanCache();

			$this->myHost = preg_replace('/^www\./i', '', $_SERVER[ 'HTTP_HOST' ]);

			// start mindshare fix for tilde's, check if tilde is found in src
			if (strstr($this->param('src'), '~')) {
				$url_parts = explode('/', $this->param('src'));
				foreach ($url_parts as $url_part) {
					//do not include any part with a ~ when building new url
					if (!strstr($url_part, '~')) {
						$new_dev_url .= $url_part . '/';
					}
				}
				//remove trailing slash
				$new_dev_url = substr($new_dev_url, 0, -1);
				$this->src = $new_dev_url;
			} else {
				$this->src = $this->param('src');
			}
			// end mindshare fix for tilde's
			$this->url = parse_url($this->src);
			$this->src = preg_replace('/https?:\/\/(?:www\.)?' . $this->myHost . '/i', '', $this->src);

			if (strlen($this->src) <= 3) {
				$this->error("No image specified");

				return FALSE;
			}

			// Always block external sites from using this script
			if (array_key_exists('HTTP_REFERER', $_SERVER) && (!preg_match('/^https?:\/\/(?:www\.)?' . $this->myHost . '(?:$|\/)/i', $_SERVER[ 'HTTP_REFERER' ]))) {
				// base64 encoded red image that says 'no hotlinkers' nothing to worry about! :)
				$imgData = base64_decode("R0lGODlhUAAMAIAAAP8AAP///yH5BAAHAP8ALAAAAABQAAwAAAJpjI+py+0Po5y0OgAMjjv01YUZ\nOGplhWXfNa6JCLnWkXplrcBmW+spbwvaVr/cDyg7IoFC2KbYVC2NQ5MQ4ZNao9Ynzjl9ScNYpneb\nDULB3RP6JuPuaGfuuV4fumf8PuvqFyhYtjdoeFgAADs=");
				header('Content-Type: image/gif');
				header('Content-Length: ' . strlen($imgData));
				header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
				header("Pragma: no-cache");
				header('Expires: ' . gmdate('D, d M Y H:i:s', time()));
				echo $imgData;

				return FALSE;
				exit(0);
			}
			if (preg_match('/^https?:\/\/[^\/]+/i', $this->src)) {
				$this->debug(2, "Is a request for an external URL: " . $this->src);
				$this->isURL = TRUE;
			} else {
				$this->debug(2, "Is a request for an internal file: " . $this->src);
			}
			if ($this->isURL && (!ALLOW_EXTERNAL)) {
				$this->error("You are not allowed to fetch images from an external website.");

				return FALSE;
			}
			if ($this->isURL) {

				$this->debug(2, "Fetching only from selected external sites is enabled.");
				$allowed = FALSE;
				foreach ($ALLOWED_SITES as $site) {
					if ((strtolower(substr($this->url[ 'host' ], -strlen($site) - 1)) === strtolower(".$site")) || (strtolower($this->url[ 'host' ]) === strtolower($site))) {
						$this->debug(3, "URL hostname {$this->url['host']} matches $site so allowing.");
						$allowed = TRUE;
					}
				}
				if (!$allowed) {
					return $this->error("You may not fetch images from that site. To enable this site in mthumb, you can either add it to \$ALLOWED_SITES and set ALLOW_EXTERNAL=true.");
				}
			}

			$cachePrefix = ($this->isURL ? '_ext_' : '_int_');
			if ($this->isURL) {
				$arr = explode('&', $_SERVER [ 'QUERY_STRING' ]);
				asort($arr);
				$this->cachefile = $this->cacheDirectory . '/' . FILE_CACHE_PREFIX . $cachePrefix . md5($this->salt . implode('', $arr) . $this->fileCacheVersion) . FILE_CACHE_SUFFIX;
			} else {
				$this->localImage = $this->getLocalImagePath($this->src);

				if (!$this->localImage) {
					$this->debug(1, "Could not find the local image: {$this->localImage}");
					$this->error("Could not find the internal image you specified.");
					$this->set404();

					return FALSE;
				}
				$this->debug(1, "Local image path is {$this->localImage}");
				$this->localImageMTime = @filemtime($this->localImage);
				//We include the mtime of the local file in case in changes on disk.
				$this->cachefile = $this->cacheDirectory . '/' . FILE_CACHE_PREFIX . $cachePrefix . md5($this->salt . $this->localImageMTime . $_SERVER [ 'QUERY_STRING' ] . $this->fileCacheVersion) . FILE_CACHE_SUFFIX;
			}
			$this->debug(2, "Cache file is: " . $this->cachefile);

			return TRUE;
		}

		/**
		 *
		 */
		public function __destruct() {
			foreach ($this->toDeletes as $del) {
				$this->debug(2, "Deleting temp file $del");
				@unlink($del);
			}
		}

		/**
		 * @return bool
		 */
		public function run() {
			if ($this->isURL) {
				if (!ALLOW_EXTERNAL) {
					$this->debug(1, "Got a request for an external image but ALLOW_EXTERNAL is disabled so returning error msg.");
					$this->error("You are not allowed to fetch images from an external website.");

					return FALSE;
				}
				$this->debug(3, "Got request for external image. Starting serveExternalImage.");
				$this->serveExternalImage();
			} else {
				$this->debug(3, "Got request for internal image. Starting serveInternalImage");
				$this->serveInternalImage();
			}

			return TRUE;
		}

		/**
		 * @return bool
		 */
		protected function handleErrors() {
			if ($this->haveErrors()) {
				$this->serveErrors();
				exit(0);
			}

			return FALSE;
		}

		/**
		 * @return bool
		 */
		protected function tryBrowserCache() {
			if (BROWSER_CACHE_DISABLE) {
				$this->debug(3, "Browser caching is disabled");

				return FALSE;
			}
			if (!empty($_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ])) {
				$this->debug(3, "Got a conditional get");
				$mtime = FALSE;
				//We've already checked if the real file exists in the constructor
				if (!is_file($this->cachefile)) {
					//If we don't have something cached, regenerate the cached image.
					return FALSE;
				}
				if ($this->localImageMTime) {
					$mtime = $this->localImageMTime;
					$this->debug(3, "Local real file's modification time is $mtime");
				} else {
					if (is_file($this->cachefile)) { //If it's not a local request then use the mtime of the cached file to determine the 304
						$mtime = @filemtime($this->cachefile);
						$this->debug(3, "Cached file's modification time is $mtime");
					}
				}
				if (!$mtime) {
					return FALSE;
				}

				$iftime = strtotime($_SERVER[ 'HTTP_IF_MODIFIED_SINCE' ]);
				$this->debug(3, "The conditional get's if-modified-since unixtime is $iftime");
				if ($iftime < 1) {
					$this->debug(3, "Got an invalid conditional get modified since time. Returning false.");

					return FALSE;
				}
				// Real file or cache file has been modified since last request, so force refetch.
				if ($iftime < $mtime) {
					$this->debug(3, "File has been modified since last fetch.");

					return FALSE;
				} else { //Otherwise serve a 304
					$this->debug(3, "File has not been modified since last get, so serving a 304.");
					header($_SERVER[ 'SERVER_PROTOCOL' ] . ' 304 Not Modified');
					$this->debug(1, "Returning 304 not modified");

					return TRUE;
				}
			}

			return FALSE;
		}

		/**
		 * @return bool
		 */
		protected function tryServerCache() {
			$this->debug(3, "Trying server cache");
			if (file_exists($this->cachefile)) {
				$this->debug(3, "Cachefile {$this->cachefile} exists");
				if ($this->isURL) {
					$this->debug(3, "This is an external request, so checking if the cachefile is empty which means the request failed previously.");
					if (filesize($this->cachefile) < 1) {
						$this->debug(3, "Found an empty cachefile indicating a failed earlier request. Checking how old it is.");
						//Fetching error occured previously
						if (time() - @filemtime($this->cachefile) > WAIT_BETWEEN_FETCH_ERRORS) {
							$this->debug(3, "File is older than " . WAIT_BETWEEN_FETCH_ERRORS . " seconds. Deleting and returning false so app can try and load file.");
							@unlink($this->cachefile);

							return FALSE; //to indicate we didn't serve from cache and app should try and load
						} else {
							$this->debug(3, "Empty cachefile is still fresh so returning message saying we had an error fetching this image from remote host.");
							$this->set404();
							$this->error("An error occured fetching image.");

							return FALSE;
						}
					}
				} else {
					$this->debug(3, "Trying to serve cachefile {$this->cachefile}");
				}
				if ($this->serveCacheFile()) {
					$this->debug(3, "Succesfully served cachefile {$this->cachefile}");

					return TRUE;
				} else {
					$this->debug(3, "Failed to serve cachefile {$this->cachefile} - Deleting it from cache.");
					//Image serving failed. We can't retry at this point, but lets remove it from cache so the next request recreates it
					@unlink($this->cachefile);

					return TRUE;
				}
			}
		}

		/**
		 * @param $err
		 *
		 * @return bool
		 */
		protected function error($err) {
			$this->debug(3, "Adding error message: $err");
			$this->errors[] = $err;

			return FALSE;
		}

		/**
		 * @return bool
		 */
		protected function haveErrors() {
			if (sizeof($this->errors) > 0) {
				return TRUE;
			}

			return FALSE;
		}

		/**
		 *
		 */
		protected function serveErrors() {
			if (!DISPLAY_ERROR_MESSAGES) {
				return;
			}
			header($_SERVER[ 'SERVER_PROTOCOL' ] . ' 400 Bad Request');
			$html = '<ul>';
			foreach ($this->errors as $err) {
				$html .= '<li>' . htmlentities($err) . '</li>';
			}
			$html .= '</ul>';
			echo '<h1>An error has occured</h1>The following error(s) occured:<br />' . $html . '<br />';
			echo '<br />Query String: ' . htmlentities($_SERVER[ 'QUERY_STRING' ], ENT_QUOTES);
		}

		/**
		 * @return bool
		 */
		protected function serveInternalImage() {
			$this->debug(3, "Local image path is $this->localImage");
			if (!$this->localImage) {
				$this->sanityFail("localImage not set after verifying it earlier in the code.");

				return FALSE;
			}
			$fileSize = filesize($this->localImage);
			if ($fileSize > MAX_FILE_SIZE) {
				$this->error("The file you specified is greater than the maximum allowed file size.");

				return FALSE;
			}
			if ($fileSize <= 0) {
				$this->error("The file you specified is <= 0 bytes.");

				return FALSE;
			}
			$this->debug(3, "Calling processImageAndWriteToCache() for local image.");
			if ($this->processImageAndWriteToCache($this->localImage)) {
				$this->serveCacheFile();

				return TRUE;
			} else {
				return FALSE;
			}
		}

		/**
		 * @return bool
		 */
		protected function serveExternalImage() {
			if (!preg_match('/^https?:\/\/[a-zA-Z0-9\-\.]+/i', $this->src)) {
				$this->error("Invalid URL supplied.");

				return FALSE;
			}
			$tempfile = tempnam($this->cacheDirectory, 'mthumb');
			$this->debug(3, "Fetching external image into temporary file $tempfile");
			$this->toDelete($tempfile);
			// fetch file here
			if (!$this->getURL($this->src, $tempfile)) {
				@unlink($this->cachefile);
				touch($this->cachefile);
				$this->debug(3, "Error fetching URL: " . $this->lastURLError);
				$this->error("Error reading the URL you specified from remote host." . $this->lastURLError);

				return FALSE;
			}

			$mimeType = $this->getMimeType($tempfile);
			if (!preg_match("/^image\/(?:jpg|jpeg|gif|png)$/i", $mimeType)) {
				$this->debug(3, "Remote file has invalid mime type: $mimeType");
				@unlink($this->cachefile);
				touch($this->cachefile);
				$this->error("The remote file is not a valid image. Mimetype = '" . $mimeType . "'" . $tempfile);

				return FALSE;
			}
			if ($this->processImageAndWriteToCache($tempfile)) {
				$this->debug(3, "Image processed succesfully. Serving from cache");

				return $this->serveCacheFile();
			} else {
				return FALSE;
			}
		}

		/**
		 * @return bool
		 */
		protected function cleanCache() {
			if (FILE_CACHE_TIME_BETWEEN_CLEANS < 0) {
				return;
			}
			$this->debug(3, "cleanCache() called");
			$lastCleanFile = $this->cacheDirectory . '/mthumb_cacheLastCleanTime.touch';

			// If the cache dir isn't writable, exit
			if (!is_writable($lastCleanFile)) {
				return;
			}
			//If this is a new mthumb installation we need to create the file
			if (!is_file($lastCleanFile)) {
				$this->debug(1, "File tracking last clean doesn't exist. Creating $lastCleanFile");
				if (!touch($lastCleanFile)) {
					$this->error("Could not create cache clean timestamp file.");
				}

				return;
			}
			if (@filemtime($lastCleanFile) < (time() - FILE_CACHE_TIME_BETWEEN_CLEANS)) { //Cache was last cleaned more than 1 day ago
				$this->debug(1, "Cache was last cleaned more than " . FILE_CACHE_TIME_BETWEEN_CLEANS . " seconds ago. Cleaning now.");
				// Very slight race condition here, but worst case we'll have 2 or 3 servers cleaning the cache simultaneously once a day.
				if (!touch($lastCleanFile)) {
					$this->error("Could not create cache clean timestamp file.");
				}
				$files = glob($this->cacheDirectory . '/*' . FILE_CACHE_SUFFIX);
				if ($files) {
					$timeAgo = time() - FILE_CACHE_MAX_FILE_AGE;
					foreach ($files as $file) {
						if (@filemtime($file) < $timeAgo) {
							$this->debug(3, "Deleting cache file $file older than max age: " . FILE_CACHE_MAX_FILE_AGE . " seconds");
							@unlink($file);
						}
					}
				}

				return TRUE;
			} else {
				$this->debug(3, "Cache was cleaned less than " . FILE_CACHE_TIME_BETWEEN_CLEANS . " seconds ago so no cleaning needed.");
			}

			return FALSE;
		}

		/**
		 * @param $localImage
		 *
		 * @return bool
		 */
		protected function processImageAndWriteToCache($localImage) {
			$sData = getimagesize($localImage);
			$origType = $sData[ 2 ];
			$mimeType = $sData[ 'mime' ];

			$this->debug(3, "Mime type of image is $mimeType");
			if (!preg_match('/^image\/(?:gif|jpg|jpeg|png)$/i', $mimeType)) {
				return $this->error("The image being resized is not a valid gif, jpg or png.");
			}

			if (!function_exists('imagecreatetruecolor')) {
				return $this->error('GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library');
			}

			if (function_exists('imagefilter') && defined('IMG_FILTER_NEGATE')) {
				$imageFilters = array(
					1  => array(IMG_FILTER_NEGATE, 0),
					2  => array(IMG_FILTER_GRAYSCALE, 0),
					3  => array(IMG_FILTER_BRIGHTNESS, 1),
					4  => array(IMG_FILTER_CONTRAST, 1),
					5  => array(IMG_FILTER_COLORIZE, 4),
					6  => array(IMG_FILTER_EDGEDETECT, 0),
					7  => array(IMG_FILTER_EMBOSS, 0),
					8  => array(IMG_FILTER_GAUSSIAN_BLUR, 0),
					9  => array(IMG_FILTER_SELECTIVE_BLUR, 0),
					10 => array(IMG_FILTER_MEAN_REMOVAL, 0),
					11 => array(IMG_FILTER_SMOOTH, 0),
				);
			}

			// get standard input properties
			$new_width = (int) abs($this->param('w', 0));
			$new_height = (int) abs($this->param('h', 0));
			$zoom_crop = (int) $this->param('zc', DEFAULT_ZC);
			$quality = (int) abs($this->param('q', DEFAULT_Q));
			$align = $this->cropTop ? 't' : $this->param('a', 'c');
			$filters = $this->param('f', DEFAULT_F);
			$sharpen = (bool) $this->param('s', DEFAULT_S);
			$canvas_color = $this->param('cc', DEFAULT_CC);
			$canvas_trans = (bool) $this->param('ct', '1');

			// set default width and height if neither are set already
			if ($new_width == 0 && $new_height == 0) {
				$new_width = (int) DEFAULT_WIDTH;
				$new_height = (int) DEFAULT_HEIGHT;
			}

			// ensure size limits can not be abused
			$new_width = min($new_width, MAX_WIDTH);
			$new_height = min($new_height, MAX_HEIGHT);

			// open the existing image
			$image = $this->openImage($mimeType, $localImage);
			if ($image === FALSE) {
				return $this->error('Unable to open image.');
			}

			// Get original width and height
			$width = imagesx($image);
			$height = imagesy($image);
			$origin_x = 0;
			$origin_y = 0;

			// generate new w/h if not provided
			if ($new_width && !$new_height) {
				$new_height = floor($height * ($new_width / $width));
			} else {
				if ($new_height && !$new_width) {
					$new_width = floor($width * ($new_height / $height));
				}
			}

			// scale down and add borders
			if ($zoom_crop == 3) {

				$final_height = $height * ($new_width / $width);

				if ($final_height > $new_height) {
					$new_width = $width * ($new_height / $height);
				} else {
					$new_height = $final_height;
				}
			}

			// create a new true color image
			$canvas = imagecreatetruecolor($new_width, $new_height);
			imagealphablending($canvas, FALSE);

			if (strlen($canvas_color) == 3) { //if is 3-char notation, edit string into 6-char notation
				$canvas_color = str_repeat(substr($canvas_color, 0, 1), 2) . str_repeat(substr($canvas_color, 1, 1), 2) . str_repeat(substr($canvas_color, 2, 1), 2);
			} else {
				if (strlen($canvas_color) != 6) {
					$canvas_color = DEFAULT_CC; // on error return default canvas color
				}
			}

			$canvas_color_R = hexdec(substr($canvas_color, 0, 2));
			$canvas_color_G = hexdec(substr($canvas_color, 2, 2));
			$canvas_color_B = hexdec(substr($canvas_color, 4, 2));

			// Create a new transparent color for image
			// If is a png and PNG_IS_TRANSPARENT is false then remove the alpha transparency
			// (and if is set a canvas color show it in the background)
			if (preg_match('/^image\/png$/i', $mimeType) && !PNG_IS_TRANSPARENT && $canvas_trans) {
				$color = imagecolorallocatealpha($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 127);
			} else {
				$color = imagecolorallocatealpha($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 0);
			}

			// Completely fill the background of the new image with allocated color.
			imagefill($canvas, 0, 0, $color);

			// scale down and add borders
			if ($zoom_crop == 2) {

				$final_height = $height * ($new_width / $width);

				if ($final_height > $new_height) {

					$origin_x = $new_width / 2;
					$new_width = $width * ($new_height / $height);
					$origin_x = round($origin_x - ($new_width / 2));
				} else {

					$origin_y = $new_height / 2;
					$new_height = $final_height;
					$origin_y = round($origin_y - ($new_height / 2));
				}
			}

			// Restore transparency blending
			imagesavealpha($canvas, TRUE);

			if ($zoom_crop > 0) {

				$src_x = $src_y = 0;
				$src_w = $width;
				$src_h = $height;

				$cmp_x = $width / $new_width;
				$cmp_y = $height / $new_height;

				// calculate x or y coordinate and width or height of source
				if ($cmp_x > $cmp_y) {

					$src_w = round($width / $cmp_x * $cmp_y);
					$src_x = round(($width - ($width / $cmp_x * $cmp_y)) / 2);
				} else {
					if ($cmp_y > $cmp_x) {

						$src_h = round($height / $cmp_y * $cmp_x);
						$src_y = round(($height - ($height / $cmp_y * $cmp_x)) / 2);
					}
				}

				// positional cropping!
				if ($align) {
					if (strpos($align, 't') !== FALSE) {
						$src_y = 0;
					}
					if (strpos($align, 'b') !== FALSE) {
						$src_y = $height - $src_h;
					}
					if (strpos($align, 'l') !== FALSE) {
						$src_x = 0;
					}
					if (strpos($align, 'r') !== FALSE) {
						$src_x = $width - $src_w;
					}
				}

				imagecopyresampled($canvas, $image, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);
			} else {

				// copy and resize part of an image with resampling
				imagecopyresampled($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			}

			if ($filters != '' && function_exists('imagefilter') && defined('IMG_FILTER_NEGATE')) {
				// apply filters to image
				$filterList = explode('|', $filters);
				foreach ($filterList as $fl) {

					$filterSettings = explode(',', $fl);
					if (isset ($imageFilters[ $filterSettings[ 0 ] ])) {

						for ($i = 0; $i < 4; $i++) {
							if (!isset ($filterSettings[ $i ])) {
								$filterSettings[ $i ] = NULL;
							} else {
								$filterSettings[ $i ] = (int) $filterSettings[ $i ];
							}
						}

						switch ($imageFilters[ $filterSettings[ 0 ] ][ 1 ]) {

							case 1:

								imagefilter($canvas, $imageFilters[ $filterSettings[ 0 ] ][ 0 ], $filterSettings[ 1 ]);
								break;

							case 2:

								imagefilter($canvas, $imageFilters[ $filterSettings[ 0 ] ][ 0 ], $filterSettings[ 1 ], $filterSettings[ 2 ]);
								break;

							case 3:

								imagefilter($canvas, $imageFilters[ $filterSettings[ 0 ] ][ 0 ], $filterSettings[ 1 ], $filterSettings[ 2 ], $filterSettings[ 3 ]);
								break;

							case 4:

								imagefilter($canvas, $imageFilters[ $filterSettings[ 0 ] ][ 0 ], $filterSettings[ 1 ], $filterSettings[ 2 ], $filterSettings[ 3 ], $filterSettings[ 4 ]);
								break;

							default:

								imagefilter($canvas, $imageFilters[ $filterSettings[ 0 ] ][ 0 ]);
								break;
						}
					}
				}
			}

			// sharpen image
			if ($sharpen && function_exists('imageconvolution')) {

				$sharpenMatrix = array(
					array(-1, -1, -1),
					array(-1, 16, -1),
					array(-1, -1, -1),
				);

				$divisor = 8;
				$offset = 0;

				imageconvolution($canvas, $sharpenMatrix, $divisor, $offset);
			}
			//Straight from Wordpress core code. Reduces filesize by up to 70% for PNG's
			if ((IMAGETYPE_PNG == $origType || IMAGETYPE_GIF == $origType) && function_exists('imageistruecolor') && !imageistruecolor($image) && imagecolortransparent($image) > 0) {
				imagetruecolortopalette($canvas, FALSE, imagecolorstotal($image));
			}

			$tempfile = tempnam($this->cacheDirectory, 'mthumb_tmpimg_');
			if (preg_match('/^image\/(?:jpg|jpeg)$/i', $mimeType)) {
				$imgType = 'jpg';
				imagejpeg($canvas, $tempfile, $quality);
			} else {
				if (preg_match('/^image\/png$/i', $mimeType)) {
					$imgType = 'png';
					imagepng($canvas, $tempfile, floor($quality * 0.09));
				} else {
					if (preg_match('/^image\/gif$/i', $mimeType)) {
						$imgType = 'gif';
						imagegif($canvas, $tempfile);
					} else {
						return $this->sanityFail("Could not match mime type after verifying it previously.");
					}
				}
			}

			if ($imgType == 'png' && OPTIPNG_ENABLED && OPTIPNG_PATH && @is_file(OPTIPNG_PATH)) {
				$exec = OPTIPNG_PATH;
				$this->debug(3, "optipng'ing $tempfile");
				$presize = filesize($tempfile);
				$out = `$exec -o1 $tempfile`; //you can use up to -o7 but it really slows things down
				clearstatcache();
				$aftersize = filesize($tempfile);
				$sizeDrop = $presize - $aftersize;
				if ($sizeDrop > 0) {
					$this->debug(1, "optipng reduced size by $sizeDrop");
				} else {
					if ($sizeDrop < 0) {
						$this->debug(1, "optipng increased size! Difference was: $sizeDrop");
					} else {
						$this->debug(1, "optipng did not change image size.");
					}
				}
			} else {
				if ($imgType == 'png' && PNGCRUSH_ENABLED && PNGCRUSH_PATH && @is_file(PNGCRUSH_PATH)) {
					$exec = PNGCRUSH_PATH;
					$tempfile2 = tempnam($this->cacheDirectory, 'mthumb_tmpimg_');
					$this->debug(3, "pngcrush'ing $tempfile to $tempfile2");
					$out = `$exec $tempfile $tempfile2`;

					if (is_file($tempfile2)) {
						$sizeDrop = filesize($tempfile) - filesize($tempfile2);
						if ($sizeDrop > 0) {
							$this->debug(1, "pngcrush was succesful and gave a $sizeDrop byte size reduction");
							$todel = $tempfile;
							$tempfile = $tempfile2;
						} else {
							$this->debug(1, "pngcrush did not reduce file size. Difference was $sizeDrop bytes.");
							$todel = $tempfile2;
						}
					} else {
						$this->debug(3, "pngcrush failed with output: $out");
						$todel = $tempfile2;
					}
					@unlink($todel);
				}
			}

			$this->debug(3, "Rewriting image with security header.");
			$tempfile4 = tempnam($this->cacheDirectory, 'mthumb_tmpimg_');
			$context = stream_context_create();
			$fp = fopen($tempfile, 'r', 0, $context);
			file_put_contents($tempfile4, $this->filePrependSecurityBlock . $imgType . ' ?' . '>'); //6 extra bytes, first 3 being image type
			file_put_contents($tempfile4, $fp, FILE_APPEND);
			fclose($fp);
			@unlink($tempfile);
			$this->debug(3, "Locking and replacing cache file.");
			$lockFile = $this->cachefile . '.lock';
			$fh = fopen($lockFile, 'w');
			if (!$fh) {
				return $this->error("Could not open the lockfile for writing an image.");
			}
			if (flock($fh, LOCK_EX)) {
				@unlink($this->cachefile); //rename generally overwrites, but doing this in case of platform specific quirks. File might not exist yet.
				rename($tempfile4, $this->cachefile);
				flock($fh, LOCK_UN);
				fclose($fh);
				@unlink($lockFile);
			} else {
				fclose($fh);
				@unlink($lockFile);
				@unlink($tempfile4);

				return $this->error("Could not get a lock for writing.");
			}
			$this->debug(3, "Done image replace with security header. Cleaning up and running cleanCache()");
			imagedestroy($canvas);
			imagedestroy($image);

			return TRUE;
		}

		/**
		 *
		 */
		protected function calcDocRoot() {
			$docRoot = @$_SERVER[ 'DOCUMENT_ROOT' ];
			if (defined('LOCAL_FILE_BASE_DIRECTORY')) {
				$docRoot = LOCAL_FILE_BASE_DIRECTORY;
			}
			if (!isset($docRoot)) {
				$this->debug(3, "DOCUMENT_ROOT is not set. This is probably windows. Starting search 1.");
				if (isset($_SERVER[ 'SCRIPT_FILENAME' ])) {
					$docRoot = str_replace('\\', '/', substr($_SERVER[ 'SCRIPT_FILENAME' ], 0, 0 - strlen($_SERVER[ 'PHP_SELF' ])));
					$this->debug(3, "Generated docRoot using SCRIPT_FILENAME and PHP_SELF as: $docRoot");
				}
			}
			if (!isset($docRoot)) {
				$this->debug(3, "DOCUMENT_ROOT still is not set. Starting search 2.");
				if (isset($_SERVER[ 'PATH_TRANSLATED' ])) {
					$docRoot = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER[ 'PATH_TRANSLATED' ]), 0, 0 - strlen($_SERVER[ 'PHP_SELF' ])));
					$this->debug(3, "Generated docRoot using PATH_TRANSLATED and PHP_SELF as: $docRoot");
				}
			}
			if ($docRoot && $_SERVER[ 'DOCUMENT_ROOT' ] != '/') {
				$docRoot = preg_replace('/\/$/', '', $docRoot);
			}
			$this->debug(3, "Doc root is: " . $docRoot);
			$this->docRoot = $docRoot;
		}

		/**
		 * @param $src
		 *
		 * @return bool|string
		 */
		protected function getLocalImagePath($src) {
			$src = ltrim($src, '/'); //strip off the leading '/'
			if (!$this->docRoot) {
				$this->debug(3, "We have no document root set, so as a last resort, lets check if the image is in the current dir and serve that.");
				//We don't support serving images outside the current dir if we don't have a doc root for security reasons.
				$file = preg_replace('/^.*?([^\/\\\\]+)$/', '$1', $src); //strip off any path info and just leave the filename.
				if (is_file($file)) {
					return $this->realpath($file);
				}

				return $this->error("Could not find your website document root and the file specified doesn't exist in mThumb's directory. We don't support serving files outside mThumb's directory without a document root for security reasons.");
			} else {
				if (!is_dir($this->docRoot)) {
					$this->error("Server path does not exist. Ensure variable \$_SERVER['DOCUMENT_ROOT'] is set correctly");
				}
			}

			//Do not go past this point without docRoot set

			//Try src under docRoot
			if (file_exists($this->docRoot . '/' . $src)) {
				$this->debug(3, "Found file as " . $this->docRoot . '/' . $src);
				$real = $this->realpath($this->docRoot . '/' . $src);
				if (stripos($real, $this->docRoot) === 0) {
					return $real;
				} else {
					$this->debug(1, "Security block: The file specified occurs outside the document root.");
					//allow search to continue
				}
			}
			//Check absolute paths and then verify the real path is under doc root
			$absolute = $this->realpath('/' . $src);
			if ($absolute && file_exists($absolute)) { //realpath does file_exists check, so can probably skip the exists check here
				$this->debug(3, "Found absolute path: $absolute");
				if (!$this->docRoot) {
					$this->sanityFail("docRoot not set when checking absolute path.");
				}
				if (stripos($absolute, $this->docRoot) === 0) {
					return $absolute;
				} else {
					$this->debug(1, "Security block: The file specified occurs outside the document root.");
					//and continue search
				}
			}

			$base = $this->docRoot;

			// account for Windows directory structure
			if (strstr($_SERVER[ 'SCRIPT_FILENAME' ], ':')) {
				$sub_directories = explode('\\', str_replace($this->docRoot, '', $_SERVER[ 'SCRIPT_FILENAME' ]));
			} else {
				$sub_directories = explode('/', str_replace($this->docRoot, '', $_SERVER[ 'SCRIPT_FILENAME' ]));
			}

			foreach ($sub_directories as $sub) {
				$base .= $sub . '/';
				$this->debug(3, "Trying file as: " . $base . $src);
				if (file_exists($base . $src)) {
					$this->debug(3, "Found file as: " . $base . $src);
					$real = $this->realpath($base . $src);
					if (stripos($real, $this->realpath($this->docRoot)) === 0) {
						return $real;
					} else {
						$this->debug(1, "Security block: The file specified occurs outside the document root.");
						//And continue search
					}
				}
			}

			return FALSE;
		}

		/**
		 * @param $path
		 *
		 * @return string
		 */
		protected function realpath($path) {
			// try to remove any relative paths
			$remove_relatives = '/\w+\/\.\.\//';
			while (preg_match($remove_relatives, $path)) {
				$path = preg_replace($remove_relatives, '', $path);
			}
			// if any remain use PHP realpath to strip them out, otherwise return $path
			// if using realpath, any symlinks will also be resolved
			return preg_match('#^\.\./|/\.\./#', $path) ? realpath($path) : $path;
		}

		/**
		 * @param $name
		 */
		protected function toDelete($name) {
			$this->debug(3, "Scheduling file $name to delete on destruct.");
			$this->toDeletes[] = $name;
		}

		/**
		 * @param $h
		 * @param $d
		 *
		 * @return int
		 */
		public static function curlWrite($h, $d) {
			fwrite(self::$curlFH, $d);
			self::$curlDataWritten += strlen($d);
			if (self::$curlDataWritten > MAX_FILE_SIZE) {
				return 0;
			} else {
				return strlen($d);
			}
		}

		/**
		 * @return bool
		 */
		protected function serveCacheFile() {
			$this->debug(3, "Serving {$this->cachefile}");
			if (!is_file($this->cachefile)) {
				$this->error("serveCacheFile called in mthumb but we couldn't find the cached file.");

				return FALSE;
			}
			$fp = fopen($this->cachefile, 'rb');
			if (!$fp) {
				return $this->error("Could not open cachefile.");
			}
			fseek($fp, strlen($this->filePrependSecurityBlock), SEEK_SET);
			$imgType = fread($fp, 3);
			fseek($fp, 3, SEEK_CUR);
			if (ftell($fp) != strlen($this->filePrependSecurityBlock) + 6) {
				@unlink($this->cachefile);

				return $this->error("The cached image file seems to be corrupt.");
			}
			$imageDataSize = filesize($this->cachefile) - (strlen($this->filePrependSecurityBlock) + 6);
			$this->sendImageHeaders($imgType, $imageDataSize);
			$bytesSent = @fpassthru($fp);
			fclose($fp);
			if ($bytesSent > 0) {
				return TRUE;
			}
			$content = file_get_contents($this->cachefile);
			if ($content != FALSE) {
				$content = substr($content, strlen($this->filePrependSecurityBlock) + 6);
				echo $content;
				$this->debug(3, "Served using file_get_contents and echo");

				return TRUE;
			} else {
				$this->error("Cache file could not be loaded.");

				return FALSE;
			}
		}

		/**
		 * @param $mimeType
		 * @param $dataSize
		 *
		 * @return bool
		 */
		protected function sendImageHeaders($mimeType, $dataSize) {
			if (!preg_match('/^image\//i', $mimeType)) {
				$mimeType = 'image/' . $mimeType;
			}
			if (strtolower($mimeType) == 'image/jpg') {
				$mimeType = 'image/jpeg';
			}
			$gmdate_expires = gmdate('D, d M Y H:i:s', strtotime('now +10 days')) . ' GMT';
			$gmdate_modified = gmdate('D, d M Y H:i:s') . ' GMT';
			// send content headers then display image
			header('Content-Type: ' . $mimeType);
			header('Accept-Ranges: none'); //Changed this because we don't accept range requests
			header('Last-Modified: ' . $gmdate_modified);
			header('Content-Length: ' . $dataSize);
			if (BROWSER_CACHE_DISABLE) {
				$this->debug(3, "Browser cache is disabled so setting non-caching headers.");
				header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
				header("Pragma: no-cache");
				header('Expires: ' . gmdate('D, d M Y H:i:s', time()));
			} else {
				$this->debug(3, "Browser caching is enabled");
				header('Cache-Control: max-age=' . BROWSER_CACHE_MAX_AGE . ', must-revalidate');
				header('Expires: ' . $gmdate_expires);
			}

			return TRUE;
		}

		/**
		 * @param        $property
		 * @param string $default
		 *
		 * @return string
		 */
		protected function param($property, $default = '') {
			if (isset ($_GET[ $property ])) {
				return $_GET[ $property ];
			} else {
				return $default;
			}
		}

		/**
		 * @param $mimeType
		 * @param $src
		 *
		 * @return resource
		 */
		protected function openImage($mimeType, $src) {
			switch ($mimeType) {
				case 'image/jpeg':
					$image = imagecreatefromjpeg($src);
					break;

				case 'image/png':
					$image = imagecreatefrompng($src);
					imagealphablending($image, TRUE);
					imagesavealpha($image, TRUE);
					break;

				case 'image/gif':
					$image = imagecreatefromgif($src);
					break;

				default:
					$this->error("Unrecognised mimeType");
			}

			return $image;
		}

		/**
		 * @return string
		 */
		protected function getIP() {
			$rem = @$_SERVER[ "REMOTE_ADDR" ];
			$ff = @$_SERVER[ "HTTP_X_FORWARDED_FOR" ];
			$ci = @$_SERVER[ "HTTP_CLIENT_IP" ];
			if (preg_match('/^(?:192\.168|172\.16|10\.|127\.)/', $rem)) {
				if ($ff) {
					return $ff;
				}
				if ($ci) {
					return $ci;
				}

				return $rem;
			} else {
				if ($rem) {
					return $rem;
				}
				if ($ff) {
					return $ff;
				}
				if ($ci) {
					return $ci;
				}

				return "UNKNOWN";
			}
		}

		/**
		 * @param $level
		 * @param $msg
		 */
		protected function debug($level, $msg) {
			if (DEBUG_ON && $level <= DEBUG_LEVEL) {
				$execTime = sprintf('%.6f', microtime(TRUE) - $this->startTime);
				$tick = sprintf('%.6f', 0);
				if ($this->lastBenchTime > 0) {
					$tick = sprintf('%.6f', microtime(TRUE) - $this->lastBenchTime);
				}
				$this->lastBenchTime = microtime(TRUE);
				error_log("mThumb Debug line " . __LINE__ . " [$execTime : $tick]: $msg");
			}
		}

		/**
		 * @param $msg
		 *
		 * @return bool
		 */
		protected function sanityFail($msg) {
			return $this->error("There is a problem in the mThumb code. Message: Please report this error at <a href='https://github.com/mindsharestudios/mthumb/issues'>mThumb's issue tracking page</a>: $msg");
		}

		/**
		 * @param $file
		 *
		 * @return string
		 */
		protected function getMimeType($file) {
			$info = getimagesize($file);
			if (is_array($info) && $info[ 'mime' ]) {
				return $info[ 'mime' ];
			}

			return '';
		}

		/**
		 * @param $size_str
		 *
		 * @return int
		 */
		protected static function returnBytes($size_str) {
			switch (substr($size_str, -1)) {
				case 'M':
				case 'm':
					return (int) $size_str * 1048576;
				case 'K':
				case 'k':
					return (int) $size_str * 1024;
				case 'G':
				case 'g':
					return (int) $size_str * 1073741824;
				default:
					return $size_str;
			}
		}

		/**
		 * @param $url
		 * @param $tempfile
		 *
		 * @return bool
		 */
		protected function getURL($url, $tempfile) {
			$this->lastURLError = FALSE;
			$url = preg_replace('/ /', '%20', $url);
			if (function_exists('curl_init')) {
				$this->debug(3, "Curl is installed so using it to fetch URL.");
				self::$curlFH = fopen($tempfile, 'w');
				if (!self::$curlFH) {
					$this->error("Could not open $tempfile for writing.");

					return FALSE;
				}
				self::$curlDataWritten = 0;
				$this->debug(3, "Fetching url with curl: $url");
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_TIMEOUT, CURL_TIMEOUT);
				curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30");
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($curl, CURLOPT_HEADER, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($curl, CURLOPT_WRITEFUNCTION, 'mthumb::curlWrite');
				@curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
				@curl_setopt($curl, CURLOPT_MAXREDIRS, 10);

				$curlResult = curl_exec($curl);
				fclose(self::$curlFH);
				$httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				if ($httpStatus == 404) {
					$this->set404();
				}
				if ($httpStatus == 302) {
					$this->error("External Image is Redirecting. Try alternate image URL.");

					return FALSE;
				}
				if ($curlResult) {
					curl_close($curl);

					return TRUE;
				} else {
					$this->lastURLError = curl_error($curl);
					curl_close($curl);

					return FALSE;
				}
			} else {
				$img = @file_get_contents($url);
				if ($img === FALSE) {
					$err = error_get_last();
					if (is_array($err) && $err[ 'message' ]) {
						$this->lastURLError = $err[ 'message' ];
					} else {
						$this->lastURLError = $err;
					}
					if (preg_match('/404/', $this->lastURLError)) {
						$this->set404();
					}

					return FALSE;
				}
				if (!file_put_contents($tempfile, $img)) {
					$this->error("Could not write to $tempfile.");

					return FALSE;
				}

				return TRUE;
			}
		}

		/**
		 * @param $file
		 *
		 * @return bool
		 */
		protected function serveImg($file) {
			$s = getimagesize($file);
			if (!($s && $s[ 'mime' ])) {
				return FALSE;
			}
			header('Content-Type: ' . $s[ 'mime' ]);
			header('Content-Length: ' . filesize($file));
			header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
			header("Pragma: no-cache");
			$bytes = @readfile($file);
			if ($bytes > 0) {
				return TRUE;
			}
			$content = @file_get_contents($file);
			if ($content != FALSE) {
				echo $content;

				return TRUE;
			}

			return FALSE;
		}

		/**
		 *
		 */
		protected function set404() {
			$this->is404 = TRUE;
		}

		/**
		 * @return bool
		 */
		protected function is404() {
			return $this->is404;
		}
	}
}
endif;

mthumb::start();
?>