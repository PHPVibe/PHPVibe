<?php
if(isset($_POST['opt']) && isset($_POST['val']) && !empty($_POST['opt'])){
add_option($_POST['opt'],$_POST['val']);
echo '<div class="msg-info">'.$_POST['opt'].' added.</div>';
$db->clean_cache();
}
$list_options = $db->get_results("SELECT option_name,autoload from ".DB_PREFIX."options limit 0,10000000");
$all_options = get_all_options();
?>
<div class="cleafix row">
	<h3>All options </h3>
	<div class="panel">
	<div class="panel-body">
<form id="validate" class="pull-right form-inline styled" action="<?php echo admin_url('options');?>" enctype="multipart/form-data" method="post">

<div class="form-group form-material mbot20">
<input type="text" name="opt" class="input-small" placeholder="Option name">
<input type="text" name="val" class="input-small" placeholder="Option value">
<button type="submit" class="btn btn-primary">Add option</button>
</div>
</form>
<div class="cleafix row">

<div class="table-overflow block full">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
                                 
                                  <th>Option name</th>
                                  <th>Option value</th>
								  <th>Autoload</th>
                               </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($list_options as $op) { ?>
                              <tr>
                                   <td><?php echo stripslashes($op->option_name); ?></td>
                                  <td class="longertd"><?php echo get_option($op->option_name); ?></td>
                                  <td><?php echo $op->autoload; ?></td>                                  
                              </tr>
							  <?php } ?>
						</tbody>  
</table>
</div>						
</div>						
<?php ?>

</div>						
</div>	
