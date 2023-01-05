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

if(isset($_SESSION['user_id'])){
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

echo '
<!doctype html> 
<html prefix="og: http://ogp.me/ns#"> 
 <html dir="ltr" lang="en-US">  
<head>  
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>PHPVibe 11 :: Setup</title>
<meta charset="UTF-8">  
<link rel="stylesheet" type="text/css" href="'.$site_url.ADMINCP.'/css/style.css" media="screen" />
<link href="'.$site_url.ADMINCP.'/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="'.$site_url.ADMINCP.'/css/plugins.css"/>
<link rel="stylesheet" href="'.$site_url.ADMINCP.'/css/font-awesome.css"/>
    <link href=\'https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800\' rel=\'stylesheet\' type=\'text/css\'>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
	<style>
	.panel-heading {padding:15px}
	[class*="msg-"] {padding-left:45px;}
	.msg-win {    font-size: 14px;}
	</style>
</head>
<body style="background: #fafafa">
<div id="wrapper" class="container-fluid page" style="max-width:740px; margin:30px auto; padding:20px;">
<div id="content">
<div class="row">

'; ?>
<div class="row" style="text-align:center;">
<div style="display:block;padding:2%">
<img src="https://phpvibe.com/assets/images/logobig.png"><br> 
<p style="margin:30px 0 0">
<h1>PHPVibe</h1>
</p>
</div>
Quick links:
<a style="display:inline-block; padding:2%;" target="_blank" href="https://old.phpvibe.com/installing-phpvibe/">Installing PHPVibe</a>
<a style="display:inline-block; padding:2%;" target="_blank" href="https://old.phpvibe.com/troubleshooting">Troubleshooting</a>
</div>
<div class="row">
<h2>Step 1</h2>
<?php $error = 0;

if(!hasA_license()) {
echo "<section class=\"panel panel-danger\">

<div class=\"panel-heading\" style='background-color: #f96868; padding:8px 15px; color:#fff'>
The PHPVibe license looks wrong: ".phpVibeKey."</div> 
<div style=\"padding:30px;\">

Create a license key at <a href=\"https://phpvibe.com/licenses\" target=\"_blank\"> PHPVibe.com -> Licenses</a> by adding this domain: <br />
 <p style='text-align:center; padding:14px; border:1px solid #4CAF50 ; border-style: dotted;'>".get_domain($site_url)." </p>
<br> Refresh the page and click the config icon near the domain, copy the key for v11 and add it to vibe_config.php's line <pre><code>define( 'phpVibeKey', 'License key' );</code></pre>
Replace <em>License key</em> with the created key.
</div>
</section>
";
_error();
}

if(hasA_license()) {
echo '<div class="msg-win">License key exists : '.phpVibeKey.'</div>';
}
if (strpos(get_domain(SITE_URL), get_domain($site_url)) === false) {
echo '<div class="msg-warning">Error: The url ( '.SITE_URL.' ) defined in vibe_config.php seems wrong</div>';
_error();	
} else {
echo '<div class="msg-win">You are installing PHPVibe at '.SITE_URL.'</div>';
	
}
if(substr(SITE_URL, -1) !== '/') {
echo '<div class="msg-warning">Make sure the url in vibe_config.php has an ending slash "/". </div>';
_error();
}
$parse = parse_url($site_url); 
if($parse['path'] != "/") {
echo '<div class="msg-hint">Seems PHPVibe it\'s installed in a folder. We suggest you use a subdomain or domain for a smooth experience.  </div><div class="msg-info"> But, if folder is your option please remember to edit the root/.httaccess file and change RewriteBase / to RewriteBase '.$parse['path'].' and also changed "Base path" in Settings -> Permalinks (after setup) for url rewrite to work, else it will return 404. </div>';
}
echo '<h2>Step 2: Database details</h2>';
if(!empty($db->captured_errors)) {
echo '<div class="msg-warning">Connection error! Please make sure the database details are correct and that the sql user has permissions (ALL PRIVILEGES) over the database.</div>';	
echo '<pre><code>';
var_dump($db->captured_errors);
echo '</code></pre>';
_error();
} else {
echo '<div class="msg-win"> Database connection seems fine.</div>';	
}
echo '<h2>Step 3: File permissions (chmod)</h2>';

@chmod(ABSPATH.'/'.ADMINCP.'/cache', 0777);
if (!is_writable(ABSPATH.'/'.ADMINCP.'/cache')) {
echo '<div class="msg-warning">Admin\'s cache folder ('.ABSPATH.'/'.ADMINCP.'/cache) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Admin\'s cache folder ('.ABSPATH.'/'.ADMINCP.'/cache) is writeable</div>';
}
@chmod(ABSPATH.'/'.ADMINCP.'/alog.txt', 0777);
if (!is_writable(ABSPATH.'/'.ADMINCP.'/alog.txt')) {
echo '<div class="msg-warning">Admin\'s log file ('.ABSPATH.'/'.ADMINCP.'/alog.txt) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Log file ('.ABSPATH.'/alog.txt) is writeable</div>';
}
@chmod(ABSPATH.'/storage/cache', 0777);
if (!is_writable(ABSPATH.'/storage/cache')) {
echo '<div class="msg-warning">Cache folder ('.ABSPATH.'/storage/cache) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Cache folder ('.ABSPATH.'/storage/cache) is writeable</div>';
}
@chmod(ABSPATH.'/storage/jcache', 0777);
if (!is_writable(ABSPATH.'/storage/jcache')) {
echo '<div class="msg-warning">Json cache folder ('.ABSPATH.'/storage/jcache) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Json cache folder ('.ABSPATH.'/storage/jcache) is writeable</div>';
}
@chmod(ABSPATH.'/storage/minify', 0777);
if (!is_writable(ABSPATH.'/storage/minify')) {
echo '<div class="msg-warning">Minify cache folder ('.ABSPATH.'/storage/minify) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Minify cache folder ('.ABSPATH.'/storage/minify) is writeable</div>';
}
@chmod(ABSPATH.'/storage/cache/thumbs', 0777);
if (!is_writable(ABSPATH.'/storage/cache/thumbs')) {
echo '<div class="msg-warning">Thumbs cache folder ('.ABSPATH.'/storage/cache/thumbs) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Thumbs cache folder ('.ABSPATH.'/storage/cache/thumbs) is writeable</div>';
}
@chmod(ABSPATH.'/storage/cache/full', 0777);
if (!is_writable(ABSPATH.'/storage/cache/full')) {
echo '<div class="msg-warning">Full cache ('.ABSPATH.'/storage/cache/full) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Full cache ('.ABSPATH.'/storage/cache/full) is writeable</div>';
}
@chmod(ABSPATH.'/storage/cache/html', 0777);
if (!is_writable(ABSPATH.'/storage/cache/html')) {
echo '<div class="msg-warning">Static pages cache ('.ABSPATH.'/storage/cache/html) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Static pages cache ('.ABSPATH.'/storage/cache/html) is writeable</div>';
}
@chmod(ABSPATH.'/storage/media', 0777);
if (!is_writable(ABSPATH.'/storage/media')) {
echo '<div class="msg-warning">Media storage folder ('.ABSPATH.'/storage/media) is not writeable</div>';
_error();
}else {
echo '<div class="msg-win">Media storage folder ('.ABSPATH.'/storage/media) is writeable</div>';
}
@chmod(ABSPATH.'/storage/rawmedia', 0777);
if (!is_writable(ABSPATH.'/storage/rawmedia')) {
echo '<div class="msg-warning">Raw media storage folder ('.ABSPATH.'/storage/rawmedia) is not writeable</div>';
_error();
}else {
echo '<div class="msg-win">Raw media storage folder ('.ABSPATH.'/storage/rawmedia) is writeable</div>';
}
@chmod(ABSPATH.'/storage/media/thumbs', 0777);
if (!is_writable(ABSPATH.'/storage/media/thumbs')) {
echo '<div class="msg-warning">Media thumbs storage folder ('.ABSPATH.'/storage/media/thumbs) is not writeable</div>';
_error();
}else {
echo '<div class="msg-win">Media thumbs storage folder ('.ABSPATH.'/storage/media/thumbs) is writeable</div>';
}
@chmod(ABSPATH.'/storage/uploads', 0777);
if (!is_writable(ABSPATH.'/storage/uploads')) {
echo '<div class="msg-warning">Common uploads folder ('.ABSPATH.'/storage/uploads) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Common uploads folder ('.ABSPATH.'/storage/uploads) is writeable</div>';
}
@chmod(ABSPATH.'/storage/langs', 0777);
if (!is_writable(ABSPATH.'/storage/langs')) {
echo '<div class="msg-warning">Languages folder ('.ABSPATH.'/storage/langs) is not writeable</div>';
_error();
} else {
echo '<div class="msg-win">Languages folder ('.ABSPATH.'/storage/langs) is writeable</div>';
}
if(!extension_loaded('mbstring')) { 
echo '<div class="msg-hint">Seems your host misses the mbstring extension. This is not an error, but you may see weird characters when cutting uft-8 titles  </div>';
 }
if (phpversion() < 7.3) {
echo '<div class="msg-warning">Error: PHPVibe needs PHP version 7.3 at least (your version is '.phpversion().' )</div>';
_error();
} else {
echo '<div class="msg-win">PHP is ok! (your version is '.phpversion().' ) </div>';
}
if($error > 0) {
echo "<section class=\"panel panel-danger\">
<div class=\"panel-heading\" style=\"background-color: #f96868; padding:8px 15px; color:#fff\">
Some things require attention</div> 
<div style=\"padding:30px; font-size:18px;\">Please correct the ".$error." errors listed above to continue this setup!";
if(hasA_license()) {
echo "<p><small><a href=\"".$site_url."setup/index-db.php\">Continue to the next step </a> (This may break the cms!)</small>
</p>";
}
echo "</div>
</section>";
die();
} else {
echo '<div class="msg-win">Congratulations: No files permission issues found.</div>';
echo "<p><a class=\"btn btn-block btn-lg btn-primary\" href=\"".$site_url."setup/index-db.php\">Continue to the next step </a> </p>";

}


echo '
</div>
</div>
</div>
</body>
</html>
';
}
ob_end_flush();
//That's all folks!
?>
