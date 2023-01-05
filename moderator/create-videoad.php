<?php $atype= (isset($_GET['type'])) ? $_GET['type'] : 1;
if(isset($_POST['name'])) {
$spot = (isset($_POST['spot']) && !nullval($_POST['spot'])) ? $_POST['spot'] : $_POST['ad_spot'];
$content = array();
$content['body'] = addslashes($_POST['content']);
$content['box'] = $_POST['box'];
$content['sec'] = $_POST['sec'];
$content['end'] = $_POST['end'];
$db->query("INSERT INTO ".DB_PREFIX."jads (`jad_start`,`jad_end`,`jad_type`, `jad_box`, `jad_body`, `jad_title`, `jad_pos`) VALUES
('".$content['sec']."','".$content['end']."','".$spot."', '".intval($content['box'])."', '".$content['body']."', '".$db->escape($_POST['name'])."', '".$db->escape($_POST['pos'])."')
");
echo '<div class="msg-info">Ad '.$_POST['name'].' created</div>';
 $db->clean_cache();
}
$adtype = array("3" => "Pre/Post-Roll","1" => "Overlay","2" => "Annotation" );

?>
<div class="row" style="margin-bottom:20px">
<h3>+ OnVideo <?php echo $adtype[$atype] ?></h3>
<div class="pull-right">	
<?php echo'<a class="btn btn-info btn-outline" style="margin-right:10px" href="'.admin_url('create-videoad').'&type=1"><i class="icon-plus"></i>Create Overlay</a>'; ?>			
<?php echo'<a class="btn btn-primary btn-outline" style="margin-right:10px" href="'.admin_url('create-videoad').'&type=2"><i class="icon-plus"></i>Create Annotation</a>'; ?>
<?php echo'<a class="btn btn-success btn-outline" style="margin-right:10px" href="'.admin_url('create-videoad').'&type=3"><i class="icon-plus"></i>Create Pre/Post Roll</a>'; ?></div>
</div>
<?php if($atype > 1) { ?>
<div class="blc">
<div class="msg-info">
Selected ad spot is available only for the following players: jPlayer, jwPlayer, VideoJs and Flowplayer.
</div>
</div>
<?php } ?>
<div class="row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('create-videoad');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="type" value="1">
<div class="form-group form-material">
<label class="control-label"><i class="icon-copy"></i>Title</label>
<div class="controls">
<input type="text" name="name" class="validate[required] col-md-12"/> 	
<span class="help-block" id="limit-text">Only visible to you.</span>						
					
</div>	
</div>	
<?php if($atype > 2 ) { ?>
<div class="form-group form-material">
<label class="control-label"><i class="icon-th"></i>Type</label>
<div class="controls">
<label class="radio inline"><input type="radio" name="spot" class="styled" value="3">Pre-roll</label>
<label class="radio inline"><input type="radio" name="spot" class="styled" value="4">Post-Roll</label>
</div>	
</div>	
<?php } else { ?>
<div class="hide">
<label class="radio inline"><input type="radio" name="spot" class="styled" value="5" <?php if($atype == 1) {?>checked <?php } ?>>Video Overlay</label>
<label class="radio inline"><input type="radio" name="spot" class="styled" value="2" <?php if($atype == 2) {?>checked <?php } ?>>Video Annotation</label>
</div>
<?php } ?>
<div class="form-group form-material">
<label class="control-label"><i class="icon-code"></i><strong>The Advertisement.</strong> <em></em>Html/Js code</label>
<div class="controls">
<textarea rows="5" cols="5" name="content" class="col-md-12" style="word-wrap: break-word; resize: horizontal; height: 88px;"></textarea>					
<span class="help-block" id="limit-text">Place here the actual html or js code that renders your ad (for example Google Adsense code, or other provider code. Or just use plain html to create your ad's output).</span>
</div>	
</div>
<?php if($atype == 2) {?>
<div class="form-group form-material">
	<label class="control-label"><i class="icon-arrows"></i>Position: </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="pos" class="styled" value="plTopleft" checked>Top Left</label>
	<label class="radio inline"><input type="radio" name="pos" class="styled" value="plTopRight">Top Right</label>
	<label class="radio inline"><input type="radio" name="pos" class="styled" value="plBotleft">Bottom Left</label>
	<label class="radio inline"><input type="radio" name="pos" class="styled" value="plBotRight">Bottom Right</label>
	<span class="help-block" id="limit-text">Ad position on the player.</span>
	</div>
	</div>
<?php } else {
echo '<input type="hidden" name="pos" value=""/>';
}	?>	
<div class="row">
<div class="col-md-4 col-xs-12">
<div class="form-group form-material">
	<label class="control-label"><i class="icon-clock-o"></i>Ad start: </label>
	<div class="controls">
<input type="text" name="sec" class="validate[required] col-md-4" value="10"/> 	
<span class="help-block" id="limit-text">When will the ad appear on the player (seconds)?</span>	
	</div>
	</div>	
	</div>
<div class="col-md-4 col-xs-12">	
<div class="form-group form-material">
	<label class="control-label"><i class="icon-clock-o"></i>Ad duration: </label>
	<div class="controls">
<input type="text" name="end" class="validate[required] col-md-4" value="25"/> 	
<span class="help-block" id="limit-text">How many seconds will it be keept? If 0 is set, it will remain until user closes it.</span>	
	</div>
	</div>
</div>	
</div>	
<?php if($atype == 1) {?>	
<div class="form-group form-material">
	<label class="control-label"><i class="icon-fullscreen"></i>Container design: </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="box" class="styled" value="0" checked>Transparent</label>
	<label class="radio inline"><input type="radio" name="box" class="styled" value="1">Black & Boxed </label>
	</div>
	</div>	
<?php } else {
echo '<input type="hidden" name="box" value=""/>';
}	?>	
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Create Ad</button>	
</div>	
</fieldset>						
</form>
</div>
