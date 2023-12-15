<?php /**
 * Serialize data if needed. Inspired by WordPress
 *
 */
function maybe_serialize($data)
{
    if (is_array($data) || is_object($data))
        return serialize($data);

    if (is_serialized($data))
        return serialize($data);

    return $data;
}


/**
 * Check value to find if it was serialized. Inspired by WordPress
 *
 */
function is_serialized($data)
{
    // if it isn't a string, it isn't serialized
    if (!is_string($data))
        return false;
    $data = trim($data);
    if ('N;' == $data)
        return true;
    if (!preg_match('/^([adObis]):/', $data, $badions))
        return false;
    switch ($badions[1]) {
        case 'a' :
        case 'O' :
        case 's' :
            if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                return true;
            break;
    }
    return false;
}

/**
 * Unserialize value only if it was serialized. Inspired by WP
 *
 */
function maybe_unserialize($original)
{
    if (is_serialized($original)) // don't attempt to unserialize data that wasn't serialized going in
        return @unserialize($original);
    return $original;
}

/**
 * Alias function.  *
 */
function sanitize_keyword($keyword)
{
    return sanitize_data($keyword);
}

/**
 * Sanitize a page title. No HTML per W3C http://www.w3.org/TR/html401/struct/global.html#h-7.4.2
 *
 */
function sanitize_title($unsafe_title)
{
    $title = $unsafe_title;
    $title = strip_tags($title);
    $title = preg_replace("/\s+/", ' ', trim($title));
    return apply_filter('sanitize_title', $title, $unsafe_title);
}

/**
 * Make sure an integer is a valid integer (PHP's intval() limits to too small numbers)
 *
 */
function sanitize_int($in)
{
    return (substr(preg_replace('/[^0-9]/', '', strval($in)), 0, 20));
}

/**
 * Perform a replacement while a string is found, eg $subject = '%0%0%0DDD', $search ='%0D' -> $result =''
 *
 */
function deep_replace($search, $subject)
{
    $found = true;
    while ($found) {
        $found = false;
        foreach ((array)$search as $val) {
            while (strpos($subject, $val) !== false) {
                $found = true;
                $subject = str_replace($val, '', $subject);
            }
        }
    }

    return $subject;
}

/**
 * Sanitize an IP address
 *
 */
function sanitize_ip($ip)
{
    return preg_replace('/[^0-9a-fA-F:., ]/', '', $ip);
}

/**
 * Make sure a date is m(m)/d(d)/yyyy, return false otherwise
 *
 */
function sanitize_date($date)
{
    if (!preg_match('!^\d{1,2}/\d{1,2}/\d{4}$!', $date)) {
        return false;
    }
    return $date;
}

/**
 * Sanitize a date for SQL search. Return false if malformed input.
 *
 */
function sanitize_date_for_sql($date)
{
    if (!sanitize_date($date))
        return false;
    return date('Y-m-d', strtotime($date));
}


/**
 * Return trimmed string
 *
 */
function trim_long_string($string, $length = 60, $append = '[...]')
{
    $newstring = $string;
    if (function_exists('mb_substr')) {
        if (mb_strlen($newstring) > $length) {
            $newstring = mb_substr($newstring, 0, $length - mb_strlen($append), 'UTF-8') . $append;
        }
    } else {
        if (strlen($newstring) > $length) {
            $newstring = substr($newstring, 0, $length - strlen($append)) . $append;
        }
    }
    return apply_filter('trim_long_string', $newstring, $string, $length, $append);
}

/**
 * Check if a string seems to be UTF-8. Inspired by Wordpress
 *
 */
function seems_utf8($str)
{
    $length = strlen($str);
    for ($i = 0; $i < $length; $i++) {
        $c = ord($str[$i]);
        if ($c < 0x80) $n = 0; # 0bbbbbbb
        elseif (($c & 0xE0) == 0xC0) $n = 1; # 110bbbbb
        elseif (($c & 0xF0) == 0xE0) $n = 2; # 1110bbbb
        elseif (($c & 0xF8) == 0xF0) $n = 3; # 11110bbb
        elseif (($c & 0xFC) == 0xF8) $n = 4; # 111110bb
        elseif (($c & 0xFE) == 0xFC) $n = 5; # 1111110b
        else return false; # Does not match any model
        for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
            if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                return false;
        }
    }
    return true;
}


