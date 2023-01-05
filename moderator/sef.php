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
  echo '<div class="msg-info">SEF configuration updated.</div>';

  $db->clean_cache();
}
$all_options = get_all_options();
include_once('setheader.php');
?>
<div class="row">

<div class="row-setts panel-body">
<h3>Permalinks </h3>
<p>Named variables:</p>
<p><code>:id</code> - The numeric identifier from db</p>
<p><code>:hid</code> - The hashed id <em>(a la Youtube)</em></p>
<p><code>:name</code> - The title of the resource</p>
<p><code>:section</code> - Extra sections / anything extra</p>
<p>&nbsp;</p>
<p>(Example) This rule: <code>/v_:hid</code> creates urls like <code>/v_Xqd</code> for a video. </p>
<div class="msg-info">Every page requires one of the following: <code>:id</code> or <code>:hid</code> to work.</div>
<h3>Permalinks settings</h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('sef');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="update_options_now" class="hide" value="1" /> 
<div class="form-group form-material">
<label class="control-label"><i class="icon-link"></i>Video & Song permalink</label>
<div class="controls">
<input type="text" name="video-seo-url" class="col-md-12" value="<?php echo get_option('video-seo-url','/video/:id/:name'); ?>" /> 
<span class="help-block" id="limit-text">Default: /video/:id/:name </span>						
</div>	
</div>
<div class="form-group form-material">
<label class="control-label"><i class="icon-link"></i>Image permalink</label>
<div class="controls">
<input type="text" name="image-seo-url" class="col-md-12" value="<?php echo get_option('image-seo-url','/image/:id/:name'); ?>" /> 
<span class="help-block" id="limit-text">Default: /image/:id/:name </span>						
</div>	
</div>	
<div class="form-group form-material">
<label class="control-label"><i class="icon-link"></i>Profile/User Channel permalink</label>
<div class="controls">
<input type="text" name="profile-seo-url" class="col-md-12" value="<?php echo get_option('profile-seo-url','/profile/:name/:id'); ?>" /> 
<span class="help-block" id="limit-text">Default: /profile/:name/:id </span>						
</div>	
</div>	
<div class="form-group form-material">
<label class="control-label"><i class="icon-link"></i>Article permalink</label>
<div class="controls">
<input type="text" name="article-seo-url" class="col-md-12" value="<?php echo get_option('article-seo-url','/article/:name/:id'); ?>" /> 
<span class="help-block" id="limit-text">Default: /article/:name/:id </span>						
</div>	
</div>	
<div class="form-group form-material">
<label class="control-label"><i class="icon-link"></i>Page permalink</label>
<div class="controls">
<input type="text" name="page-seo-url" class="col-md-12" value="<?php echo get_option('page-seo-url','/read/:name/:id'); ?>" /> 
<span class="help-block" id="limit-text">Default: /read/:name/:id </span>						
</div>	
</div>	
<div class="form-group form-material">
<label class="control-label"><i class="icon-link"></i>Category permalink</label>
<div class="controls">
<input type="text" name="channel-seo-url" class="col-md-12" value="<?php echo get_option('channel-seo-url','/category/:name/:id'); ?>" /> 
<span class="help-block" id="limit-text">Default: /category/:name/:id</span>						
</div>	
</div>	

<div class="row page-footer">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update settings"); ?></button>	
</div>	
</fieldset>						
</form>
</div>
</div>
