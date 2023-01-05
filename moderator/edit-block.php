<?php
 if(isset($_GET['delete'])){ 
    $db->query("DELETE from ".DB_PREFIX."homepage WHERE id = '".intval($_GET['delete'])."'");
	echo '<div class="msg-info">You deleted the home box with id : '.$_GET['delete'].'</div>';
	 }
if(isset($_POST['queries'])){ 
$insertvideo = $db->query("
Update ".DB_PREFIX."homepage SET title = '".toDb($_POST['title'])."', ident = '".toDb($_POST['ident'])."',
querystring = '".toDb($_POST['queries'])."', total = '".toDb($_POST['number'])."', mtype = '".toDb($_POST['type'])."', car = '".toDb($_POST['car'])."' where id = '".intval($_GET['id'])."'");
}


echo '<div class="row">';


$box = $db->get_row("SELECT * FROM ".DB_PREFIX."homepage where id ='".intval($_GET['id'])."' order by `ord` ASC limit 0,1");
if($box) {
?>

<div class="box-element col-md-12">
					<div class="box-head-light"><i class="icon-pencil"></i><h3>Editing '<?php echo _html($box->title); ?>'</h3></div>
					<div class="box-content">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('edit-block').'&id='.intval($_GET['id']);  ?>" enctype="multipart/form-data" method="post">
		<input type="hidden" name="cid" value="<?php echo intval($_GET['id']); ?>">
	<div class="form-group form-material">
	<label class="control-label">Block title</label>
	<div class="controls">
	<input type="text" id="title" name="title" class="col-md-12" value="<?php echo _html($box->title); ?>">
	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label">Videos limit</label>
	<div class="controls">
	<input type="text" id="number" name="number" class="col-md-4 validate[required]" value="<?php echo _html($box->total); ?>">
	<span class="help-block" id="limit-text">Number of videos per block. If you have 1 block, it will be the number of videos to load per scroll.</span>
	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label">Video query:</label>
	<div class="controls">
	<select data-placeholder="Select type" name="queries" id="queris" class="select validate[required]" tabindex="2">
	
	<option value="most_viewed" <?php if($box->querystring == "most_viewed") { echo 'checked="checked" selected'; } ?>>Most viewed </option>
<option value="top_rated" <?php if($box->querystring == "top_rated") { echo 'checked="checked" selected'; } ?>>Most liked</option>
<option value="viral" <?php if($box->querystring == "viral") { echo 'checked="checked" selected'; } ?>>Recent</option>
<option value="featured" <?php if($box->querystring == "featured") { echo 'checked="checked" selected'; } ?>>Featured</option>
<option value="random" <?php if($box->querystring == "random") { echo 'checked="checked" selected'; } ?>>Random </option>

	</select>

	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-sort"></i>Media</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="type" data-rel="1" class="styled trigger" value="1" <?php if($box->mtype == "1") { echo 'checked'; } ?>>Video</label>
	<label class="radio inline"><input type="radio" name="type" data-rel="2" class="styled trigger" value="2" <?php if($box->mtype == "2") { echo 'checked'; } ?>>Music</label>
	<label class="radio inline"><input type="radio" name="type" data-rel="3" class="styled trigger" value="3" <?php if($box->mtype == "3") { echo 'checked'; } ?>>Images</label>
	</div>
	</div>	
		<div class="form-group form-material">
	<label class="control-label"><i class="icon-sort"></i>Carousel</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="car" class="styled" value="1" <?php if($box->car == "1") { echo 'checked'; } ?>>Yes</label>
	<label class="radio inline"><input type="radio" name="car" class="styled" value="0" <?php if($box->car == "0") { echo 'checked'; } ?>>No</label>

	</div>
	</div>
<?php echo '
<div class="form-group form-material">
	<label class="control-label">'._lang("Category:").'</label>
	<div class="controls">
	<select data-placeholder="'._lang("Choose a category:").'" name="ident" id="ident clear-results" class="select" tabindex="2">
	';
$categories = $db->get_results("SELECT cat_id as id, cat_name as name FROM  ".DB_PREFIX."channels order by cat_name asc limit 0,10000");
if($categories) {
$i = 0;
foreach ($categories as $cat) {	
if($box->ident == $cat->id) { $ss = "selected"; $i++; } else { $ss ="";}
echo '<option value="'.intval($cat->id).'" '.$ss.'>'._html($cat->name).'</option>';
$ss ="";
}
}  
if($i > 0 ) {
echo '<option value="">-- ALL --</option>'; 
} else {
echo '<option value="" selected>-- ALL --</option>'; 
}
echo '	  
	  </select>
	  	<span class="help-block" id="limit-text"> Optional: Restrict video in block to a category.</span>
	  </div>             
	  </div>
';
echo '
 <div class="box-bottom clearfix"> <button class="btn btn-primary btn-mini pull-right">Save changes</button>  </div>
</form>
</div>
</div>';
} else {
echo '<div class="box-element col-md-6">
Missing block? </div>
';
}			
 ?>
