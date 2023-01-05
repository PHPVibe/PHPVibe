<?php $pid = (isset($_POST["pid"])) ? intval($_POST["pid"]) : intval($_GET['pid']);
if(isset($_POST['play-name'])) {
$db->query("UPDATE ".DB_PREFIX."posts SET 
ch ='".intval($_POST['ch'])."', 
title ='".$db->escape($_POST['play-name'])."' ,
content ='".$db->escape(htmlentities($_POST['content']))."', 
tags ='".$db->escape($_POST['tags'])."' 
WHERE pid = '".$pid."'");
echo '<div class="msg-win">Article updated.</div>';
}


if(isset($_FILES['play-img']) && !empty($_FILES['play-img']['name'])){

$endextens = explode('.', $_FILES['play-img']['name']);

$extension = end($endextens);

$thumb = ABSPATH.'/storage/uploads/'.nice_url($_FILES['play-img']['name']).uniqid().'.'.$extension;

if (move_uploaded_file($_FILES['play-img']['tmp_name'], $thumb)) {

    $sthumb = str_replace(ABSPATH.'/' ,'',$thumb);
	$db->query("UPDATE ".DB_PREFIX."posts SET pic ='".$sthumb."' WHERE pid = '".$pid."'");
	echo '<div class="msg-win">Picture updated.</div>';
	} else {

	echo '<div class="msg-warning">Logo upload failed.</div>';

	}
}
$page = $db->get_row("select * from ".DB_PREFIX."posts where pid = '".$pid."'");

?>
<div class="row">
<form id="validate" class="form-horizontal styled" action="<?php echo canonical();?>" enctype="multipart/form-data" method="post">
<fieldset>
<div class="form-group form-material">
<label class="control-label"><i class="icon-text-height"></i>Title</label>
<div class="controls">
<input type="text" name="play-name" class="validate[required] col-md-12" value="<?php echo _html($page->title); ?>"/> 						
</div>	
</div>	
<div class="form-group form-material">
<label class="control-label">Article content</label>
<div class="controls">
<textarea rows="5" cols="5" name="content" class="ckeditor col-md-12" style="word-wrap: break-word; resize: horizontal; height: 88px;"><?php echo _html($page->content); ?></textarea>					
</div>	
</div>
<label class="control-label">Change the Image?</label>
<div class="form-group form-material form-material-file">

<div class="controls">
<input type="text" class="form-control empty" readonly="" />
<input type="file" id="play-img" name="play-img" class="styled" />
<label class="floating-label">Browse...</label>
</div>	
</div>

<?php
echo '<div class="form-group form-material">
	<label class="control-label">'._lang("Category:").'</label>
	<div class="controls">
	<select data-placeholder="'._lang("Choose a category:").'" name="ch" id="clear-results" class="select" tabindex="2">
	';
$categories = $db->get_results("SELECT cat_id as id, cat_name as name FROM  ".DB_PREFIX."postcats order by cat_name asc limit 0,10000");
if($categories) {
foreach ($categories as $cat) {	
    if($page->ch <> $cat->id) {
    echo '<option value="'.intval($cat->id).'">'._html($cat->name).'</option> ';
	} else {
	echo '<option value="'.intval($cat->id).'" selected>'._html($cat->name).'</option> ';
	$hint = _lang("Initial category is").' <strong>'._html($cat->name).'</strong>';
	}
	
}
}	else {
echo '<option value="">'._lang("No categories").'</option>';
}
echo '</select>
	  </div>             
	  </div>';
?> 
<div class="form-group form-material">
	<label class="control-label">Tags</label>
	<div class="controls">
	<input type="text" id="tags" name="tags" class="tags col-md-12" value="<?php echo _html($page->tags); ?>">
	<span class="help-block" id="limit-text">Press enter after each tag</span>
	</div>
	</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Save changes</button>	
</div>	
</fieldset>						
</form>
</div>
