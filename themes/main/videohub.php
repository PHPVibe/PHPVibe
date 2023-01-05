<?php the_sidebar();
      //Add sorter 
      if(isset($sortop) && $sortop) {
          /* Most liked , Most viewed time sorting */
          $st = '
<div class="inline-block pull-right relative">
       <a data-toggle="dropdown" class="btn btn-default btn-outline dropdown-toogle"> <i class="icon icon-calendar"></i> <i class="icon icon-angle-down"></i> </a>
			<ul class="dropdown-menu dropdown-menu-right bullet">
			<li title="'._lang("This Week").'"><a href="'.hub_url(token()).'?sort=w"><i class="icon icon-circle-thin"></i>'._lang("This Week").'</a></li>
			<li title="'._lang("This Month").'"><a href="'.hub_url(token()).'?sort=m"><i class="icon icon-circle-thin"></i>'._lang("This Month").'</a></li>
			<li title="'._lang("This Year").'"><a href="'.hub_url(token()).'?sort=y"><i class="icon icon-circle-thin"></i>'._lang("This Year").'</a></li>
			<li class="divider" role="presentation"></li>
			<li title="'._lang("This Week").'"><a href="'.hub_url(token()).'"><i class="icon icon-circle-thin"></i>'._lang("All").'</a></li>
		</ul>
		</div>
';
      }

?>
<div class="row main-holder">

<ul class="nav nav-tabs nav-tabs-line mtop20">
    <li class="<?php aTab(browse);?>" role="presentation"><a href="<?php echo hub_url('browse'); ?>"> <i class="material-icons">&#xE8D0;</i> <?php echo _lang('Fresh'); ?> </li></a>
    <li class="<?php aTab(mostviewed);?>" role="presentation"><a href="<?php echo hub_url(mostviewed); ?>"> <i class="material-icons">&#xE01D;</i> <?php echo _lang('Most Viewed'); ?></a></li>
    <li class="<?php aTab(mostliked);?>" role="presentation"><a href="<?php echo hub_url(mostliked); ?>"> <i class="material-icons">&#xE8DD;</i> <?php echo _lang('Enjoyed'); ?></a></li>
    <li class="<?php aTab(mostcom);?>" role="presentation"><a href="<?php echo hub_url(mostcom); ?>"> <i class="material-icons">&#xE253;</i> <?php echo _lang('Discussed'); ?></a></li>
    <li class="<?php aTab(promoted);?>" role="presentation"><a href="<?php echo hub_url(promoted); ?>"> <i class="material-icons">&#xE39F;</i> <?php echo _lang('Staff Picks'); ?></a></li>
	</ul>
<div id="videolist-content">
<?php echo _ad('0','video-hub-top');
      include_once(TPL.'/video-loop.php');
      echo _ad('0','video-hub-bottom');
?>

</div>
</div>