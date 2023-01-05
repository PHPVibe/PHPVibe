<?php if(isset($_GET['ac']) && $_GET['ac'] ="remove-logo"){
update_option('site-logo', '');
 $db->clean_cache();
}
if(isset($_POST['update_options_now'])){
foreach($_POST as $key=>$value)
{
  update_option($key, $value);
}
  echo '<div class="msg-info">Settings updated.</div>';

  $db->clean_cache();
}
$all_options = get_all_options();
?>

<div class="row row-setts">
<h3>Admin log (alog.txt)</h3>
<script>
$(document).ready(function() {
   $.getScript( "<?php echo admin_url();?>js/jquery.showmore.min.js" );       
   });      
</script>
<?php
$file_handle = fopen("alog.txt", "r");
while (!feof($file_handle)) {
   $line = fgets($file_handle);
   if (strpos($line, 'Removed') !== false) {
	echo '<br>';   
   }
   echo str_replace("scroll-items","showmore",$line)." <br> ";
}
fclose($file_handle);

?>
</div>
