<?php  //Global query options
$options = DB_PREFIX."videos.id,".DB_PREFIX."videos.title,".DB_PREFIX."videos.date,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.duration,".DB_PREFIX."videos.nsfw";
/* Define list to load */
$interval = '';
if(_get('sort'))
{
switch(_get('sort')){
case "w":
$interval = "AND WEEK( DATE ) = WEEK( CURDATE( ) ) ";
break;
case "m":
$interval = "AND MONTH(date) = MONTH(CURDATE( ))";
break;
case "y":
$interval = "AND YEAR( DATE ) = YEAR( CURDATE( ) ) ";
break;
}
}
switch(token()){

case mostliked:
		$heading = ('Liked');	
        $heading_plus = _lang('Most hearted songs');
		$sortop = true;
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.liked > 0 and ".DB_PREFIX."videos.media = '2' ".$interval." ORDER BY ".DB_PREFIX."videos.liked DESC ".this_limit();
		$active = mostliked;
		break;
case mostcom:
		$heading = ('Discussed songs');	
        $heading_plus = _lang('Songs with most comments');
	    $vq = "select ".DB_PREFIX."videos.id,".DB_PREFIX."videos.title,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.duration,".DB_PREFIX."videos.nsfw, ".DB_PREFIX."users.name as owner , count(a.object_id) as cnt FROM ".DB_PREFIX."em_comments a LEFT JOIN ".DB_PREFIX."videos ON a.object_id LIKE CONCAT('%', ".DB_PREFIX."videos.id) LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.liked > 0 and ".DB_PREFIX."videos.media = '2' group by a.object_id order by cnt desc ".this_limit();
		$active = mostcom;
		break;		
case mostviewed:
		$heading = ('Most listened to');	
        $heading_plus = _lang('Most played songs');
        $sortop = true;		
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.views > 0 and ".DB_PREFIX."videos.media = '2' ".$interval." ORDER BY ".DB_PREFIX."videos.views DESC ".this_limit();
		$active = mostviewed;
		break;
case promoted:
		$heading = _lang('Featured');
        $heading_plus = _lang('Songs we\'ve picked for you');
        $sortop = true;		
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.featured = '1' and ".DB_PREFIX."videos.media = '2' ".$interval." ORDER BY ".DB_PREFIX."videos.id DESC ".this_limit();
        $active = promoted;
		break;
default:
		$heading = _lang('New songs');	
        $heading_plus = _lang('Listen to some cool new songs');        
		$vq = "select ".$options.", ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.pub > 0 and ".DB_PREFIX."videos.media = '2' ORDER BY ".DB_PREFIX."videos.id DESC ".this_limit();
        $active = browse;
		break;		
}


// Canonical url
if(_get('sort')) {
$canonical = music_url(token())."&sort="._get('sort'); 
} else {
$canonical = music_url(token()); 
}
// SEO Filters
function modify_title( $text ) {
global $heading;
    return strip_tags(stripslashes($heading));
}
function modify_desc( $text ) {
global $heading_plus;
    return _cut(strip_tags(stripslashes($heading_plus)), 160);
}
add_filter( 'phpvibe_title', 'modify_title' );
add_filter( 'phpvibe_desc', 'modify_desc' );
//Time for design
if (!is_ajax_call()) {  the_header(); the_sidebar(); }
include_once(TPL.'/musiclist.php');
if (!is_ajax_call()) { the_footer(); }
?>