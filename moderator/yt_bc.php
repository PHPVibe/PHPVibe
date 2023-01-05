<?php  $the = $_GET;
if(!isset($the['auto'])) { $the['auto'] = 0;}
if(!isset($the['imode']) || nullval($the['imode'])) { $the['imode'] = 2;}
$youtube = new Youtube(array('key' => get_option('youtubekey')));
if(isset($the['c'])) {
$channel = $youtube->getChannelById($the['c']);
} elseif(isset($the['chName'])) {
$channel = $youtube->getChannelByName($the['chName']);

}
if(isset($channel) && $channel) {
	echo '<h1>'._html($channel->snippet->title).'</h1>';
	echo '<p><em>'.$channel->snippet->description.'</em></p>';
	
	if(isset($channel->contentDetails->relatedPlaylists)) {
		echo '<h1> Likes & Uploads</h1>';
		
		$own = array();
		if(isset($channel->contentDetails->relatedPlaylists->likes)) {
		$own[] = $youtube->getPlaylistById($channel->contentDetails->relatedPlaylists->likes);
		}
		if(isset($channel->contentDetails->relatedPlaylists->uploads)) {
		$own[] = $youtube->getPlaylistById($channel->contentDetails->relatedPlaylists->uploads);
	    }
	if(!nullval($own)) {
echo '<ol class="ytChannel row">';
foreach ($own as $video) {
echo '<li class="holder">';
echo '<div class="YtScene"><div class="cover"><img src="'.$video->snippet->thumbnails->medium->url.'" style="width:100%; height:238px;"/></div>
<div class="bubble"><a href="'.admin_url('yt_playlistsearch').'&pID='.$video->id.'&categ='.$the['categ'].'&owner='.$the['owner'].'&auto='.$the['auto'].'&imode='.$the['imode'].'" class="tipS" title="Explore playlist & import options in new tab" target="_blank"><i class="icon-list"></i></a></div>
</div>';
echo '<h2>'.$video->snippet->title.'</h2>';
echo '<div class="yfooter">  by <a href="https://www.youtube.com/channel/'.$video->snippet->channelId.'" class="tipS" title="See this channel on Youtube" target="_blank"><i class="icon-youtube"></i> '.$video->snippet->channelTitle.' </a>
<div class="import"><a href="https://www.youtube.com/playlist?list='.$video->id.'" class="tipS" title="Explore this playlist on Youtube" target="_blank"><i class="icon-youtube"></i> </a></div></div>
';
echo '</li>';	
}
echo '</ol>';
}	
		
	}
echo '<h1> Other Playlists</h1>';	
for($i=1; $i <= 1000; $i++) {	
$params['maxResults'] = 20;
$params['pageToken'] = $youtube->thisToken($params['maxResults'],$i);
$playlists = $youtube->getPlaylistsByChannelId($channel->id,$params);
if($playlists) {
echo '<ol class="ytChannel row">';
foreach ($playlists as $video) {
echo '<li class="holder">';
echo '<div class="YtScene"><div class="cover"><img src="'.$video->snippet->thumbnails->medium->url.'" style="width:100%; height:238px;"/></div>
<div class="bubble"><a href="'.admin_url('yt_playlistsearch').'&pID='.$video->id.'&categ='.$the['categ'].'&owner='.$the['owner'].'&auto='.$the['auto'].'&imode='.$the['imode'].'" class="tipS" title="Explore playlist & import options in new tab" target="_blank"><i class="icon-list"></i></a></div>
</div>';
echo '<h2>'.$video->snippet->title.'</h2>';
echo '<p>'._cut($video->snippet->description,220).'</p>';
echo '<div class="yfooter">  by <a href="https://www.youtube.com/channel/'.$video->snippet->channelId.'" class="tipS" title="See this channel on Youtube" target="_blank"><i class="icon-youtube"></i> '.$video->snippet->channelTitle.' </a>
<div class="import"><a href="https://www.youtube.com/playlist?list='.$video->id.'" class="tipS" title="Explore this playlist on Youtube" target="_blank"><i class="icon-youtube"></i> </a></div></div>
';
echo '</li>';	
}
echo '</ol>';
} else {
break;
}
}
} else {
echo "Wrong name or id";	
}