/**
 * Checks for invalid UTF8 in a string. Inspired by WP
 *
 * @param string $string The text which is to be checked.
 * @param boolean $strip Optional. Whether to attempt to strip out invalid UTF8. Default is false.
 * @return string The checked text.
 * @since 1.6
 *
 */
function check_invalid_utf8($string, $strip = false)
{
    $string = (string)$string;

    if (0 === strlen($string)) {
        return '';
    }

    // Check for support for utf8 in the installed PCRE library once and store the result in a static
    static $utf8_pcre;
    if (!isset($utf8_pcre)) {
        $utf8_pcre = @preg_match('/^./u', 'a');
    }
    // We can't demand utf8 in the PCRE installation, so just return the string in those cases
    if (!$utf8_pcre) {
        return $string;
    }

    // preg_match fails when it encounters invalid UTF8 in $string
    if (1 === @preg_match('/^./us', $string)) {
        return $string;
    }

    // Attempt to strip the bad chars if requested (not recommended)
    if ($strip && function_exists('iconv')) {
        return iconv('utf-8', 'utf-8', $string);
    }

    return '';
}

/**
 * Converts a number of special characters into their HTML entities. Inspired by Wordpress
 *
 * Specifically deals with: &, <, >, ", and '.
 *
 * $quote_style can be set to ENT_COMPAT to encode " to
 * &quot;, or ENT_QUOTES to do both. Default is ENT_NOQUOTES where no quotes are encoded.
 *
 * @param string $string The text which is to be encoded.
 * @param mixed $quote_style Optional. Converts double quotes if set to ENT_COMPAT, both single and double if set to ENT_QUOTES or none if set to ENT_NOQUOTES. Also compatible with old values; converting single quotes if set to 'single', double if set to 'double' or both if otherwise set. Default is ENT_NOQUOTES.
 * @param string $charset Optional. The character encoding of the string. Default is false.
 * @param boolean $double_encode Optional. Whether to encode existing html entities. Default is false.
 * @return string The encoded text with HTML entities.
 * @since 1.6
 *
 */

function vibe_specialchars($string, $quote_style = ENT_NOQUOTES, $double_encode = false)
{
    $string = (string)$string;

    if (0 === strlen($string))
        return '';

    // Don't bother if there are no specialchars - saves some processing
    if (!preg_match('/[&<>"\']/', $string))
        return $string;

    // Account for the previous behaviour of the function when the $quote_style is not an accepted value
    if (empty($quote_style))
        $quote_style = ENT_NOQUOTES;
    elseif (!in_array($quote_style, array(0, 2, 3, 'single', 'double'), true))
        $quote_style = ENT_QUOTES;

    $charset = 'UTF-8';

    $_quote_style = $quote_style;

    if ($quote_style === 'double') {
        $quote_style = ENT_COMPAT;
        $_quote_style = ENT_COMPAT;
    } elseif ($quote_style === 'single') {
        $quote_style = ENT_NOQUOTES;
    }

    // Handle double encoding ourselves
    if ($double_encode) {
        $string = @htmlspecialchars($string, $quote_style, $charset);
    } else {
        // Decode &amp; into &
        $string = specialchars_decode($string, $_quote_style);

        // Guarantee every &entity; is valid or re-encode the &
        $string = kses_normalize_entities($string);

        // Now re-encode everything except &entity;
        $string = preg_split('/(&#?x?[0-9a-z]+;)/i', $string, -1, PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 0; $i < count($string); $i += 2)
            $string[$i] = @htmlspecialchars($string[$i], $quote_style, $charset);

        $string = implode('', $string);
    }

    // Backwards compatibility
    if ('single' === $_quote_style)
        $string = str_replace("'", '&#039;', $string);

    return $string;
}


