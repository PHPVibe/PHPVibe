<?php /** Arrays with options for logins **/
$conf_facebook = array();
$conf_google = array();
//Callback url for facebook
$conf_facebook['redirect_uri'] = SITE_URL.'callback.php?type=facebook';
//Callback url for google
$conf_google['return_url'] = SITE_URL.'callback.php?type=google';
//Facebook callback fields
$conf_facebook['fields'] = 'id,name,email,first_name,gender,last_name,location,about';
//Facebook permissions(default values)
$conf_facebook['permissions'] = 'public_profile,email';


/* URL delimiter RULE for phpVibe */
define( 'url_split', '/' );

/* SEO url structure */
define( 'trending', 'trending' );
define( 'page', 'read' );
define( 'blog', 'blog' );
define( 'blogcat', 'articles' );
define( 'blogpost', 'article' );
define( 'embedcode', 'embed' );
define( 'video', 'video' );
define( 'videos', 'videos' );
define( 'premiumhub', 'premiumhub' );
define( 'channel', 'channel' );
define( 'channels', 'channels' );
define( 'playlist', 'playlist' );
define( 'album', 'album' );
define( 'playlists', 'lists' );
define( 'albums', 'albums' );
define( 'note', 'note' );
define( 'profile', 'profile' );
define( 'show', 'show' );
define( 'thetags', 'tag' );
define( 'members', 'users' );
define( 'share', 'share' );
define( 'add', 'add-video' );
define( 'upmusic', 'add-music' );
define( 'upimage', 'add-image' );
define( 'subscriptions', 'subscriptions' );
define( 'manage', 'manage' );
define( 'me', 'me' );
define( 'buzz', 'activity' );
define( 'imgsearch', 'imgsearch' );
define( 'pplsearch', 'pplsearch' );
define( 'playlistsearch', 'playlistsearch' );
// Mini video seo excerpts
define( 'mostliked', 'most-liked' );
define( 'mostviewed', 'most-viewed' );
define( 'promoted', 'featured' );
define( 'browse', 'browse' );
define( 'mostcom', 'most-commented' );

?>