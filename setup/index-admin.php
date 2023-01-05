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
<?php $u_check = $db->get_row("SELECT count(*) as nr from ".DB_PREFIX."users where group_id='1'");
if($u_check) {
$checked = $u_check->nr;
} else {
$checked = 0;	
}

if($checked > 0) {
echo "<div class=\"msg-info\">You have ".$checked." administrators so far</div>";	
}	
if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['pass1']) && isset($_POST['pass2'])){
if($_POST['pass1'] == $_POST['pass2']) {
$msg = '<div class="msg-win">All done. Remember to remove the file called "hold" in root.</div>';
$sql = "INSERT INTO ".DB_PREFIX."users (name,email,type,lastlogin,date_registered,group_id,password,avatar)"
 . " VALUES ('" . $db->escape($_POST['name']) . "','" . $db->escape($_POST['email']) . "','core', now(), now(), '1', '".sha1($_POST['pass1'])."', 'storage/uploads/def-avatar.jpg')";
$db->query($sql);
$checked++; 
do_remove_file_now(ABSPATH.'/hold'); 
} else {
$msg = '<div class="msg-warning">Passwords do not match</div>';
}
}


?>
<section class="panel panel-blue">
<div class="panel-heading"><?php if($checked > 0) { echo "<span class=\"label label-primary\">Extra</span> "; } ?>Administrator creation</div>
<form id="validate" class="form-horizontal styled" action="<?php echo $base_href;?>index-admin.php#done" enctype="multipart/form-data" method="post">
<fieldset>
<div class="form-group form-material floating">
<label class="control-label">Administrator's name</label>
<div class="controls">
<input type="text" name="name" class="form-control col-md-12" value="" /> 						
<span class="help-block" id="limit-text">The admin account's name.</span>
</div>	
</div>
<div class="form-group form-material floating">
<label class="control-label">Administrator's email</label>
<div class="controls">
<input type="text" name="email" class="form-control col-md-12" value="" /> 						
<span class="help-block" id="limit-text">Your e-mail adress.</span>
</div>	
</div>		
<div class="form-group form-material floating">
<label class="control-label">Password</label>
<div class="controls">
<div class="row">
<div class="col-md-6">
<input type="password" name="pass1" class="form-control col-md-12" value=""  /> 
<span class="help-block" id="limit-text">Type password</span>
</div>	
<div class="col-md-6">
<input type="password" name="pass2" class="form-control col-md-12" value="" /> 	
<span class="help-block" id="limit-text">Re-type password.</span>
</div>	
</div>					
</div>	
</div>	
<div class="form-group form-material floating">
<button class="btn btn-large btn-primary pull-right" type="submit">Create admin</button>	
</div>	
</fieldset>					
</form>
</section>
<?php
if($checked > 0) { 
echo '<div class="msg-hint">Seems there is already an admin user in the database, so you are pretty much done.</div>';
if(is_readable(ABSPATH.'/hold')){
echo '<section class="panel panel-danger">
<span class="label label-danger">One last thing</span>
<div style="padding:25px 15px;"> Remove the file called "hold" in the root for your website to be online.</div></section>';
}
}
if($checked > 0) {
echo '<section id="done" class="panel panel-blue">
<span class="label label-primary">Setup is done</span>
<div style="padding:25px 15px;">Head to <a href="'.str_replace("setup",ADMINCP,$base_href).'">/'.ADMINCP.'</a> for the admin panel. <br> Thank you for choosing PHPVibe!';
echo '<div class="form-group form-material floating">
<a class="btn btn-large btn-primary pull-right" href="'.$site_url.ADMINCP.'" target="_blank">Setup is complete. Continue</a>
</div></div></section>';
}

?>

</div>
</div>
</div>
</body>
</html>
<?php

ob_end_flush();
//That's all folks!
?>
