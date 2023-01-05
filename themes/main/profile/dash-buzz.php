	<div class="row odet">
	<div class="panel panel-transparent">
	<div class="panel-heading">
	<h4 class="panel-title"><?php echo _lang("Activity on your media");?></h4>
	</div>
	<div class="panel-body">
	<?php
	//Latest notifications
	$count= $db->get_row("Select count(*) as nr from vibe_activity where (type not in (8,9) and vibe_activity.object in (select id from vibe_videos where user_id ='".user_id()."' ) ) or (type in (8,9) and vibe_activity.object in (select id from vibe_images where user_id ='".user_id()."' ) ) and user <> '".user_id()."'");

		if($count){
		if($count->nr > 0) {
		echo '<p style="line-height:15px;"><span class="badge" style="font-size:16px">'.$count->nr.' <i class="icon material-icons" style="font-size:16px">&#xE7F7;</i></span></p>';
		$vq = "Select ".DB_PREFIX."activity.*, ".DB_PREFIX."users.avatar,".DB_PREFIX."users.id as pid, ".DB_PREFIX."users.name from ".DB_PREFIX."activity left join ".DB_PREFIX."users on ".DB_PREFIX."activity.user=".DB_PREFIX."users.id where
		((".DB_PREFIX."activity.type not in (8,9) and ".DB_PREFIX."activity.object in (select id from ".DB_PREFIX."videos where user_id ='".user_id()."' ))  or
		(".DB_PREFIX."activity.type in (8,9) and ".DB_PREFIX."activity.object in (select id from ".DB_PREFIX."images where user_id ='".user_id()."' ))) and ".DB_PREFIX."activity.user <> '".user_id()."'
		ORDER BY ".DB_PREFIX."activity.id DESC ".this_limit();
		$activity = $db->get_results($vq);
		if ($activity) {
		$did =  array();
		
		/* print_r($activity); */
		echo '<div class="row">
		<ul id="usertimeline" class="timelist user-timeline">
		'; 
		$licon = array();
		$licon["1"] = "icon-heart";
		$licon["2"] = "icon-share";
		$licon["3"] = "icon-youtube-play";
		$licon["4"] = "icon-upload";
		$licon["5"] = "icon-rss";
		$licon["6"] = "icon-comments";
		$licon["7"] = "icon-thumbs-up";
		$licon["8"] = "icon-camera";
		$licon["9"] = "icon-star";
		$lback = array();
		$lback["1"] = $lback["9"] = "bg-smooth";
		$lback["2"] = "bg-success";
		$lback["3"] = "bg-flat";
		$lback["4"] = $lback["8"] = "bg-default";
		$lback["5"] = "bg-default";
		$lback["6"] = "bg-info";
		$lback["7"] = "bg-smooth";
		$agr = array();
		
		foreach ($activity as $buzz) {
		$aid = 	$buzz->type.$buzz->object;
		$cid = $buzz->pid;
		if(!isset($agr[$aid]['did'])) {
		$agr[$aid]['did'] = get_activity($buzz);
		$agr[$aid]['date'] = $buzz->date;
		$agr[$aid]['type'] = $buzz->type;
		}
		if(!isset($agr[$aid]['user'][$cid])) {
		
		$agr[$aid]['user'][$cid]['id'] = $cid;
		$agr[$aid]['user'][$cid]['name'] = $buzz->name;
		}
		
				}
		
		foreach ($agr as $do) {
			
		$who ='';
			foreach ($do['user'] as $u) {
			
				
				$who .= '<a href="'.profile_url($u['id'],$u['name']).'">'._html($u['name']).'</a>, ';
				
			}
		$who = substr($who, 0, -2);;
		
		echo '
		<li class="smting cul-'.$do["type"].' t-item">
		 <div class="user-timeline-time">'.time_ago($do["date"]).'</div>
		<i class="icon '.$licon[$do["type"]].' user-timeline-icon '.$lback[$do["type"]].'"></i>
		<div class="user-timeline-content">
		<p>'.$who.'  '.$do["did"]["what"].'</p>
		';
		if(not_empty($do["did"]["content"])) {
		echo '<div class="timeline-media">'.$do["did"]["content"].'</div>';
		}
		echo '</div>
		</li>';
			
		}
		echo '</ul><br style="clear:both;"/></div>';
		}
        //$a->show_pages($ps);	
echo '<nav id="page_nav"><a href="'.site_url().'dashboard/?sk=activity&p='.(this_page() + 1).'"></a></nav>';
echo '

<div class="page-load-status">

  <div class="infinite-scroll-request" style="display:none">

    <div class="cp-spinner cp-flip"></div>  

    <p>'._lang('Loading...').'</p>

  </div>

  <p class="infinite-scroll-error infinite-scroll-last" style="display:none">

    '._lang('You have reached the end!').'

  </p>

</div>

';
		
		} else {
		echo '<p>'._lang("No activity on your media yet").'</p>';	
		}
		} else {
		echo '<p>'._lang("No activity yet").'</p>';	
		}
?>	
</div>
</div>
</div>