/**
 * Converts a number of HTML entities into their special characters. Inspired by Wordpress
 *
 * Specifically deals with: &, <, >, ", and '.
 *
 * $quote_style can be set to ENT_COMPAT to decode " entities,
 * or ENT_QUOTES to do both " and '. Default is ENT_NOQUOTES where no quotes are decoded.
 *
 * @param string $string The text which is to be decoded.
 * @param mixed $quote_style Optional. Converts double quotes if set to ENT_COMPAT, both single and double if set to ENT_QUOTES or none if set to ENT_NOQUOTES. Also compatible with old _wp_specialchars() values; converting single quotes if set to 'single', double if set to 'double' or both if otherwise set. Default is ENT_NOQUOTES.
 * @return string The decoded text without HTML entities.
 * @since 1.6
 *
 */
function specialchars_decode($string, $quote_style = ENT_NOQUOTES)
{
    $string = (string)$string;

    if (0 === strlen($string)) {
        return '';
    }

    // Don't bother if there are no entities - saves a lot of processing
    if (strpos($string, '&') === false) {
        return $string;
    }

    // Match the previous behaviour of _wp_specialchars() when the $quote_style is not an accepted value
    if (empty($quote_style)) {
        $quote_style = ENT_NOQUOTES;
    } elseif (!in_array($quote_style, array(0, 2, 3, 'single', 'double'), true)) {
        $quote_style = ENT_QUOTES;
    }

    // More complete than get_html_translation_table( HTML_SPECIALCHARS )
    $single = array('&#039;' => '\'', '&#x27;' => '\'');
    $single_preg = array('/&#0*39;/' => '&#039;', '/&#x0*27;/i' => '&#x27;');
    $double = array('&quot;' => '"', '&#034;' => '"', '&#x22;' => '"');
    $double_preg = array('/&#0*34;/' => '&#034;', '/&#x0*22;/i' => '&#x22;');
    $others = array('&lt;' => '<', '&#060;' => '<', '&gt;' => '>', '&#062;' => '>', '&amp;' => '&', '&#038;' => '&', '&#x26;' => '&');
    $others_preg = array('/&#0*60;/' => '&#060;', '/&#0*62;/' => '&#062;', '/&#0*38;/' => '&#038;', '/&#x0*26;/i' => '&#x26;');

    if ($quote_style === ENT_QUOTES) {
        $translation = array_merge($single, $double, $others);
        $translation_preg = array_merge($single_preg, $double_preg, $others_preg);
    } elseif ($quote_style === ENT_COMPAT || $quote_style === 'double') {
        $translation = array_merge($double, $others);
        $translation_preg = array_merge($double_preg, $others_preg);
    } elseif ($quote_style === 'single') {
        $translation = array_merge($single, $others);
        $translation_preg = array_merge($single_preg, $others_preg);
    } elseif ($quote_style === ENT_NOQUOTES) {
        $translation = $others;
        $translation_preg = $others_preg;
    }

    // Remove zero padding on numeric entities
    $string = preg_replace(array_keys($translation_preg), array_values($translation_preg), $string);

    // Replace characters according to translation table
    return strtr($string, $translation);
}


/**
 * Escaping for HTML blocks. Inspired by WP
 *
 * @param string $text
 * @return string
 * @since 1.6
 *
 */
function esc_html($text)
{
    $safe_text = check_invalid_utf8($text);
    $safe_text = vibe_specialchars($safe_text, ENT_QUOTES);
    return apply_filters('esc_html', $safe_text, $text);
}

/**
 * Escaping for HTML attributes.  Inspired by WP
 *
 * @param string $text
 * @return string
 * @since 1.6
 *
 */
function esc_attr($text)
{
    $safe_text = check_invalid_utf8($text);
    $safe_text = vibe_specialchars($safe_text, ENT_QUOTES);
    return apply_filters('esc_attr', $safe_text, $text);
}

