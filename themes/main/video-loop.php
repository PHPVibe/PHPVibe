<?php do_action('videoloop-start');
if(!nullval($vq)) { $videos = $db->get_results($vq); } else {$videos = false;}
if(!isset($st)){ $st = ''; }
if(!isset($blockclass)){ $blockclass = ''; }
if(!isset($blockextra)){ $blockextra = ''; }
if(isset($heading) && !empty($heading)) { echo '<h1 class="loop-heading"><span>'._html($heading).'</span>'.$st.'</h1>';}
if(isset($heading_meta) && !empty($heading_meta)) { echo $heading_meta;}
if(isset($heading_plus) && !empty($heading_plus)) { echo '<small class="videod">'.$heading_plus.'</small>';}
if ($videos) {

echo $blockextra.'<div class="loop-content phpvibe-video-list '.$blockclass.'">';
foreach ($videos as $video) {
			$title = _html(_cut($video->title, 70));
			$full_title = _html(str_replace("\"", "",$video->title));
			$url = video_url($video->id , $video->title);
			if(isset($video->group_id)) { $grcreative= group_creative($video->group_id); } else { $grcreative=''; };
			$watched = (is_watched($video->id)) ? '<span class="vSeen">&nbsp;</span>' : '';
            $wlater = (is_user()) ? '<a class="laterit" title="'._lang("Add to watch later").'" href="javascript:Padd('.$video->id.', '.later_playlist().')"><i class="material-icons">&#xE924;</i></a>' : '';
			echo '
<div id="video-'.$video->id.'" class="video">
<div class="video-thumb">
		<a class="clip-link" data-id="'.$video->id.'" title="'.$full_title.'" href="'.$url.'">
			<span class="clip">
				<img src="'.thumb_fix($video->thumb, true, get_option('thumb-width'), get_option('thumb-height')).'" data-name="'.addslashes(strtok($full_title, " ")).'" /><span class="vertical-align"></span>
			</span>
          	<span class="overlay"></span>
		</a>'.$watched.$wlater;
if($video->duration > 0) { echo '   <span class="timer">'.video_time($video->duration).'</span>'; }
echo '</div>
<div class="video-data">
	<h4 class="video-title"><a href="'.$url.'" title="'.$full_title.'">'._html($title).'</a></h4>
<ul class="stats">
<li class="uploaderlink"> <a href="'.profile_url($video->user_id, $video->owner).'" title="'.$video->owner.'">'.$video->owner.' </a> '.$grcreative.'</li>
 <li>'.number_format($video->views).' '._lang('views').'</li>';
if(isset($video->date)) { echo '<li>'.time_ago($video->date).'</li>';}
echo '</ul>
</div>
	</div>
';
}
echo _ad('0','after-video-loop');
/* Kill for home if several blocks */
if(!isset($kill_infinite) || !$kill_infinite) {
if(!_contains($canonical,"?")) {
echo '
<nav id="page_nav"><a href="'.$canonical.'?p='.next_page().'"></a></nav>
';
} else {
echo '
<nav id="page_nav"><a href="'.$canonical.'&p='.next_page().'"></a></nav>
';
}
echo '
<div class="page-load-status">
  <div class="infinite-scroll-request" style="display:none">
    <div class="loadspinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
    </div>
  </div>
  <p class="infinite-scroll-error infinite-scroll-last" style="display:none">
    '._lang('Congratulations, you have reached the end!').'
  </p>
</div>
';
}
echo '

</div>';
} else {
echo '<p class="empty-content">'._lang('Nothing here so far.').'</p>';
}
do_action('videoloop-end');
?>