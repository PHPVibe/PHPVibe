<?php the_sidebar();
      //Add sorter
      if(isset($sortop) && $sortop) {
          /* Most liked , Most viewed time sorting */
          $st = '
<div class="inline-block pull-right relative">
       <a data-toggle="dropdown" class="btn btn-default btn-outline dropdown-toogle"> <i class="icon icon-calendar"></i> <i class="icon icon-angle-down"></i> </a>
			<ul class="dropdown-menu dropdown-menu-right bullet">
			<li title="'._lang("This Week").'"><a href="'.images_url(token()).'?sort=w'.(_get('tag')? '?tag='._get('tag') : '').'"><i class="icon icon-circle-thin"></i>'._lang("This Week").'</a></li>
			<li title="'._lang("This Month").'"><a href="'.images_url(token()).'?sort=m'.(_get('tag')? '?tag='._get('tag') : '').'"><i class="icon icon-circle-thin"></i>'._lang("This Month").'</a></li>
			<li title="'._lang("This Year").'"><a href="'.images_url(token()).'?sort=y'.(_get('tag')? '?tag='._get('tag') : '').'"><i class="icon icon-circle-thin"></i>'._lang("This Year").'</a></li>
			<li class="drop-footer" title="'._lang("This Week").'"><a href="'.images_url(token()).(_get('tag')? '?tag='._get('tag') : '').'"><i class="icon icon-circle-thin"></i>'._lang("All").'</a></li>
		</ul>
		</div>
';
      } ?>
<div class="text-center removeonload">
    <div class="cp-spinner cp-flip"></div>
</div>
<div id="imagelist-content" class="hides">
    <?php echo _ad('0','images-list-top');
          include_once(TPL.'/images-loop.php');
          echo _ad('0','images-list-bottom');
    ?>

</div>