/**
 * Escaping for textarea values. Inspired by Wordpress
 *
 * @param string $text
 * @return string
 * @since 1.6
 *
 */
function esc_textarea($text)
{
    $safe_text = vibe_specialchars($text, ENT_QUOTES);
    return apply_filters('esc_textarea', $safe_text, $text);
}

/**
 * Escape single quotes, htmlspecialchar " < > &, and fix line endings. Inspired by Wordpress
 *
 * Escapes text strings for echoing in JS. It is intended to be used for inline JS
 * (in a tag attribute, for example onclick="..."). Note that the strings have to
 * be in single quotes. The filter 'js_escape' is also applied here.
 *
 * @param string $text The text to be escaped.
 * @return string Escaped text.
 * @since 1.6
 *
 */
function esc_js($text)
{
    $safe_text = check_invalid_utf8($text);
    $safe_text = vibe_specialchars($safe_text, ENT_COMPAT);
    $safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
    $safe_text = str_replace("\r", '', $safe_text);
    $safe_text = str_replace("\n", '\\n', addslashes($safe_text));
    return apply_filters('esc_js', $safe_text, $text);
}

function stripslashes_deep($value)
{
    if (is_array($value)) {
        $value = array_map('stripslashes_deep', $value);
    } elseif (is_object($value)) {
        $vars = get_object_vars($value);
        foreach ($vars as $key => $data) {
            $value->{$key} = stripslashes_deep($data);
        }
    } elseif (is_string($value)) {
        $value = stripslashes($value);
    }

    return $value;
}

/**
 * Adds backslashes before letters and before a number at the start of a string. Inspired by Wordpress
 *
 * @param string $string Value to which backslashes will be added.
 * @return string String with backslashes inserted.
 * @since 1.6
 *
 */
function backslashit($string)
{
    $string = preg_replace('/^([0-9])/', '\\\\\\\\\1', $string);
    $string = preg_replace('/([a-z])/i', '\\\\\1', $string);
    return $string;
}

