<?php  //Global query options
$key = toDb(token());
$heading = _lang('#').ucfirst(str_replace(array("+","-")," ",$key));	
$heading_plus = ('Video search results for ').$key;
$ntype = _get('type');
if(not_empty($ntype)){
	switch ($ntype) {
	case 'picture':
    redirect(site_url().imgsearch.'/'.$key);
    break;	
	case 'channel':
    redirect(site_url().pplsearch.'/'.$key);
    break;	
	case 'playlist':
    redirect(site_url().playlistsearch.'/'.$key);
    break;	
		
	}
	
}
if(not_empty($key)) {
$interval = '';
//Check for sorting 
if(_get('sort'))
{
switch(_get('sort')){
case "w":
$interval = "AND WEEK( DATE ) = WEEK( CURDATE( ) ) ";
break;
case "m":
$interval = "AND MONTH(date) = MONTH(CURDATE( ))";
break;
case "y":
$interval = "AND YEAR( DATE ) = YEAR( CURDATE( ) ) ";
break;
}
}
//Remove url format
$key = str_replace(array("-","+")," ",$key);
$nkey = str_replace(" ", "_",$key);
$mkey = str_replace(" ", "-",$key);
$options = DB_PREFIX."videos.id,".DB_PREFIX."description.description,".DB_PREFIX."videos.title, ".DB_PREFIX."videos.date,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.duration,".DB_PREFIX."videos.nsfw";
 /* If 3 letter word */
 if((strlen($key) < 4) || (get_option("searchmode",1) == 1)) {
 $vq = "select ".$options.", ".DB_PREFIX."users.name as owner,".DB_PREFIX."users.group_id FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id 
	LEFT JOIN ".DB_PREFIX."description ON ".DB_PREFIX."videos.id = ".DB_PREFIX."description.vid

	WHERE ".DB_PREFIX."videos.pub > 0 and ".DB_PREFIX."videos.date < now() and ( ".DB_PREFIX."videos.title like '%".$key."%' or ".DB_PREFIX."videos.title like '%".$nkey."%' or ".DB_PREFIX."videos.title like '%".$mkey."%' or ".DB_PREFIX."description.description like '%".$key."%' ) ".$interval."
	   ORDER BY CASE WHEN ".DB_PREFIX."videos.title like '" .$key. "%' THEN 0
	           WHEN ".DB_PREFIX."videos.title like '%" .$key. "%' THEN 1	    	   
               WHEN ".DB_PREFIX."description.description like '%" .$key. "%' THEN 2
               ELSE 3
          END, title ".this_limit();
		 
 } else {
 /* Use full search */	 
$vq = "select ".$options.", ".DB_PREFIX."users.name as owner, ".DB_PREFIX."users.group_id,
MATCH (title,description) AGAINST ('".$key."' IN BOOLEAN MODE) AS relevance,
MATCH (title) AGAINST ('".$key."' IN BOOLEAN MODE) AS title_relevance FROM ".DB_PREFIX."videos 
LEFT JOIN ".DB_PREFIX."description ON ".DB_PREFIX."videos.id = ".DB_PREFIX."description.vid
LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id 
	WHERE MATCH (title,description) AGAINST('".$key."' IN BOOLEAN MODE) AND ".DB_PREFIX."videos.pub > 0 and ".DB_PREFIX."videos.date < now() $interval ORDER by title_relevance DESC,relevance DESC ".this_limit();
 }	
// Canonical url
if(_get('sort')) {
$canonical = site_url().show.url_split.str_replace(array(" "),array("-"),$key)."&sort="._get('sort'); 
} else {
$canonical = site_url().show.url_split.str_replace(array(" "),array("-"),$key);	
}

} else {
$vq = '';
}	
// SEO Filters
function modify_title( $text ) {
global $heading;
    return strip_tags(stripslashes($heading));
}
function modify_desc( $text ) {
global $heading_plus;
    return _cut(strip_tags(stripslashes($heading_plus)), 160);
}
add_filter( 'phpvibe_title', 'modify_title' );
add_filter( 'phpvibe_desc', 'modify_desc' );
//Time for design
if (!is_ajax_call()) {  the_header(); the_sidebar(); }
include_once(TPL.'/searchresults.php');
if (!is_ajax_call()) { the_footer(); }
?>