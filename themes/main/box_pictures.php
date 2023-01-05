<?php $options = DB_PREFIX."images.id,".DB_PREFIX."images.title,".DB_PREFIX."images.date,".DB_PREFIX."images.user_id,".DB_PREFIX."images.thumb, ".DB_PREFIX."users.avatar , ".DB_PREFIX."users.name as owner ";
$query = $box->querystring;
$c_add="AND date < now() ";
$limit =  $box->total;
$heading = $box->title;
if(!empty($box->ident)){ $c_add .="AND category in (select cat_id from ".DB_PREFIX."channels where cat_id='".intval($box->ident)."' or child_of = '".intval($box->ident)."') "; }
if($query == "most_viewed"):
$vq = "select ".$options." FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.views > 0 and pub > 0 $c_add ORDER BY ".DB_PREFIX."images.views DESC ".this_offset($limit);
elseif($query == "top_rated"):
$vq = "select ".$options." FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.liked > 0 and pub > 0 $c_add ORDER BY ".DB_PREFIX."images.liked DESC ".this_offset($limit);
elseif($query == "random"):
$vq = "select ".$options." FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.views >= 0 and pub > 0 $c_add ORDER BY rand() ".this_offset($limit);
elseif($query == "featured"):
$vq = "select ".$options." FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.featured = '1' and pub > 0 $c_add ORDER BY ".DB_PREFIX."images.id DESC ".this_offset($limit);
else:
$vq = "select ".$options." FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."images.views >= 0 and pub > 0 $c_add ORDER BY ".DB_PREFIX."images.id DESC ".this_offset($limit);
endif;
$kill_infinite = true;
$blockclass = ''; /*Kill hide, behaves bad */
echo '<div id="imagelist-content">';
if(isset($box->car) && ($box->car > 0)){
include(TPL.'/images-carousel.php');
} else {
include(TPL.'/images-loop.php');
}
echo '</div>';
$blockclass = 'hide'; /*Resume hide, for next block */
unset($c_add);
?>