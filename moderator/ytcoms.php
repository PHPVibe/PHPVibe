<?php 
$all_options = get_all_options();
$ytid = _get('yt');
$tid = _get('local');
$uurl = admin_url('ytcoms').'&yt='.$ytid.'&local='.$tid;
if(is_empty($ytid) || is_empty($tid)) {
die("Ids incomplete");	
}
function callYtCs($url)
         {
         $ch      = curl_init();
         $timeout = 15;
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // add this one, it seems to spawn redirect 301 header
         curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'); // spoof
         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
         $data = curl_exec($ch);
         curl_close($ch);
         return $data;
         }
	function DoYtcsCall ($yt, $local, $token='') {
		if(not_empty($token)) {
		$content = callYtCs('https://www.googleapis.com/youtube/v3/commentThreads?part=snippet%2Creplies&videoId='.$yt.'&maxResults=50&moderationStatus=published&order=relevance&pageToken='.$token.'&key='.get_option('youtubekey'));
		} else {
		$content = callYtCs('https://www.googleapis.com/youtube/v3/commentThreads?part=snippet%2Creplies&videoId='.$yt.'&maxResults=50&moderationStatus=published&order=relevance&key='.get_option('youtubekey'));
        }
		return json_decode($content, true);
	}	
	function  CreateYTUser($name, $image, $usr) {
		global $db;
		if(user::checkUnique('username', toDb($usr))) {
		/* Copy Avatar */
		$savePath	     = ABSPATH.'/storage/uploads/'.$usr.'_photo.jpg';
		$avatar = 'storage/uploads/'.$usr.'_photo.jpg';
		$imageString = file_get_contents($image);
        $save = file_put_contents($savePath,$imageString);
		
		$keys_values = array(
                                "avatar"=> $avatar,								
                                "name"=> $name,	
								"username"=> $usr,
                                "password"	 => sha1(uniqid()),							
                                "type"=> "core"  );
	$add = user::AddUser($keys_values);
	} 
	$result = $db->get_row("SELECT id FROM ".DB_PREFIX."users WHERE username ='" . toDb($usr) . "'");	
	return 	$result->id;
	
	}
	function lastKeyID($key) {
		global $db;
	$result = $db->get_row("SELECT id FROM ".DB_PREFIX."em_comments WHERE access_key ='" . toDb($key) . "'");	
    return 	$result->id;
	}
	
		 
?>

<div class="row">
<h3>Import Youtube Comments</h3>
<ul id="notes">
<?php 
if(_get('token')) { 
$c = DoYtcsCall($ytid, $tid, _get('token')); 
} else {
$c = DoYtcsCall($ytid, $tid); 
}
$nxToken = $c['nextPageToken'];
foreach ($c['items'] as $vid) {
	$tg = $vid['snippet']['topLevelComment']['snippet'];
	 /* Prepare user channel */
	$thisU = CreateYTUser(toDb($tg['authorDisplayName']), str_replace('s28','s288',$tg['authorProfileImageUrl']), $tg['authorChannelId']['value']);
    /*Insert comment */
	$com_body = toDb($tg['textOriginal']);
	$obj_id = toDb('video_'.$tid);
	$reply_id = 0;
	$objDate = date('F j, Y, g:i a', strtotime($tg['publishedAt']));
	$key = md5(uniqid());
    $it = "INSERT INTO ".DB_PREFIX."em_comments(`object_id`, `created`, `sender_id`, `comment_text`, `access_key`, `reply`) VALUES ('".$obj_id."', '".$objDate."', '".$thisU."', '".$com_body."', '".$key."', '".$reply_id."')";
	$addit = $db->query($it);
	$db->query("INSERT INTO ".DB_PREFIX."activity (`user`, `type`, `object`, `extra`) VALUES ('".$thisU."', '6', '".$tid."', '')");
    //$db->debug();
	/* Print */
	echo '<li>
   <div class="aInner">
      <img src="'.$tg['authorProfileImageUrl'].'" style="width:32px; height:32px;">
      <div class="aBody"># '.$thisU.' '.$tg['authorDisplayName'].' ('.$tg['authorChannelId']['value'].') said: '.$tg['textOriginal'].' </div>
      <div class="aTime">'.$tg['publishedAt'].'</div>
      <br style="clear:both">
   </div>
</li>';


if(isset($vid['replies']) && !is_empty($vid['replies'])) {
//echo '<pre>';	
//print_r($vid['replies']);
//echo '</pre>';		
$reply_id = lastKeyID($key);	
echo '<ul style="padding-left:45px;">';
foreach ($vid['replies']['comments'] as $rep) {	
$tg =  $rep['snippet'];
$rethisU = CreateYTUser(toDb($tg['authorDisplayName']), str_replace('s28','s288',$tg['authorProfileImageUrl']), $tg['authorChannelId']['value']);  
$com_body = toDb($tg['textOriginal']);
	$obj_id = toDb('video_'.$tid);
	$objDate = date('F j, Y, g:i a', strtotime($tg['publishedAt']));
	$rekey = md5(uniqid());
    $it = "INSERT INTO ".DB_PREFIX."em_comments(`object_id`, `created`, `sender_id`, `comment_text`, `access_key`, `reply`) VALUES ('".$obj_id."', '".$objDate."', '".$rethisU."', '".$com_body."', '".$rekey."', '".$reply_id."')";
	$addit = $db->query($it);
	$db->query("INSERT INTO ".DB_PREFIX."activity (`user`, `type`, `object`, `extra`) VALUES ('".$rethisU."', '6', '".$tid."', '')");
	//$db->debug();
	echo '<li>
   <div class="aInner">
      <img src="'.$tg['authorProfileImageUrl'].'" style="width:26px; height:26px;">
      <div class="aBody"># '.$thisU.' '.$tg['authorDisplayName'].' ('.$tg['authorChannelId']['value'].') said: '.$tg['textOriginal'].' </div>
      <div class="aTime">'.$tg['publishedAt'].'</div>
      <br style="clear:both">
   </div>
</li>';

}
echo '</ul>';
}

}
?>
</ul>
<?php
if(not_empty($nxToken)) {
$ps = $uurl.'&token='.$nxToken;
echo '<div class="row page-footer text-center">
<a href="'.$ps.'" class="btn btn-primary">Next page</a>
</div>
';
}
?>
</div>
