<?php function _cleanup_header_comment( $str ) {
	return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
}
function get_plugin_data( $plugin_file, $markup = true, $translate = true ) {

	$default_headers = array(
		'Name' => 'Plugin Name',
		'PluginURI' => 'Plugin URI',
		'Version' => 'Version',
		'Description' => 'Description',
		'Author' => 'Author',
		'AuthorURI' => 'Author URI',
		'TextDomain' => 'Text Domain',
		'DomainPath' => 'Domain Path',
		'Network' => 'Network',
		// Site Wide Only is deprecated in favor of Network.
		'_sitewide' => 'Site Wide Only',
	);

	$plugin_data = get_file_data( $plugin_file, $default_headers, 'plugin' );

	

		$plugin_data['Title']      = $plugin_data['Name'];
		$plugin_data['AuthorName'] = $plugin_data['Author'];
		$plugin_data['PluginURI']   = escape( $plugin_data['PluginURI'] );
	    $plugin_data['AuthorURI']   = escape( $plugin_data['AuthorURI'] );
		$plugin_data['Description'] = _html( $plugin_data['Description']);
	$plugin_data['Version']     = _html( $plugin_data['Version'] );
	
	return $plugin_data;
}
function get_file_data( $file, $default_headers, $context = '' ) {
	// We don't need to write to the file, so just open for reading.
	$fp = fopen( $file, 'r' );

	// Pull only the first 8kiB of the file in.
	$file_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );

	// Make sure we catch CR-only line endings.
	$file_data = str_replace( "\r", "\n", $file_data );


	$all_headers = $default_headers;
	

	foreach ( $all_headers as $field => $regex ) {
		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] )
			$all_headers[ $field ] = _cleanup_header_comment( $match[1] );
		else
			$all_headers[ $field ] = '';
	}

	return $all_headers;
}
function get_folder_name($path){
$path = str_replace(array(ABSPATH."/plugins/","/plugin.php"),"",$path );	
return $path;
}
?>
<div class="row">
<?php
if(_get('activate')) {
$list = get_option('activePlugins',null);
if(is_null($list)) {
$list = _get('activate').',';	
} else {
$lar = explode(",",$list);
$lar[] = _get('activate');
$list = implode(",",array_unique($lar));
}
update_option('activePlugins',$list);
echo '<div class="msg-win">Plugin <strong>'._get('activate').'</strong> is now active.</div>';
$db->clean_cache();
}
if(_get('disable')) {
$list = get_option('activePlugins',null);
$lar = explode(",",$list);
$remove = array(_get('disable'));
$list = array_diff($lar,$remove);
$list = implode(",",array_unique($list));

update_option('activePlugins',$list);
echo '<div class="msg-info">Plugin <strong>'._get('disable').'</strong> is now disabled.</div>';
$db->clean_cache();
}	
/* Refresh all global site options */
$all_options = get_all_options();
$active =  explode(",",get_option('activePlugins'));
$plugDir = ABSPATH."/plugins";
include_once('setheader.php');
?>
<div class="row">

<div class="row-setts panel-body">
<h3>Plugins</h3>
<div class="top10 multicheck">
<div class="row">
	<ul class="list-group plugins-list">					  
<?php $checkplg = array();
foreach(glob($plugDir.'/*/plugin.php') as $plugin)
	{
		
		
		if(file_exists($plugin)) {
			$app = get_plugin_data($plugin);
			$plugfolder = get_folder_name($plugin);
			$checkplg[] = $plugfolder;
			if(!in_array($plugfolder,$active)) {
			echo "<li class=\"list-group-item\">";
			} else {
			echo "<li class=\"list-group-item plugin-activated\">";	
			}
			
			echo "<div class=\"row\">
			<div class=\"inline-block img-hold\">";
			echo "<div class=\"inline-block right20 img-txt\">
			<h4>".$app["Name"];
			/*
			if(!in_array(get_folder_name($plugin),$active)) {
			echo '<span class="badge badge-radius badge-warning mleft20">Disabled</span>';
			} else {
			echo '<span class="badge badge-radius badge-success mleft20">Active</span>';	
			}
			*/
			echo "</h4>";
			
			echo '<div class="img-det-text">
			<i class="material-icons"> update </i> v'.$app["Version"].'   
			<a href="'.$app["AuthorURI"].'" target="_blank"> <i class="material-icons"> supervised_user_circle </i> '.$app["Author"].'</a>
			</div>';
echo "<small>".$app["Description"]."</small>";
			echo "</div>
			</div>";
			echo '<div class="btn-group btn-group-vertical pull-right">';
			if(!in_array($plugfolder,$active)) {
			echo '<a class="btn btn-sm btn-success pull-right" href="'.admin_url('plugins').'&activate='.$plugfolder.'">Activate </a>';
			} else {
			echo '<a class="btn btn-sm btn-danger pull-right" href="'.admin_url('plugins').'&disable='.$plugfolder.'">Disable</a>';	
			}	
			echo "</div>";
			echo "</div>";
			echo "</li>";
			
		}
		
		
	}
?>
</ul>
</div>  
</div>

<div class="row plugins plugins-small">
<h3>Available at PHPVibe.com</h3>
<ul class="list-group plugins-list">	
<?php 
$upd = json_decode(getpb("https://labs.phpvibe.com/server/api.php"), true);
if(not_empty($upd)) {
	foreach ($upd as $recent) {
		if(!in_array($recent["compatible"],$checkplg)) {
		?>
		
			<li class="list-group-item">
			<div class="pull-left mg-t-xs mg-r-md plugin-imgwrap">
			<img src="<?php echo $recent["image"]; ?>" class="plugin-image " alt="">
			</div>
			<div class="show no-margin pd-t-xs inline-block"> 
			<a href="<?php echo $recent["link"]; ?>&ref=<?php echo _domain(site_url());?>" target="_blank"><h4><?php echo _html($recent["title"]); ?></h4></a>
			</div>
			<a class="btn btn-sm btn-primary pull-right inline-block" target="_blank" href="<?php echo $recent["link"]; ?>&ref=<?php echo _domain(site_url());?>">Download </a>
			</li>
<?php
		}
	}
}

?>
</ul>
</div>

</div>						
</div>	