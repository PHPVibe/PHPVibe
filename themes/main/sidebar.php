<div id="sidebar" class="<?php if(is_video() || is_picture() || is_com('conversation')) {echo 'hide';} ?>"> 
<div class="sidescroll">
<?php do_action('sidebar-start');
      echo _ad('0','sidebar-start');
      /* The specials */
      echo '<div class="sidebar-specials">';
      /* echo com(); */
      $cat_for_video = array('videolist','category');
      if( in_array(com(),$cat_for_video )) { ?>
<div class="sidebar-nav blc">
<ul>
    <li class="ajaxed <?php aTab(browse);?>" ><a href="<?php echo list_url('browse'); ?>"> <i class="material-icons">&#xE038;</i> <?php echo _lang('Recent'); ?> </li></a>
    <li class="ajaxed <?php aTab(mostviewed);?>" ><a href="<?php echo list_url(mostviewed); ?>"> <i class="material-icons">&#xE8E5;</i> <?php echo _lang('Top'); ?></a></li>
    <li class="ajaxed <?php aTab(mostliked);?>" ><a href="<?php echo list_url(mostliked); ?>"> <i class="material-icons">&#xE8DD;</i> <?php echo _lang('Liked'); ?></a></li>
    <li class="ajaxed <?php aTab(mostcom);?>" ><a href="<?php echo list_url(mostcom); ?>"> <i class="material-icons">&#xE0B7;</i> <?php echo _lang('Commented'); ?></a></li>
    <li class="ajaxed <?php aTab(promoted);?>" ><a href="<?php echo list_url(promoted); ?>"> <i class="material-icons">&#xE41B;</i> <?php echo _lang('Picks'); ?></a></li>
	<?php 
          if(get_option('premiumhub',1) == 1 ) {
              echo '<li><a href="'.hub_url(browse).'"><i class="material-icons">&#xE8D0;</i> '._lang('Premium Hub').'</a></li>';	
          }
          
          if(_UpVideo() || _EmbedVideo()) { ?>
	<li><a href="<?php echo site_url().add; ?>"> <i class="material-icons">&#xE146;</i> <?php echo _lang('Add a video'); ?></a></li>
    <?php } ?>
	</ul></div>
<?php	
          echo '<div class="load-cats" data-type="1">&nbsp;</div>';
      }
      $cat_for_images = array('imageslist','categoryimage');
      if( in_array(com(),$cat_for_images )) { ?>
<div class="sidebar-nav blc"><ul>
    <li class="<?php aTab(browse);?>" ><a href="<?php echo images_url('browse'); ?>"> <i class="icon icon-eye"></i> <?php echo _lang('Recent'); ?> </li></a>
    <li class="<?php aTab(mostviewed);?>" ><a href="<?php echo images_url(mostviewed); ?>"> <i class="icon icon-line-chart"></i> <?php echo _lang('Top'); ?></a></li>
    <li class="<?php aTab(mostliked);?>" ><a href="<?php echo images_url(mostliked); ?>"> <i class="icon icon-heart"></i> <?php echo _lang('Most Liked'); ?></a></li>
    <li class="<?php aTab(mostcom);?>" ><a href="<?php echo images_url(mostcom); ?>"> <i class="icon icon-comments"></i> <?php echo _lang('Discussed'); ?></a></li>
    <li class="<?php aTab(promoted);?>" ><a href="<?php echo images_url(promoted); ?>"> <i class="icon icon-bullhorn"></i> <?php echo _lang('Featured'); ?></a></li>
	<?php 
          echo '<li><a href="'.site_url().albums.'"><i class="material-icons">&#xE43C;</i> '._lang('Galleries').'</a></li>';
          if(_UpImage()) { ?>
	<li ><a href="<?php echo site_url().upimage; ?>"> <i class="icon material-icons">&#xE146;</i> <?php echo _lang('Add a image'); ?></a></li>
    <?php } ?>
</ul></div>
<div class="load-cats" data-type="3">&nbsp;</div>
<?php  }
      $cat_for_music = array('musiclist','categorymusic');
      if( in_array(com(),$cat_for_music )) {
?>
<div class="sidebar-nav blc"><ul>
	<li class="ajaxed <?php aTab(browse);?>" ><a href="<?php echo music_url('browse'); ?>"> <i class="material-icons">&#xe01d </i> <?php echo _lang('Recent'); ?> </li></a>
	<li class="ajaxed <?php aTab(mostviewed);?>" ><a href="<?php echo music_url(mostviewed); ?>"> <i class="icon icon-line-chart"></i> <?php echo _lang('Top'); ?></a></li>
    <li class="ajaxed <?php aTab(mostliked);?>" ><a href="<?php echo music_url(mostliked); ?>"> <i class="material-icons">&#xe8dc </i> <?php echo _lang('Most Liked'); ?></a></li>
    <li class="ajaxed <?php aTab(mostcom);?>" ><a href="<?php echo music_url(mostcom); ?>"> <i class="icon icon-comments"></i> <?php echo _lang('Discussed'); ?></a></li>
    <li class="ajaxed <?php aTab(promoted);?>" ><a href="<?php echo music_url(promoted); ?>"> <i class="icon icon-bullhorn"></i> <?php echo _lang('Featured'); ?></a></li>
	<?php if(_UpMusic() || _EmbedMusic()) { ?>
	<li><a href="<?php echo site_url().upmusic; ?>"> <i class="material-icons">&#xE146;</i> <?php echo _lang('Add a song'); ?></a></li>
    <?php } 

          echo '</ul>	</div><div class="load-cats" data-type="2"> &nbsp; </div>';
      }
      echo '</div>';
      /* The main menu	 */
      echo '<div class="sidebar-nav blc"><ul>';
      echo '<li class="lihead"><a href="'.site_url().'"><span class="iconed"><i class="material-icons">&#xE88A;</i></span> '._lang('Home').'</a></li>';
      /*
      echo '<li class="lihead hidden-md hidden-lg visible-sm-block visible-sm visible-xs-block visible-xs">
      <a data-target="#search-now" data-toggle="modal" href="javascript:void(0)" id="show-searched2"> <span class="iconed"> <i class="material-icons">&#xE8B6;</i> </span>'._lang('Search').'</a>
      </a>';
       */

      echo '<li class="lihead hlt"><a href="'.list_url(browse).'"><span class="iconed"><i class="material-icons">&#xe04b;</i></span> '._lang('Videos').'</a></li>';
      if(get_option('musicmenu') == 1 ) {
          echo '<li class="lihead hlt"><a href="'.music_url(browse).'"><span class="iconed"><i class="material-icons">&#xE1B8;</i></span> '._lang('Music').'</a></li>';	
      }
      if(get_option('imagesmenu') == 1 ) {
          echo '<li class="lihead hlt"><a href="'.images_url(browse).'"><span class="iconed"><i class="material-icons">&#xE413;</i></span> '._lang('Pictures').'</a></li>';
      }

      if(get_option('showusers',1) == 1 ) {
          echo '<li class="lihead"><a href="'.site_url().members.'/"><span class="iconed"><i class="material-icons">&#xe7fd;</i></span>'._lang('Channels').'</a></li>';
      }if(get_option('showplaylists',1) == 1 ) {echo '<li class="lihead"><a href="'.site_url().playlists.'/"><span class="iconed"><i class="material-icons">&#xE431;</i></span>'._lang('Collections').'</a></li>';}
      if(get_option('showblog',1) == 1 ) {
          echo '<li class="lihead"><a href="'.site_url().blog.'/"><span class="iconed"><i class="material-icons">&#xE8CD;</i></span>'._lang('Blog').'</a></li>';
      }
      echo '</ul></div>';
      /* End of menu */
    ?>
