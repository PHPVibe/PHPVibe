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
$uphandler = site_url().'app/uploading/upload_pl_ffmpeg.php?pvo='.$token;
} else {
$uphandler = site_url().'app/uploading/upload_pl.php?pvo='.$token;
}	
$text .= "
<!-- The basic File Upload plugin -->
<script src=\"".site_url()."app/3rdparty/plupload/plupload.min.js\"></script>
<script type=\"text/javascript\" >
function JustCapitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
$(document).ready(function(){
	
var uploader = new plupload.Uploader({
	runtimes : 'html5,flash', // html5,flash,silverlight,html4
	browse_button : 'pickfiles', // you can pass an id...
	drop_element: 'dropzoned',
	container: document.getElementById('dumpvideo'), // ... or DOM Element itself
	url : '".$uphandler."',
	flash_swf_url : '".site_url()."app/3rdparty/plupload/Moxie.swf',
	silverlight_xap_url : '".site_url()."app/3rdparty/plupload/Moxie.xap',
	multipart: true,
	chunk_size: '5mb',
	max_retries: 5,
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
			
			$('.AddVid').removeClass('hide');	
			$('#dumpvideo').hide();			
			plupload.each(files, function(file) {
				document.getElementById('filelist').innerHTML += '<div id=\"' + file.id + '\">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
				
				var fname = file.name.substr(0, file.name.lastIndexOf('.')) || file.name;
				 var punctRE = /[\u2000-\u206F\u2E00-\u2E7F\\'!\"#$%&()*+,\-.\/:;<=>?@\[\]^_`{|}~]/g;
			     fname = fname.replace(punctRE, ' ');
			     fname = JustCapitalize(fname);
			 $('#title').val(fname);			 
			});
				},
        BeforeUpload: function(up, file) {
		$('.vibeprogress').removeClass('hide');	
        $( '#formVid').removeClass('ffup');
            },
		UploadProgress: function(up, file) {
			document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + \"%</span>\";
		   $('#progressBar .upload-bar').css('width', file.percent + '%').attr('aria-valuenow', file.percent);
		   $('#progressBar .upload-number').css('-webkit-transform', 'translateX(' + $('#progressBar').outerWidth()/100*file.percent + 'px)').attr('aria-valuenow', file.percent);
		},
        UploadComplete: function(up, files) {
            processVid();	
            $('#validate').validator('validate');			
            $('#progressBar .upload-bar').css('width','100%');
		    $('#progressBar .upload-number').css('-webkit-transform', 'translateX(' + $('#progressBar').outerWidth() + 'px)').attr('aria-valuenow', 100);
 			
            },
		Error: function(up, err) {        
		$.notify(err.message + 'Error ' + err.code);
		if(err.code == 600 || err.code == -600 ) {
		$.notify('"._lang("Max allowed size is")." ".get_option('maxup','200')."mb');
		} else {
		alert(JSON.stringify(err));	
		}
		},
        FileUploaded: function(up, file, info) {
	     var response = jQuery.parseJSON(info.response);
		if(response.error) {
		  if(response.error.code == 107) { 
		 $( '#formVid').remove();
         $( '#vibe-error').html(response.error.message).removeClass('hide');
         $('#progressBar .upload-bar').addClass('failed');
		 $('#progressBar .upload-number').remove();
		  }
		 }
        }
		
	}
});
uploader.init();
$('#validate').validator().on('submit', function (e) {
  if (e.isDefaultPrevented()) {
  alert('"._lang('Required fields are missing')."');
  } 
})	
});		
</script>

";
return $text;
}
add_filter( 'filter_extrajs', 'file_up_support');

