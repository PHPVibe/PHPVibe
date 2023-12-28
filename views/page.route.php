<?php $pid = token_id();

$_page = $db->get_row("select * from ".DB_PREFIX."pages where pid = '".$pid."'");

if($_page) {

// SEO Filters
function modify_title( $text ) {
global $_page;
 return get_option('seo-page-pre','')._cut(strip_tags(stripslashes($_page->title)), 260).get_option('seo-page-post','');
}
function modify_desc( $text ) {
global $_page;
    return _cut(strip_tags(stripslashes($_page->content)), 160);
}
add_filter( 'phpvibe_title', 'modify_title' );
add_filter( 'phpvibe_desc', 'modify_desc' );
//Time for design
 the_header();
include_once(TPL.'/page.php');
the_footer();
} else {
//Oups, not found
layout('404');
}

?>
