<?php  //Global query options
$key = toDb(token());
$heading = _lang('#').ucfirst(str_replace(array("+","-")," ",$key));	
$heading_plus = ('Image results for ').$key;
$noNavs = true;
$sortop = false;
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
$options = DB_PREFIX."images.id,".DB_PREFIX."images.nsfw,".DB_PREFIX."images.title,".DB_PREFIX."images.date,".DB_PREFIX."images.user_id,".DB_PREFIX."images.source";

       /* if(strlen($key) < 4) { */
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner, ".DB_PREFIX."users.avatar FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id 
	    WHERE ".DB_PREFIX."images.pub > 0 and ".DB_PREFIX."images.date < now() and ( ".DB_PREFIX."images.title like '%".$key."%' or ".DB_PREFIX."images.description like '%".$key."%' or ".DB_PREFIX."images.tags like '%".$key."%' ) ".$interval."
	    ORDER BY CASE WHEN ".DB_PREFIX."images.title like '" .$key. "%' THEN 0
	           WHEN ".DB_PREFIX."images.title like '%" .$key. "%' THEN 1
	           WHEN ".DB_PREFIX."images.tags like '" .$key. "%' THEN 2
               WHEN ".DB_PREFIX."images.tags like '%" .$key. "%' THEN 3		   
               WHEN ".DB_PREFIX."images.description like '%" .$key. "%' THEN 4
			   WHEN ".DB_PREFIX."images.tags like '%" .$key. "%' THEN 5
               ELSE 6
          END, title ".this_limit();
 /* } else {		
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner, ".DB_PREFIX."users.avatar ,
        MATCH (title,description,tags) AGAINST ('".$key."' IN BOOLEAN MODE) AS relevance,
        MATCH (title) AGAINST ('".$key."' IN BOOLEAN MODE) AS title_relevance FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id 
	    WHERE MATCH (title,description,tags) AGAINST('".$key."' IN BOOLEAN MODE) AND ".DB_PREFIX."images.pub > 0 and ".DB_PREFIX."images.date < now() $interval ORDER by title_relevance DESC,relevance DESC ".this_limit();
        }
		*/
 
// Canonical url
if(_get('sort')) {
$canonical = site_url().imgsearch.url_split.str_replace(array(" "),array("-"),$key)."&sort="._get('sort'); 
} else {
$canonical = site_url().imgsearch.url_split.str_replace(array(" "),array("-"),$key);	
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
include_once(TPL.'/imageslist.php');
if (!is_ajax_call()) { the_footer(); }
?>