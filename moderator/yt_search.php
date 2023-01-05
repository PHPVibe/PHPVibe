<?php  $the = $_GET;
$type = $the['type'];
if(!isset($the['auto'])) { $the['auto'] = 0;}
if(!isset($the['imode']) || nullval($the['imode'])) { $the['imode'] = 2;}
$auto = ($the['imode'] < 2) ? true : false;
$all = ($the['auto'] > 0) ? true : false;
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $raw) {
$video = base64_decode($raw);
youtube_import(maybe_unserialize($video), $the['categ'],$the['owner']);
}
echo '<div class="msg-info">Selected videos have been imported.</div>';
}
$youtube = new Youtube(array('key' => get_option('youtubekey')));
switch($type) {
case 'video' :	
$params = array(
    'q'             => $the['q'],
    'type'          => $the['type'],
    'part'          => 'id',
	'videoEmbeddable' => 'true',
    'maxResults'    => $the['bpp']
);
if(isset($the['channelID'])) {$params['channelId'] = $the['channelID'];}
$params['pageToken'] = $youtube->thisToken($params['maxResults'],this_page()); 
$search = $youtube->searchAdvanced($params, true);
if (isset($search['info'])) {
$inf['totalResults'] = ($search['info']["totalResults"] < 501) ? $search['info']["totalResults"] : 500 ;
$inf['nextPageToken'] = $search['info']['nextPageToken'];
$inf['prevPageToken'] = $search['info']['prevPageToken'];
if(!isset($the['maxResults'])) { $the['maxResults'] = ceil($inf['totalResults'] / $the['bpp'] );}
}

if(!$all) {
if(!$auto) { ?>
<form id="validate" class="form-horizontal styled" action="<?php echo canonical();?>" enctype="multipart/form-data" method="post">
<?php } ?>
<div class="cleafix full"></div>
<fieldset>
<div class="panel top10 multicheck">
<div class="panel-heading">
<h3 class="panel-title">
<?php echo ucfirst($the['type']).'s #'._html($the['q']); ?>
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
                        
						  <?php foreach ($search['results'] as $vd) {
$video = $youtube->Single($vd->id->videoId);
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
							  ?>
</div>
</div>
 </div>
 </fieldset>
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
$a->set_values($inf['totalResults']);
 ?>
<a class="btn btn-lg btn-primary textWhite tipS pull-right" title="Turn this search to a cron" href="<?php echo str_replace('sk=','sk=crons&docreate&type=',$pagi);?>" >
<i class="material-icons">add_to_queue</i>
Automate this import
</a>
<?php $a->show_pages($pagi);

} else {
	if(!$auto) { ?>
	
<form id="validate" class="form-horizontal styled" action="<?php echo canonical();?>" enctype="multipart/form-data" method="post">
<?php } ?>
<fieldset>
<div class="panel top10 multicheck">
<div class="panel-heading">
<h3 class="panel-title">
<?php echo ucfirst($the['type']).'s #'._html($the['q']); ?>
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
                        
	<?php //Monster loop videos
	
	for($i=1; $i <= $the['maxResults']; $i++) {
		$params = array(
    'q'             => $the['q'],
    'type'          => $the['type'],
    'part'          => 'id',
	'videoEmbeddable' => 'true',
    'maxResults'    => $the['bpp']
);
if(isset($the['channelID'])) {$params['channelId'] = $the['channelID'];}
$params['pageToken'] = $youtube->thisToken($params['maxResults'],$i); 
$search = $youtube->searchAdvanced($params, true);
if (isset($search['info'])) {
$inf['totalResults'] = ($search['info']["totalResults"] < 501) ? $search['info']["totalResults"] : 500 ;
$inf['nextPageToken'] = $search['info']['nextPageToken'];
$inf['prevPageToken'] = $search['info']['prevPageToken'];
if(!isset($the['maxResults'])) { $the['maxResults'] = ceil($inf['totalResults'] / $the['bpp'] );}
}
		 foreach ($search['results'] as $vd) {
$video = $youtube->Single($vd->id->videoId);
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
		if(nullval($inf['nextPageToken'])) {break;}					  
	}
	echo '
 </tbody>  
 </table>
 </div>
	';
	
}
?>

<?php
break;
case 'channel' :
$params = array(
    'q'             => $the['q'],
    'type'          => $the['type'],
    'part'          => 'id,snippet',
    'maxResults'    => $the['bpp']
);
$params['pageToken'] = $youtube->thisToken($params['maxResults'],this_page()); 
$search = $youtube->searchAdvanced($params, true);
if (isset($search['info'])) {
$inf['totalResults'] = ($search['info']["totalResults"] < 501) ? $search['info']["totalResults"] : 500 ;
$inf['nextPageToken'] = $search['info']['nextPageToken'];
$inf['prevPageToken'] = $search['info']['prevPageToken'];
if(!isset($the['maxResults'])) { $the['maxResults'] = ceil($inf['totalResults'] / $the['bpp'] );}
}
echo '<h1> '.$the['type'].'s // '._html($the['q']).'</h1>';
echo '<ol class="ytChannel row">';

foreach ($search['results'] as $video) {
echo '<li class="holder">';
echo '<div class="YtScene"><div class="cover"><img src="'.$video->snippet->thumbnails->medium->url.'" style="width:100%; height:238px;"/></div>
<div class="bubble"><a href="'.admin_url('yt_bc').'&c='.$video->id->channelId.'" class="tipS" title="Explore channel & import options in new tab" target="_blank"><i class="icon-list"></i></a></div>
</div>';
echo '<h2>'.$video->snippet->title.'</h2>';
echo '<p>'._cut($video->snippet->description,220).'</p>';
echo '<div class="yfooter">  '.$video->snippet->title.'
<div class="import"><a href="https://www.youtube.com/channel/'.$video->id->channelId.'" class="tipS" title="Explore channel on Youtube" target="_blank"><i class="icon-youtube"></i> </a></div></div>
';
echo '</li>';	
}
echo '</ol>';
echo "<br/>";
if(!$all) {
$params['pageToken'] = $youtube->nextToken($params['maxResults'],this_page());
unset($the['p']);
$pageVars = $the;
$pagi = admin_url().'?'.urldecode(http_build_query($pageVars)).'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(12);
$a->set_per_page($the['bpp']);
$a->set_values($inf['totalResults']);
$a->show_pages($pagi);
} else {
for($i=2; $i <= $the['maxResults']; $i++) {
// Monster channel loop
$params = array(
    'q'             => $the['q'],
    'type'          => $the['type'],
    'part'          => 'id,snippet',
    'maxResults'    => $the['bpp']
);
$params['pageToken'] = $youtube->thisToken($params['maxResults'],$i); 
$search = $youtube->searchAdvanced($params, true);
if (isset($search['info'])) {
$inf['totalResults'] = ($search['info']["totalResults"] < 501) ? $search['info']["totalResults"] : 500 ;
$inf['nextPageToken'] = $search['info']['nextPageToken'];
$inf['prevPageToken'] = $search['info']['prevPageToken'];
if(!isset($the['maxResults'])) { $the['maxResults'] = ceil($inf['totalResults'] / $the['bpp'] );}
}
echo '<ol class="ytChannel row">';

foreach ($search['results'] as $video) {
echo '<li class="holder">';
echo '<div class="YtScene"><div class="cover"><img src="'.$video->snippet->thumbnails->medium->url.'" style="width:100%; height:238px;"/></div>
<div class="bubble"><a href="'.admin_url('yt_bc').'&c='.$video->id->channelId.'" class="tipS" title="Explore channel & import options in new tab" target="_blank"><i class="icon-list"></i></a></div>
</div>';
echo '<h2>'.$video->snippet->title.'</h2>';
echo '<p>'._cut($video->snippet->description,220).'</p>';
echo '<div class="yfooter">  '.$video->snippet->title.'
<div class="import"><a href="https://www.youtube.com/channel/'.$video->id->channelId.'" class="tipS" title="Explore channel on Youtube" target="_blank"><i class="icon-youtube"></i> </a></div></div>
';
echo '</li>';	
}
echo '</ol>';
echo "<br/>";
if(!isset($inf['nextPageToken']) || nullval($inf['nextPageToken'])) {break;}

}	
}
break;
case 'playlist':
$params = array(
    'q'             => $the['q'],
    'type'          => $the['type'],
    'part'          => 'id,snippet',
    'maxResults'    => $the['bpp']
);
$params['pageToken'] = $youtube->thisToken($params['maxResults'],this_page()); 
$search = $youtube->searchAdvanced($params, true);
if (isset($search['info'])) {
$inf['totalResults'] = ($search['info']["totalResults"] < 501) ? $search['info']["totalResults"] : 500 ;
$inf['nextPageToken'] = $search['info']['nextPageToken'];
$inf['prevPageToken'] = $search['info']['prevPageToken'];
if(!isset($the['maxResults'])) { $the['maxResults'] = ceil($inf['totalResults'] / $the['bpp'] );}
}
echo '<h1> '.$the['type'].'s // '._html($the['q']).'</h1>';
echo '<ol class="ytChannel row">';

foreach ($search['results'] as $video) {
echo '<li class="holder">';
echo '<div class="YtScene"><div class="cover"><img src="'.$video->snippet->thumbnails->medium->url.'" style="width:100%; height:238px;"/></div>
<div class="bubble"><a href="'.admin_url('yt_playlistsearch').'&pID='.$video->id->playlistId.'&categ='.$the['categ'].'&owner='.$the['owner'].'" class="tipS" title="Explore playlist & import options in new tab" target="_blank"><i class="icon-list"></i></a></div>
</div>';
echo '<h2>'.$video->snippet->title.'</h2>';
echo '<p>'._cut($video->snippet->description,220).'</p>';
echo '<div class="yfooter">  by <a href="https://www.youtube.com/channel/'.$video->snippet->channelId.'" class="tipS" title="See this channel on Youtube" target="_blank"><i class="icon-youtube"></i> '.$video->snippet->channelTitle.' </a>
<div class="import"><a href="https://www.youtube.com/playlist?list='.$video->id->playlistId.'" class="tipS" title="Explore this playlist on Youtube" target="_blank"><i class="icon-youtube"></i> </a></div></div>
';
echo '</li>';	
}
echo '</ol>';

if(!$all) {
$params['pageToken'] = $youtube->nextToken($params['maxResults'],this_page());
unset($the['p']);
$pageVars = $the;
$pagi = admin_url().'?'.urldecode(http_build_query($pageVars)).'&p=';
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(12);
$a->set_per_page($the['bpp']);
$a->set_values($inf['totalResults']);
$a->show_pages($pagi);
} else {
for($i=2; $i <= $the['maxResults']; $i++) {
// Monster playlist loop
$params = array(
    'q'             => $the['q'],
    'type'          => $the['type'],
    'part'          => 'id,snippet',
    'maxResults'    => $the['bpp']
);
$params['pageToken'] = $youtube->thisToken($params['maxResults'],$i); 
$search = $youtube->searchAdvanced($params, true);
if (isset($search['info'])) {
$inf['totalResults'] = ($search['info']["totalResults"] < 501) ? $search['info']["totalResults"] : 500 ;
$inf['nextPageToken'] = $search['info']['nextPageToken'];
$inf['prevPageToken'] = $search['info']['prevPageToken'];
if(!isset($the['maxResults'])) { $the['maxResults'] = ceil($inf['totalResults'] / $the['bpp'] );}
}
echo '<ol class="ytChannel row">';

foreach ($search['results'] as $video) {
echo '<li class="holder">';
echo '<div class="YtScene"><div class="cover"><img src="'.$video->snippet->thumbnails->medium->url.'" style="width:100%; height:238px;"/></div>
<div class="bubble"><a href="'.admin_url('yt_playlistsearch').'&pID='.$video->id->playlistId.'&categ='.$the['categ'].'&owner='.$the['owner'].'&auto='.$the['auto'].'&imode='.$the['imode'].'" class="tipS" title="Explore playlist & import options in new tab" target="_blank"><i class="icon-list"></i></a></div>
</div>';
echo '<h2>'.$video->snippet->title.'</h2>';
echo '<p>'._cut($video->snippet->description,220).'</p>';
echo '<div class="yfooter">  by <a href="https://www.youtube.com/channel/'.$video->snippet->channelId.'" class="tipS" title="See this channel on Youtube" target="_blank"><i class="icon-youtube"></i> '.$video->snippet->channelTitle.' </a>
<div class="import"><a href="https://www.youtube.com/playlist?list='.$video->id->playlistId.'" class="tipS" title="Explore this playlist on Youtube" target="_blank"><i class="icon-youtube"></i> </a></div></div>
';
echo '</li>';	
}
echo '</ol>';
if(!isset($inf['nextPageToken']) || nullval($inf['nextPageToken'])) {break;}

}	
}
break;
}



