<?php
/**
 * Plugin Name: Izlesene.com source
 * Plugin URI: http://phpvibe.com/
 * Description: Adds Izlesene embed source to PHPVibe
 * Version: 3.0
 * Author: PHPVibe Crew
 * Author URI: http://www.phpvibe.com
 * License: Commercial
 */
function _Izlesene($hosts = array())
{
    $hosts[] = 'izlesene';
    return $hosts;
}

function EmbedIzlesene($txt = '')
{
    global $VibeProvider, $VibeLink;
    if (isset($VibeProvider) && isset($VibeLink)) {

        if (_contains(strtolower($VibeProvider), 'izlesene')) {
            if (!nullval($VibeLink)) {
                $variable = $VibeLink;
                $variable = substr($variable, 0, strpos($variable, "#list"));
                $id = $vid->getLastNr($variable);
                if (!nullval($id)) {
                    $tembed = ' <iframe src="https://www.izlesene.com/embedplayer/' . $id . '/?showrel=0&loop=0&autoplay=1&autohide=1&showinfo=1&socialbuttons=0" allowfullscreen="true" frameborder="0"></iframe>';
                    $txt .= $tembed;
                }
                /* End link check */
            }
            /* End provider check */
        }
        /* End isset(vid) */
    }
    /* End function */
    return $txt;
}

add_filter('EmbedModify', 'EmbedIzlesene');
add_filter('vibe-video-sources', '_Izlesene');
?>
