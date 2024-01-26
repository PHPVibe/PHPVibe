<?php $id = token_id();
if(intval($id) > 0) {
$channel = $cachedb->get_row("SELECT * FROM ".DB_PREFIX."channels where cat_id = '".$id ."' limit  0,1");
if ($channel) {
$count = $cachedb->get_row("Select count(*) as nr from ".DB_PREFIX."videos where category in (select cat_id from ".DB_PREFIX."channels where cat_id='".$id ."' or child_of = '".$id ."')");
//Subs
$subchannels = $db->get_results("SELECT cat_name, cat_id,picture FROM ".DB_PREFIX."channels where child_of = '".$id ."' limit  0,50");
// Canonical url
$canonical = channel_url($channel->cat_id , $channel->cat_name); 
$typeofC = '';  
if(isset($channel->type) && ($channel->type > 1) ) {$typeofC =$channel->type; }
// SEO Filters
function modify_title( $text ) {
global $channel;
  if ($channel->type <> 3) {
        return strip_tags(stripslashes(get_option('seo-channel-pre', '') . $channel->cat_name . get_option('seo-channel-post', '')));
    } else {
        return strip_tags(stripslashes(get_option('seo-image-channel-pre', '') . $channel->cat_name . get_option('seo-image-channel-post', '')));

    }
}
function modify_desc( $text ) {
global $channel;
    return _cut(strip_tags(stripslashes($channel->cat_desc)), 160);

}
add_filter( 'phpvibe_title', 'modify_title' );
add_filter( 'phpvibe_desc', 'modify_desc' );
/*Now to the actual channel page */
if (!is_ajax_call()) { 
the_header();
the_sidebar();
}
include_once(TPL.'/category.php');
the_footer();
} else {
//Oups, not found
layout('404');
}
} else {
//Oups, not found
layout('404');
}
?>
