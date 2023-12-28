<?php $v_id = _dHash(toDb(token()));
//Global video weight & height
$width = get_option('video-width');  $height = get_option('video-height'); $embedCode = '';
if(intval($v_id) > 0) { 

//Get the video
$video = new PHPVibe\Video\SingleVideo($v_id);
if ($video->isvalid()) {
//Check if it's processing
if(!$video->hassource() && !$video->isembed() && !$video->isremote()) {
 $embedvideo = '<img src="'.site_url().'storage/uploads/processing.png"/>';
 $origin = 0;
} else {
//See what embed method to use
if($video->isremote()) {
	//Check if video is remote/link
   $vid = new Vibe_Providers($width, $height);    $embedvideo = $vid->remotevideo($video->remote());
    $origin = 1;
   } elseif($video->isembed()) {
   //Check if has embed code
	$embedvideo	=  render_video(stripslashes($video->embed));
   $origin = 2;
   } else {
   //Embed from external video url
   $vid = new Vibe_Providers($width, $height);    $embedvideo = $vid->getEmbedCode($video->source());
   $origin = 0;
   }
 } 
 /* Load assets for players */
if($video->ismusic()) {
/* Load player for music */	
//VideoJs Waves
add_filter( 'addplayers', 'vjsup' ); 	
add_filter( 'addplayers', 'wavesup' ); 
} else {
/* Load player for videos (uploaded and remote) */	
//JwPlayer
if((get_option('youtube-player') == 2 ) || ((get_option('remote-player',1) == 1) || (get_option('choosen-player', 1) == 1))) {
add_filter( 'addplayers', 'jwplayersup' );
}  
//FlowPlayer
if(((get_option('remote-player',1) == 2) && ($origin == 1)) || (get_option('choosen-player',1) == 2))	{					 
add_filter( 'addplayers', 'flowsup' );  
}
if(((get_option('remote-player',1) == 3) && ($origin == 1)) || (get_option('choosen-player',1) == 3))	{					 
//jPlayer
add_filter( 'addplayers', 'jpsup' );  
}
//VideoJS
if(((get_option('remote-player',1) == 6) && ($origin == 1)) || (get_option('choosen-player',1) == 6)|| (get_option('youtube-player',1) == 3))	{					 
add_filter( 'addplayers', 'vjsup' );  
}
}
$canonical = video_url($video->id() , $video->title()); 
 //Print iframe content
 echo '<!DOCTYPE html>  <html lang="en" dir="ltr"  data-cast-api-enabled="true">
<head>
<title>'._html($video->title()).' - '.get_option('site-logo-text').'</title>
<link rel="canonical" href="'.video_url($video->id() , $video->title()).'">
<style>
body,html{overflow:hidden;display:block;margin:0;padding:0;max-width:100%}img{max-width:100%;height:auto;vertical-align:middle;border:0;-ms-interpolation-mode:bicubic;vertical-align:middle}.video-js{display:block;overflow:hidden}.embeddedVP{width:100%;height:100vh;display:block;overflow:hidden}.embeddedVP .plAd img{max-width:140px!important}.video-player iframe{position:absolute;top:0;left:0;width:100%!important;height:100%!important}.hide{display:none!important}
</style>
<link rel="stylesheet" href="'.tpl().'styles/playerads.css"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons"/>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script>
if((typeof jQuery == "undefined") || !window.jQuery )
{
   var script = document.createElement("script");
   script.type = "text/javascript";
   script.src = "https://code.jquery.com/jquery-2.2.4.min.js";
   document.getElementsByTagName(\'head\')[0].appendChild(script);
}
</script>
<script type="text/javascript" src="'.tpl().'scripts/embedtrack.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $(".close-ad").click(function(){
 	$(this).closest(".adx").hide();
    });		
	 $(".plclose").click(function(){
 	$(this).closest(".plAd").hide();
    });	

 });		
</script>
'.players_js(); ?>
<script type="text/javascript">
var site_url = '<?php echo site_url(); ?>';
</script>
</head>
<body dir="ltr">
<div class="video-player embeddedVP">
<?php 
//Print the embed code
echo  $embedvideo; ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
DOtrackview(<?php echo $video->id(); ?>);
});
</script>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo Fb_Key; ?>";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
</body>
</html>
<?php } else {
//No video
echo _lang("Video was removed.");
}

}
echo '
<div id=\'PL2\' class=\'plAd plBotRight hide\' style=\'font-size:16px; padding:0 8px 0\'>'._lang('Watch more great videos at').' <a  style=\'font-size:16px; padding:0 8px 0\' href=\''.site_url().'\' target=\'_blank\'> '.get_option('site-logo-text').'</a> <a class=\'plclose\' href=\'javascript:void(0)\'></a><div class="clearfix"></div></div>
<script>
startNextVideo = function() { 
$(".plAd").detach().appendTo(".embeddedVP").removeClass("hide");	

}
</script>
';
?>