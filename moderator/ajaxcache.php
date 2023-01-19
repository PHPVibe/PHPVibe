<?php  error_reporting(E_ALL);

//Vital file include

require_once("../load.php");

ob_start();

// physical path of admin

if( !defined( 'ADM' ) )

	define( 'ADM', ABSPATH.'/'.ADMINCP);

define( 'in_admin', 'true' );

require_once( ADM.'/adm-functions.php' );

require_once( ADM.'/adm-hooks.php' );
$tmp = array();
$cInc = ABSPATH;
if( !defined( 'vSTATIC_FOLD' ) )
define('vSTATIC_FOLD',	'/cache/html/');
require_once( CNC.'/fullcache.php' );	
$debug1 =FullCache::ClearAll();
foreach ($debug1 as $d1) {
$tmp[] = str_replace($cInc,'', $d1);
}
$debug = $db->clean_cache();
foreach ($debug as $d) {
$tmp[] = str_replace($cInc,'', $d);
}
$debug2 = $db->clean_cache(true);
foreach ($debug2 as $d2) {
$tmp[] = str_replace($cInc,'', $d2);;
}
$jdebug = jc_purge();
foreach ($jdebug as $dj) {
$tmp[] = str_replace($cInc,'', $dj);
}
echo '<p>Purged <span class="badge badge-success"> '.count($tmp).' </span> cache files.</p>'; 
ob_end_flush();

//That's all folks!

?>
