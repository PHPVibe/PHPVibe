<?php error_reporting(E_ALL);
// Degugging?
$sttime = microtime(true);
// Security
if (!defined('in_phpvibe'))
    define('in_phpvibe', true);
// Root
if (!defined('ABSPATH'))
    define('ABSPATH', str_replace('\\', '/', dirname(__FILE__)));
//Check if installed
if (!is_readable('vibe_config.php') || is_readable('hold')) {
    echo '<div style="padding:10% 20%; display:block; color:#fff; background:#ff604f"><h1>Hold on!</h1>';
    echo '<h3> The configuration file needs editing OR/AND the "hold" file exists on your server! </h3><br />';
    echo '<a href="setup/index.php"><h2>RUN PHPVibe\'s SETUP</h2></a></strong>';
    echo '</div>';
    die();
}
//Include configuration
require_once(ABSPATH . '/vibe_config.php');
//Check session start
if (!isset($_SESSION)) {
    session_start();
}
/*** Start static serving from cache ***/
/* Kill static cache for users */
if (isset($_SESSION['user_id']) || isset($_COOKIE[md5(SITE_URL)]) || isset($_GET['action'])) {
    $killcache = true;
}
/* Serve static pages for visitors */
if (isset($killcache) && !$killcache) {
    $a = strip_tags($_SERVER['REQUEST_URI']);
    if (($a === '/') || ($a === '/index.php')) {
        $a = '/index.php';
    }
    /* Exclude specific pages */
    if ((strpos($a, 'register') == false) && (strpos($a, 'dashboard') == false) && (strpos($a, 'login') == false) && (strpos($a, 'moderator') == false) && (strpos($a, 'setup') == false) && !isset($_GET['clang'])) {
        require_once(ABSPATH . '/lib/fullcache.php');
        /* Create cache token for this page */
        $token = (isset($_SESSION['phpvibe-language'])) ? $a . $_SESSION['phpvibe-language'] : $a;
        /* Run static caching and serving */
        FullCache::Encode($token);
        FullCache::Live();
    }
}
/** End static serving from cache **/
//Vital file include
require_once("load.php");
ob_start();
// Login, maybe?
if (!is_user()) {
    //action = login, logout ; type = facebook, google
    if (!empty($_GET['action']) && $_GET['action'] == "login") {
        switch ($_GET['type']) {
            case 'facebook':
                if (get_option('allowfb') == 1) {
                    require_once(TRDS . '/facebook/autoload.php');
                    $fb = new Facebook\Facebook([
                        'app_id' => Fb_Key,
                        'app_secret' => Fb_Secret,
                        'default_graph_version' => 'v2.8',
                    ]);
                    $helper = $fb->getRedirectLoginHelper();
                    $permissions = explode(",", $conf_facebook['permissions']); // Optional permissions
                    $facebookLoginUrl = $helper->getLoginUrl($conf_facebook['redirect_uri'], $permissions);
                    //Send user to login
                    redirect($facebookLoginUrl);
                }
                break;
            case 'google':
                if (get_option('allowg') == 1) {
                    //Initialize google login

                    require_once(TRDS . '/google/Google/Client.php');

                    $client = new Google_Client();
                    $client->setClientId(trim(get_option('GClientID')));
                    $client->setClientSecret(trim(get_option('GClientSecret')));
                    $client->setRedirectUri($conf_google['return_url']);
                    $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));
                    $authUrl = $client->createAuthUrl();

                    if (!empty($authUrl)) {
                        redirect($authUrl);
                    }
                }
                break;

            default:
                //If any login system found, warn user
                echo _lang('Invalid Login system');
        }
    }
} else {
    if (!empty($_GET['action']) && $_GET['action'] == "logout") {
        //If action is logout, kill sessions
        user::clearSessionData();
        //var_dump($_COOKIE);exit;
        redirect(site_url() . "index.php");
    }
}

