<?php $id = token_id();
$pageinfo = '';
if($id > 0) {
$playlist = $db->get_row("SELECT a.*, b.name as usersname FROM ".DB_PREFIX."playlists a left join ".DB_PREFIX."users b on a.owner = b.id where a.id = '".$id ."' limit  0,1");$counter = $db->get_var("SELECT count(*) FROM ".DB_PREFIX."playlist_data where playlist = '".$id ."'");
//var_dump($_POST);
	if ($playlist) {	    
	/* Is it the owner? */		
	if(is_user() && ($playlist->owner == user_id())) {	
		$isOwner= true;
		//$youwrote= _lang('You described it: ').' ';
		$youwrote= '';			
	} else {			
		$isOwner= false;			
		$youwrote= '';		
	} 		
	/* Handle removal requests */		
	if($isOwner) {			
		if(isset($_POST['playlistsRemoval'])) { 
		playlist_remove($id, $_POST['playlistsRemoval']); 
		$pageinfo = count($_POST['playlistsRemoval']).' '._lang('videos have been removed from this playlist');
		} 			
	}
	// Canonical url
	$canonical = playlist_url($playlist->id , $playlist->title);   
	// SEO Filters
	function modify_title( $text ) {
	global $playlist;
		return strip_tags(stripslashes(get_option('seo-playlist-pre','').$playlist->title.get_option('seo-playlist-post','')));
	}
	function modify_desc( $text ) {
	global $playlist;
		return _cut(strip_tags(stripslashes($playlist->title.':'.$playlist->description)), 160);
	}
	add_filter( 'phpvibe_title', 'modify_title' );
	add_filter( 'phpvibe_desc', 'modify_desc' );
	/*Now to the actual channel page */
	if (!is_ajax_call()) { 
	the_header();
	the_sidebar();
	}
	include_once(TPL.'/playlist.php');
	the_footer();
	//Increase views
	$db->query("UPDATE ".DB_PREFIX."playlists SET views = views+1 WHERE id = '".$playlist->id."'");
	} else {
	//Oups, not found
	layout('404');
	}
}else {
//Oups, not found
layout('404');
}
?>