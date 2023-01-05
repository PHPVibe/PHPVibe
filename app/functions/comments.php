<?php  $cobj = '';
function comments($iden =null, $p = 1) {
global $video,$cobj;
if (get_option('video-coms') == 1) {
//Facebook comments
if(is_null($iden) && isset($video)) {
/* Facebook video comments */	
return '<div id="coments" class="fb-comments" data-href="'.video_url($video->id(),$video->rawtitle()).'" data-width="100%" data-num-posts="15" data-notify="true"></div>						
';
} else {
/* Facebook comments */	
return '<div id="coments" class="fb-comments" data-href="'.canonical().'" data-width="100%" data-num-posts="15" data-notify="true"></div>';						
	
}	
} else {
/* Local PHPVibe comments system */	
if(is_null($iden) && isset($video)) {$cobj = 'video_'.$video->id();	} else {  $cobj = $iden;}	 
return show_comments($cobj, $p);
}
}
function reply_box($to ='0'){
global $cobj;	
$uhtml = '';
$xtra = ($to > 0) ? "hide" : "";
$comtra = ($to > 0) ? _lang('reply') : _lang('comment');	
if( is_user() ){
    $uhtml .= '<li id="reply-'.$cobj.'-'.$to.'" class="addcm '.$xtra.'">
  <img class="avatar" data-name="'.addslashes(user_name()).'" src="'.thumb_fix(user_avatar(), true, 55, 55).'">
  <div class="message clearfix">
  <div class="arrow">
  <div class="arrow-inner"></div>
  <div class="arrow-outer"></div>
  </div>
  <form class="body" method="post" action="'.site_url().'ajax/addComment.php" onsubmit="return false;">
  <textarea placeholder="'._lang('Write your '.$comtra).'" id="addEmComment_'.$to.'" class="addEmComment auto" name="comment" /></textarea>
  <button type="submit" class="btn btn-default btn-sm buttonS pull-right" id="emAddButton_'.$cobj.'" onclick="addEMComment(\''.$cobj.'\',\''.$to.'\')" />'._lang($comtra).'</button>
  <input type="hidden" name="object_id" value="'.$cobj.'" />
  <input type="hidden" name="reply_to" value="'.$to.'" />
  </form>
  </div>
  </li>';
} elseif($to < 1) {
	$uhtml .= '<li id="reply-'.$cobj.'-'.$to.'" class="addcm '.$xtra.'">
	<img class="avatar" data-name="'.addslashes(user_name()).'" src="'.thumb_fix(site_url().'/storage/uploads/def-avatar.jpg', true, 55, 55).'">
	<div class="message clearfix">
    <div class="arrow">
    <div class="arrow-inner"></div>
    <div class="arrow-outer"></div>
    </div>
    <form class="body" method="post" onsubmit="return false;">
	<textarea placeholder="'._lang("Register or login to leave your impressions.").'" id="addDisable" class="addEmComment auto" name="comment"/></textarea>
    </form>
   </div>
   </li>';
}
return $uhtml; 	
}
function show_comments($object_id, $p = 1, $ALLOWLIKE = true) {
global $db,$cobj,$comsNav,$video;
$limit = 'LIMIT ' .($p - 1) * 10 .',10';
$uhtml = '';
if(!isset($comsNav)) {
	if(isset($canonical)) {
	$comsNav = '<nav id="page_nav"><a href="'.$canonical.'?p='.next_page().'"></a></nav>';
	} else {
	$comsNav = '<nav id="page_nav"><a href="'.canonical().'?p='.next_page().'"></a></nav>';	
	}
	}
//get comments from database
$totals = $db->get_row("SELECT count(*) as nr, count(case when reply=0 then 1 else NULL end) as mains from ".DB_PREFIX."em_comments WHERE object_id =  '".$object_id."'");
$totals->replies = $totals->nr - $totals->mains;
$html     = '<ul id="emContent_'.$object_id.'-0" class="comments full">
<div class="cctotal">'.number_format($totals->mains + $totals->replies).' '._lang("Comments").'</div>
';


$html .=  reply_box();
if($totals->nr > 0) {
$mainComs = '';	$comments = false;
$main_comments = $db->get_results("SELECT id from ".DB_PREFIX."em_comments WHERE object_id =  '".$object_id."' and reply < 1  ORDER BY  ".DB_PREFIX."em_comments.id desc $limit", ARRAY_N);
if($main_comments) {
$mainComs =  implode(",", array_column($main_comments, '0'));
$comments   = $db->get_results("SELECT ".DB_PREFIX."em_comments . *  , ".DB_PREFIX."users.name, ".DB_PREFIX."users.avatar
FROM ".DB_PREFIX."em_comments
LEFT JOIN ".DB_PREFIX."em_likes ON ".DB_PREFIX."em_comments.id = ".DB_PREFIX."em_likes.comment_id and ".DB_PREFIX."em_comments.sender_id = ".DB_PREFIX."em_likes.sender_ip
LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."em_comments.sender_id = ".DB_PREFIX."users.id
WHERE ".DB_PREFIX."em_comments.id in (".$mainComs.") or ".DB_PREFIX."em_comments.reply in (".$mainComs.")
ORDER BY  ".DB_PREFIX."em_comments.id desc limit 0,1000");

if($comments) {
//Get your votes	
$targets=array();
$delete = '';
$is_mod = is_moderator();
foreach( $comments as $tg) {
$targets[]=	$tg->id;
}	
$rated   = $db->get_results("SELECT comment_id from ".DB_PREFIX."em_likes where comment_id in (".implode(',',$targets).") and sender_ip = '".user_id()."'", ARRAY_N);
if(is_array($rated)) {
	$rated =  @array_map('array_pop', $rated);
	}
//Start comments
$ci = 1;
$cmp = array();
	 foreach( $comments as $comment) {
	 if(is_user() && in_array($comment->id,$rated)){            
            $likeText = commentLikeText($comment->rating_cache -1,true);
        }else{
			if(is_user()) {
            $likeText = '<a class="tipS" href="javascript:iLikeThisComment('.$comment->id.')" title="'._lang('Like this comment').'"> <i class="material-icons">thumb_up</i> '._lang('Like').' </a>';		
			} else {
			$likeText = '<a class="tipS" href="javascript:showLogin()" title="'._lang('Like this comment').'"> <i class="material-icons">thumb_up</i> '._lang('Like').' </a>';		
			}
			if($comment->rating_cache){
                $likeText .= ' &mdash; '.commentLikeText($comment->rating_cache,false);
            }
        }
	if(is_user()) {
    $rtxt = '<li class="reply-btn"><a href="javascript:ReplyCom(\'reply-'.$cobj.'-'.$comment->id.'\')">'._lang("Reply").'</a></li>';
	if($is_mod) {
			$delete = '<a title="'._lang('Remove comment').'" href="javascript:DeleteThisComment('.$comment->id.',\''.$comment->access_key.'\')" class="deleteCom tipS"><i class="material-icons">&#xE15C;</i></a>';			
			} else {
				if(user_id() <> $comment->sender_id ) {
				$delete = '';
				}else {			
				$delete = '<a title="'._lang('Delete comment').'" href="javascript:DeleteThisComment('.$comment->id.',\''.$comment->access_key.'\')" class="deleteCom tipS"><i class="material-icons">&#xE872;</i></a>';			
				} 
			}	
	} else {
    $rtxt = '<li class="reply-btn"><a href="javascript:showLogin()">'._lang("Reply").'</a></li>';
	$delete = '';
	}	
	$cls = ($comment->reply < 1) ? $rtxt : '';
	$cmp[$comment->reply][$comment->id]['id']=$comment->id;
	$author = (isset($video) && ($video->owner() == $comment->sender_id)) ? '<span class="com_author badge">'._lang('Author').'</span>':'';
    $cmp[$comment->reply][$comment->id]['body']= ' <li id="comment-id-'.$comment->id.'" class="comment-'.$comment->reply.' left">
    <a class="theCAvatar" href="'.profile_url($comment->sender_id,$comment->name).'" title="'.addslashes($comment->name).'">
	<img class="avatar" src="'.thumb_fix($comment->avatar, true, 55, 55).'" data-name="'.addslashes($comment->name).'" alt="'.addslashes($comment->name).'"">
	</a>
<div class="message">
<span class="arrow"> </span>
<a class="name" href="'.profile_url($comment->sender_id,$comment->name).'" title="'.addslashes($comment->name).'">'._html($comment->name).'
 '.$author.' <span class="com_time">'.time_ago($comment->created).'</span>
</a> 
<span class="body">'.emojify(_html($comment->comment_text)).' '.$delete.'</span>
<ul class="msg-footer">
'.$cls.'
<li><span class="like-com" id="iLikeThis_'.$comment->id.'">'.$likeText.'</span></li>
</ul>
';	
$cmp[$comment->reply][$comment->id]['body'] .='</div>
';
$ci++;
$delete = '';
}
foreach ($cmp[0] as $body) {	
$html .= $body['body'];
$html .= '<ul id="emContent_'.$cobj.'-'.$body['id'].'" class="reply" >';
if(is_user()) {
$html .= reply_box($body['id']);	
}
if(isset($cmp[$body['id']])){

foreach ($cmp[$body['id']] as $ch) {
$html .= $ch['body'];	
$html .= '</li>';
}
}
$html .= "</ul>";
$html .= '</li>';
}
 $html .= '</ul>';
} 
}
}
$psts = ' <div class="full mtop10 text-center"><button class="btn btn-default view-more-button">'._lang("View more").'</button></div>
<div class="page-load-status">
  <div class="infinite-scroll-request" style="display:none">
    <div class="cp-spinner cp-flip"></div>  
    <p>'._lang('Loading more comments').'</p>
  </div>
  <p class="infinite-scroll-error infinite-scroll-last" style="display:none">
    '._lang('That\'s all!').'
  </p>
</div>
';
if(($totals->nr > 10) && isset($mainComs) && not_empty($mainComs)) {
    //send reply to client
    return '<div id="'.$object_id.'" class="emComments" object="'.$object_id.'" class="ignorejsloader">'.$html.'
	'.$comsNav.$psts.'</div>';
} else {
	//send reply to client
    return '<div id="'.$object_id.'" class="emComments" object="'.$object_id.'" class="ignorejsloader">'.$html.'<div id="NoMore" class="NoMore"></div></div>';
}
}

function commentLikeText($total, $me=true){
           
        if($me){
			if($total < 0){
			return '';	
			}
            elseif($total == 0){
                return '<i class="material-icons">thumb_up</i> '._lang('by you');
            }elseif($total == 1){
                return '<i class="material-icons">thumb_up</i> '._lang('by you +1 like this');
            }else{
                return '<i class="material-icons">thumb_up</i> '.str_replace('XXX',$total,_lang('by you and XXX others'));
            }       
        }else{
            if($total < 0){
			return '';	
			}
            elseif($total == 1){
                return '<i class="material-icons">thumb_up</i>'._lang('by one');
            }else{
                return '<i class="material-icons">thumb_up</i>'.str_replace('XXX',$total,_lang(' by XXX others'));
            }
        }
    }	
 ?>