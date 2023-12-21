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
/* Alias the old function */if(!function_exists('main_dom')) {	function main_dom($url = null) {	return _domain($url); 
	}}/* Check pins */
if(get_option('UseAdPin') == 1) {
if(_get('sk') != "pin") {
if(is_empty(get_option('PINA1',''))) {add_option('PINA1',1); 
}	
if(is_empty(get_option('PINA2',''))) {add_option('PINA2',2); 
}
if(is_empty(get_option('PINA3',''))) {add_option('PINA3',3); 
}
if(is_empty(get_option('PINA4',''))) { add_option('PINA4',4); 
}
$adpin = get_option('PINA1',1).get_option('PINA2',2).get_option('PINA3',3).get_option('PINA4',4); 
	
if(!isset($_SESSION['admpin']) || ( $_SESSION['admpin'] <> $adpin) ) {redirect(admin_url('pin')); 
}
}}/* End  pins */

if (is_admin()) {  admin_header(); 
  do_action( 'phpviberun'); 
 echo ' <div id="phpvibe-content"> 
<div class="container-fluid"> 
'; 
  apply_filters("admin-pre-body",false); 
   if(_get('sk')) {  /* security */$file = ADM.'/'.str_replace(array("/",":","http","www"),array("","","",""),_get("sk")).'.php'; 
   if(is_readable( $file )) {  require_once($file); 
    } else if(has_action('adm-'._get('sk'))){  do_action('adm-'._get('sk')); 
  	 }else {  echo 'No page <strong>'._get("sk").'.php</strong> found<br />'; 
  echo 'No action <strong>adm-'._get("sk").'</strong> found<br />'; 
   } } else {  require_once( ADM.'/dashboard.php' ); 
   }echo '</div></div>'; 
  echo '</div></div>'; 
  apply_filters("admin-post-body",false); 
  echo '</body></html>'; 
  /*admin wide included functions could go here */ } else {  echo admin_css(); 
  echo '<div id="wrap"> 
 <div class="container" style="text-align:center; 
  padding-top:40px "> 
 <section class="panel panel-danger" style="width:88%"> 
 
  <div class="panel-heading"> 
Not logged as an admin!</div> 
  <div style="padding:25px"> 
Login in the website with your admin account first!  
  <p style="display:block; padding:20px; "> 
 <a href="'.site_url().'login&return='.ADMINCP.'" class="btn btn-primary"> 
Take me to login</a></p> </div>
</section></div></div>'; 
  die(); 
   } 
   echo  '<div class="container-fluid page"> 
   <div class="row-fluid"> 
   <div class="breadcumps row"> <ul>';
	if(isset($_SERVER['HTTP_REFERER'])) {
		$purl_pies = parse_url($_SERVER['HTTP_REFERER']);
		if(isset($purl_pies['query'])) {
		$query_sks = $purl_pies['query'];
		parse_str($query_sks, $query_sk);
			if(isset($query_sk['sk']) && not_empty($query_sk['sk'])){
				$pastest = str_replace("-"," ",$query_sk['sk']);
				
				if(isset($descrie[$pastest])){ $pastest = $descrie[$pastest];  }
					
				echo  '<li class="inline inline-block pull-right"> 
				<i class="material-icons"> 
				keyboard_return</i> <a href="'.$_SERVER['HTTP_REFERER'].'"> 
				'.ucwords($pastest).'</a></li>';
							}	
				}
		}
echo  '<li class="inline inline-block"> 
		<a href="'.admin_url().'"> <i class="material-icons"> home</i></a> 
		<i class="material-icons breadcumps-pointer"> keyboard_arrow_right</i> 
		</li> ';

if(isset($_GET['sk'])) {
	$curent = trim($_GET['sk']);
	if(isset($descrie[$curent])){ $curent = $descrie[$curent];  }
echo  ' <li class="inline inline-block"> 
 '.ucfirst($curent).'  </li> ';		
}

echo  '</ul></div>
</div>
 <div class="row-fluid text-center"> 
<div class="span2 nomargin" style="padding: 20px"> 
'.show_logo().'</div>'; 
  do_action('admin-footer-scr-s'); 
  echo '</div></div></div>'.apply_filters("admin_custom_footerjs_links",false);
 
ob_end_flush(); 

//That's all folks!
?>