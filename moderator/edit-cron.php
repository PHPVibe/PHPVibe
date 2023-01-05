<?php $the_cron = (isset($_POST["id"])) ? $_POST["id"] : escape($_GET['id']);
$the_cron = intval($the_cron);
if($the_cron) {
//var_dump($lang_terms);
if(isset($_POST["id"])) {
$the_cron = $_POST["id"];
$ar = array();
foreach ($_POST["value"] as $key=>$value) {
$ar[$key] = $value;
}
$values = maybe_serialize($ar);
//var_dump($_POST);
$db->query("UPDATE ".DB_PREFIX."crons SET cron_name='".toDb($_POST['cron_name'])."', cron_period='".intval($_POST['cron_period'])."' , cron_pages='".intval($_POST['cron_pages'])."' ,cron_value='".$values."'  WHERE cron_id = '".$the_cron ."'");
echo '<div class="msg-info">Cron '.$_POST['cron_name'].' was updated.</div>';
cron_fastest($_POST['cron_period']);
}
$cron = $db->get_row("select * from ".DB_PREFIX."crons where cron_id =' ".$the_cron."'");
if($cron) {
$data = maybe_unserialize($cron->cron_value);
?>
<div class="cleafix row">
<h3>Cron : <?php echo $cron->cron_name; ?></h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('edit-cron');?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="id" class="hide" value="<?php echo $the_cron; ?>" readonly /> 
<div class="form-group form-material">
<label class="control-label"><i class="icon-truck"></i>Cron type</label>
<div class="controls">
<input type="text" name="cron_type" class=" col-md-4" value="<?php echo $cron->cron_type; ?>" readonly /> 
</div>	
</div>
<div class="form-group form-material">
<label class="control-label"><i class="icon-font"></i>Cron name</label>
<div class="controls">
<input type="text" name="cron_name" class="col-md-12" value="<?php echo $cron->cron_name; ?>" /> 
</div>	
</div>
<div class="form-group form-material">
<label class="control-label"><i class="icon-bar-chart"></i>Cron period</label>
<div class="controls">
<input type="text" name="cron_period" class=" col-md-4" value="<?php echo $cron->cron_period; ?>" /> 
</div>	
</div>
<div class="form-group form-material">
<label class="control-label"><i class="icon-copy"></i>Cron pages</label>
<div class="controls">
<input type="text" name="cron_pages" class=" col-md-4" value="<?php echo $cron->cron_pages; ?>" /> 
</div>	
</div>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
                                 
                                  <th>Input</th>
                                  <th >Value</th>
								  
                               </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($data as $t=>$tr) {
                             if($t !== "language-name") {
						  ?>
                              <tr>
                                   <td><?php echo stripslashes($t); ?></td>
                                  <td>
								  <input type="text" name="value[<?php echo stripslashes($t); ?>]" class="col-md-12" value="<?php echo stripslashes($tr); ?>" /> 	
								  </td>
                                                                
                              </tr>
							  <?php }} ?>
						</tbody>  
</table>
</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Save changes</button>	
</div>	
</form>						
</div>						
<?php  } else {
echo '<div class="msg-warning">Invalid id.</div>';
}
} else {
echo '<div class="msg-warning">Missing the cron id.</div>';
}

?>
