<?php the_sidebar(); ?>
<div class="row">
<div class="col-md-12 nomargin">
  <div class="row">
 <div id="videolist-content" class="full"> 
<?php echo _ad('0','users-top');

      if(!isset($st)){ $st = ''; }

      if(isset($heading_meta) && !empty($heading_meta)) { echo $heading_meta;}
      if(this_page() < 2) {
          
          $topusers = $db->get_results("select name,group_id,avatar,id from ".DB_PREFIX."users order by views DESC ".this_limit()."");
          if($topusers) {
              echo '<h1 class="loop-heading"><span>'._lang("Popular").'</span></h1>';
              echo '<div class="fake-padding black-slider"> <ul id="carousel" class="owl-carousel">'; 
              foreach ($topusers as $user) {
                  $title = _html(_cut($user->name, 70));
                  if(isset($user->group_id)) { $grcreative= group_creative($user->group_id); } else { $grcreative=''; };
                  $full_title = _html(str_replace("\"", "",$user->name));			
                  $url = profile_url($user->id , $user->name);
                  
                  echo '
<div class="subitem">
		<a class="block text-center" data-id="'.$user->id.'" title="'.$full_title.'" href="'.$url.'">
					<img class="img-circle cartistic" src="'.thumb_fix($user->avatar, true, 100, 100).'" data-name="'.$full_title.'" style="width:100px; height:100px" />
        
		<span class="btn btn-block">'._html($title).' '.$grcreative.'</span>
		</a>
		<div class="block">';
                  subscribe_box($user->id,'',false);
                  echo '</div>
</div>';

              }
              echo '</ul></div>';	
              
              
          }
          $actusers = $db->get_results("select o.name,o.id,o.group_id, o.avatar, c.user, count(c.user) as actives from ".DB_PREFIX."activity c left join ".DB_PREFIX."users o on c.user = o.id group by c.user order by actives desc
 ".this_limit()."");
          if($actusers) {
              echo '<h1 class="loop-heading"><span>'._lang("Highly active").'</span></h1>';
              echo '<div class="fake-padding black-slider"> <ul id="carousel" class="owl-carousel">'; 
              foreach ($actusers as $user) {
                  $title = _html(_cut($user->name, 70));
                  if(isset($user->group_id)) { $grcreative= group_creative($user->group_id); } else { $grcreative=''; };
                  $full_title = _html(str_replace("\"", "",$user->name));			
                  $url = profile_url($user->id , $user->name);
                  
                  echo '
<div class="subitem">
		<a class="block text-center" data-id="'.$user->id.'" title="'.$full_title.'" href="'.$url.'">
					<img class="img-circle cartistic" src="'.thumb_fix($user->avatar, true, 100, 100).'" data-name="'.$full_title.'" style="width:100px; height:100px" />
        
		<span class="btn btn-block">'._html($title).' '.$grcreative.'</span>
		</a>
		<div class="block">';
                  subscribe_box($user->id,'',false);
                  echo '</div>
</div>';

              }
              echo '</ul></div>';	
              
              
          }	
          
          
      }
      echo '<h2 class="loop-heading text-left"><span>'._lang("Recently online").'</span></h2>';
      if ($users) {

          echo '<div id="ChannelResults" class="loop-content phpvibe-video-list ">'; 
          foreach ($users as $user) {
              $title = _html(_cut($user->name, 70));
              $full_title = _html(str_replace("\"", "",$user->name));	
              if(isset($user->group_id)) { $grcreative= group_creative($user->group_id); } else { $grcreative=''; };
              $url = profile_url($user->id , $user->name);
              
              echo '
<div id="video-'.$user->id.'" class="video">
<div class="video-inner">
<div class="video-thumb">
		<a class="clip-link" data-id="'.$user->id.'" title="'.$full_title.'" href="'.$url.'">
			<span class="clip">
				<img class="img-circle" src="'.thumb_fix($user->avatar, true, 130, 130).'" alt="'.$full_title.'" style="width:130px; height:130px; background:none"/><span class="vertical-align"></span>
			</span>
          	
		</a>';
              
              echo '</div>	
<div class="video-data mtop10">';	
              echo '	<h4 class="video-title"><a href="'.$url.'" title="'.$full_title.'">'._html($title).'</a> '.$grcreative.'</h4>
	<p style="font-size:12px">'._html(_cut($user->bio,170)).'</p>
<ul class="stats">	';
              echo '<li>';
              if($user->lastNoty) {
                  if(date('d-m-Y', strtotime($user->lastNoty)) != date('d-m-Y')) {
                      echo '<i class="icon-circle-thin offline" style="margin-right:9px;"></i>';
                  } else {
                      echo '<i class="icon-circle-thin online" style="margin-right:9px;"></i>';
                  }
              }
              echo time_ago($user->lastlogin).'</li>
</ul>
</div>	
	</div>
		</div>
';
          }
          $a->show_pages($ps);
          echo ' <br style="clear:both;"/></div>';
      } else {
          echo _lang('Sorry but there are no results.');
      }

      echo _ad('0','users-bottom');
?>
</div>
<?php $ad = _ad('0','members-sidebar');
      if(!empty($ad)) {
          echo '<div id="SearchSidebar" class="col-md-4 oboxed">'.$ad.'</div>';
      }
?>
</div>
