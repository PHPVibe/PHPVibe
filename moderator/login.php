<?php
if(isset($_POST['update_options_now'])){
foreach($_POST as $key=>$value)
{
update_option($key, toDb($value));
}
  echo '<div class="msg-info">Login options have been updated.</div>';
  $db->clean_cache();
}
$all_options = get_all_options();
include_once('setheader.php');

?>

<div class="row">

<div class="row-setts panel-body">
<h3>Login Settings</h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('login');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="update_options_now" class="hide" value="1" /> 

	<div class="form-group form-material">
	<label class="control-label"><i class="icon-facebook-sign"></i>Allow Facebook logins/registrations </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowfb" class="styled" value="1" <?php if(get_option('allowfb') == 1 ) { echo "checked"; } ?>>Yes</label>
	<label class="radio inline"><input type="radio" name="allowfb" class="styled" value="0" <?php if(get_option('allowfb') == 0 ) { echo "checked"; } ?>>No</label>
	<span class="help-block" id="limit-text">Allow Facebook users to login? Note: It will not work without a valid Key and Secret from your <a href="https://developers.facebook.com/apps" target="_blank">Facebook App</a> </span>
	</div>
	</div>
	<div class="form-group form-material">
<label class="control-label"><i class="icon-key"></i>Facebook login settings</label>
 <div class="controls">
<div class="row">
<div class="col-md-6">
<input type="text" name="Fb_Key" class="col-md-12" value="<?php echo get_option('Fb_Key'); ?>"><span class="help-block">Facebook app <strong>App ID/API Key</strong> </span>
</div>
<div class="col-md-6">
<input type="text" name="Fb_Secret" class="col-md-12" value="<?php echo get_option('Fb_Secret'); ?>"><span class="help-block align-center"> <strong>App Secret</strong></span>
</div>
</div>
</div>
</div>
	
<div class="form-group form-material">
	<label class="control-label"><i class="icon-google-plus-sign"></i>Allow Google logins/registrations </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowg" class="styled" value="1" <?php if(get_option('allowg') == 1 ) { echo "checked"; } ?>>Yes</label>
	<label class="radio inline"><input type="radio" name="allowg" class="styled" value="0" <?php if(get_option('allowg') == 0 ) { echo "checked"; } ?>>No</label>
	<span class="help-block" id="limit-text">Allow Google users to login? Get your developer keys from <a href="https://console.developers.google.com/project" target="_blank">Google</a>. <a href="https://developers.google.com/api-client-library/php/guide/aaa_oauth2_web" target="_blank">More info</a> </span>

	</div>
	</div>
<div class="form-group form-material">
<label class="control-label"><i class="icon-key"></i>Google login settings</label>
 <div class="controls">
<div class="row">
<div class="col-md-6">
<input type="text" name="GClientID" class="col-md-12" value="<?php echo get_option('GClientID'); ?>"><span class="help-block align-center"> <strong>Client ID</strong></span>
</div>
<div class="col-md-6">
<input type="text" name="GClientSecret" class="col-md-12" value="<?php echo get_option('GClientSecret'); ?>"><span class="help-block align-center"> <strong>Client Secret</strong></span>
</div>

</div>
</div>
</div>
	
		<div class="form-group form-material">
	<label class="control-label"><i class="icon-pencil"></i>Allow local (mail & password) registrations </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowlocalreg" class="styled" value="1" <?php if(get_option('allowlocalreg') == 1 ) { echo "checked"; } ?>>Yes</label>
	<label class="radio inline"><input type="radio" name="allowlocalreg" class="styled" value="0" <?php if(get_option('allowlocalreg') == 0 ) { echo "checked"; } ?>>No</label>
	</div>
	</div>
	<div class="row"> 
<div class="form-group form-material">
<label class="control-label"><i class="icon-key"></i>Google Recaptcha Key</label>
<div class="row"> 
<div class="controls col-md-6 col-xs-12">
<input type="text" name="recaptcha-sk" class="" value="<?php echo get_option('recaptcha-sk'); ?>" /> 
<span class="help-block" id="limit-text">Site key</span>						
</div>	
<div class="controls col-md-6 col-xs-12">
<input type="text" name="recap-secret" class="" value="<?php echo get_option('recap-secret'); ?>" /> 
<span class="help-block" id="limit-text">Secret key</span>						
</div>
</div>
</div>
</div>

		<div class="form-group form-material">
<h3>Security data</h3>
<label class="control-label">Cookies</label>
 <div class="controls">
<div class="row">
<div class="col-md-4">
<input type="text" name="COOKIEKEY" class="col-md-12" value="<?php echo get_option('COOKIEKEY'); ?>"><span class="help-block">Custom  <strong>cookie key</strong>. Name of your set cookie </span>
</div>
<div class="col-md-4">
<input type="text" name="SECRETSALT" class="col-md-12" value="<?php echo get_option('SECRETSALT'); ?>"><span class="help-block align-center"> Secret encryption <strong>salt</strong> For hashing cookies, random characters composing a string of sizes 16, 24 or 32</span>
</div>
<div class="col-md-4">
<input type="text" name="COOKIESPLIT" class="col-md-12" value="<?php echo get_option('COOKIESPLIT'); ?>"><span class="help-block align-center">Cookie <strong>split</strong> (Unique, hard to reproduce by common letter combinations, string to split encripted strings, ex: ###, $$$)</span>
</div>
</div>
</div>
</div>
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-lock"></i>Use also a pin for the admin panel? </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="UseAdPin" class="styled" value="1" <?php if(get_option('UseAdPin') == 1 ) { echo "checked"; } ?>>Yes</label>
	<label class="radio inline"><input type="radio" name="UseAdPin" class="styled" value="0" <?php if(get_option('UseAdPin') <> 1 ) { echo "checked"; } ?>>No</label>
	</div>
	</div>
<div class="form-group form-material">
<label class="control-label">Admin <strong>pin code</strong></label>
 <div class="controls">
<div class="row">
<div class="col-md-1">
<input type="number" name="PINA1" min="0" max="99" class="col-md-12 form-control" value="<?php echo get_option('PINA1',1); ?>">
</div>
<div class="col-md-1">
<input type="number" name="PINA2" min="0" max="99" class="col-md-12 form-control" value="<?php echo get_option('PINA2',2); ?>">
</div>
<div class="col-md-1">
<input type="number" name="PINA3" min="0" max="99" class="col-md-12 form-control" value="<?php echo get_option('PINA3',3); ?>">
</div>
<div class="col-md-1">
<input type="number" name="PINA4"min="0"  max="99" class="col-md-12 form-control" value="<?php echo get_option('PINA4',4); ?>">
</div>
</div>
<span class="help-block" id="limit-text">You'll need this to access the admin panel.</span>
</div>
</div>
<div class="row page-footer">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update settings"); ?></button>	
</div>	
</fieldset>						
</form>
</div>
</div>