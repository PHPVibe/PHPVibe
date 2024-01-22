<?php
if (isset($_GET['delete-channel'])) {
    $db->get_row("DELETE from " . DB_PREFIX . "channels where cat_id ='" . intval($_GET['delete-channel']) . "' ");
    echo '<div class="msg-info">Channel #' . $_GET['delete-channel'] . ' deleted.</div>';
}
if (isset($_POST['checkRow'])) {
    foreach ($_POST['checkRow'] as $del) {
        $db->get_row("DELETE from " . DB_PREFIX . "channels where cat_id ='" . intval($del) . "' ");
    }
    echo '<div class="msg-info">Channels #' . implode(',', $_POST['checkRow']) . ' deleted.</div>';
}
$stype = '';

if (_get('type')) {
    $stype = 'where type =' . intval(_get('type'));
}

$count = $db->get_row("Select count(*) as nr from " . DB_PREFIX . "channels $stype");
$channels = $db->get_results("select " . DB_PREFIX . "channels.* from " . DB_PREFIX . "channels $stype order by cat_id DESC " . this_limit() . "");
?>

<h3>Categories
    <a class="btn btn-large btn-success mleft20" href="<?php echo admin_url('create-channel'); ?>"> Create new</a>
</h3>
<?php
if ($channels) {
    $names = $db->get_results("select cat_id,cat_name from " . DB_PREFIX . "channels order by cat_id DESC limit 0,10000000000000000");
    $titles = array();
    foreach ($names as $naray) {
        $titles[$naray->cat_id] = $naray->cat_name;
    }
    if (_get('type')) {
        $ps = admin_url('channels') . '&type=' . _get('type') . '&p=';
    } else {
        $ps = admin_url('channels') . '&p=';
    }
    $a = new pagination;
    $a->set_current(this_page());
    $a->set_first_page(true);
    $a->set_pages_items(7);
    $a->set_per_page(bpp());
    $a->set_values($count->nr);
//$a->show_pages($ps);
    ?>
    <form class="form-horizontal styled" action="<?php echo admin_url('channels'); ?>&p=<?php echo this_page(); ?>"
          enctype="multipart/form-data" method="post">

        <div class="cleafix full"></div>
        <fieldset>
            <div class="panel top10 multicheck">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="material-icons">
                            filter_list
                        </i>
                        <a href="<?php echo admin_url('channels'); ?>&type=1" class="right10"><small>Video</small></a>
                        <a href="<?php echo admin_url('channels'); ?>&type=2" class="right10"><small>Music</small></a>
                        <a href="<?php echo admin_url('channels'); ?>&type=3"><small>Images</small></a>
                    </h3>
                    <ul class="panel-actions">
                        <li>
                            <div class="checkbox-custom checkbox-danger"><input type="checkbox" name="checkRows"
                                                                                class="check-all"/> <label
                                        for="checkRows"></label></div>
                        </li>
                        <li>
                            <button class="btn btn-large btn-danger"
                                    type="submit"><?php echo _lang("Delete selected"); ?></button>
                        </li>
                    </ul>
                </div>
                <div class="panel-body" style="border-top: 1px solid #e4eaec; padding-top:15px;">
                    <div class="multilist">
                        <ul class="list-group">
                            <?php
                            $type = array();
                            $type[1] = "Videos";
                            $type[2] = "Music ";
                            $type[3] = "Images";
                            $sub = array();
                            $sub[0] = "Private";
                            $sub[1] = "Public";


                            foreach ($channels as $video) { ?>
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="inline-block img-hold">
                                            <div class="inline-block right20 img-checker">
                                 <span class="pull-left mg-t-xs mg-r-md top20">
								  <input type="checkbox" name="checkRow[]" value="<?php echo $video->cat_id; ?>"
                                         class="styled"/>
								  </span>
                                                <span class="pull-left mg-t-xs mg-r-md">
								  <img class="row-image <?php if (is_empty($video->picture)) {
                                      echo 'NoAvatar';
                                  } ?>" data-name="<?php echo stripslashes($video->cat_name); ?>"
                                       src="<?php if (not_empty($video->picture)) {
                                           echo thumb_fix($video->picture);
                                       } ?>">
								  </span>
                                            </div>
                                            <div class="inline-block right20 img-txt">
                                                <h4 class="mtop10"><?php echo stripslashes($video->cat_name); ?></h4>
                                                <p><?php echo not_empty($video->cat_desc) ? stripslashes($video->cat_desc) : 'empty'; ?></p>
                                                <p><small><?php echo $sub[$video->sub] ?>
                                                        / <?php echo $type[$video->type] ?></small></p>

                                                <?php if ($video->child_of) {
                                                    echo '<p>Child of: ' . $titles[$video->child_of] . '</p>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="btn-group btn-group-vertical pull-right">
                                            <a class="btn btn-sm btn-outline btn-default confirm"
                                               href="<?php echo admin_url('channels'); ?>&p=<?php echo this_page(); ?>&delete-channel=<?php echo $video->cat_id; ?>">
                                                <i class="material-icons mright10">
                                                    delete </i><?php echo _lang("Delete"); ?>
                                            </a>
                                            <a class="btn btn-sm btn-default"
                                               href="<?php echo admin_url('edit-channel'); ?>&id=<?php echo $video->cat_id; ?>">
                                                <i class="material-icons mright10">
                                                    edit </i><?php echo _lang("Edit"); ?>
                                            </a>
                                            <a class="btn btn-sm btn-outline btn-default" target="_blank" href="#">
                                                <i class="material-icons mright10">
                                                    link </i><?php echo _lang("View"); ?>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
        </fieldset>
    </form>
    <?php $a->show_pages($ps);
} else {
    echo '<div class="msg-note">Nothing here yet.</div>';
}

?>
