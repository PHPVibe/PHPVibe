<?php the_header();
      the_sidebar(); ?>

<script>
 $(document).ready(function() {
	 if ($(window).width() < 972) {
		 $('#DashContent').addClass('tab-pane active');
		 $('#DashSidebar').addClass('tab-pane');
		 $('#myTab a,#myTabs a').click(function (e) {
           e.preventDefault();
           $(this).tab('show');
         });
	 }
	 });
</script>
<div id="theHolder" class="row tab-content">
    <?php layout('layouts/dashbar'); ?>
    <div id="DashContent" class="col-md-10 col-xs-12 isBoxed">
        <div class="row odet">
            <?php
            if(_get('msg')) {
                echo '<div class="msg-info">'.toDb(_get('msg')).'</div>';
            }
            if(isset($msg)) {echo $msg;}
            do_action('dash-top');
            if((_get('sk') == "edit") || isset($_POST['changeavatar']) || isset($_POST['changecover']) || isset($_POST['changeuser'])  ) {
                include_once(TPL.'/profile/edit.php');
            } elseif(_get('sk') == "activity") {
                include_once(TPL.'/profile/dash-buzz.php');
            } else {
                //Frontpage
                if(get_option('allowpremium') == 1 ) {
                    if( !is_empty(premium_upto())) {
                        if (new DateTime() > new DateTime(premium_upto())) {
                            echo'<div class="block isBoxed msg-content msg-warning"> <a href="'.site_url().'payment"> <i class="material-icons">&#xE8D0;</i> '._lang("Premium expired on").' '.premium_upto().' </a></div>';
                        }else {
                            echo'<div class="block isBoxed msg-content msg-win"> <a href="'.site_url().'payment"> <i class="material-icons">&#xE8D0;</i> '._lang("You are a premium member until").' '.premium_upto().' </a></div>';

                        }
                    }elseif( is_empty(premium_upto()) && !is_moderator()) {
                        echo'<div class="block isBoxed msg-content msg-hint"> <a href="'.site_url().'payment"><i class="material-icons">&#xE8D0;</i> '._lang("Why not try premium?").'</a></div>';
                    }
                }

                $playlists = array(history_playlist(),likes_playlist(),later_playlist());
                foreach ($playlists as $playlist)

                {
                    if($playlist == history_playlist()) {
                        echo '<h2>'._lang("Watch it again").'</h2>';
                    } elseif ($playlist == likes_playlist()) {
                        echo '<h2>'._lang("You've enjoyed this").'</h2>';
                    } else {
                        echo '<h2>'._lang("You wanted to check this").'</h2>';
                    }
                    $options = DB_PREFIX."videos.id,".DB_PREFIX."videos.media,".DB_PREFIX."videos.title,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.duration,".DB_PREFIX."videos.nsfw";
                    $vq = "SELECT ".DB_PREFIX."videos.id, ".DB_PREFIX."videos.title, ".DB_PREFIX."videos.user_id, ".DB_PREFIX."videos.thumb, ".DB_PREFIX."videos.views, ".DB_PREFIX."videos.liked, ".DB_PREFIX."videos.duration, ".DB_PREFIX."videos.nsfw, ".DB_PREFIX."users.group_id, ".DB_PREFIX."users.name AS owner
		FROM ".DB_PREFIX."playlist_data
		LEFT JOIN ".DB_PREFIX."videos ON ".DB_PREFIX."playlist_data.video_id = ".DB_PREFIX."videos.id
		LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id
		WHERE ".DB_PREFIX."playlist_data.playlist =  '".$playlist."'
		ORDER BY ".DB_PREFIX."playlist_data.id DESC ".this_offset(bpp());
                    include(TPL.'/video-carousel.php');
                }

                echo '<div class="block full text-center mtop20 mbot10">
	<a class="btn btn-default" href="'.profile_url(user_id(), user_name()).'"> '._lang("Go to profile").' </a>
	</div>
	';
            }
            do_action('dash-bottom'); ?>
        </div>
        <?php do_action('dashboard-bottom'); ?>
    </div>

</div>