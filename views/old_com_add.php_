<?php if(!is_user()) { redirect(site_url().'login/'); }
$error='';
// SEO Filters
function modify_title( $text ) {
 return strip_tags(stripslashes($text.' '._lang('share')));
}
$token = md5(user_name().user_id().time());
function file_up_support($text) {
global $token;
$text  = '';
$allext = get_option('alext','flv,mp4,mp3,avi,mpeg');
if(get_option('ffa','0') == 1 ) {		
$uphandler = site_url().'lib/upload_pl_ffmpeg.php';
} else {
$uphandler = site_url().'lib/upload_pl.php';
}	
$text .= "
<!-- The basic File Upload plugin -->
<script src=\"".site_url()."lib/plupload/plupload.full.min.js\"></script>
<script type=\"text/javascript\" >
function JustCapitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
$(document).ready(function(){
var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'pickfiles', // you can pass an id...
	container: document.getElementById('dumpvideo'), // ... or DOM Element itself
	url : '".$uphandler."',
	flash_swf_url : '".site_url()."lib/plupload/Moxie.swf',
	silverlight_xap_url : '".site_url()."lib/plupload/Moxie.xap',
	 multipart_params : {
        'token' : '".$token."'
    },
	filters : {
		'max_file_size' : '".get_option('maxup','200')."mb',
		'mime_types': [
			{'title' : '"._lang('Video file types')."', 'extensions' : '".$allext."'}
		]
	},
    multi_selection: false,
	init: {
		PostInit: function() {
			document.getElementById('filelist').innerHTML = '';

			document.getElementById('uploadfiles').onclick = function() {
				uploader.start();
				return false;
			};
		},

		FilesAdded: function(up, files) {
			up.start();
			$('#dumpvideo').hide();			
			plupload.each(files, function(file) {
				document.getElementById('filelist').innerHTML += '<div id=\"' + file.id + '\">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
				
				var fname = file.name.substr(0, file.name.lastIndexOf('.')) || file.name;
			     fname = fname.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ' ');
			     fname = JustCapitalize(fname);
			 $('#title').val(fname);
			 $('#description').val(fname);
			});
				},
        BeforeUpload: function(up, file) {
		$('.vibeprogress').removeClass('hide');	
        $( '#formVid').removeClass('ffup');
            },
		UploadProgress: function(up, file) {
			document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + \"%</span>\";
			$('.vibebar').width(file.percent + '%');
		},
        UploadComplete: function(up, files) {
                processVid(); 
            $('.vibebar').addClass('completed');				
            },
		Error: function(up, err) {
        alert(err);
		},
        FileUploaded: function(up, file, info) {
	     var response = jQuery.parseJSON(info.response);
		if(response.error.code == 107) { 
		 $( '#formVid').remove();
         $( '#vibe-error').html(response.error.message).removeClass('hide');
         $('.vibebar').addClass('failed');		 
		 }
        }
		
	}
});
uploader.init();
});
</script>

