<?php the_sidebar(); ?>
<div class="row">
    <div class="col-md-8 blog-holder col-xs-12 panel isBoxed">
        <?php do_action('blogpost-start');

              echo '<div class="row"><h1 class="blogH">'._html($_post->title).'</h1>';
              echo '<p><span class="badge badge-primary badge-radius">'.time_ago($_post->date).'</span></p></div>';

              $txt = '';
              if($_post->pic) {
                  $txt .= '<div class="blog-image">
<div class="text-center ">
<img src="'.thumb_fix($_post->pic).'" />
</div>
</div>';
              }
              $txt .= '<div class="blog-text mtop20">';
              $txt .= closehtmltags(_html($_post->content));
              $txt .= '</div>';
              echo  $txt;
        ?>
        <div id="jsshare" data-url="<?php echo canonical(); ?>" data-title="<?php echo _cut($_post->title, 40); ?>"></div>

        <?php do_action('blogpost-end');
              echo comments('art'.token_id());
        ?>
    </div>
    <?php include_once('blog-sidebar.php'); ?>
</div>
