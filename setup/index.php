<?php  error_reporting(E_ALL); 
if(!isset($_SESSION['user_id'])) {$_SESSION['user_id'] = 1;}
function glob_recursive(string $baseDir, string $pattern, int $flags = GLOB_NOSORT | GLOB_BRACE)
{
    $paths = glob(rtrim($baseDir, '\/') . DIRECTORY_SEPARATOR . $pattern, $flags);
    if (is_array($paths)) {
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $subPaths = (__FUNCTION__)($path, $pattern, $flags);
                if ($subPaths !== false) {
                    $subPaths = (array) $subPaths;
                    array_push($paths, ...$subPaths);
                }
            }
        }
    }
    return $paths;
}
// security
if( !defined( 'in_phpvibe' ) )
	define( 'in_phpvibe', true);
// physical path of your root
if( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', str_replace( '\\', '/',  dirname(dirname( __FILE__ ) ))  );
// physical path of includes directory
if( !defined( 'INC' ) )
define( 'INC', ABSPATH.'/app/classes' );	
//Check if config exists
if(!is_readable(ABSPATH.'/vibe_config.php')){
echo '<h1>Hold on! Configuration file (vibe_config.php) is missing! </h1><br />';
die();
}	
//Config include
require_once(ABSPATH."/vibe_config.php");
require_once(ABSPATH."/setup/functions.php");
// Include all db classes
// Sql db classes
require_once( INC.'/ez_sql_core.php' );
if( !defined( 'cacheEngine' ) || (cacheEngine == "mysql") ) {
require_once( INC.'/ez_sql_mysql.php' );
  /* Define live db for MySql */
$db = new ezSQL_mysql(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');
 /* Define cached db for MySql */
$cachedb = new ezSQL_mysql(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');
} else {
require_once( INC.'/ez_sql_mysqli.php' );	
  /* Define live db for MySql Improved */
$db = new ezSQL_mysqli(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');
 /* Define cached db for MySql Improved */
$cachedb = new ezSQL_mysqli(DB_USER,DB_PASS,DB_NAME,DB_HOST,'utf8');	
}

	
ob_start();
$error = 0;
// Base URI

$base_href_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);

$base_href_protocol = ( array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http' ).'://';
if( array_key_exists('HTTP_HOST', $_SERVER) && !empty($_SERVER['HTTP_HOST']) )
{
	$base_href_host = $_SERVER['HTTP_HOST'];
}
elseif( array_key_exists('SERVER_NAME', $_SERVER) && !empty($_SERVER['SERVER_NAME']) )
{
	$base_href_host = $_SERVER['SERVER_NAME'].( $_SERVER['SERVER_PORT'] !== 80 ? ':'.$_SERVER['SERVER_PORT'] : '' );
}
$base_href = rtrim( $base_href_protocol.$base_href_host.$base_href_path, "/" ).'/';

$site_url = str_replace("setup/","",$base_href);

echo '
<!doctype html> 
<html prefix="og: http://ogp.me/ns#"> 
 <html dir="ltr" lang="en-US">  
<head>  
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>PHPVibe :: Setup</title>
<meta charset="UTF-8">  
<link rel="stylesheet" type="text/css" href="'.$base_href.'setup.style.css" media="screen" />
<script type="text/javascript" src="'.$base_href.'jquery.js"></script>
</head>
<body>
<div id="wrapper" class="container">
<div id="content">

'; ?>
<div class="text-center">
<div class="row head-row">
		<div class="logo">
		<img src="logobig.png"><br> 
		<p style="margin:30px 0 0">
		<h1>PHPVibe</h1>
		</p>
		</div>
</div>
<?PHP
$handler = isset($_REQUEST['step']) ? $_REQUEST['step'] : 0 ;

$aerror = '<div class="oksign">
	<span class="bg-red">
	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>	</span>
	</div>';
$caution = '<div class="oksign">
	<span class="bg-yellow">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
	</span>
	</div>';
$passed =  '<div class="oksign">
	<span class="bg-blue">
			<svg  x-description="Heroicon name: solid/check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
			  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
			</svg>
	</span>
	</div>';


switch ($handler) {
	case 0:	
    default:
        echo '
		<div class="row head-row">
		
		<p style="display:block; text-align:left; line-height:20px">PHPVibe is a dynamic and versatile video sharing platform, empowering users to create, share, and monetize their multimedia content with ease. </p>
        
				
			<div class="row text-left top30 bb">
			
			<div class="scrollbar" id="style-1" >';
			
			$fh = fopen('license.txt','r');
				while ($line = fgets($fh)) {
				
				  echo $line;
				}
				fclose($fh);
			
			echo '
			</div>
			</div>
			
			<div class="row text-center">
		<div class="col-12 top30 text-center">
		<a class="button primary lg" href="index.php?step=1">Accept agrement & Start</a>
		</div>	

			
	
			</div>
		</div>
		';
        break;
		
	case 1:	
	echo '<h2>Server requirements</h2>';
	
	echo '<iframe src="'.$site_url.'setup/reqcheck.php" width="100%" height="1000px" style="border:none;" scrolling="no"></iframe>
	<div class="row text-center top30">
		<div class="col-12 top30 text-center">
		<a class="button primary lg" href="index.php?step=2">Continue</a>
		</div>	';
	break;
    case 2:
	echo '<h2>Url</h2>';
			if (strpos(get_domain(SITE_URL), get_domain($site_url)) === false) {
		echo '<div class="lilbox col-12 pad20 shadow">'.$aerror.' The url <pre> '.SITE_URL.' </pre> defined seems wrong</div>';
		_error();
		} else {
		echo '<div class="lilbox col-12 pad20 shadow">'.$passed.'You are installing PHPVibe at <pre>'.SITE_URL.'</pre></div>';			
		}
		if(substr(SITE_URL, -1) !== '/') {
		echo '<div class="lilbox col-12 pad20 shadow">'.$aerror.' The url is missing the ending slash "/".
        <pre>'.SITE_URL.'/</pre>		</div>';
		_error();
		}
		$parse = parse_url($site_url); 
		if($parse['path'] != "/") {
			echo '<div class="lilbox col-12 pad20 shadow">'.$caution.' PHPVibe seems to be installed in a folder! <br> <pre>We suggest you avoid that for a smooth experience.</pre>  
			<br> <strong>Else</strong> : 
			<br> 1.) Edit the root .htaccess file and change <strong>RewriteBase /</strong> to <br> <strong>RewriteBase /yourfoldername</strong> <br> (the folder name is probably '.$parse['path'].')
			<br>	2.)	Uncomment and change "Base path" in the index.php file like this: 
			<pre><code>/* Uncomment and edit bellow if installed in a folder */
			$router->setBasePath(\'/yourfoldername\'));</code></pre>
			</div>';
			}
			
		if($error > 0) {
        echo '<p class="full pad20 text-center">You may be looking for the <a target="_blank" href="https://old.phpvibe.com/installing-phpvibe/">configuration file</a>.</p>';
		echo '	<div class="row text-center">
		<div class="col-12 top30 text-center">
		<a class="button outline dark" href="index.php?step=3">Continue</a><br>
		<span class="red-text">Caution!</span> <small class="red-text">Doing it with errors may break your website!</small>
		</div>	
		</div>	';
		} else {
			echo '	<div class="row text-center">
		<div class="col-12 top30 text-center">
		<a class="button primary lg" href="index.php?step=3">Continue</a>
		</div>	
		</div>	';
		}
        break;
    case 3:
       echo '<h2>Database test</h2>';
	   
	   
	   echo '<div class="lilbox col-12 pad20 shadow">Connecting as '.DB_USER.' to  '.DB_NAME.' @ '.DB_HOST.'</div>';
	    echo '<p class="full pad20 text-center">Result bellow: </p>
		<iframe src="'.$site_url.'setup/dbcheck.php" width="100%" height="300" style="border:none;"></iframe>
		';
	    echo '<p class="full pad20 text-center">Any errors? Check the data in the <a target="_blank" href="https://old.phpvibe.com/installing-phpvibe/">configuration file</a>.<br>
		<small>Makes sure the details are ok, no whitespaces, no errors, then check that the user has full permissions over the database (on the server)</small></p>';
	    echo '<div class="row text-center">
		<div class="col-12 top30 text-center">
		<a class="button primary lg" href="index.php?step=4">Continue</a>
		</div>	
		</div>	';
	   
        break;
		
	case 4:
		    echo '<h2>Database :: Setup</h2>';
			echo 'Your tables prefix for this install is : <pre>'.DB_PREFIX.'</pre><br>';
			$test_db = $db->get_col("SHOW TABLES",0);
	
			
				if($test_db) {
					echo '<div class="row text-center">
							<div class="col-12 ">
					<div class="lillist flex-center">'.$caution.' <strong>The database is not empty!</strong> </div>';
					echo 'Existing tables are</p><ol class="listute">';
					foreach ($test_db as $tabelvechi) {
			        echo '<li>'.$tabelvechi.'</li>';			 
	            }
				echo '</ol><p class="full"> Please check if this could lead to a conflict!</p></div></div>';
					

					
				}
			
			echo "
			<script>
				function installTheDatabase() {
					
					alert('Have patience and don\'t close the page!');
				  $.ajax({
				  url: '".SITE_URL."setup/install.db.php',
				  beforeSend: function( xhr ) {
					$('a#setuptables').html('Started...this may take a minute');
					 $('#secod').html('<img src=\"cog-gear.gif\"/>');
				  }
				})
				  .done(function( data ) {
					  //alert(data);
					if ( data == 'done' ) {		
					  $('a#setuptables').html('Done. You can now continue');
					  $('#secod').remove();
						} else {
					  $('a#setuptables').text('Fail! Check the errors and try again.');
					$('a#setuptables').removeClass('success').addClass('dark');
					  $('#secod').html(data);
					}
				  });	
				}
				</script>
			";
			/* 
			echo '<a target="_blank" href="https://icons8.com/icon/H6C79JoP90DH/settings">Gear</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a>';
			*/
			echo '<div class="row text-center">
		<div class="col-12 top30 text-center">
		<a id="setuptables" class="button success lg"  href="#" onclick="installTheDatabase();return false;">Install the database tables</a>
		<div id="secod" class="flex flex-col flex-center pad20"></div>
		</div>
		
		</div>	';
		   
		     echo '<div class="row text-center">
		<div class="col-12 top30 text-center">
		<a class="button primary lg" href="index.php?step=5">Continue</a>
		</div>	
		</div>	';
		   
		break;
			 
	case 5:
		  echo '<h2>Folders</h2>';
		  
		  $checkpassed = array();
		  
		  $search_results = glob_recursive(ABSPATH.'/storage/', '*', GLOB_ONLYDIR);
		  $existingdirs = array();
		  foreach($search_results as $tdir) {
			  $item = ltrim(str_replace(ABSPATH, '',$tdir),'/');
			  $existingdirs[] = str_replace('\\', '/',$item);
		  }
		  
		 
		  $expectedfolders = array(		  
			'storage/media',
			'storage/media/thumbs',
			'storage/media',
			'storage/minify',
			'storage/rawmedia',
			'storage/uploads',
			'storage/cache',
			'storage/jcache',	
			'storage/cache/thumbs',
			'storage/cache/full',
			'storage/cache/html',
			'storage/langs'	  
		  );
		  unset($search_results);
		  array_unique($existingdirs);
		  asort($existingdirs);
		  //var_dump($existingdirs);
		  array_unique($expectedfolders);
		   array_unique($existingdirs);
		asort($expectedfolders);
		//var_dump($expectedfolders);	
		 $missingresult = array_diff($expectedfolders, $existingdirs);
		if(count($missingresult) > 0) {
			echo '<strong>You need to create the following folders</strong> <br> <small> in '.ABSPATH.'</small>';
			foreach ($missingresult as $buildthis) {
				echo '<pre>'.$buildthis.'</pre>';
				
			}
		}
		
		echo '<strong>Folder permissions</strong>';
		$ci = 0;
		foreach ($expectedfolders as $dirverify) {
			@chmod(ABSPATH.'/'.$dirverify, 0777);
			if (!is_writable(ABSPATH.'/'.$dirverify)) {
				echo '<div class="lillist">'.$aerror.'<strong>'.$dirverify.'</strong></div>'; /* Flex issue? */
				$ci++;
			} else {
				
				echo '<div class="lillist">'.$passed.'<strong>'.$dirverify.'</strong></div>';/* Flex issue? */
			}
			
			
		}
		
		if($ci > 0) {
				echo '<strong>Please make sure all this folders are writable! </strong> 
				<br> If they are writable your server can modify files, add, put videos. 
				<br> Folders are located in : '.ABSPATH.'
				<br><pre>Chmod them 0777 or 0755 depending on your server(\'s settings).</pre>';
			}
		   echo '<div class="row text-center">
		<div class="col-12 top30 text-center">
		<a class="button primary lg" href="index.php?step=6">Continue</a>
		</div>	
		</div>	';
		  break;
		 
		 case 6:
		 
		 if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['pass1']) && isset($_POST['username'])){
			 
			if(!empty($_POST['pass1']) && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['username'])) {
				
			if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {	
				$uniqAd = $db->get_var("SELECT count(*) from ".DB_PREFIX."users where email='" . $db->escape($_POST['email']) . "' or username='" . $db->escape($_POST['username']) . "'");
				if($uniqAd > 1 ) {
						echo '<div class="lilbox shadow pad20"> '.$aerror.' <pre>Email or username are already in use! Try another one</pre></div>';
				} else {
			$sql = "INSERT INTO ".DB_PREFIX."users (name,username,email,type,lastlogin,date_registered,group_id,password,avatar)"
			 . " VALUES ('" . $db->escape($_POST['name']) . "','" . $db->escape($_POST['username']) . "','" . $db->escape($_POST['email']) . "','core', now(), now(), '1', '".sha1($_POST['pass1'])."', 'storage/uploads/def-avatar.jpg')";
			$db->query($sql);
			echo '<div class="lilbox shadow pad20"> '.$passed.' <pre>Account '. $db->escape($_POST['name']) .' created!</pre></div>';
			
				}
			} else {
			echo '<div class="lilbox shadow pad20"> '.$aerror.' <pre>Wrong email format</pre></div>';
			}
			} else {
			echo '<div class="lilbox shadow pad20"> '.$aerror.' <pre>Some fields are empty </pre></div>';
			}
			}
		 
		 
		 
		 $u_check = $db->get_var("SELECT count(*) as nr from ".DB_PREFIX."users where group_id='1'");
			if($u_check) {
			$Admins = $u_check;
			} else {
			$Admins = 0;	
			}
		  echo '<h2>Administrators ('.$Admins.')</h2>';
		  
		  if($Admins > 0) {
			 			  
			  $Adnames = $db->get_results("SELECT name, username from ".DB_PREFIX."users where group_id='1'");
			   echo '<ol class="listute">';
			  foreach ($Adnames as $Names) {
				  echo '<li>'.$Names->name.'  ('.(empty($Names->username) ? ' Empty ' : $Names->username).')'.'</li>';
				  
			  }
			   echo '</ol>';
			  echo '<hr class="full top30">';
			  
		  }
		  echo '<h2>Add a new admin</h2>';
		  if($Admins < 1) {
		  echo '<strong>This would be your first admin, so...you maybe?</strong>';		  
		  } 
		  
		  echo '
		  <form id="validate" class="form-horizontal top30 pad20" action="'.$base_href.'index.php?step=6&done=1" enctype="multipart/form-data" method="post">
			<fieldset>
					<div class="form-group form-material floating">
					<label class="control-label">Name</label>
						<div class="controls">
						<input type="text" name="name" class="form-control full" value="" /> 						
						<span class="help-block" id="limit-text">Account\'s name.</span>
						</div>	
					</div>
					<div class="form-group form-material floating">
					<label class="control-label">Username</label>
						<div class="controls">
						<input type="text" id="nickname" name="username" class="form-control full" value="" onkeyup="checktheuser()"/> 						
						<span class="help-block" id="nicknamehelp">Try a nickname.</span>
						</div>	
					</div>
					<div class="form-group form-material floating">
					<label class="control-label">Email</label>
						<div class="controls">
						<input type="text" id="mail" name="email" onkeyup="checkthemail()" class="form-control full" value="" /> 						
						<span class="help-block" id="mailhelp">Valid e-mail address.</span>
						</div>	
					</div>		
					<div class="form-group form-material floating">
					<label class="control-label">Password</label>
						<div class="controls">
						<input id="password" type="password" name="pass1" class="form-control full" value=""  /> 
						<span class="help-block" id="limit-text">
						<input type="checkbox" class="ios-switch blue" onclick="ShowPass()">  Show Password						
					    </span>
						</div>	
					</div>	
					<div class="form-group form-material floating top30 pad20">
						<button class="button success" type="submit">Create account</button>	
					</div>	
			</fieldset>					
		  </form>		  
		  ';
		  echo "
			<script>
			function ShowPass() {
							  var x = document.getElementById('password');
							  if (x.type === 'password') {
								x.type = 'text';
							  } else {
								x.type = 'password';
							  }
							}
				function checktheuser() {
				var username = $('#nickname').val();
				
					if(username.length > 3) {
					//alert(username);
						  $.ajax({
						  url: '".SITE_URL."setup/ucheck.php',
                          type: 'post',
                          data: {username: username}
						  })
						  .done(function( data ) {
							  //alert(data);
							 if(data < 1) {
							  $('#nicknamehelp').html('Username is available!').addClass('helper text-success').removeClass('text-error');
							 } else {
								 $('#nicknamehelp').html('Username is taken!').addClass('helper text-error').removeClass('text-success');
							 }
							
						  });	
					}
				}
					function checkthemail() {
				var email = $('#mail').val();
				
					if(email.length > 3) {					
						  $.ajax({
						  url: '".SITE_URL."setup/mailcheck.php',
                          type: 'post',
                          data: {email: email}
						  })
						  .done(function( data ) {
							  //alert(data);
							 if(data < 1) {
							  $('#mailhelp').html('Email is available!').addClass('helper text-success').removeClass('text-error');
							 } else {
								 $('#mailhelp').html('Email is already used! Try another one!').addClass('helper text-error').removeClass('text-success');
							 }
							
						  });	
					}
				}
				</script>
			";
		  
		   echo '<div class="row text-center">
		<div class="col-12 top30 text-center">
		<a class="button primary lg" href="index.php?step=7">Continue</a>
		</div>	
		</div>	';
		  
		   break;
	case 7:
		do_remove_file_now(ABSPATH.'/hold');
 echo '<h2>Done install!</h2>';	
 
		
		  echo '<div class="row text-center">
		<div class="col-12 top30 text-center">
		<a class="button primary lg" href="'.$base_href.'done.php"> Start configuring</a>
		</div>	
		</div>	';
		
		
		break;
}


echo '
		</div>
		</div> <br style="clear:both">
			<strong class="grey-text" style="font-weight: 300;">&copy; Copyright 2010 - '.date('Y').' Marius Patrascu, PHPVibe.com</strong>';
?>
