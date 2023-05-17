<?php if(_post('type') && _post('file') && ($_FILES['play-img'] || _post('remote-img'))) {
$sec = intval(_post('duration'));
//if is image upload
if(isset($_FILES['play-img']) && !empty($_FILES['play-img']['name'])){
$formInputName   = 'play-img';							
	$savePath	     = ABSPATH.'/storage/'.get_option('mediafolder').'/thumbs';	
	$saveName        = md5(time()).'-'.user_id();									
	$allowedExtArray = array('.jpg', '.png', '.gif');	
	$imageQuality    = 100;
$uploader = new FileUploader($formInputName, $savePath, $saveName , $allowedExtArray);
if ($uploader->getIsSuccessful()) {
//$uploader -> resizeImage(200, 200, 'crop');
$uploader -> saveImage($uploader->getTargetPath(), $imageQuality);
$thumb  = $uploader->getTargetPath();
$thumbz  = str_replace(ABSPATH.'/' ,'',$thumb);
}
} else {
$thumbz = toDb($_POST['remote-img']);
}
$thumb = empty($thumbz)? 'storage/uploads/noimage.png' : $thumbz ;

//Insert video
if(_post('type') < 2) {
//if it's web link
$db->query("INSERT INTO ".DB_PREFIX."videos (`stayprivate`,`pub`,`source`, `user_id`, `date`, `thumb`, `title`, `duration` ,  `liked` , `category`, `nsfw`, `views`, `featured`) VALUES 
('".intval(_post('priv'))."','".intval(_post('pub'))."','"._post('file')."', '"._post('owner')."', now() , '".$thumb."', '".toDb(_post('title')) ."', '".$sec."',  '0','".toDb(_post('categ'))."','".toDb(_post('nsfw'))."','".intval(_post('views'))."','".intval(_post('featured'))."')");	
} else {
//if it's remote file
$db->query("INSERT INTO ".DB_PREFIX."videos (`stayprivate`,`pub`,`remote`, `user_id`, `date`, `thumb`, `title`, `duration`, `liked` , `category`, `nsfw`, `views`, `featured`) VALUES 
('".intval(_post('priv'))."','".intval(_post('pub'))."','"._post('file')."', '"._post('owner')."', now() , '".$thumb."', '".toDb(_post('title')) ."', '".$sec."', '0','".toDb(_post('categ'))."','".toDb(_post('nsfw'))."','".intval(_post('views'))."','".intval(_post('featured'))."')");	
}
$doit = $db->get_row("SELECT id from ".DB_PREFIX."videos where user_id = '".user_id()."' order by id DESC limit 0,1");
add_activity('4', $doit->id);
//add tags
if(_post('tags')){
	foreach (explode(',',_post('tags')) as $tagul){
		save_tag($tagul,$doit->id);
	}
}
//add description
save_description($doit->id,_post('description'));
add_activity('4', $doit->id);
echo '<div class="msg-info">'._post('title').' '._lang("created successfully.").' <a href="'.admin_url("videos").'">'._lang("Manage videos.").'</a></div>';
}elseif(_post('vfile') || _post('vremote')) { 
if(_post('vfile')){
$vid = new Vibe_Providers();
if(!$vid->isValid(_post('vfile'))){
echo '<div class="msg-warning">'._lang("We don't support yet embeds from that website").'</div>';
die();
}
$details = $vid->get_data();	
$file = _post('vfile');
$type = 1;
}
if(_post('vremote')){
if(!validateRemote(_post('vremote'))){ 
echo '<div class="msg-warning">'._lang("Seems that link it's not valid or link is wrong").'</div>';
die();
}
$file = _post('vremote');
$details = array("title" => "","thumbnail" => "","duration" => "","description" => "");
$type = 2;
}
$span = 12;
$data = '<div class="clearfix">			
	<div class="row clearfix ">
		<h3 class="loop-heading"><span>'._lang("Share video by link").'</span></h3>	
    <div id="formVid" class="col-md-12 pull-left well">
	<h3 style="display:block; margin:10px 20px">'._lang("New video details").'</h3>
	<div class="ajax-form-result clearfix "></div>
	<form id="validate" class="form-horizontal styled" action="'.admin_url('add-video').'" enctype="multipart/form-data" method="post">
	<input type="hidden" name="file" id="file" value="'.$file.'" readonly/>
	<input type="hidden" name="type" id="type" value="'.$type.'" readonly/>
	<div class="form-group form-material">
	<label class="control-label">'._lang("Title:").'</label>
	<div class="controls">
	<input type="text" id="title" name="title" class="validate[required] col-md-12" value="'.$details['title'].'">
	</div>
	</div>
	<label class="control-label">'._lang("Thumbnail:").'</label>
<div class="form-group form-material form-material-file">
	
	<div class="controls"> 
	<input type="text" class="form-control empty" readonly=""/>
<input type="file" id="play-img" name="play-img" class="styled" />
<label class="floating-label">Browse...</label>
</div>
</div>
	<h3>'._lang("OR").'</h3>
	<div class="row">';
	if($details['thumbnail'] && !empty($details['thumbnail'])) {
$data .=' 
<div class="col-md-4 pull-left">
	<img src="'.$details['thumbnail'].'"/>
	</div>
<div class="col-md-8 pull-right">
<div class="form-group form-material">
	<input type="text" id="remote-img" name="remote-img" class=" col-md-12" value="'.$details['thumbnail'].'" placeholder="'._lang("http://www.dailymotion.com/img/x116zuj_imbroglio_shortfilms.jpg").'">
	<span class="help-block" id="limit-text">'._lang("Link to original video image file. Don't change this to use video default (if any in left)").'</span>
	</div>
	</div>
	</div>';
} else {
$data .='
<div class="form-group form-material">
<div class="controls"> 
	<input type="text" id="remote-img" name="remote-img" class=" col-md-12" value="" placeholder="'._lang("http://www.dailymotion.com/img/x116zuj_imbroglio_shortfilms.jpg").'">
	<span class="help-block" id="limit-text">'._lang("Link to web image file.").'</span>
	</div>
	</div>
	
 ';
}	
$data .=' 
<div class="row">
    <div class="col-md-6 col-xs-6">
	<div class="form-group form-material">
	<label class="control-label">'._lang("Duration:").'</label>
	<div class="controls">
	<input type="text" id="duration" name="duration" class="validate[required] col-md-12" value="'.$details['duration'].'" placeholder="'._lang("In seconds").'">
	</div>
	</div>
	</div>
	 <div class="col-md-6 col-xs-6">
	<div class="form-group form-material">
	<label class="control-label">Initial views</label>
	<div class="controls">
	<input type="text" id="duration" name="views" class="col-md-12" value="1">
	</div>
	</div>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">'._lang("Category:").'</label>
	<div class="controls">
	<select data-placeholder="'._lang("Choose a category:").'" name="categ" id="clear-results" class="select validate[required]" tabindex="2">
	';
$categories = $db->get_results("SELECT cat_id as id, cat_name as name FROM  ".DB_PREFIX."channels where type ='1' order by cat_name asc limit 0,10000");
if($categories) {
foreach ($categories as $cat) {	
$data .='	
	
	 <option value="'.intval($cat->id).'">'._html($cat->name).'</option> 

	';
	}
}	else {
$data .='<option value="">'._lang("No categories").'</option>';
}
$data .='	  
	  </select>
	  </div>             
	  </div>
	<div class="form-group form-material">
	<label class="control-label">'._lang("Description:").'</label>
	<div class="controls">
	<textarea id="description" name="description" class="col-md-12 auto">'.$details['description'].'</textarea>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">'._lang("Tags:").'</label>
	<div class="controls">
	<input type="text" id="tags" name="tags" class="tags col-md-12" value="">
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">'._lang("NSFW:").'</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="nsfw" class="styled" value="1"> '._lang("Not safe").' </label>
	<label class="radio inline"><input type="radio" name="nsfw" class="styled" value="0" checked>'._lang("Safe").'</label>
	</div>
	</div>
		<div class="form-group form-material">
	<label class="control-label">User</label>
	<div class="controls">';

$data.='<select data-placeholder="'._lang("Choose owner:").'" name="owner" id="clear-results" class="select validate[required]" tabindex="2">
	';
$users = $db->get_results("SELECT id, name FROM  ".DB_PREFIX."users order by id asc limit 0,1000");
if($users) {
foreach ($users as $cat) {	
$data.='<option value="'.intval($cat->id).'">'._html($cat->name).'</option>';
	}
}	else {
$data.='<option value="">'._lang("No users").'</option>';
}
$data.='</select>';
	
	$data.='
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Featured?</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="featured" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="featured" class="styled" value="0" checked>NO</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Published?</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="pub" class="styled" value="1" checked> YES </label>
	<label class="radio inline"><input type="radio" name="pub" class="styled" value="0">NO</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Registered access only?</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="priv" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="priv" class="styled" value="0" checked>NO</label>
	</div>
	</div>
	<div class="form-group form-material">
	<button id="Subtn" class="btn btn-success btn-large pull-right" type="submit">'._lang("Add video").'</button>
	</div>
	</form>
	</div>
	
	</div>
	</div>
	</div>
';
echo $data;
 } else { ?>

<div class="row clearfix ">
    <div id="formVid" class="col-md-12 pull-left well">
	<h3 style="display:block; margin:10px 20px"><?php echo _lang("New video"); ?></h3>
	<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('add-video'); ?>" enctype="multipart/form-data" method="post">
	<input type="hidden" name="vembed" id="vembed" readonly/>
	<div class="form-group form-material">
	<label class="control-label"><?php echo _lang("Social video link:"); ?></label>
	<div class="controls">
	<input type="text" id="vfile" name="vfile" class=" col-md-12" value="" placeholder="<?php echo _lang("http://www.dailymotion.com/video/x116zuj_imbroglio_shortfilms"); ?>">
	<span class="help-block" id="limit-text"><?php echo _lang("Link to video (Youtube, Metacafe, etc)"); ?></span>
	<h3><?php echo _lang("OR"); ?></h3>
	<input type="text" id="vremote" name="vremote" class=" col-md-12" value="" placeholder="<?php echo _lang("http://www.mycustomhost.com/video/rihanna-file.mp4"); ?>">
	<span class="help-block" id="limit-text"><?php echo _lang("Remote file (direct link to .mp4,.flv file)"); ?></span>
	</div>
	</div>
	<div class="form-group form-material">
	<button id="Subtn" class="btn btn-success btn-large pull-right" type="submit"><?php echo _lang("Continue"); ?></button>
	</div>
	</form>
</div>
</div>	
<?php } ?>
	
