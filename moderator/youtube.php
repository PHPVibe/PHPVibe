<?php $importer = array_merge($_GET, $_POST);
//var_dump($importer);
$nb_display = 20;
$startIndex = $nb_display * this_page() - $nb_display + 1;
if (isset($importer['action'])) {
if($importer['action'] == "search") {
$importer['q'] = str_replace(" ", "+",$importer['key'] );
$importer['start-index'] = $startIndex;
$importer['max-results'] = $nb_display;
$importer['format'] = 5;
$v1 = new Youtube_class();
$url = $v1->getYoutubeSearchVideosFeeds($importer);
//echo $url;
$videosData = $v1->returnYoutubeVideosDatasByURL($url);
$nbTotal=(isset($videosData['stats']['totalResults'])) ? $videosData['stats']['totalResults'] : 0;
if(($nbTotal==0 ) && isset($videosData['videos'])) { $nbTotal = count($videosData['videos']); 	}
$pagi_url = admin_url("youtube").'&action=search&key='.$importer['q'].'&categ='.$importer['categ'].'&owner='.$importer['owner'].'&orderby='.$importer['orderby'].'&safeSearch='.$importer['safeSearch'].'&lr='.$importer['lr'];
if(isset($importer['author']) && !empty($importer['author'])) { $pagi_url .= '&author='.$importer['author'];}
$pagi_url .= '&auto='.$importer['auto'].'&allowduplicates='.$importer['allowduplicates'].'&sleeppush='.$importer['sleeppush'].'&sleepvideos='.$importer['sleepvideos'].'&endpage='.$importer['endpage'].'&p=';
}elseif($importer['action'] == "feed") {
$importer['feed'] = str_replace(" ", "+",$importer['feed_id'] );
$importer['start-index'] = $startIndex;
$importer['max-results'] = $nb_display;
$importer['format'] = 5;
$v1 = new Youtube_class();
$url = $v1->getYoutubeStandardVideosFeeds($importer);
$videosData = $v1->returnYoutubeVideosDatasByURL($url);
$nbTotal=(isset($videosData['stats']['totalResults'])) ? $videosData['stats']['totalResults'] : 0;
if(($nbTotal==0 ) && isset($videosData['videos'])) { $nbTotal = count($videosData['videos']); 	}
$pagi_url = admin_url("youtube").'&action=feed&feed_id='.$importer['feed_id'].'&time='.$importer['time'].'&categ='.$importer['categ'].'&owner='.$importer['owner'].'&safeSearch='.$importer['safeSearch'].'&lr='.$importer['lr'];
$pagi_url .= '&auto='.$importer['auto'].'&allowduplicates='.$importer['allowduplicates'].'&sleeppush='.$importer['sleeppush'].'&sleepvideos='.$importer['sleepvideos'].'&endpage='.$importer['endpage'].'&p=';

}elseif($importer['action'] == "user") {
$importer['start-index'] = $startIndex;
$importer['max-results'] = $nb_display;
$importer['format'] = 5;
$v1 = new Youtube_class();
$url = $v1->getYoutubeUsernameVideos($importer);
$videosData = $v1->returnYoutubeVideosDatasByURL($url);
$nbTotal=isset($videosData['stats']['totalResults']) ? $videosData['stats']['totalResults'] : 0;
if($nbTotal==0 && isset($videosData['videos'])) { $nbTotal = count($videosData['videos']); 	}
$pagi_url = admin_url("youtube").'&action=user&username='.$importer['username'].'&categ='.$importer['categ'];
$pagi_url .= '&auto='.$importer['auto'].'&allowduplicates='.$importer['allowduplicates'].'&sleeppush='.$importer['sleeppush'].'&sleepvideos='.$importer['sleepvideos'].'&endpage='.$importer['endpage'].'&p=';

}elseif($importer['action'] == "category") {
$importer['start-index'] = $startIndex;
$importer['max-results'] = $nb_display;
$importer['format'] = 5;
$v1 = new Youtube_class();
$url = $v1->getYoutubeVideosByCategory($importer);
$videosData = $v1->returnYoutubeVideosDatasByURL($url);
$nbTotal=(isset($videosData['stats']['totalResults'])) ? $videosData['stats']['totalResults'] : 0;
if(($nbTotal==0 ) && isset($videosData['videos'])) { $nbTotal = count($videosData['videos']); 	}
$pagi_url = admin_url("youtube").'&action=category&category='.$importer['category'].'&orderby='.$importer['orderby'].'&categ='.$importer['categ'];
$pagi_url .= '&auto='.$importer['auto'].'&allowduplicates='.$importer['allowduplicates'].'&sleeppush='.$importer['sleeppush'].'&sleepvideos='.$importer['sleepvideos'].'&endpage='.$importer['endpage'].'&p=';
} elseif($importer['action'] == "playlist") {

$importer['start-index'] = $startIndex;
$importer['max-results'] = $nb_display;
$importer['format'] = 5;
$v1 = new Youtube_class();
$url = $v1->getYoutubeVideosByPlaylist($importer);
$videosData = $v1->returnPlaylistVideos($url);
//var_dump($videosData);
$nbTotal=(isset($videosData['stats']['totalResults'])) ? $videosData['stats']['totalResults'] : 0;
if(($nbTotal==0 ) && isset($videosData['videos'])) { $nbTotal = count($videosData['videos']); 	}
$pagi_url = admin_url("youtube").'&action=playlist&category='.$importer['category'].'&orderby='.$importer['orderby'].'&categ='.$importer['categ'];
$pagi_url .= '&auto='.$importer['auto'].'&allowduplicates='.$importer['allowduplicates'].'&sleeppush='.$importer['sleeppush'].'&sleepvideos='.$importer['sleepvideos'].'&endpage='.$importer['endpage'].'&p=';


} else {
echo 'Missing action/section. Click back and try again.';
}
//Owner recheck 
if(!isset($importer['owner']) || nullval($importer['owner'])) {$importer['owner'] = user_id();}
// Do the import
if(isset($videosData['videos']) && (count($videosData['videos'] > 0))) {
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(12);
$a->set_per_page($nb_display);
$a->set_values(500);
$a->show_pages($pagi_url);
?>
<div class="row" style="padding: 10px 0">
<a class="btn btn-large btn-success pull-right" href="<?php echo str_replace('sk=','sk=crons&docreate&type=',$pagi_url);?>" ><i class="icon-time"></i>Automate this</a>
</div>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                                <tr>                                 
                                  <th width="130px"></th>								 
                                  <th><?php echo _lang("Video"); ?></th>
							      <th>Status</th>								  
                                  <th>Youtube link</th> 
								</tr>
                          </thead>
                          <tbody>
						  <?php for($i=0;$i<count($videosData['videos']);$i++) { ?>
                              <tr>
                                 
                                  <td><img src="<?php echo 'http://i4.ytimg.com/vi/'.$videosData['videos'][$i]['videoid'].'/0.jpg'; ?>" style="width:130px; height:90px;"></td>
                                  <td><?php echo _html($videosData['videos'][$i]['title']); ?></td>
                                  <td>
								    <?php if($importer['allowduplicates'] > 0) {
								   echo '<span class="greenText">Imported</span>';
								  youtube_import($videosData['videos'][$i],$importer['categ'],$importer['owner'] );
								  } else {
                                   if(has_youtube_duplicate($videosData['videos'][$i]['videoid'])) {
								    echo '<span class="redText">Skipped as duplicate</span>';
								   } else {
								    echo '<span class="greenText">Imported</span>';
									youtube_import($videosData['videos'][$i],$importer['categ'],$importer['owner'] );
								   }

                                  } ?>
								  </td>
								  <td><a class="btn btn-primary btn-small" href="http://www.youtube.com/watch?v=<?php echo $videosData['videos'][$i]['videoid']; ?>" target="_blank"><i class="icon-link whiteText"></i>  Preview</a></td>
                                  
                              </tr>
							  <?php 
							if($importer['sleepvideos'] > 0) {   sleep($importer['sleepvideos']); }
							  }  //end loop 
							  ?>
						</tbody>  
</table>
</div>						
<?php
$next = this_page() + 1;
if(($importer['auto'] > 0) && ($nbTotal > 0) && ($next < $importer['endpage'])) {
echo 'Redirecting to '.$next;
echo '
<script type="text/javascript">
setTimeout(function() {
  window.location.href = "'.$pagi_url.$next.'";
}, '.$importer['sleeppush'].');

</script>
';
}

$a->show_pages($pagi_url);
} else { echo '<div class="msg-info">No (more) videos found</div>'; 
if(isset($videosData['error'])) {
echo '<div class="msg-warning">Youtube API response code <strong>'.$videosData["error"]["code"].'</strong> : '.$videosData["error"]["message"].' </div>';
}
}

 //end if data
//end actions
//render forms
} else {
?>

<h2 class=""> Youtube automated importer</h2>

<ul class="nav nav-tabs" id="myTab">
  <li class="active"><a href="#search">Import via Search</a></li>
  <li><a href="#feed">Import via feed</a></li>
   <li><a href="#user">Import by user</a></li>
  <li><a href="#category">Import by Category</a></li>
  <li><a href="#playlist">Import by Playlist</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="search">
  <div class="row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('youtube');?>" enctype="multipart/form-data" method="post">
<i>Note: This will import all videos without asking </i>
<input type="hidden" name="action" class="hide" value="search"> 
<div class="form-group form-material">
<label class="control-label"><i class="icon-search"></i>Keyword</label>
<div class="controls">
<input type="text" name="key" class="validate[required] col-md-8" value=""> 						
</div>	
</div>

<?php
echo '<div class="form-group form-material">
	<label class="control-label">'._lang("Category:").'</label>
	<div class="controls">
	'.cats_select("categ","select","").'
	  </div>             
	  </div>';
?>	  
	<div class="form-group form-material">
	<label class="control-label">Order by</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="relevance" checked /> Relevance </label>
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="published" />Published</label>
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="viewCount" /> Views Count </label>
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="rating" />Rating</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">User</label>
	<div class="controls">
	<?php
	echo '<select data-placeholder="'._lang("Choose owner:").'" name="owner" id="clear-results" class="select validate[required]" tabindex="2">
	';
$users = $db->get_results("SELECT id, name FROM  ".DB_PREFIX."users order by id asc limit 0,1000");
if($users) {
foreach ($users as $cat) {	
echo'<option value="'.intval($cat->id).'">'._html($cat->name).'</option>';
	}
}	else {
echo'<option value="">'._lang("No users").'</option>';
}
echo '</select>';
	
	?>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Safe search</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="safeSearch" class="styled" value="strict" /> Strict </label>
	<label class="radio inline"><input type="radio" name="safeSearch" class="styled" value="moderate" checked />Moderate</label>
	<label class="radio inline"><input type="radio" name="safeSearch" class="styled" value="none" /> None </label>

	</div>
	</div>
	<div class="form-group form-material">
<label class="control-label">Language (code)</label>
<div class="controls">
<input type="text" name="lr" class="validate[required] col-md-1" value="en"> 	
<span class="help-block">Details at <a href="https://developers.google.com/youtube/2.0/developers_guide_protocol_api_query_parameters#lrsp" target="_blank">Youtube API (LR)</a> </span>				
					
</div>	
</div>
<div class="form-group form-material">
<label class="control-label">Author (optional)</label>
<div class="controls">
<input type="text" name="author" class="col-md-4" value=""> 				
</div>	
</div>
	<div class="form-group form-material">
	<label class="control-label">Autopush</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="0" checked>NO</label>
	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label">Allow duplicates</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="0" checked>NO</label>
	<span class="help-block">If set to NO it will search if video is already in the database and skip it. </span>				
		
	</div>
	</div>	
<div class="form-group form-material">
	<label class="control-label">Advanced settings</label>
	<div class="controls">
<div class="row">
	<div class="col-md-4">
		<input class="col-md-12" name="sleeppush" type="text" value="2"><span class="help-block">Seconds to sleep before push </span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="sleepvideos" type="text" value="0"><span class="help-block k align-center">Seconds to sleep between videos import</span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="endpage" type="text" value="19"><span class="help-block k align-right">Which page to end push (Youtube returns </span>
	</div>
</div>
	</div>
	</div>		
<div class="form-group form-material">
<button type="submit" class="pull-right btn btn-success">Start import</button> 						

</div>	  
	</form>    
    </div>
   </div> 

  <div class="tab-pane" id="feed">
  <div class="row">
<form class="form-horizontal styled" action="<?php echo admin_url('youtube');?>" enctype="multipart/form-data" method="post">
<i>Note: This will import all videos without asking </i>
<input type="hidden" name="action" class="hide" value="feed"> 
<div class="form-group form-material">
<label class="control-label"><i class="icon-list"></i>Youtube feed</label>
<div class="controls">
<select data-placeholder="Select feed" name="feed_id" id="clear-results" class="select validate[required]" tabindex="2">
<option value="top_rated">Top rated</option> 
<option value="top_favorites">Top favorites</option> 
<option value="most_shared">Most shared</option> 
<option value="most_popular">Most popular</option> 
<option value="most_recent">Most recent</option> 
<option value="most_discussed">Most discussed</option> 
<option value="most_responded">Most responded</option> 
<option value="recently_featured">Recently featured</option> 
<option value="on_the_web">Trending videos</option> 
</select>					
<span class="help-block">Details at <a href="https://developers.google.com/youtube/2.0/developers_guide_protocol_video_feeds#Standard_feeds" target="_blank">Youtube API (Standard feeds)</a> </span>				

</div>	
</div>
<div class="form-group form-material">
<label class="control-label"><i class="icon-time"></i>Time frame</label>
<div class="controls">
<label class="radio inline"><input type="radio" name="time" class="styled" value="today"> Today </label>
<label class="radio inline"><input type="radio" name="time" class="styled" value="this_week"> This week </label>
<label class="radio inline"><input type="radio" name="time" class="styled" value="all_time"> All time </label>
<label class="radio inline"><input type="radio" name="time" class="styled" value="" checked> None </label>
<span class="help-block">Details at <a href="https://developers.google.com/youtube/2.0/developers_guide_protocol_api_query_parameters#timesp" target="_blank">Youtube API (time)</a> </span>				

</div>	
</div>
<?php
echo '<div class="form-group form-material">
	<label class="control-label">'._lang("Category:").'</label>
	<div class="controls">
	'.cats_select("categ","select","").'
	  </div>             
	  </div>';
?>	  
<div class="form-group form-material">
	<label class="control-label">Safe search</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="safeSearch" class="styled" value="strict" /> Strict </label>
	<label class="radio inline"><input type="radio" name="safeSearch" class="styled" value="moderate" checked />Moderate</label>
	<label class="radio inline"><input type="radio" name="safeSearch" class="styled" value="none" /> None </label>

	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">User</label>
	<div class="controls">
	<?php
	echo '<select data-placeholder="'._lang("Choose owner:").'" name="owner" id="clear-results" class="select validate[required]" tabindex="2">
	';
$users = $db->get_results("SELECT id, name FROM  ".DB_PREFIX."users order by id asc limit 0,1000");
if($users) {
foreach ($users as $cat) {	
echo'<option value="'.intval($cat->id).'">'._html($cat->name).'</option>';
	}
}	else {
echo'<option value="">'._lang("No users").'</option>';
}
echo '</select>';
	
	?>
	</div>
	</div>
	<div class="form-group form-material">
<label class="control-label">Language (code)</label>
<div class="controls">
<input type="text" name="lr" class="validate[required] col-md-1" value="en"> 	
<span class="help-block">Details at <a href="https://developers.google.com/youtube/2.0/developers_guide_protocol_api_query_parameters#lrsp" target="_blank">Youtube API (LR)</a> </span>				
					
</div>	
</div>
	<div class="form-group form-material">
	<label class="control-label">Autopush</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="0" checked>NO</label>
	</div>
	</div>	
		<div class="form-group form-material">
	<label class="control-label">Allow duplicates</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="0" checked>NO</label>
	<span class="help-block">If set to NO it will search if video is already in the database and skip it. </span>				
		
	</div>
	</div>	
<div class="form-group form-material">
	<label class="control-label">Advanced settings</label>
	<div class="controls">
<div class="row">
	<div class="col-md-4">
		<input class="col-md-12" name="sleeppush" type="text" value="3"><span class="help-block">Seconds to sleep before push </span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="sleepvideos" type="text" value="0"><span class="help-block k align-center">Seconds to sleep between videos import</span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="endpage" type="text" value="19"><span class="help-block k align-right">Which page to end push (Youtube returns </span>
	</div>
</div>
	</div>
	</div>		
<div class="form-group form-material">
<button type="submit" class="pull-right btn btn-success">Start import</button> 						

</div>	  
	</form>  
    </div>
	</div>
	  <div class="tab-pane" id="category">
  <div class="row">
<form class="form-horizontal styled" action="<?php echo admin_url('youtube');?>" enctype="multipart/form-data" method="post">
<i>Note: This will import all videos without asking </i>
<input type="hidden" name="action" class="hide" value="category"> 
<div class="form-group form-material">
<label class="control-label"><i class="icon-list"></i>Youtube category</label>
<div class="controls">
<input type="text" name="category" class="validate[required] col-md-8" value=""> 						
</div>	
</div>
<?php
echo '<div class="form-group form-material">
	<label class="control-label">'._lang("Category:").'</label>
	<div class="controls">
	'.cats_select("categ","select","").'
	  </div>             
	  </div>';
?>	 
<div class="form-group form-material">
	<label class="control-label">Order by</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="relevance" checked /> Relevance </label>
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="published" />Published</label>
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="viewCount" /> Views Count </label>
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="rating" />Rating</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">User</label>
	<div class="controls">
	<?php
	echo '<select data-placeholder="'._lang("Choose owner:").'" name="owner" id="clear-results" class="select validate[required]" tabindex="2">
	';
$users = $db->get_results("SELECT id, name FROM  ".DB_PREFIX."users order by id asc limit 0,1000");
if($users) {
foreach ($users as $cat) {	
echo'<option value="'.intval($cat->id).'">'._html($cat->name).'</option>';
	}
}	else {
echo'<option value="">'._lang("No users").'</option>';
}
echo '</select>';
	
	?>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Autopush</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="0" checked>NO</label>
	</div>
	</div>	
		<div class="form-group form-material">
	<label class="control-label">Allow duplicates</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="0" checked>NO</label>
	<span class="help-block">If set to NO it will search if video is already in the database and skip it. </span>				
		
	</div>
	</div>	
<div class="form-group form-material">
	<label class="control-label">Advanced settings</label>
	<div class="controls">
<div class="row">
	<div class="col-md-4">
		<input class="col-md-12" name="sleeppush" type="text" value="3"><span class="help-block">Seconds to sleep before push </span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="sleepvideos" type="text" value="0"><span class="help-block k align-center">Seconds to sleep between videos import</span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="endpage" type="text" value="19"><span class="help-block k align-right">Which page to end push (Youtube returns </span>
	</div>
</div>
	</div>
	</div>	
<div class="form-group form-material">
<button type="submit" class="pull-right btn btn-success">Start import</button> 						

</div>	 
</form>
</div>
  </div>
  	 <div class="tab-pane" id="playlist">
  <div class="row">
<form class="form-horizontal styled" action="<?php echo admin_url('youtube');?>" enctype="multipart/form-data" method="post">
<i>Note: This will import all videos without asking </i>
<input type="hidden" name="action" class="hide" value="playlist"> 
<div class="form-group form-material">
<label class="control-label"><i class="icon-list"></i>Youtube playlist id</label>
<div class="controls">
<input type="text" name="category" class="validate[required] col-md-8" value=""> 						
</div>	
</div>
<?php
echo '<div class="form-group form-material">
	<label class="control-label">'._lang("Category:").'</label>
	<div class="controls">
	'.cats_select("categ","select","").'
	  </div>             
	  </div>';
?>	 
<div class="form-group form-material">
	<label class="control-label">Order by</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="relevance" checked /> Relevance </label>
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="published" />Published</label>
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="viewCount" /> Views Count </label>
	<label class="radio inline"><input type="radio" name="orderby" class="styled" value="rating" />Rating</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">User</label>
	<div class="controls">
	<?php
	echo '<select data-placeholder="'._lang("Choose owner:").'" name="owner" id="clear-results" class="select validate[required]" tabindex="2">
	';
$users = $db->get_results("SELECT id, name FROM  ".DB_PREFIX."users order by id asc limit 0,1000");
if($users) {
foreach ($users as $cat) {	
echo'<option value="'.intval($cat->id).'">'._html($cat->name).'</option>';
	}
}	else {
echo'<option value="">'._lang("No users").'</option>';
}
echo '</select>';
	
	?>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Autopush</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="0" checked>NO</label>
	</div>
	</div>	
		<div class="form-group form-material">
	<label class="control-label">Allow duplicates</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="0" checked>NO</label>
	<span class="help-block">If set to NO it will search if video is already in the database and skip it. </span>				
		
	</div>
	</div>	
<div class="form-group form-material">
	<label class="control-label">Advanced settings</label>
	<div class="controls">
<div class="row">
	<div class="col-md-4">
		<input class="col-md-12" name="sleeppush" type="text" value="3"><span class="help-block">Seconds to sleep before push </span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="sleepvideos" type="text" value="0"><span class="help-block k align-center">Seconds to sleep between videos import</span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="endpage" type="text" value="19"><span class="help-block k align-right">Which page to end push (Youtube returns </span>
	</div>
</div>
	</div>
	</div>	
<div class="form-group form-material">
<button type="submit" class="pull-right btn btn-success">Start import</button> 						

</div>	 
</form>
</div>
  </div>
	<div class="tab-pane" id="user">
  <div class="row">
<form class="form-horizontal styled" action="<?php echo admin_url('youtube');?>" enctype="multipart/form-data" method="post">
<i>Note: This will import all videos without asking </i>
<input type="hidden" name="action" class="hide" value="user"> 
<div class="form-group form-material">
<label class="control-label"><i class="icon-user"></i>Youtube username</label>
<div class="controls">
<input type="text" name="username" class="validate[required] col-md-8" value=""> 						
</div>	
</div>
<div class="form-group form-material">
	<label class="control-label">(Local) Owner</label>
	<div class="controls">
	<?php
	echo '<select data-placeholder="'._lang("Choose owner:").'" name="owner" id="clear-results" class="select validate[required]" tabindex="2">
	';
$users = $db->get_results("SELECT id, name FROM  ".DB_PREFIX."users order by id asc limit 0,1000");
if($users) {
foreach ($users as $cat) {	
echo'<option value="'.intval($cat->id).'">'._html($cat->name).'</option>';
	}
}	else {
echo'<option value="">'._lang("No users").'</option>';
}
echo '</select>';
	
	?>
	</div>
	</div>

<?php
echo '<div class="form-group form-material">
	<label class="control-label">'._lang("Category:").'</label>
	<div class="controls">
	'.cats_select("categ","select","").'
	  </div>             
	  </div>';
?>	 
	<div class="form-group form-material">
	<label class="control-label">Autopush</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="0" checked>NO</label>
	</div>
	</div>	
		<div class="form-group form-material">
	<label class="control-label">Allow duplicates</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="0" checked>NO</label>
	<span class="help-block">If set to NO it will search if video is already in the database and skip it. </span>				
		
	</div>
	</div>	
<div class="form-group form-material">
	<label class="control-label">Advanced settings</label>
	<div class="controls">
<div class="row">
	<div class="col-md-4">
		<input class="col-md-12" name="sleeppush" type="text" value="3"><span class="help-block">Seconds to sleep before push </span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="sleepvideos" type="text" value="0"><span class="help-block k align-center">Seconds to sleep between videos import</span>
	</div>
	<div class="col-md-4">
		<input class="col-md-12" name="endpage" type="text" value="19"><span class="help-block k align-right">Which page to end push (Youtube returns </span>
	</div>
</div>
	</div>
	</div>	
<div class="form-group form-material">
<button type="submit" class="pull-right btn btn-success">Start import</button> 						

</div>	 
</form>
</div>
  </div>
 


<?php } ?>
