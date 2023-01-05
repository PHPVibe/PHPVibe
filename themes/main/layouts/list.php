<?php $list = decList(_get('list'));
$options = DB_PREFIX."videos.id as vid,".DB_PREFIX."videos.title,".DB_PREFIX."videos.user_id as owner, ".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.duration";
$sorts = '';
if(intval($list) > 0) {
$sorter = _get('sorter'); 
switch ($sorter) {
	case "vd":
	$sorterop = DB_PREFIX."videos.id DESC";
	$sorts ='&sorter=vd';
	break;
	case "va":
	$sorterop = DB_PREFIX."videos.id ASC";
	$sorts ='&sorter=va';
	break;
	case "td":
	$sorterop = DB_PREFIX."videos.title DESC";
	$sorts ='&sorter=td';
	break;
	case "ta":
	$sorterop = DB_PREFIX."videos.title ASC";
	$sorts ='&sorter=ta';
	break;
	case "wd":
	$sorterop = DB_PREFIX."videos.views DESC";
	$sorts ='&sorter=wd';
	break;
	case "wa":
	$sorterop = DB_PREFIX."videos.views ASC";
	$sorts ='&sorter=wa';
	break;
	default:
	$sorterop = DB_PREFIX."playlist_data.id DESC";
	break;
}
	
	
$result =$db->get_results("select ".$options.", ".DB_PREFIX."users.name as name 
FROM ".DB_PREFIX."playlist_data
LEFT JOIN ".DB_PREFIX."videos ON ".DB_PREFIX."playlist_data.video_id = ".DB_PREFIX."videos.id
LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id
WHERE ".DB_PREFIX."playlist_data.playlist =  '".$list."' group by ".DB_PREFIX."playlist_data.video_id
ORDER BY ".$sorterop." ".this_offset(get_option('related-nr')));
/*
if(is_admin()) {
echo _get('sorter');	
echo $sorterop;
}*/
} else {
if(strpos(_get('list'), 'ums-') !== false) { $md = "> 1";}
if(strpos(_get('list'), 'uvs-') !== false) {$md = "< 2"; }
$options = "id,title,user_id, thumb,duration";
$vq = "
SELECT ".$options.", '".toDb($video->owner)."' as name FROM (
	(
		SELECT ".$options.", '".toDb($video->owner)."' as name
		FROM `vibe_videos`
		WHERE id >=".$video->id."
        and pub > 0 and date < now() and media $md and user_id ='".$video->user_id."'
		ORDER BY id
		LIMIT 60
	) UNION ALL (
		SELECT ".$options.", '".toDb($video->owner)."' as name
		FROM `vibe_videos`
		WHERE id < ".$video->id."
        and pub > 0 and date < now() and media $md and user_id ='".$video->user_id."'
		ORDER BY id DESC
		LIMIT 160
	)
) AS n
ORDER BY id desc
LIMIT 220";
$result =$db->get_results($vq);	
}
 if ($result) {
	foreach ($result as $related) {
		if(not_empty($related->title)) {
			if(!isset($related->vid)) {$related->vid = $related->id;}
			if(!isset($related->owner)) {$related->owner = $related->user_id;}
		$nowP = ($related->vid == $video->id)? "playingNow" : "";
$duration = ($related->duration > 0) ? video_time($related->duration) : '<i class="icon-picture"></i>';		
echo '
					<li id="'.$nowP.'" data-id="'.$related->vid.'" class="item-post '.$nowP.'">
				<div class="inner">
					
	<div class="thumb">
		<a class="clip-link" data-id="'.$related->vid.'" title="'.addslashes(_html($related->title)).'" href="'.video_url($related->vid , $related->title, hashList($list)).$sorts.'">
			<span class="clip">
				<img src="'.thumb_fix($related->thumb).'" alt="'.addslashes(_html($related->title)).'" /><span class="vertical-align"></span>
			</span>
		<span class="timer">'.$duration.'</span>					
			<span class="overlay"></span>
		</a>
	</div>			
					<div class="data">
						<span class="title"><a href="'.video_url($related->vid , $related->title, hashList($list)).$sorts.'" rel="bookmark" title="'.addslashes(_html($related->title)).'">'._cut(_html($related->title),124 ).'</a></span>
			
						<span class="usermeta">
							'._lang('by').' <a href="'.profile_url($related->owner, $related->name).'"> '._html($related->name).' </a>
							
						</span>
					</div>
				</div>
				</li>
		
	';
	}
}
}

?>