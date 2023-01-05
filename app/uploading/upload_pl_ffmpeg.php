<?php
/**
 * upload.php (Customised for PHPVibe.com)
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */


// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* 
// Support CORS
header("Access-Control-Allow-Origin: *");
// other CORS headers if any...
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	exit; // finish preflight CORS requests here
}
*/

// 5 minutes execution time
@set_time_limit(15 * 60);

// Uncomment this one to fake upload time
// usleep(5000);
require_once("../../load.php");
if(!is_user()) {
die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "'._lang("Login first!").'"}, "id" : "id"}');
}
/*
$abc = '<code>Serialized Post </code> '.maybe_serialize($_POST);
$abc .= '<code><br>Serialized GET </code> '.maybe_serialize($_GET);
$abc .= '<code><br>Serialized Request </code> '.maybe_serialize($_REQUEST).'<br>';
vibe_log($abc);
*/
//Save to db
//function
function vinsert($file) {
global $db, $token;
//Just one insert
if(!isset($_SESSION['upl-'.$token])){
$ext = substr($file, strrpos($file, '.') + 1);
$db->query("INSERT INTO ".DB_PREFIX."videos_tmp (`uid`, `name`, `path`, `ext`) VALUES ('".user_id()."', '".$token."', '".$file."', '".$ext."')");
$ext = strtolower($ext);
// Prepare conversion
$db->query("INSERT INTO ".DB_PREFIX."videos (`date`,`pub`,`token`, `user_id`, `tmp_source`, `thumb`) VALUES (now(), '0','".$token."', '".user_id()."', '".$file."','storage/uploads/processing.png')");
//Add action
$doit = $db->get_row("SELECT id from ".DB_PREFIX."videos where token = '".$token."' order by id DESC limit 0,1");
if($doit) { add_activity('4', $doit->id); }
}
//Prevent multiple
//inserts when chucking
$_SESSION['upl-'.$token] = 1;
}
// Settings
$targetDir = ABSPATH.'/storage/'.get_option('tmp-folder','rawmedia')."/";
$token = '';
if(isset($_REQUEST['token']) && not_empty($_REQUEST['token'])) {
$token = toDb($_REQUEST['token']);
} else {
if(isset($_REQUEST['pvo']) && not_empty($_REQUEST['pvo'])) {
$token = toDb($_REQUEST['pvo']);
}	
}

 if(is_empty($token)){
die('{"jsonrpc" : "2.0", "error" : {"code": 107, "message": "'._lang("Oups! Something went wrong. <br> Token was empty. [".@$_REQUEST['token']. " / ".@$_REQUEST['pvo']. "] Please refresh the page and try again").'"}, "id" : "id"}');
  }
//$targetDir = 'uploads';
$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds


// Create target dir
if (!file_exists($targetDir)) {
	@mkdir($targetDir);
}

// Get a file name
if (isset($_REQUEST["name"])) {
	$fileName = $_REQUEST["name"];
}elseif (isset($_REQUEST["fnm"])) {
	$fileName = $_REQUEST["fnm"];
} elseif (!empty($_FILES)) {
	$fileName = $_FILES["file"]["name"];
} else {
$fileName = $token;
//die('{"jsonrpc" : "2.0", "error" : {"code": 107, "message": "'._lang("Oups! Something went wrong. Please refresh the page and try again <br> If this continues rename your video file simpler before uploading.").'"}, "id" : "id"}');
}
  if(is_insecure_file(strtolower($fileName))){
die('{"jsonrpc" : "2.0", "error" : {"code": 107, "message": "'._lang("Insecure file detected! This file has an high chance of being an hacking attempt!").'"}, "id" : "id"}');
  
  }
$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
$ext = substr($fileName, strrpos($fileName, '.') + 1);
$targetPath = $targetDir . DIRECTORY_SEPARATOR . $token.'.'.$ext;;
// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


// Remove old temp files	
if ($cleanupTargetDir) {
	if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// If temp file is current file proceed to the next
		if ($tmpfilePath == "{$filePath}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			@unlink($tmpfilePath);
		}
	}
	closedir($dir);
}	


// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {	
	if (!$in = @fopen("php://input", "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
	// Strip the temp .part suffix off 
	rename("{$filePath}.part", $filePath);
	rename($filePath,$targetPath);
}
//Insert in db
vinsert($token.'.'.$ext);
// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
