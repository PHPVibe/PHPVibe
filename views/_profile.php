<?php $user = token_id();
//Query this user
if($user > 0) {
	$profile = $db->get_row("SELECT * FROM ".DB_PREFIX."users where id = '".$user ."' limit  0,1");
	if ($profile) {
		// Canonical url
		$canonical = profile_url($profile->id , $profile->name); 
		// Your profile?
        $myself = false;
       if(is_user() && (user_id() == $user)) {
		$myself = true;
	   }		   
		// SEO Filters
		function modify_title( $text ) {
		global $profile;
			return get_option('seo-profile-pre','').strip_tags(stripslashes($profile->name)).get_option('seo-profile-post','');
		}
		function modify_desc( $text ) {
		global $profile;
			return _cut(strip_tags(stripslashes($profile->bio)), 160);
		}
		//Filters

		add_filter( 'phpvibe_title', 'modify_title' );
		add_filter( 'phpvibe_desc', 'modify_desc' );
		//Time for design
		 the_header();
		include_once(TPL.'/profile.php');
		 the_footer(); 
		 //Track this view
			
		$db->query("UPDATE ".DB_PREFIX."users SET views = views+1 WHERE id = '".$profile->id."'");
	} else {
	//Oups, not found
	layout('404');
	}
} else {
	//Oups, not found
	layout('404');
}
?>