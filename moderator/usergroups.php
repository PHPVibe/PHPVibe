<?php //Usergroups
if(isset($_POST['add_group'])) {
$db->query("INSERT INTO ".DB_PREFIX."users_groups (`name`,`group_creative`) VALUES ('".$db->escape($_POST['group-name'])."','".$db->escape($_POST['group-creative'])."')
");
echo '<div class="msg-win">Usergroup "'.$_POST['group-name'].'" was created.</div>';
}
if(isset($_GET['delete'])) {
if(intval($_GET['delete']) > 4) {
$db->get_row("DELETE from ".DB_PREFIX."users_groups where id ='".intval($_GET['delete'])."'");
echo '<div class="msg-win">Usergroup #'.intval($_GET['delete']).' was removed.</div>';
} else {
echo '<div class="msg-warning">Usergroup #'.intval($_GET['delete']).' was not removed : Reason protected group</div>';
}
} 
$count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."users_groups");
$users = $db->get_results("select * from ".DB_PREFIX."users_groups order by id ASC ".this_limit()."");

if($users) {
if(isset($term)){
$ps = admin_url('usergroups').'&term='.$term.'&p=';
}else if(isset($_GET['sort'])) {
$ps = admin_url('usergroups').'&sort='.$_GET['sort'].'&p=';
} else {
$ps = admin_url('usergroups').'&p=';
}
$a = new pagination;	
$a->set_current(this_page());
$a->set_first_page(true);
$a->set_pages_items(7);
$a->set_per_page(bpp());
$a->set_values($count->nr);
$a->show_pages($ps);
?>

<form class="form-horizontal styled" action="<?php echo $ps;?><?php echo this_page();?>" enctype="multipart/form-data" method="post">

<div class="cleafix full"></div>
<div class="cleafix full"></div>
<fieldset>
<div class="table-overflow top10">
                        <table class="table table-bordered table-checks">
                          <thead>
                              <tr>
                                  <th><?php echo _lang("Id"); ?></th>
                                  <th><?php echo _lang("Group"); ?></th>
                                  <th><?php echo _lang("Role"); ?></th>
								  <th><?php echo _lang("Creative"); ?></th>
                                <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>
						  <?php foreach ($users as $video) { ?>
                              <tr>
                                <td><?php echo $video->id; ?> </td>
                                  <td><?php echo _html($video->name); ?> </td>
								  <td><?php if($video->id == 1) {
								  echo 'Administator';
								  } elseif($video->id == 2) {
									  echo 'Moderator';
								  }
								  ?></td>
								  <td>
								  <?php echo _html($video->group_creative);?>
								  </td>
								  <td>
								  <p>
								  <a class="" style="margin-right:30px;" href="<?php echo admin_url('editusergroup');?>&id=<?php echo $video->id; ?>">
								  <i class="material-icons">&#xE254;</i>
								  </a>
								  <?php if(($video->id <> 4) && ($video->id > 2) ) { ?>
								  <a class="confirm" href="<?php echo admin_url('usergroups');?>&p=<?php echo this_page();?>&delete=<?php echo $video->id;?>"><i class="material-icons">&#xE872;</i></a></p>
						          <?php } ?>
								  </td>
                              </tr>
							  <?php } ?>
						</tbody>  
</table>
</div>	
<h3>Add group</h3>
<form class="form-horizontal styled" action="<?php echo $ps;?><?php echo this_page();?>" enctype="multipart/form-data" method="post">

<div class="cleafix full"></div>
<fieldset>		
<input type="hidden" name="add_group" class="hide" value="1" />	
<div class="form-group form-material">
<label class="control-label"><i class="material-icons">&#xE7F0;</i> Group's name</label>
<div class="controls">
<input type="text" name="group-name" class=" col-md-6" value="" /> 
<span class="help-block" id="limit-text">New group's name.</span>						
</div>	
</div>	
<div class="form-group form-material">
	<label class="control-label"><i class="material-icons">&#xE8E8;</i>Creative</label>
	<div class="controls">
<input type="text" name="group-creative" class=" col-md-6" value="" /> 
<span class="help-block" id="limit-text">Html for badge or other creative! You can use <a href="https://material.io/icons/" target="_blank">Material icons</a>. Exemple: <i class="material-icons">&#xE86C;</i> is <code> &#x3C;i class=&#x22;material-icons&#x22;&#x3E;&#x26;#xE86C;&#x3C;/i&#x3E;</code></span>	
					
</div>	
	</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Add group</button>	
</div>		
</fieldset>					
</form>
<?php  $a->show_pages($ps); } else {
$db->query("INSERT INTO `".DB_PREFIX."users_groups` (`id`, `name`, `admin`, `default_value`, `access_level`) VALUES
(1, 'Administrators', 1, 0, 3),
(4, 'Members', 0, 1, 1),
(3, 'Author', 0, 2, 2),
(2, 'Moderators', 0, 2, 2);");
 echo '<div class="msg-win">Small error. Usergroups missing. Default ones where installed. Please refresh page.</div>';
 } ?>
