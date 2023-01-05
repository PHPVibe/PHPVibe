<?php $limit =  $box->total;
if(($box->ident == "most_viewed") || is_empty($box->ident)) {
$topusers = $db->get_results("select name,group_id,avatar,id from ".DB_PREFIX."users order by views DESC ".this_offset($limit)."");
}if($box->ident == "viral") {
$topusers = $db->get_results("select name,group_id,avatar,id from ".DB_PREFIX."users order by id DESC ".this_offset($limit)."");
} else {
$topusers = $db->get_results("select o.name,o.group_id,o.id, o.avatar, c.user, count(c.user) as actives from ".DB_PREFIX."activity c left join ".DB_PREFIX."users o on c.user = o.id group by c.user order by actives desc ".this_offset($limit)."");
}
echo '<div class="row">
	<h1 class="loop-heading">'._html($box->title).'</h1>';
if($topusers) {
	echo '<div class="fake-padding black-slider"> <ul id="carousel" class="owl-carousel">';
foreach ($topusers as $user) {
			if(isset($user->group_id)) { $grcreative= group_creative($user->group_id); } else { $grcreative=''; };
            $title = _html(_cut($user->name, 20));
			$full_title = _html(str_replace("\"", "",$user->name));
			$url = profile_url($user->id , $user->name);
echo '
<div class="subitem">
		<a class="block text-center" data-id="'.$user->id.'" title="'.$full_title.'" href="'.$url.'">
					<img class="img-circle cartistic" src="'.thumb_fix($user->avatar, true, 100, 100).'" data-name="'.$full_title.'" style="width:100px; height:100px" />
		<span class="btn btn-block">'._html($title).' '.$grcreative.'</span>
		</a>
		<div class="block">';
		subscribe_box($user->id);
		echo '</div>
</div>';
}
echo '</ul></div>';
	}
	echo '</div>';
?>