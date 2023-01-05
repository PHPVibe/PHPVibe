<?php include_once('../../load.php');
if (is_user( )) {
    $_REQUEST['comment_id']  = intval($_REQUEST['comment_id']);

    if($_REQUEST['comment_id']){
	 if(!has_activity('7', intval($_REQUEST['comment_id']))) {
        //insert comment into database
        $insert_com = $db->query('INSERT INTO '.DB_PREFIX.'em_likes SET comment_id   = '.intval($_REQUEST['comment_id']).', sender_ip    = '.user_id( ));
        add_activity('7', intval($_REQUEST['comment_id']));
      
        //update cache
        $up_rate = $db->query('UPDATE '.DB_PREFIX.'em_comments SET rating_cache = rating_cache + 1  WHERE id = '.intval($_REQUEST['comment_id']));
    }
        header('Content-type: application/x-json');
        echo json_encode(array(
                                'id'    => $_REQUEST['comment_id'],
                                'text'  => '<i class="icon-heart"></i>'._lang('Liked')
                                ));
    } 
	}else {
		header('Content-type: application/x-json');
        echo json_encode(array(
                                'id'    => $_REQUEST['comment_id'],
                                'text'  => '<i class="icon-heart-o"></i>'._lang('Login')
                                ));
	}

?>