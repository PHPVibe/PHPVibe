<?php if(isset($_GET['ac']) && $_GET['ac'] ="remove-logo"){
update_option('site-logo', '');
 $db->clean_cache();
}
if(isset($_POST['update_options_now'])){
foreach($_POST as $key=>$value)
{
if($key !== "site-logo") {
  update_option($key, $value);
}
}
  echo '<div class="msg-info">Configuration options have been updated.</div>';

//Set logo
if(isset($_FILES['site-logo']) && !empty($_FILES['site-logo']['name'])){
$extension = end(explode('.', $_FILES['site-logo']['name']));
$thumb = ABSPATH.'/uploads/'.nice_url($_FILES['site-logo']['name']).uniqid().'.'.$extension;
if (move_uploaded_file($_FILES['site-logo']['tmp_name'], $thumb)) {
     $sthumb = str_replace(ABSPATH.'/' ,'',$thumb);
    update_option('site-logo', $sthumb);
	  //$db->clean_cache();
	} else {
	echo '<div class="msg-warning">Logo upload failed.</div>';
	}
	
}
  $db->clean_cache();
}
$all_options = get_all_options();
?>

<div class="row">
<h3>EzSql Caching</h3>
For now this settings reside in vibe_config.
<pre>
/** MySQL cache timeout */
/** For how many hours should queries be cached? **/
define( 'DB_CACHE', '1' );
</pre>
<h3>FullCache</h3>
For now this settings reside in app/classes/fullcache.php
<pre>
define('FULLCACHE_DEFAULT_TTL', 10800);
</pre>
Note: fullcache duration is in seconds.

Further fullcache manipulation can be done in load.php
<pre>
/* Cache it for visitors */
$cacheable = array("video","videos","search","profile","api");
if(!isset($_SESSION['user_id']) && in_array(com(), $cacheable)) {
require_once( CNC.'/fullcache.php' );
FullCache::Encode($_SERVER['REQUEST_URI']);
FullCache::Live();
}
</pre>
<div class="msg-info">For now moving this settings to the admin panel has proven impossible without affecting server load.</div>
</div>
