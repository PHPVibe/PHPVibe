<?php the_sidebar(); ?>
<div class="row">
<div class="col-md-10 nomargin">
  <div class="row">
 <div id="videolist-content" class="oboxed col-md-8"> 
<?php echo _ad('0','playlists-top');
      if(!isset($st)){ $st = ''; }
      if(isset($heading) && !empty($heading)) { echo '<h1 class="loop-heading"><span>'._html($heading).'</span>'.$st.'</h1>';}
      if(isset($heading_meta) && !empty($heading_meta)) { echo $heading_meta;}
      if ($playlists) {

          echo '<div id="PlaylistResults" class="loop-content">'; 
          foreach ($playlists as $pl) {
              $title = _html(_cut($pl->title, 170));
              $full_title = _html(str_replace("\"", "",$pl->title));			
              $url = playlist_url($pl->id , $pl->title);
              $plays = 0;
              $ov = '';	
              $grid = '';
              $pictures = get_album_img($pl->id);		
              if($pictures) {
                  $grid = '<div class="grid-pic-wrapp grid-col-'.count($pictures).'">';	
                  $i = 1;
                  foreach($pictures as $picture) {					
                      //print_r($picture);
                      $thumb = site_url().'storage/'.get_option('mediafolder').'/pictures/thumbs/'. $picture->source;
				  	$grid .= '<div class="grid-pic-item pic-'.$i.' list-of-'.count($pictures).'">';
                      $grid .= '<img src="'.$thumb.'"/>';
                      $grid .= '</div>';
                      $i++;
                  }
                  $grid .= '</div>';
                  //echo $grid;
              }
              if(not_empty( $pl->picture)){
                  $image = '<span class="clip"><img src="'.thumb_fix($pl->picture, true, get_option('thumb-width'), get_option('thumb-height')).'" alt="'.$full_title.'" /></span>';			
              }
              if(not_empty( $grid)) {
                  $image = $grid;
              }else {
                  $image = '<span class="clip"><img class="NoAvatar" src="" data-name="'.addslashes(trim($full_title)).'" /></span>';	
              }
              if($pl->owner == user_id()) { 
                  $ol = $url;
              } else {
                  $ol = site_url().'forward/'.$pl->id;
              }
              if($pl->ptype == 1) { 
                  $ov = _lang("Play all"); 
                  $ico = '<i class="material-icons">&#xE04A;</i>';
                  
              } else {
                  $ov = _lang("View all");
                  $ico = '<i class="material-icons">&#xE030;</i>';
              }
              $ove = '<div class="playlists-overlay"> <a title="'._lang($ov).'" href="'.$url.'"> '.$ov.' </a>	</div> ';
              if(isset($entries[$pl->id])) {$plays = intval($entries[$pl->id]); }
              echo '
<div id="video-'.$pl->id.'" class="video">
<div class="video-inner"><div class="video-thumb">
'.$image.'
<a class="playlist-cover-a" href="'.$url.'" "></a>
<span class="timesplayed"> '.$plays.' <i class="material-icons">&#xE038;</i></span>
</div>	</div>	
<div class="video-data">
	<h3 class="video-title"><a href="'.$url.'" title="'.$full_title.'">'._html($title).'</a> </h3>
	<span class="playlist-descrip">'._html(_cut($pl->description,270)).'</span>

</div>	
	</div>
	
';
          }
          $a->show_pages($ps);
          echo ' <br style="clear:both;"/></div>';
      } else {
          echo _lang('Sorry but there are no results on this page.');
      }


      echo _ad('0','playlists-bottom');
?>
</div>
<?php $ad = _ad('0','playlists-sidebar');
      if(!empty($ad)) {
          echo '<div id="SearchSidebar" class="col-md-4 oboxed">'.$ad.'</div>';
      }
?>
</div>
