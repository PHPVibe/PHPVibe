<?php if(isset($_POST['edited-video']) && !is_null(intval($_POST['edited-video']))) {
	$updater = new PHPVibe\Video\VideoUpdate(intval($_POST['edited-video']));
	// Track changes 
	$changes = array();
	
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
			$changes['thumb'] = toDb(str_replace(ABSPATH.'/' ,'',$thumb));
		}

	} else {
		if(not_empty($_POST['remote-img'])) {	
			//$db->query("UPDATE ".DB_PREFIX."videos SET thumb='".toDb($_POST['remote-img'])."' WHERE id = '".intval($_POST['edited-video'])."'");
		    $changes['thumb'] = toDb($_POST['remote-img']);
		}
	}
	if(isset($_FILES['subtitle']) && !empty($_FILES['subtitle']['name'])){
		$fp = ABSPATH.'/storage/'.get_option('mediafolder')."/";
		$extension = end(explode('.', $_FILES['subtitle']['name']));
		$srt_path = $fp.'subtitle-'.intval($_POST['edited-video']).'.'.$extension;
		$srt = 'subtitle-'.intval($_POST['edited-video']).'.'.$extension;
		if (move_uploaded_file($_FILES['subtitle']['tmp_name'], $srt_path)) {
		//$db->query("UPDATE  ".DB_PREFIX."videos SET srt='".toDb($srt)."' WHERE id = '".intval($_POST['edited-video'])."'");
        $changes['srt'] = toDb($srt);
			echo '<div class="msg-win">New subtitle file uploaded.</div>';
			} else {
			echo '<div class="msg-warning">Subtitle upload failed.</div>';
			}			
	}
