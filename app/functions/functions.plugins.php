<?php

/**
 * The filter/plugin API is located in this file, which allows for creating filters
 * and hooking functions, and methods. The functions or methods will be run when
 * the filter is called.
 *
 * Any of the syntaxes explained in the PHP documentation for the
 * {@link http://us2.php.net/manual/en/language.pseudo-types.php#language.types.callback 'callback'}
 * type are valid.
 *
 * This API is heavily inspired by the one I implemented in Zenphoto 1.3, which was heavily inspired by the one used in WordPress.
 *
 * @author Ozh
 * @since 1.5
 */

$filters = array();
/* This global var will collect filters with the following structure:
 * $filters['hook']['array of priorities']['serialized function names']['array of ['array (functions, accepted_args)]']
 */

/**
 * Registers a filtering function
 * 
 * Typical use:
 *		add_filter('some_hook', 'function_handler_for_hook');
 *
 * @global array $filters Storage for all of the filters
 * @param string $hook the name of the YOURLS element to be filtered or YOURLS action to be triggered
 * @param callback $function_name the name of the function that is to be called.
 * @param integer $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default=10, lower=earlier execution, and functions with the same priority are executed in the order in which they were added to the filter)
 * @param int $accepted_args optional. The number of arguments the function accept (default is the number provided).
 */
function add_filter( $hook, $function_name, $priority = 10, $accepted_args = NULL, $type = 'filter' ) {
	global $filters;
	// At this point, we cannot check if the function exists, as it may well be defined later (which is OK)
	$id = filter_unique_id( $hook, $function_name, $priority );
	
	$filters[ $hook ][ $priority ][ $id ] = array(
		'function'      => $function_name,
		'accepted_args' => $accepted_args,
		'type'          => $type,
	);
	/* echo $hook.' - '.$function_name.' | '; */
}

/**
 * Hooks a function on to a specific action.
 *
 * Actions are the hooks that YOURLS launches at specific points
 * during execution, or when specific events occur. Plugins can specify that
 * one or more of its PHP functions are executed at these points, using the
 * Action API.
 *
 * @param string $hook The name of the action to which the $function_to_add is hooked.
 * @param callback $function_name The name of the function you wish to be called.
 * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
 * @param int $accepted_args optional. The number of arguments the function accept (default 1).
 */
function add_action( $hook, $function_name, $priority = 10, $accepted_args = 1 ) {
	return add_filter( $hook, $function_name, $priority, $accepted_args, 'action' );
}



/**
 * Build Unique ID for storage and retrieval.
 *
 * Simply using a function name is not enough, as several functions can have the same name when they are enclosed in classes.
 *
 * @global array $filters storage for all of the filters
 * @param string $hook hook to which the function is attached
 * @param string|array $function used for creating unique id
 * @param int|bool $priority used in counting how many hooks were applied.  If === false and $function is an object reference, we return the unique id only if it already has one, false otherwise.
 * @param string $type filter or action
 * @return string unique ID for usage as array key
 */
function filter_unique_id( $hook, $function, $priority ) {
	global $filters;

	// If function then just skip all of the tests and not overwrite the following.
	if ( is_string( $function ) )
		return $function;
	// Object Class Calling
	else if ( is_object( $function[0] ) ) {
		$obj_idx = get_class( $function[0] ) . $function[1];
		if ( !isset( $function[0]->_filters_id ) ) {
			if ( false === $priority )
				return false;
			$count = isset( $filters[ $hook ][ $priority ]) ? count( (array)$filters[ $hook ][ $priority ] ) : 0;
			$function[0]->_filters_id = $count;
			$obj_idx .= $count;
			unset( $count );
		} else
			$obj_idx .= $function[0]->_filters_id;
		return $obj_idx;
	}
	// Static Calling
	else if ( is_string( $function[0] ) )
		return $function[0].$function[1];

}

/**
 * Performs a filtering operation on a YOURLS element or event.
 *
 * Typical use:
 *
 * 		1) Modify a variable if a function is attached to hook 'hook'
 *		$var = "default value";
 *		$var = apply_filter( 'hook', $var );
 *
 *		2) Trigger functions is attached to event 'event'
 *		apply_filter( 'event' );
 *      (see do_action() )
 * 
 * Returns an element which may have been filtered by a filter.
 *
 * @global array $filters storage for all of the filters
 * @param string $hook the name of the YOURLS element or action
 * @param mixed $value the value of the element before filtering
 * @return mixed
 */
function apply_filter( $hook, $value = '' ) {
	global $filters;
	if ( !isset( $filters[ $hook ] ) )
		return $value;
	
	$args = func_get_args();
	
	// Sort filters by priority
	ksort( $filters[ $hook ] );
	
	// Loops through each filter
	reset( $filters[ $hook ] );
	do {
		foreach( (array) current( $filters[ $hook ] ) as $the_ ) {
			if ( !is_null( $the_['function'] ) ){
				$args[1] = $value;
				$count = $the_['accepted_args'];
				if ( is_null( $count ) ) {
					$_value = call_user_func_array( $the_['function'], array_slice( $args, 1 ) );
				} else {
					$_value = call_user_func_array( $the_['function'], array_slice( $args, 1, (int) $count ) );
				}
			}
			if( $the_['type'] == 'filter' )
				$value = $_value;
		}

	} while ( next( $filters[ $hook ] ) !== false );
	
	if( $the_['type'] == 'filter' )
		return $value;
}

