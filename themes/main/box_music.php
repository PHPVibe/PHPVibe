<?php $options = DB_PREFIX."videos.id,".DB_PREFIX."videos.description,".DB_PREFIX."videos.title,".DB_PREFIX."videos.date,".DB_PREFIX."videos.media,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.duration, ".DB_PREFIX."users.avatar, ".DB_PREFIX."users.name as owner ";
$query = $box->querystring;
$c_add="AND date < now() ";
$limit =  $box->total;
$heading = $box->title;
if(!empty($box->ident)){ $c_add .="AND category in (select cat_id from ".DB_PREFIX."channels where cat_id='".intval($box->ident)."' or child_of = '".intval($box->ident)."') "; }
$c_add .="AND media > 1 ";
if($query == "most_viewed"):
$vq = "select ".$options." FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.views > 0 and pub > 0 $c_add ORDER BY ".DB_PREFIX."videos.views DESC ".this_offset($limit);
elseif($query == "top_rated"):
$vq = "select ".$options." FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.liked > 0 and pub > 0 $c_add ORDER BY ".DB_PREFIX."videos.liked DESC ".this_offset($limit);
elseif($query == "random"):
$vq = "select ".$options." FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.views >= 0 and pub > 0 $c_add ORDER BY rand() ".this_offset($limit);
elseif($query == "featured"):
$vq = "select ".$options." FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.featured = '1' and pub > 0 $c_add ORDER BY ".DB_PREFIX."videos.id DESC ".this_offset($limit);
else:
$vq = "select ".$options." FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id WHERE ".DB_PREFIX."videos.views >= 0 and pub > 0 $c_add ORDER BY ".DB_PREFIX."videos.id DESC ".this_offset($limit);
endif;
$kill_infinite = true;
include(TPL.'/music-loop.php');
unset($c_add);
?>