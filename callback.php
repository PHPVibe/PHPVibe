<?php error_reporting(E_ERROR);
include 'load.php';
ob_start();
if (is_user()) { redirect();}
//Check callback type(twitter, facebook, google)
if (!empty($_GET['type'])) {
$cookieArr = array();
switch ($_GET['type']) {
case 'facebook':
require_once( INC.'/facebook/autoload.php' );
$fb = new Facebook\Facebook([
'app_id'  => Fb_Key,
'app_secret' => Fb_Secret,
'default_graph_version' => 'v2.8',
]);

$helper = $fb->getRedirectLoginHelper();
/*
if(!isset($_SESSION['FBRLH_state'])) {
$_SESSION['FBRLH_state']=$_GET['state'];
}
*/
try {
$accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
// When Graph returns an error
echo 'Graph returned an error: ' . $e->getMessage();
exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
// When validation fails or other local issues
echo 'Facebook SDK returned an error: ' . $e->getMessage();
exit;
}

if (! isset($accessToken)) {
if ($helper->getError()) {
header('HTTP/1.0 401 Unauthorized');
echo "Error: " . $helper->getError() . "\n";
echo "Error Code: " . $helper->getErrorCode() . "\n";
echo "Error Reason: " . $helper->getErrorReason() . "\n";
echo "Error Description: " . $helper->getErrorDescription() . "\n";
} else {
header('HTTP/1.0 400 Bad Request');
echo 'Bad request';
}
exit;
}
// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
try {
// Returns a `Facebook\FacebookResponse` object
$response = $fb->get('/me?fields='.$conf_facebook['fields'], $accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
echo 'Graph returned an error: ' . $e->getMessage();
exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
echo 'Facebook SDK returned an error: ' . $e->getMessage();
exit;
}
$user = $response->getGraphUser();
if ($user) {
$user_pic = user::getDataFromUrl('http://graph.facebook.com/'.$user["id"].'/picture?type=large&redirect=false');
$avatarInfo = json_decode($user_pic);

        /* Copy Avatar */
		$user["img"] = $avatarInfo->data->url;
		$savePath	     = ABSPATH.'/storage/uploads/'.$user["id"].'_photo.jpg';
		$avatar = 'storage/uploads/'.$user["id"].'_photo.jpg';
		$imageString = file_get_contents($avatarInfo->data->url);
        $save = file_put_contents($savePath,$imageString);
		if(file_exists($savePath) || $save) {
		$user["img"] = $avatar;	
		}
$keys_values = array(
"fid"=>$user["id"],
"name"=>$user["name"],
"username"=>nice_tag($user["name"]),
"email"=>$user["email"],
"local"=>"",
"country"=>"",
"email"=>$user["email"],
"bio"=>"",
"gender"=>$user["gender"],
"avatar"=>$user["img"],
"type"=>"facebook"
);
/* echo "<pre>";
var_dump($keys_values);
echo "</pre>";
exit;
*/
}

break;
case 'google':
//Initialize google by using factory pattern over main class
require_once(INC.'/google/Google/Client.php');
require_once(INC.'/google/Google/Service/Oauth2.php');
$client_key = trim(get_option('GClientID'));
$client_secret = trim(get_option('GClientSecret'));
$redirect_uri = $conf_google['return_url'];
//Call Google API
$client = new Google_Client();
$client->setApplicationName(strip_tags(get_option('site-logo-text')));
$client->setClientId($client_key);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);

$oauth2 = new Google_Service_Oauth2($client);
$client->authenticate($_GET['code']);

if ($client->getAccessToken()) {
  $token_data = $client->verifyIdToken();
}
if ($client->getAccessToken()) {
  $user = $oauth2->userinfo->get();

var_dump($user);
$keys_values = array(
"gid"=>$user->id,
"name"=> $user->name,
"username"=>nice_tag($user->name),
"email"=>$user->email,
"country"=>"",
"avatar"=>$user->picture,
"type"=>"google"  );

/*
echo "<pre>";
var_dump($keys_values);
echo "</pre>";
exit;
*/
}
break;
}
if(isset($keys_values) && is_array($keys_values)) {
$id = user::checkUser($keys_values);
if(!$id || nullval($id)) {
$xid = user::AddUser($keys_values);
user::LoginUser($xid);
if (is_user()) { redirect(site_url().'index.php');}
} elseif(intval($id) > 0) {
user::LoginUser($id);
if (is_user()) { redirect(site_url().'index.php');}
} else {
die(_lang('Error. Please go back'));
}
} else {
echo _lang('Error. Please go back');
}
} else {
echo _lang('Error. Please go back');
}
?>