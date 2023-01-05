<?php if(isset($_GET['ac']) && $_GET['ac'] ="remove-logo"){
update_option('site-logo', '');
 $db->clean_cache();
}
if(isset($_POST['update_options_now'])){
foreach($_POST as $key=>$value)
{
  update_option($key, $value);
}
  echo '<div class="msg-info">Settings updated.</div>';

  $db->clean_cache();
}
$all_options = get_all_options();
?>

<div class="row">
<h3>Youtube API Settings</h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('ytsetts');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="update_options_now" class="hide" value="1" /> 	
<div class="form-group form-material">
<label class="control-label"><i class="icon-pencil"></i>Youtube key</label>
<div class="controls">
<input type="text" name="youtubekey" class="col-md-12" value="<?php echo get_option('youtubekey'); ?>" /> 						
<span class="help-block" id="limit-text">Your Youtube API  server key. See <a href="https://developers.google.com/youtube/registering_an_application" target="_blank">Google : Register your application</a>. </span>
</div>	
</div>
	
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update settings"); ?></button>	
</div>	
</fieldset>						
</form>
</div>
