<?php the_sidebar(); ?>
    <!-- Message Sidebar -->
    <div class="page-aside">
      <div class="page-aside-switch">
        <i class="icon icon-chevron-left"></i>
        <i class="icon icon-chevron-right"></i>
      </div>
      <div class="page-aside-inner">
          <div class="app-message-list">
          <div data-role="container">
            <div data-role="content">
              <ul class="list-group">
		<?php 
		$lists = $db->get_results(" select p1.*, p2.*, count(case when read_at = 0 and (by_user <> '".user_id()."') then 1 else null end) as unread  from ".DB_PREFIX."conversation p1 INNER JOIN ( SELECT * FROM ".DB_PREFIX."con_msgs order by at_time desc ) p2 on p2.conv = p1.c_id  where ((p1.user_one='".user_id()."') OR (p1.user_two='".user_id()."')) GROUP BY p2.conv order by p2.msg_id desc limit 0,500");
        if($lists) {
			foreach($lists as $list) {
				if($list->user_one == user_id()) {$partner = $list->user_two;} else { $partner = $list->user_one;}
				$partner = convBuddy($partner);
				if(!$partner) {
					$partner = new stdClass();
					$partner->avatar = 'storage/uploads/noimage.png';
					$partner->name= _lang('Deleted user');
				}
				echo '
				<li class="list-group-item '.(($list->c_id == token_id()) ? "active" :"").'">
                  <div class="media">
                    <div class="media-left">
                      <a class="avatar" href="'.site_url().'conversation/'.$list->c_id .'">
                        <img class="img-responsive" src="'.thumb_fix($partner->avatar, true, 40, 40).'" alt="..."><i></i></a>
                    </div>
                    <div class="media-body">
                      <h4 class="media-heading">
					  <a href="'.site_url().'conversation/'.$list->c_id .'">
					  '._html($partner->name).'
					  </a>
					  </h4>
                      <span class="media-time">'.time_ago($list->at_time).'</span>
                    </div>
                    <div class="media-right">
                     '.(($list->unread > 0 ) ? "<span class=\"badge badge-danger\">$list->unread</span>" :"").' 
                    </div>
                  </div>
                </li>
				
				';
			}
			
		}
        ?>	  
             </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Message Sidebar -->
    <div class="page-main">
	<?php if($cid > 0) { ?>
      <!-- Chat Box -->
      <div class="app-message-chats">	  
        <div id="conv-<?php echo $_conv->c_id; ?>" class="chats">		<div class="conv-user-header row text-left">		<h1>		<a href="<?php echo $us[$other]['profile'];?>">		<img src="<?php echo $us[$other]['avatar'];?>">		<span><?php echo $us[$other]['name'];?> </span>		</a>				<?php if(is_empty($_conv->closedby)) { ?>			<button class="btn btn-sm btn-icon btn-default pull-right" rel="popover" data-content="<a class='btn btn-sm btn-danger' href='<?php echo canonical();?>?close=1'><?php echo _lang("Yes, close it! I am aware I need to reopen it if I want to continue it.");?></a>" data-html="true" data-toggle="popover" data-placement="bottom" tabindex="0" title="<?php echo _lang("Close conversation");?>" type="button">   <i class="icon icon-lock"></i> <?php echo _lang("End");?> </button>		<?php } ?>		</h1>		</div>		
		<!-- First is fake & hidden -->
		 <div class="chat dummy-chat chat-left hidden">
            <div class="chat-avatar">
              <a target="_blank" class="avatar" href="<?php echo _html($us[user_id()]["profile"]); ?>" title="<?php echo _html($us[user_id()]["name"]); ?>">
                <img src="<?php echo _html($us[user_id()]["avatar"]); ?>" alt="<?php echo _html($us[user_id()]["name"]); ?>">
              </a>
            </div>
            <div class="chat-body">
              <div class="chat-content">
                
              </div>
            </div>
          </div>		 
		<?php 	
              $chats = $db->get_results(" select * from ".DB_PREFIX."con_msgs where conv ='".$_conv->c_id."' order by msg_id ASC limit 0,50");
              if($chats) {
                  foreach ($chats as $chat){ ?>
		 <div id="<?php echo $chat->msg_id; ?>" class="chat <?php if($chat->by_user == user_id()) {echo 'chat-right';} else {echo 'chat-left';} ?>">
            <div class="chat-avatar">
              <a target="_blank" class="avatar" href="<?php echo _html($us[$chat->by_user]["profile"]); ?>" title="<?php echo _html($us[$chat->by_user]["name"]); ?>">
                <img src="<?php echo _html($us[$chat->by_user]["avatar"]); ?>" data-name="<?php echo $us[$chat->by_user]["name"]; ?>" alt="<?php echo $us[$chat->by_user]["name"]; ?>">
              </a>
			  <?php 
                      //if($chat->by_user == user_id()) {
                      if($chat->read_at > 0 ) {
                          echo '<i class="icon chat-seen icon-check tipS tooltip-scale" data-toggle="tooltip" data-placement="';
                          if($chat->by_user == user_id()) {echo 'left';} else {echo 'right';}
                          echo '" title="'._lang('Seen ').''.$chat->read_at.'"></i>';
                      } 
                      //}
              ?>
            </div>
            <div class="chat-body">
              <div class="chat-content">
                <p class="tipS tooltip-scale" data-toggle="tooltip" data-placement="<?php if($chat->by_user == user_id()) {echo 'left';} else {echo 'right';} ?>" title="<?php echo $chat->at_time;?>">
                <?php echo linkify(emojify(_html($chat->reply))); ?>
                </p>
              </div>
            </div>
          </div>
		
		<?php }
              } else {
                  echo '<div class="cute text-left mbot20">
		   <h1>Say hi to <span>'.$us[$other]['name'].'!</span></h1>
		   <div class="cute-line">&nbsp;</div>
		   </div>
		   ';
              }		   
        ?>
          </div>
      </div>
      <!-- End Chat Box -->
	     
      <!-- Message Input-->
      <form class="app-message-input">
	 
        <div class="message-input">	
<?php if(not_empty($_conv->closedby) &&  (intval($_conv->closedby) > 0 )) { ?>		
		<textarea id="insertChat" class="form-control" rows="1" disabled readonly>
		<?php echo _lang("This conversation was closed by").' '.$us[$_conv->closedby]['name'] ;?>
		</textarea>
		 		    </div>
		<?php if($_conv->closedby == user_id()) { ?>
        <a href="<?php echo canonical();?>?open=1" class="message-input-btn btn btn-danger"><?php echo _lang("Open");?></a>
		<?php } else { ?>		
        <button class="message-input-btn btn btn-primary" type="button" disabled><?php echo _lang("SEND");?></button>
        <?php } ?>
		<?php } else { ?>
          <textarea id="insertChat" class="form-control" rows="1"></textarea>
		  
		 <div class="message-input-actions btn-group">
		  <div id="showEmoji"></div>
      			
            </div>
		  
        </div>
        <button id="sendChat" class="message-input-btn btn btn-primary" type="button"><?php echo _lang("SEND");?></button>
     <?php } ?>
	 </form>
      <!-- End Message Input-->
   
	<?php } else { 
              //No conversation
              echo '<div class="row text-center" style="margin-top:11%;">'._lang("Start a conversation by clicking 'Message' on that channel").'</div>';
              echo '<div class="row text-center mtop20"><a class="btn btn-primary" href="'.site_url().members.'">'._lang("Browse channels").'</a></div>';
          } ?>
 </div>