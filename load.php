<?php //Check session start
if (!isset($_SESSION)) { @session_start(); }
// Root
if( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', str_replace( '\\', '/',  dirname( __FILE__ ) )  );
// Includes
if( !defined( 'APP' ) )
	define( 'APP', ABSPATH.'/app' );	if( !defined( 'TRDS' ) )	define( 'TRDS', ABSPATH.'/app/3rdparty' );
if( !defined( 'INC' ) )
	define( 'INC', APP.'/library' );
if( !defined( 'FNC' ) )
	define( 'FNC', APP.'/functions' );
if( !defined( 'CNC' ) )
	define( 'CNC', APP.'/classes' );
// Security bypass
if( !defined( 'in_phpvibe' ) )
	define( 'in_phpvibe', true);
// Configs
require_once( ABSPATH.'/vibe_config.php' );
require_once( ABSPATH.'/vibe_setts.php' );
// Sql db classes
require_once( CNC.'/ez_sql_core.php' );require_once( CNC.'/ez_sql_mysqli.php' );
	  /* Define live db for MySql Improved */
	$db =  new ezSQL_mysqli(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');
	 /* Define cached db for MySql Improved */
    $cachedb =	new ezSQL_mysqli(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');
	if( !defined( 'DB_CACHE' ) ) { define( 'DB_CACHE', '8' ); } 	$cachedb->cache_timeout = DB_CACHE; 	$cachedb->cache_dir = ABSPATH.'/storage/cache';	$cachedb->use_disk_cache = true;	$cachedb->cache_queries = true;	/* Short life cache */	$shortcache = $cachedb;	$shortcache->cache_timeout = 1;
//JCache
define( 'jcachefold' , ABSPATH.'/storage/jcache');
// Include functions
require_once( CNC.'/Router.php' );
require_once( CNC.'/Route.php' );
require_once( CNC.'/HashGenerator.php');
require_once( FNC.'/functions.permalinks.php');
require_once( CNC.'/Hashids.php');
require_once( FNC.'/functions.plugins.php' );
require_once( FNC.'/functions.html.php' );
require_once( FNC.'/functions.global.php' );
require_once( FNC.'/functions.videoads.php' );
require_once( FNC.'/functions.user.php' );
require_once( FNC.'/functions.kses.php' );
require_once( FNC.'/comments.php' );
// Theme
if( !defined( 'THEME' ) )
	define( 'THEME', get_option('theme','main') );
// Themes directory
if( !defined( 'TPL' ) )
	define( 'TPL', ABSPATH.'/themes/'.THEME);
// Site options
$all_options = get_all_options();
// Global classes
require_once( APP.'/phpvibe/phpvibe.video.php' );
require_once( CNC.'/class.upload.php' );
require_once( CNC.'/class.providers.php' );
require_once( CNC.'/class.pagination.php' );
require_once( CNC.'/class.phpmailer.php' );
require_once( CNC.'/class.images.php' );
require_once( CNC.'/class.youtube.php' );

// Current translation
$trans = init_lang();
// Plugins
if(!is_null(get_option('activePlugins',null))) {
//Plugins array
$Plugins = explode(",",get_option('activePlugins',null));
if(!empty($Plugins) && is_array($Plugins)){
// Plugins loop
foreach ($Plugins as $plugin) {
if(file_exists(plugin_inc($plugin))) { include_once(plugin_inc($plugin)); }
}
}
}
//Facebook API Login
define( 'Fb_Key', get_option('Fb_Key') );
define( 'Fb_Secret', get_option('Fb_Secret'));
// OnSite Login
define('COOKIEKEY', get_option('COOKIEKEY', md5(site_url())) );
define('SECRETSALT', get_option('SECRETSALT', substr(md5(site_url()), -16) ));
define( 'COOKIESPLIT', get_option('COOKIESPLIT','--') );
// Cookie logins
authByCookie();
validate_session();
if(is_user()) {$killcache = true;}
?>