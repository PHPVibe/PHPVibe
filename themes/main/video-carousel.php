<?php if(!nullval($vq)) { $videos = $db->get_results($vq); } else {$videos = false;}
if(!isset($st)){ $st = ''; }
if(!isset($blockclass)){ $blockclass = ''; }
if(!isset($blockextra)){ $blockextra = ''; }
if(isset($heading) && !empty($heading)) { echo '<h3 class="loop-heading loop-carousel"><span>'._html($heading).'</span>'.$st.'</h3>';}
if(isset($heading_meta) && !empty($heading_meta)) { echo $heading_meta;}
if ($videos) {

echo '<div class="loop-content"> <div class="owl-carousel">';
foreach ($videos as $video) {
			$title = _html(_cut($video->title, 70));
			$full_title = _html(str_replace("\"", "",$video->title));
			$url = video_url($video->id , $video->title);
			if(isset($video->group_id)) {$grcreative= group_creative($video->group_id); } else { $grcreative=''; };
			$watched = (is_watched($video->id)) ? '<span class="vSeen">'._lang("Watched").'</span>' : '';
			$liked = (is_liked($video->id)) ? '' : '<a class="heartit" title="'._lang("Like this video").'" href="javascript:iLikeThis('.$video->id.')"><i class="material-icons">&#xE8DC;</i></a>';
            $wlater = (is_user()) ? '<a class="laterit" title="'._lang("Add to watch later").'" href="javascript:Padd('.$video->id.', '.later_playlist().')"><i class="material-icons">&#xE924;</i></a>' : '';
			echo '
<div id="video-'.$video->id.'" class="video">
<div class="video-thumb">
		<a class="clip-link" data-id="'.$video->id.'" title="'.$full_title.'" href="'.$url.'">
			<span class="clip">
				<img src="'.thumb_fix($video->thumb, true, get_option('thumb-width'), get_option('thumb-height')).'" alt="'.$full_title.'" /><span class="vertical-align"></span>
			</span>
          	<span class="overlay"></span>
			</a>'.$watched.$wlater;
if($video->duration > 0) { echo '   <span class="timer">'.video_time($video->duration).'</span>'; }
echo '</div>
<div class="video-data">
	<h4 class="video-title"><a href="'.$url.'" title="'.$full_title.'">'._html($title).'</a></h4>
<ul class="stats">
<li class="uploaderlink"><a href="'.profile_url($video->user_id, $video->owner).'" title="'.$video->owner.'">'.$video->owner.'</a>'.$grcreative.'</li>
 <li>'.number_format($video->views).' '._lang('views').'</li>';
if(isset($video->date)) { echo '<li>'.time_ago($video->date).'</li>';}
echo '</ul>
</div>
	</div>
';
}
echo '</div>';
echo _ad('0','after-video-carousel');
echo '</div>';
} else {
echo '<p class="empty-content">'._lang('Nothing here so far.').'</p>';
}
?>