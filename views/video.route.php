<?php $v_id = token_id();
if(_get('nsfw') == 1) { $_SESSION['nsfw'] = 1; }
//Global video weight & height
$width = get_option('video-width');  
$height = get_option('video-height'); 
$embedCode = '';
//Query this video
if(intval($v_id) > 0) { 

if(this_page() > 1) {
//return only comments ;)
echo comments('video_'.$v_id,this_page());
exit();
}
//Get the video
$video = new PHPVibe\Video\SingleVideo($v_id);

if ($video->isvalid()) {
/* It exists */
$is_owner = false;	
$is_liked = false;
$is_disliked = false;
if(is_user()) {
/* Check if current user is the owner */	
if($video->owner() == user_id()){
$is_owner = true;
}
/* Check rating */	
$checkr = $cachedb->get_row("SELECT count(*) as nr, type FROM ".DB_PREFIX."likes WHERE vid = '".$video->id() ."' AND uid ='".user_id()."' order by id desc");
if($checkr->nr > 0) {
/* Got a rating*/
if($checkr->type == "like") {
$is_liked = true;	
} elseif($checkr->type == "dislike") {
$is_disliked = true;	
}	
}
unset($checkr); /* Done job */	
}
// Canonical url
$canonical = video_url($video->id() , $video->title()); 
$origin = 1;
//Check if it's private 
if(($video->ispremium()) && !has_premium()) {
//Premium video
$embedvideo = '<div class="vprocessing">
<div class="vpre">'._lang("This video is for premium users only!").'</div> 
<div class="vex">'._lang("Become a premium user for as low as ").' '.get_option("monthlycurrency", "USD").' '.get_option("monthlyprice", "1").'
<div class="full text-center mtop20"><a class="btn btn-primary" href="'.site_url().'payment">'._lang("Upgrade").'</a></div>
</div>
</div>';	
}elseif(($video->isprivate()) && !im_following($video->owner())) {
//Video is not public
$embedvideo = '<div class="vprocessing">
<div class="vpre">'._lang("This video is for subscribers only!").'</div> 
<div class="vex"><a href="'.profile_url($video->owner(),$video->owner).'">'._lang("Please subscribe to ").' '.$video->owner.' '._lang("to see this video").'</a>
</div>
</div>';

//Check if it's processing
}elseif(!$video->isembed() && !$video->hassource() && !$video->isremote()) {
 $embedvideo = '<div class="vprocessing"><div class="vpre">'._lang("This video is being processed").'</div>
 <div class="vex">'._lang("Please check back in a few minutes.").'</div></div>';
} else {
//See what embed method to use
if($video->isremote()) {
	//Check if video is remote/link
   $vid = new Vibe_Providers($width, $height);    
   $embedvideo = $vid->remotevideo($video->remote());
   $origin = 1;
   } elseif($video->isembed()) {
   //Check if has embed code
	$embedvideo	=  render_video(stripslashes($video->embed()));
	$origin = 2;
   } else {
   //Embed from video providers
   $vid = new Vibe_Providers($width, $height);    
   $embedvideo = $vid->getEmbedCode($video->source());
   $origin = 0;
   }
   // Filter result
   $embedvideo = apply_filters('the_embedded_video' , $embedvideo);
 }
do_action('afterEmbedGeneration'); 
/* Is it NSFW */
 if (nsfilter()) { 
$embedvideo	.='<div class="nsfw-warn"><span>'._lang("This video is not safe").'</span>
<a href="'.$canonical.'?nsfw=1">'.("I understand and I am over 18").'</a><a href="'.site_url().'">'._lang("I am under 18").'</a>
</div>';
} 
/* Load assets for players */
if($video->ismusic()) {
/* Load player for music */	
//VideoJs Waves
add_filter( 'addplayers', 'vjsup' ); 	
add_filter( 'addplayers', 'wavesup' ); 
} else {
/* Load player for videos (uploaded and remote) */	
//JwPlayer
if((get_option('youtube-player') == 2 ) || ((get_option('remote-player',1) == 1) || (get_option('choosen-player', 1) == 1))) {
add_filter( 'addplayers', 'jwplayersup' );
}  
//FlowPlayer
if(((get_option('remote-player',1) == 2) && ($origin == 1)) || (get_option('choosen-player',1) == 2))	{					 
add_filter( 'addplayers', 'flowsup' );  
}
if(((get_option('remote-player',1) == 3) && ($origin == 1)) || (get_option('choosen-player',1) == 3))	{					 
//jPlayer
add_filter( 'addplayers', 'jpsup' );  
}
//VideoJS
if(((get_option('remote-player',1) == 6) && ($origin == 1)) || (get_option('choosen-player',1) == 6)|| (get_option('youtube-player',1) == 3))	{					 
add_filter( 'addplayers', 'vjsup' );  
}
}
// SEO Filters
function modify_title( $text ) {
global $video;
    return strip_tags(_html(get_option('seo-video-pre','').$video->title().get_option('seo-video-post','')));
}
function modify_desc( $text ) {
global $video;
    return _cut(strip_tags(_html($video->description())), 160);
}
add_filter( 'phpvibe_title', 'modify_title' );
add_filter( 'phpvibe_desc', 'modify_desc' );
// Percentages of likes/dis
$likes_percent =  percent($video->likes(), $video->likes() + $video->dislikes());
$dislikes_percent = ($likes_percent > 0 || $video->dislikes() > 0)? 100 - $likes_percent : 0;

//Time for design
 the_header();
include_once(TPL.'/video.php');	 
 the_footer();
 } else {
//Oups, not found
layout('404');
}
}else {
//Oups, not found
layout('404');
}
?>