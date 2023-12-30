<?php the_sidebar(); do_action('pre-video');?>
<div class="video-holder row">
<?php if(has_list()){  ?>
<div id="renderPlaylist">
<?php } ?>
    <div class="<?php if(!has_list()){ echo "col-md-8 col-xs-12";} else {echo "row block player-in-list";}?> ">
        <div id="video-content" class="<?php if(has_list()){ echo "col-md-8 col-xs-12";} else {echo "row block";}?>">
            <div class="video-player pull-left
                <?php rExternal() ?>">
                <?php do_action('before-videoplayer');
                      echo _ad('0','before-videoplayer');
                      echo the_embed();
                      echo _ad('0','after-videoplayer');
                      do_action('after-videoplayer');
                ?>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php if(has_list()){ ?>
        <div id="ListRelated" class="video-under-right nomargin pull-right col-md-4 col-xs-12">
            <?php do_action('before-listrelated'); ?>
            <div class="video-player-sidebar pull-left">
                <div class="items">
                  <?php
				  echo fakePlace(7,'videolist').'
					<div class="ajaxreqList" data-url="playlist/?list='._get('list').'&idowner='.$video->owner().'&videoowner&videoid='.$video->id().'&sorter='._get('sorter').'"> </div>
					';
                  ?>
               </div>
				</div>
                <?php do_action('after-listrelated'); ?>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php } ?>
    </div>
    <?php if(has_list()){  ?>
    <div id="LH" class="row nomargin">
        <div class="playlistvibe">
            <div class="cute">
                <h1>
				<?php $listest = decList(_get('list'));
                      /* Test if list can be sorted */
                      if(intval($listest) > 0) {
                          $thesort = not_empty(_get('sorter')) ? _get('sorter') : 'no' ;
                          $thesorts = array(
                          'no' => _lang("Default order"),
                          'vd' => _lang("Added descending"),
                          'va' => _lang("Added ascending"),
                          'td' => _lang("Title descending"),
                          'ta' => _lang("Title ascending"),
                          'wd' => _lang("Views descending"),
                          'wa' => _lang("Views ascending"),
                          );
                          if (isset($thesorts[$thesort])) {
                ?>
				<div class="playlist-order">
				<a class="dropdown-toggle tipS" title="<?php echo $thesorts[$thesort];  ?>" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button">
				<i class="icon icon-calendar"></i>
				</a>
				<ul class="dropdown-menu dropdown-left bullet" role="menu">
				<?php
                              foreach ($thesorts as $key => $value) {
                                  echo '<li><a href="'.video_url($video->id() , $video->title(), hashList($listest)).'&sorter='.$key.'">';
                                  if($key !== $thesort) {
                                      echo '<i class="icon material-icons">&#xE836;</i> ';
                                  } else {
                                      echo '<i class="icon material-icons">&#xE837;</i> ';
                                  }
                                  echo $value.'</a></li>';

                              }
                              echo '</ul>';
                          } ?>
				</div>
				<?php } ?>

                    <?php  echo _html('Now playing:'); ?>
                    <span>
                        <?php  echo _html(_cut(list_title(_get('list')),260));?>
                    </span>
                </h1>


            </div>
            <div class="next-an list-next">
                <a class="fullit tipS" href="javascript:void(0)" title="<?php  echo _html('Resize player');?>">
                    <i id="flT" class="material-icons">&#xE85B;</i>
                </a>
                <a id="ComingNext" href="" class="tipS" title="">
                   <i class="material-icons">&#xE5C8;</i>
                </a>
                <a class="tipS" title="<?php  echo _html('Stop playlist');?>" href="<?php  echo $canonical;?>">
                <i class="material-icons">&#xE047;</i></a>
            </div>
        </div>
    </div>
	</div>
    <?php } ?>
    <div class="rur video-under-right oboxed <?php if(has_list()){ echo "mtop10";}?> pull-right col-md-4 col-xs-12">
        <?php do_action('before-related');  echo _ad('0','related-videos-top');?>
            <div class="related video-related top10 related-with-list">

                    <?php
					if(get_option("ajaxyRel" , 1) == 1) {
                        echo fakePlace(get_option('related-nr',12),'videolist').'
					<div class="ajaxreqRelated" data-url="relatedvids?videoowner&videoid='.$video->id().'&videomedia='.($video->isvideo() ? '1' : '2').'&videocategory='.$video->rawcategorylist().'">
					 </div>
					';
					} else {
                        echo '<ul>';
                        layout('layouts/related');
                        echo '</ul>';
					}
                    ?>

            </div>
            <?php do_action('after-related'); ?>
        </div>
        <div class="video-under col-md-8 col-xs-12">
            <div class="oboxed odet">
                <div class="row vibe-interactions">


					<div class="user-media-actions">


					<div class="like-views">
					<?php do_action('before-video-title'); ?>
                    <h1>
                        <?php echo $video->title();?>
                    </h1>
                    <?php do_action('after-video-title'); ?>

                    </div>
                          <div class="interaction-icons">
                <div class="likes-bar">

                            <?php if($is_liked) { ?>
                            <div class="aaa">
                                <a href="javascript:RemoveLike(<?php echo $video->id();?>)" id="i-like-it" class="isLiked pv_tip likes" title=" <?php echo _lang('Remove liked');?>">
                                    <i class="material-icons">&#xE8DC;</i>
                                    <span><?php echo $video->likes();?></span>
                                </a>
                            </div>
                            <?php } else { ?>
                            <div class="aaa">
                                <a href="javascript:iLikeThis(<?php echo $video->id();?>)" id="i-like-it" class="pv_tip likes" title=" <?php echo _lang('Like');?>">
                                    <i class="material-icons">&#xE8DC;</i>
                                    <span><?php echo number_format($video->likes());?></span>
                                </a>
                            </div>
                            <?php } ?>
							  <div class="aaa ">
                                <a href="javascript:iHateThis(<?php echo $video->id();?>)" id="i-dislike-it" class="pv_tip dislikes <?php if($is_disliked) { echo 'isLiked'; }?>" data-toggle="tooltip" data-placement="top" title="<?php if($is_disliked) { echo 'Remove '; }?> <?php echo _lang('Dislike');?>">
                                    <i class="material-icons">&#xE8DB;</i>
                                   <span> <?php echo number_format($video->dislikes()); ?></span>
                                </a>
                            </div>
                            <div class="like-box">

                                <div class="like-progress">
                                    <div class="likes-success" style="width:
                                        <?php echo $likes_percent;?>%;">
                                    </div>
                                    <div class="likes-danger second" style="width:
                                        <?php echo $dislikes_percent;?>%;">
                                    </div>
                                </div>
                            </div>
						   </div>

                            <div class="aaa">
                                <a id="social-sharer" data-target="#sharing-it" data-toggle="modal" href="javascript:void(0)"  title=" <?php echo _lang('Share or Embed');?>">
                                   <i class="material-icons">&#xE80d;</i>
                                    <span>
                                        <?php echo _lang('Share');?>
                                    </span>
                                </a>
                            </div>
                            <?php if (is_user()) { ?>
                            <div class="aaa">
                                <a data-toggle="dropdown" id="dLabel" data-target="#" class="pv_tip dropdown-toogle" title=" <?php echo _lang('Add To');?>">
                                    <i class="material-icons">&#xe7e5;</i>
									<span class="hidden-md hidden-lg visible-xs">
                                        <?php echo _lang('Add To');?>
                                    </span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dLabel">
             <?php  $playlists=$cachedb->get_results("SELECT * from ".DB_PREFIX."playlists where owner='".user_id()."' and ptype = 1 and picture not in ('[likes]', '[history]', '[later]') limit 0,100");
                    if($playlists){  ?>
			<li>
			<a title="<?php echo _lang("Watch later");?>" href="javascript:Padd(<?php echo $video->id(); ?>,<?php echo later_playlist(); ?>)">
                                   <i class="material-icons">&#xE924;</i>
                                  <?php  echo _lang("Watch later");?>
                                </a>
			</li>
                                    <?php  foreach($playlists as $pl){?>
                                    <li id="PAdd-<?php echo $pl->id; ?>">
                                        <a href="javascript:Padd(<?php echo $video->id(); ?>,<?php echo $pl->id; ?>)">
                                        <i class="material-icons">&#xE145;</i>
                                            <?php  echo _html($pl->title);?>
                                        </a>
                                    </li>
                                    <?php }?>
                                    <?php }?>
									<li class="divider" role="presentation"></li>
                                    <li>
                                        <a href="<?php echo site_url().me; ?>?sk=new-playlist">
                                           <i class="material-icons">&#xE609;</i>
                                            <?php  echo _lang("Create playlist");?>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <?php } ?>
                            <div class="aaa">
                                <a class="tipS" title=" <?php echo _lang('Report');?>" data-target="#report-it" data-toggle="modal" href="javascript:void(0)"  title=" <?php echo _lang('Report media');?>">
                                   <i class="material-icons">&#xE153;</i>
								   <span class="hidden-md hidden-lg visible-xs">
                                        <?php echo _lang('Report');?>
                                    </span>
                                </a>
                            </div>
							</div>
							<br style="clear:both">
							</div>

                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="user-container full top20 bottom20">
                    <div class="pull-left user-box" style="">
                        <?php echo '
                        <a class="userav" href="'.profile_url($video->owner(),$video->rawauthor()).'" title="'.addslashes($video->rawauthor()).'">
                            <img src="'.thumb_fix($video->rawavatar(), true, 60,50).'" data-name="'.addslashes($video->rawauthor()).'"/>
                        </a>
						<div class="user-box-txt">
						<a class="" href="'.profile_url($video->owner(),$video->rawauthor()).'" title="'.addslashes($video->rawauthor()).'">
                                <h3>'.$video->author().' '.group_creative($video->authorgroup()).'</h3>
                            </a> 
							<p class="user-box-numbers">  <span class="mright20">'.number_format($video->views()).' '._lang("views").'</span> '.time_ago($video->added()).'</p>
						</div>';?>

                        <div class="pull-right"><?php subscribe_box($video->owner());?></div>
                    </div>
                    <div style="clear:both"></div>
                </div>


                <?php do_action('before-description-box'); ?>
                <div class="video-description">
                    <ul id="media-info" class="list-unstyled">



						  <li>
						  <div id ="media-description" data-small="<?php echo _lang("show more");?>" data-big=" <?php echo _lang("show less");?>">
                            <?php echo makeLn($video->description());?>


                            </div>
                        </li>
                        <?php
                        if(not_empty($video->rawcategorylist())) {
                            echo '<li class="mbot20 mtop20">'.$video->categories('','achip inline mright10 mleft10').'</li>';
                        }
                        ?>
                        <?php if(is_array($video->tags()) && not_empty($video->tags())) { ?>
                            <li class="mbot20"><div class="thetags"> <?php echo $video->prettytags('right20','#','');?></div></li>
                        <?php } ?>
                    </ul>

                    <?php do_action('after-description-box'); ?>
                </div>
                <div class="clearfix"></div>
                <div class="oboxed related-mobi mtop10 visible-sm visible-xs hidden-md hidden-lg">
                    <a id="revealRelated" href="javascript:void(0)">
                        <span class="show_more text-uppercase">
                            <?php echo _lang("show more");?>
                        </span>
                        <span class="show_more text-uppercase hide">
                            <?php echo _lang("show less");?>
                        </span>
                    </a>
                </div>
                <div class="clearfix"></div>
                <div class="oboxed ocoms mtop10">
                    <?php echo _ad('0','top-of-comments');?>
                    <?php do_action('before-comments'); ?>
                    <?php
					$comsNav = '<nav id="page_nav"><a href="'.$canonical.'?p='.next_page().'"></a></nav>';
					echo comments();
                    ?>
                    <?php do_action('after-comments'); ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <script type="text/javascript">
