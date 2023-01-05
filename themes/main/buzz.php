<?php echo the_sidebar(); ?>
<ul class="nav nav-tabs nav-tabs-line mtop20">
 <li class="<?php if(!_get('myfeed')) { echo "active";}?>" role="presentation"><a href="<?php echo site_url().buzz; ?>"> <i class="material-icons">&#xe064;</i> <?php echo _lang('Global'); ?> </li></a>
 <?php if(!is_user()) { ?>
  <li class="<?php if(_get('myfeed')) { echo "active";}?>" role="presentation"><a href="javascript:showLogin()"> <i class="material-icons">&#xE639;</i> <?php echo _lang('Channels I follow'); ?></a></li>
<?php } else { ?>
 <li class="<?php if(_get('myfeed')) { echo "active";}?>" role="presentation"><a href="<?php echo site_url().buzz."?myfeed=1"; ?>"> <i class="material-icons">&#xE639;</i> <?php echo _lang('Channels I follow'); ?></a></li>
 <?php } ?>
 </ul>

<div class="pad-holder col-md-12">
<?php 
if(_get('myfeed')) {
    $count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."activity where user in (select uid from ".DB_PREFIX."users_friends where fid ='".user_id()."')");
    $vq = "Select ".DB_PREFIX."activity.*, ".DB_PREFIX."users.avatar,".DB_PREFIX."users.id as pid, ".DB_PREFIX."users.name from ".DB_PREFIX."activity left join ".DB_PREFIX."users on ".DB_PREFIX."activity.user=".DB_PREFIX."users.id where ".DB_PREFIX."activity.user in (select uid from ".DB_PREFIX."users_friends where fid ='".user_id()."') ORDER BY ".DB_PREFIX."activity.id DESC ".this_limit();
} else {
    $count = $db->get_row("Select count(*) as nr from ".DB_PREFIX."activity");
    $vq = "Select ".DB_PREFIX."activity.*, ".DB_PREFIX."users.avatar,".DB_PREFIX."users.id as pid, ".DB_PREFIX."users.name from ".DB_PREFIX."activity left join ".DB_PREFIX."users on ".DB_PREFIX."activity.user=".DB_PREFIX."users.id ORDER BY ".DB_PREFIX."activity.id DESC ".this_limit();
}
if($count->nr > 0) {
    include_once(TPL.'/layouts/global_activity.php');
    if(_get('myfeed')) {
        $pagestructure = canonical().'?myfeed=1&p=';
    } else {	
        $pagestructure = canonical().'?p=';
    }
    $a = new pagination;	
    $a->set_current(this_page());
    $a->set_first_page(true);
    $a->set_pages_items(7);
    $a->set_per_page(bpp());
    $a->set_values($count->nr);
    $a->show_pages($pagestructure);
} else {
    echo '<p class="empty-content">'._lang('Nothing here so far.').'</p>';	
}
?>
</div>
