<?php function youtubelinks($txt = '') {
return $txt.'
<li><a href="'.admin_url('yt').'"><div class="lidot"></div>Youtube Importer</a></li>
';
}
add_filter('importers_menu', 'youtubelinks')

?>