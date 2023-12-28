<?php  error_reporting(0); 
header('Content-Type: application/rss+xml; charset=UTF-8');
require_once( INC.'/rss.class.php' );

//Initialize RSS:
	$rss = new FeedRSS(_html(get_option('site-logo-text')), site_url().'feed/', _html(seo_desc()), current_lang(), _html(get_option('site-copyright')), '', '', '', '', '');
	if(get_option('site-logo')) {
	$rss->AddImage(_html(get_option('site-logo-text')), thumb_fix(get_option('site-logo')), site_url().'feed/', '145', '45', get_option('site-logo-text'));
}
$options = DB_PREFIX."videos.*, ".DB_PREFIX."channels.cat_name as channel_name ,".DB_PREFIX."users.name as owner";
$media = _get("m");
if($media && (intval($media) == 3)) {
$options = DB_PREFIX."images.*, ".DB_PREFIX."channels.cat_name as channel_name ,".DB_PREFIX."users.name as owner";
$vq = "select ".$options.", ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."channels ON ".DB_PREFIX."images.category =".DB_PREFIX."channels.cat_id  LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.views >= 0 and ".DB_PREFIX."images.pub > 0 ORDER BY ".DB_PREFIX."images.id DESC ".this_limit();
}elseif($media && (intval($media) <> 3)) {
$vq = "select ".$options.", ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."channels ON ".DB_PREFIX."videos.category =".DB_PREFIX."channels.cat_id  LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.views >= 0 and ".DB_PREFIX."videos.pub > 0 and ".DB_PREFIX."videos.media ='".intval($media)."' ORDER BY ".DB_PREFIX."videos.id DESC ".this_limit();
} else {
$vq = "select ".$options.", ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."channels ON ".DB_PREFIX."videos.category =".DB_PREFIX."channels.cat_id  LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.views >= 0 and ".DB_PREFIX."videos.pub > 0  ORDER BY ".DB_PREFIX."videos.id DESC ".this_limit();
}
$videos = $db->get_results($vq);
foreach ($videos as $video) {
if(!empty($video->title)) {
if(intval($media) < 3) {	
$rss->AddArticle(_html($video->title), video_url($video->id , $video->title), gmdate(DATE_RSS, strtotime($video->date)), $video->owner, $video->channel_name, channel_url($video->category,$video->channel_name), _cut(_html($video->description),20), _html($video->description));
} else {
$rss->AddArticle(_html($video->title), image_url($video->id , $video->title), gmdate(DATE_RSS, strtotime($video->date)), $video->owner, $video->channel_name, channel_url($video->category,$video->channel_name), _cut(_html($video->description),20), _html($video->description));
}
}
}
	
//Publish RSS:
	$rss->Output();
?>