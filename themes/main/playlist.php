<?php if(not_empty($pageinfo)) {
          echo '<span class="badge badge-success pull-right top10 right10 bottom10 left10">'.$pageinfo.'</span>';
      }
?>
<div id="playlist-content" class="playlist-listing isBoxed">
    <div id="playlistdetails" class="playlist-listing-head">
        <div class="playlist-listing-media-left">
            <?php
            $pic = (not_empty($playlist->picture)) ?   $playlist->picture : '';
            echo '<img class="playlist-listing-image" data-background="'.$pic.'" src="'.$pic.'" />';
            echo '
</div>
<div class="playlist-listing-media-body">
<p><a href="'.profile_url($playlist->owner, $playlist->usersname).'">@'._html($playlist->usersname).'</a></p>';
            if($isOwner) {
                echo '<h1 class="playlist-listing-title"><a href="#"  data-pk="'.$playlist->id.'" data-name="playtitle" data-type="text" class="editable-click editable-empty" data-value="'._html($playlist->title).'" title="'._lang('Edit').'">'._html($playlist->title).'</a> </h1>';
            } else {
                echo '<h1 class="playlist-listing-title">'._html($playlist->title).'</h1>';
            }


            echo '
		<div class="playlist-listing-description">';
            if($isOwner) {
                echo '<a href="#"  data-pk="'.$playlist->id.'" data-name="playdesc" data-type="text" class="editable-click editable-empty" data-value="'._html($playlist->description).'" title="'._lang('Edit').'">';
            }
            if(not_empty($playlist->description)) {
                echo $youwrote._html($playlist->description);
            } else {
                echo _lang('No description provided');
            }
            if($isOwner) {
                echo '</a>';
            }
            echo '</div>
	<div class="playlist-listing-btns">';
            if($playlist->ptype ==1) {
                echo '
	<a class="playlist-listing-play tipS" title="'._lang("Play all").'" href="'.site_url().'forward/'.$playlist->id.'/"><i class="icon icon-play-circle"></i>  '.$playlist->views.'</a>
	';
            }
            echo ' <span class="playlist-listing-views"><i class="material-icons">visibility</i> '.$counter.'</span>
	<div class="playlist-listing-fb">
	<div class="fb-like" data-href="'.$canonical.'" data-width="200" data-layout="button" data-action="like" data-size="large" data-share="true"></div>
	</div>
	</div>
	';


            ?>
        </div>
    </div>

    <?php

    if($playlist->ptype ==1) {
        echo '<div class="playlist-listing-list">';
        $options = DB_PREFIX."videos.id,".DB_PREFIX."videos.media,".DB_PREFIX."videos.title,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.duration,".DB_PREFIX."videos.nsfw";
        $vq = "SELECT ".DB_PREFIX."videos.id, ".DB_PREFIX."videos.title, ".DB_PREFIX."videos.user_id, ".DB_PREFIX."videos.thumb, ".DB_PREFIX."videos.views, ".DB_PREFIX."videos.liked, ".DB_PREFIX."videos.duration, ".DB_PREFIX."videos.nsfw, ".DB_PREFIX."users.name AS owner
FROM ".DB_PREFIX."playlist_data
LEFT JOIN ".DB_PREFIX."videos ON ".DB_PREFIX."playlist_data.video_id = ".DB_PREFIX."videos.id
LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id
WHERE ".DB_PREFIX."playlist_data.playlist =  '".$playlist->id."'
ORDER BY ".DB_PREFIX."playlist_data.id DESC ".this_offset(bpp() * 2);
        echo '<div class="loop-content phpvibe-video-list playlist-page-videos">';
        $videos = $db->get_results($vq);
        if($videos) {

            $ps =$canonical.'?p=';
            $a = new pagination;
            $a->set_current(this_page());
            $a->set_first_page(false);
            $a->set_pages_items(5);
            $a->set_per_page(bpp() * 2);
            $a->set_values($counter);

            if($isOwner) {
                echo '<form class="styled" id="MultiVid" action="'.$ps.this_page().'" enctype="multipart/form-data" method="post" onsubmit="return confirm(\''._lang("Are you sure?").'\')">

	<div class="playlist-listing-toolbar">
	<a href="#" class="changeview tipS" title="'._lang("Change view").'">
	<span class="material-icons flip">equalizer</span>
	</a>
		<div class="playlist-listing-toolbar-left">
			<div class="checkbox-custom checkbox-danger checkbox-circle">
			<input type="checkbox" name="checkRows" class="check-all" />
			<label></label>  '._lang("Check all").'
			<button id="submitIT" class="mleft20 btn btn-default btn-sm" type="submit">
      '._lang("Delete selected").'
      </button>

		</div>
	</div>

	</div> ';

            } else {
                echo '
			<div class="playlist-listing-toolbar">
	<a href="#" class="changeview tipS" title="'._lang("Change view").'">
	<span class="material-icons flip">equalizer</span>
	</a>
	</div>';
            }
            echo '<ol class="content--items">';
            foreach ($videos as $video) {
                $title = _html(_cut($video->title, 270));
                $full_title = _html(str_replace("\"", "",$video->title));
                $url = video_url($video->id , $video->title);
                echo '
		<li id="video-'.$video->id.'" class="video">';
                if($isOwner) {
                    echo '<div class="checkbox-custom checkbox-default">
		 <input type="checkbox" name="playlistsRemoval[]" value="'.$video->id.'" />
		 <label></label>
		 </div> ';
                }
                echo '
		<div class="video-thumb">
				<a class="clip-link" data-id="'.$video->id.'" title="'.$full_title.'" href="'.$url.'">
					<span class="clip">
						<img src="'.thumb_fix($video->thumb, true, get_option('thumb-width'), get_option('thumb-height')).'" data-name="'.addslashes(strtok($full_title, " ")).'" /><span class="vertical-align"></span>
					</span>
					<span class="overlay"></span>
				</a>';
                echo '</div>
		<div class="video-details">
			<h5 class="video-title-in-list"><a href="'.$url.'" title="'.$full_title.'">'._html($title).'</a></h5>

		<div class="uploader--link"> <a href="'.profile_url($video->user_id, $video->owner).'" title="'.$video->owner.'">'.$video->owner.' </a></div>

		</div>
			</li>
		';

            }
            echo '</ol></form>';
            $a->show_pages($ps);
        }

        echo '</div></div>';
    } else {
        /* Image album? */
        $options = DB_PREFIX."images.id,".DB_PREFIX."images.title,".DB_PREFIX."images.user_id,".DB_PREFIX."images.source";
        $vq = "SELECT $options, ".DB_PREFIX."users.name AS owner, ".DB_PREFIX."users.avatar
FROM ".DB_PREFIX."playlist_data
LEFT JOIN ".DB_PREFIX."images ON ".DB_PREFIX."playlist_data.video_id = ".DB_PREFIX."images.id
LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id
WHERE ".DB_PREFIX."playlist_data.playlist =  '".$playlist->id."'
ORDER BY ".DB_PREFIX."playlist_data.id DESC ".this_offset(bpp());
        echo '<div id="imagelist-content" class="loop-content phpvibe-video-list playlist-page-videos">';
        $videos = $db->get_results($vq);
        if($videos) {

            $ps =$canonical.'?p=';
            $a = new pagination;
            $a->set_current(this_page());
            $a->set_first_page(false);
            $a->set_pages_items(5);
            $a->set_per_page(bpp());
            $a->set_values($counter);

            if($isOwner) {
                echo '<form class="styled" id="MultiVid" action="'.$ps.this_page().'" enctype="multipart/form-data" method="post" onsubmit="return confirm(\''._lang("Are you sure?").'\')">
	<div class="playlist-listing-toolbar">
		<div class="playlist-listing-toolbar-left">
			<div class="checkbox-custom checkbox-danger checkbox-circle">
			<input type="checkbox" name="checkRows" class="check-all" />
			<label></label>  '._lang("Check all").'
			<button id="submitIT" class="mleft20 btn btn-default btn-sm" type="submit">
      '._lang("Delete selected").'
      </button>

		</div>
	</div>

	</div> ';

            }
            echo '<ol class="content--items">';
            foreach ($videos as $video) {
                $title = _html(_cut($video->title, 270));
                $full_title = _html(str_replace("\"", "",$video->title));
                $url = video_url($video->id , $video->title);
                $video->thumb = site_url().'storage/'.get_option('mediafolder').'/pictures/thumbs/'. $video->source;
                echo '
		<li id="video-'.$video->id.'" class="video">';
                if($isOwner) {
                    echo '<div class="checkbox-custom checkbox-default">
		 <input type="checkbox" name="playlistsRemoval[]" value="'.$video->id.'" />
		 <label></label>
		 </div> ';
                }
                echo '
		<div class="video-thumb">
				<a class="clip-link" data-id="'.$video->id.'" title="'.$full_title.'" href="'.$url.'">
					<span class="clip">
						<img src="'.thumb_fix($video->thumb).'" data-name="'.addslashes(strtok($full_title, " ")).'" /><span class="vertical-align"></span>
					</span>
					<span class="overlay"></span>
				</a>';
                echo '</div>
		<div class="video-details">
			<h5 class="video-title-in-list"><a href="'.$url.'" title="'.$full_title.'">'._html($title).'</a></h5>

		<div class="uploader--link"> <a href="'.profile_url($video->user_id, $video->owner).'" title="'.$video->owner.'">'.$video->owner.' </a></div>

		</div>
			</li>
		';

            }
            echo '</ol></form>';
        }
        if(isset($a)) {
            $a->show_pages($ps);
        }
        echo '</div>';
        echo '</div>';
    }
    ?>
    <script type="text/javascript">
$( document ).ready(function() {
	var firstfound = $('span.clip > img').attr('src');
	if(typeof firstfound != 'undefined') {
	$('img.playlist-listing-image').attr('src' , firstfound);
	} else {
		$('img.playlist-listing-image').attr('src' , '<?php echo tpl().'images/placeholder.png'?>');
		$('.playlist-listing-media-left').css('background-color', '#e6e6e6');

	}
	$('#playlistdetails').editable({
		selector: 'a.editable-click',
		url: '<?php echo site_url(); ?>api/xedit/',
		ajaxOptions: {type: 'POST'},
		send: 'always',
		autotext: 'always',
		savenochange : true,
		mode: 'inline',
		emptytext: '<?php echo _lang('Empty'); ?>',
	error: function(errors) {
		if(errors && errors.responseText) { //ajax error, errors = xhr object
               msg = errors.responseText;
           } else { //validation error (client-side or server-side)
               $.each(errors, function(k, v) { msg += k+": "+v+"<br>"; });
           }
        return msg;

	}
});
});

    </script>
</div>
