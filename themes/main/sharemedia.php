<?php the_sidebar(); 
      $active = com();
?>
<div id="default-content" class="share-media">
<div class="row">
<div class="block mtop20">
                    <ul class="nav nav-tabs nav-tabs-line">
					<?php if(_EmbedVideo()) { ?>
                      <li class="<?php aTab('share');?>"><?php echo '<a href="'.site_url().share.'">';?><i class="icon icon-youtube"></i><?php echo _lang('Share video'); ?></a></li>
                      <?php } ?>
					  <?php if(_UpVideo()) { ?>
                      <li class="<?php aTab('add');?>"><?php echo '<a href="'.site_url().add.'">';?><i class="icon icon-upload"></i><?php echo _lang('Upload video'); ?></a></li>
					  <?php } ?>
					  <?php if(_UpMusic() || _EmbedMusic()) { ?>
                      <li class="<?php aTab('addmusic');?>"><?php echo '<a href="'.site_url().upmusic.'">';?><i class="icon icon-volume-up"></i><?php echo _lang('Upload music'); ?></a></li>
					  <?php } ?>
					<?php if(_UpImage()) { ?>
                      <li class="<?php aTab('addimage');?>"><?php echo '<a href="'.site_url().upimage.'">';?><i class="icon icon-camera"></i><?php echo _lang('Upload a picture'); ?></a></li>
					  <?php } ?>
					</ul>
</div>
<div class="block">
<?php echo default_content(); ?>

</div>
</div>
