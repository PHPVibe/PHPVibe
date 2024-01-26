<?php  //Global query options
$options = DB_PREFIX."images.id,".DB_PREFIX."images.source,".DB_PREFIX."images.nsfw,".DB_PREFIX."images.title,".DB_PREFIX."images.date,".DB_PREFIX."images.user_id,".DB_PREFIX."images.source";
/* Define list to load */
$interval = '';
if(_get('sort'))
{
switch(_get('sort')){
case "w":
$interval = "AND WEEK( DATE ) = WEEK( CURDATE( ) ) AND YEAR( DATE ) = YEAR( CURDATE( ) )";
break;
case "m":
$interval = "AND MONTH(date) = MONTH(CURDATE( )) AND YEAR( DATE ) = YEAR( CURDATE( ) )";
break;
case "y":
$interval = "AND YEAR( DATE ) = YEAR( CURDATE( ) ) ";
break;
}
}
switch(token()){

case mostliked:
		$heading = ('Most liked images');	
        $heading_plus = _lang('Most liked images selection');
		$sortop = true;
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner, ".DB_PREFIX."users.avatar FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.liked > 0 and ".DB_PREFIX."images.date < now() and pub > 0 ".$interval." ORDER BY ".DB_PREFIX."images.liked DESC ".this_limit();
		$active = mostliked;
		break;
case mostcom:
		$heading = ('Most commented images');	
        $heading_plus = _lang('Images with most comments');
	    $vq = "select ".DB_PREFIX."images.id, ".DB_PREFIX."images.source, ".DB_PREFIX."images.title,".DB_PREFIX."images.user_id,".DB_PREFIX."images.source,".DB_PREFIX."images.views,".DB_PREFIX."images.liked,".DB_PREFIX."images.nsfw, ".DB_PREFIX."users.name as owner, ".DB_PREFIX."users.avatar , count(a.object_id) as cnt FROM ".DB_PREFIX."em_comments a LEFT JOIN ".DB_PREFIX."images ON a.object_id LIKE CONCAT('img-', ".DB_PREFIX."images.id) LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.liked > 0 and ".DB_PREFIX."images.date < now() and pub > 0 group by a.object_id order by cnt desc ".this_limit();
		
		$active = mostcom;
		break;		
case mostviewed:
		$heading = ('Most viewed images');	
        $heading_plus = _lang('Most viewed images');
        $sortop = true;		
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner, ".DB_PREFIX."users.avatar FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.views > 0 and ".DB_PREFIX."images.date < now() and pub > 0 ".$interval." ORDER BY ".DB_PREFIX."images.views DESC ".this_limit();
		$active = mostviewed;
		break;
case promoted:
		$heading = _lang('Featured images');
        $heading_plus = _lang('Featured inages');
        $sortop = true;		
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner, ".DB_PREFIX."users.avatar FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.featured = '1' and ".DB_PREFIX."images.date < now() and pub > 0 ".$interval." ORDER BY ".DB_PREFIX."images.id DESC ".this_limit();
        $active = promoted;
		break;
case 'tag':
        $key = toDb(_get('tag'));
		$heading = _lang('#').ucfirst(str_replace(array("+","-")," ",$key));	
        $heading_plus = ('Images tagged with ').$key;
        $sortop = true;
        if(strlen($key) < 4) {
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
 } else {		
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner, ".DB_PREFIX."users.avatar ,
        MATCH (title,description,tags) AGAINST ('".$key."' IN BOOLEAN MODE) AS relevance,
        MATCH (title) AGAINST ('".$key."' IN BOOLEAN MODE) AS title_relevance FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id 
	    WHERE MATCH (title,description,tags) AGAINST('".$key."' IN BOOLEAN MODE) AND ".DB_PREFIX."images.pub > 0 and ".DB_PREFIX."images.date < now() $interval ORDER by title_relevance DESC,relevance DESC ".this_limit();
        }
		$active = 'tag';
		break;		
default:
		$heading = _lang('New images');	
        $heading_plus = _lang('New images shared');        
		$vq = "select ".$options.", ".DB_PREFIX."users.name as owner, ".DB_PREFIX."users.avatar FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.pub > 0 and ".DB_PREFIX."images.date < now() and pub > 0 ORDER BY ".DB_PREFIX."images.id DESC ".this_limit();
        $active = browse;
		break;		
}


// Canonical url
if(_get('sort')) {
$canonical = images_url(token())."&sort="._get('sort'); 
} else {
$canonical = images_url(token()).(_get('tag')? '&tag='._get('tag') : ''); 
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