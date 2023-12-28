<?php $heading = _lang("Blog");
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."posts");
$vq = "Select * FROM ".DB_PREFIX."posts order by pid DESC ".this_limit();

// Canonical url
$canonical = site_url().blog;   
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

?>