<?php 
function admin_url($sk = null){
if(is_null($sk)) {
return site_url().ADMINCP.'/';
} else {
return site_url().ADMINCP.'/?sk='.$sk;
}
}
function video_importer_links() {
return apply_filters('importers_menu',false);
}
class PHPVibeVersionHelper
{
    var $list         = null;
    public static $cms = 'PHPVibe';
	function __construct() {
    $string = file_get_contents(ADM."/phpvibe.json");
	$this->list = json_decode($string, false);
	if(!isset($this->list->fullversion)) { exit("ERROR! Missing or broken PHPVibe version file! <br>Please check/reupload the ".ADM."/phpvibe.json file");}
  }
	public function cms() {
		 return (is_empty($this->list->software)? self::$cms : $this->list->software );
    }
    public function fullversion() {
        return $this->list->fullversion;
    }
	public function major() {
        return $this->list->major_version;
    }
	public function subversion() {
        return $this->list->subversion;
    }
	public function state() {
       return $this->list->release_type;
    }
	public function released() {
        return $this->list->release_date;
    }
}
$aboutVibe = new PHPVibeVersionHelper();
/* Usage:
		echo $aboutVibe->fullversion();
		echo $aboutVibe->major();
		echo $aboutVibe->subversion();
		echo $aboutVibe->state();
		echo $aboutVibe->released();
		echo $aboutVibe->cms();
	*/
//filter
function admin_css(){
	global $aboutVibe;
return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>'.$aboutVibe->cms().' #'.$aboutVibe->major().' - Dashboard</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="PHPVibe.com">
	<base href="'.admin_url().'" />  
<link rel="stylesheet" href="'.admin_url().'css/bootstrap.min.css">
	<link rel="stylesheet" href="'.admin_url().'css/responsive.css">
    <link rel="stylesheet" href="'.admin_url().'css/font-awesome.css">
    <link rel="stylesheet" href="'.admin_url().'css/style.css" type="text/css" media="screen" >
	<link rel="stylesheet" href="'.admin_url().'css/plugins.css"/>
		<link rel="stylesheet" href="'.admin_url().'css/chartist.css"/>
	<link rel="stylesheet" href="'.admin_url().'editor/summernote/summernote.css"/>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
	 '.apply_filters("admin_custom_css_links",false).'	
	<style>
	 '.apply_filters("admin_custom_css_raw",false).'	
	</style>
	';
}
	$descrie = array();
	$descrie['videos'] = "Video manager";
	$descrie['upsetts'] = "Upload settings";
	$descrie['setts'] = "Site settings";
