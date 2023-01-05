<?php
if(isset($_GET['makedef'])) {
$lang = $_GET['makedef'];
update_option('def_lang',$lang);
$db->clean_cache();
$all_options = get_all_options();
}
if(isset($_POST['rtl'])) {
$rlang = $_POST['rtl'];
update_option('rtl_langs',$rlang);
$db->clean_cache();
$all_options = get_all_options();
}
if(isset($_GET['delete'])) {
$lang = $_GET['delete'];
if($lang) {
remove_file(ABSPATH.'/'.ADMINCP.'/cache/'.$lang);
}
}
if(isset($_GET['import'])) {
$lang = $_GET['import'];
$file = ABSPATH.'/'.ADMINCP.'/cache/'.$lang;
if($lang) {
if(is_readable($file)) {
$zip = file_get_contents($file);
//var_dump($zip);
$zip = json_decode($zip,true);
if(not_empty($zip)){
if(not_empty($zip["language-code"])) {	
$code = $zip["language-code"];
$language = $db->get_row( "SELECT count(*) as nr FROM  ".DB_PREFIX."languages WHERE `lang_code` like '$code%'" );
if ( $language) {
if ( $language->nr > 0 ) {
/* Language code already exists */	
$nx = $language->nr + 1;	
$code .= '-'.$nx;
}
}
} else {
$code = strtolower(substr($zip["language-name"], 0, 2 ));
}	
	
add_language($code ,$zip );
} else {
echo '<div class="msg-warning">'.$file.' was returned empty or read incorect from server.</div>';	
}
} else {
echo '<div class="msg-warning">'.$file.' was not found.</div>';	
}
}
}
if(isset($_GET['delete-lang'])) {
$lang = $_GET['delete-lang'];
if($lang) {
delete_language($lang);
echo '<div class="msg-info">Language #'.$lang.' was deleted.</div>';
} 
}
if(isset($_POST['checkRow'])) {
foreach ($_POST['checkRow'] as $del) {
if($del !== "en") {
delete_language($del);
} 
}
echo '<div class="msg-info">Languages #'.implode(',', $_POST['checkRow']).' deleted.</div>';
}
//Upload lang file
if(isset($_FILES['language']) && !empty($_FILES['language']['name'])){
$pos = strpos('lang-', $_FILES['language']['name']);
if ($pos !== false) {	
$newf = ABSPATH.'/'.ADMINCP.'/cache/'.$_FILES['language']['name'];
} else {
$newf = ABSPATH.'/'.ADMINCP.'/cache/lang-'.$_FILES['language']['name'];	
}
if (move_uploaded_file($_FILES['language']['tmp_name'], $newf)) {
	echo '<div class="msg-win">New language file uploaded.</div>';
	} else {
	echo '<div class="msg-warning">New language file upload failed.</div>';
	}
	
}

$langDir = ABSPATH.'/storage/langs/';
if(!is_writeable($langDir)) {
echo '<div class="msg-warning">'.$langDir.' is not writeable.</div>';
} 

