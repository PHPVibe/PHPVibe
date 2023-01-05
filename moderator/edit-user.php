<?php $uid = intval($_GET["id"]);
if(isset($_POST['changeuser'])) {
if(isset($_POST['name'])) { user::Update('name',$_POST['name'],$uid); }
if(isset($_POST['city'])) { user::Update('local',$_POST['city'],$uid); }
if(isset($_POST['country'])) { user::Update('country',$_POST['country'], $uid); }
if(isset($_POST['bio'])) { user::Update('bio',$_POST['bio'], $uid); }
if(isset($_POST['gender'])) { user::Update('gender',$_POST['gender'],$uid); }
if(isset($_POST['group_id'])) { user::Update('group_id',$_POST['group_id'],$uid); }
echo '<div class="msg-info">User: '._post('name').' updated.</div>';
} 
$profile = $db->get_row("SELECT * from ".DB_PREFIX."users where id = '".$uid."' ");
if($profile) {
?>
<div class="row">
<h3>Update user <a href="<?php echo profile_url($profile->id, $profile->name);?>" target="_blank"><?php echo $profile->name;?></a></h3>
<div class="row clearfix">
<form class="form-horizontal styled" action="<?php echo admin_url('edit-user');?>&id=<?php echo $profile->id;?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="changeuser" class="hide" value="1" /> 
<fieldset>
<div class="form-group form-material">
<label class="control-label"><i class="icon-user"></i><?php echo _lang("Name"); ?></label>
<div class="controls">
<input type="text" name="name" class="col-md-12" value="<?php echo $profile->name;?>" /> 						
</div>	
</div>	
<div class="form-group form-material">
<label class="control-label"><?php echo _lang("City"); ?></label>
<div class="controls">
<input type="text" name="city" class="col-md-12" value="<?php echo stripslashes($profile->local); ?>" /> 						
</div>	
</div>	
<div class="form-group form-material">
<label class="control-label"><?php echo _lang("Country"); ?></label>
<div class="controls">
<input type="text" name="country" class="col-md-12" value="<?php echo stripslashes($profile->country); ?>" /> 						
</div>	
</div>	
<div class="form-group form-material">
<label class="control-label"><?php echo _lang("About you"); ?></label>
<div class="controls">
<textarea rows="5" cols="5" name="bio" class="auto col-md-12" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 88px;"><?php echo stripslashes($profile->bio); ?></textarea>					
</div>	
</div>
<div class="form-group form-material">
<label class="control-label"><?php echo _lang("Gender"); ?></label>
<div class="controls">
<label class="radio">
<input type="radio" name="gender" id="gender2" class="styled" value="1" <?php if($profile->gender < 2) { ?>checked="checked"<?php } ?>><?php echo _lang("Male"); ?>
</label>
<label class="radio">
<input type="radio" name="gender" id="gender2"  class="styled" value="2" <?php if($profile->gender > 1) { ?>checked="checked"<?php } ?>>
<?php echo _lang("Female"); ?>
</label>
</div>	
</div>
<div class="form-group form-material">
<label class="control-label">Group</label>
<div class="controls">
<?php $groups = $db->get_results("SELECT * from ".DB_PREFIX."users_groups limit 0,1000 ");
foreach ($groups as $gp) {
?>
<label class="radio">
<input type="radio" name="group_id" id="group2" class="styled" value="<?php echo $gp->id; ?>" <?php if($profile->group_id <> $gp->id) {} else { ?> checked <?php } ?>><?php echo $gp->name; ?>
</label>
<?php } ?>
</div>	
</div>					

<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Update user</button>	
</div>	
</fieldset>					
</form>
</div>

<?php
} else {
echo '<div class="msg-warning">Missing user</div>';
} ?>
</div>
