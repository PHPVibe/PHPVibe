<?php error_reporting(E_ALL);
// physical path of your root
if( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', str_replace( '\\', '/',  dirname( __FILE__ ) )  );
@ini_set('zlib.output_compression', 'Off');
if(isset($_GET["file"])) {
/*Build file compatibility for Vibe 3 files*/
$token = htmlspecialchars(base64_decode(base64_decode($_GET["file"])));
list($filename,$pathx) = explode('@@', $token);
if (strpos($pathx, 'storage') !== false) {
$pathx = str_replace("storage/","",$pathx);
}
$path = ABSPATH.'/storage/'.$pathx.'/';
$ext=strrchr($filename, ".");
$ignore = array('.mp4','.mp3',".jpg", ".jpeg", ".png", ".gif");
if(!in_array($ext,$ignore)) {
exit("Something is wrong");
}
$file = $path . $filename;
/* End compatibility */
} else if(isset($_GET["sk"])) {
/* PHPVibe 5 file calls */
$media = 'media'; /* Edit this if changed from admin */	
if (strpos('.',$_GET["sk"])!==false) {
exit("Something is wrong");
}
if(!isset($_GET["q"]) || (intval($_GET["q"]) < 1)) { $q = "";} else {$q = $_GET["q"]; }
$ext = '.mp4';
if(strlen($q) < 3) { 
$file = ABSPATH.'/storage/'.$media.'/'.$_GET["sk"].$ext;
} else {
$file = ABSPATH.'/storage/'.$media.'/'.$_GET["sk"].'-'.$q.$ext;	
}
/* End PHPVibe 5 File finder */
}

if (is_file($file)) {

//NOT IOS
$mime_type = str_replace(array(".","ogv"),array("","ogg"),$ext); 
header("Content-type: video/$mime_type");	
 
	if (isset($_SERVER['HTTP_RANGE']))  { /* do it for any device that supports byte-ranges not only iPhone */
	
		rangeDownload($file);
		
	}	else {
		
		header('Content-Length: ' . filesize($file));
		header("Content-Type: application/octet-stream");
        header('Content-Disposition:  attachment; filename="phpvibevideo"');
        header('Accept-Ranges: bytes');
		
		$handle = fopen($file, "rb");
		while (!feof($handle)){			
			echo fread($handle, 8192);
		}
		fclose($handle);
		exit;
		/*
		//Alternative
		set_time_limit(0);
		$stdout = fopen('php://output', 'w');
		$bfname = basename($file);

		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$bfname\"");

		$filein = fopen($file, 'r');
		stream_copy_to_stream($filein, $stdout);

		fclose($filein);
		fclose($stdout);
		exit;
		*/
		
	}
 
} else {
 
	// some error...
 
}
 
function rangeDownload($file) {
 
	$fp = @fopen($file, 'rb');
 
	$size   = filesize($file); // File size
	$length = $size;           // Content length
	$start  = 0;               // Start byte
	$end    = $size - 1;       // End byte
	// Now that we've gotten so far without errors we send the accept range header
	/* At the moment we only support single ranges.
	 * Multiple ranges requires some more work to ensure it works correctly
	 * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	 *
	 * Multirange support annouces itself with:
	 * header('Accept-Ranges: bytes');
	 *
	 * Multirange content must be sent with multipart/byteranges mediatype,
	 * (mediatype = mimetype)
	 * as well as a boundry header to indicate the various chunks of data.
	 */
	header("Accept-Ranges: 0-$length");
	// header('Accept-Ranges: bytes');
	// multipart/byteranges
	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	if (isset($_SERVER['HTTP_RANGE'])) {
 
		$c_start = $start;
		$c_end   = $end;
		// Extract the range string
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
		// Make sure the client hasn't sent us a multibyte range
		if (strpos($range, ',') !== false) {
 
			// (?) Shoud this be issued here, or should the first
			// range be used? Or should the header be ignored and
			// we output the whole content?
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		// If the range starts with an '-' we start from the beginning
		// If not, we forward the file pointer
		// And make sure to get the end byte if spesified
		if ($range0 == '-') {
 
			// The n-number of the last bytes is requested
			$c_start = $size - substr($range, 1);
		}
		else {
 
			$range  = explode('-', $range);
			$c_start = $range[0];
			$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
		}
		/* Check the range and make sure it's treated according to the specs.
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
		 */
		// End bytes can not be larger than $end.
		$c_end = ($c_end > $end) ? $end : $c_end;
		// Validate the requested range and return an error if it's not correct.
		if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
 
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		$start  = $c_start;
		$end    = $c_end;
		$length = $end - $start + 1; // Calculate new content length
		fseek($fp, $start);
		header('HTTP/1.1 206 Partial Content');
	}
	// Notify the client the byte range we'll be outputting
	header("Content-Range: bytes $start-$end/$size");
	header("Content-Length: $length");
 
	// Start buffered download
	$buffer = 1024 * 8;
	while(!feof($fp) && ($p = ftell($fp)) <= $end) {
 
		if ($p + $buffer > $end) {
 
			// In case we're only outputtin a chunk, make sure we don't
			// read past the length
			$buffer = $end - $p + 1;
		}
		set_time_limit(0); // Reset time limit for big files
		echo fread($fp, $buffer);
		flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
	}
 
	fclose($fp);
 
}