if(isset($_POST['vtoken'])) {
$tok = toDb(_post('vtoken'));
$doit = getVideobyToken($tok);
if($doit) {
if(get_option('ffa','0') <> 1 ) {
if(!is_insecure_file($_FILES['play-img']['name'])) {	
//No ffmpeg
$formInputName   = 'play-img';							
	$savePath	     = ABSPATH.'/storage/'.get_option('mediafolder').'/thumbs';								
	$saveName        = md5(time()).'-'.user_id();									
	$allowedExtArray = array('.jpg', '.png', '.gif');	
	$imageQuality    = 90;
$uploader = new FileUploader($formInputName, $savePath, $saveName , $allowedExtArray);
if ($uploader->getIsSuccessful()) {
$uploader -> resizeImage(get_option('thumb-width',205), get_option('thumb-height',115), 'crop');
$uploader -> saveImage($uploader->getTargetPath(), $imageQuality);
$thumb  = $uploader->getTargetPath();
$thumb = str_replace(ABSPATH.'/' ,'',$thumb);
} else { $thumb  = 'storage/uploads/noimage.png'; 	}

$sec = _tSec(_post('hours').":"._post('minutes').":"._post('seconds'));
$db->query("UPDATE  ".DB_PREFIX."videos SET duration='".$sec."', thumb='".toDb($thumb )."' , stayprivate = '".intval(_post('priv'))."', pub = '".intval(get_option('videos-initial'))."', title='".toDb(_post('title'))."', category='".toDb(intval(_post('categ')))."', nsfw='".intval(_post('nsfw') )."'  WHERE user_id= '".user_id()."' and id = '".intval($doit)."'");
//$error .=$db->debug();
} else { $thumb  = 'storage/uploads/noimage.png'; 	}
} else {
//Ffmpeg active
$db->query("UPDATE  ".DB_PREFIX."videos SET stayprivate = '".intval(_post('priv'))."', pub = '".intval(get_option('videos-initial'))."',title='".toDb(_post('title'))."', category='".toDb(intval(_post('categ')))."', nsfw='".intval(_post('nsfw') )."'  WHERE user_id= '".user_id()."' and id = '".intval($doit)."'");
}
//add tags
if(_post('tags')){
	foreach (explode(',',_post('tags')) as $tagul){
		save_tag($tagul,$doit);
	}
}
//add description
save_description($doit,_post('description'));
add_activity('4', $doit);
if(get_option('ffa','0') <> 1 ) {
$error .= '<div class="msg-info mtop20 mright20 mleft20">'._post('title').' '._lang("created successfully.").' 
</div>
<div class="text-center">
<a href="'.site_url().me.'" class="btn btn-default">'._lang("Manage videos").'</a>
<a href="'.site_url().add.'" class="btn btn-primary">'._lang("Upload another").'</a>
</div>
';
} else {
//Fire conversion
//for FFMPEG	
if(function_exists('exec')) {
$binpath = get_option('binpath','/usr/bin/php');
$command = $binpath." -cli -f ".ABSPATH."/videocron.php";
exec( "$command > /dev/null &", $arrOutput );
}	
// Show a message
$error .= '<div class="msg-info mtop20 mright20 mleft20"><strong>'._post('title').'</strong> '._lang("uploaded successfully.").' 
<br> <a href="'.site_url().me.'">'._lang("This video will be available after conversion.").'</a></div>
<div class="text-center">
<a href="'.site_url().me.'" class="btn btn-default">'._lang("Manage videos").'</a>
<a href="'.canonical().'" class="btn btn-primary">'._lang("Upload another").'</a>
</div>
';
}
if(get_option('videos-initial') <> 1) {
$error .= '<div class="msg-info mtop20 mright20 mleft20">
<'._lang("Video requires admin approval before going live.").'
</div>
<div class="text-center">
<a href="'.site_url().me.'" class="btn btn-default">'._lang("Manage videos").'</a>
<a href="'.canonical().'" class="btn btn-primary">'._lang("Upload another").'</a>
</div>
';

}
}
}
function modify_content( $text ) {
global $error, $token, $db;
if(not_empty($error)) {
return '<div style="margin:30px 0 50px">'.$error.'</div>';	
}

$data =  '<div id="vibe-error" class="hide msg-warning mtop20 mbot20 mleft20 mright20"></div>
   <div class="vibe-upload isBoxed row" style="padding:55px 10px;">			
	<div id="AddVid" class="AddVid text-center mtop20 left10 right10 hide">
   <div class="vibeprogress">    
	 <div class="uploading" id="progressBar">
    <div class="upload-number" aria-valuenow="0" style="left: 0;"></div>
    <div class="rounded">
      <div class="upload-bar" style="width: 0;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
      </div>
    </div>
  </div>
	</div>
  </div>
	<div id="filelist" class="text-center hide">Your browser doesn\'t have Flash or HTML5 support.</div>
	<div id="dumpvideo" class="text-center mtop20">
	<div id="dropzoned" class="block dropzoned"><img src="'.tpl().'images/cloud-upload.png"/>
	<p>'._lang("Drag and drop a video file here or").'</p>
	</div>
	<div class="block mtop20 mleft20 mright20">
	 <a id="pickfiles" class="btn btn-primary" href="javascript:;">'._lang("Choose a video").'</a>
	 </div>
    <a class="hide" id="uploadfiles" href="javascript:;">[Upload files]</a>
	</div>
	</div>
	</div>
	<div class="row AddVid hide">
    <div id="formVid" class="bottom20 top20 isBoxed ffup">
	<form id="validate" action="'.site_url().add.'" enctype="multipart/form-data" method="post">
	<input type="hidden" name="vfile" id="vfile" value="'.$token.'.mp4"/>	
	<input type="hidden" name="vup" id="vup" value="1"/>	
	<input type="hidden" name="vtoken" id="vtoken" value="'.$token.'"/>
	<div class="control-group form-group blc row">
	<label class="control-label">'._lang("Title:").'</label>
	<div class="controls">
	<input type="text" id="title" name="title" class="form-control col-md-12" required value="">
	<div class="help-block with-errors"></div>
	</div>
	</div>
	<div class="row">
	<div class="col-md-5">
	<div class="control-group mtop10">
	<label class="control-label">'._lang("Topic:").'</label>
	<div class="controls">
	'.cats_select('categ').'
	  </div>             
	  </div>
	  </div>
	  <div class="col-md-6 col-md-offset-1 top10">
	  <p class="control-label">'._lang("Audience").'</p>
	  <div class="inline">	  
			<div class="btn-group" data-toggle="buttons" role="group">
			<label class="btn btn-outline btn-default active">
			<input type="radio" name="nsfw" autocomplete="off" value="0" checked="checked" />
			<i class="icon icon-check text-active" aria-hidden="true"></i>
			'._lang("SAFE").'</label>
			<label class="btn btn-outline btn-default ">
			<input type="radio" name="nsfw" autocomplete="off" value="1"  />
			<i class="icon icon-check text-active" aria-hidden="true"></i>
			'._lang("NSFW").'</label>
			</div>
	  </div>
	   <div class="inline mleft20">
			<div class="btn-group" data-toggle="buttons" role="group">
			<label class="btn btn-outline btn-default active">
			<input type="radio" name="priv" autocomplete="off" value="0" checked="checked" />
			<i class="icon icon-check text-active" aria-hidden="true"></i>
			'._lang("Public").'</label>
			<label class="btn btn-outline btn-default ">
			<input type="radio" name="priv" autocomplete="off" value="2"  />
			<i class="icon icon-check text-active" aria-hidden="true"></i>
			'._lang("Followers").'</label>
			</div>
	  </div>
	  
	  </div>
	 </div> 
	<div class="control-group form-group mtop10">
			<label class="control-label">'._lang("Description:").'</label>
			<div class="controls">
			<textarea id="description" name="description" class="form-control auto" required></textarea>
			<div class="help-block with-errors"></div>
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
	</div>';
	
	if(get_option('ffa','0') <> 1 ) {
	$data .='
		<div class="form-group form-material mtop10">
		<label class="control-label" for="inputFile">'._lang("Choose thumbnail:").'</label>
		<input type="text" class="form-control" placeholder="'._lang("Browse...").'" readonly="" />
		<input type="file" name="play-img" id="play-img" />
			</div>
		 ';		
			$data .= '<div class="control-group mbot20">
			<label class="control-label">'._lang("Duration:").'</label>
			<div class="controls row">
		<div class="col-md-2 col-xs-3 mright10">
		   <div class="input-group">
				<span class="input-group-addon">'._lang("Hours").'</span>
				<input type="number" class="form-control" min="00" max="59" name="hours" value="">
			</div>
		</div>	
		<div class="col-md-2 col-xs-3 mright10">
		 <div class="input-group">
				<span class="input-group-addon">'._lang("Min").'</span>
				<input type="number" min="00" max="59" class="form-control" name="minutes" value="">
			</div>
		</div>
		<div class="col-md-2 col-xs-3 mright10">
		<div class="input-group">
				<span class="input-group-addon">'._lang("Sec").'</span>
				<input type="number" name="seconds" min="00" max="59" class="form-control" value="">
		</div>
		</div>
		</div>
		</div>';
	}	
$data .= '

<div class="row">

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
