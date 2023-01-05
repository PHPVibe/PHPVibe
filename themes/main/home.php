<div id="home-content" class="main-holder col-md-12">
    <?php echo _ad('0','home-start');
          do_action('home-start');
          $boxes = $db->get_results("SELECT * FROM ".DB_PREFIX."homepage ORDER BY ord ASC");
          if ($boxes) {
              $blockclass = 'hide';
              $blockextra = '';
              $bnr = $db->num_rows;
              $i= 1;
              foreach ($boxes as $box) {

                  /* Box start */
                  if(is_empty($box->mtype)) {$box->mtype = 1;}
                  if(is_empty($box->type) || ($box->type == 2) ) {
                      $type = $box->mtype;
                      switch($type){
                          case "1":
                          default:
                              include(TPL.'/box_video.php');
                              break;
                          case "2":
                              include(TPL.'/box_music.php');
                              break;
                          case "3":
                              include(TPL.'/box_pictures.php');
                              break;
                      }
                  } elseif($box->type == 1) {
                      // Html box
                      echo '<div class="row">
	<h1 class="loop-heading">'._html($box->title).'</h1>
	<div class="'.$box->querystring.'">
	'._html($box->ident).'
	</div>
	</div>';
                  } elseif($box->type == 3) {
                      $heading = _html($box->title);
                      $playlist =$db->get_row("SELECT id,ptype FROM ".DB_PREFIX."playlists where id = '".$box->ident."' limit  0,1");
                      if($playlist->ptype ==1) {
                          $options = DB_PREFIX."videos.id,".DB_PREFIX."videos.media,".DB_PREFIX."videos.title,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.duration,".DB_PREFIX."videos.nsfw";
                          $vq = "SELECT ".DB_PREFIX."videos.id, ".DB_PREFIX."videos.title, ".DB_PREFIX."videos.user_id, ".DB_PREFIX."videos.thumb, ".DB_PREFIX."videos.views, ".DB_PREFIX."videos.liked, ".DB_PREFIX."videos.duration, ".DB_PREFIX."videos.nsfw, ".DB_PREFIX."users.group_id, ".DB_PREFIX."users.name AS owner
		FROM ".DB_PREFIX."playlist_data
		LEFT JOIN ".DB_PREFIX."videos ON ".DB_PREFIX."playlist_data.video_id = ".DB_PREFIX."videos.id
		LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id
		WHERE ".DB_PREFIX."playlist_data.playlist =  '".$playlist->id."'
		ORDER BY ".DB_PREFIX."playlist_data.id DESC ".this_offset(bpp());
                          if(isset($box->car) && ($box->car > 0)){
                              include(TPL.'/video-carousel.php');
                          } else {
                              include(TPL.'/video-loop.php');
                          }
                      } else {
                          $heading = _html($box->title);
                          $options = DB_PREFIX."images.id,".DB_PREFIX."images.title,".DB_PREFIX."images.user_id,".DB_PREFIX."images.thumb";
                          $vq = "SELECT $options, , ".DB_PREFIX."users.group_id, ".DB_PREFIX."users.name AS owner, ".DB_PREFIX."users.avatar
		FROM ".DB_PREFIX."playlist_data
		LEFT JOIN ".DB_PREFIX."images ON ".DB_PREFIX."playlist_data.video_id = ".DB_PREFIX."images.id
		LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id
		WHERE ".DB_PREFIX."playlist_data.playlist =  '".$playlist->id."'
		ORDER BY ".DB_PREFIX."playlist_data.id DESC ".this_offset(bpp());
                          if(isset($box->car) && ($box->car > 0)){
                              include(TPL.'/images-carousel.php');
                          } else {
                              include(TPL.'/images-loop.php');
                          }
                      }
                  }elseif($box->type == 4) {
                      include(TPL.'/box_channel.php');
                  }elseif($box->type == 6) {
                      include(TPL.'/box_channels.php');
                  }elseif($box->type == 7) {
                      include(TPL.'/box_playlists.php');
                  }

                  unset($box);
                  if(isset($type)) { unset($type); }
                  if(isset($vq)) { unset($vq); }
                  if(isset($options)) { unset($options); }
                  if(isset($kill_infinite)) { unset($kill_infinite); }
                  //Loading preloaders
                  echo '<div class="homeLoader"> '.fakePlace(7).'  </div>';
              }

              /* Box ended */
              do_action('home-after-block');
          } else {
              echo _lang('Nothing selected for home content.').'<p class="mtop20"><a href="'.site_url().ADMINCP.'/?sk=homepage">'._lang("Choose content").'</a> </p>';
          }
          do_action('home-end');
          echo _ad('0','home-end');
    ?>
</div>
