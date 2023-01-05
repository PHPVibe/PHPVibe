<?php //echo $vq;
$users = $db->get_results($vq);
if ($users) {
echo '<div class="row"><ul class="list-group users-list">'; 
foreach ($users as $user) {
			$title = stripslashes(_cut($user->name, 46));
			$full_title = stripslashes(str_replace("\"", "",$user->name));			
			$url = profile_url($user->id , $user->name);
echo '
<li id="user-'.$user->id.'" class="list-group-item">
<div class="media">
                      <div class="media-left">
                        <div class="avatar">
                          <img src="'.thumb_fix($user->avatar, true, 40, 40).'" alt="...">
                        </div>
                      </div>
                      <div class="media-body">
                        <h4 class="media-heading">
                          <a href="'.$url.'">'.$full_title.'</a>                         
                        </h4>
                        <p>
                       <small> '._lang("Last online:").' '.time_ago($user->lastnoty).'</small>
						</p>
                      </div>
                      <div class="media-right">';
			subscribe_box($user->id,"btn btn-xs", false);
                     echo ' </div>
                    </div>
	</li>
';
}
echo '</ul></div>';
}
?>