/**
 * Alias for apply_filter because I never remember if it's _filter or _filters
 *
 * Plus, semantically, it makes more sense. There can be several filters. I should have named it
 * like this from the very start. Duh.
 *
 * @since 1.6
 *
 * @param string $hook the name of the YOURLS element or action
 * @param mixed $value the value of the element before filtering
 * @return mixed
 */
function apply_filters( $hook, $value = '' ) {
	return apply_filter( $hook, $value );
}


/**
 * Performs an action triggered by a YOURLS event.
* 
 * @param string $hook the name of the YOURLS action
 * @param mixed $arg action arguments
 */
function do_action( $hook, $arg = '' ) {
	global $actions;
	
	// Keep track of actions that are "done"
	if ( !isset( $actions ) )
		$actions = array();
	if ( !isset( $actions[ $hook ] ) )
		$actions[ $hook ] = 1;
	else
		++$actions[ $hook ];

	$args = array();
	if ( is_array( $arg ) && 1 == count( $arg ) && isset( $arg[0] ) && is_object( $arg[0] ) ) // array(&$this)
		$args[] =& $arg[0];
	else
		$args[] = $arg;
	for ( $a = 2; $a < func_num_args(); $a++ )
		$args[] = func_get_arg( $a );
	
	apply_filter( $hook, $args );
}

/**
* Retrieve the number times an action is fired.
*
* @param string $hook Name of the action hook.
* @return int The number of times action hook <tt>$hook</tt> is fired
*/
function did_action( $hook ) {
	global $actions;
	if ( !isset( $actions ) || !isset( $actions[ $hook ] ) )
		return 0;
	return $actions[ $hook ];
}

/**
 * Removes a function from a specified filter hook.
 *
 * This function removes a function attached to a specified filter hook. This
 * method can be used to remove default functions attached to a specific filter
 * hook and possibly replace them with a substitute.
 *
 * To remove a hook, the $function_to_remove and $priority arguments must match
 * when the hook was added.
 *
 * @global array $filters storage for all of the filters
 * @param string $hook The filter hook to which the function to be removed is hooked.
 * @param callback $function_to_remove The name of the function which should be removed.
 * @param int $priority optional. The priority of the function (default: 10).
 * @param int $accepted_args optional. The number of arguments the function accepts (default: 1).
 * @return boolean Whether the function was registered as a filter before it was removed.
 */
function remove_filter( $hook, $function_to_remove, $priority = 10, $accepted_args = 1 ) {
	global $filters;
	
	$function_to_remove = filter_unique_id( $hook, $function_to_remove, $priority );

	$remove = isset( $filters[ $hook ][ $priority ][ $function_to_remove ] );

	if ( $remove === true ) {
		unset ( $filters[$hook][$priority][$function_to_remove] );
		if ( empty( $filters[$hook][$priority] ) )
			unset( $filters[$hook] );
	}
	return $remove;
}


/**
 * Check if any filter has been registered for a hook.
 *
 * @global array $filters storage for all of the filters
 * @param string $hook The name of the filter hook.
 * @param callback $function_to_check optional.  If specified, return the priority of that function on this hook or false if not attached.
 * @return int|boolean Optionally returns the priority on that hook for the specified function.
 */
function has_filter( $hook, $function_to_check = false ) {
	global $filters;

	$has = !empty( $filters[ $hook ] );
	if ( false === $function_to_check || false == $has ) {
		return $has;
	}
	if ( !$idx = filter_unique_id( $hook, $function_to_check, false ) ) {
		return false;
	}

	foreach ( (array) array_keys( $filters[ $hook ] ) as $priority ) {
		if ( isset( $filters[ $hook ][ $priority ][ $idx ] ) )
			return $priority;
	}
	return false;
}

function has_action( $hook, $function_to_check = false ) {
	return has_filter( $hook, $function_to_check );
}



/**
 * Check if a file is safe for inclusion (well, "safe", no guarantee)
 *
 * @param string $file Full pathname to a file
 */
function validate_plugin_file( $file ) {
	if (
		false !== strpos( $file, '..' )
		OR
		false !== strpos( $file, './' )
		OR
		'plugin.php' !== substr( $file, -10 )	// a plugin must be named 'plugin.php'
		OR
		!is_readable( $file )
	)
		return false;
		
	return true;
}

/* Shortcodes */

function do_shortcode($text) {
	if(not_empty($text))	{
	if ( false === strpos( $text, '[' ) ) {
        return $text;
    }	
		
    return theShortcodes::parse($text);
	} 
return $text;	
}
function add_shortcode($short ='',$name ='' ) {
	if(not_empty($short) && not_empty($name) )	{
	theShortcodes::add($short, $name);
	}
}
/*
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 * https://github.com/force-components/Shortcode
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
		$trans = array( '&#91;' => '&#091;', '&#93;' => '&#093;' );
        $content = strtr( $content, $trans );
        $trans = array( '[' => '&#91;', ']' => '&#93;' );
      	$noopen = false === strpos( $content, '[' );
        $noclose = false === strpos( $content, ']' );
        if ( $noopen || $noclose ) {
            // This element does not contain shortcodes.
            if ( $noopen xor $noclose ) {
                // Need to encode stray [ or ] chars.
                $content = strtr( $element, $trans );
            }
            //continue;
        }
		
        $shortcodes = implode('|', array_map('preg_quote', array_keys(theShortcodes::$shortcode_tags)));
        $pattern    = "/(.?)\[([$shortcodes]+)(.*?)(\/)?\](?(4)|(?:(.+?)\{\/\s*\\2\s*\}))?(.?)/s";

         $content = preg_replace_callback($pattern, 'theShortcodes::_handle',  $content);
		
		
	
		return $content;

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
        //print_r($matches); 
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