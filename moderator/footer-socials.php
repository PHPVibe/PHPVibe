<?php if(isset($_GET['ac']) && $_GET['ac'] ="remove-logo"){
update_option('site-logo', '');
 $db->clean_cache();
}
if(isset($_POST['update_options_now'])){
foreach($_POST as $key=>$value)
{
if($key !== "site-logo") {
  update_option($key, $value);
}
}
 $db->clean_cache();
  echo '<div class="msg-info">Options have been updated.</div>';
}
$all_options = get_all_options();
include_once('setheader.php');
?>
<div class="row">

<div class="row-setts panel-body">
<h3>Social links in footer</h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('footer-socials');?>" enctype="multipart/form-data" method="post">

<input type="hidden" name="update_options_now" class="hide" value="1" />
<?php $aa = array("our_facebook","our_googleplus","our_youtube","our_pinterest","our_twitter","our_rss","our_skype","our_vimeo","our_dribbble","our_flickr","our_linkedin"); 
foreach ($aa as $a) { ?>
<div class="form-group form-material">
<label class="control-label"><i class="icon-link"></i><?php echo ucfirst(str_replace("our_","",$a)); ?></label>
<div class="controls">
<input type="text" name="<?php echo $a; ?>" class="col-md-12" value="<?php echo get_option($a,'#'); ?>" /> 						
</div>	
</div>	
<?php } ?>	
<div class="row page-footer">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update settings"); ?></button>	
</div>					
</form>
</div>
</div>