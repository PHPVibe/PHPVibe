<?php /*!
* phpVibe v5.9
*
* Copyright Media Vibe Solutions
* http://www.phpvibe.com
* phpVibe IS NOT A FREE SOFTWARE
* If you have downloaded this CMS from a website other
* than www.phpvibe.com if you have received
* this CMS from someone who is not a representative of phpVibe, you are involved in an illegal activity.
* The phpVibe team takes actions against all unlincensed websites using Google, local authorities and 3rd party agencies.
* Designed and built exclusively for sale @ phpVibe.com.
*/
function _jwads() {
global $cachedb;
define( 'jwplayerloaded', 'true');
$ads = $cachedb->get_results("select * from ".DB_PREFIX."jads limit 0,1000",ARRAY_A);
$cads = ''; $fads = '';
if($ads) {
$fads = ' $(document).ready(function() {
jwplayer().onPlay( function(){
$(\'.plAd\').detach().appendTo(\'.jwplayer\');	
$(\'.bAd\').detach().appendTo(\'.jwplayer\');	
$(\'div.screenAd\').addClass(\'hide\');
$(\'.plAd\').addClass(\'hide\');
});';
$pre=array();
foreach ($ads as $ad) {
$ar = array();
$pre[$ad['jad_type']][] = $ad;
}
/* Let's start rendering*/
if(isset($pre[3]) && !is_null($pre[3])) {
/* Pre-roll ad */
/* Extract only one random ad */
$pread = array();
$pread = $pre[3][array_rand($pre[3], 1)];
$pread["jad_body"] = trim(stripslashes($pread["jad_body"]));
//var_dump($pread);
$fads .= " jwplayer().stop();	
$('.pre-roll-ad').removeClass('hide');
$('.bigadclose').click(function(){ $('.pre-roll-ad').addClass('hide'); jwplayer().play(true); });

";
$cads .= "<div id='bigAd' class='pre-roll-ad screenAd hide'><div class='innerAd'>".$pread['jad_body']." <a class='bigadclose' href='javascript:void(0)'>"._lang('Skip this and play')."</a></div></div>";
/* End pre-rol */
}
if(!has_list() && (isset($pre[4]) && !is_null($pre[4]))) {
/* Post-roll ad */
/* Extract only one random ad */
$postad = array();
$postad = $pre[4][array_rand($pre[4], 1)];
//var_dump($postad);
$postad["jad_body"] = trim(stripslashes($postad["jad_body"]));
$fads .= "
jwplayer().onComplete( function(){
$('.post-roll-ad').removeClass('hide');
$('.bigadclose').click(function(){  jwplayer().play(true); });
});
";
$cads .= "<div id='bigAd' class='post-roll-ad screenAd hide'><div class='innerAd'>".$postad['jad_body']." <a class='bigadclose' href='javascript:void(0)'>"._lang('Restart the video')."</a></div></div>";
/* End post-rol */
}
/* Star time dependable events */
$fads .= "
jwplayer().onTime( function(){
var currentTime = Math.floor(jwplayer().getPosition());
";
/* Start Annotations */
if(isset($pre[2]) && !is_null($pre[2])) {
foreach ($pre[2] as $an) {
//var_dump($an);
$an["jad_body"] = trim(stripslashes($an["jad_body"]));
$fads .= "
if (currentTime == ".$an["jad_start"]."){
if($('#PL".$an["jad_id"]."').hasClass('hide')){
$('#PL".$an["jad_id"]."').removeClass('hide');
$(\"#PL".$an["jad_id"]." > .plclose\").click(function(){  $('#PL".$an["jad_id"]."').addClass('hide');   });
}
}
";
$cads .= "<div id='PL".$an["jad_id"]."' class='plAd ".$an["jad_pos"]." hide'>".$an["jad_body"]."<a class='plclose' href='javascript:void(0)'></a></div>";
if(intval($an["jad_end"]) > 0) {
$time = $an["jad_end"] + $an["jad_start"];
$fads .=  '
if(!$("#PL'.$an["jad_id"].'").hasClass("hide")){
if (currentTime > '.$time.'){
$("#PL'.$an["jad_id"].'").addClass("hide");
}
}
';
}
}
}
/* End Annotations */
/* Start OVerlays */
if(isset($pre[5]) && !is_null($pre[5])) {
foreach ($pre[5] as $an) {
$an["jad_body"] = trim(stripslashes($an["jad_body"]));
$box_render = array("0" =>"plTransparent" , "1" => "");
//var_dump($an);
$fads .= "
if (currentTime == ".$an["jad_start"]."){
if($('#BD".$an["jad_id"]."').hasClass('hide')){
$(\"div.bAd\").addClass('hide');
$('#BD".$an["jad_id"]."').removeClass('hide');
$(\".adclose\").click(function(){  	$(\"div.bAd\").addClass('hide');   });
}
}
";
$cads .= "<div id='BD".$an["jad_id"]."' class='bAd ".$box_render[intval($an["jad_box"])]." hide'><div class='innerAd'>".$an["jad_body"]."<a class='adclose' href='javascript:void(0)'></a></div></div>";
if(intval($an["jad_end"]) > 0) {
$time = $an["jad_end"] + $an["jad_start"];
$fads .=  '
if(!$("#BD'.$an["jad_id"].'").hasClass("hide")){
if (currentTime > '.$time.'){
$("#BD'.$an["jad_id"].'").addClass("hide");
}
}
';
}
}
}
/* End Overlays */
$fads .= "
});
";
/* End time dependable events */
$fads .= "
});
";
}
/* Ends IF ads */
$res = array();
/* jquery return */
$res['js'] = $fads;
/* html return */
$res['html'] =$cads;
return $res;
}
function _vjsads() {
global $cachedb;
define( 'vjsloaded', 'true');
$ads = $cachedb->get_results("select * from ".DB_PREFIX."jads limit 0,1000",ARRAY_A);
$cads = ''; $fads = '';
if($ads) {
$fads = ' $(document).ready(function() {
myPlayer.on("play", function(){
$(\'div.screenAd\').addClass(\'hide\');
$(\'.plAd\').addClass(\'hide\');
});
';
$pre=array();
foreach ($ads as $ad) {
$ar = array();
$pre[$ad['jad_type']][] = $ad;
}
/* Let's start rendering*/
if(isset($pre[3]) && !is_null($pre[3])) {
/* Pre-roll ad */
/* Extract only one random ad */
$pread = array();
$pread = $pre[3][array_rand($pre[3], 1)];
$pread["jad_body"] = trim(stripslashes($pread["jad_body"]));
//var_dump($pread);
$fads .= " myPlayer.pause();	
myPlayer.ready(function(){
$('.pre-roll-ad').removeClass('hide');
$('.bigadclose').click(function(){ $('.pre-roll-ad').addClass('hide'); myPlayer.play(); });
});
";
$cads .= "<div id='bigAd' class='pre-roll-ad screenAd hide'><div class='innerAd'>".$pread['jad_body']." <a class='bigadclose' href='javascript:void(0)'>"._lang('Skip this and play')."</a></div></div>";
/* End pre-rol */
}
if(!has_list() && (isset($pre[4]) && !is_null($pre[4]))) {
/* Post-roll ad */
/* Extract only one random ad */
$postad = array();
$postad = $pre[4][array_rand($pre[4], 1)];
//var_dump($postad);
$postad["jad_body"] = trim(stripslashes($postad["jad_body"]));
$fads .= "
myPlayer.on('ended', function(){
$('.bAd').addClass('hide');	
$('.post-roll-ad').removeClass('hide');
$('.bigadclose').click(function(){  myPlayer.play(); });
});
";
$cads .= "<div id='bigAd' class='post-roll-ad screenAd hide'><div class='innerAd'>".$postad['jad_body']." <a class='bigadclose' href='javascript:void(0)'>"._lang('Restart the video')."</a></div></div>";
/* End post-rol */
}
/* Star time dependable events */
$fads .= "
myPlayer.on('timeupdate', function(){
var currentTime = Math.floor(myPlayer.currentTime());
";
/* Start Annotations */
if(isset($pre[2]) && !is_null($pre[2])) {
foreach ($pre[2] as $an) {
//var_dump($an);
$an["jad_body"] = trim(stripslashes($an["jad_body"]));
$fads .= "
if (currentTime == ".$an["jad_start"]."){
if($('#PL".$an["jad_id"]."').hasClass('hide')){
$('#PL".$an["jad_id"]."').removeClass('hide');
$(\"#PL".$an["jad_id"]." > .plclose\").click(function(){  $('#PL".$an["jad_id"]."').addClass('hide');   });
}
}
";
$cads .= "<div id='PL".$an["jad_id"]."' class='plAd ".$an["jad_pos"]." hide'>".$an["jad_body"]."<a class='plclose' href='javascript:void(0)'></a></div>";
if(intval($an["jad_end"]) > 0) {
$time = $an["jad_end"] + $an["jad_start"];
$fads .=  '
if(!$("#PL'.$an["jad_id"].'").hasClass("hide")){
if (currentTime > '.$time.'){
$("#PL'.$an["jad_id"].'").addClass("hide");
}
}
';
}
}
}
/* End Annotations */
/* Start OVerlays */
if(isset($pre[5]) && !is_null($pre[5])) {
foreach ($pre[5] as $an) {
$an["jad_body"] = trim(stripslashes($an["jad_body"]));
$box_render = array("0" =>"plTransparent" , "1" => "");
//var_dump($an);
$fads .= "
if (currentTime == ".$an["jad_start"]."){
if($('#BD".$an["jad_id"]."').hasClass('hide')){
$(\"div.bAd\").addClass('hide');
$('#BD".$an["jad_id"]."').removeClass('hide');
$(\".adclose\").click(function(){  	$(\"div.bAd\").addClass('hide');   });
}
}
";
$cads .= "<div id='BD".$an["jad_id"]."' class='bAd ".$box_render[intval($an["jad_box"])]." hide'><div class='innerAd'>".$an["jad_body"]."<a class='adclose' href='javascript:void(0)'></a></div></div>";
if(intval($an["jad_end"]) > 0) {
$time = $an["jad_end"] + $an["jad_start"];
$fads .=  '
if(!$("#BD'.$an["jad_id"].'").hasClass("hide")){
if (currentTime > '.$time.'){
$("#BD'.$an["jad_id"].'").addClass("hide");
}
}
';
}
}
}
/* End Overlays */
$fads .= "
});
";
/* End time dependable events */
$fads .= "
});
";
}
/* Ends IF ads */
$res = array();
/* jquery return */
$res['js'] = $fads;
/* html return */
$res['html'] =$cads;
return $res;
}
function _flowads() {
global $cachedb;
define( 'flowloaded', 'true');
$ads = $cachedb->get_results("select * from ".DB_PREFIX."jads limit 0,1000",ARRAY_A);
$cads = ''; $fads = '';
if($ads) {
$fads = ' $(document).ready(function() {
api = flowplayer($(".flowplayer"));
api.one("playing", function(){
$(\'div.screenAd\').addClass(\'hide\');
$(\'.plAd\').addClass(\'hide\');
});
';
$pre=array();
foreach ($ads as $ad) {
$ar = array();
$pre[$ad['jad_type']][] = $ad;
}
/* Let's start rendering*/
if(isset($pre[3]) && !is_null($pre[3])) {
/* Pre-roll ad */
/* Extract only one random ad */
$pread = array();
$pread = $pre[3][array_rand($pre[3], 1)];
$pread["jad_body"] = trim(stripslashes($pread["jad_body"]));
//var_dump($pread);
$fads .= "
api.one('ready', function(e, api, video) {
api.stop();	
$('.pre-roll-ad').removeClass('hide');
$('.bigadclose').click(function(){ 
$('.pre-roll-ad').addClass('hide'); 
api.play();
});
});
";
$cads .= "<div id='bigAd' class='pre-roll-ad screenAd hide'><div class='innerAd'>".$pread['jad_body']." <a class='bigadclose' href='javascript:void(0)'>"._lang('Skip this and play')."</a></div></div>";
/* End pre-rol */
}
if(!has_list() && (isset($pre[4]) && !is_null($pre[4]))) {
/* Post-roll ad */
/* Extract only one random ad */
$postad = array();
$postad = $pre[4][array_rand($pre[4], 1)];
//var_dump($postad);
$postad["jad_body"] = trim(stripslashes($postad["jad_body"]));
$fads .= "
api.bind('finish', function (e, api, video) {
$('.post-roll-ad').removeClass('hide');
$('.plAd').addClass('hide');
$('.bAd').addClass('hide');
$('.bigadclose').click(function(){
$('div.screenAd').addClass('hide');
api.play();
});
});
";
$cads .= "<div id='bigAd' class='post-roll-ad screenAd hide'><div class='innerAd'>".$postad['jad_body']." <a class='bigadclose' href='javascript:void(0)'>"._lang('Restart the video')."</a></div></div>";
/* End post-rol */
}
/* Star time dependable events */
$fads .= "
api.bind('progress', function (e, api, video) {
var currentTime = Math.floor(api.video.time);
";
/* Start Annotations */
if(isset($pre[2]) && !is_null($pre[2])) {
foreach ($pre[2] as $an) {
//var_dump($an);
$an["jad_body"] = trim(stripslashes($an["jad_body"]));
$fads .= "
if (currentTime == ".$an["jad_start"]."){
if($('#PL".$an["jad_id"]."').hasClass('hide')){
$('#PL".$an["jad_id"]."').removeClass('hide');
$(\"#PL".$an["jad_id"]." > .plclose\").click(function(){  $('#PL".$an["jad_id"]."').addClass('hide');   });
}
}
";
$cads .= "<div id='PL".$an["jad_id"]."' class='plAd ".$an["jad_pos"]." hide'>".$an["jad_body"]."<a class='plclose' href='javascript:void(0)'></a></div>";
if(intval($an["jad_end"]) > 0) {
$time = $an["jad_end"] + $an["jad_start"];
$fads .=  '
if(!$("#PL'.$an["jad_id"].'").hasClass("hide")){
if (currentTime > '.$time.'){
$("#PL'.$an["jad_id"].'").addClass("hide");
}
}
';
}
}
}
/* End Annotations */
/* Start OVerlays */
if(isset($pre[5]) && !is_null($pre[5])) {
foreach ($pre[5] as $an) {
$an["jad_body"] = trim(stripslashes($an["jad_body"]));
$box_render = array("0" =>"plTransparent" , "1" => "");
//var_dump($an);
$fads .= "
if (currentTime == ".$an["jad_start"]."){
if($('#BD".$an["jad_id"]."').hasClass('hide')){
$(\"div.bAd\").addClass('hide');
$('#BD".$an["jad_id"]."').removeClass('hide');
$(\".adclose\").click(function(){  	$(\"div.bAd\").addClass('hide');   });
}
}
";
$cads .= "<div id='BD".$an["jad_id"]."' class='bAd ".$box_render[intval($an["jad_box"])]." hide'><div class='innerAd'>".$an["jad_body"]."<a class='adclose' href='javascript:void(0)'></a></div></div>";
if(intval($an["jad_end"]) > 0) {
$time = $an["jad_end"] + $an["jad_start"];
$fads .=  '
if(!$("#BD'.$an["jad_id"].'").hasClass("hide")){
if (currentTime > '.$time.'){
$("#BD'.$an["jad_id"].'").addClass("hide");
}
}
';
}
}
}
/* End Overlays */
$fads .= "
});
";
/* End time dependable events */
$fads .= "
});
";
}
/* Ends IF ads */
$res = array();
/* jquery return */
$res['js'] = $fads;
/* html return */
$res['html'] =$cads;
return $res;
}
function _jads() {
/* Load ads to jPlayer */
global $cachedb;
define( 'jplayerloaded', 'true');
$ads = $cachedb->get_results("select * from ".DB_PREFIX."jads limit 0,1000",ARRAY_A);
$fads = '';
$cads = ''; $fads = '';
if($ads) {
$pre=array();
foreach ($ads as $ad) {
$ar = array();
$pre[$ad['jad_type']][] = $ad;
}
/* Let's start rendering*/
if(isset($pre[3]) && !is_null($pre[3])) {
/* Pre-roll ad */
/* Extract only one random ad */
$pread = array();
$pread = $pre[3][array_rand($pre[3], 1)];
$pread["jad_body"] = trim(stripslashes($pread["jad_body"]));
//var_dump($pread);
$fads .= "
$('.mediaPlayer').bind($.jPlayer.event.ready, function() {
$(cpJP).jPlayer('pause');
$('.pre-roll-ad').removeClass('hide');
$('.bigadclose').click(function(){  $(cpJP).jPlayer('play'); });
});
";
$cads .= "<div id='bigAd' class='pre-roll-ad screenAd hide'><div class='innerAd'>".$pread['jad_body']." <a class='bigadclose' href='javascript:void(0)'>"._lang('Skip this and play')."</a></div></div>";
/* End pre-rol */
}
if(!has_list() && isset($pre[4]) && !is_null($pre[4])) {
/* Post-roll ad */
/* Extract only one random ad */
$postad = array();
$postad = $pre[4][array_rand($pre[4], 1)];
//var_dump($postad);
$postad["jad_body"] = trim(stripslashes($postad["jad_body"]));
$fads .= "
$('.mediaPlayer').bind($.jPlayer.event.ended, function() {
$(cpJP).jPlayer('pause');
$('.post-roll-ad').removeClass('hide');
$('.bigadclose').click(function(){  $(cpJP).jPlayer('play'); });
});
";
$cads .= "<div id='bigAd' class='post-roll-ad screenAd hide'><div class='innerAd'>".$postad['jad_body']." <a class='bigadclose' href='javascript:void(0)'>"._lang('Restart the video')."</a></div></div>";
/* End post-rol */
}
/* Star time dependable events */
$fads .= "
$('.mediaPlayer').bind($.jPlayer.event.timeupdate, function(event) {
var currentTime = Math.floor(event.jPlayer.status.currentTime);
";
/* Start Annotations */
if(isset($pre[2]) && !is_null($pre[2])) {
foreach ($pre[2] as $an) {
//var_dump($an);
$an["jad_body"] = trim(stripslashes($an["jad_body"]));
$fads .= "
if (currentTime == ".$an["jad_start"]."){
if($('#PL".$an["jad_id"]."').hasClass('hide')){
$('#PL".$an["jad_id"]."').removeClass('hide');
$(\"#PL".$an["jad_id"]." > .plclose\").click(function(){  $('#PL".$an["jad_id"]."').addClass('hide');   });
}
}
";
$cads .= "<div id='PL".$an["jad_id"]."' class='plAd ".$an["jad_pos"]." hide'>".$an["jad_body"]."<a class='plclose' href='javascript:void(0)'></a></div>";
if(intval($an["jad_end"]) > 0) {
$time = $an["jad_end"] + $an["jad_start"];
$fads .=  '
if(!$("#PL'.$an["jad_id"].'").hasClass("hide")){
if (currentTime > '.$time.'){
$("#PL'.$an["jad_id"].'").addClass("hide");
}
}
';
}
}
}
/* End Annotations */
/* Start OVerlays */
if(isset($pre[5]) && !is_null($pre[5])) {
foreach ($pre[5] as $an) {
$an["jad_body"] = trim(stripslashes($an["jad_body"]));
$box_render = array("0" =>"plTransparent" , "1" => "");
//var_dump($an);
$fads .= "
if (currentTime == ".$an["jad_start"]."){
if($('#BD".$an["jad_id"]."').hasClass('hide')){
$(\"div.bAd\").addClass('hide');
$('#BD".$an["jad_id"]."').removeClass('hide');
$(\".adclose\").click(function(){  	$(\"div.bAd\").addClass('hide');   });
}
}
";
$cads .= "<div id='BD".$an["jad_id"]."' class='bAd ".$box_render[intval($an["jad_box"])]." hide'><div class='innerAd'>".$an["jad_body"]."<a class='adclose' href='javascript:void(0)'></a></div></div>";
if(intval($an["jad_end"]) > 0) {
$time = $an["jad_end"] + $an["jad_start"];
$fads .=  '
if(!$("#BD'.$an["jad_id"].'").hasClass("hide")){
if (currentTime > '.$time.'){
$("#BD'.$an["jad_id"].'").addClass("hide");
}
}
';
}
}
}
/* End Overlays */
$fads .= "
});
";
/* End time dependable events */
}
/* Ends IF ads */
$fads .= "
});
";
$res = array();
/* jquery return */
$res['js'] = $fads;
/* html return */
$res['html'] =$cads;
return $res;
}
?>
