<?php the_sidebar(); 
      $active = _get("sk"); if(is_null(_get("sk"))) { $active = "profile";}
      do_action('profile-start');
      $vd = $cachedb->get_row("SELECT count(case when pub = 1 then 1 else null end) as nr, sum(views) as vnr , sum(liked) as lnr FROM ".DB_PREFIX."videos where user_id='".$profile->id."'");
      $imgs = $cachedb->get_row("SELECT count(*) as imgnr, sum(views) as vnr, sum(liked) as lnr FROM ".DB_PREFIX."images where user_id='".$profile->id."'");
      $md = $cachedb->get_row("SELECT count(case when pub = 1 then 1 else null end) as nr FROM ".DB_PREFIX."videos where media > 1 and user_id='".$profile->id."'");
      $actives = $cachedb->get_var("SELECT count(*) as active FROM ".DB_PREFIX."activity where user='".$profile->id."'");
?>
<div id="profile-body" class="row full">
<div id="playlist-content" class="playlist-listing isBoxed">
  <div id="playlistdetails" class="playlist-listing-head">
    <div class="playlist-listing-media-left">
      <img class="playlist-listing-image"  src="<?php echo thumb_fix($profile->avatar, true, 260, 260);?>" />
    </div>
    <div class="playlist-listing-media-body">
      <p>
       @<?php echo $profile->username;?>
      </p>
      <h1 class="playlist-listing-title full">
       <?php if($myself) { ?>
       <a href="#" data-pk="5" data-name="profiletitle" data-type="text" class="editable-click editable-empty" data-value="Your username" title="Edit"><?php echo _html($profile->name);  ?></a>
       <?php } else { ?>
           <?php echo _html($profile->name);  ?>
          <?php } ?>
          <div class="pull-right left30">
	  <?php subscribe_box($profile->id); ?>	
	  <?php if (!$myself) { ?>
	  <a href="<?php echo site_url();?>msg/<?php echo $profile->id;?>/" class="tipS left30" title="<?php echo _lang("Message"). ' '._html($profile->name); ?>"><i class="material-icons">chat</i> </a>
	  <?php } ?>
	  </div>
	  </h1>
	   
      <div class="playlist-listing-description">
         <?php if($myself) { ?>
        <a href="#" data-pk="5" data-name="profileabout" data-type="text" class="editable-click editable-empty" data-value="Your about." title="Edit"><?php echo _html($profile->bio);  ?></a>
         <?php } else { ?>
             <?php echo _html($profile->bio);  ?>
          <?php } ?>
      </div>
      <div class="playlist-listing-btns">
	  <?php if ($vd->nr > 1) { ?>
        <a class="playlist-listing-play tipS" title="<?php echo _lang("Play all media");?>" href="<?php site_url(); ?>forward/uvs-<?php echo $profile->id; ?>/">
          <i class="icon icon-play-circle"></i> <?php echo $vd->nr; ?>
		  </a>
		  <?php  } ?>
        <span class="playlist-listing-views tipS" title="<?php echo _lang("Media views received");?>">
          <i class="material-icons">live_tv</i> <?php echo u_k($vd->vnr + $imgs->vnr);?> </span>
        <div class="playlist-listing-views tipS" title="<?php echo _lang("Likes received");?>">
		<i class="material-icons">thumb_up</i><?php echo u_k($vd->lnr + $imgs->lnr);?>
        </div>
		<div class="playlist-listing-views tipS" title="<?php echo _lang("Activities");?>">
		<i class="material-icons">show_chart</i><?php echo $actives;?>
        </div>
		<div class="playlist-listing-views">
		<i class="material-icons">location_on</i> <?php if($profile->local) { ?>  <?php echo _html($profile->local);?>, <?php } ?> <?php if($profile->country) { ?> <?php echo _html($profile->country);?> <?php } else { echo _lang("Somewhere");} ?>
		</div>
	
      </div>
    </div>
  </div>

    <?php if($myself) { ?>
<script>
$( document ).ready(function() {	
	
	$('#playlistdetails').editable({
		selector: 'a.editable-click',		
		url: '<?php echo site_url();?>api/predit/',
		ajaxOptions: {type: 'POST'},  
		send: 'always',	
		autotext: 'always',	
		savenochange : true,		
		mode: 'inline',
		emptytext: '<?php echo _lang('Empty');?>',
	error: function(errors) {
		if(errors && errors.responseText) { 
               msg = errors.responseText;
           } else { 
               $.each(errors, function(k, v) { msg += k+": "+v+"<br>"; });
           }
        return msg;		   
		   
	}
});
});

</script>
    <?php  } ?>
<div class="profile-content">
<div class="profile-hero">
      
<div class="cute">				

<div class="pull-left full block">

<div class="profile-social inline pull-right">
<?php if($profile->twlink) { ?><a target="_blank" rel="nofollow" class="icon icon-twitter" href="<?php echo $profile->twlink;?>"></a> <?php } ?>
<?php if($profile->fblink) { ?><a target="_blank" rel="nofollow" class="icon icon-facebook" href="<?php echo $profile->fblink;?>"></a> <?php } ?>
<?php if($profile->glink) { ?> <a target="_blank" rel="nofollow" class="icon icon-google-plus" href="<?php echo $profile->glink;?>"></a> <?php } ?>
<?php if($profile->iglink) { ?> <a target="_blank" rel="nofollow" class="icon icon-instagram" href="<?php echo $profile->iglink;?>"></a> <?php } ?>
                </div>
