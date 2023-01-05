<?php $options = "id,title,user_id,thumb,views,duration"; 
$vq = "select ".$options.", '".toDb($profile->name)."' as owner FROM ".DB_PREFIX."videos WHERE pub > 0 and date < now() and media < 2 and user_id ='".$profile->id."' ORDER BY date DESC ".this_limit(bpp());
?>
<div id="videolist-content" class="usered">
<?php 
echo _ad('0','user-video-list-top');
include_once(TPL.'/video-loop.php');
 echo _ad('0','user-video-list-bottom');
?>
</div>
