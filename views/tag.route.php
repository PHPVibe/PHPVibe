<?php  //Global query options
$key = toDb(token());
$heading = _lang('#').ucfirst(str_replace(array("+","-")," ",$key));	
$heading_plus = ('Tagged ').$key;
$ntype = _get('type');
if(not_empty($key)) {
$interval = '';
//Check for sorting 
if(_get('sort'))
{
$interval = 'order by id desc ';	
switch(_get('sort')){
case "mv":
$interval = "order by views desc ";
break;
case "ml":
$interval = "order by liked desc ";
break;
}
}
//Remove url format
$key = str_replace(array("-","+")," ",$key);
$key = preg_replace('/[^\w]+/', '%', $key);
$key = str_replace("amp","%",$key);
$mkey = toDb($key);
$precontent = '';
$hastags = true; //asumme
$tagged= 0;
$tag_id = $db->get_var("Select id from ".DB_PREFIX."tag_names where tag_name like '".$mkey."'");
	if($tag_id) {
	$tagged = $cachedb->get_var("Select count(*) from ".DB_PREFIX."tag_rel where tag_id = '".$tag_id."'");
	$heading .= ' ('.$tagged.')';
			if($tagged > 0 ) {
				$options = DB_PREFIX."videos.id,".DB_PREFIX."videos.title, ".DB_PREFIX."videos.date,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.duration,".DB_PREFIX."videos.nsfw";
				$vq = "select ".$options." FROM ".DB_PREFIX."videos where id in (Select distinct media_id from ".DB_PREFIX."tag_rel where tag_id = '".$tag_id."') AND ".DB_PREFIX."videos.pub > 0 and ".DB_PREFIX."videos.date < now() $interval ".this_limit(); 
				//echo $vq;
			}
	} 
//Empty?
if(!$tag_id || ($tagged < 1) ) {
$hastags = false;	
}
//No such tag_id
if(!$hastags) {
$tags = $db->get_var("Select GROUP_CONCAT(`tag_name` SEPARATOR ',') from ".DB_PREFIX."tag_names where tag_name like '%".$mkey."%' or tag_name like '".$mkey."%' limit 0,20");
	if($tags) {
		$precontent = '<p class="text-left">'._lang('Try instead:').'</p>';
		$precontent .= '<div class="text-left top20 left10 right10 block full bot20">
		<h3 class="loop-heading">
		'.pretty_tags($tags,'block text-left','#','<br>').'
		</h3></div>';	
	}	
}
// Canonical url
if(_get('sort')) {
$canonical = site_url().thetags.url_split.str_replace(array(" "),array("-"),$key)."&sort="._get('sort'); 
} else {
$canonical = site_url().thetags.url_split.str_replace(array(" "),array("-"),$key);	
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
include_once(TPL.'/tag.php');
if (!is_ajax_call()) { the_footer(); }
?>