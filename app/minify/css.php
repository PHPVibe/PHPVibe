<?php error_reporting(E_ALL);
// Security
if (!defined('in_phpvibe')) define('in_phpvibe', true);
// Root
if (!defined('ABSPATH')) $abs = str_replace(array('\\', '/app/minify'), array('/', ''), dirname(__FILE__));
define('ABSPATH', $abs);
//Include configuration
require_once(ABSPATH . '/vibe_config.php');
$site_link = SITE_URL;
$themefold = isset($_GET['t']) ? $_GET['t'] : "main";
$themefold = preg_replace('/(\.+\/)/', '', $themefold);
$txt = '';
if (isset($_GET['sign'])) {
    $styles = '';
    if (isset($_GET['f'])) {
        $styles = preg_replace('/(\.+\/)/', '', $_GET['f']);
    }
    $sf = preg_replace('/\W+/', '-', $styles);
    $cachedfile = ABSPATH . '/storage/minify/' . date('w-m-y') . '-' . $sf . '.css';
    if (file_exists($cachedfile)) {
        /* Serve minified from cache */
        // Enable caching
        header('Cache-Control: public');
        // Expire in one day
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
        // Set the correct MIME type, because Apache won't set it for us
        header("Content-type: text/css");
        header('Pragma: public');
        // Write everything out
        readfile($cachedfile);

        /* $txt = file_get_contents($cachedfile); echo $txt;	*/
    } else {
        $defaults = array('phpvibe', 'bootstrap.min', 'playerads', 'materialicons', 'roboto');
        $newstyles = array();
        if (!is_null($styles) && !empty($styles)) {
            $newstyles = explode('_', $styles);
        }
        $allstyles = array_unique(array_merge($newstyles, $defaults));


        foreach ($allstyles as $css) {
            $cssfile = ABSPATH . '/themes/' . $themefold . '/styles/' . trim($css) . '.css';

            //echo $cssfile;	
            if (file_exists($cssfile)) {
                $txt .= file_get_contents($cssfile);
            }

        }

        // Fix font urls 
        $txt = str_replace('fonts/', $site_link . 'themes/' . $themefold . '/styles/fonts/', $txt);
        // Fix theme image links
        $txt = str_replace('../images/', $site_link . 'themes/' . $themefold . '/images/', $txt);
        // Remove space after colons
        $txt = str_replace(': ', ':', $txt);
        // Remove whitespace
        $txt = preg_replace("/\s{2,}/", " ", $txt);
        $txt = str_replace("\n", "", $txt);
        $txt = str_replace(', ', ",", $txt);
        $txt = preg_replace('/(\/\*[\w\'\s\r\n\*\+\,\"\-\.]*\*\/)/', '$2', $txt);
        //A fix 
        $txt = str_replace('and(', 'and (', $txt);
        // Signature
        $txt = "/* Powered by the PHPVibe CMS ( PHPVibe.com ) */ " . $txt;


        // Create cache file
        if (!empty(trim($txt))) {
            $f = fopen($cachedfile, 'w');
            fwrite($f, $txt);
            fclose($f);
        }
        /*Enable GZip encoding.
        ob_start("ob_gzhandler");*/
        // Enable caching
        header('Cache-Control: public');
        // Expire in one day
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
        // Set the correct MIME type, because Apache won't set it for us
        header("Content-type: text/css");
        // Write everything out
        echo($txt);
    }
}

?>
