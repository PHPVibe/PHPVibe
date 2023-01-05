<?php 
if($imgs->imgnr > 0) {
echo '<h4 class="loop-heading"><span>'._lang("Pictures").' ('.number_format($imgs->imgnr).')</span></h4>';	
$vq = "select id,title,source,user_id, '".toDb($profile->name)."' as owner, '".toDb($profile->avatar)."' as avatar FROM ".DB_PREFIX."images WHERE pub > 0 and date < now() and user_id ='".$profile->id."' ORDER BY id DESC ".this_limit(bpp());
echo '<div id="imagelist-content">';
include_once(TPL.'/images-loop.php');
echo '</div>';
echo '</div>';
}
?>
<div class="clearfix"></div>
</div>
