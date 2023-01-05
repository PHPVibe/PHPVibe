<?php  error_reporting(E_ALL); 
if(!isset($_SESSION['user_id'])) {$_SESSION['user_id'] = 0;}

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
	.w-6, .h-6 {width:20px; height:20px}
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
</div>
<div class="row">

<?php
$test_db = $db->get_col("SHOW TABLES",0);
if($test_db) {
	echo '<div class="msg-note">Pre-existing database tables found. This could be a potential issue</div>';
	foreach ($test_db as $tabelvechi) {
		echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">   <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /> </svg>
             '.$tabelvechi.'<br>';			 
	}
	echo 'Your chosen prefix for this PHPVibe install is : <strong>'.DB_PREFIX.'</strong>';
} else {
	echo '<div class="msg-win">All good! No pre-existing database tables found</div>';
}
if(isset($_REQUEST['dodb'])){ 
$sql_queries = array();

     $sql_file = 'db.sql';
				if(is_file($sql_file))
				{
					$sql_queries = array_merge($sql_queries, SQLSplit(file_get_contents($sql_file)));
		
					foreach($sql_queries as $query)
					{
					$check_q = trim($query);
					if(!empty($check_q) && !is_null($check_q)) {					
					
						$qt = str_replace("#dbprefix#",DB_PREFIX,$query);
						$db->query($qt);
						if(isset($_REQUEST['debug'])){ 
						$db->debug();
						}
					}
					}
					
				}	
				$d_file = 'demo.sql';
				if(is_file($d_file))
				{               
				
				$d_queries = array();
					$d_queries = array_merge($d_queries, SQLSplit(file_get_contents($d_file)));
		
					foreach($d_queries as $query)
					{
					$check_q = trim($query);
					if(!empty($check_q) && !is_null($check_q)) {					
					
						$qt = str_replace("#dbprefix#",DB_PREFIX,$query);
						$db->query($qt);
						if(isset($_REQUEST['debug'])){
						$db->debug();
						}
					}
					}
				}					
                $cookie = md5(SITE_URL).rand(56, 456);
                $salt = substr($cookie, 2, 16);				
				$db->query( "UPDATE  ".DB_PREFIX."options SET `option_value` = '".$cookie."' WHERE `option_name` = 'COOKIEKEY'" );
	            $db->query( "UPDATE  ".DB_PREFIX."options SET `option_value` = '".$salt."' WHERE `option_name` = 'SECRETSALT'" );

				//* Install demo data *//
	echo "<p><a class=\"btn btn-block btn-lg btn-default\" href=\"".$site_url."setup/index-db.php?dodb=1&debug=1\">Issues? Debug! </a> </p>";			
	echo "<p><a class=\"btn btn-block btn-lg btn-primary\" href=\"".$site_url."setup/index-admin.php\">Continue to next step </a> </p>";			
} else {
echo "<p><a class=\"btn btn-block btn-lg btn-primary\" href=\"".$site_url."setup/index-db.php?dodb=1\">Install tables </a> </p>";

echo "<p style=\"text-align:center; padding:25px 0\"> OR  </p>
<p><a class=\"btn btn-block btn-lg btn-default\" href=\"".$site_url."setup/index-admin.php\">Skip step </a> </p>
<p style=\"text-align:center\">(If you know what you are doing!)</p>";
}
echo '

</div>
</div>
</div>
</body>
</html>
';

ob_end_flush();
//That's all folks!
?>
