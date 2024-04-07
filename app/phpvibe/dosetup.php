<?php 
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
$site_url = rtrim( $base_href_protocol.$base_href_host.$base_href_path, "/" ).'/';

	
echo '<!DOCTYPE html>
<html>
<head>
<title>PHPVibe setup</title>


<link rel="stylesheet" href="'.$site_url.'themes\main\styles\roboto.css">
<style>
body, html {
	padding:0;
	margin:0;
	font-family: "Roboto", sans-serif;
  font-weight: 400;
  font-style: normal;
  font-size:16px;
  color:#fff;
  background-color:#07143F
}
.content {
	 display: flex;
    color: #fff;
    min-height: 100vh;
    width: 100vw;
    flex-direction: column;
    flex-wrap: nowrap;
    align-content: center;
    justify-content: center;
    align-items: center;
}
.logopic{
	max-width:100%;
	width:140px;
	padding: 0 0 40px
}
.btnstart {
    display: block;
    padding: 8px 16px;
    margin: 25px 0;
    color: #fff;
    text-decoration: none;
   border: 2px solid transparent;
    border-radius: 8px;
    font-size: 25px;
    background: #4E46C6;
    box-shadow: 0 1px 6px rgb(32 33 36 / 28%);
}
.btnstart:hover {
	border: 2px solid #fff;
}
</style>
</head>
<body>
<div class="content">
<div class="logo">
<img class="logopic" src="https://phpvibe.com/assets/images/logobig.png">
</div>
<h1>Just a second!</h1>
  <h3> Edit the configuration file* and then </h3><br />
  <a href="setup/index.php" class="btnstart">Start the setup</a>
  <p><em>* vibe_config.php</em></p>
	NOTE: If you have already completed the steps and still see this,<br> then you need to remove the "hold" file in the root of this instalation!
  </div>';
die();