/*
$db->query("UPDATE  ".DB_PREFIX."videos SET ispremium='".toDb(_post('ispremium'))."',disliked='".toDb(_post('dislikes'))."',
liked='".toDb(_post('likes'))."',views='".toDb(_post('views'))."',stayprivate='".toDb(_post('priv'))."',
title='".toDb(_post('title'))."', description='".toDb(_post('description') )."', duration='".intval(_post('duration') )."',
category='".toDb(intval(_post('categ')))."', tags='".toDb(_post('tags') )."',
 nsfw='".intval(_post('nsfw') )."', source='".toDb(_post('source'))."',
 remote='".toDb(_post('remote'))."',
 embed='".esc_textarea(_post('embed'))."' WHERE id = '".intval($_POST['edited-video'])."'");
*/
	if(not_empty(_post('tags'))) {
	$xtags = explode(',', _post('tags'));
		if(not_empty($xtags)) {
			foreach ($xtags as $tagul) {
			$changes['tags'][]['name'] = toDb($tagul);
			}
		}
	}
	if(_post('categ')) {
	$cats = $_POST['categ'];
		if(is_array($cats)) {
			$changes['category'] = implode(',', $cats);
		}
		
	}
	if(_post('source')) {
		$changes['source'] = toDb(_post('source'));		
	}
	if(_post('embed')) {
		$changes['embed'] = toDb(_post('embed'));		
	}
	if(_post('remote')) {
		$changes['remote'] = toDb(_post('remote'));		
	}
	
	$othervalues = array(
			  'ispremium' => toDb(_post('ispremium')),
			  'stayprivate' => toDb(_post('priv')),			 
			  'title' => toDb(_post('title')),		  
			  'duration' => intval(_post('duration') ),
			  'views' => toDb(_post('views')),
			  'liked' => toDb(_post('likes')),
			  'disliked' => toDb(_post('dislikes')),
			  'nsfw' => intval(_post('nsfw') ),
			  'description' => toDb(_post('description') ));

 $updater->add(array_merge($changes, $othervalues));
 echo '<div class="msg-info">'; 
 $doit = $updater->doupdate(); 
 echo $doit.'</div>';
	if($updater->isdone()) {		  
		echo '<div class="msg-win">'._post('title').' updated.</div>';
	} else {
		echo '<div class="msg-warning">'.$updater->error().' updated.</div>';
	}
} 
if(isset($_GET['removefn'])) {
	$fl =  $folder = ABSPATH.'/storage/'.get_option('mediafolder','media').'/'.$_GET['removefn'];
	remove_file($fl);
	echo '<div class="msg-win">Quality file removed.</div>';
}
if(isset($_GET['reconvertfrom'])) {
	$input = ABSPATH.'/storage/'.get_option('mediafolder','media').'/'.$_GET['reconvertfrom'];
	$bq = explode('-',$_GET['reconvertfrom']);
	$size = str_replace('.mp4','',$bq[1]);
	$token = $bq[0];
	$tp = ABSPATH.'/storage/'.get_option('tmp-folder','rawmedia')."/";
	//echo $tp;
	$fp = ABSPATH.'/storage/'.get_option('mediafolder','media')."/";
	$folder = $fp;
	$final = $fp.$token;
	$sizes = get_option('ffmeg-qualities','360');
	$to = @explode(",", $sizes);	
	// Log start
	vibe_log("<br>Conversion starting for: <br><code>".$input."</code><br>");	
	if (file_exists($input)) { 
		//Start video conversion

		$command ='';
		/* Loop qualities */
		foreach ($to as $call) {
			if($call <= ($size + 100)) {	
				if(not_empty($call)) {	
				$conv = get_option('fftheme-'.$call,'');
					if(not_empty($conv)) {	
					$out = str_replace(array('{ffmpeg-cmd}','{input}','{output}'),array(get_option('ffmpeg-cmd','ffmpeg'), $input,$final), $conv);
					$command .=$out.';';
					}
				}
			}
		}
		/* Silently exec chained ffmpeg commands*/
		if(not_empty($command)) {	
		vibe_log('Chained cmds:' .$command. '<br>');
		//print_r($command);
			if(function_exists('shell_exec')) {
			$thisoutput = shell_exec("$command > /dev/null 2>/dev/null &");
			echo '<div class="msg-win">FFMPEG command sent to the server.</div>';
			} else {
			echo '<div class="msg-warning">shell_exec not available on the server.</div>';	
			}
		vibe_log($thisoutput);
		}


		/* End this loops item */
	} else {
		vibe_log("<br>Conversion failed for: <br><code>".$input."</code> - File not found<br>");
		echo '<div class="msg-warning">Failed! Source file doesn\'t exit.</div>';		
	}
	/* End reconversion */
}
// Query video
$video = new PHPVibe\Video\SingleVideo(intval(_get("vid")));		
if($video) {
?>

<div class="row row-setts">
<h3>Update <a href="<?php echo video_url($video->id(),$video->rawtitle()); ?>" target="_blank"><?php echo $video->title(); ?> <i class="icon icon-play-circle"></i></a></h3>
<div id="thumbus" class="row odet mtop20 text-center"> 
<?php
$showthumb = true;
if(not_empty($video->token())) {
	$vl = $video->rawthumbnails();
	if($vl) {
		foreach($vl as $vidid) {
			$cls='';	
			$vidid = str_replace(ABSPATH.'/' ,'',$vidid);
			if( $video->rawthumb() == $vidid ) {$cls='img-selected';}	
			echo '<a href="#" class="thumb-selects" data-url="'.urlencode($vidid).'">
			<img src="'.thumb_fix($vidid).'" class="'.$cls.'"/>
			</a>
			';

		}
		$showthumb = false;	
	}
}
 ?>
 <a class="show-more-thumbs tipS" title="Add or upload new thumb" id="thumbad" href="#"><i class="material-icons">add_box</i></a>
 </div>
  <script>
 $(document).ready(function() {
	 $('.img-selected').parent('a').addClass('tcc');
	  $('#thumbus > a').click(function() {
		  $('#thumbus > a').find('img').removeClass('img-selected');
		  $('#thumbus > a').removeClass('tcc');
		  
		  $(this).addClass('tcc');
		  $(this).find('img').addClass('img-selected');
                        var valoare = $(this).attr("data-url");
                        $("#remote-image").val(valoare);
                        return false;
                    }); 
	 });
 </script>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('edit-video');?>&vid=<?php echo $video->id(); ?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="edited-video" id="edited-video" value = "<?php echo $video->id(); ?>"/>
<input type="hidden" name="edited-token" id="edited-token" value = "<?php echo $video->token(); ?>"/> 
<div class="form-group form-material the-thumbnails hide">
<label class="control-label">Thumbnail <br/>
<img <?php if(!$showthumb) { echo 'class="hide"';} ?> src="<?php echo thumb_fix($video->rawthumb()); ?>" style="max-width:150px; max-height:80px; margin-bottom:5px;"/>

</label>
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
<div class="col-md-6">
<div class="form-group form-material">
<input id="remote-image" type="text" name="remote-img" class="col-md-12" placeholder="https:// Some link to image" /> 	
<span class="help-block" id="limit-text"><strong>Remote image. </strong> Leave unchanged for default / to keep the current thumbnail as default.</span>
</div>
</div>
</div>
</div>
	
</div> 
<div class="form-group form-material">
<label class="control-label"><i class="icon-bookmark"></i><?php echo _lang("Title"); ?></label>
<div class="controls">
<input type="text" name="title" class="validate[required] col-md-12" value="<?php echo $video->rawtitle(); ?>" /> 						
</div>	
</div>	
	
<div class="form-group form-material">
<label class="control-label"><?php echo _lang("Description"); ?></label>
<div class="controls">
<textarea rows="5" cols="5" name="description" class="auto validate[required] col-md-12" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 88px;"><?php echo $video->rawdescription(); ?></textarea>					
</div>	
</div>

<div class="row mtop10">
<div class="col-md-6">
	<div class="control-group blc row">
	<label class="control-label"><?php echo _lang("Category:"); ?></label>
	<div class="controls">
	<?php echo cats_select('categ','select','validate[required]', 1); ?>
	<?php  if(isset($hint)) { ?>
	  <span class="help-block"> <?php echo $hint; ?></span>
	<?php }    
	$cats = $video->rawcategories();
	if(is_array($cats)) { ?>  
	<script>
	      $(document).ready(function(){
			  
			  <?php $vals ='';			
			  foreach ( $cats as $cat ) { ;?>
	$('.select').find('option[value="<?php echo $cat['id'];?>"]').attr("selected",true);
	
			  <?php $vals .= '"'.$cat['id'].'",';
			  } ?>
			  var selectedValuesTest = [<?php echo  rtrim($vals,','); ?>];

			   $('.select').val(selectedValuesTest).trigger('change');
});
</script>
<?php  } ?>
	
	  </div>             
	  </div>
	  </div>
	  <div class="col-md-6">

	  <div class="form-group form-material">
	<label class="control-label"><?php echo _lang("Tags:"); ?></label>
	<div class="controls">
	<?php $tags = '';
	if(not_empty($video->tags())) {
	$tags =  array(); 
	$u = 0;
	foreach ($video->tags() as $tag) {
		$tags[$u] = $tag['name'];
		$u++;
	}
	
	$tags = implode(',',$tags); 
	}?>
	<input type="text" id="tags" name="tags" class="tags col-md-12" value="<?php echo $tags; ?>">
	</div>
	</div>
	</div>
	</div>
	<div class="row mtop20">
	<div class="col-md-3">
	<div class="form-group form-material">
	<label class="control-label"><?php echo _lang("Duration (in seconds):") ?></label>
	<div class="controls">
	<input type="text" id="duration" name="duration" class="validate[required] col-md-12" value="<?php echo $video->seconds(); ?>">
	</div>
	</div>
	</div>
	<div class="col-md-3">
	<div class="form-group form-material">
	<label class="control-label">Views</label>
	<div class="controls">
	<input type="text" id="views" name="views" class=" col-md-12" value="<?php echo $video->views(); ?>">
	</div>
	</div>
	</div>
	<div class="col-md-3">
	<div class="form-group form-material">
	<label class="control-label">Likes</label>
	<div class="controls">
	<input type="text" id="liked" name="likes" class=" col-md-12" value="<?php echo $video->likes(); ?>">
	</div>
	</div>
	</div>
	<div class="col-md-3">
		<div class="form-group form-material">
	<label class="control-label">Dislikes</label>
	<div class="controls">
	<input type="text" id="disliked" name="dislikes" class=" col-md-12" value="<?php echo $video->dislikes(); ?>">
	</div>
	</div>
	</div>
	</div>
    
	
	
	<div class="form-group form-material">
	<?php if($video->hassource()) { ?>
	<label class="control-label">Source / video file (link or specific)</label>
	<div class="controls">
	<input type="text" id="source" name="source" class=" col-md-12 form-control" value="<?php echo $video->source(); ?>">

	<?php } 
	if ($video->isupload()) { ?>
	<span class="help-block" id="limit-text"><code>up</code> and <code>localfile</code> are reserved keywords. Do not edit them with the link!</span>
	<?php
	 $link = site_url().get_option('mediafolder').'/';
     $pattern = "{*".$video->token()."*}";
	 $folder = ABSPATH.'/storage/'.get_option('mediafolder','media').'/';
	 $vl = glob($folder.$pattern, GLOB_BRACE);
	 echo '<div class="panel"><div class="panel-heading"><h4 class="panel-title">Video qualities</h4></div>
	 <ul class="list-group">';
	 $cn = 0;
	 foreach($vl as $vids) {
		echo '<li class="list-group-item">'; 
		$fn = str_replace($folder,'',$vids);
		echo '<a class="btn btn-xs btn-danger btn-raised confirm" href="'.admin_url('edit-video').'&vid='.$video->id().'&removefn='.$fn.'">Delete file</a>';
		echo '&nbsp;&nbsp;'.$fn.'  <span class="badge">'.FileSizeConvert(filesize($vids)).'</span> ';
		echo '</li>';
		$bq	= str_replace(array($video->token(),'.mp4','-'),'',$fn);
		if($bq > $cn) { $cn = $bq;}
	 }
	 $bq = $cn;
	 echo '<li class="list-group-item bottom20">'; 
	 echo '<p>
	 <a class="btn btn-xs btn-primary pull-right btn-raised tipS" title="Attempt to convert video to missing qualities" href="'.admin_url('edit-video').'&vid='.$video->id().'&reconvertfrom='.$video->token().'-'.$bq.'.mp4">Create missing qualities from <span class="badge">'.$bq.'p</span></a>
	 <p>';
	 echo '</li>';
	 
	 echo '</ul></div>';
	 
	} ?>
	</div>
	</div>
	<?php if($video->isremote() || $video->isembed() ) { ?>
	<div class="row">
	<div class="col-md-6">
	<div class="form-group form-material">
	<label class="control-label">Remote link</label>
	<div class="controls">
	<input type="text" id="remote" name="remote" class=" col-md-12" value="<?php echo $video->remote(); ?>" placeholder="Default: blank">
	<span class="badge"> For direct links mp4's hosted somewhere else</span>
	</div>
	</div>
	</div>
	<div class="col-md-6">
	<div class="form-group form-material">
	<label class="control-label">Embed/Iframe</label>
	<div class="controls">
	<textarea id="embed" name="embed" class="auto col-md-12" placeholder="Default: blank"><?php echo render_video(_html($video->embed())); ?></textarea>
	<span class="badge"> For shares with direct iframe code</span>
	</div>
	</div>
	</div>
	</div>
	<?php } 
	if ($video->isupload()) { ?>
	<div class="row">
	<div class="col-md-6">
	<label class="control-label">Subtitle</label>
	<div class="form-group form-material form-material-file">
		<div class="controls">
<input type="text" readonly="" />		
<input type="file" id="subtitle" name="subtitle" class="styled" />
<label class="floating-label">Choose a .srt or .vtt file</label>
</div>
</div>
<?php if($video->srt()) {
echo '<span class="badge">'.$video->srt().'</span>';
} else {
echo '<span class="badge">No subtitle attached yet</span>';
}
	?>
<br>
<p><strong>.vtt</strong> subtitles are supported in both jwPlayer and VideoJS! <strong>.srt</strong> only in jwPlayer</p>
<br>	
</div>
</div>
<?php } ?>
 <div class="row mbot20 mtop20">
	<div class="col-md-4">	
	<div class="form-group form-material">
	<label class="control-label">Premium ?</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="ispremium" class="styled" value="1" <?php if($video->ispremium()) { echo "checked"; } ?>>Premium </label>
	<label class="radio inline"><input type="radio" name="ispremium" class="styled" value="0" <?php if(!$video->ispremium() ) { echo "checked"; } ?>>Normal</label>
	</div>
	</div>
	</div>
	<div class="col-md-4">
	<div class="form-group form-material">
	<label class="control-label"><?php echo _lang("NSFW:"); ?></label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="nsfw" class="styled" value="1" <?php if($video->nsfw()) { echo "checked"; } ?>> <?php echo _lang("Not safe"); ?> </label>
	<label class="radio inline"><input type="radio" name="nsfw" class="styled" value="0" <?php if(!$video->nsfw()) { echo "checked"; } ?>><?php echo _lang("Safe"); ?></label>
	</div>
	</div>
	</div>
	<div class="col-md-4">
	<div class="control-group">
	<label class="control-label"><?php echo _lang("Visibility"); ?> </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="priv" class="styled" value="1" <?php if($video->isprivate() ) { echo "checked"; } ?>> <?php echo _lang("Users only");?> </label>
	<label class="radio inline"><input type="radio" name="priv" class="styled" value="0" <?php if($video->ispublic() ) { echo "checked"; } ?>><?php echo _lang("Everybody");?> </label>
	</div>
	</div>
	</div>
	</div>
<div class="page-footer">
<div class="row">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update video"); ?></button>	
</div>	
</div>	
</fieldset>						
</form>

<?php
} else {
echo '<div class="msg-warning">Missing video</div>';
} ?>
</div>
