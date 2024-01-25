<?php /* PHPVibe's header */
if(!is_home()) {
    register_style('jssocials');
}

/* register_style('roboto'); */
if(_get('darkmode')) {
    $_SESSION['darkmode'] = _get('darkmode');
}
if(isset( $_SESSION['darkmode']) && ( $_SESSION['darkmode'] == 1) ) {
    register_style('dark');
}
register_style('https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700');

if(not_empty(get_option('rtl_langs',''))) {
    //Rtl
    $lg = @explode(",",get_option('rtl_langs'));
    if(in_array(current_lang(),$lg)) {
        register_style('rtl');
    }
}
if(!is_video()) {
    register_style('owl');
}
function header_add(){
    global $page;
    $head = render_styles(0);
    $head .= extra_css().'
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

<script>
if((typeof jQuery == "undefined") || !window.jQuery )
{
   var script = document.createElement("script");
   script.type = "text/javascript";
   script.src = "'.tpl().'scripts/jquery.min.js";
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
<link rel="apple-touch-icon" sizes="180x180" href="'.site_url().applibrary().'/favicos/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="'.site_url().applibrary().'/favicos/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="'.site_url().applibrary().'/favicos/favicon-16x16.png">
<link rel="manifest" href="'.site_url().applibrary().'/favicos/site.webmanifest">
<link rel="mask-icon" href="'.site_url().applibrary().'/favicos/safari-pinned-tab.svg" color="#5bbad5">
<link rel="shortcut icon" href="'.site_url().applibrary().'/favicos/favicon.ico">
<meta name="msapplication-TileColor" content="#2b5797">
<meta name="msapplication-config" content="'.site_url().applibrary().'/favicos/browserconfig.xml">
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
                $meta .= '<meta property="og:video" content="'.$video->source().'">';
            }
            $meta .= '
		<meta property="og:image" content="'.$video->thumb().'" />
		<meta property="og:description" content="'.seo_desc().'"/>
		<meta property="og:title" content="'.$video->title().'" />
		<meta property="og:type" content="video.other" />
		<meta itemprop="name" content="'.$video->title().'">
		<meta itemprop="description" content="'.seo_desc().'">
		<meta itemprop="image" content="'.$video->thumb().'">
		<meta property="video:duration" content="'.$video->seconds().'">
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
<header class="top-header">
	<div class="logo-wrapper header-left">';
	$nav .= '<a id="show-sidebar" href="javascript:void(0)" title="'._lang('Show sidebar').'"><i class="material-icons">&#xE5D2;</i></a>
	<a href="'.site_url().'" title="" class="logo">'.show_logo().'</a>
	<div class="user-express">
	<div class="user-quick">';
	if(is_user()) {
		$nav .= '
		<a id="openusr" class="top-link uav dropdown-toggle"  data-toggle="dropdown" href="#" aria-expanded="false"
	data-animation="scale-up" role="button" title="'._lang('Dashboard').'">
	<img data-name="'.addslashes(user_name()).'" src="'.thumb_fix(user_avatar(), true, 35,35).'" />
	</a>
		<a id="notifs" href="'.site_url().'dashboard/?sk=activity" class="top-link"><i class="icon material-icons">notifications</i></a>
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
	<li class="my-inbox" role="presentation"><a href="'.site_url().'conversation/0/"><i class="icon material-icons">&#xE0C9;</i> '. _lang('Messages').'</a> </li>';
        if(is_admin()){
            $nav .= '
	<li role="presentation"><a href="'.ADMINCP.'"><i class="icon material-icons">&#xE8A4;</i> '._lang("Administration").'</a></li>
	';
        }
        $nav .= '<li role="presentation" class="drop-footer"><a href="'.site_url().'index.php?action=logout"><i class="icon material-icons">&#xE14C;</i> '._lang("Logout").'</a></li>
	</ul>

	';
	} else {
/* Show guest */
        $nav .= ' <a id="openusr" class=" hidden-xs uav no-user top-link"  href="javascript:showLogin()"
	data-animation="scale-up" role="button" title="'._lang('Login').'">
	<i class="material-icons">account_circle</i>
	</a>
	<a id="uploadNow" data-target="#login-now" data-toggle="modal" href="javascript:void(0)" class="hidden-xs top-link btn-upload" title="'._lang("Login to upload").'">
	<i class="material-icons">&#xE2C6;</i>
	</a>
	<a href="javascript:showLogin()" class="btn btn-primary btn-small btn-block">'._lang("Join").'</a>
	';
	}
    //if(_contains(canonical(), '?')) { $darklink = canonical().'&darkmode='; } else {$darklink = canonical().'?darkmode=';}
    $darklink = canonical().'?darkmode=';
    if(isset($_SESSION['darkmode']) &&  ($_SESSION['darkmode']== 1)) {

        $nav .= '<a href="'.$darklink.'2" class="top-link">
<svg id="theme-toggle-light-icon" class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0-11a1 1 0 0 0 1-1V1a1 1 0 0 0-2 0v2a1 1 0 0 0 1 1Zm0 12a1 1 0 0 0-1 1v2a1 1 0 1 0 2 0v-2a1 1 0 0 0-1-1ZM4.343 5.757a1 1 0 0 0 1.414-1.414L4.343 2.929a1 1 0 0 0-1.414 1.414l1.414 1.414Zm11.314 8.486a1 1 0 0 0-1.414 1.414l1.414 1.414a1 1 0 0 0 1.414-1.414l-1.414-1.414ZM4 10a1 1 0 0 0-1-1H1a1 1 0 0 0 0 2h2a1 1 0 0 0 1-1Zm15-1h-2a1 1 0 1 0 0 2h2a1 1 0 0 0 0-2ZM4.343 14.243l-1.414 1.414a1 1 0 1 0 1.414 1.414l1.414-1.414a1 1 0 0 0-1.414-1.414ZM14.95 6.05a1 1 0 0 0 .707-.293l1.414-1.414a1 1 0 1 0-1.414-1.414l-1.414 1.414a1 1 0 0 0 .707 1.707Z"></path>
        </svg>
        </a> ';

    } else {
        $nav .= '<a href="'.$darklink.'1" class="top-link">
 <svg id="theme-toggle-dark-icon" class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
          <path d="M17.8 13.75a1 1 0 0 0-.859-.5A7.488 7.488 0 0 1 10.52 2a1 1 0 0 0 0-.969A1.035 1.035 0 0 0 9.687.5h-.113a9.5 9.5 0 1 0 8.222 14.247 1 1 0 0 0 .004-.997Z"></path>
        </svg>
        </a> ';
    }
    $nav .= ' 
	<a data-target="#search-now" data-toggle="modal" href="javascript:void(0)" class="top-link" id="show-search"><i class="material-icons">&#xE8B6;</i></a>
	</div>

	</div>
	</div>
<div class="header-center">
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
					<i class="icon material-icons explainer">&#xe152</i><i class="icon material-icons">&#xe04b</i></a>
					<input type="text" id="switch-com" class="hide" name="component" value ="video">
					<ul class="dropdown-menu dropdown-left bullet" role="menu">
					<li role="presentation"><a id="s-video" href="javascript:SearchSwitch(\'video\')"><i class="icon material-icons">&#xe04b</i>'._lang("Videos and music").'</a></li>
					<li role="presentation"><a id="s-picture" href="javascript:SearchSwitch(\'picture\')"><i class="icon material-icons">&#xe3f4;</i>'._lang("Pictures").'</a></li>
					<li role="presentation"><a id="s-channel" href="javascript:SearchSwitch(\'channel\')"><i class="icon material-icons">&#xe7fd;</i>'._lang("Channels").'</a></li>
					<li role="presentation"><a id="s-playlist" href="javascript:SearchSwitch(\'playlist\')"><i class="icon material-icons">&#xE431;</i>'._lang("Collections").'</a></li>
					</ul>
					</div>
                    <div class="form-control-wrap">
                      <input type="text" class="form-control input-lg empty" name="tag" value ="" placeholder="'._lang("Enjoy something").'">
                    </div>
                     </div>

				</form>
';
    if(get_option('youtube-suggest',1) > 0) {
        $nav .= '<div id="suggest-results"></div> ';
    }

    $nav .= '</div></div>
<div class="header-right">
<div class="user-quick">';
    $topsep = false;
    if(get_option('showfirelink','1') == 1 ) {
        $nav .=  '<a class="top-link" href="'.site_url().buzz.'/" title="'._lang('What\'s new').'"><i class="material-icons">&#xE80E;</i></a>';
        $topsep = true;
    }
    if(get_option('showbuzzlink','1') == 1 ) {
        $nav .=  '<a class="top-link" href="'.list_url(browse).'" title="'._lang('Videos').'"><i class="material-icons">&#xe064;</i></a>';
        $topsep = true;
    }
    if($topsep) {
        $nav .=  '<span class="top-separator"></span>';
    }




	if(is_user()) {
        if((get_option('upmenu') == 1) ||  is_moderator()) {
            $nav .= '
	<a id="uplBtn" href="'.site_url().share.'" class="btn-upload-now" title="'._lang('Upload or share').'">
	<i class="material-icons">&#xE2C6;</i>
	</a>';
        }
	}

$nav .= '</div>';
$nav .= '
</div>
</div>
</header>
</div>
';
    return $nav;
}
