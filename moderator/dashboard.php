 <div class="row"> 
 <div class="row-setts panel-body">

 <div class="row-header"> 
 <div class="row"> 
  <div class="col-md-6  col-xs-12"> 
 <h1>DASHBOARD</h1>
  </div>
 <div class="col-md-6  col-xs-12 text-right"> 
  <?php
  echo '<h4>'.$aboutVibe->cms().'   <code>'.$aboutVibe->fullversion().'</code>
  <a class="tipS" title="Check for updates" href="'.admin_url('updates').'"><i class="material-icons mleft10">published_with_changes</i></a>
  </h4>
  ';
  ?>
  </div>
   </div>
 </div>
  <div class="row full">
  <div class="panel panel-transparent">
    <div class="panel-body card-holder">
      <ul class="list-group card-icos">
        <li class="list-group-item"> 
          <a class="iconed-block" href="<?php echo admin_url('videos'); ?>" title=""> 
          <i class="material-icons">           video_library</i> </a>   
		  <strong> <?php echo _count('videos'); ?> </strong>   
		  <a href="<?php echo admin_url('videos'); ?>" title=""> 
          <?php echo _lang('Videos');?>
		  </a>  
        </li>
        <li class="list-group-item"> 
          <a class="iconed-block" href="<?php echo admin_url('users'); ?>" title=""> 
          <i class="icon-slideshare"> 
          </i>		</a>    <strong>        <?php echo _count('users'); ?>      </strong>      
		  <a href="<?php echo admin_url('users'); ?>" title=""> 
          <?php echo _lang('Members');?>
		  </a> 
        </li>
        <li class="list-group-item"> 
          <a class="iconed-block" href="<?php echo admin_url('videos'); ?>" title=""> 
          <i class="material-icons"> 
          remove_red_eye </i></a>		<strong>        <?php echo _count('videos','views'); ?>      </strong>      
		  <a href="<?php echo admin_url('videos'); ?>" title=""> 
          <?php echo _lang('Media views');?>      
		  </a>  
        </li>
        <li class="list-group-item"> 
          <a class="iconed-block" href="<?php echo admin_url('images'); ?>" title=""> 
          <i class="material-icons"> 
          picture_in_picture_alt    </i>   
		  </a>		
		  <strong>        
		  <?php echo _count('images','views',true ); ?>      </strong>      
		  <a href="<?php echo admin_url('images'); ?>" title=""> 
          <?php echo _lang('Picture views');?>      
		  </a>  
        </li>
        <li class="list-group-item"> 
          <a class="iconed-block"href="<?php echo admin_url('videos'); ?>&sort=liked-desc" title=""> 
          <i class="material-icons">           thumb_up</i></a>	
		  <strong><?php echo _count('likes' ); ?> </strong>      
		  <a href="<?php echo admin_url('videos'); ?>" title=""> 
          <?php echo _lang('Video likes');?>      
		  </a>  
        </li>
        <li class="list-group-item"> 
          <a class="iconed-block" href="<?php echo admin_url('playlists'); ?>" title=""> 
          <i class="material-icons">          playlist_add_check     </i>	 
		  </a>	
		  <strong>        <?php echo _count('playlists' ); ?>      </strong>      
		  <a href="<?php echo admin_url('playlists'); ?>" title=""> 
          <?php echo _lang('Collections');?>      
		  </a>  
        </li>
        <li class="list-group-item"> 
          <a class="iconed-block" href="<?php echo admin_url('comments'); ?>" title=""> 
          <i class="material-icons">comment</i> 
		  </a>		
		  <strong>        <?php echo _count('em_comments' ); ?>      </strong>      
		  <a href="<?php echo admin_url('comments'); ?>" title=""> 
          <?php echo _lang('Comments');?>       
		  </a>  
        </li>
        <li class="list-group-item"> 
          <a class="iconed-block" href="<?php echo admin_url('reports'); ?>" title=""> 
          <i class="material-icons"> flag</i>
		  </a>  		<strong>        <?php echo _count('reports' ); ?>      </strong>      
		  <a href="<?php echo admin_url('reports'); ?>" title=""> 
          <?php echo _lang('Reports');?>      
		  </a>    
        </li>
        <li class="list-group-item"> 
          <a class="iconed-block" href="<?php echo admin_url('ffmpeg'); ?>" title=""> 
          <i class="material-icons">slow_motion_video</i>	
		  </a> 	        
          <strong>FFMPEG </strong>		      
          <a href="<?php echo admin_url('ffmpeg'); ?>" title=""> 
          <code><?php echo (get_option('ffa','0') == 1)? 'On' : 'Off'; ?></code>		      
		  </a>    
        </li>
      </ul>
    </div>
  </div>
</div>
 <div class="row full"> 
   <div class="col-md-6  col-xs-12"> 
 <div class="panel panel-transparent"> 
<div class="example-wrap m-md-0">

<?php 
$videoviews = $db->get_results("select date, count(id) as numar from ".DB_PREFIX."videos GROUP BY EXTRACT(DAY FROM date ) order by date desc");

