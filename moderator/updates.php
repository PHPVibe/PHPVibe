<div class="row"> 
 <div class="row-setts panel-body">

 <div class="row-header"> 
 <div class="row"> 
  <div class="col-md-6  col-xs-12"> 
 <h1>UPDATES</h1>
  </div>
 <div class="col-md-6  col-xs-12 text-right"> 
  <?php
  echo '<h4>'.$aboutVibe->cms().'   <code>'.$aboutVibe->fullversion().'</code>  </h4>
  ';
  ?>
  </div>
   </div>
 </div>
 
 <div class="row full"> 
  <div class="col-md-4  col-xs-12"> 
 <h4> Your version </h4>
 <?php 
 echo '<h3>'.$aboutVibe->cms().' <code class="mleft20">'.$aboutVibe->fullversion().'</code></h3>';
		echo '<br>Major version: '.$aboutVibe->major();
		echo '<br>Subversion: '.$aboutVibe->subversion();
		echo '<br>Release type: '.$aboutVibe->state();
		echo '<br>Release date: '.$aboutVibe->released();
		echo '</div><div class="col-md-4 col-md-offset-2 col-xs-12">  <h4> Available at PHPVibe.com </h4>';
		
 $updater = json_decode(getpb("https://validate.phpvibe.com/api/"), true);
 $updater =$updater['cms']; /* dropping excess */
 echo '<h3>'.$updater['name'].'';
 echo '<code class="mleft20">'.$updater['release'].'</code></h3>';
 echo '<br>Major version: '.$updater['release'];
		echo '<br>Subversion: '.$updater['suv'];
		echo '<br>Release type: '.$updater['state'];
		echo '<br>Release date: '.date("d.m.Y", strtotime($updater['released']));
		echo '<br><small>'.time_ago($updater['released']).'</small>';
 ?>
 </div>
  </div>
  <div class="row full"> 
 <?php
 
 if(($aboutVibe->major() < $updater['version']) && ($aboutVibe->major() < $updater['version'])) {
	 echo '<div class="msg-warning mtop20 col-md-3">A new version is available!</div>';
	 echo '<div class="col-md-6 col-md-offset-3  mtop20">Outdated versions may be vulnerable and miss new features. <br> Head to <a target="_blank" href="https://phpvibe.com">PHPVibe</a> to check what\'s new</div>';
 } else {
	 echo '<div class="msg-win mtop20 col-md-3">Up to date!</div>'; 
	 	 echo '<div class="col-md-6 col-md-offset-3  mtop20">Browse <a target="_blank" href="https://phpvibe.com">PHPVibe</a>, <br>to check what\'s new in the addons section and keep in touch.</div>';

 }
 echo '</div>';
  echo ' <div class="row full"> ';
  if($aboutVibe->state() == 'alpha' ) {
	  echo '<div class="msg-warning mtop20 col-md-3">Alpha version detected!</div>';
	 echo '<div class="col-md-6 col-md-offset-3  mtop20"><strong>Alpha versions are for pre-testing only!</strong> <br> This versions could have bugs or missing features. They have not been tested!  Head to <a target="_blank" href="https://forums.phpvibe.com">PHPVibe</a> to report any bugs or errors or to check if a stable version is available.</div>';
  }
   if($aboutVibe->state() == 'beta' ) {
	  echo '<div class="msg-warning mtop20 col-md-3">Beta version detected!</div>';
	 echo '<div class="col-md-6 col-md-offset-3  mtop20"><strong>Beta versions are for testing!</strong> This versions could have bugs or missing features. They have not been tested enough in the real world! <br> Head to <a target="_blank" href="https://forums.phpvibe.com">PHPVibe</a> to report any bugs or errors or to check if a stable version is available.</div>';
  }
 ?>

  </div>
 
 </div>
 
  </div>