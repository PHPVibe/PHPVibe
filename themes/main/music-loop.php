<?php do_action('songloop-start');
if(!nullval($vq)) { $songs = $db->get_results($vq); } else {$songs = false;}
if(!isset($st)){ $st = ''; }
if(isset($heading) && !empty($heading)) { echo '<h1 class="loop-heading">'._html($heading).' '.$st.'</h1>';}
if(isset($heading_plus) && !empty($heading_plus)) { echo '<small class="songd">'.$heading_plus.'</small>';}
if ($songs) {
echo '<ul class="songs list-group list-group-dividered list-group-full">';
foreach ($songs as $song) {
			$title = _html(_cut($song->title, 70));
			$description = $full_title = _html(str_replace("\"", "",$song->title));

            $full_description = _html(str_replace("\"", "",$description));
			$url = video_url($song->id , $song->title);
			//$watched = (is_watched($song->id)) ? '<span class="badge badge-primary badge-sm">'._lang("Listened").'</span>' : '';
			$liked = (is_liked($song->id)) ? '' : '<a class="heartit  pv_tip" data-toggle="tooltip" data-placement="left" title="'._lang("Like this song").'" href="javascript:iLikeThis('.$song->id.')"><i class="material-icons">&#xE8DC;</i></a>';
            $wlater = (is_user()) ? '<a class="laterit pv_tip" data-toggle="tooltip" data-placement="right" title="'._lang("Listen later").'" href="javascript:Padd('.$song->id.', '.later_playlist().')"><i class="material-icons">&#xE924;</i></a>' : '';
			echo '
            <li id="song-'.$song->id.'" class="list-group-item song">
                  <div class="media">
                    <div class="media-left">
                    <a class="song song-thumb" href="'.$url.'">';
					if(is_empty($song->thumb) || _contains($song->thumb,"xmp3.jpg")) {
                        echo '<img src="" class="NoAvatar" data-name="'.$song->title.'">';
					} else {
					   echo '<img src="'.thumb_fix($song->thumb, true, 270, 169).'" alt="'.$song->title.'" data-name="'.$song->title.'">';

					}
				echo '
				<span class="badge badge-radius badge-dark"> '.video_time($song->duration).'</span>
				</a>

                    </div>
                    <div class="media-body">
                      <div>
                       <span> <a href="'.$url.'" class="song-title"><h4>'.$full_title.'</h4></a> </span>
                       <div class="song-owner">@<a href="'.profile_url($song->user_id, $song->owner).'" title="'.$song->owner.'">'.$song->owner.'</a> '.time_ago($song->date).'</div>
					  <div class="song-icons">
					   <i class="material-icons">&#xe1b8</i> '.u_k($song->views).'
					   <i class="material-icons">&#xe8dc </i> '.u_k($song->liked).'
					  <div class="song-actions">
					   '.$liked.$wlater.'
					   </div>
					   </div>
					  </div>
                    </div>
                  </div>
                </li>';
}
echo '</ul>';
echo _ad('0','after-song-loop');
/* Kill for home if several blocks */

if(!isset($kill_infinite) || !$kill_infinite) {
if(!_contains($canonical,"?")) {
echo '
<nav id="page_nav"><a href="'.$canonical.'?ajax=1&p='.next_page().'"></a></nav>
';
} else {
echo '
<nav id="page_nav"><a href="'.$canonical.'&ajax=1&p='.next_page().'"></a></nav>
';
}
}
echo ' <br style="clear:both;"/>';
} else {
echo '<p class="empty-content">'._lang('Nothing here so far.').'</p>';
}
do_action('songloop-end');
?>