<?php
/**
 * Plugin Name: Izlesene.com source
 * Plugin URI: http://phpvibe.com/
 * Description: Adds Izlesene embed source to PHPVibe
 * Version: 2.0
 * Author: PHPVibe Crew
 * Author URI: http://www.phpvibe.com
 * License: Commercial
 */
function _Izlesene($hosts = array()){
$hosts[] = 'izlesene';
return $hosts;
}
function EmbedIzlesene($txt =''){
global $vid;
if(isset($vid)) {
if($vid->VideoProvider($vid->theLink()) == 'izlesene' ) {	
$link = $vid->theLink();if(!nullval($link)) {	
$variable = $vid->theLink();$variable  = substr($variable, 0, strpos($variable, "#list"));$id = $vid->getLastNr($variable);if(!nullval($id)) {
$tembed = ' <iframe src="https://www.izlesene.com/embedplayer/'.$id.'/?showrel=0&loop=0&autoplay=1&autohide=1&showinfo=1&socialbuttons=0" allowfullscreen="true" frameborder="0"></iframe>';
$txt .= $tembed;
}
/* End link check */
}
/* End provider check */
}
/* End isset(vid) */
}
/* End function */
return $txt;
}
add_filter('EmbedModify', 'EmbedIzlesene');
add_filter('vibe-video-sources', '_Izlesene');
?>