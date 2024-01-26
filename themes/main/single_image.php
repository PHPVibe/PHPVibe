<?php the_sidebar();
$is_liked = '';
if (is_user()) {
    //Liked?
    $check = $db->get_row("SELECT count(*) as nr FROM " . DB_PREFIX . "hearts WHERE vid = '" . $image->id . "' AND uid ='" . user_id() . "'");
    if ($check && ($check->nr > 0)) {
        $is_liked = 'done-like';
    }
}
do_action('pre-image'); ?>
    <script type="text/javascript">
        $(document).ready(function () {
            DOtrackviewIMG(<?php echo $image->id; ?>);
        });
    </script>
    <div class="container">
        <div class="row">
            <a class="rm_next tipS hide" data-placement="right" href="#"></a>
            <a class="rm_prev tipS hide" data-placement="left" href="#"></a>
            <div class="col-md-6 image-holder">
                <div class="the-image block full">
                    <?php do_action('before-image');
                    echo $embedimage;
                    do_action('after-image');
                    ?>

                </div>

            </div>

            <div class="col-md-6 img-inner isBoxed">
                <div class="playlistvibe">
                    <div class="block full img-owner">
                        <?php echo '<a href="' . profile_url($image->user_id, $image->owner) . '" class="owner-avatar"><img data-name="' . addslashes($image->owner) . '" class="owner-avatar img-circle" src="' . thumb_fix($image->avatar, true, 35, 35) . '"/>
		<span class="owner-name text-action">@' . $image->owner . '</span>
		</a>'; ?>
                        <?php subscribe_box($image->user_id); ?>
                    </div>
                    <?php do_action('before-image-title'); ?>
                    <div class="cute">
                        <h1>
                            <a href="<?php echo channel_url($image->category, $image->channel_name); ?>"
                               title="<?php echo _html($image->channel_name); ?>"><?php echo _html($image->channel_name); ?>
                                : </a>

                            <?php echo _html($image->title); ?>
                        </h1>
                        <p> <?php echo time_ago($image->date); ?></p>

                    </div>
                    <?php do_action('after-image-title'); ?>

                </div>

                <?php do_action('before-social-box'); ?>
                <div class="user-media-actions">
                    <div class="interaction-icons">
                        <div class="aaa">
                            <a id="i-like-it" class="<?php echo $is_liked; ?>"
                               href="javascript:iHeartThis(<?php echo $image->id; ?>)">
                                <i class="material-icons">&#xE87D;</i>
                                <span><?php echo $image->liked; ?></span>
                            </a>
                        </div>
                        <div class="aaa">
                            <a href="javascript:void()">
                                <i class="material-icons">&#xE417;</i>
                                <span><?php echo $image->views; ?></span>
                            </a>
                        </div>
                        <div class="aaa">
                            <a data-toggle="dropdown" data-target="#" class="dropdown-toogle dropdown-left"
                               title="<?php echo _lang('Add to album'); ?>">
                                <i class="material-icons">&#xE03B;</i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                <?php $albums = $cachedb->get_results("SELECT id,title from " . DB_PREFIX . "playlists where owner='" . user_id() . "' and picture not in ('[likes]', '[history]', '[later]') and ptype > 1 limit 0,100");
                                if ($albums) {
                                    foreach ($albums as $pl) {
                                        ?>
                                        <li id="PAdd-<?php echo $pl->id; ?>"><a
                                                    href="javascript:Padd(<?php echo $image->id; ?>,<?php echo $pl->id; ?>)">
                                                <i class="material-icons">&#xE019;</i>
                                                <?php echo _html($pl->title); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <li class="divider" role="presentation"></li>
                                <li>
                                    <a href="<?php echo site_url() . me; ?>?sk=new-album">
                                        <i class="material-icons">&#xE3CD;</i>
                                        <?php echo _lang("Create album"); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <br style="clear:both">
                </div>
                <ul class="list-unstyled media-txt">
                    <?php if ($image->tags) { ?>
                        <li>
                            <?php echo pretty_imgtags($image->tags, 'right20', '
                        <i class="icon-hashtag right10"></i>', ''); ?>
                        </li>
                    <?php } ?>
                    <li>
                        <?php do_action('before-description-box'); ?>
                        <div id="media-description" data-small="<?php echo _lang("show more"); ?>"
                             data-big=" <?php echo _lang("show less"); ?>">
                            <?php echo makeLn(_html($image->description)); ?>
                        </div>
                    </li>
                </ul>
                <div id="jsshare" data-url="<?php echo $canonical; ?>"
                     data-title="<?php echo _cut($image->title, 40); ?>"></div>
                <?php $collections = $db->get_results("select * FROM " . DB_PREFIX . "playlists WHERE ptype='2' and id in (Select distinct(playlist) from " . DB_PREFIX . "playlist_data where video_id='" . $image->id . "' ) ");
                if ($collections) {
                    echo '<h5 class="collin">' . _lang("Collected in") . '</h5>
				<ul class="list-group list-group-full">';
                    foreach ($collections as $pl) {
                        $title = _html(_cut($pl->title, 170));
                        $full_title = _html(str_replace("\"", "", $pl->title));
                        $url = playlist_url($pl->id, $pl->title);
                        ?>

                        <li class="list-group-item colist">
                            <div class="media">
                                <div class="media-left">
                                    <a class="avatar" href="<?php echo $url; ?>">
                                        <img class="img-responsive img-circle"
                                             src="<?php echo thumb_fix($pl->picture, true, 45, 45); ?>"
                                             data-name="<?php echo $full_title; ?>"></a>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading">
                                        <a href="<?php echo $url; ?>">
                                            <?php echo $full_title; ?>
                                        </a>
                                    </h4>
                                    <p><?php echo _html(_cut($pl->description, 170)); ?></p>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                    </ul>
                <?php } ?>
                <?php echo _ad('0', 'top-of-comments'); ?>
                <?php do_action('before-comments'); ?>
                <?php echo comments('img-' . $image->id); ?>
                <?php do_action('after-comments'); ?>
            </div>
        </div>


        <?php $blockclass = 'noInf';
        layout('layouts/relatedimages'); ?>
    </div>
    <script>
        $(document).ready(function () {
            if ($(".image-item").length) {
                var NILink = $(".image-item:first").find("a.clip-link").attr("href");
                var NITitle = $(".image-item:first").find("a.clip-link").attr("title");
                $(".rm_next").attr("title", NITitle).attr("href", NILink).data('title', NITitle).removeClass('hide');
            }
            <?php
            if (isset($_SESSION['lastImg'])) {
                $lastImg = maybe_unserialize($_SESSION['lastImg']);
                if (not_empty($lastImg) && isset($lastImg['url'])) {
                    echo '$(".rm_prev").attr("title","' . $lastImg['title'] . '").attr("href","' . site_url() . urldecode($lastImg['url']) . '");
$(".rm_prev").data("title","' . $lastImg['title'] . '").removeClass("hide");';
                }
            }
            ?>
        });
    </script>

<?php //Set previous
$this_img = array('url' => urlencode(str_replace(site_url(), "", $canonical)), 'title' => $image->title);
$_SESSION['lastImg'] = maybe_serialize($this_img);
if (isset($_SESSION['vseenimg'])) {
    $_SESSION['vseenimg'] .= ',' . $image->id;
} else {
    $_SESSION['vseenimg'] = $image->id;
}
?>