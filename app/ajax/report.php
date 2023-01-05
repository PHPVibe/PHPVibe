<?php include_once('../../load.php');
$add_video = intval($_POST['id']);
if(is_empty($_POST['report-text'])) { echo '<div class="msg-warning">'._lang('No detail entered. Report rejected').'</div>';}
if(is_user() && ($add_video > 0) && $_POST['rep'] && $_POST['report-text']) {
$saferep = array();
foreach ($_POST['rep'] as $rep) {
//sanitize values
$saferep[] = toDb($rep);
}
$reason = maybe_serialize($saferep);
$motive = toDb($_POST['report-text']);
$db->query("INSERT INTO ".DB_PREFIX."reports (`uid`, `vid`, `reason`, `motive`) VALUES ('".user_id()."', '".$add_video."', '".$reason."', '".$motive."')");


echo '<div class="msg-info">'._lang('Report sent!').'</div>';
} else {
echo '<div class="msg-warning">'._lang('Report not sent! Please make sure you complete all fields').'</div>';
}
?>