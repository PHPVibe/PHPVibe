<?php // Canonical url
$canonical = site_url().buzz;   
// SEO Filters
function modify_title( $text ) {
    return strip_tags(stripslashes(user_name( )));
}
add_filter( 'phpvibe_title', 'modify_title' );
//Time for design
 the_header();
include_once(TPL.'/buzz.php');
 the_footer(); 

?>