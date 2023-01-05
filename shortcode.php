<?php function not_empty($bla){
	return true;
}
function do_shortcode($text) {
	if(not_empty($text))	{
    return theShortcodes::parse($text);
	}
}
function add_shortcode($short ='',$name ='' ) {
	if(not_empty($short) && not_empty($name) )	{
	theShortcodes::add($short, $name);
	}
}
/* demo */ 
function foo($attributes) {
    // Extract attributes
    extract($attributes);

    // text
    if (isset($text)) $text = $text; else $text = '';

    // return
    return $text;
}
function food($attributes) {
	extract($attributes);
    // text
    if (isset($text)) $text = strtolower($text); else $text = '';

    // return
    return $text;
}
$text = '{foo text="Hello World"} {food text="Hello World"}';
add_shortcode('foo','foo');
add_shortcode('food','food');
echo do_shortcode($text);
/*
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class theShortcodes
{
    /**
     * Shortcode tags array
     *
     * @var shortcode_tags
     */
    protected static $shortcode_tags = array();

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        // Nothing here
    }

    /**
     * Add new shortcode
     *
     *  <code>
     *      function returnSiteUrl() {
     *          return 'http://example.org';
     *      }
     *
     *      // Add shortcode {siteurl}
     *      theShortcodes::add('siteurl', 'returnSiteUrl');
     *  </code>
     *
     * @param string $shortcode         Shortcode tag to be searched in content.
     * @param string $callback_function The callback function to replace the shortcode with.
     */
    public static function add($shortcode, $callback_function)
    {
        // Redefine vars
        $shortcode = (string) $shortcode;

        // Add new shortcode
        if (is_callable($callback_function)) {
            theShortcodes::$shortcode_tags[$shortcode] = $callback_function;
        }
    }

    /**
     * Remove a specific registered shortcode.
     *
     *  <code>
     *      theShortcodes::delete('shortcode_name');
     *  </code>
     *
     * @param string $shortcode Shortcode tag.
     */
    public static function delete($shortcode)
    {
        // Redefine vars
        $shortcode = (string) $shortcode;

        // Delete shortcode
        if (theShortcodes::exists($shortcode)) {
            unset(theShortcodes::$shortcode_tags[$shortcode]);
        }
    }

    /**
     * Remove all registered shortcodes.
     *
     *  <code>
     *      theShortcodes::clear();
     *  </code>
     *
     */
    public static function clear()
    {
        theShortcodes::$shortcode_tags = array();
    }

    /**
     * Check if a shortcode has been registered.
     *
     *  <code>
     *      if (theShortcodes::exists('shortcode_name')) {
     *          // do something...
     *      }
     *  </code>
     *
     * @param string $shortcode Shortcode tag.
     */
    public static function exists($shortcode)
    {
        // Redefine vars
        $shortcode = (string) $shortcode;

        // Check shortcode
        return array_key_exists($shortcode, theShortcodes::$shortcode_tags);
    }

    /**
     * Parse a string, and replace any registered shortcodes within it with the result of the mapped callback.
     *
     *  <code>
     *      $content = theShortcodes::parse($content);
     *  </code>
     *
     * @param  string $content Content
     * @return string
     */
    public static function parse($content)
    {
        if (! theShortcodes::$shortcode_tags) {
            return $content;
        }

        $shortcodes = implode('|', array_map('preg_quote', array_keys(theShortcodes::$shortcode_tags)));
        $pattern    = "/(.?)\{([$shortcodes]+)(.*?)(\/)?\}(?(4)|(?:(.+?)\{\/\s*\\2\s*\}))?(.?)/s";

        return preg_replace_callback($pattern, 'theShortcodes::_handle', $content);
    }

    /**
     * _handle()
     */
    protected static function _handle($matches)
    {
        $prefix    = $matches[1];
        $suffix    = $matches[6];
        $shortcode = $matches[2];

        // Allow for escaping shortcodes by enclosing them in {{shortcode}}
        if ($prefix == '{' && $suffix == '}') {
            return substr($matches[0], 1, -1);
        }

        $attributes = array(); // Parse attributes into into this array.

        if (preg_match_all('/(\w+) *= *(?:([\'"])(.*?)\\2|([^ "\'>]+))/', $matches[3], $match, PREG_SET_ORDER)) {
            foreach ($match as $attribute) {
                if (! empty($attribute[4])) {
                    $attributes[strtolower($attribute[1])] = $attribute[4];
                } elseif (! empty($attribute[3])) {
                    $attributes[strtolower($attribute[1])] = $attribute[3];
                }
            }
        }

        // Check if this shortcode realy exists then call user function else return empty string
        return (isset(theShortcodes::$shortcode_tags[$shortcode])) ? $prefix . call_user_func(theShortcodes::$shortcode_tags[$shortcode], $attributes, $matches[5], $shortcode) . $suffix : '';
    }
}
?>