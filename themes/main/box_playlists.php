<?php $limit =  $box->total;
if(($box->ident == "most_viewed") || is_empty($box->ident)) {
$playlists = $db->get_results("select ".DB_PREFIX."playlists.*, ".DB_PREFIX."users.name as user from ".DB_PREFIX."playlists LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."playlists.owner = ".DB_PREFIX."users.id WHERE (".DB_PREFIX."playlists.picture not in ('[likes]','[history]','[later]') or ".DB_PREFIX."playlists.picture is null) and (".DB_PREFIX."playlists.ptype <> 2) order by ".DB_PREFIX."playlists.views DESC ".this_offset($limit)."");
$is_media = true;
}elseif($box->ident == "viral") {
$playlists = $db->get_results("select ".DB_PREFIX."playlists.*, ".DB_PREFIX."users.name as user from ".DB_PREFIX."playlists LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."playlists.owner = ".DB_PREFIX."users.id WHERE (".DB_PREFIX."playlists.picture not in ('[likes]','[history]','[later]') or ".DB_PREFIX."playlists.picture is null) and (".DB_PREFIX."playlists.ptype <> 2) order by ".DB_PREFIX."playlists.id DESC ".this_offset($limit)."");
$is_media = true;
} else {
if($box->ident == "alb_mv") {
$playlists = $db->get_results("select ".DB_PREFIX."playlists.*, ".DB_PREFIX."users.name as user from ".DB_PREFIX."playlists LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."playlists.owner = ".DB_PREFIX."users.id WHERE (".DB_PREFIX."playlists.picture not in ('[likes]','[history]','[later]') or ".DB_PREFIX."playlists.picture is null) and (".DB_PREFIX."playlists.ptype = 2) order by views DESC ".this_offset($limit)."");
$is_media = false;
} else {
$playlists = $db->get_results("select ".DB_PREFIX."playlists.*, ".DB_PREFIX."users.name as user from ".DB_PREFIX."playlists LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."playlists.owner = ".DB_PREFIX."users.id WHERE (".DB_PREFIX."playlists.picture not in ('[likes]','[history]','[later]') or ".DB_PREFIX."playlists.picture is null) and (".DB_PREFIX."playlists.ptype = 2) order by ".DB_PREFIX."playlists.id DESC ".this_offset($limit)."");
$is_media = false;
}

}
echo '<div class="row">
	<h1 class="loop-heading">'._html($box->title).'</h1>';
if($playlists) {
	echo '<div class="fake-padding black-slider playlists-owl"> <ul id="carousel" class="owl-carousel">';
foreach ($playlists as $pl) {
			$title = _html(_cut($pl->title, 170));
			$full_title = _html(str_replace("\"", "",$pl->title));
			$url = playlist_url($pl->id , $pl->title);
			$plays = '';
            $ov = '';
			if(not_empty( $pl->picture)){
            $image = '<img src="'.thumb_fix($pl->picture, true, get_option('thumb-width'), get_option('thumb-height')).'" alt="'.$full_title.'" />';
			} else {
			$image = '<img class="NoAvatar" src="" data-name="'.addslashes(trim($full_title)).'" />';
			}
			if($pl->owner == user_id()) {
			$ol = $url;
			} else {
			$ol = site_url().'forward/'.$pl->id;
			}
			if($pl->ptype == 1) {
			$ov = _lang("Play all");
			$ico = '<i class="material-icons">&#xE04A;</i>';

			} elseif($pl->ptype == 2) {
				$ov = _lang("View all");
				$ico = '<i class="material-icons">&#xE43C;</i>';
				} else {
				$ov = _lang("Play all");
				$ico = '<i class="material-icons">&#xE030;</i>';
				}
			$ove = '<div class="playlists-overlay">
	     	<a title="'._lang($ov).'" href="'.$ol.'">
			'.$ov.'
			</a>
			</div>
			';

echo '
<div id="video-'.$pl->id.'" class="video">
<div class="video-inner">
<div class="video-thumb">
		<a class="clip-link" data-id="'.$pl->id.'" title="'.$full_title.'" href="'.$ol.'">
			<span class="clip">
				'.$image.'
				'.$ove.'
				<div class="playlists-meta">
				<span>'.$ico.'</span>
				<span>'.$plays.'</span>
				</div>
			</span>

		</a>';
echo '</div>
	<h4 class="video-title"><a href="'.$ol.'" title="'.$full_title.'">'._html($title).'</a></h4>
	</div>
		</div>
';
}
echo '</ul></div>';


	}
	echo '</div>';
?>