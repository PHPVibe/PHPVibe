<?php require_once("load.php");
$crons = $db->get_results("select * from ".DB_PREFIX."crons where cron_type = 'youtube' or cron_type = 'youtube-1by1' or cron_type = 'video' or cron_type = 'yt_playlistsearch' order by cron_lastrun ASC limit 0,100000");
if($crons) {
$youtube = new Youtube(array('key' => get_option('youtubekey')));
/* loop crons */
foreach ($crons as $cron) {
echo "<br /> Executed ".$cron->cron_name;
$db->query("UPDATE ".DB_PREFIX."crons SET cron_lastrun =now() WHERE cron_id = '".$cron->cron_id ."'");
//$db->debug();
$the = maybe_unserialize($cron->cron_value);
if($the['type'] == "yt_playlistsearch") {
//Playlist
for($p=1;$p < ($cron->cron_pages + 1);$p++) {
$params = array(
    'maxResults'    => $the['bpp']
);
$params['pageToken'] = $youtube->thisToken($params['maxResults'],$i);
$search = $youtube->getPlaylistItemsByPlaylistId($the['pID'],$the['bpp'],$i);
if($search) {
foreach ($search as $vd) {
$video = $youtube->Single($vd->contentDetails->videoId);
 if(!ytExists($video['videoid'])) {
	 youtube_import($video,$the['categ'],$the['owner'] );
	 echo 'Imported '.$video['videoid'].'<br>';
 } else {
	 echo 'Skipped as duplicated '.$video['videoid'].'<br>';
 }

}
}
//End loop
}

} else {
//Search
for($p=1;$p < ($cron->cron_pages + 1);$p++) {
$params = array(
    'q'             => $the['q'],
    'type'          => $the['type'],
    'part'          => 'id',
	'videoEmbeddable' => 'true',
    'maxResults'    => $the['bpp']
);
if(isset($the['channelID'])) {$params['channelId'] = $the['channelID'];}
$params['pageToken'] = $youtube->thisToken($params['maxResults'],$p);
$search = $youtube->searchAdvanced($params, true);
if($search) {
foreach ($search['results'] as $vd) {
$video = $youtube->Single($vd->id->videoId);
 if(!ytExists($video['videoid'])) {
	 youtube_import($video,$the['categ'],$the['owner'] );
	 echo 'Imported '.$video['videoid'].'<br>';
 } else {
	 echo 'Skipped as duplicated '.$video['videoid'].'<br>';
 }

}

}
/** End if search */
}
}
/** End search loop for this cron */

}

} else {
echo "No crons found";
}

?>