//This function strips everything...no questions asked
//...except for some few safe html tags
function antixss_light($text)
{
    $text = nl2br($text);
    $text = strtr($text, array(
        "\r\n" => "<br>",
        "\\r\\n" => "<br>",
        "\r" => "<br>",
        "\n" => "<br>",
        "\t" => " "));
    $text = _html(stripslashes($text));
    /* Maybe overkill ... but better safe */
    // Remove any attribute starting with "on" or xmlns
    $text = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+[>\b]?#iu', '$1>', $text);
    // Remove javascript: and vbscript: protocols
    $text = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $text);
    $text = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $text);
    $text = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $text);
    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $text = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $text);
    $text = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $text);
    $text = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $text);
    // Remove tags
    $text = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $text);

    // Fail safes
    //Remove external scripts
    $search = array(
        '#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#',
        '@<head[^>]*?>.*?</head>@siu',
        '@<style[^>]*?>.*?</style>@siu',
        '@<script[^>]*?.*?</script>@siu',
        '@<object[^>]*?.*?</object>@siu',
        '@<embed[^>]*?.*?</embed>@siu',
        '@<applet[^>]*?.*?</applet>@siu',
        '@<noframes[^>]*?.*?</noframes>@siu',
        '@<noscript[^>]*?.*?</noscript>@siu',
        '@<noembed[^>]*?.*?</noembed>@siu',
        '/style=\\"[^\\"]*\\"/',
        '/<style\\b[^>]*>(.*?)<\\/style>/s',
        '|<style\b[^>]*>(.*?)</style>|s',
        '#</*\w+:\w[^>]*+>#i',
        '@<(script|style)[^>]*?>.*?</\\1>@si',
        '@<script[^>]*?>.*?</script>@si',
        '@<style[^>]*?>.*?</style>@siU',
        '@<![\s\S]*?--[ \t\n\r]*>@'
    );
    $tx_output = preg_replace($search, '', $text);
    //Deep remove the rest
    $injections = array('onerror', '<body', '<Body', '&amp;body', '&amp;Body', 'prompt(', 'src=', '<script', 'iframe', '<object', 'confirm("', 'onLoad=', 'onLoad=confirm', 'onload=confirm', 'onload=', 'applet', '<embed', 'onblur', ');>', 'onchange', 'onclick', 'ondblclick', 'onfocus', 'onkeydown', 'onkeypress', 'onkeyup', 'onload', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onreset', 'onselect', 'onsubmit', 'onunload', '<src', '<img src', 'onerror', 'prompt(', 'alert(', 'document.body.innerHTML', 'document.body', 'document.title', '<!--', 'innerHTML', '<style', '<style>');
    $output = str_replace($injections, '', $text);
    if (function_exists('str_ireplace')) {
        $output = str_ireplace($injections, '', $output);
    }
    $output = str_replace('n&lt;', '&lt;', $output);
    $output = strip_tags($output, '<br><br /><b></b><strong></strong><em></em><i></i><a></a><pre></pre><code></code><hr>');

    return addslashes($output);
}

// Global wrapper, using the vibe_clean_xss class
//and the light antixss class
function sanitize_data($data)
{
    $data = antixss_light($data);
    $data = check_invalid_utf8($data);
    $data = vibe_specialchars($data, ENT_COMPAT);
    return $data;
}

function print_data($data, $ch = false)
{
    if (!$ch)
        return specialchars_decode($data);
    echo specialchars_decode($data);
}

function _html($txt)
{
    if (not_empty($txt)) {
        $txt = nl2br(stripslashes($txt));
        $purge = array("\r\n" => "<br>", "\\r\\n" => "<br>", "\r" => "<br>", "\n" => "<br>", ";n&lt" => ";&lt", "&gt;n" => "&gt;", "&lt;br /&gt;n&lt;br /&gt;" => "<br>", "<br /><br />" => "<br>", "<br />n<br />" => "<br>", ">n<" => "><", "<br />\n<br />" => "<br>", "\t" => " ");
        $txt = strtr($txt, $purge);
        $txt = html_entity_decode($txt, ENT_QUOTES, 'UTF-8');
        return strtr($txt, $purge);
    }
    return '';
}

function _pjs($txt)
{
    if (not_empty($txt)) {
        return stripslashes(html_entity_decode($txt, ENT_QUOTES, 'UTF-8'));
    }
    return '';
}

function escape($str)
{
    global $db;
    return rawurldecode($db->escape($str));
}

function toDb($data)
{
    if (not_empty($data)) {
        $data = escape(nl2br($data));
        $data = sanitize_data($data);
        return $data;
    }
    return '';
}

function makeLn($text)
{
    $text = preg_replace('#(<br */?>\s*)+#i', '<br />', $text);
    return linkify($text);
}

function linkify($value, $protocols = array('http', 'mail'), array $attributes = array("rel" => "nofollow", "target" => "_blank"))
{
    // Link attributes
    $attr = "";
    foreach ($attributes as $key => $val) {
        $attr .= ' ' . $key . '="' . htmlentities($val) . '"';
    }

    $links = array();

    // Extract existing links and tags
    $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
        return '<' . array_push($links, $match[1]) . '>';
    }, $value);

    // Extract text links for each protocol
    foreach ((array)$protocols as $protocol) {
        switch ($protocol) {
            case 'http':
            case 'https':
                $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                    if ($match[1]) $protocol = $match[1];
                    $link = $match[2] ?: $match[3];
                    return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>';
                }, $value);
                break;
            case 'mail':
                $value = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($match) use (&$links, $attr) {
                    return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>';
                }, $value);
                break;
            case 'twitter':
                $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) {
                    return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1] . "\">{$match[0]}</a>") . '>';
                }, $value);
                break;
            default:
                $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                    return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>';
                }, $value);
                break;
        }
    }

    // Insert all link
    return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) {
        return $links[$match[1] - 1];
    }, $value);
}
