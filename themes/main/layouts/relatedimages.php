<?php global $image, $canonical, $blockclass;
$seenImg = array();
if (isset($_SESSION['vseenimg']) && not_empty($_SESSION['vseenimg'])) {
    $seenImg = explode(',', $_SESSION['vseenimg']);
}
$options = DB_PREFIX . "images.id," . DB_PREFIX . "images.title," . DB_PREFIX . "images.user_id," . DB_PREFIX . "users.name as owner," . DB_PREFIX . "users.avatar," . DB_PREFIX . "images.source," . DB_PREFIX . "images.views";
$noseen = 'and ' . DB_PREFIX . 'images.id <> ' . $image->id;
$vq = "select " . $options . "  FROM " . DB_PREFIX . "images LEFT JOIN " . DB_PREFIX . "users ON " . DB_PREFIX . "images.user_id = " . DB_PREFIX . "users.id 
WHERE (" . DB_PREFIX . "images.category = '" . $image->category . "' or " . DB_PREFIX . "images.user_id = '" . $image->user_id . "') " . $noseen . " and " . DB_PREFIX . "images.pub > 0 and " . DB_PREFIX . "images.date < now() ORDER by views,liked DESC limit 0," . get_option('related-nr') . " ";
echo '<div class="row">';
$rimages = $cachedb->get_results($vq);
echo '<h2 class="loop-heading"><span>' . _lang('More images') . '</span></h2>';

if ($rimages) {
    echo '<div class="row text-center"><div class="col-md-12 col-xs-12 gfluid">';
    foreach ($rimages as $rel) {
        if (isset($rel->id) && not_empty($rel->id)) {
            $rel->thumb = $source = str_replace('localimage', '', $rel->source);
            $rel->thumb = ltrim( $rel->thumb, '/');
            $rel->thumb = site_url().'storage/'.get_option('mediafolder').'/thumb_'. $rel->thumb;

            if (isset($rel->nsfw) && ($rel->nsfw > 0)) {
                $rel->thumb = tpl() . 'images/nsfw.jpg';
            }
            $title = _html(_cut($rel->title, 370));
            $full_title = _html(str_replace("\"", "", $rel->title));
            $url = image_url($rel->id, $rel->title);
            if (in_array($rel->id, $seenImg)) {
                $icls = "seenI";
            } else {
                $icls = 'freshI';
            }
            echo '
		<div class="image-item item ' . $icls . '">
        <div class="image-content">
		<a class="clip-link" data-id="' . $rel->id . '" title="' . $full_title . '" href="' . $url . '">
		<img data-name="' . $rel->title . '" src="' . $rel->thumb . '"/>
        </a>		
        </div>
	    <div class="image-footer text-left">
		<a href="' . profile_url($rel->user_id, $rel->owner) . '" class="text-left owner-avatar"><img class="owner-avatar" data-name="' . $rel->owner . '" src="' . thumb_fix($rel->avatar, true, 56, 56) . '"/>
		<span class="owner-name">@' . _html($rel->owner) . '</span>
		</a>
		</div>
    </div>
';
        }
    }
    echo _ad('0', 'after-video-loop');
    echo '</div></div>';
}
echo '</div>';
?>