";
return $text;
}
add_filter( 'filter_extrajs', 'file_up_support');
if(isset($_POST['vtoken'])) {
$tok = toDb(_post('vtoken'));
$doit = $db->get_row("SELECT id from ".DB_PREFIX."videos where token = '".$tok."'");
if($doit) {
if(get_option('ffa','0') <> 1 ) {
if(!is_insecure_file($_FILES['play-img']['name'])) {	
//No ffmpeg
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
$thumb = str_replace(ABSPATH.'/' ,'',$thumb);
} else { $thumb  = 'storage/uploads/noimage.png'; 	}

$sec = _tSec(_post('hours').":"._post('minutes').":"._post('seconds'));
$db->query("UPDATE  ".DB_PREFIX."videos SET duration='".$sec."', thumb='".toDb($thumb )."' , privacy = '".intval(_post('priv'))."', pub = '".intval(get_option('videos-initial'))."', title='".toDb(_post('title'))."', description='".toDb(_post('description') )."', category='".toDb(intval(_post('categ')))."', tags='".toDb(_post('tags') )."', nsfw='".intval(_post('nsfw') )."'  WHERE user_id= '".user_id()."' and id = '".intval($doit->id)."'");
//$error .=$db->debug();
} else { $thumb  = 'storage/uploads/noimage.png'; 	}
} else {
//Ffmpeg active
$db->query("UPDATE  ".DB_PREFIX."videos SET privacy = '".intval(_post('priv'))."', pub = '".intval(get_option('videos-initial'))."',title='".toDb(_post('title'))."', description='".toDb(_post('description') )."', category='".toDb(intval(_post('categ')))."', tags='".toDb(_post('tags') )."', nsfw='".intval(_post('nsfw') )."'  WHERE user_id= '".user_id()."' and id = '".intval($doit->id)."'");
}
add_activity('4', $doit->id);
if(get_option('ffa','0') <> 1 ) {
$error .= '<div class="msg-info mtop20 mright20">'._post('title').' '._lang("created successfully.").' <a href="'.site_url().me.'">'._lang("Manage videos.").'</a></div>';
} else {
$error .= '<div class="msg-info mtop20 mright20">'._post('title').' '._lang("uploaded successfully.").' <a href="'.site_url().me.'">'._lang("This video will be available after conversion.").'</a></div>';
}
if(get_option('videos-initial') <> 1) {
$error .= '<div class="msg-info mtop20 mright20">'._lang("Video requires admin approval before going live.").'</div>';

}
}
}
function modify_content( $text ) {
global $error, $token, $db;
$data =  $error.'<div id="vibe-error" class="hide msg-warning mtop20 mbot20"></div>
<h1 class="block full mtop20 mbot20">'._lang("Share a video").'</h1>	
   <div class="clearfix vibe-upload mright20 mbot20">			
	<div class="row clearfix ">
	<div id="AddVid" class="text-center">
  <div class="vibeprogress hide">
    <div class="vibebar" style="width:0.1%">
	<div class="pin bounce"></div>
	</div>
  </div>
	<div id="filelist">Your browser doesn\'t have Flash or HTML5 support.</div>
    <br />
	<div id="dumpvideo">
	 <a id="pickfiles" href="javascript:;">[Select files]</a>
    <a id="uploadfiles" href="javascript:;">[Upload files]</a>
	</div>
	</div>
	</div>
	<div class="row clearfix right10">
    <div id="formVid" class="nomargin well ffup">
	<form id="validate" action="'.canonical().'" enctype="multipart/form-data" method="post">
	<input type="hidden" name="vfile" id="vfile"/>	
	<input type="hidden" name="vup" id="vup" value="1"/>	
	<input type="hidden" name="vtoken" id="vtoken" value="'.$token.'"/>
	<div class="control-group blc row">
	<label class="control-label">'._lang("Title:").'</label>
	<div class="controls">
	<input type="text" id="title" name="title" class="validate[required] form-control col-md-12" value="">
	</div>
	</div>';
	if(get_option('ffa','0') <> 1 ) {
	$data .='
<div class="form-group form-material mtop10">
<label class="control-label" for="inputFile">'._lang("Choose thumbnail:").'</label>
<input type="text" class="form-control" placeholder="'._lang("Browse...").'" readonly="" />
<input type="file" name="play-img" id="play-img" />
    </div>
 ';		
	$data .= '<div class="control-group">
	<label class="control-label">'._lang("Duration:").'</label>
	<div class="controls row">
<div class="col-md-4">
   <div class="input-group">
        <span class="input-group-addon">'._lang("Hours").'</span>
        <input type="number" class="form-control" min="00" max="59" name="hours" value="">
    </div>
</div>	
<div class="col-md-4">
 <div class="input-group">
        <span class="input-group-addon">'._lang("Min").'</span>
        <input type="number" min="00" max="59" class="form-control" name="minutes" value="">
    </div>
</div>
<div class="col-md-4">
<div class="input-group">
        <span class="input-group-addon">'._lang("Sec").'</span>
        <input type="number" name="seconds" min="00" max="59" class="form-control" value="">
</div>
</div>
</div>
</div>';
	}
	$data .= '
	<div class="control-group mtop10">
	<label class="control-label">'._lang("Category:").'</label>
	<div class="controls">
	'.cats_select('categ').'
	  </div>             
	  </div>
	<div class="control-group mtop10">
	<label class="control-label">'._lang("Description:").'</label>
	<div class="controls">
	<textarea id="description" name="description" class="validate[required] form-control auto"></textarea>
	</div>
	</div>
	<div class="control-group mtop10">
	<div class="form-group">
	<div class="input-group">
    <span class="input-group-addon">'._lang("Tags:").'</span>
	<div class="form-control withtags">
	<input type="text" id="tags" name="tags" class="tags form-control" value="">
	</div>
	</div>
	</div>
	</div>
	<div class="control-group">
	<label class="control-label">'._lang("NSFW:").'</label>
	<div class="controls row">
	<div class="radio-custom radio-primary col-md-4">
	<input type="radio" name="nsfw" value="1">
	<label> '._lang("Not safe").' </label>
	</div>
	<div class="radio-custom radio-primary col-md-4">
	<input type="radio" name="nsfw" value="0" checked>
	<label >'._lang("Safe").'</label>
	</div>
	</div>
	</div>
	<div class="control-group">
	<label class="control-label">'._lang("Privacy:").' </label>
	<div class="controls row">
	<div class="radio-custom radio-primary col-md-4">
	<input type="radio" name="priv" value="1">
	<label> '._lang("Followers only").' </label>
	</div>
	<div class="radio-custom radio-primary col-md-4">
	<input type="radio" name="priv" value="0" checked>
	<label>'._lang("Public").' </label>
	</div>
	</div>
	</div>
	<div class="control-group blc row">
	<button id="Subtn" class="btn btn-large pull-right" type="submit" disabled>'._lang("Waiting for upload").'</button>
	</div>
	</form>
	</div>
	
	</div>
	</div>
	</div>
';
return $data;
}
add_filter( 'phpvibe_title', 'modify_title' );

if((get_option('uploadrule') == 1) ||  is_moderator()) {	
add_filter( 'the_defaults', 'modify_content' );
} else {
function udisabled() {
return _lang("This uploading section is disabled");
}
add_filter( 'the_defaults', 'udisabled'  );
}
//Time for design
 the_header();
include_once(TPL.'/sharemedia.php');
the_footer();
?>
