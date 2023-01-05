<?php if(isset($_GET['ac']) && $_GET['ac'] ="remove-logo"){
update_option('player-logo', '');
 $db->clean_cache();
}
if(isset($_POST['update_options_now'])){
foreach($_POST as $key=>$value)
{
if(($key !== "player-logo") && !empty($key)) {
	if($key == "vjimaads") {
	update_option($key, rawurlencode($value));	
	} else {
  update_option($key, $value);
	}
}
}
  echo '<div class="msg-info">Configuration options have been updated.</div>';
  

//Set logo
if(isset($_FILES['player-logo']) && !empty($_FILES['player-logo']['name'])){
$nn = explode('.', $_FILES['player-logo']['name']);	
$extension = end($nn);
$thumb = ABSPATH.'/storage/uploads/'.nice_url($_FILES['player-logo']['name']).uniqid().'.'.$extension;
if (move_uploaded_file($_FILES['player-logo']['tmp_name'], $thumb)) {
     $sthumb = str_replace(ABSPATH.'/' ,'',$thumb);
    update_option('player-logo', $sthumb);
	 
	} else {
	echo '<div class="msg-warning">Logo upload failed.</div>';
	}
	
}
$db->clean_cache();
}

$all_options = get_all_options();
include_once('setheader.php');
?>
<div class="row">

<div class="row-setts panel-body">
<h3>Players</h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('players');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="update_options_now" class="hide" value="1" /> 
<label class="control-label"><i class="icon-picture"></i>Player logo</label>	
<?php if(get_option('player-logo')) { ?><div class="block text-center bottom20"><img src="<?php echo thumb_fix(get_option('player-logo')); ?>"/> <br><a class="btn btn-xs btn-danger mtop20 mbottom20" href="<?php echo admin_url('players');?>&ac=remove-logo">Remove</a></div><?php } ?>

<div class="form-group form-material form-material-file">
<input type="text" class="form-control empty" readonly="" />
<input type="file" id="player-logo" name="player-logo" class="styled" />
<label class="floating-label">Choose a logo</label>
<div class="controls">

<span class="help-block" id="limit-text">The logo on the player.</span>
</div>	
</div>
	
	<div class="form-group ">
	<label class="control-label"><i class="icon-cloud-upload"></i>Video path</label>
	<div class="controls">
	<label class="radio"><input type="radio" name="hide-mp4" class="styled" value="0" <?php if(get_option('hide-mp4',0) == 0 ) { echo "checked"; } ?>>Real link to .mp4 file</label>
	<label class="radio"><input type="radio" name="hide-mp4" class="styled" value="1" <?php if(get_option('hide-mp4',0) == 1 ) { echo "checked"; } ?>>Hide link with PHP (Server heavy!)</label>
	<span class="help-block" id="limit-text"><code>Hiding with PHP has issues on some devices and/or servers</code>. <br> Please test first!</span>
	</div>
	</div>
	<div class="form-group ">
	<label class="control-label"><i class="icon-play"></i>Default Player <br /> <i>HTML5</i></label>
	<div class="controls">
	<label class="radio "><input type="radio" name="choosen-player" class="styled" value="6" <?php if(get_option('choosen-player') == 6 ) { echo "checked"; } ?>><strong>VibesJS</strong> <small>a VideoJS +</small></label>
	<label class="radio "><input type="radio" name="choosen-player" class="styled" value="1" <?php if(get_option('choosen-player') == 1 ) { echo "checked"; } ?>>JwPlayer 7</label>
	<label class="radio "><input type="radio" name="choosen-player" class="styled" value="2" <?php if(get_option('choosen-player') == 2 ) { echo "checked"; } ?>>FlowPlayer 7</label>
	<label class="radio "><input type="radio" name="choosen-player" class="styled" value="3" <?php if(get_option('choosen-player') == 3 ) { echo "checked"; } ?>>jPlayer</label>
	<span class="help-block" id="limit-text">Which player should be loaded for mobile supported files (.mp4, etc)? JwPlayer is loaded for the rest.</span>
	</div>
	</div>
	<div class="form-group ">
	<label class="control-label"><i class="icon-link"></i>Remote Player <br /> <i>Linked Vids</i> </label>
	<div class="controls">
	<label class="radio"><input type="radio" name="remote-player" class="styled" value="6" <?php if(get_option('remote-player') == 6 ) { echo "checked"; } ?>><strong>VibesJS</strong></label>
	<label class="radio"><input type="radio" name="remote-player" class="styled" value="1" <?php if(get_option('remote-player') == 1 ) { echo "checked"; } ?>>JwPlayer 7</label>
	<label class="radio"><input type="radio" name="remote-player" class="styled" value="2" <?php if(get_option('remote-player') == 2 ) { echo "checked"; } ?>>FlowPlayer 7</label>
	<label class="radio"><input type="radio" name="remote-player" class="styled" value="3" <?php if(get_option('remote-player') == 3 ) { echo "checked"; } ?>>jPlayer</label>
	<span class="help-block" id="limit-text">Which player should be loaded for mobile supported files (.mp4, .mp3, etc)? JwPlayer is loaded for the rest.</span>
	</div>
	</div>
	<div class="form-group ">
	<label class="control-label"><i class="icon-youtube"></i>Youtube videos</label>
	<div class="controls">
	<label class="radio"><input type="radio" name="youtube-player" class="styled" value="2" <?php if(get_option('youtube-player') == 2 ) { echo "checked"; } ?>>Use JwPlayer 7</label>
	<label class="radio"><input type="radio" name="youtube-player" class="styled" value="0" <?php if(get_option('youtube-player') == 0 ) { echo "checked"; } ?>>Youtube's Player</label>
	<label class="radio"><input type="radio" name="youtube-player" class="styled" value="3" <?php if(get_option('youtube-player') == 3 ) { echo "checked"; } ?>><strong>VibesJS</strong></label>

	<span class="help-block" id="limit-text">Which player do you wish to play Youtube videos in?</span>
	</div>
	</div>
	<div class="form-group ">
<label class="control-label"><i class="icon-fast-forward"></i>Video page settings</label>
 <div class="controls">
<div class="row">
<div class="col-md-3">
<input type="text" name="related-nr" class="col-md-12" value="<?php echo get_option('related-nr'); ?>"><span class="help-block align-center">Number of <strong> related videos</strong></span>
</div>
<div class="col-md-3">
<input type="text" name="jwkey" class="col-md-12" value="<?php echo get_option('jwkey'); ?>"><span class="help-block align-right"><strong>JwPlayer key</strong> <small>External product!</small> <a href="https://dashboard.jwplayer.com/" target="_blank"> Get yours ></a></span>
</div>
</div>
</div>
</div>
<div class="form-group ">
<label class="control-label"><i class="icon-fast-forward"></i>VAST/VPAID Ad tag url</label>
 <div class="controls">
<div class="row">
<div class="col-md-12">
<input type="text" name="vjimaads" class="col-md-12" value="<?php echo get_option('vjimaads'); ?>">
<span class="help-block">Load VAST/VPAID ads to player. <strong>For now: VideoJS supports this by default.</strong>
<br> JwPlayer sells the <a href="https://www.jwplayer.com/video-solutions/video-advertising/" target="_blank"> JwPlayer ads edition</a>
and Flowplayer has a <a href="https://flowplayer.com/docs/vast.html" target="_blank">commercial (subscription based) plugin</a>.
</span>
</div>

</div>
</div>
</div>
<div class="row page-footer">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update settings"); ?></button>	
</div>	
</fieldset>						
</form>
</div>
</div>