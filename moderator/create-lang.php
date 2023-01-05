<?php /* We build it on top of Enghlish */
$en_terms = $db->get_results("SELECT DISTINCT term from ".DB_PREFIX."langs limit 0,100000", ARRAY_A );
if(isset($_POST["this-langShortcode"])) {
$orlang = $_POST["this-langShortcode"];
$ar = array();
$ar["language-name"] = $_POST["this-language-name"];
foreach ($_POST["term"] as $key=>$value) {
$ar[$key] = $value;
}
add_language($orlang ,$ar );
echo '<div class="msg-info">Language '.$ar["language-name"].' was created.</div>';
unset($ar);
}

if (!is_writable(ABSPATH.'/storage/langs')) {
echo '<div class="msg-warning">Languages folder (/storage/langs) is not writeable. Langs can\'t be stored. </div>';
}
?>
<div class="cleafix row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('create-lang');?>" enctype="multipart/form-data" method="post">
<div class="form-group form-material">
<label class="control-label"><i class="icon-font"></i>Language name</label>
<div class="controls">
<input type="text" name="this-language-name" class=" col-md-5" value="" /> 
<span class="help-block" id="limit-text">Ex: Italian, Romanian, Swedish</span>						
</div>	
</div>
<div class="form-group form-material">
<label class="control-label"><i class="icon-globe"></i>Language code</label>
<div class="controls">
<input type="text" name="this-langShortcode" class=" col-md-1" value="" /> 
<span class="help-block" id="limit-text">Ex: it, es, fr, ro, se. See <a href="http://www.worldatlas.com/aatlas/ctycodes.htm" target="_blank">country codes (2 letters)</a></span>						
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
								  <input type="text" name="term[<?php echo stripslashes($t["term"]); ?>]" class="col-md-12" value="<?php echo stripslashes($t["term"]); ?>" /> 	
								  </td>
                                                                
                              </tr>
							  <?php }} ?>
						</tbody>  
</table>
</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Create language</button>	
</div>	
</form>						
</div>						
<?php ?>
