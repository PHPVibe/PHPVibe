<?php if (!is_ajax_call()) { 
the_header();
the_sidebar();
}
include_once(TPL.'/home.php');
the_footer();
?>