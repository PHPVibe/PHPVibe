<?php /* PHPVibe PRO v6's header */
register_style('phpvibe');
if(!is_home() && !is_video()) {
register_style('more');
}
register_style('bootstrap.min');
if(!is_home()) {
register_style('jssocials');
register_style('playerads');	
}
if(!is_video()) {
register_style('owl');
}
register_style('materialicons');
register_style('https://fonts.googleapis.com/css?family=Material+Icons|Noto+Sans|Roboto:300,400,500');
if(not_empty(get_option('rtl_langs',''))) {
//Rtl	
$lg = @explode(",",get_option('rtl_langs'));
if(in_array(current_lang(),$lg)) {	
	register_style('rtl');
}
}
function header_add(){
global $page;
$head = render_styles(0);
$head .= extra_css().'
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script>
if((typeof jQuery == "undefined") || !window.jQuery )
{
   var script = document.createElement("script");
   script.type = "text/javascript";
   script.src = "'.tpl().'styles/js/jquery.js";
   document.getElementsByTagName(\'head\')[0].appendChild(script);
}
var acanceltext = "'._lang("Cancel").'";
var startNextVideo,moveToNext,nextPlayUrl;
</script>
';
$head .=players_js();

$head .= '</head>
<body class="body-'.$page.'">
'.top_nav().'
<div id="wrapper" class="'.wrapper_class().' haside">
<div class="row block page p-'.$page.'">
';
return $head;
}

function meta_add(){
$meta = '<!doctype html> 
<html prefix="og: http://ogp.me/ns#" dir="ltr" lang="en-US">  
<head>  
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>'.seo_title().'</title>
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width,  height=device-height, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<base href="'.site_url().'" />  
<meta name="description" content="'.seo_desc().'">
<meta name="generator" content="PHPVibe" />
<link rel="alternate" type="application/rss+xml" title="'.get_option('site-logo-text').' '._lang('All Media Feed').'" href="'.site_url().'feed/" />
<link rel="alternate" type="application/rss+xml" title="'.get_option('site-logo-text').' '._lang('Video Feed').'" href="'.site_url().'feed?m=1" />
<link rel="alternate" type="application/rss+xml" title="'.get_option('site-logo-text').' '._lang('Music Feed').'" href="'.site_url().'feed?m=2" />
<link rel="alternate" type="application/rss+xml" title="'.get_option('site-logo-text').' '._lang('Images Feed').'" href="'.site_url().'feed?m=3" />
<link rel="canonical" href="'.canonical().'" />
<meta property="og:site_name" content="'.get_option('site-logo-text').'" />
<meta property="fb:app_id" content="'.Fb_Key.'" />
<meta property="og:url" content="'.canonical().'" />
<link rel="apple-touch-icon" sizes="180x180" href="'.site_url().'lib/favicos/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="'.site_url().'lib/favicos/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="'.site_url().'lib/favicos/favicon-16x16.png">
<link rel="manifest" href="'.site_url().'lib/favicos/site.webmanifest">
<link rel="mask-icon" href="'.site_url().'lib/favicos/safari-pinned-tab.svg" color="#5bbad5">
<link rel="shortcut icon" href="'.site_url().'lib/favicos/favicon.ico">
<meta name="msapplication-TileColor" content="#2b5797">
<meta name="msapplication-config" content="'.site_url().'lib/favicos/browserconfig.xml">
<meta name="theme-color" content="#ffffff">
';
if(is_video()) {
global $video,$qualities;
if(isset($video) && $video) {
if(isset($qualities) && !empty($qualities)){
$max = max(array_keys($qualities));	
if(isset($qualities[$max])) {	
	$meta .= '<meta property="og:video" content="'.$qualities[$max].'">';
}
} else {
/* Url source */
	$meta .= '<meta property="og:video" content="'.$video->source.'">';
}
	$meta .= '
		<meta property="og:image" content="'.thumb_fix($video->thumb).'" />
		<meta property="og:description" content="'.seo_desc().'"/>
		<meta property="og:title" content="'._html($video->title).'" />
		<meta property="og:type" content="video.other" />
		<meta itemprop="name" content="'._html($video->title).'">
		<meta itemprop="description" content="'.seo_desc().'">
		<meta itemprop="image" content="'.thumb_fix($video->thumb).'">
		<meta property="video:duration" content="'.$video->duration.'">
	';
}
}
if(is_picture()) {
global $image;
	if(isset($image) && $image) {
	$meta .= ' 
		<meta property="og:image" content="'.thumb_fix(str_replace('localimage','storage/'.get_option('mediafolder','media'),$image->source), false).'" />
		<meta property="og:description" content="'.seo_desc().'"/>
		<meta property="og:title" content="'._html($image->title).'" />
		<meta property="og:type"   content="image.gallery" /> 
		<meta itemprop="name" content="'._html($image->title).'">
		<meta itemprop="description" content="'.seo_desc().'">
		<meta itemprop="image" content="'.thumb_fix(str_replace('localimage','storage/'.get_option('mediafolder','media'),$image->source), false).'">
	';
	}
}
if(com() == profile) {
global $profile;
	if(isset($profile) && $profile) {
	$meta .= '
		<meta property="og:image" content="'.thumb_fix($profile->avatar).'" />
		<meta property="og:description" content="'.seo_desc().'"/>
		<meta property="og:title" content="'._html($profile->name).'" />';
	}
}
return $meta;
}

function top_nav(){
$nav = '';
$nav .= '
<div class="fixed-top">
<div class="header row block">
	<div class="logo-wrapper header-left col-md-3 col-lg-3 col-xs-4">';    
	$nav .= '<a id="show-sidebar" href="javascript:void(0)" title="'._lang('Show sidebar').'"><i class="material-icons">&#xE5D2;</i></a>
	<a href="'.site_url().'" title="" class="logo">'.show_logo().'</a>
	<br style="clear:both;"/>
	</div>		
<div class="header-center col-md-6 col-lg-6 col-lg-offset-1 col-xs-3">';
$nav .= '
<div class="searchWidget">
<form action="" method="get" id="searchform" onsubmit="location.href=\''.site_url().show.'/\' + encodeURIComponent(this.tag.value).replace(/%20/g, \'+\') + \'?type=\' + encodeURIComponent(this.component.value).replace(/%20/g, \'+\'); return false;"';
if(get_option('youtube-suggest',1) > 0) { $nav .= 'autocomplete="off"'; }
$nav .= '> <div class="search-holder">
                    <span class="search-button">
					<button type="submit">
					<i class="material-icons">&#xE8B6;</i>
					</button>
					</span>
					<div class="search-target">
					<a id="switch-search" class="dropdown-toggle"  data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button">
					<i class="icon material-icons explainer">&#xe152</i><i class="icon material-icons">&#xe038</i></a>
					<input type="text" id="switch-com" class="hide" name="component" value ="video">                
					<ul class="dropdown-menu dropdown-left bullet" role="menu">
					<li role="presentation"><a id="s-video" href="javascript:SearchSwitch(\'video\')"><i class="icon material-icons">&#xe039</i>'._lang("Videos and music").'</a></li>
					<li role="presentation"><a id="s-picture" href="javascript:SearchSwitch(\'picture\')"><i class="icon material-icons">&#xE43B;</i>'._lang("Pictures").'</a></li>
					<li role="presentation"><a id="s-channel" href="javascript:SearchSwitch(\'channel\')"><i class="icon material-icons">&#xE55A;</i>'._lang("Channels").'</a></li>
					<li role="presentation"><a id="s-playlist" href="javascript:SearchSwitch(\'playlist\')"><i class="icon material-icons">&#xE05F;</i>'._lang("Playlists").'</a></li>
					</ul>
					</div>
                    <div class="form-control-wrap">
                      <input type="text" class="form-control input-lg empty" name="tag" value ="" placeholder="'._lang("Search media").'">                
                    </div>
                     </div>

				</form>
';
if(get_option('youtube-suggest',1) > 0) {
	$nav .= '<div id="suggest-results"></div> ';
}
	$nav .= '</div><div class="user-quick left20">';
if(get_option('showfirelink','1') == 1 ) {
	$nav .=  '<a class="top-link" href="'.site_url().buzz.'/" title="'._lang('What\'s new').'"><i class="material-icons">&#xE80E;</i></a>';
}
if(get_option('showbuzzlink','1') == 1 ) {
	$nav .=  '<a class="top-link" href="'.list_url(browse).'" title="'._lang('Videos').'"><i class="material-icons">&#xe064;</i></a>';
}
$nav .= '</div></div>
<div class="header-right col-md-3 col-lg-2 col-xs-5 pull-right">
<div class="user-quick">
<a data-target="#search-now" data-toggle="modal" href="javascript:void(0)" class="top-link" id="show-search"><i class="material-icons">&#xE8B6;</i></a>';

if(!is_user()) {
$nav .= ' <a id="uploadNow" data-target="#login-now" data-toggle="modal" href="javascript:void(0)" class="btn btn-upload" title="'._lang("Login to upload").'">	
<i class="material-icons">&#xE2C6;</i> <span class="hidden-xs">'._lang("Share some").'</span>
</a>'; } else {
if((get_option('upmenu') == 1) ||  is_moderator()) {
$nav .= '
<a id="uplBtn" href="'.site_url().share.'" class="btn btn-upload" title="'._lang('Upload or share media').'">	
<i class="material-icons">&#xE2C6;</i> <span class="hidden-xs">'._lang("Share some").'</span>
</a>';	
}
}
if(!is_user()) {
$nav .= '
<a id="openusr" class="btn uav btn-small no-user top-link"  href="javascript:showLogin()"
data-animation="scale-up" role="button" title="'._lang('Login').'">	
<i class="material-icons">account_circle</i>
</a>
</div>';
} else {
$nav .= '
<a id="notifs" href="'.site_url().'dashboard/?sk=activity" class="top-link">
<i class="icon material-icons">notifications</i>
</a>
<a id="openusr" class="btn uav btn-small dropdown-toggle"  data-toggle="dropdown" href="#" aria-expanded="false"
data-animation="scale-up" role="button" title="'._lang('Dashboard').'">	
<img data-name="'.addslashes(user_name()).'" src="'.thumb_fix(user_avatar(), true, 35,35).'" /> 
</a>
<ul class="dropdown-menu dropdown-left" role="menu">
<li role="presentation" class="drop-head">'.group_creative(user_group()).' <a href="'.profile_url(user_id(), user_name()).'"> '.user_name().' </a>
';
if( !is_empty(premium_upto())) {
if (new DateTime() > new DateTime(premium_upto())) {	
$nav .= '<p class="small nomargin"><a href="'.site_url().'payment">'._lang("Premium expired").'</a></p>';	
} 
}
$nav .= '
</li>
<li class="divider" role="presentation"></li>';
if(get_option('allowpremium') == 1 ) {
if( is_empty(premium_upto())) {
$nav .= '<li><a href="'.site_url().'payment"><i class="icon material-icons">&#xE8D0;</i> '._lang("Get Premium").'</a></li>';	
}
}
$nav .= '<li class="my-buzz" role="presentation"><a href="'.site_url().'dashboard/"><i class="icon material-icons">&#xE031;</i> '. _lang('Media Studio').'</a> </li>
<li role="presentation"><a href="'.site_url().'dashboard/?sk=edit"><i class="icon material-icons">&#xE8B8;</i> '._lang("My Settings").'</a></li>
<li role="presentation"> <a href="'.site_url().me.'"> <i class="icon material-icons">&#xE04A;</i> '._lang("My Videos").' </a>       </li>       
<li role="presentation"> <a href="'.site_url().me.'?sk=music"> <i class="icon material-icons">&#xE030;</i> '._lang("My Music").' </a>       </li>       
<li role="presentation"> <a href="'.site_url().me.'?sk=images"> <i class="icon material-icons">&#xE413;</i> '._lang("My Images").' </a>       </li>       

<li class="my-inbox" role="presentation"><a href="'.site_url().'conversation/0/"><i class="icon material-icons">&#xE0C9;</i> '. _lang('Messages').'</a> </li>';
if(is_admin()){
$nav .= '
<li role="presentation"><a href="'.ADMINCP.'"><i class="icon material-icons">&#xE8A4;</i> '._lang("Administration").'</a></li>
';	
}
$nav .= '<li role="presentation" class="drop-footer"><a href="'.site_url().'index.php?action=logout"><i class="icon material-icons">&#xE14C;</i> '._lang("Logout").'</a></li>
</ul>
</div>
';
}
$nav .= '
</div>
</div>
</div>
</div>
';
return $nav;
}