// Let's start the site
//$page = com();
$id_pos = null;
$router = new Router();
/* Uncomment and edit bellow if installed in a folder */
// $router->setBasePath('/video-folder'));
/* End folder check */
do_action('VibePermalinks');
$router->map('/', 'home', array('methods' => 'GET', 'filters' => array('id' => '(\d+)')));
$router->map('/payment/:section', 'payment', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
$router->map(get_option('profile-seo-url', '/profile/:name/:id/'), 'profile', array('methods' => 'GET,PUT,POST', _makeUrlArgs(get_option('profile-seo-url', '/profile/:name/:id/'))));
$router->map('/' . premiumhub . '/:section', 'premiumhub', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . videos . '/:section', 'videolist', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/images/:section', 'imageslist', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/music/:section', 'musiclist', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/music/:section', 'musiclist', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map(get_option('channel-seo-url', '/category/:name/:id/'), 'category', array('methods' => 'GET', 'filters' => _makeUrlArgs(get_option('channel-seo-url', '/category/:name/:id/'))));
$router->map(get_option('music-channel-seo-url', '/musicfilter/:name/:id/'), 'categorymusic', array('methods' => 'GET', 'filters' => _makeUrlArgs(get_option('channel-seo-url', '/category/:name/:id/'))));
$router->map(get_option('image-channel-seo-url', '/imagefilter/:name/:id/'), 'categoryimage', array('methods' => 'GET', 'filters' => _makeUrlArgs(get_option('channel-seo-url', '/category/:name/:id/'))));
$router->map('/playlist/:name/:id/:section', 'playlist', array('methods' => 'GET,POST', 'filters' => array('id' => '(\d+)', 'section' => '(.*)')));
$router->map(get_option('page-seo-url', '/read/:name/:id'), 'page', array('methods' => 'GET', 'filters' => _makeUrlArgs(get_option('page-seo-url', '/read/:name/:id'))));
$router->map('/' . me . ':section', 'manager', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
$router->map('/' . blog, 'blog', array('methods' => 'GET'));
$router->map('/' . members . '/:section', 'channels', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . playlists . '/:section', 'playlists', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . albums . '/:section', 'albums', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . album . '/:section', 'album', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . blogcat . '/:name/:id/:section', 'blogcat', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map(get_option('article-seo-url', '/read/:name/:id'), 'post', array('methods' => 'GET', 'filters' => _makeUrlArgs(get_option('article-seo-url', '/read/:name/:id'))));
$router->map('/forward/:section', 'forward', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/msg/:section', 'msg', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/conversation/:id', 'conversation', array('methods' => 'GET', 'filters' => array('id' => '(\d+)')));
$router->map('/login/:section', 'login', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
$router->map('/register/:section', 'register', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
$router->map('/' . buzz . '/:section', 'buzz', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . buzz . '/:section', 'buzz', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . thetags . '/:section', 'tag', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . show . '/:section', 'search', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . imgsearch . '/:section', 'searchimages', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . pplsearch . '/:section', 'searchppl', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/' . playlistsearch . '/:section', 'searchpaylist', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/api/:section', 'api', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
$router->map('/embed/:section', 'embed', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/feed/:section', 'feed', array('methods' => 'GET', 'filters' => array('section' => '(.*)')));
$router->map('/share/:section', 'share', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
$router->map('/' . upimage . '/:section', 'addimage', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
$router->map('/' . upmusic . '/:section', 'addmusic', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
$router->map('/' . add . '/:section', 'addvideo', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
$router->map('/dashboard/:section', 'dashboard', array('methods' => 'GET,PUT,POST', 'filters' => array('section' => '(.*)')));
/* Single video or song */
$router->map(get_option('video-seo-url', '/video/:id/:name'), 'video', array('methods' => 'GET', 'filters' => _makeUrlArgs(get_option('video-seo-url', '/video/:id/:name'))));
/* Single image */
$router->map(get_option('image-seo-url', '/image/:id/:name'), 'image', array('methods' => 'GET', 'filters' => _makeUrlArgs(get_option('image-seo-url', '/image/:id/:name'))));
//Match
$route = $router->matchCurrentRequest();
//end routing
/* include the theme functions / filters */
//Global tpl
if ($route) {
    /* Assign page from route */
    $page = $route->getTarget();
}

include_once(TPL . '/tpl.globals.php');
//If offline
if (!is_admin() && (get_option('site-offline', 0) == 1)) {
    layout('offline');
    exit();
}
/* Include public resolver */
if ($route) {
    include_once(ABSPATH . "/views/" . $route->getTarget() . ".route.php");
} else {
    include_once(ABSPATH . "/views/404.route.php");
}

//end sitewide
ob_end_flush();
//Debugging
/*
echo "<pre class=\"footerdebug\" style='text-align:center'>Time Elapsed: " . (microtime(true) - $sttime) . "s</pre>";

*/
//That's all folks!
?>
