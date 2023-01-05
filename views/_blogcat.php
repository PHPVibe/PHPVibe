<?php $id = token_id();
$ch = $db->get_row("SELECT cat_name FROM ".DB_PREFIX."postcats where cat_id ='".$id."'");
if($ch) {
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."posts where ch= '".token_id()."'");
$vq = "Select * FROM ".DB_PREFIX."posts where ch= '".token_id()."' order by pid DESC ".this_limit();
$heading = _html($ch->cat_name);
// Canonical url
$canonical = bc_url($id , $ch->cat_name);   
//Pagination 
$pagestructure = $canonical.'?p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
// SEO Filters
function modify_title( $text ) {
global $heading;
    return strip_tags(stripslashes($heading));
}
add_filter( 'phpvibe_title', 'modify_title' );
//Time for design
 the_header();
include_once(TPL.'/blog.php');
 the_footer(); 
} else {
//Oups, not found
layout('404');
}
?>