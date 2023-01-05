<?php $options = "id,title,media,user_id,thumb,views,liked,duration,nsfw"; 
/* Music */
echo '<h4 class="loop-heading"><span>'._lang("Songs").' ('.number_format($md->nr).')</span></h4>';	
if($md->nr > 0) {
$vq = "select date,".$options.", '".toDb($profile->name)."' as owner FROM ".DB_PREFIX."videos WHERE pub > 0 and date < now() and media > 1 and user_id ='".$profile->id."' ORDER BY id DESC ".this_limit(bpp());
include_once(TPL.'/music-loop.php');
}
?>

