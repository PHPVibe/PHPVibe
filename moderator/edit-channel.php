<?php
if(isset($_POST['play-name'])) {
if(isset($_FILES['play-img']) && !empty($_FILES['play-img']['name'])){$thumb = ABSPATH.'/storage/uploads/'.$_FILES['play-img']['name'];if (move_uploaded_file($_FILES['play-img']['tmp_name'], $thumb)) {     $picture = str_replace(ABSPATH.'/' ,'',$thumb);   	$db->query("UPDATE  ".DB_PREFIX."channels SET picture='".toDb($picture)."' WHERE cat_id= '".intval($_GET['id'])."'");	} else {	echo '<div class="msg-warning">Image upload failed.</div>';	}	}

$ch = 0;
if($_POST['subz'] > 1) {
$ch = $_POST['categ'.$_POST['type']];if(is_array($ch)) { $ch = $ch[0];}
}
$db->query("UPDATE ".DB_PREFIX."channels SET type ='".$_POST['type']."', child_of ='".intval($ch)."', sub ='".$_POST['sub']."', cat_name ='".toDb($_POST['play-name'])."', cat_desc = '".toDb($_POST['play-desc'])."' WHERE cat_id= '".intval($_GET['id'])."'");

echo '<div class="msg-info">Channel '.$_POST['play-name'].' updated</div>';
}$db->clean_cache();
$ch = $db->get_row("SELECT * FROM ".DB_PREFIX."channels where cat_id ='".intval($_GET['id'])."'");
if($ch) {
?>

<div class="row">
<script>
   
 $(document).ready(function(){
  $('#chz,#a2,#a3').hide();
    $('.trigger').on('ifChecked', function(event){
        $('#a1,#a2,#a3').hide();
        $('#a' + $(this).data('rel')).show();
    });
	 $('.shs').on('ifChecked', function(event){
        if ($(this).data('rel') === 1) {
		$('#chz').hide();
		} else {
		$('#chz').show();
		}
    });
	<?php if (intval($ch->child_of) > 0) { ?>
	
	var num = <?php echo $ch->child_of;?>;
    $("div#a<?php echo $ch->type;?> select.select option").each(function(){
        if($(this).val()==num){ 
            $(this).attr("selected","selected");    
        }
		
    });
	$(".select").select2();
	$('#chz').show();
	$('#a<?php echo $ch->type;?>').show();
	
	
	<?php } ?>

});
</script>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('edit-channel');?>&id=<?php echo intval($_GET['id']); ?>" enctype="multipart/form-data" method="post">
<fieldset>
<div class="form-group form-material">
<label class="control-label"><i class="icon-bookmark"></i><?php echo _lang("Title"); ?></label>
<div class="controls">
<input type="text" name="play-name" class="validate[required] col-md-12" value="<?php echo $ch->cat_name; ?>" /> 						
</div>	
</div>	

<div class="form-group form-material">
	<label class="control-label"><i class="icon-user"></i>Is this a sub-channel?</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="subz" data-rel="1" class="styled shs" value="1" <?php if (intval($ch->child_of) < 1) { ?>checked <?php } ?>>No</label>
	<label class="radio inline"><input type="radio" name="subz" data-rel="2" class="styled shs" value="2" <?php if (intval($ch->child_of) > 0) { ?>checked <?php } ?>>Yes</label>
	</div>
	</div>		
<div class="form-group form-material">
	<label class="control-label"><i class="icon-user"></i>Accepted media</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="type" data-rel="1" class="styled trigger" value="1" <?php if (intval($ch->type) == 1) { ?>checked <?php } ?>>Video</label>
	<label class="radio inline"><input type="radio" name="type" data-rel="2" class="styled trigger" value="2" <?php if (intval($ch->type) == 2) { ?>checked <?php } ?>>Music</label>
	<label class="radio inline"><input type="radio" name="type" data-rel="3" class="styled trigger" value="3" <?php if (intval($ch->type) == 3) { ?>checked <?php } ?>>Images</label>
	</div>
	</div>	
	
<div id="chz" class="control-group row">
	<label class="control-label">Parent channel:</label>
	<div class="controls">
	<div id="a1" class="sel">
	<?php echo cats_select("categ1","select","");?>
	<span class="help-block" id="limit-text">FOR VIDEOS</span>
	  </div> 
<div id="a2" class="sel">
	<?php echo cats_select('categ2',"select","","2");?>
	<span class="help-block" id="limit-text">FOR MUSIC</span>
	  </div>  
<div id="a3" class="sel">
	<?php echo cats_select('categ3',"select","","3");?>
	<span class="help-block" id="limit-text">FOR IMAGES</span>
	  </div>  	
  
	  </div>
	  </div>
	

<div class="form-group form-material">
	<label class="control-label"><i class="icon-cloud-upload"></i>Sharing to this channel:</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="sub" class="styled" value="1" <?php if (intval($ch->sub) == 1) { ?>checked <?php } ?>>Public (Every registred user)</label>
	<label class="radio inline"><input type="radio" name="sub" class="styled" value="0" <?php if (intval($ch->sub) <> 1) { ?>checked <?php } ?>>Private (Mods & Admins)</label>

	</div>
	</div>	
<div class="form-group form-material">
<label class="control-label"><?php echo _lang("Description"); ?></label>
<div class="controls">
<textarea rows="5" cols="5" name="play-desc" class="auto col-md-12" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 88px;"><?php echo $ch->cat_desc; ?></textarea>					
</div>	
</div>
<label class="control-label"><?php echo _lang("Channel image"); ?></label>
<div class="form-group form-material form-material-file">
<div class="controls">
<input type="text" class="form-control empty" readonly=""/>
<input type="file" id="play-img" name="play-img" class="styled" />
<label class="floating-label">Browse...</label>
<span class="help-block" id="limit-text"><?php echo _lang("Select only if you wish to change the image");?></span>
</div>	
</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update channel"); ?></button>	
</div>	
</fieldset>						
</form>
</div>
<?php } else {
echo '<div class="msg-warning">Channel '.$_GET['id'].' not found</div>';
} ?>
