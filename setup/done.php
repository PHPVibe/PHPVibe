<?php  error_reporting(E_ALL); 
if(!isset($_SESSION['user_id'])) {$_SESSION['user_id'] = 1;}
function glob_recursive(string $baseDir, string $pattern, int $flags = GLOB_NOSORT | GLOB_BRACE)
{
    $paths = glob(rtrim($baseDir, '\/') . DIRECTORY_SEPARATOR . $pattern, $flags);
    if (is_array($paths)) {
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $subPaths = (__FUNCTION__)($path, $pattern, $flags);
                if ($subPaths !== false) {
                    $subPaths = (array) $subPaths;
                    array_push($paths, ...$subPaths);
                }
            }
        }
    }
    return $paths;
}
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
//Vital file include
require_once("../load.php"); 

ob_start(); 

// physical path of admin
if( !defined( 'ADM' ) )
	define( 'ADM', ABSPATH.'/'.ADMINCP); 

define( 'in_admin', 'true' ); 

require_once( ADM.'/adm-functions.php' ); 


	
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

echo '
<!doctype html> 
<html prefix="og: http://ogp.me/ns#"> 
 <html dir="ltr" lang="en-US">  
<head>  
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>PHPVibe :: Setup</title>
<meta charset="UTF-8">  
<link rel="stylesheet" type="text/css" href="'.$base_href.'setup.style.css" media="screen" />
<script type="text/javascript" src="'.$base_href.'jquery.js"></script>
</head>
<body>
<div id="wrapper" class="container">
<div id="content">

'; ?>
<div class="text-center">
<div class="row head-row">
		<div class="logo">
		<img src="logobig.png"><br> 
		<p style="margin:30px 0 0">
		<h1>PHPVibe</h1>
		</p>
		</div>
</div>
<?PHP
$handler = isset($_REQUEST['step']) ? $_REQUEST['step'] : 0 ;

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
	
	if(isset($_POST['update_options_now'])){
		foreach($_POST as $key=>$value)
		{
		if($key == "site-logo-text") {
		  update_option('seo_title', $value);
		}
		update_option($key, $value);
		}
		  echo '<div class="msg-win">You\'re all done! Good luck and enjoy your new website!</div>';
		  $db->clean_cache();
		
$all_options = get_all_options();
?>
<div class="row text-center">
		<div class="col-12 top30 text-center">
		<div class="msg-warning"><h4>Please remove this (/setup) folder!</h4>	</div>
		</div>
		<div class="col-12 top30 text-center">
		<a class="button primary lg" href="<?php echo site_url(); ?>">Finish</a>
		</div>	
		</div>

	<?php } else { 
	
	?>

<form id="validate" class="form-horizontal styled" action="<?php echo $base_href;?>done.php" enctype="multipart/form-data" method="post">

		<input type="hidden" name="update_options_now" class="hide" value="1" />
<div class="row"> 
		<div class="col-12"> 		
				<div class="form-group">
				<label class="control-label">Website Name</label>
					<div class="controls">
						<input type="text" name="site-logo-text" class="col-12" value="<?php echo get_option('site-logo-text'); ?>" /> 	
									
							<span class="help-block" id="limit-text">The site's name! Example: Mira's food casts, Lessons with Mike</span>
							
					</div>	
				</div>
		</div>	
</div>	
<div class="row"> 
		<div class="col-12"> 		
				<div class="form-group">
				<label class="control-label">Admin/Site email</label>
					<div class="controls">
						<input type="text" name="site-email" class="col-12" value="<?php echo get_option('site-email'); ?>" /> 	
									
							<span class="help-block" id="limit-text">Optional but useful</span>
							
					</div>	
				</div>
		</div>	
</div>	
<div class="row"> 
		<div class="col-12"> 		
				<div class="form-group">
				<label class="control-label">Commercial license key</label>
					<div class="controls">
						<input type="text" name="site-commercial-key" class="col-12" value="<?php echo get_option('site-commercial-key'); ?>" /> 	
									
							<span class="help-block" id="limit-text">If you have one!</span>
							
					</div>	
				</div>
		</div>	
</div>	
<div class="flex flex-center">
<button class="button success" type="submit"><?php echo _lang("Save values"); ?></button>	
</div>

</form>
<?php }
	
	?>

</div>	
		</div>
		</div> <br style="clear:both">
			<strong class="grey-text" style="font-weight: 300;">&copy; Copyright 2010 - '.date('Y').' Marius Patrascu, PHPVibe.com</strong>
