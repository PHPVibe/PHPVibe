<?php $adtype = array("3" => "Pre-roll","4" => "Post-Roll", "5" => "Overlay","2" => "Annotation" );
$adbox = array("0" => "Transparent","1" => "Boxed" );
if(isset($_GET['del-ad'])) {
$db->query("DELETE from ".DB_PREFIX."jads where jad_id = '".intval($_GET['del-ad'])."' ");
echo '<div class="msg-info">Ad deleted</div>';
} 

$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."jads ");
if($count->nr > 0) {
$ads = $db->get_results("select * from ".DB_PREFIX."jads ORDER BY jad_id DESC ".this_limit()."");
} else {
$ads = false;
}

?>
<div class="row" style="margin-bottom:20px">
<h3>OverVideo ads</h3>
<div class="pull-right">	
<?php echo'<a class="btn btn-info btn-outline" style="margin-right:10px" href="'.admin_url('create-videoad').'&type=1"><i class="icon-plus"></i>Create Overlay</a>'; ?>			
<?php echo'<a class="btn btn-primary btn-outline" style="margin-right:10px" href="'.admin_url('create-videoad').'&type=2"><i class="icon-plus"></i>Create Annotation</a>'; ?>
<?php echo'<a class="btn btn-success btn-outline" style="margin-right:10px" href="'.admin_url('create-videoad').'&type=3"><i class="icon-plus"></i>Create Pre/Post Roll</a>'; ?>
</div>
</div>
<?php
if($ads) {
$ps = admin_url('videoads').'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);

?><div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
							  <th>Id</th>
                                  <th>Title</th>                                 
							      <th>Type</th>
								  <th>Design</th>
								    <th>Start</th>
									 <th>End</th>
								   <th class="lastone">Opt.</th>
                                  </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($ads as $ad) { ?>
						   
                              <tr>
							  <td><?php echo _html($ad->jad_id); ?></td>
                                  <td><?php echo _html($ad->jad_title); ?></td>								  							
                                  <td><?php echo $adtype[$ad->jad_type]; ?></td>
								  <td>
								  <?php if($ad->jad_type == 5) {
								  echo $adbox[intval($ad->jad_box)];								 
								  } else {
								  echo "-";
								  }
								  ?>
								  </td>
								  <td>
								   <?php
								  echo $ad->jad_start;								  
								  ?>
								   </td>
								  <td>
								   <?php
								   echo $ad->jad_end;								  
								  ?>
								   </td>
								  <td class="lastone">
								  <div class="btn-group">
								 <a class="btn btn-default btn-sm btn-outline tipS" href="<?php echo admin_url('edit-videoad');?>&id=<?php echo $ad->jad_id;?>" title="Edit <?php echo _html($ad->jad_title); ?>"><i class="icon-pencil" style=""></i></a>
								  <a class="btn btn-danger btn-sm btn-outline tipS" href="<?php echo $ps;?>&del-ad=<?php echo $ad->jad_id;?>" title="Delete <?php echo _html($ad->jad_title); ?>"><i class="icon-trash" style=""></i></a>
                                 </div>
								</td>
                              </tr>
							 
							  
							   <?php } ?>
						</tbody>  
</table>
</div>						

<?php  $a->show_pages($ps);
}else {
echo '<div class="msg-note">Nothing here yet.</div>';
}

 ?>