function admin_h(){
	global  $descrie;
$head= admin_css().'
	<!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
<script type="text/javascript" src="'.admin_url().'js/jquery.js"></script>

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="'.admin_url().'js/jquery.imagesloaded.min.js"></script>
<script type="text/javascript" src="'.admin_url().'js/jquery.tipsy.js"></script>
<script src="'.admin_url().'js/bootstrap.min.js"></script>
<script type="text/javascript" src="'.admin_url().'js/jquery.validation.js"></script>
<script type="text/javascript" src="'.admin_url().'js/jquery.validationEngine-en.js"></script> 	
<script type="text/javascript" src="'.admin_url().'js/jquery.tagsinput.min.js"></script>	
<script type="text/javascript" src="'.admin_url().'js/jquery.select2.min.js"></script>	
<script type="text/javascript" src="'.admin_url().'js/jquery.listbox.js"></script>	
<script type="text/javascript" src="'.admin_url().'js/jquery.autosize.js"></script>
<script type="text/javascript" src="'.admin_url().'js/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="'.admin_url().'js/jquery.form.js"></script>
<script type="text/javascript" src="'.admin_url().'editor/summernote/summernote.min.js"></script>
<script type="text/javascript" src="'.admin_url().'js/jquery.navgoco.js"></script>
<script type="text/javascript" src="'.admin_url().'js/highlight.pack.js"></script>
<script type="text/javascript" src="'.admin_url().'js/jquery-labelauty.js"></script>
<script type="text/javascript" src="'.admin_url().'js/jquery.autocomplete.min.js"></script>
<script type="text/javascript" src="'.admin_url().'js/chartist.min.js"></script>
<script type="text/javascript" src="'.admin_url().'js/snackbar.js"></script>
<script type="text/javascript" src="'.admin_url().'js/phpvibe.js"></script>
 '.apply_filters("admin_custom_js_links",false).'	

<script type="text/javascript"> 
hljs.initHighlightingOnLoad();
var admin__url  = "'.admin_url().'"; 
var admin_url  = "'.admin_url().'";
$(document).ready(function() {
$(".ckeditor").summernote();
'.apply_filters("admin_custom_jqreadyjs_raw",false).'
});

 '.apply_filters("admin_custom_js_raw",false).'	
</script>
';
$clx = '';
$head .= '</head>
   <div id="wrap">
   <div class="container-fluid page '.$clx.'">
';


$head .=  '<div id="header" class="hide"> 
  <div class="full">
  
	<div class="searchWidget full">
				<form action="" method="get" onsubmit="location.href=\''.admin_url().'?sk=search-videos&key=\' + encodeURIComponent(this.key.value); return false;" id="searchform">
				<div class="form-group">
                  <div class="input-search ">  
						<button type="submit" class="input-search-btn">
							<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
						</button>				  
                      <input type="text" id="autocompletesearch" placeholder="Search for media or people" class="form-control empty" name="key" value ="">                         
                  </div>
                </div>
				</form>
		</div>
<div id="suggest-results">
</div>

   </div>
   <div class="ajax-form-result hide"></div>
   </div>
'.adm_sidebar();
return $head;
}
add_filter('adm_head', 'admin_h');
//common
function admin_head () {
echo apply_filters('adm_head', false);
}
add_action('ahead','admin_head', 1);
function admin_header() {
do_action('ahead');
}
function add_active($sub) {
$a = _get('sk');
$c = explode(",",$sub);
if(!in_array($a, $c)) {
return '';
} else {
return 'in';
}
}
//style
function adm_sidebar(){
$sb = '
<div class="left-navbars">
<div class="short-navbar">
<div class="navbar-top">
<div class="navbar-top-logo">
<img src="'.admin_url().'css/logo.png" title="Administration panel for PHPVibe"/>
</div>
<a class="toggle-btn" href="javascript:void(0);"><i class="material-icons">&#xE5D2;</i></a>
<a class="searchit-btn" href="javascript:void(0);"><i class="material-icons">search</i></a>
</div>
<div class="navbar-jump">
<a class="menu__jumps tipW" href="'.admin_url().'" title="Admin Dashboard"><i class="material-icons">home</i></a>
<a class="menu__jumps tipW" href="'.admin_url("clean-cache").'" title="Purge cache"><i class="material-icons">sync</i></a>
<a class="menu__jumps tipW" href="'.admin_url("videos").'" title="Manager"><i class="material-icons">view_carousel</i></a>
<a class="menu__jumps tipW" href="'.admin_url('add-video').'" title="Add video"><i class="material-icons">add_to_queue</i></a>
<a class="menu__jumps tipW" href="'.site_url().'" title="Exit to website"><i class="material-icons">power_settings_new</i></a>
</div>

</div>
<div class="navbar admin-sidebar">
<div class="logo-content">'.show_logo().'</div>
<div class="sidescroll">
          <div class="sidebar-nav blc">
		  <h1>Admin</h1>
		  <h2>Hi '.user_name().'!</h2>
		  <ul>		
	    '.apply_filters("before_admin_menu",false).'
		 <li class="LiHead"> <a href="#"><i class="material-icons">&#xE1AD;</i> Settings</a>
                   <ul>
                     <li><a href="'.admin_url('setts').'"><div class="lidot"></div> Global options</a></li>
					
					 <li><a href="'.admin_url('upsetts').'"><div class="lidot"></div> Uploading</a>
                    <li><a href="'.admin_url('players').'"><div class="lidot"></div> Players</a>
                    <li><a href="'.admin_url('ffmpeg').'"><div class="lidot"></div> Ffmpeg conversion </a>
                    <li><a href="'.admin_url('login').'"><div class="lidot"></div> Login and registration</a>
					 <li><a href="'.admin_url('seo').'"><div class="lidot"></div> SEO  </a>
					 <li><a href="'.admin_url('sef').'"><div class="lidot"></div> Permalinks</a>                     
                     <li><a href="'.admin_url('homepage').'"><div class="lidot"></div> Frontpage</a></li>					 
					<li><a href="'.admin_url('footer-socials').'"><div class="lidot"></div> Socials</a></li>
 
					'.apply_filters("configuration_menu",false).'
					</ul>
				</li>	
		 '.apply_filters("before_plugins_menu",false).'		
       <li class="LiHead"><a href="'.admin_url('plugins').'"><i class="material-icons">system_update_alt</i> Plugins</a></li>
					 <li class="LiHead"><a href="'.admin_url('langs').'"><i class="material-icons">language</i> Languages</a></li>
               <li class="LiHead"><a href="#"><i class="material-icons">video_library</i> Media library</a>
                  
                    <ul>
                     <li> <a  href="'.admin_url('videos').'"><div class="lidot"></div> Videos</a></li>
                      <li><a  href="'.admin_url('images').'"><div class="lidot"></div> Pictures</a></li>
					  <li><a  href="'.admin_url('music').'"><div class="lidot"></div> Music</a></li>	
                 <li><a href="'.admin_url('playlists').'"><div class="lidot"></div> Collections</a>
				 <li><a href="'.admin_url('channels').'"><div class="lidot"></div> Categories</a>
                    
					  <li><a href="'.admin_url('comments').'"><div class="lidot"></div> Comments</a>
                  </li>
					<li class="nav-split">-</li>  
					<li> <a  href="'.admin_url('unvideos').'"><div class="lidot"></div> Unpublished media</a></li>
					<li> <a  href="'.admin_url('unimages').'"><div class="lidot"></div> Unpublished images</a></li>
					<li> <a  href="'.admin_url('rawmedia').'"><div class="lidot"></div> Source files (raw)</a></li>
					</ul>
                  </li>
                <li class="LiHead">
                    <a href="#"><i class="material-icons">add_box</i> Add media</a>
                    <ul>
					'.apply_filters("pre-importers_menu",false).'
                     '.video_importer_links().'
					 '.apply_filters("post-importers_menu",false).'
					  <li> <a  href="'.admin_url('add-video').'"><div class="lidot"></div> Add remote video</a></li>
            		<li>  <a  href="'.admin_url('add-by-iframe').'"><div class="lidot"></div> Add embed code</a></li>
                    
      
                    </ul>
                  </li>
                               
               '.apply_filters("midd_menu",false).'
               <li class="LiHead">
                    <a  href="#"><i class="material-icons">sports_kabaddi</i> Community</a>
                  <ul>
				  <li><a href="'.admin_url('users').'"><div class="lidot"></div> Users</a>
				  <li><a href="'.admin_url('create-user').'"><div class="lidot"></div> Create user</a>
				  <li><a href="'.admin_url('usergroups').'"><div class="lidot"></div> Usergroups</a>
				   <li><a href="'.admin_url('activity').'"><div class="lidot"></div> Activity</a>
					<li class="nav-split">-</li>  
                    <li><a href="'.admin_url('users').'&sort=active"><div class="lidot"></div> Active users</a> 
                    <li><a href="'.admin_url('users').'&sort=innactive"><div class="lidot"></div> Inactive users</a>             
                   </ul>
                  </li>
                <li class="LiHead">
                    <a href="#"><i class="material-icons">import_contacts</i> Text pages</a>
                    <ul>
                    <li><a href="'.admin_url('posts').'"><div class="lidot"></div> Articles</a> 
                    <li><a href="'.admin_url('pch').'"><div class="lidot"></div> Categories</a> 									         
                    <li class="nav-split">-</li>  				  		  
                    <li><a href="'.admin_url('pages').'"><div class="lidot"></div> Pages</a> 
                         
                 
				   </ul>
                  </li>
				  

                    

				<li class="LiHead">
                    <a  href="#"><i class="material-symbols-outlined">ads_click</i>Ads & money </a>
                  <ul>
					<li><a href="'.admin_url('videoads').'"><div class="lidot"></div> Player overlays</a>
				
                    <li><a href="'.admin_url('ads').'"><div class="lidot"></div> Static advertising</a> 
					
				  
                     		'.apply_filters('filter-ads-menu',false).'		  
							<li class="nav-split">-</li> 
							<li><a href="'.admin_url('subscriptions').'"><div class="lidot"></div>Premium subscriptions</a></li>
                   </ul>
                  </li>
         
                 <li class="LiHead"><a href="'.admin_url('reports').'"><i class="material-symbols-outlined">flag_circle</i> Reports</a>
                  </li>
                  </li>
				   <li class="LiHead"><a href="'.admin_url('alog').'"><i class="material-icons">fact_check</i> Logs</a>
                  </li>
				
				'.apply_filters("end_menu",false).'
				<li class="LiHead"><a  href="#"><i class="material-icons">circle</i> More</a>
                 
                    <ul>
                      '.tools_menu().'                     
					 </ul>
                  </li>
               
        </div>
		<div class="somespace30">
		<div id="purgeresult"></div>
		<a id="purgecache" class="btn btn-primary btn-block btninnav" href="'.admin_url().'ajaxcache.php"><i class="material-icons mright10">published_with_changes</i>Refresh CACHE</a>
		</div>
   </div>
 </div>
 </div>
';
return $sb;
}

