<?php if(isset($_POST['edited-image']) && !is_null(intval($_POST['edited-image']))) {
if(isset($_FILES['play-img']) && !empty($_FILES['play-img']['name'])){
$formInputName   = 'play-img';							# This is the name given to the form's file input
	$savePath	     = ABSPATH.'/storage/'.get_option('mediafolder').'/thumbs';								# The folder to save the image
	$saveName        = md5(time()).'-'.user_id();									# Without ext
	$allowedExtArray = array('.jpg', '.png', '.gif');	# Set allowed file types
	$imageQuality    = 100;
$uploader = new FileUploader($formInputName, $savePath, $saveName , $allowedExtArray);
if ($uploader->getIsSuccessful()) {
//$uploader -> resizeImage(200, 200, 'crop');
$uploader -> saveImage($uploader->getTargetPath(), $imageQuality);
$thumb  = $uploader->getTargetPath();
$tthumb  = str_replace(ABSPATH.'/' ,'',$thumb);
$source  = str_replace(ABSPATH.'/' ,'localimage/',$thumb);
	$db->query("UPDATE  ".DB_PREFIX."images SET source='".toDb($source)."', thumb='".get_option('mediafolder').'/'.toDb($tthumb )."' WHERE id = '".intval($_POST['edited-image'])."'");
}

}
$db->query("UPDATE  ".DB_PREFIX."images SET ispremium='".toDb(_post('ispremium'))."',liked='".toDb(_post('likes'))."',views='".toDb(_post('views'))."',stayprivate='".toDb(_post('priv'))."',title='".toDb(_post('title'))."', description='".toDb(_post('description') )."', category='".toDb(intval(_post('categ')))."', tags='".toDb(_post('tags') )."', nsfw='".intval(_post('nsfw') )."' WHERE id = '".intval($_POST['edited-image'])."'");
echo '<div class="msg-info">image: '._post('title').' updated.</div>';
$db->clean_cache();
} 
$image = $db->get_row("SELECT * from ".DB_PREFIX."images where id = '".intval(_get("vid"))."' ");
if($image) {
?>

<div class="row">
<h3>Update <a href="<?php echo image_url($image->id,$image->title); ?>" target="_blank"><?php echo $image->title; ?> <i class="icon-link"></i></a></h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('edit-image');?>&vid=<?php echo $image->id; ?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="edited-image" id="edited-image" value = "<?php echo $image->id; ?>"/>
<div class="form-group form-material">
<label class="control-label"><i class="icon-bookmark"></i><?php echo _lang("Title"); ?></label>
<div class="controls">
<input type="text" name="title" class="validate[required] col-md-12" value="<?php echo $image->title; ?>" /> 						
</div>	
</div>	
	
<div class="form-group form-material">
<label class="control-label"><?php echo _lang("Description"); ?></label>
<div class="controls">
<textarea rows="5" cols="5" name="description" class="auto validate[required] col-md-12" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 88px;"><?php echo $image->description; ?></textarea>					
</div>	
</div>
<div class="form-group form-material">
<label class="control-label">Image</label>
<div class="row mleft20">
<img src="<?php echo thumb_fix($image->thumb); ?>" style="max-width:350px; max-height:380px; margin-bottom:5px;"/>
</div>
<div class="controls">
<div class="row">
<div class="col-md-6">
<div class="form-group form-material form-material-file">
<div class="controls">
<input type="text" class="form-control empty" readonly="" />
<input type="file" id="play-img" name="play-img" class="styled" />
<label class="floating-label">Browse...</label>
<span class="help-block" id="limit-text"><?php echo _lang("Select only if you wish to change the image");?></span>
</div>
</div>
</div>
</div>
</div>
	
</div>
	<div class="control-group blc row">
	<label class="control-label"><?php echo _lang("Category:"); ?></label>
	<div class="controls">
	<?php echo cats_select('categ','select','validate[required]', 3); ?>
	<?php  if(isset($hint)) { ?>
	  <span class="help-block"> <?php echo $hint; ?></span>
	<?php } ?>  
	<script>
	      $(document).ready(function(){
	$('.select').find('option[value="<?php echo $image->category;?>"]').attr("selected",true);	
});
	</script>
	  </div>             
	  </div>
	  <div class="form-group form-material">
	<label class="control-label"><?php echo _lang("Tags:"); ?></label>
	<div class="controls">
	<input type="text" id="tags" name="tags" class="tags col-md-12" value="<?php echo $image->tags; ?>">
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Premium ?</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="ispremium" class="styled" value="1" <?php if($image->ispremium > 0 ) { echo "checked"; } ?>>Premium </label>
	<label class="radio inline"><input type="radio" name="ispremium" class="styled" value="0" <?php if($image->ispremium < 1 ) { echo "checked"; } ?>>Normal</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label"><?php echo _lang("NSFW:"); ?></label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="nsfw" class="styled" value="1" <?php if($image->nsfw > 0 ) { echo "checked"; } ?>> <?php echo _lang("Not safe"); ?> </label>
	<label class="radio inline"><input type="radio" name="nsfw" class="styled" value="0" <?php if($image->nsfw < 1 ) { echo "checked"; } ?>><?php echo _lang("Safe"); ?></label>
	</div>
	</div>
	<div class="control-group blc row">
	<label class="control-label"><?php echo _lang("Visibility"); ?> </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="priv" class="styled" value="1" <?php if($image->stayprivate > 0 ) { echo "checked"; } ?>> <?php echo _lang("Followers only");?> </label>
	<label class="radio inline"><input type="radio" name="priv" class="styled" value="0" <?php if($image->stayprivate < 1 ) { echo "checked"; } ?>><?php echo _lang("Everybody");?> </label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Views</label>
	<div class="controls">
	<input type="text" id="views" name="views" class=" col-md-12" value="<?php echo $image->views; ?>">
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Likes</label>
	<div class="controls">
	<input type="text" id="liked" name="likes" class=" col-md-12" value="<?php echo $image->liked; ?>">
	</div>
	</div>
	
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update image"); ?></button>	
</div>	
</fieldset>						
</form>
<?php
} else {
echo '<div class="msg-warning">Missing image</div>';
} ?>
</div>
