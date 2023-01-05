<?php 
$all_options = get_all_options();
?>

<div class="row">
<h3>JPlayer Configuration</h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('jplayer');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="update_options_now" class="hide" value="1" /> 	
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-cloud-upload"></i>Video path</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="hide-mp4" class="styled" value="0" <?php if(get_option('hide-mp4',0) == 0 ) { echo "checked"; } ?>>Real link</label>
	<label class="radio inline"><input type="radio" name="hide-mp4" class="styled" value="1" <?php if(get_option('hide-mp4',0) == 1 ) { echo "checked"; } ?>>Hide with PHP</label>
	<span class="help-block" id="limit-text">Note: Hiding it with PHP may create issues on some players. Please test which mode you prefer.</span>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-play"></i>Default Player <br /> <i>HTML5</i></label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="choosen-player" class="styled" value="5" <?php if(get_option('choosen-player') == 5 ) { echo "checked"; } ?>>Easy Video Player</label>
	<label class="radio inline"><input type="radio" name="choosen-player" class="styled" value="4" <?php if(get_option('choosen-player') == 4 ) { echo "checked"; } ?>>PHPVibe's Chameleon</label>
	<label class="radio inline"><input type="radio" name="choosen-player" class="styled" value="1" <?php if(get_option('choosen-player') == 1 ) { echo "checked"; } ?>>JwPlayer</i></label>
	<label class="radio inline"><input type="radio" name="choosen-player" class="styled" value="2" <?php if(get_option('choosen-player') == 2 ) { echo "checked"; } ?>>FlowPlayer HTML5</label>
	<label class="radio inline"><input type="radio" name="choosen-player" class="styled" value="3" <?php if(get_option('choosen-player') == 3 ) { echo "checked"; } ?>>MediaElement</label>
	<span class="help-block" id="limit-text">Which player should be loaded for mobile supported files (.mp4, etc)? JwPlayer is loaded for the rest.</span>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-facetime-video"></i>Remote Player <br /> <i>(Linked Videos)</i> </label>
	<div class="controls">
		<label class="radio inline"><input type="radio" name="remote-player" class="styled" value="5" <?php if(get_option('remote-player') == 5 ) { echo "checked"; } ?>>Easy Video Player</label>
	<label class="radio inline"><input type="radio" name="remote-player" class="styled" value="4" <?php if(get_option('remote-player') == 4 ) { echo "checked"; } ?>>Chameleon</label>
	<label class="radio inline"><input type="radio" name="remote-player" class="styled" value="1" <?php if(get_option('remote-player') == 1 ) { echo "checked"; } ?>>JwPlayer</i></label>
	<label class="radio inline"><input type="radio" name="remote-player" class="styled" value="2" <?php if(get_option('remote-player') == 2 ) { echo "checked"; } ?>>FlowPlayer HTML5</label>
	<label class="radio inline"><input type="radio" name="remote-player" class="styled" value="3" <?php if(get_option('remote-player') == 3 ) { echo "checked"; } ?>>MediaElement</label>
	<span class="help-block" id="limit-text">Which player should be loaded for mobile supported files (.mp4, etc)? JwPlayer is loaded for the rest.</span>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-youtube"></i>Youtube videos</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="youtube-player" class="styled" value="2" <?php if(get_option('youtube-player') == 2 ) { echo "checked"; } ?>>Use JwPlayer</label>
	<label class="radio inline"><input type="radio" name="youtube-player" class="styled" value="0" <?php if(get_option('youtube-player') == 0 ) { echo "checked"; } ?>>Youtube's Player</label>
			<label class="radio inline"><input type="radio" name="youtube-player" class="styled" value="3" <?php if(get_option('youtube-player') == 3 ) { echo "checked"; } ?>>Easy Video Player</label>

	<span class="help-block" id="limit-text">Which player do you wish to handle Youtube?</span>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label"><i class="icon-film"></i>JwPlayer version</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="jwp_version" class="styled" value="5" <?php if(get_option('jwp_version') == 5 ) { echo "checked"; } ?>>Old 5</label>
	<label class="radio inline"><input type="radio" name="jwp_version" class="styled" value="6" <?php if(get_option('jwp_version') == 6 ) { echo "checked"; } ?>>New 6</label>
	<span class="help-block" id="limit-text">Which version of JwPlayer do you wish to use? Note: Only 6 has mobile/pad support</span>
	</div>
	</div>
	<div class="form-group form-material">
<label class="control-label"><i class="icon-fast-forward"></i>Video page settings</label>
 <div class="controls">
<div class="row">
<div class="col-md-3">
<input type="text" name="video-width" class="col-md-12" value="<?php echo get_option('video-width'); ?>"><span class="help-block">Default video <strong>width</strong> </span>
</div>
<div class="col-md-3">
<input type="text" name="video-height" class="col-md-12" value="<?php echo get_option('video-height'); ?>"><span class="help-block align-center">Default video <strong>height</strong></span>
</div>
<div class="col-md-3">
<input type="text" name="related-nr" class="col-md-12" value="<?php echo get_option('related-nr'); ?>"><span class="help-block align-center">Number of <strong> related videos</strong></span>
</div>
<div class="col-md-3">
<input type="text" name="jwkey" class="col-md-12" value="<?php echo get_option('jwkey'); ?>"><span class="help-block align-right">Optional <strong>JwPlayer key</strong> (mostly if PRO)</span>
</div>
</div>
</div>
</div>
		<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update settings"); ?></button>	
</div>	
</fieldset>						
</form>
</div>
