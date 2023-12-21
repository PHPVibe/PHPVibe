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
$term = isset($_REQUEST['q'])? $_REQUEST['q'] : '';
$term = toDB($term);
$users = $db->get_results("Select id, name from ".DB_PREFIX."users where name like '".$term."' or name like '%".$term."' or name like '%".$term."&' or email like '".$term."' ");

 echo json_encode($users);
//That's all folks!

?>
