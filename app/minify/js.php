<?php  error_reporting(E_ALL);
// Root 
if( !defined( 'ABSPATH' ) )$abs = str_replace( array('\\', '/app/minify'),array( '/',''),  dirname( __FILE__ ) );	define( 'ABSPATH', $abs  );
$cachef = ABSPATH.'/';
$themefold = isset($_GET['t']) ? $_GET['t'] : "main";
$themefold = preg_replace('/(\.+\/)/','',$themefold);	
$txt = '';

/* Define scripts */
$scripts = array (
  0 => 'bootstrap.min',
  1 => 'jquery.form.min',
  2 => 'jquery.imagesloaded.min',
  3 => 'jquery.infinitescroll.min',
  4 => 'js-alert',
  5 => 'jquery.slimscroll.min',
  6 => 'jquery.emoticons',
  7 => 'owl.carousel.min',
  8 => 'jquery.minimalect.min',
  9 => 'jquery.validarium',
  10 => 'jquery.tagsinput',
  11 => 'jssocials.min',
  12 => 'jquery.grid-a-licious.min',
  13 => 'main',
  14 => 'extravibes'
);
// Enable caching
	header('Cache-Control: public');
	// Expire in one day
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
	// Set the correct MIME type, because Apache won't set it for us
	header("Content-Type: application/javascript");
	// Start the css
	echo('/*  Powered by the PHPVibe CMS ( PHPVibe.com ) */');
/* Cache file */
$cachexists= false;
$cachedfile = $cachef.'storage/minify/js-combined.js';
    if (file_exists($cachef.'storage/minify/js-combined-min.js') && (filesize($cachef.'storage/minify/js-combined-min.js') > 100000 )) {
	// Prefer minimized	file
	/* $txt = file_get_contents($cachef.'storage/minify/js-combined-min.js');	
	$cachexists= true; 
	*/ 
	
	readfile($cachef.'storage/minify/js-combined-min.js');
	} elseif (file_exists($cachef.'storage/minify/js-combined.js')){ 
    // Use combined	file
	/* $txt = file_get_contents($cachef.'storage/minify/js-combined.js');	
	$cachexists= true; */
	readfile($cachef.'storage/minify/js-combined.js');
	} else {
    // Get individual js & combine	them	
	if(!is_null($scripts) && !empty($scripts)) {
		foreach ($scripts as $js) {			
			$jsfile = ABSPATH.'/themes/'.$themefold.'/scripts/'.trim($js).'.js';
			//echo $jsfile;	
            if (file_exists($jsfile)) {			
            $txt .= file_get_contents($jsfile);				
			/* Strip comments 
 			$txt = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/', '', $txt); 		
			*/
			}			
			
		}
		$cachexists= false;
	}
	// Print the css
	echo($txt);	
	// Write cache file 
	if(!$cachexists && !empty(trim($txt))) {  
		// Create cache file
		$f = fopen($cachedfile, 'w');	fwrite ($f, $txt);	fclose($f);			
		/* Minify */	
		include_once(ABSPATH.'/app/minify/jshrink-minifier.php');	
		$minjs = \JShrink\Minifier::minify($txt);	
		$x = fopen(str_replace('js-combined','js-combined-min',$cachedfile), 'w');	
		fwrite ($x,  $minjs);	
		fclose($x);
	}
}
?>