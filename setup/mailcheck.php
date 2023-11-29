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

if(isset($_POST['email']) && !empty($_POST['email'])) {
		
	$uniqAd = $db->get_var("SELECT count(*) from ".DB_PREFIX."users where email='" . $db->escape($_POST['email']) . "'");
	echo $uniqAd;
	
} else {
	echo '100';
}


?>