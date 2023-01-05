<?php
if(isset($_POST['play-name'])) {if(isset($_FILES['play-img']) && !empty($_FILES['play-img']['name'])){	$endextens = explode('.', $_FILES['play-img']['name']);$extension = end($endextens);	$thumb = ABSPATH.'/storage/uploads/'.nice_url($_FILES['play-img']['name']).uniqid().'.'.$extension;	if (move_uploaded_file($_FILES['play-img']['tmp_name'], $thumb)) {   	$picture = str_replace(ABSPATH.'/' ,'',$thumb);   }	else { $picture = '';}	}  	else { $picture = '';}
$db->query("INSERT INTO ".DB_PREFIX."pages (`date`, `menu`, `pic`, `title`, `content`, `tags`, `m_order`)
 VALUES (now(),'".intval($_POST['menu'])."', '".$picture."', '".$db->escape($_POST['play-name'])."', '".$db->escape(htmlentities($_POST['content']))."', '".$db->escape($_POST['tags'])."', '".$db->escape($_POST['m_order'])."')");
echo '<div class="msg-info">Page '.$_POST['play-name'].' created</div>';
}

?>
<div class="row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('create-page');?>" enctype="multipart/form-data" method="post">
<fieldset>
<div class="form-group form-material">
<label class="control-label"><i class="icon-text-height"></i>Page title</label>
<div class="controls">
<input type="text" name="play-name" class="validate[required] col-md-12"/> 						
</div>	
</div>	
<div class="row">
<div class="col-md-4 col-xs-12">
<div class="form-group form-material">
	<label class="control-label"><i class="icon-check"></i>Show in menu?</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="menu" class="styled" value="1">Yes</label>
	<label class="radio inline"><input type="radio" name="menu" class="styled" value="0" checked>No</label>
		<span class="help-block" id="limit-text">Should this be visible in menus? <br><em class="small">(The location depends on theme but usually is sidebar or footer)</em></span>
	</div>
	</div>	
	</div>
<div class="col-md-4 col-xs-12">
<div class="form-group form-material">
<label class="control-label"><i class="icon-align-left"></i>Menu order</label>
<div class="controls">
<input type="text" name="m_order" value="1" class="validate[required] col-md-12"/> 						
</div>	
</div>		
	</div>
	</div>
<div class="form-group form-material">
<label class="control-label">Page content</label>
<div class="controls">
<textarea rows="5" cols="5" name="content" class="ckeditor col-md-12" style="word-wrap: break-word; resize: horizontal; height: 88px;"></textarea>					
</div>	
</div>
<label class="control-label">Image?</label>
<div class="form-group form-material form-material-file">
<div class="controls">
<input type="text" class="form-control empty" readonly="" />
<input type="file" id="play-img" name="play-img" class="styled" />
<label class="floating-label">Browse...</label>
</div>	
</div>
<div class="form-group form-material">
	<label class="control-label">Tags</label>
	<div class="controls">
	<input type="text" id="tags" name="tags" class="tags col-md-12" value="">
	<span class="help-block" id="limit-text">Press enter after each tag</span>
	</div>
	</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Create page</button>	
</div>	
</fieldset>						
</form>
</div>
