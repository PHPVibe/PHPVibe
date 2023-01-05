<?php 
$options = DB_PREFIX."videos.id as vid,".DB_PREFIX."videos.title,".DB_PREFIX."videos.views,".DB_PREFIX."videos.liked,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.duration";
$uresult =$db->get_results("select ".$options." FROM ".DB_PREFIX."videos WHERE ".DB_PREFIX."videos.user_id='".$video->user_id."' and ".DB_PREFIX."videos.pub > 0 and ".DB_PREFIX."videos.date < now() ORDER BY ".DB_PREFIX."videos.id DESC ".this_offset(get_option('related-nr',12)));
 if ($uresult) {
	foreach ($uresult as $uvids) {
$duration = ($uvids->duration > 0) ? video_time($uvids->duration) : '<i class="icon-picture"></i>';		
echo '
					<li data-id="'.$uvids->vid.'" class="item-post">
				<div class="inner">
					
	<div class="thumb">
		<a class="clip-link" data-id="'.$uvids->vid.'" title="'._html($uvids->title).'" href="'.video_url($uvids->vid , $uvids->title).'">
			<span class="clip">
				<img src="'.thumb_fix($uvids->thumb, true, 100, 56).'" alt="'._html($uvids->title).'" /><span class="vertical-align"></span>
			</span>
		<span class="timer">'.$duration.'</span>					
			<span class="overlay"></span>
		</a>
	</div>			
					<div class="data">
						<span class="title"><a href="'.video_url($uvids->vid , $uvids->title).'" rel="bookmark" title="'._html($uvids->title).'">'._cut(_html($uvids->title),124 ).'</a></span>
			
						<span class="usermeta">
							'._lang('by').' <a href="'.profile_url($video->user_id, $video->owner).'"> '._html($video->owner).' </a>
							
						</span>
					</div>
				</div>
				</li>
		
	';
	}
}

?>