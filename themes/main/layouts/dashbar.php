   <div id="DashSidebar" class="col-md-10 col-xs-12"> <?php   do_action('dashSide-top'); ?>
<div class="pull-left">
   <a class="btn btn-lg btn-primary mbot20 dropdown-toggle"  data-toggle="dropdown" href="#" aria-expanded="false"
	data-animation="scale-up" role="button"><i class="material-icons">sort</i> Navigate</a>

	<ul class="dropdown-menu dropdown-left">
		<li><a href="<?php echo site_url(); ?>dashboard/"><i class="icon material-icons">home</i><?php echo _lang("Overview");?></a></li>
		<li><a href="<?php echo site_url().me; ?>"><i class="icon material-icons">video_library</i><?php echo _lang("Videos");?></a></li>
		<li><a href="<?php echo site_url().me; ?>?sk=music"><i class="icon material-icons">library_music</i><?php echo _lang("Music");?></a></li>
		<li><a href="<?php echo site_url().me; ?>?sk=images"><i class="icon material-icons">photo_library</i><?php echo _lang("Images");?></a></li>
		<li><a href="<?php echo site_url(); ?>dashboard/?sk=activity"><i class="icon material-icons">&#xE7F7;</i><?php echo _lang("Activities");?></a></li>
	    <li><a href="<?php echo site_url().me; ?>?sk=subscriptions"><i class="icon material-icons">&#xE8A1;</i><?php echo _lang("Payments");?></a></li>
		<li><a href="<?php echo site_url().me; ?>?sk=playlists"><i class="icon material-icons">&#xE431;</i><?php echo _lang("Playlists");?></a></li>
		<li><a href="<?php echo site_url().me; ?>?sk=albums"><i class="icon material-icons">&#xe8a7;</i><?php echo _lang("Albums");?></a></li>
		<li><a href="<?php echo site_url().me; ?>?sk=hearts"><i class="icon material-icons">&#xe87d;</i><?php echo _lang("Loved");?></a></li>
		<li class="drop-footer"><a href="<?php echo site_url(); ?>dashboard/?sk=edit"><i class="icon material-icons">tune</i><?php echo _lang("Channel Settings");?></a></li>

	</ul>
</div>
 <?php do_action('dashSide-bottom'); ?> </span> 
 </div>  