<?php
if(isset($_POST['play-name'])) {
$picture ='storage/uploads/noimage.png';
$formInputName   = 'play-img';							# This is the name given to the form's file input
	$savePath	     = ABSPATH.'/storage/uploads';								# The folder to save the image
	$saveName        = md5(time()).'-'.user_id();									# Without ext
	$allowedExtArray = array('.jpg', '.png', '.gif');	# Set allowed file types
	$imageQuality    = 100;
$uploader = new FileUploader($formInputName, $savePath, $saveName , $allowedExtArray);
if ($uploader->getIsSuccessful()) {
//$uploader -> resizeImage(200, 200, 'crop');
$uploader -> saveImage($uploader->getTargetPath(), $imageQuality);
$thumb  = $uploader->getTargetPath();
$picture  = str_replace(ABSPATH.'/' ,'',$thumb);
} 
if(isset($_POST['categ']) && intval($_POST['categ']) > 0) {
$ch = $_POST['categ'];
} else {
$ch = null;
}

$db->query("INSERT INTO ".DB_PREFIX."postcats (`cat_name`, `picture`, `cat_desc`) VALUES ('".toDb($_POST['play-name'])."', '".toDb($picture)."' , '".toDb($_POST['play-desc'])."')");
echo '<div class="msg-info">Blog category "'.$_POST['play-name'].'" created</div>';
}

?><div class="row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('create-pch');?>" enctype="multipart/form-data" method="post">
<fieldset>
<div class="form-group form-material">
<label class="control-label">Category's title</label>
<div class="controls">
<input type="text" name="play-name" class="validate[required] col-md-12" placeholder="<?php echo _lang("The title"); ?>" /> 						
</div>	
</div>	

<div class="form-group form-material">
<label class="control-label"><?php echo _lang("Description"); ?></label>
<div class="controls">
<textarea rows="5" cols="5" name="play-desc" class="auto col-md-12" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 88px;"></textarea>					
</div>	
</div>
<label class="control-label"><?php echo _lang("Image"); ?></label>
<div class="form-group form-material">

<div class="controls">
<input type="text" class="form-control empty" readonly="" />
<input type="file" id="play-img" name="play-img" class="styled" />
<label class="floating-label">Browse...</label>
</div>	
</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Create"); ?></button>	
</div>	
</fieldset>						
</form>
</div>