?>
                <h4>Video pulse</h4>
                <div id="videoMorrisLine"></div>
                 
                </div>
                <!-- End Example Line -->
              </div>
 <script type="text/javascript">
 (function () {
    Morris.Line({
      element: 'videoMorrisLine',
      data: [		  
		  <?php 
		  foreach ($videoviews as $view){
			  echo '{"y": "'.date("Y-m-d", strtotime($view->date)).'",      "a": "'.$view->numar.'"},';
		  }		  
		  ?>        
      ],
      xkey: 'y',
      ykeys: ['a'],
      labels: ['New media'],
      resize: true,
      pointSize: 3,
      smooth: true,
      gridTextColor: '#474e54',
      gridLineColor: '#eef0f2',
      goalLineColors: '#e3e6ea',
      gridTextFamily: 'Poppins,Sans-Serif',
      gridTextWeight: '300',
      numLines: 7,
      gridtextSize: 14,
      lineWidth: 1,
	  yLabelFormat: function (y) {			
			return Math.round(y);
		},
	 lineColors: ['#1e88e5']
    });
  })(); 
 </script>

 </div>

  
   <div class="col-md-6  col-xs-12"> 
 <div class="panel panel-transparent"> 
<div class="example-wrap m-md-0">

<?php 
$videoviews = $db->get_results("select date, count(id) as numar from ".DB_PREFIX."images GROUP BY EXTRACT(DAY FROM date ) order by date desc");

?>
                <h4>New pictures</h4>
                <div id="musicMorrisLine"></div>
                 
                </div>
                <!-- End Example Line -->
              </div>
 <script type="text/javascript">
 (function () {
    Morris.Line({
      element: 'musicMorrisLine',
      data: [		  
		  <?php 
		  foreach ($videoviews as $view){
			  echo '{"y": "'.date("Y-m-d", strtotime($view->date)).'",      "a": "'.$view->numar.'"},';
		  }		  
		  ?>        
      ],
      xkey: 'y',
      ykeys: ['a'],
      labels: ['New pictures'],
      resize: true,
      pointSize: 3,
      smooth: true,
      gridTextColor: '#474e54',
      gridLineColor: '#eef0f2',
      goalLineColors: '#e3e6ea',
      gridTextFamily: 'Poppins,Sans-Serif',
      gridTextWeight: '300',
      numLines: 7,
      gridtextSize: 14,
      lineWidth: 1,
	  yLabelFormat: function (y) {			
			return Math.round(y);
		},
	 lineColors: ['#43a047']
    });
  })(); 
 </script>

 </div>
  
   </div>  
   
   
   
   
 
				
 <div class="row"> 
 
 <div class="col-xlg-6 col-md-6  col-xs-12"> 
 
 <div class="panel panel-bordered"> 
<?php $countu = $db->get_row("Select count(*) as nr from ".DB_PREFIX."users");
$users = $db->get_results("select name, id, avatar from ".DB_PREFIX."users order by id DESC limit 0,8");?> 
 <div class="panel-heading"> 
<h3 class="panel-title"> New users</h3> 
<ul class="panel-actions"> 
<li><a href="<?php echo admin_url("users");?>"> 
View all (<?php echo $countu->nr; ?>)</a></li></div>              </ul> 
 <div class="panel-body nopad scroll-items"> 
<ul class="list-group"> 
 <?php foreach ($users as $u) { 
 if(is_empty($u->name)) {$u->name= "Empty"; }
 ?>
 <li class="list-group-item"> 
 
 <div class="show no-margin pd-t-xs"> 
<span class="pull-left mg-t-xs mg-r-md"> 
<img data-name="<?php echo $u->name; ?>" src="<?php echo thumb_fix($u->avatar); ?>" class=" img-circle avatar avatar-sm" alt=""> 
</span> <a href="<?php echo profile_url($u->id, $u->name); ?>" target="_blank"> 
<?php echo _html($u->name); ?></a> <small class="pull-right"> 
<?php echo count_uvid($u->id); ?> videos</small></div><small class="text-muted"> 
Has <?php echo count_uact($u->id); ?> activities so far</small></li><?php } ?></ul></div>
</div>

</div>

 <div class="col-xlg-6 col-md-6  col-xs-12"> 
 
 <div class="panel panel-bordered"> 
<?php $countu = $db->get_row("Select count(*) as nr from ".DB_PREFIX."videos");
$videos = $db->get_results("select id,title,thumb from ".DB_PREFIX."videos where pub > 0 order by id DESC limit 0,8");?> 
 <div class="panel-heading"> 
<h3 class="panel-title"> New media</h3> 
<ul class="panel-actions"> 
<li><a href="<?php echo admin_url("videos");?>"> 
View all (<?php echo $countu->nr; ?>)</a></li></div>              </ul> 
 <div class="panel-body nopad scroll-items"> 
<ul class="list-group"> 
 <?php foreach ($videos as $v) { 
 if(is_empty($v->title)) {$v->title= "Empty"; }
 
 ?>
 <li class="list-group-item">  
 <div class="show no-margin pd-t-xs"> 
<span class="pull-left mg-t-xs mg-r-md"> 
<img data-name="<?php echo $v->title; ?>"  src="<?php echo thumb_fix($v->thumb); ?>" class=" img-circle avatar avatar-sm" alt="" src=""> 
</span> 
<a href="<?php echo video_url($v->id, $v->title); ?>" target="_blank"> <?php echo _html(_cut($v->title,90)); ?></a> 
</div>
</li>
<?php } ?></ul>
</div>
</div>

</div>  

</div>
</div>
				</div> 