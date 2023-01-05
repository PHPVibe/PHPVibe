<div class="col-md-3 col-xs-12 left20 blog-sidebar">
    <div class="blog-holder">
        <?php echo _ad('0','blog-sidebar-top');
              $blogcats = $db->get_results("select * from ".DB_PREFIX."postcats order by cat_id DESC limit 0,10000");;
              if($blogcats) {
                  echo '<h4>'._lang("Blog categories").'</h4>
<div class="row"><ul class="sidebar-nav">';
                  foreach ($blogcats as $popular) {
                      echo '
<li>
<a title="'.$popular->cat_name.'" href="'.bc_url($popular->cat_id , $popular->cat_name).'">
<span class="iconed"><i class="material-icons">folder</i></span>'._html($popular->cat_name).'</a>';
                      echo '</li>';
                  }
                  echo '</ul>
</div>';
              }
              $articles =  $db->get_results("select title,pid,pic from ".DB_PREFIX."posts ORDER BY pid DESC limit 0,10");
              /* The pages lists */
              if($articles) {
                  echo '<h4>'._lang("Recent articles").'</h4>
		 <div class="sidebar-nav row"><ul>';
                  foreach ($articles as $art) {
                      echo '<li class="row"><a href="'.article_url($art->pid, $art->title).'" title="'._html($art->title).'">'._cut(_html($art->title),60).'</a></li>';
                  }
                  echo '</ul></div>';
              }
              echo _ad('0','blog-sidebar-bottom');
        ?>
    </div>
</div>