$count = $db->get_row("Select count(lang_code) as nr from ".DB_PREFIX."languages");
$langs = $db->get_results("select * from ".DB_PREFIX."languages order by lang_code ASC ".this_limit()."");
if($langs) {

$ps = admin_url('langs').'&p=';

$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
$a->show_pages($ps);
include_once('setheader.php');
?>
<div class="row">

<div class="row-setts panel-body">
<form class="form-horizontal styled" action="<?php echo admin_url('langs');?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">
<h3>Languages management</h3>
<div class="cleafix full"></div>
<fieldset>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
<th> <div class="checkbox-custom checkbox-danger"> <input type="checkbox" name="checkRows" class="check-all" /> <label for="checkRows"></label> </div>  </th>
                                 
                                  <th>Language</th>								   
                                    <th>Code</th>
									 <th>File</th>
									<th>Default</th>
<th>Export</th>						<th>Edit</th>			
								  <th><button class="btn btn-sm btn-danger" type="submit"><?php echo _lang("Delete selected"); ?></button></th>
                              </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($langs as $language) { ?>
                              <tr>
                                  <td><input type="checkbox" name="checkRow[]" value="<?php echo $language->lang_code; ?>" class="styled" /></td>
                                 
                                  <td><strong><?php echo _html($language->lang_name);?></strong></td>
								 <td><strong><?php echo _html($language->lang_code); ?></strong></td>
								 <td>
								 <?php if(file_exists(ABSPATH.'/storage/langs/'.escape($language->lang_code).'.json')) {
									 echo escape($language->lang_code).'.json';
								 } else {
									 echo 'Missing';
								 }
								 ?>
								 
								 </td>
								 <td>
								 <?php if($language->lang_code == get_option('def_lang','en')) { ?>
								 <i class="icon-toggle-on greenText"></i>
								 <?php } else { ?>
								 <a href="<?php echo admin_url('langs');?>&makedef=<?php echo _html($language->lang_code); ?>" class="tipS" title="Make default"><i class="icon-toggle-off"></i></a>
								  <?php }  ?>
								 
								 </div>
								 <td><strong><a class="btn btn-sm btn-outline btn-success" href="<?php echo admin_url(); ?>api.php?action=exportlang&id=<?php echo _html($language->term_id); ?>">Download</a></strong>
								  </td>
							<td>								 
							<p><a class="btn btn-sm btn-outline btn-primary" href="<?php echo admin_url('edit-lang');?>&id=<?php echo $language->lang_code;?>"></i><?php echo _lang("Edit"); ?></a></p>
						    </td>
							<td>
							<p><a class="btn btn-sm btn-outline btn-danger confirm" href="<?php echo admin_url('langs');?>&p=<?php echo this_page();?>&delete-lang=<?php echo $language->lang_code;?>"><?php echo _lang("Delete"); ?></a></p>
							 </td>
                                 
                              </tr>
							  <?php } ?>
						</tbody>  
</table>
</div>						
</fieldset>					
</form>

<?php  $a->show_pages($ps); } ?>
<div class="row" style="padding: 20px 0">
<a class="btn btn-large btn-success pull-right" href="<?php echo admin_url('create-lang');?>" >Create new</a>
</div>
<div class="row">
<section class="panel panel-blue panel2x pad25">
<div class="panel-heading">
Uploaded languages
</div>
<div class="panel-body nopad">
<ul class="list-group">
<?php 
$clist = glob(ABSPATH.'/'.ADMINCP.'/cache/'."{lang-*.json*}", GLOB_BRACE);
		if($clist) {
        foreach ($clist as $filename)  {
		if($filename){	
		if (file_exists($filename)) {
    $mod = date ("F d Y H:i:s", filemtime($filename));
}
        $filename = explode('/',$filename);	
        $filename = end($filename);		
        echo '<li class="list-group-item"> 
		<div class="show no-margin pd-t-xs">
        <strong>'.$filename.'</strong>  <em>('.$mod.')</em> <div class="pull-right">
<a href="'.admin_url("langs").'&import='.$filename.'" class="btn btn-sm btn-success tipS" title="Import this language file" style="margin-right:10px; margin-left:0; display:inline">Create</a>
<a href="'.admin_url("langs").'&delete='.$filename.'" class="btn btn-sm btn-danger confirm" style="margin-right:0; margin-left:10px; display:inline">Remove</a>
</div>
</div></li>'; 
		}
		}
		}
?>
</ul>

Upload a language file
</div>
<div class="panel-body">
<form class="form-horizontal styled" action="<?php echo admin_url('langs');?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">
<div class="form-group form-material form-material-file">
<input type="text" class="form-control empty" readonly=""/>
<input type="file" id="language" name="language" class="styled" />
<label class="floating-label">Browse for .json language file...</label>

<span class="help-block" id="limit-text">Choose a .json language</span>
</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Start upload</button>	
</div>	
</form>
</div>
</section>

<h3>RTL Languages</h3>
<form class="form-horizontal styled" action="<?php echo admin_url('langs');?>&p=<?php echo this_page();?>" enctype="multipart/form-data" method="post">
 <div class="form-group form-material">
	<label class="control-label"><i class="icon-resize-full"></i>Languages that require RTL:</label>
	<div class="controls">
	<input type="text" id="tags" name="rtl" class="tags col-md-12" value="<?php echo get_option('rtl_langs','');?>">
	</div>
	<span class="help-block" id="limit-text"><code style="color:red">Code only</code>. Only add the language code. When switching to a language from this list the <?php echo tpl();?>styles/css/rtl.css will be loaded. <br> By default this is <code>empty</code> but it can be used to overwrite ltr rules. </span>
	</div>
	<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Save</button>	
</div>
</form>


</div>
</div>
</div>
