<?php  $the = $_GET;
$the['bpp'] = 15;
if(!isset($the['auto'])) { $the['auto'] = 0;}
if(!isset($the['imode']) || nullval($the['imode'])) { $the['imode'] = 2;}
$auto = ($the['imode'] < 2) ? true : false;
$all = ($the['auto'] > 0) ? true : false;
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $raw) {
$video = base64_decode($raw);
youtube_import(maybe_unserialize($video), intval($the['categ']),$the['owner']);
}
echo '<div class="msg-info">Selected videos have been imported.</div>';
}
$youtube = new Youtube(array('key' => get_option('youtubekey')));
$params = array(
    'maxResults'    => $the['bpp']
);

if(!isset($the['name'])) {
$playlist = $youtube->getPlaylistById($the['pID']);
$the['name'] = $playlist->snippet->title.' <em>by '.$playlist->snippet->channelTitle.'</em>';
}
//echo '<h1> '.$the['name'].'</h1>';

$params['pageToken'] = $youtube->thisToken($params['maxResults'],this_page()); 
$search = $youtube->getPlaylistItemsByPlaylistId($the['pID'],$the['bpp']);	
if(!$auto) { ?>
<form id="validate" class="form-horizontal styled" action="<?php echo canonical();?>" enctype="multipart/form-data" method="post">
<?php } ?>
<div class="panel top10 multicheck">
<div class="panel-heading">
<h3 class="panel-title">
<?php echo ucfirst($the['name']); ?>
</h3> 
<?php if(!$auto) { ?>
<ul class="panel-actions">
<li>
<li>
<small>Select all</small>
</li>
<li>
<div class="checkbox-custom checkbox-danger"> <input type="checkbox" name="checkRows" class="check-all" /> <label for="checkRows"></label> </div>
</li>
<li>
<button class="btn btn-large btn-primary btn-raised" type="submit">Save all selected</button>
</li>
</ul>
<?php } ?>
</div>
<div class="panel-body" style="border-top: 1px solid #e4eaec; padding-top:15px;">
						  <div class="multilist">
