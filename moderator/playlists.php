<?php
if(isset($_GET['delete-playlist'])) {
delete_playlist(intval($_GET['delete-playlist']));
echo '<div class="msg-info">Playlist #'.$_GET['delete-playlist'].' deleted.</div>';
} 
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
delete_playlist(intval($del));
}
echo '<div class="msg-info">Playlists #'.implode(',', $_POST['checkRow']).' deleted.</div>';
}
$add = "";
if(_get('sort')) {
$add = 'and ptype = '.intval(_get('sort')). ' ';	
}
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."playlists WHERE ".DB_PREFIX."playlists.picture not in ('[likes]','[history]','[later]') $add");
$playlists = $db->get_results("select ".DB_PREFIX."playlists.*, ".DB_PREFIX."users.name as user from ".DB_PREFIX."playlists LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."playlists.owner = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."playlists.picture not in ('[likes]','[history]','[later]') $add order by id DESC ".this_limit()."");

if($playlists) {

if(_get('sort')) {
$ps = admin_url('playlists').'&sort='._get('sort').'&p=';	
} else {
$ps = admin_url('playlists').'&p=';
}
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
$a->show_pages($ps);
?>
<form class="form-horizontal styled" action="<?php echo admin_url('playlists');?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">
<h3>Collections</h3>
<div class="cleafix full"></div>
<fieldset>
<div class="panel top10 multicheck">

<div class="panel-heading">
<h3 class="panel-title">
<i class="material-icons">
playlist_play

</i>
<a href="<?php echo admin_url('playlists');?>&sort=1" class="right10"><small>Playlists</small></a>	
<a href="<?php echo admin_url('playlists');?>&sort=2"><small>Albums</small></a>	
</h3> 
<ul class="panel-actions">
<li>
<div class="checkbox-custom checkbox-danger"> <input type="checkbox" name="checkRows" class="check-all" /> <label for="checkRows"></label> </div>
</li>
<li>
<button class="btn btn-large btn-danger btn-raised" type="submit"><?php echo _lang("Delete selected"); ?></button>
</li>
</ul>
</div>

 <div class="panel-body" style="border-top: 1px solid #e4eaec; padding-top:15px;">
						  <div class="multilist">
						  <ul class="list-group">
						  <?php foreach ($playlists as $video) { ?>
                              <li class="list-group-item">
                             <div class="row">
							 <div class="inline-block img-hold">
							 <div class="inline-block right20 img-checker">
							  <span class="pull-left mg-t-xs mg-r-md top20">
                                 <input type="checkbox" name="checkRow[]" value="<?php echo $video->id; ?>" class="styled" />
								 </span>
                                  <span class="pull-left mg-t-xs mg-r-md">
								  <img class="row-image <?php if(is_empty($video->picture)) { echo 'NoAvatar';} ?>" data-name="<?php echo stripslashes($video->title); ?>" src="<?php if(not_empty($video->picture)) { echo thumb_fix($video->picture); } ?>">
								 </span>
								 </div>
								 <div class="inline-block right20 img-txt">
                                  <h4>  <?php echo _html($video->title); ?></h4>
                                 <a href="<?php echo profile_url($video->owner, $video->user); ?>" target="_blank"><?php echo $video->user; ?></a></td>                                 
								<p>
								  <small>	
								  <?php if($video->ptype > 1) {echo "Pictures album";} else {echo "Media playlist";} ?>
								  </small>		
								</p>
								</div>
									 </div>						 
								  <div class="btn-group btn-group-vertical pull-right">								
								  <a class="btn btn-sm btn-outline btn-danger confirm" href="<?php echo admin_url('playlists');?>&p=<?php echo this_page();?>&delete-playlist=<?php echo $video->id;?>">
								  <i class="material-icons mright10"> delete </i><?php echo _lang("Delete"); ?>
								  </a>
								  <a class="btn btn-sm btn-raised btn-primary" href="<?php echo admin_url('edit-playlist');?>&id=<?php echo $video->id;?>">
								  <i class="material-icons mright10"> edit </i><?php echo _lang("Edit"); ?>
								  </a>
								  <a class="btn btn-sm btn-outline btn-success" target="_blank" href="<?php echo playlist_url($video->id, $video->title);?>">
								  <i class="material-icons mright10"> link </i><?php echo _lang("View"); ?>
								  </a>								  
								  </div>
								  </div>
                              </li>
							  <?php } ?>
						</ul>  
</div>
</div>						
</fieldset>					
</form>
</div>	
<?php  $a->show_pages($ps); } ?>