function tools_menu() {
return apply_filters('filter-tools-menu',false);
}
function support_links($tools){
return $tools.'
<li><a href="'.admin_url('integrity').'"><div class="lidot"></div>Folder integrity check</a></li>
<li><a href="'.admin_url('options').'"><div class="lidot"></div>Current Options </a></li>
<li><a target="_blank" href="https://phpvibe.com"><div class="lidot"></div>Get help</a></li>                  
';
}
add_filter('filter-tools-menu','support_links');

function count_uvid($u){
global $db;
$sub = $db->get_row("Select count(*) as nr from ".DB_PREFIX."videos where user_id ='".$u."'");
return $sub->nr;
}
function count_uimgs($u){
global $db;
$sub = $db->get_row("Select count(*) as nr from ".DB_PREFIX."images where user_id ='".$u."'");
return $sub->nr;
}
function count_uact($u){
global $db;
$sub = $db->get_row("Select count(*) as nr from ".DB_PREFIX."activity where user ='".$u."'");
return $sub->nr;
}
function delete_activity_by_video($id){
global $db;
$db->query("delete from ".DB_PREFIX."activity where object ='".$id."'");
}
function delete_user($id){
global $db;
$user = $db->get_row("select id,name,avatar from ".DB_PREFIX."users where id = ".$id." and group_id > 1");
if($user){
//remove avatar
if($user->avatar){
$thumb = $user->avatar;
if($thumb && ($thumb != "storage/uploads/noimage.png") && ($thumb != "media/thumbs/xmp3.jpg")) {
$vurl = parse_url(trim($thumb, '/')); 
if(!isset($vurl['scheme']) || $vurl['scheme'] !== 'http'){ 
$thumb = ABSPATH.'/'.$thumb;
//remove avatar file
 remove_file($thumb);
 }
}
}
//remove videos
$videos = $db->get_results("Select id,token,source from ".DB_PREFIX."videos where user_id ='".$id."' limit 0,10000000");
if($videos) {
foreach ($videos as $re) {
if($re->id == "up") {
/* Get list of video files attached */
$pattern = "{*".$re->token."*}";
$folder = ABSPATH.'/storage/'.get_option('mediafolder','media').'/';
$vl = glob($folder.$pattern, GLOB_BRACE);
foreach($vl as $videocheck) {
remove_file($videocheck);
}
}	
delete_video($re->id);
delete_activity_by_video($re->id);
}
}
//remove likes
$likes = $db->get_results("Select vid from ".DB_PREFIX."likes where uid ='".$id."' limit 0,10000000");
if($likes){
foreach ($likes as $lre) {
unlike_video($lre->vid, $id);
}
}
//remove friendships
$db->query("delete from ".DB_PREFIX."users_friends where uid ='".$id."' or fid='".$id."'");
//remove comments
$db->query("delete from ".DB_PREFIX."em_comments where sender_id ='".$id."'");
//remove playlists
$play = $db->get_results("Select id from ".DB_PREFIX."playlists where owner ='".$id."' limit 0,10000000");
if($play){
foreach ($play as $pl) {
delete_playlist($pl->id);
}
}
//remove activity 
$db->query("delete from ".DB_PREFIX."activity where user ='".$id."'");
//finally remove user
$db->query("delete from ".DB_PREFIX."users where id ='".$id."'");
echo '<div class="msg-info">User '.$user->name.' deleted.</div>';
} else {
echo '<div class="msg-warning">User with id #'.$id.' does not exist.</div>';
}
}
function acjs(){
$txt = '>a/<>llams/<)1102 dehsilbatsE(>llams< ebiVPHP>"SMC oediV ebiVPHP"=eltit "knalb_"=tegrat "moc.ebivphp.www//:sptth"=ferh a<';
echo strrev($txt);
}
function delete_cron($id) {
global $db;
$db->query("delete from ".DB_PREFIX."crons where cron_id ='".$id."'");
}
function add_cron($args = array(), $title = null) {
global $db;
unset($args["sk"]);
unset($args["docreate"]);
unset($args["p"]);
$value = maybe_serialize($args);
$type = escape($args["type"]);
if(is_null($title)) {
$name = ucfirst($type).' - '.ucfirst($args["q"]).' - '.date('l jS \of F');
} else {
$name = escape($title);
}
$db->query( "INSERT INTO  ".DB_PREFIX."crons (`cron_type`, `cron_name`, `cron_value`) VALUES ('$type','$name', '$value')" );
echo '<div class="msg-info">Cron '.$name.' created .</div>';
}
function cron_fastest($new) {
$old = get_option('cron_interval');
if($old > $new ) {
update_option('cron_interval', $new);
}
}

add_action('admin-footer-scr-s','acjs');

function FileSizeConvert($bytes)
{
	$result = 0;
    $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}

 function getpb($url)
      {
          $ch      = curl_init();
          $timeout = 15;
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
          $data = curl_exec($ch);
          curl_close($ch);
          return $data;
      }

?>