<?php 
if(isset($_POST['update_options_now'])){
foreach($_POST as $key=>$value)
{
if($key !== "site-logo") {
  update_option($key, $value);
}
}
  echo '<div class="msg-info">Configuration options have been updated.</div>';
  $db->clean_cache();
}

$all_options = get_all_options();
?>

<div class="row">
<h3>Menus & Links</h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('menulinks');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="update_options_now" class="hide" value="1" /> 
<div class="form-group form-material">
	<label class="control-label"><i class="icon-cloud-upload"></i>Show sharing menu to</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="upmenu" class="styled" value="1" <?php if(get_option('upmenu') == 1 ) { echo "checked"; } ?>>All registered users</label>
	<label class="radio inline"><input type="radio" name="upmenu" class="styled" value="0" <?php if(get_option('upmenu') <> 1 ) { echo "checked"; } ?>>Only moderators & administrators</label>
	</div>
	</div>
<div class="form-group form-material">
	<label class="control-label"><i class="icon-reorder"></i>Music menu</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="musicmenu" class="styled" value="1" <?php if(get_option('musicmenu') == 1 ) { echo "checked"; } ?>>Show</label>
	<label class="radio inline"><input type="radio" name="musicmenu" class="styled" value="0" <?php if(get_option('musicmenu') <> 1 ) { echo "checked"; } ?>>Hide</label>
	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-reorder"></i>Images menu</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="imagesmenu" class="styled" value="1" <?php if(get_option('imagesmenu') == 1 ) { echo "checked"; } ?>>Show</label>
	<label class="radio inline"><input type="radio" name="imagesmenu" class="styled" value="0" <?php if(get_option('imagesmenu') <> 1 ) { echo "checked"; } ?>>Hide</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-reorder"></i>Playlists menu</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="showplaylists" class="styled" value="1" <?php if(get_option('showplaylists','1') == 1 ) { echo "checked"; } ?>>Show</label>
	<label class="radio inline"><input type="radio" name="showplaylists" class="styled" value="0" <?php if(get_option('showplaylists','1') <> 1 ) { echo "checked"; } ?>>Hide</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-reorder"></i>Members menu</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="showusers" class="styled" value="1" <?php if(get_option('showusers','1') == 1 ) { echo "checked"; } ?>>Show</label>
	<label class="radio inline"><input type="radio" name="showusers" class="styled" value="0" <?php if(get_option('showusers','1') <> 1 ) { echo "checked"; } ?>>Hide</label>
	</div>
	</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update settings"); ?></button>	
</div>	
</fieldset>						
</form>
</div>
