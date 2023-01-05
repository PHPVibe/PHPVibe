<?php the_sidebar(); ?>
<div class="row">
    <div class="isBoxed col-md-8 col-xs-12">
        <?php
        $posts = $db->get_results($vq);
        if ($posts) {
        ?>
        <ul class="blog-list blog-holder list-unstyled">
            <?php foreach ($posts as $ar) {
                      echo '
<li>
<div class="blog-post block block-inline">
<header>
<a class="text-primary" href="'.article_url($ar->pid, $ar->title).'" title="'._html($ar->title).'">
<h3 class="blogH">'._html($ar->title).'</h3>
</a>
</header>';
                      echo '<p><span class="badge badge-primary badge-radius">'.time_ago($ar->date).'</span></p>';

                      if(isset($ar->pic) && !nullval($ar->pic)) {
                          echo '<div class="blog-image">
<div class="text-center ">
<a href="'.article_url($ar->pid, $ar->title).'" title="'._html($ar->title).'"><img src="'.thumb_fix($ar->pic).'"></a>
</div>
</div>';
                      }
                      echo '	<div class="blog-text">
							'.closehtmltags(_html(_cut(strip_tags($ar->content),560))).'...

						</div>
						<div class="blog-more">
						<a  href="'.article_url($ar->pid, $ar->title).'" class="btn btn-default" title="'._html($ar->title).'">'._lang("Read More").'</a>
						</div>
					</div>

</li>';

                  }
            ?>


        </ul>


        <?php
        } else {
            echo _lang("Sorry but there are no results");
        }

        $a->show_pages($pagestructure);

        ?>
    </div>
    <?php include_once('blog-sidebar.php'); ?>
</div>