$(document).ready(function(){
	DOtrackview(<?php echo $video->id(); ?>);
});

        </script>
    </div>
    <?php do_action('post-video'); ?>
    <!-- Start Report Sidebar -->
    <div class="modal fade" id="report-it" aria-hidden="true" aria-labelledby="report-it"
                        role="dialog" tabindex="-1">
        <div class="modal-dialog modal-sidebar modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">
                        <?php  echo _lang('Report video');?>
                    </h4>
                </div>
                <div class="modal-body">
                    <?php if(!is_user()){?>
                    <p>
                        <?php  echo _lang('Please login in order to report media.');?>
                    </p>
                    <?php } elseif(is_user()){?>
                    <div class="ajax-form-result"></div>
                    <form class="horizontal-form ajax-form" action="
                        <?php echo site_url().'app/ajax/report.php';?>" enctype="multipart/form-data" method="post">
                        <input type="hidden" name="id" value="
                            <?php  echo $video->id();?>" />
                            <input type="hidden" name="token" value="
                                <?php  echo $_SESSION['token'];?>" />
                                <div class="control-group" style="border-top: 1px solid #fff;">
                                    <label class="control-label">
                                        <?php  echo _lang('Reason for reporting');?>:
                                    </div>
                                    <div class="controls">
                                        <div class="checkbox-custom checkbox-primary">
                                            <input type="checkbox" name="rep[]" value="
                                                <?php echo _lang('Media not playing');?>" class="checkbox-custom">
                                                <label>
                                                    <?php echo _lang('Video not playing');?>
                                                </label>
                                            </div>
                                            <div class="checkbox-custom checkbox-primary">
                                                <input type="checkbox" name="rep[]" value="
                                                    <?php  echo _lang('Wrong title/description');?>" class="styled">
                                                    <label>
                                                        <?php  echo _lang('Wrong title/description');?>
                                                    </label>
                                                </div>
                                                <div class="checkbox-custom checkbox-primary">
                                                    <input type="checkbox" name="rep[]" value="
                                                        <?php  echo _lang('Media is offensive');?>" class="styled">
                                                        <label>
                                                            <?php echo _lang('Video is offensive');?>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox-custom checkbox-primary">
                                                        <input type="checkbox" name="rep[]" value="
                                                            <?php  echo _lang('Media is restricted');?>" class="styled">
                                                            <label>
                                                                <?php echo _lang('Video is restricted');?>
                                                            </label>
                                                        </div>
                                                        <div class="checkbox-custom checkbox-primary">
                                                            <input type="checkbox" name="rep[]" value="
                                                                <?php  echo _lang('Copyrighted material');?>" class="styled">
                                                                <label>
                                                                    <?php  echo _lang('Copyrighted material');?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <textarea rows="5" cols="3" name="report-text" class="form-control" required></textarea>
                                                            <p>
                                                                <strong>
                                                                    <?php  echo _lang('Required'); ?>
                                                                </strong> :
                                                                <?php  echo _lang('Tell us what is wrong with the video in a few words');?>
                                                            </p>
                                                            <div class="row mtop20 bottom10">
                                                                <button class="btn btn-primary btn-block" type="submit">
                                                                    <?php  echo _lang('Send report');?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Modal -->
                                </div>
                            </div>
                            <!-- End Report Sidebar -->
<div class="modal fade" id="sharing-it" aria-hidden="true" aria-labelledby="sharing-it"   role="dialog" tabindex="-1">
        <div class="modal-dialog modal-sidebar modal-lg">
            <div class="modal-content">
   <div class="sharing-icos odet ">
				<?php do_action('before-social-box'); ?>
			<div class="full">
            <div class="text-center text-uppercase full bottom10 top20">
			<h4><?php  echo _lang('Let your friends enjoy it also!');?></h4>
			</div>
             <div id ="jsshare" data-url="<?php echo $canonical; ?>" data-title="<?php echo _cut($video->title(), 40); ?>"></div>
            </div>
			<div class="video-share mtop10 clearfix">
			<div class="text-center text-uppercase full bottom20 top20">
			<h4><?php  echo _lang('Add it to your website');?></h4>
			</div>
                <div class="form-group form-material floating">
                    <div class="input-group">
                        <span class="input-group-addon">
                           <i class="material-icons">&#xE157;</i>
                        </span>
                        <div class="form-control-wrap">
                            <input type="text" name="link-to-this" id="share-this-link" class="form-control" title="<?php echo _lang('Link back');?>" value="<?php echo canonical();?>" />
                                <label class="floating-label">
                                    <?php  echo _lang('Link to this');?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-material floating">
                        <div class="input-group">
                            <span class="input-group-addon">
                               <i class="material-icons">&#xE911;</i>
                            </span>
                            <div class="form-control-wrap">
							<div class="row">

							<div class="col-md-7">
<textarea style="min-height:80px" id="share-embed-code-small" name="embed-this" class="form-control" title=" <?php echo _lang('Embed this media on your page');?>"><iframe width="853" height="480" src="<?php echo site_url().embedcode.'/'._mHash($video->id()).'/';?>" frameborder="0" allowfullscreen></iframe></textarea>
 <label class="floating-label"> <?php  echo _lang('Embed code');?></label>
     <div class="radio-custom radio-primary"><input type="radio" name="changeEmbed" class="styled" value="1"><label>1920x1080</label></div>
	<div class="radio-custom radio-primary"><input type="radio" name="changeEmbed" class="styled" value="2"><label>1280x720</label></div>
	<div class="radio-custom radio-primary"><input type="radio" name="changeEmbed" class="styled" value="3"><label>854x480</label></div>
	<div class="radio-custom radio-primary"><input type="radio" name="changeEmbed" class="styled" value="4"><label>640x360</label></div>
	<div class="radio-custom radio-primary"><input type="radio" name="changeEmbed" class="styled" value="5"><label>426x240</label></div>
					  </div>
							<div class="col-md-4 col-md-offset-1">
  	<div class="well">
				<div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="material-icons">&#xE86F;</i></span>
                    <input type="number" class="form-control" name="CustomWidth" id="CustomWidth" placeholder="<?php echo _lang("Custom width");?>">
                  </div>
                </div>
				<div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="material-icons">&#xE883;</i></span>
                    <input type="number" name="CustomHeight" id="CustomHeight" class="form-control" placeholder="<?php echo _lang("Custom height");?>"> </div>
                </div>
				</div>
					</div>
							</div>
							</div>
                        </div>
                    </div>
					<div class="form-group form-material floating">
                    <div class="input-group">
                        <span class="input-group-addon">
                           <i class="material-icons">&#xE1B1;</i>
                        </span>
                        <div class="form-control-wrap">
                            <input type="text" name="link-to-this" id="share-this-responsive" class="form-control" title=" <?php echo _lang('Make the iframe responsive on your website');?>" value='<script src="<?php  echo site_url();?>lib/vid.js"></script>' />
                                <label class="floating-label">
                                    <?php  echo _lang('Responsive embed');?>
                                </label>
                            </div>
							<span class="help-block">
							<?php  echo _lang('Include this script into your page along with the iframe for a'); ?> <code><?php  echo _lang('responsive media embed');?></code>
                        <span>
						</div>
                    </div>
                </div>
                </div>
			</div>
        </div>
	</div>