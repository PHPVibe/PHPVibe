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
$debuger = '';
		$sql_queries = array();
		
		/* Main sql files holding the tables*/
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
						
						if(!empty($db->captured_errors)) {
						$debuger .= $db->debug(false);
						}
						
						
						
					}
					}
					
				} else {
					die('setup/db.sql missing');
				}
			/* Secondary sql files (demo & more) */ 

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
						
						if(!empty($db->captured_errors)) {
						$debuger .= $db->debug(false);
						}
						
						
						
					}
					}
				}	
				else {
					die('setup/demo.sql missing');
				}
				
				/* Create a lil cookie for the website */			

				$cookie = bin2hex(random_bytes(16));
                $salt = strrev(bin2hex(random_bytes(16)));				
				$db->query( "UPDATE  ".DB_PREFIX."options SET `option_value` = '".$cookie."' WHERE `option_name` = 'COOKIEKEY'" );
	            $db->query( "UPDATE  ".DB_PREFIX."options SET `option_value` = '".$salt."' WHERE `option_name` = 'SECRETSALT'" );	

echo $debuger.'done';


?>