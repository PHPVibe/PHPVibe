<?php $pid = token_id();

$_post = $db->get_row("select * from ".DB_PREFIX."posts where pid = '".$pid."'");

if($_post) {

// SEO Filters
function modify_title( $text ) {
global $_post;
 return get_option('seo-post-pre','')._cut(strip_tags(stripslashes($_post->title)), 260).get_option('seo-post-post','');
}
function modify_desc( $text ) {
global $_post;
    return _cut(strip_tags(stripslashes($_post->content)), 160);
}
add_filter( 'phpvibe_title', 'modify_title' );
add_filter( 'phpvibe_desc', 'modify_desc' );
//Time for design
 the_header();
include_once(TPL.'/blog-post.php');
the_footer();
} else {
//Oups, not found
layout('404');
}

?>