<?php	
if (is_user()) {
    /* start my playlists */	
?>
<h4 class="li-heading"><?php echo _lang('My collections'); ?></h4>
<div class="sidebar-nav blc"><ul>
<?php 
    echo '<li><a href="'.site_url().me.'/?sk=likes"><span class="iconed"><i class="material-icons">&#xE8DC;</i></span> '. _lang('Likes').'</a> </li>
<li><a href="'.site_url().me.'/?sk=history"><span class="iconed"><i class="material-icons">&#xE889;</i></span> '. _lang('History').'</a> </li>
<li><a href="'.site_url().me.'/?sk=later"><span class="iconed"><i class="material-icons">&#xE924;</i></span> '. _lang('Watch Later').'</a> </li>
';
    $plays = $cachedb->get_results("SELECT id, title FROM ".DB_PREFIX."playlists where owner= '".user_id()."' and picture not in ('[likes]','[history]','[later]') and ptype < 2 order by title asc limit 0,100");
    if($plays) { 
        foreach ($plays as $play) {
            echo '<li>
<a href="'.playlist_url($play->id, $play->title).'" original-title="'.$play->title.'" title="'.$play->title.'"><i class="material-icons">&#xE064;</i>
'._html(_cut($play->title, 24)).'
</a>
</li>';
        }
    }

    /* end my playlists */
    /* start my albums */
    $albums = $cachedb->get_results("SELECT id, title FROM ".DB_PREFIX."playlists where owner= '".user_id()."' and picture not in ('[likes]','[history]','[later]') and ptype > 1 order by title asc limit 0,100");
    if($albums) { 
        foreach ($albums as $play) {
            echo '<li>
<a href="'.playlist_url($play->id, $play->title).'" original-title="'.$play->title.'" title="'.$play->title.'"><i class="material-icons">&#xE1bc;</i>
'._html(_cut($play->title, 24)).'
</a>
</li>';
        }
    }
    echo '</ul>
</div>';

    /* end my albums */	
    /* start my  subscriptions */ 
    $followings = $cachedb->get_results("SELECT id,avatar,name from ".DB_PREFIX."users where id in (select uid from ".DB_PREFIX."users_friends where fid ='".user_id()."') order by lastNoty desc limit 0,15");
    if($followings) {
        $snr = $cachedb->num_rows;
?>
<h4 class="li-heading">
<a style="color: #797E89;" href="<?php echo profile_url(user_id(), user_name()); ?>?sk=subscribed" title="<?php echo _("View all"); ?>"><?php echo _lang('My subscriptions'); ?> </a>
</h4>
<div class="sidebar-nav blc"><ul>
<?php
        foreach ($followings as $following) {
            echo '
<li>
<a title="'.$following->name.'" href="'.profile_url($following->id , $following->name).'">
<img src="'.thumb_fix($following->avatar, true, 27, 27).'" alt="'.$following->name.'" />'._html(_cut($following->name, 20)).'';
            echo '
</a></li>';
        }
        echo '</ul>
</div>
';
    }
    /* end subscriptions */
    do_action('user-sidebar-end');	
} else {
    do_action('guest-sidebar');					
}
do_action('sidebar-end');
echo lang_menu();
echo _ad('0','sidebar-end')
?>	
<div class="sidebar-nav blc mtop20"><ul>
<?php
$posts = $cachedb->get_results("select title,pid from ".DB_PREFIX."pages where menu = 1 ORDER BY m_order, title ASC limit 0,100");
if($posts) {
	foreach ($posts as $px) {
		echo '<li><a href="'.page_url($px->pid, $px->title).'" title="'._html($px->title).'"> '._cut(_html($px->title),22).'</a></li>';	
	}	
}
?>
</ul>
</div>
<div class="blc" style="height:400px">&nbsp;</div>
</div>
</div>