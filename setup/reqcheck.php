<?php  error_reporting(E_ALL); 
if(!isset($_SESSION['user_id'])) {$_SESSION['user_id'] = 1;}

// security
if( !defined( 'in_phpvibe' ) )
	define( 'in_phpvibe', true);
// physical path of your root
if( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', str_replace( '\\', '/',  dirname(dirname( __FILE__ ) ))  );
// physical path of includes directory
if( !defined( 'INC' ) )
define( 'INC', ABSPATH.'/app/classes' );	
//Check if config exists
if(!is_readable(ABSPATH.'/vibe_config.php')){
echo '<h1>Hold on! Configuration file (vibe_config.php) is missing! </h1><br />';
die();
}	
//Config include
require_once(ABSPATH."/vibe_config.php");
require_once(ABSPATH."/setup/functions.php");
// Include all db classes
// Sql db classes
require_once( INC.'/ez_sql_core.php' );
if( !defined( 'cacheEngine' ) || (cacheEngine == "mysql") ) {
require_once( INC.'/ez_sql_mysql.php' );
  /* Define live db for MySql */
$db = new ezSQL_mysql(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');
 /* Define cached db for MySql */
$cachedb = new ezSQL_mysql(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');
} else {
require_once( INC.'/ez_sql_mysqli.php' );	
  /* Define live db for MySql Improved */
$db = new ezSQL_mysqli(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');
 /* Define cached db for MySql Improved */
$cachedb = new ezSQL_mysqli(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');	
}

	
ob_start();
$error = 0;
// Base URI

$base_href_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);

$base_href_protocol = ( array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http' ).'://';
if( array_key_exists('HTTP_HOST', $_SERVER) && !empty($_SERVER['HTTP_HOST']) )
{
	$base_href_host = $_SERVER['HTTP_HOST'];
}
elseif( array_key_exists('SERVER_NAME', $_SERVER) && !empty($_SERVER['SERVER_NAME']) )
{
	$base_href_host = $_SERVER['SERVER_NAME'].( $_SERVER['SERVER_PORT'] !== 80 ? ':'.$_SERVER['SERVER_PORT'] : '' );
}
$base_href = rtrim( $base_href_protocol.$base_href_host.$base_href_path, "/" ).'/';

$site_url = str_replace("setup/","",$base_href);

		
$error = 0;


echo '
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Requirements test</title>
    <link rel="stylesheet" type="text/css" href="'.$base_href.'setup.style.css" media="screen" />
	<script type="text/javascript" src="'.$base_href.'jquery.js"></script>
	<style>
	body, html, #wrapper {
    background-color: #fff;
}
	#wrapper ,#content {
  margin:0;
  border-radius: 0;
  padding:0;
    width: 100%;
    width: 100vw;
    max-width: 100%;
}
#content {
	padding:15px;
	min-height:100vh
}
.scrollbar {
	height:990px;
}
.lillist {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}
	</style>
  </head>
  <body>
  <div id="wrapper" class="container">
  <div class="scrollbar" id="style-1">
<div id="content">
';

function getDataFromUrl($url) {
		$ch = curl_init();
		$timeout = 15;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
$aerror = '<div class="oksign">
	<span class="bg-red">
	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>	</span>
	</div>';
$caution = '<div class="oksign">
	<span class="bg-yellow">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
	</span>
	</div>';
$passed =  '<div class="oksign">
	<span class="bg-blue">
			<svg  x-description="Heroicon name: solid/check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
			  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
			</svg>
	</span>
	</div>';	
	
echo '<h4>Basics</h4>';	

if (phpversion() < 7.3) {
echo '<div class="lillist">'.$aerror.' PHPVibe needs PHP version 8 or at least a 7.3 (your version is '.phpversion().' )</div>';
$error++;
}  else {
 echo '<div class="lillist">'.$passed.' PHP ('.phpversion().') is enabled. PHP 7.3+ is needed, 8 is recommended.</div>';
}
 if(function_exists('mysqli_connect')) {
 echo '<div class="lillist">'.$passed.' MySqli is enabled.</div>';
} else {
echo '<div class="lillist">'.$aerror.' MySqli is disabled.</div>';
	$error++;
}
if(extension_loaded('mbstring')) { 
echo '<div class="lillist">'.$passed.' mbString is enabled.</div>';
 } else {
 echo '<div class="lillist">'.$aerror.' mbString is disabled.</div>';
 }
 if(extension_loaded('zip')) { 
echo '<div class="lillist">'.$passed.' Zip is enabled.</div>';
 } else {
 echo '<div class="lillist">'.$aerror.' Zip is disabled.</div>';
 }
 if(function_exists('base64_decode')) {
 echo '<div class="lillist">'.$passed.' Base64 encode/decode is enabled.</div>';
} else {
echo '<div class="lillist">'.$aerror.' Base64 encode/decode is disabled.</div>';
	$error++;
}
if(extension_loaded('gd')){
echo '<div class="lillist">'.$passed.' GD is enabled.</div>';
 } else {
 echo '<div class="lillist">'.$aerror.' GD is disabled.</div>';
 }	
 if( ini_get('allow_url_fopen') ) {
echo '<div class="lillist">'.$passed.'allow_url_fopen is enabled.</div>';    
    } else {
 echo '<div class="lillist">'.$aerror.'allow_url_fopen is disabled.</div>';
    }
 if( strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
echo '<div class="lillist">'.$passed.' Apache server</div>';
} else {
 echo '<div class="lillist">'.$aerror.' PHPVibe works best on a Apache server.' .$_SERVER['SERVER_SOFTWARE'].' may not be supported out of the box.</div>';
}

if( ini_get('safe_mode') ){
 echo '<div class="lillist">'.$aerror.' Safe Mode is on. (Required : Off)</div>';
}else{
echo '<div class="lillist">'.$passed.' Safe Mode is off. (Required : Off)</div>';
}

$result = getDataFromUrl("http://labs.phpvibe.com/demo.php");
$result = json_decode($result, true);
if($result['valid'] == "true"){
 echo '<div class="lillist">'.$passed.' cUrl test passed.</div>';
} else {
echo '<div class="lillist">'.$aerror.' cUrl test failed.</div>';
$error++;
}
$isShellOk = (is_callable('shell_exec') && false === stripos(ini_get('disable_functions'), 'shell_exec'));

if($isShellOk) {
 echo '<div class="lillist">'.$passed.' shell_exec is enabled.</div>';
}	else {
echo '<div class="lillist">'.$aerror.' shell_exec is disabled.</div>'; 
}
if($isShellOk) {
$ffmpeg =shell_exec('ffmpeg -version 2>&1; echo $?');
if (empty($ffmpeg)) {
echo '<div class="lillist">'.$aerror.' Can\'t find FFMPEG from here.</div>'; 
} else {
 echo '<div class="lillist"> FFMPEG required version: 2.5+ </strong></div>'; 
exec(trim($ffmpeg)." -h full", $codecArr);
echo "<pre style=\"height:60px; overflow:auto\">";
    echo $ffmpeg;
echo "</pre>";
}
} else {
echo '<div class="lillist">'.$aerror.' Can\'t find FFMPEG FFMPEG since shell_exec is disabled.</div>'; 
}	
if($error > 0) {
echo '<div class="top30"><div class="msg-warning m-b-20"> '.(($error > 1)? $error.'warnings' : 'One warning').' listed above.</div> </div>';
} else {
echo '
<div class="top30"><div class="msg-warning">PHPVibe depends on more requirements than this test can perform, <br> you seem to have a fit hosting environment,<br> but please check the requirements list updated on our website.</div>
</div>';
}

echo '<div>
			</div>
			</div>
			</body>
			</html>';
?>