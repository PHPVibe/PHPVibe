<?php $the_lang = (isset($_POST["lang-code"])) ? $_POST["lang-code"] : escape($_GET['id']);
$en_terms = $db->get_results("SELECT DISTINCT term from ".DB_PREFIX."langs limit 0,100000", ARRAY_A );
//$db->debug();
if($en_terms) {
$translated = lang_terms($the_lang);
//var_dump($translated);
if(isset($_POST["lang-code"])) {
$lang = $the_lang;
$ar = array();
$ar["language-name"] = $_POST["language-name"];
foreach ($_POST["term"] as $key=>$value) {
$ar[$key] = $value;
}
delete_language($lang);
add_language($lang ,$ar );
echo '<div class="msg-info">Language '.$lang.' was updated.</div>';
$db->clean_cache();
$translated = lang_terms($the_lang);
}
if (!is_writable(ABSPATH.'/storage/langs')) {
echo '<div class="msg-warning">Languages folder (/lib/langs) is not writeable. Langs can\'t be edited. </div>';
}
$lang_file = ABSPATH.'/storage/langs/'.$the_lang.'.json';
if (!file_exists($lang_file)) {
echo '<div class="msg-warning">Language '.$lang_file.' doesn\'t exist yet.</div>';	
}
?>
<div class="cleafix row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('edit-lang');?>&id=<?php echo $the_lang; ?>" enctype="multipart/form-data" method="post">
<div class="form-group form-material">
<label class="control-label"><i class="icon-globe"></i>Language code</label>
<div class="controls">
<input type="text" name="lang-code" class=" col-md-1" value="<?php echo $the_lang; ?>" /> 
</div>	
</div>
<div class="form-group form-material">
<label class="control-label"><i class="icon-font"></i>Language name</label>
<div class="controls">
<input type="text" name="language-name" class=" col-md-5" value="<?php echo $translated["language-name"]; ?>" /> 
</div>	
</div>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
                                 
                                  <th>Term</th>
                                  <th >Translation</th>
								  
                               </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($en_terms as $t) {
                             if($t["term"] !== "language-name") {
						  ?>
                              <tr>
                                   <td><?php echo stripslashes($t["term"]); ?></td>
                                  <td>
								  <?php if(isset($translated[$t["term"]])) { ?>
								  <input type="text" name="term[<?php echo stripslashes($t["term"]); ?>]" class="col-md-12" value="<?php echo $translated[$t["term"]]; ?>" /> 	
								  <?php } else { ?>
								   <input type="text" name="term[<?php echo stripslashes($t["term"]); ?>]" class="col-md-12" value="<?php echo $t["term"]; ?>" /> 	
								  <?php } ?>
								  </td>
                                                                
                              </tr>
							  <?php }} ?>
						</tbody>  
</table>
</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Update language</button>	
</div>	
</form>						
</div>						
<?php  } else {
echo '<div class="msg-warning">Missing the lang id.</div>';
}

?>
