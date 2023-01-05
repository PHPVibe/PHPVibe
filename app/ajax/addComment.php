<?php  error_reporting(E_ALL);
include_once('../../load.php');
if (is_user( ) && isset($_REQUEST['comment'])) {
   
    $info_user = user_id( );
	
    if($_REQUEST['comment'] == _lang('enterComment')){
        unset($_REQUEST['comment']);
    }

    if($_REQUEST['comment']) {
		
   $thecom =  html_entity_decode(urldecode($_REQUEST['comment']));
   
	$com_body = toDb(MakeEmoji($thecom));
	$obj_id = toDb($_REQUEST['object_id']);
	$reply_id = (isset($_REQUEST['reply'])) ? intval($_REQUEST['reply']) : 0;
    $it = "INSERT INTO ".DB_PREFIX."em_comments(`object_id`, `created`, `sender_id`, `comment_text`, `access_key`, `reply`) VALUES ('".$obj_id."', '".date("F j, Y, g:i a",time())."', '".$info_user."', '".$com_body."', '".md5(uniqid())."', '".$reply_id."')";
	
     	$addit = $db->query($it);
		$commentID  = $db->insert_id;
         add_activity('6', toDb(str_replace('video_','',$obj_id)));
      
        // /finished insert
        
        
        
         //send reply to browser
        //header('Content-type: application/x-json');
		
		$com_body = emojify($com_body);
        echo json_encode(array(
                                'id'    => $commentID,
                                'text'  => html_entity_decode(urldecode($_REQUEST['comment'])),
                                'name'  => stripslashes(user_name()), 
                                'url'  => my_profile(),								
                                'image' => user_avatar(),
                                'date'  => time_ago(date("F j, Y, g:i a",time())),                              
                                'like'  => '<a href="javascript:iLikeThisComment('.$commentID.')">'._lang("ilike").'</a>'
                                ));
    }
} else {
 echo json_encode(array(
                                'id'    => 0,
                                'text'  => _lang('Register First'),
                                'name'  => _lang('Error'),
                                'mail'  => '',
                                'image' => '',
                                'date'  => '',
                                'total' => '',
                                'like'  => ''
                                ));
}

	?>