<?php $tp = ABSPATH.'/storage/'.get_option('tmp-folder','rawmedia')."/";
$ts = site_url().'storage/'.get_option('tmp-folder','rawmedia')."/";
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
remove_file($tp.$del);
}
}
?>
<div class="row">
<h3>RawMedia Folder</h3>				
</div>
<?php

$videos = glob($tp."*.{".get_option('alext')."}", GLOB_BRACE);
if($videos) {
?>
<form class="form-horizontal styled" action="<?php echo admin_url('rawmedia');?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">

<div class="cleafix full"></div>
<fieldset>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
<th> <div class="checkbox-custom checkbox-danger"> <input type="checkbox" name="checkRows" class="check-all" /> <label for="checkRows"></label> </div>  </th>
                                  <th>File</th> 
                                  <th>Download</th> 								  
								  <th><button class="btn btn-large btn-danger" type="submit">Delete selected files</button></th>
                              </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($videos as $video) { ?>
                              <tr>
                                  <td><input type="checkbox" name="checkRow[]" value="<?php echo str_replace($tp,"",$video);?>" class="styled" /></td>
                                  <td><?php echo str_replace($tp,"",$video);?></td>
								  <td><a href="<?php echo str_replace($tp,$ts,$video);?>" class="btn btn-default btn-small">Download</a></td>
							 <td><?php echo date ("F d Y H:i:s", filemtime($video));?></td>
							 </tr>
							  <?php } ?>
						</tbody>  
</table>
</div>						
</fieldset>					
</form>
<?php 
}else {
echo '<div class="msg-note">Nothing here yet.</div>';
}

 ?>
