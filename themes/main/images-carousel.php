<?php do_action('videoloop-start');
if(!nullval($vq)) { $images = $db->get_results($vq); } else {$images = false;}
if(!isset($st)){ $st = ''; }
if(!isset($blockclass)){ $blockclass = ''; }
if(!isset($blockextra)){ $blockextra = ''; }
if(isset($heading) && !empty($heading)) { echo '<h1 class="loop-heading"><span>'._html($heading).'</span>'.$st.'</h1>';}
if(isset($heading_meta) && !empty($heading_meta)) { echo $heading_meta;}
if(isset($heading_plus) && !empty($heading_plus)) { echo '<small class="videod">'.$heading_plus.'</small>';}
if ($images) {

echo $blockextra.'<div class="row text-center"><div class="loop-content owl-carousel '.$blockclass.'">';
foreach ($images as $image) {
	if(isset($image->nsfw) && ($image->nsfw > 0) ) { $image->thumb = tpl().'images/nsfw.jpg';}
			$title = _html(_cut($image->title, 370));
			$full_title = _html(str_replace("\"", "",$image->title));
			$url = image_url($image->id , $image->title);
			echo '
		<div class="image-item item">
        <div class="image-content">
		<a class="clip-link" data-id="'.$image->id.'" title="'.$full_title.'" href="'.$url.'">
		<img data-name="'.$image->title.'" src="'.thumb_fix($image->thumb, true, 400, 'auto').'"/>
        </a>
        </div>
	    <div class="image-footer text-left">
		<a href="'.profile_url($image->user_id, $image->owner).'" class="text-left owner-avatar"><img class="owner-avatar" data-name="'.$image->owner.'" src="'.thumb_fix($image->avatar, true, 56, 56).'"/>
		<span class="owner-name">@'._html($image->owner).'</span>
		</a>
		</div>
    </div>
';
}
echo _ad('0','after-video-loop');
/* Kill for home if several blocks */
if(!isset($kill_infinite) || !$kill_infinite) { echo '<nav id="page_nav"><a href="'.$canonical.'?ajax&p='.next_page().'"></a></nav>'; }
echo '</div></div>';
} else {
echo '<p class="empty-content">'._lang('Nothing here so far.').'</p>';
}
do_action('videoloop-end');
?>