</div>
</div>
</div>			
<nav id="profile-nav" class="red-nav">
  <ul>
    <li class="<?php echo aTab("profile");?>"><a href="<?php echo $canonical; ?>"><?php echo _lang("Channel"); ?></a></li>
	<li class="<?php echo aTab("collections");?>"><a href="<?php echo $canonical; ?>?sk=collections"><?php echo _lang("Collections"); ?></a></li>
      <?php if (($vd->nr > 0) || ($imgs->imgnr > 0)) { ?>
    <li class="<?php echo aTab("videos");?>"><a href="<?php echo $canonical; ?>?sk=videos"><?php echo _lang("Videos"); ?></a></li>
    <?php if(get_option('imagesmenu','1') == 1 ) { ?>
	<li class="<?php echo aTab("images");?>"><a href="<?php echo $canonical; ?>?sk=images"><?php echo _lang("Images"); ?></a></li>
    <?php } ?>
	 <?php if(get_option('musicmenu','1') == 1 ) { ?>
	<li class="<?php echo aTab("music");?>"><a href="<?php echo $canonical; ?>?sk=music"><?php echo _lang("Music"); ?></a></li>
   <?php } ?>
      <?php } ?>
   
   <?php if($myself ) { ?>
       <li class="<?php echo aTab("activity");?>"><a href="<?php echo $canonical; ?>?sk=activity"><?php echo _lang("Activity"); ?></a></li>
       <li class="<?php echo aTab("activity");?>"><a href="<?php echo $canonical; ?>?sk=subscribed"><?php echo _lang("Following"); ?></a></li>
       <li class="<?php echo aTab("activity");?>"><a href="<?php echo $canonical; ?>?sk=subscribers"><?php echo _lang("Followers"); ?></a></li>
  <?php } ?>
 </ul>
</nav>
<div id="panel-<?php echo $active;?>" class="panel">
<div class="full">

<?php do_action('profile-precontent');
      switch(_get('sk')){
          case 'subscribed':
              $count = $cachedb->get_row("Select count(*) as nr from ".DB_PREFIX."users where ".DB_PREFIX."users.id in ( select uid from ".DB_PREFIX."users_friends where fid ='".$profile->id."')");
              $vq = "select id,avatar,name,lastnoty from ".DB_PREFIX."users where ".DB_PREFIX."users.id in ( select uid from ".DB_PREFIX."users_friends where fid ='".$profile->id."') ORDER BY ".DB_PREFIX."users.views DESC ".this_offset(18);
              include_once(TPL.'/profile/users.php');	
              $pagestructure = $canonical.'?sk=subscribed&p=';
              $bp = bpp();	
              break;
          case 'images':
              include_once(TPL.'/profile/user_images.php');	
              break;
          case 'subscribers':
              $count = $cachedb->get_row("Select count(*) as nr from ".DB_PREFIX."users where ".DB_PREFIX."users.id in ( select fid from ".DB_PREFIX."users_friends where uid ='".$profile->id."')");
              $vq = "select id,avatar,name,lastnoty from ".DB_PREFIX."users where ".DB_PREFIX."users.id in ( select fid from ".DB_PREFIX."users_friends where uid ='".$profile->id."') ORDER BY ".DB_PREFIX."users.views DESC ".this_offset(18);
              include_once(TPL.'/profile/users.php');	
              $pagestructure = $canonical.'?sk=subscribers&p=';
              $bp = bpp();
              break;
          case 'activity':
              if($myself) {
              $sort =(isset($_GET['sort']) && (intval($_GET['sort']) > 0) ) ? "and type='".intval($_GET['sort'])."'" : "";
              $count = $cachedb->get_row("Select count(*) as nr from ".DB_PREFIX."activity where user='".$profile->id."' ".$sort);
              $vq = "Select * from ".DB_PREFIX."activity where user='".$profile->id."' ".$sort." ORDER BY id DESC ".this_offset(45);
              include_once(TPL.'/profile/activity.php');
              } else {
          echo _lang('This part is private');
          }
              break;	
          case 'videos':
              $pagestructure = $canonical.'?sk=videos&p=';
              $canonical = $canonical.'?sk=videos';
              include_once(TPL.'/profile/user_videos.php');
              break;
          case 'collections':
              $pagestructure = $canonical.'?sk=collections&p=';
              $canonical = $canonical.'?sk=collections';
              include_once(TPL.'/profile/user_collections.php');
              break;
          case 'music':
              $pagestructure = $canonical.'?sk=music&p=';
              $canonical = $canonical.'?sk=music';
              include_once(TPL.'/profile/user_music.php');
              break;	
          default:
              $pagestructure = $canonical.'?p=';
              include_once(TPL.'/profile/user_profile.php');
              break;		
      }
      if(isset($bp) && $pagestructure) {
          $a = new pagination;	
          $a->set_current(this_page());
          $a->set_first_page(true);
          $a->set_pages_items(7);
          $a->set_per_page($bp);
          $a->set_values($count->nr);
          $a->show_pages($pagestructure);
      }
      do_action('profile-postcontent');
?>
</div>
</div>
</div>
</div>

