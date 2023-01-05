<?php if(isset($_GET['id']) || isset($_POST['id'])) {
$id= (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];	
if(isset($_POST['name'])) {
$spot = (isset($_POST['spot']) && !nullval($_POST['spot'])) ? $_POST['spot'] : $_POST['ad_spot'];
$content = array();
$content['body'] = addslashes($_POST['content']);
$content['box'] = $_POST['box'];
$content['sec'] = $_POST['sec'];
$content['end'] = $_POST['end'];
$db->query("UPDATE ".DB_PREFIX."jads 
set `jad_start` = '".$content['sec']."',
`jad_end` = '".$content['end']."',
`jad_type` = '".$spot."',
`jad_box` = '".intval($content['box'])."',
`jad_body` = '".$content['body']."',
`jad_title` = '".$db->escape($_POST['name'])."',
`jad_pos` = '".$db->escape($_POST['pos'])."'
WHERE jad_id = $id");
//$db->debug();
echo '<div class="msg-info">Ad '.$_POST['name'].' updated</div>';
}
$adtype = array("3" => "Pre-roll","4" => "Post-Roll", "5" => "Overlay","2" => "Annotation" );
$ad = $db->get_row("select * from ".DB_PREFIX."jads WHERE jad_id = '$id'");
$atype= $ad->jad_type;
?>
<div class="row" style="margin-bottom:10px">
<h3>Editing "<?php echo _html($ad->jad_title); ?>"</h3>
</div>
<?php if($atype <> 5) { ?>
<div class="blc">
<div class="msg-info">
Selected ad spot is available only for the following players: jPlayer, jwPlayer 6, VideoJs and Flowplayer.
</div>
</div>
<?php } ?>
<div class="row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('edit-videoad');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<div class="form-group form-material">
<label class="control-label"><i class="icon-copy"></i>Title</label>
<div class="controls">
<input type="text" name="name" class="validate[required] col-md-12" value="<?php echo _html($ad->jad_title); ?>"/> 	
<span class="help-block" id="limit-text">Only visible to you.</span>						
					
</div>	
</div>	
<div class="form-group form-material">
<label class="control-label"><i class="icon-th"></i>Type</label>
<div class="controls">
<label class="radio inline"><input type="radio" name="spot" class="styled" value="5" <?php if($atype == 5) {?>checked <?php } ?>>Video Overlay</label>
<label class="radio inline"><input type="radio" name="spot" class="styled" value="2" <?php if($atype == 2) {?>checked <?php } ?>>Video Annotation</label>
<label class="radio inline"><input type="radio" name="spot" class="styled" value="3" <?php if($atype == 3) {?>checked <?php } ?>>Pre-roll</label>
<label class="radio inline"><input type="radio" name="spot" class="styled" value="4" <?php if($atype == 4) {?>checked <?php } ?>>Post-Roll</label>
</div>
</div>
<div class="form-group form-material">
<label class="control-label"><strong>The Advertisement</strong> <br><em></em>Html/Js code</label>
<div class="controls">
<textarea rows="5" cols="5" name="content" class="col-md-12" style="word-wrap: break-word; resize: horizontal; height: 88px;"><?php echo stripslashes($ad->jad_body); ?></textarea>					
<span class="help-block" id="limit-text">Place here the actual html or js code that renders your ad (for example Google Adsense code, or other provider code. Or just use plain html to create your ad's output).</span>
</div>	
</div>
<div class="form-group form-material">
	<label class="control-label"><i class="icon-fullscreen"></i>Position(Annotation only): </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="pos" class="styled" value="plTopleft" <?php if($ad->jad_pos == "plTopleft") {?>checked <?php } ?>>Top Left</label>
	<label class="radio inline"><input type="radio" name="pos" class="styled" value="plTopRight" <?php if($ad->jad_pos == "plTopRight") {?>checked <?php } ?>>Top Right</label>
	<label class="radio inline"><input type="radio" name="pos" class="styled" value="plBotleft" <?php if($ad->jad_pos == "plBotleft") {?>checked <?php } ?>>Bottom Left</label>
	<label class="radio inline"><input type="radio" name="pos" class="styled" value="plBotRight" <?php if($ad->jad_pos == "plBotRight") {?>checked <?php } ?>>Bottom Right</label>
	<span class="help-block" id="limit-text">Ad position on the player.</span>
	</div>
	</div>
<div class="form-group form-material">
	<label class="control-label"><i class="icon-fullscreen"></i>Ad start: </label>
	<div class="controls">
<input type="text" name="sec" class="validate[required] col-md-4" value="<?php echo _html($ad->jad_start); ?>"/> 	
<span class="help-block" id="limit-text">When will the ad appear on the player?</span>	
	</div>
	</div>	
<div class="form-group form-material">
	<label class="control-label"><i class="icon-fullscreen"></i>Ad duration: </label>
	<div class="controls">
<input type="text" name="end" class="validate[required] col-md-4" value="<?php echo _html($ad->jad_end); ?>"/> 	
<span class="help-block" id="limit-text">To which duration in seconds will it be kept on the player? If 0 is set, it will remain until user closes it.</span>	
	</div>
	</div>	

<div class="form-group form-material">
	<label class="control-label"><i class="icon-fullscreen"></i>Container design (Overlay only): </label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="box" class="styled" value="0" <?php if($ad->jad_box == 0) {?>checked <?php } ?>>Transparent</label>
	<label class="radio inline"><input type="radio" name="box" class="styled" value="1" <?php if($ad->jad_box == 1) {?>checked <?php } ?>>Black & Boxed </label>
	</div>
	</div>	
	
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Modify Ad</button>	
</div>	
</fieldset>						
</form>
</div>
<?php } else {
echo 'Something went wrong, id is missing!';	
} ?>
