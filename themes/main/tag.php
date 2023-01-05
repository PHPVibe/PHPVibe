<?php the_sidebar();
      /* Most liked , Most viewed time sorting */
      $st = '
<div class="inline-block pull-right relative">
       <a data-toggle="dropdown" class="btn dropdown-toogle text-uppercase"><i class="material-icons">&#xE152;</i> '._lang("Sort").'</a>
			<ul class="dropdown-menu dropdown-menu-right bullet">
			<li title="'._lang("This Week").'"><a href="'.site_url().thetags.url_split.str_replace(array(" "),array("-"),$key).'?sort=mv"><i class="icon material-icons">&#xE8E5;</i>'._lang("Popular").'</a></li>
			<li title="'._lang("This Week").'"><a href="'.site_url().thetags.url_split.str_replace(array(" "),array("-"),$key).'?sort=ml"><i class="icon material-icons">&#xE8DD;</i>'._lang("Liked").'</a></li>
		</ul>
		</div>
';

?>
<div class="row">
    <div id="videolist-content" class="oboxed full">
        <?php echo _ad('0','search-top');
              if(isset($vq) && !nullval($vq)) { $videos = $db->get_results($vq); } else {$videos = false;}
              if(!isset($st)){ $st = ''; }

              if(isset($heading) && !empty($heading)) { echo '<h1 class="loop-heading"><span>'._html($heading).'</span>'.$st.'</h1>';}
              if(isset($heading_meta) && !empty($heading_meta)) { echo $heading_meta;}

              if ($videos) {
                  echo $precontent;
                  echo '<div id="tsgResults" class="loop-content phpvibe-video-list ">';
                  foreach ($videos as $video) {
                      $title = _html(_cut($video->title, 100));
                      $full_title = _html(str_replace("\"", "",$video->title));
                      $url = video_url($video->id , $video->title);
                      $watched = (is_watched($video->id)) ? '<span class="vSeen">'._lang("Watched").'</span>' : '';
                      $wlater = (is_user()) ? '<a class="laterit" title="'._lang("Add to watch later").'" href="javascript:Padd('.$video->id.', '.later_playlist().')"><i class="material-icons">&#xE924;</i></a>' : '';
                      if(isset($video->group_id)) { $grcreative= group_creative($video->group_id); } else { $grcreative=''; };
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
<li class="uploaderlink">#'.ucfirst(str_replace(array("+","-")," ",$key)).'</li>
 <li>'.$video->views.' '._lang('views').'</li>';
                      if(isset($video->date)) { echo '<li>'.time_ago($video->date).'</li>';}
                      echo '</ul>

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
    <div class="loadspinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
    </div>

  </div>
  <p class="infinite-scroll-error infinite-scroll-last" style="display:none">
    '._lang('The end!').'
  </p>
</div>
';
                  echo ' <br style="clear:both;"/></div>';
              } else {

                  echo '<div class="msg-info">'._lang('Sorry but nothing is tagged with this.').'</div>';
                  echo $precontent;
              }

              echo _ad('0','search-bottom');
        ?>
    </div>
</div>