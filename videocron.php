<?php error_reporting(E_ALL); 
//Vital file include
require_once("load.php");
$tp = ABSPATH.'/storage/'.get_option('tmp-folder','rawmedia')."/";
//echo $tp;
$fp = ABSPATH.'/storage/'.get_option('mediafolder','media')."/";
$folder = $fp;
$ip = ABSPATH.'/storage/'.get_option('mediafolder','media').'/thumbs/';	;
/* Get sizes */
$sizes = get_option('ffmeg-qualities','360');
$to = @explode(",", $sizes);

//Run conversions
$crons = $db->get_results("select id,tmp_source,token from ".DB_PREFIX."videos where tmp_source != '' limit 0,100000");
//var_dump($crons);
if($crons) {

foreach ($crons as $cron) {
$input = $tp.$cron->tmp_source;
//echo $input;
$final = $fp.$cron->token;
$check = $fp.$cron->token.'.mp4';
$source= 'up';
//Fix for plupload
//generating .id extension
if (!file_exists($input)) { 
$pattern = "{*".$cron->token."*}";
$vl = glob($tp.$pattern, GLOB_BRACE);
	if($vl) {
		foreach($vl as $vidid) {
		rename($vidid,$input);
		}	
	}
}
// Log start
//vibe_log("<br>Conversion starting: <br><code>".$input."</code><br>");	
if (file_exists($input)) { 
$va = _get_va($input, get_option('ffmpeg-cmd','ffmpeg'));
$size = 	$va['height']; 
$duration = $va['duration'];
if(is_empty($duration)) {$duration = 0;}
//If mp4 copy it
$ext = substr($input, strrpos($input, '.') + 1);
	if($ext == "mp4") {
	/* Alter this from '.mp4' to 'hd.mp4' to reconvert to original size as well */
	$double = $fp.$cron->token.'-'.$size.'.mp4';
	copy($input, $double);	
	}	
//Do not double
$db->query("UPDATE  ".DB_PREFIX."videos SET tmp_source='',duration='".intval($duration)."'  WHERE id = '".intval($cron->id)."'");
//Extract thumbnail
$imgout = '{ffmpeg-cmd} -i {input} -vf "select=gt(scene,0.3),scale=360:202:force_original_aspect_ratio=increase,crop=360:202" -frames:v 10 -vsync vfr -vf fps=fps=1/60 -qscale:v 2  {token}-%02d.jpg';

/* (Thumbs not working?) Fallback to this
$imgout = '{ffmpeg-cmd} -i {input} -vf "select=gt(scene,0.3)" -frames:v 5 -vsync vfr -vf fps=fps=1/60 -qscale:v 2 {token}-%02d.jpg';
*/
$imgfinal = $ip.$cron->token;
$thumb = str_replace(ABSPATH.'/' ,'',$ip.$cron->token.'-01.jpg');
$imgout = str_replace(array('{ffmpeg-cmd}','{input}','{token}'),array(get_option('ffmpeg-cmd','ffmpeg'), $input,$imgfinal), $imgout);
$thisimgoutput = shell_exec ( $imgout);
vibe_log('<br>'.$imgout.' <br> '.$thisimgoutput.'<br>');
// Update database
$db->query("UPDATE  ".DB_PREFIX."videos SET thumb='".$thumb."', source='".$source."', pub = '".intval(get_option('videos-initial'))."'  WHERE id = '".intval($cron->id)."'");
add_activity('4', $cron->id); 
//Start video conversion

$command ='';
$commandlog ='';
/* Loop qualities */
foreach ($to as $call) {
if($call <= ($size + 100)) {	
if(not_empty($call)) {	
$conv = get_option('fftheme-'.$call,'');
if(not_empty($conv)) {	
$out = str_replace(array('{ffmpeg-cmd}','{input}','{output}'),array(get_option('ffmpeg-cmd','ffmpeg'), $input,$final), $conv);
$command .=$out.';';
$commandlog .='<p class="small">'.$out.'</p>';
}
}
}
}
/* Silently exec chained ffmpeg commands*/
if(not_empty($command)) {	
vibe_log('Sent cmds: ' .$commandlog);
$thisoutput = shell_exec("$command > /dev/null 2>/dev/null &");
//logs last lines
vibe_log('Last lines for debug: '.mb_substr($thisoutput, -750));
}


/* End this loops item */
/* Clean 0 size files */
/* Get list of video files attached */
if(not_empty($folder)) {
$pattern = "{*".$cron->token."*}";
$vl = glob($folder.$pattern, GLOB_BRACE);
foreach($vl as $videocheck) {
if((filesize($videocheck) < 15000) && !is_dir($videocheck) && !_contains($videocheck, 'vtt') && !_contains($videocheck, 'srt')){
remove_file($videocheck);
vibe_log("Removed $videocheck for 0 filesize \n");
}	
}
}
} else {
vibe_log("<br>Conversion failed for: <br><code>".$input."</code> - File not found<br>");	
}
/* End foreach cron */
}
/* Get list of video files attached */
if(not_empty($folder)) {
$pattern = "{*}";
$vl = glob($folder.$pattern, GLOB_BRACE);
foreach($vl as $videocheck) {
if((filesize($videocheck) < 15000) && !is_dir($videocheck) && !_contains($videocheck, 'vtt') && !_contains($videocheck, 'srt')){
//remove_file($videocheck);
//vibe_log("Removed $videocheck for 0 filesize \n");
}	
}
}
$db->clean_cache();
} 

?>