<ul class="list-group">						  
						  <?php 
	if(!$all) {					  
						  
						  if($search) {
						  foreach ($search as $vd) {
$video = $youtube->Single($vd->contentDetails->videoId);
						   ?>
                                <li class="list-group-item">
								 <div class="row">
							 <div class="inline-block img-hold">
							 <div class="inline-block right20 img-checker">
							  <?php if(!$auto) { ?>	
                            <span class="pull-left mg-t-xs mg-r-md top20"><input type="checkbox" name="checkRow[]" value="<?php echo base64_encode(maybe_serialize($video)); ?>" class="styled" /></span>
                            <?php } ?>	
							<span class="pull-left mg-t-xs mg-r-md">
							<img src="<?php echo $video['thumb']; ?>" class="row-image">
							</span>
								  </div>
                                   <div class="inline-block right20 img-txt">
								 <h4 class="mtop10"> 
								   <?php 
								   //echo "<pre>";
								   //print_r($video);
								  // echo "</pre>";
									if($auto) {
									if(isset($the['allowduplicates']) && ($the['allowduplicates'] > 0)) {
								   echo '<span class="label label-success mright10">Imported</span>';
								  youtube_import($video,$the['categ'],$the['owner'] );
								  } else {
                                   if(ytExists($video['videoid'])) {
								    echo '<span class="label label-danger mright10">Already available</span>';
								   } else {
								    echo '<span class="label label-success mright10">Imported</span>';
									youtube_import($video,$the['categ'],$the['owner'] );
								   }
                                  }
									} else {
                                  if(ytExists($video['videoid'])) {
								    echo '<span class="label label-danger mright10">Already available</span>';
								  } else {
									  echo '<span class="label label-primary mright10">Unique in site</span>';
								  }

								  }								  
								  
								   echo _html($video['title']); ?></strong></h4>
								   <div class="img-det-text">
								   	<i class="material-icons">timer</i> <?php echo video_time($video['duration']); ?>
                                    <i class="material-icons">person</i> <?php echo $video['author']; ?>
								   </div>
								  <p><small> <?php echo _cut(str_replace("<br>"," ",_html($video['description'])), 200); ?></small></p>
								    </div>
                    </div>
                                 
								  <div class="btn-group btn-group-vertical pull-right">
								  <a class="btn btn-default btn-xs tipS" title="Preview in Youtube (new window)" href="https://www.youtube.com/watch?v=<?php echo $video['videoid']; ?>" target="_blank">
								  <i class="material-icons">link</i>  View in Yt</a>
								  </div>
                                  
                              </li>
							  <?php 
							  
							  }  //end loop 
} else {
	echo "API : No more results";
}
							  ?>
 </tbody>  
 </table>
 </div>
<?php if(!$auto) { ?>
</form><?php } 
$params['pageToken'] = $youtube->nextToken($params['maxResults'],this_page());
unset($the['p']);
$pageVars = $the;
$pagi = admin_url().'?'.urldecode(http_build_query($pageVars)).'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(12);
$a->set_per_page($the['bpp']);
$a->set_values(500);
$a->show_pages($pagi);
} else {
 //Monster loop videos
	
	for($i=1; $i <= 5000; $i++) {
$params = array(
    'maxResults'    => $the['bpp']
);
$params['pageToken'] = $youtube->thisToken($params['maxResults'],$i); 
$search = $youtube->getPlaylistItemsByPlaylistId($the['pID'],$the['bpp'],$i);
if($search) {
foreach ($search as $vd) {
$video = $youtube->Single($vd->contentDetails->videoId);
						  ?>
                                <li class="list-group-item">
								 <div class="row">
							 <div class="inline-block img-hold">
							 <div class="inline-block right20 img-checker">
							  <?php if(!$auto) { ?>	
                            <span class="pull-left mg-t-xs mg-r-md top20"><input type="checkbox" name="checkRow[]" value="<?php echo base64_encode(maybe_serialize($video)); ?>" class="styled" /></span>
                            <?php } ?>	
							<span class="pull-left mg-t-xs mg-r-md">
							<img src="<?php echo $video['thumb']; ?>" class="row-image">
							</span>
								  </div>
                                   <div class="inline-block right20 img-txt">
								 <h4 class="mtop10"> 
								   <?php 
								   //echo "<pre>";
								   //print_r($video);
								  // echo "</pre>";
									if($auto) {
									if(isset($the['allowduplicates']) && ($the['allowduplicates'] > 0)) {
								   echo '<span class="label label-success mright10">Imported</span>';
								  youtube_import($video,$the['categ'],$the['owner'] );
								  } else {
                                   if(ytExists($video['videoid'])) {
								    echo '<span class="label label-danger mright10">Already available</span>';
								   } else {
								    echo '<span class="label label-success mright10">Imported</span>';
									youtube_import($video,$the['categ'],$the['owner'] );
								   }
                                  }
									} else {
                                  if(ytExists($video['videoid'])) {
								    echo '<span class="label label-danger mright10">Already available</span>';
								  } else {
									  echo '<span class="label label-primary mright10">Unique in site</span>';
								  }

								  }								  
								  
								   echo _html($video['title']); ?></strong></h4>
								   <div class="img-det-text">
								   	<i class="material-icons">timer</i> <?php echo video_time($video['duration']); ?>
                                    <i class="material-icons">person</i> <?php echo $video['author']; ?>
								   </div>
								  <p><small> <?php echo _cut(str_replace("<br>"," ",_html($video['description'])), 200); ?></small></p>
								    </div>
                    </div>
                                 
								  <div class="btn-group btn-group-vertical pull-right">
								  <a class="btn btn-default btn-xs tipS" title="Preview in Youtube (new window)" href="https://www.youtube.com/watch?v=<?php echo $video['videoid']; ?>" target="_blank">
								  <i class="material-icons">link</i>  View in Yt</a>
								  </div>
                                  
                              </li>
							  <?php 
							  
							  } 
			
} else {
break;
}	
	}
	echo '
 </tbody>  
 </table>
 </div>
	';
	
}
?>
<div class="row" style="padding: 10px 0">
<a class="btn btn-lg btn-primary textWhite tipS" title="Turn this import to a cron" href="<?php echo str_replace('sk=','sk=crons&docreate&type=',$pagi);?>" ><i class="material-icons icon">add_to_queue</i> Automate this</a>
</div>
