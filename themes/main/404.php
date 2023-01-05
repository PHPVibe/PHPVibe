<?php  header("Status: 404 Not Found");
       the_header();
       the_sidebar();
?>
<div class="error-info 404-page 404-warning main-holder pad-holder col-md-12 nomargin">
    <h3>
        <?php echo _lang("404"); ?>
    </h3>
    <h4>
        <?php echo _lang("oops! page not found"); ?>
    </h4>
    <a class="button-center" href="<?php echo site_url(); ?>">
        <?php echo _lang("Back to home"); ?>
    </a>
</div>
<?php  the_footer(); ?>
