<?php the_sidebar();
      /* Most liked , Most viewed time sorting */
      $st = '
<div class="inline-block pull-right relative">
       <a data-toggle="dropdown" class="btn dropdown-toogle text-uppercase"><i class="material-icons">&#xE152;</i> '._lang("Refine").'</a>
			<ul class="dropdown-menu dropdown-menu-right bullet">
			<li title="'._lang("This Week").'"><a href="'.site_url().show.url_split.str_replace(array(" "),array("-"),$key).'?sort=w"><i class="icon material-icons">&#xE425;</i>'._lang("This Week").'</a></li>
			<li title="'._lang("This Month").'"><a href="'.site_url().show.url_split.str_replace(array(" "),array("-"),$key).'?sort=m"><i class="icon material-icons">&#xE425;</i>'._lang("This Month").'</a></li>
			<li title="'._lang("This Year").'"><a href="'.site_url().show.url_split.str_replace(array(" "),array("-"),$key).'?sort=y"><i class="icon material-icons">&#xE425;</i>'._lang("This Year").'</a></li>
			<li class="divider" role="presentation"></li>
			<li title="'._lang("All the time").'"><a href="'.site_url().show.url_split.str_replace(array(" "),array("-"),$key).'"><i class="icon material-icons">&#xE922;</i>'._lang("Always").'</a></li>
		</ul>
		</div>
';

?>
<div class="row">
    <div id="videolist-content" class="oboxed">
        <?php echo _ad('0','search-top');

              if(!nullval($vq)) { $videos = $db->get_results($vq); } else {$videos = false;}
              if(!isset($st)){ $st = ''; }

              if(isset($heading) && !empty($heading)) { echo '<h1 class="loop-heading"><span>'._html($heading).'</span>'.$st.'</h1>';}
              if(isset($heading_meta) && !empty($heading_meta)) { echo $heading_meta;}
              if ($videos) {

                  echo '<div id="PlaylistResults" class="loop-content loop-4 ">';
                  foreach ($videos as $video) {
                      $title = _html(_cut($video->title, 100));
                      $full_title = _html(str_replace("\"", "",$video->title));
                      $url = video_url($video->id , $video->title);
                      $watched = (is_watched($video->id)) ? '<span class="vSeen">'._lang("Watched").'</span>' : '';
                      $wlater = (is_user()) ? '<a class="laterit" title="'._lang("Add to watch later").'" href="javascript:Padd('.$video->id.', '.later_playlist().')"><i class="material-icons">&#xE924;</i></a>' : '';
                      if(isset($video->group_id)) { $grcreative= group_creative($video->group_id); } else { $grcreative=''; };
                      $description = _html($video->description);
                      $description = _cut(trim($description),320);
                      if(empty($description)) {$description = $full_title;}
                      echo '
<div id="video-'.$video->id.'" class="video">
<div class="video-inner">
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
<li class="uploaderlink">'._lang("by").' <a href="'.profile_url($video->user_id, $video->owner).'" title="'.$video->owner.'">'.$video->owner.'</a> '.$grcreative.'</li>
 <li>'.$video->views.' '._lang('views').'</li>';
                      if(isset($video->date)) { echo '<li>'.time_ago($video->date).'</li>';}
                      echo '</ul>
	<p>'.$description.'</p>
</div>
	</div>
		</div>
';
                  }
                  if(_get('sort')) {
                      echo '<nav id="page_nav"><a href="'.$canonical.'?p='.next_page().'&sort='.toDb(_get('sort')).'"></a></nav>';
                  } else {
                      echo '<nav id="page_nav"><a href="'.$canonical.'?p='.next_page().'"></a></nav>';
                  }
                  echo '
<div class="page-load-status">
  <div class="infinite-scroll-request" style="display:none">
    <div class="cp-spinner cp-flip"></div>
    <p>'._lang('Loading...').'</p>
  </div>
  <p class="infinite-scroll-error infinite-scroll-last" style="display:none">
    '._lang('The end!').'
  </p>
</div>
';
                  echo ' <br style="clear:both;"/></div>';
              } else {
                  echo _lang('Sorry but there are no results.');
              }

              echo _ad('0','search-bottom');
        ?>
    </div>
</div>