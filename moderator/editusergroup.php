<?php //Usergroups
$term = toDb($_REQUEST['id']);
if(isset($_POST['add_group'])) {
$db->query("UPDATE ".DB_PREFIX."users_groups SET `name` = '".$db->escape($_POST['group-name'])."', `group_creative` = '".$db->escape($_POST['group-creative'])."' where id = '".$db->escape($_POST['id_group'])."'");
echo '<div class="msg-win">Usergroup "'.$_POST['group-name'].'" was updated.</div>';
echo '<div class="full msg-content"><a class="btn btn-default" href="'.admin_url('editusergroup').'&id='.$db->escape($_POST['id_group']).'">Continue editing this group</a></div>';
echo '<div class="full msg-content"><a class="btn btn-primary" href="'.admin_url('usergroups').'">Go to Usergroups</a></div>';
}

$group = $db->get_row("Select * from ".DB_PREFIX."users_groups where id = '".$term."' ");


if(isset($group->id)){

$ps = admin_url('editusergroup').'&id='.$term;

?>
<h3>Edit group</h3>
<form class="form-horizontal styled" action="<?php echo $ps;?><?php echo this_page();?>" enctype="multipart/form-data" method="post">

<div class="cleafix full"></div>
<fieldset>		
<input type="hidden" name="add_group" class="hide" value="1" />	
<input type="hidden" name="id_group" class="hide" value="<?php echo $group->id;?>" />	
<div class="form-group form-material">
<label class="control-label"><i class="material-icons">&#xE7F0;</i> Group's name</label>
<div class="controls">
<input type="text" name="group-name" class=" col-md-6" value="<?php echo addslashes($group->name);?>" /> 
<span class="help-block" id="limit-text">New group's name.</span>						
</div>	
</div>	
<div class="form-group form-material">
	<label class="control-label"><i class="material-icons">&#xE8E8;</i>Creative</label>
	<div class="controls">
<input type="text" name="group-creative" class=" col-md-6" value="<?php echo htmlentities($group->group_creative);?>" /> 
<span class="help-block" id="limit-text">Html for badge or other creative! You can use <a href="https://material.io/icons/" target="_blank">Material icons</a>. Exemple: <i class="material-icons">&#xE86C;</i> is <code> &#x3C;i class=&#x22;material-icons&#x22;&#x3E;&#x26;#xE86C;&#x3C;/i&#x3E;</code></span>	
					
</div>	
	</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Update group</button>	
</div>		
</fieldset>					
</form>
<?php  } ?>
