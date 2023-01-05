<?php error_reporting(0);
ini_set('memory_limit', '892M');
require_once("../load.php");
$txt = '';

$curry = function ($f) {
    $args = array_slice(func_get_args(), 1);
    return function() use ($f, $args) { return call_user_func_array($f, array_merge($args, func_get_args())); };
};

$prog1 = function ($ret) {
    if ($ret instanceof Closure)
        $ret = $ret();
    $arguments = func_get_args();
    for ($i = 1, $n = func_num_args(); --$n > 0; ++$i)
        $arguments[$i]();
    return $ret;
};

$array_to_hash = function ($a) {
    return array_fill_keys($a, true);
};

$characters = function ($str, $no_unicode = true) {
    if ($no_unicode) return str_split($str);
    return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
};

if (!isset($warn)) $warn = 'error_log';

/* -----[ Tokenizer (constants) ]----- */

$KEYWORDS = $array_to_hash([
    "break",
    "case",
    "catch",
    "const",
    "continue",
    "debugger",
    "default",
    "delete",
    "do",
    "else",
    "finally",
    "for",
    "function",
    "if",
    "in",
    "instanceof",
    "new",
    "return",
    "switch",
    "throw",
    "try",
    "typeof",
    "var",
    "void",
    "while",
    "with"
]);

$RESERVED_WORDS = $array_to_hash([
    "abstract",
    "boolean",
    "byte",
    "char",
    "class",
    "double",
    "enum",
    "export",
    "extends",
    "final",
    "float",
    "goto",
    "implements",
    "import",
    "int",
    "interface",
    "long",
    "native",
    "package",
    "private",
    "protected",
    "public",
    "short",
    "static",
    "super",
    "synchronized",
    "throws",
    "transient",
    "volatile"
]);

$KEYWORDS_BEFORE_EXPRESSION = $array_to_hash([
    "return",
    "new",
    "delete",
    "throw",
    "else",
    "case"
]);

$KEYWORDS_ATOM = $array_to_hash([
    "false",
    "null",
    "true",
    "undefined"
]);

$OPERATOR_CHARS = $array_to_hash($characters("+-*&%=<>!?|~^"));

$RE_HEX_NUMBER = '/^0x[0-9a-f]+$/i';
$RE_OCT_NUMBER = '/^0[0-7]+$/';
$RE_DEC_NUMBER = '/^\d*\.?\d*(?:e[+-]?\d*(?:\d\.?|\.?\d)\d*)?$/i';

$OPERATORS = $array_to_hash([
    "in",
    "instanceof",
    "typeof",
    "new",
    "void",
    "delete",
    "++",
    "--",
    "+",
    "-",
    "!",
    "~",
    "&",
    "|",
    "^",
    "*",
    "/",
    "%",
    ">>",
    "<<",
    ">>>",
    "<",
    ">",
    "<=",
    ">=",
    "==",
    "===",
    "!=",
    "!==",
    "?",
    "=",
    "+=",
    "-=",
    "/=",
    "*=",
    "%=",
    ">>=",
    "<<=",
    ">>>=",
    "|=",
    "^=",
    "&=",
    "&&",
    "||"
]);

//$WHITESPACE_CHARS = $array_to_hash($characters(json_decode('" \u00a0\n\r\t\f\u000b\u200b\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u202f\u205f\u3000\uFEFF"'), false));
$WHITESPACE_CHARS = $array_to_hash($characters(" \n\r\t\f\v"));

$PUNC_BEFORE_EXPRESSION = $array_to_hash($characters("[{(,.;:"));

$PUNC_CHARS = $array_to_hash($characters("[]{}(),;:"));

$REGEXP_MODIFIERS = $array_to_hash($characters("gmsiy"));

/* -----[ Parser (constants) ]----- */

$UNARY_PREFIX = $array_to_hash([
    "typeof",
    "void",
    "delete",
    "--",
    "++",
    "!",
    "~",
    "-",
    "+"
]);

$UNARY_POSTFIX = $array_to_hash([ "--", "++" ]);

$ASSIGNMENT = call_user_func_array(function($a, $ret, $i){
    while ($i < count($a)) {
        $ret[$a[$i]] = substr($a[$i], 0, strlen($a[$i]) - 1);
        $i++;
    }
    return $ret;
}, [
    ["+=", "-=", "/=", "*=", "%=", ">>=", "<<=", ">>>=", "|=", "^=", "&="],
    [ "=" => true ],
    0
]);

$PRECEDENCE = call_user_func_array(function($a, $ret){
    for ($i = 0, $n = 1; $i < count($a); ++$i, ++$n) {
        $b = $a[$i];
        for ($j = 0; $j < count($b); ++$j) {
            $ret[$b[$j]] = $n;
        }
    }
    return $ret;
}, [
    [
        ["||"],
        ["&&"],
        ["|"],
        ["^"],
        ["&"],
        ["==", "===", "!=", "!=="],
        ["<", ">", "<=", ">=", "in", "instanceof"],
        [">>", "<<", ">>>"],
        ["+", "-"],
        ["*", "/", "%"]
    ],
    []
]);

$STATEMENTS_WITH_LABELS = $array_to_hash([ "for", "do", "while", "switch" ]);

$ATOMIC_START_TOKEN = $array_to_hash([ "atom", "num", "string", "regexp", "name" ]);

/* -----[ Tokenizer ]----- */

$UNICODE = ['letter' => '/\p{L}|\p{Nl}/u', 'combining_mark' => '/\p{Mn}|\p{Mc}/u', 'connector_punctuation' => '/\p{Pc}/u', 'digit' => '/\p{Nd}/u'];
// Drop Unicode support

$is_identifier_start = function ($ch) {
    //return (bool)preg_match('/[$_A-Za-z]/', $ch);
    return $ch === "$" || $ch === "_" || ctype_alpha($ch);
};

$is_identifier_char = function ($ch) {
    //return (bool)preg_match('/[$_A-Za-z0-9]/', $ch);
    return $ch === "$" || $ch === "_" || ctype_alnum($ch);
};

$is_unicode_identifier_char = function ($ch) use ($UNICODE) {
    return $ch === "$" || $ch === "_" || preg_match($UNICODE['letter'], $ch)
        || preg_match($UNICODE['combining_mark'], $ch)
        || preg_match($UNICODE['digit'], $ch)
        || preg_match($UNICODE['connector_punctuation'], $ch)
        || $ch === json_decode('"\u200c"') // zero-width non-joiner <ZWNJ>
        || $ch === json_decode('"\u200d"') // zero-width joiner <ZWJ> (in my ECMA-262 PDF, this is also 200c)
    ;
};

$parse_js_number = function ($num) use ($RE_HEX_NUMBER, $RE_OCT_NUMBER/*, $RE_DEC_NUMBER*/) {
    if (preg_match($RE_HEX_NUMBER, $num)) {
        return hexdec($num);
    } elseif (preg_match($RE_OCT_NUMBER, $num)) {
        return octdec($num);
    } elseif (is_numeric($num)) return 1 * $num;
    return NAN;
};

$js_error = function ($message, $line, $col, $pos) {
    $line = $line + 1;
    $col = $col + 1;
    $pos = $pos + 1;
    $message = $message . " (line: " . $line . ", col: " . $col . ", pos: " . $pos . ")";
    throw new Exception($message);
};

$is_token = function ($token, $type, $val) {
    return $token['type'] === $type && ($val === null || $token['value'] === $val);
};

$EX_EOF = new Exception();

$tokenizer = function ($TEXT) use (
    $KEYWORDS,
    $KEYWORDS_BEFORE_EXPRESSION,
    $KEYWORDS_ATOM,
    $OPERATOR_CHARS,
    $OPERATORS,
    $WHITESPACE_CHARS,
    $PUNC_BEFORE_EXPRESSION,
    $PUNC_CHARS,
    $UNARY_POSTFIX,
    $EX_EOF,
    $warn,
    $is_identifier_start,
    $is_identifier_char,
    $is_unicode_identifier_char,
    $parse_js_number,
    $js_error
) {

    $S = [
        'text'            => preg_replace(['/\r\n?|[\n\x{2028}\x{2029}]/u', '/^\x{FEFF}/u'], ["\n", ''], $TEXT),
        'pos'             => 0,
        'tokpos'          => 0,
        'line'            => 0,
        'tokline'         => 0,
        'col'             => 0,
        'tokcol'          => 0,
        'newline_before'  => false,
        'regex_allowed'   => false,
        'comments_before' => []
    ];

    $peek = function () use (&$S) { return isset($S['text'][$S['pos']]) ? $S['text'][$S['pos']] : ''; };

    $next = function ($signal_eof = false, $in_string = false) use (&$S, $EX_EOF) {
        //$ch = substr($S['text'], $S['pos']++, 1);
        $ch = isset($S['text'][$S['pos']]) ? $S['text'][$S['pos']++] : '';
        if ($signal_eof && $ch === '')
            throw $EX_EOF;
        if ($ch === "\n") {
            $S['newline_before'] = $S['newline_before'] || !$in_string;
            ++$S['line'];
            $S['col'] = 0;
        } else {
            ++$S['col'];
        }
        return $ch;
    };

    $find = function ($what, $signal_eof = false) use (&$S, $EX_EOF) {
        $pos = strpos($S['text'], $what, $S['pos']);
        if ($signal_eof && $pos === false) throw $EX_EOF;
        return $pos;
    };

    $start_token = function () use (&$S) {
        $S['tokline'] = $S['line'];
        $S['tokcol'] = $S['col'];
        $S['tokpos'] = $S['pos'];
    };

    $token = function ($type, $value = null, $is_comment = false) use (
        &$S,
        $UNARY_POSTFIX,
        $KEYWORDS_BEFORE_EXPRESSION,
        $PUNC_BEFORE_EXPRESSION
    ) {
        $S['regex_allowed'] = (($type === "operator" && !isset($UNARY_POSTFIX[$value])) || 
                           ($type === "keyword" && isset($KEYWORDS_BEFORE_EXPRESSION[$value])) || 
                           ($type === "punc" && isset($PUNC_BEFORE_EXPRESSION[$value])));
        $ret = [
            'type'   => $type,
            'value'  => $value,
            'line'   => $S['tokline'],
            'col'    => $S['tokcol'],
            'pos'    => $S['tokpos'],
            'endpos' => $S['pos'],
            'nlb'    => $S['newline_before']
        ];
        if (!$is_comment) {
            $ret['comments_before'] = $S['comments_before'];
            $S['comments_before'] = [];
            // make note of any newlines in the comments that came before
            for ($i = 0, $len = count($ret['comments_before']); $i < $len; $i++) {
                $ret['nlb'] = $ret['nlb'] || $ret['comments_before'][$i]['nlb'];
            }
        }
        $S['newline_before'] = false;
        return $ret;
    };

    $read_while = function ($pred) use ($peek, $next) {
        $ret = ''; $ch = $peek(); $i = 0;
        while ($ch !== '' && $pred($ch, $i++)) {
            $ret .= $next();
            $ch = $peek();
        }
        return $ret;
    };

    $parse_error = function ($err) use (&$S, $js_error) {
        $js_error($err, $S['tokline'], $S['tokcol'], $S['tokpos']);
    };

    $read_num = function ($prefix = '') use (
        $read_while,
        $parse_js_number,
        $token,
        $parse_error
    ) {
        $has_e = false; $after_e = false; $has_x = false; $has_dot = $prefix === ".";
        $num = $read_while(function ($ch, $i) use (
            $prefix,
            &$has_e,
            &$after_e,
            &$has_x,
            &$has_dot
        ) {
            if ($ch === "x" || $ch === "X") {
                if ($has_x) return false;
                return $has_x = true;
            }
            if (!$has_x && ($ch === "E" || $ch === "e")) {
                if ($has_e) return false;
                return $has_e = $after_e = true;
            }
            if ($ch === "-") {
                if ($after_e || ($i === 0 && !$prefix)) return true;
                return false;
            }
            if ($ch === "+") return $after_e;
            $after_e = false;
            if ($ch === ".") {
                if (!$has_dot && !$has_x && !$has_e)
                    return $has_dot = true;
                return false;
            }
            return ctype_xdigit($ch);
        });
        if ($prefix)
            $num = $prefix . $num;
        $valid = $parse_js_number($num);
        if (!is_nan($valid)) {
            return $token("num", $valid);
        } else {
            $parse_error("Invalid syntax: " . $num);
        }
    };

    $hex_bytes = function ($n) use ($next, $parse_error) {
        for ($num = ''; $n > 0; --$n) $num .= $next(true);
        if (!ctype_xdigit($num)) $parse_error("Invalid hex-character pattern in string");
        return $num;
    };

    $read_escaped_char = function ($in_string = false) use ($next, $hex_bytes) {
        $ch = $next(true, $in_string);
        switch ($ch) {
          case "n" : return "\n";
          case "r" : return "\r";
          case "t" : return "\t";
          case "b" : return chr(8);
          case "v" : return "\v";
          case "f" : return "\f";
          case "0" : return "\0";
          case "x" : return json_decode('"\u00' . $hex_bytes(2) . '"');
          case "u" : return json_decode('"\u'   . $hex_bytes(4) . '"');
          case "\n": return "";
          default  : return $ch;
        }
    };

    $with_eof_error = function ($eof_error, $cont) use ($EX_EOF, $parse_error) {
        try {
            return $cont();
        } catch (Exception $ex) {
            if ($ex === $EX_EOF) $parse_error($eof_error);
            else throw $ex;
        }
    };

    $read_string = function () use (
        $EX_EOF,
        $next,
        $token,
        $read_while,
        $read_escaped_char,
        $with_eof_error
    ) {
        return $with_eof_error("Unterminated string constant", function () use (
            $EX_EOF,
            $next,
            $token,
            $read_while,
            $read_escaped_char
        ) {
            $quote = $next(); $ret = "";
            for (;;) {
                $ch = $next(true);
                if ($ch === "\\") {
                    // read OctalEscapeSequence (XXX: deprecated if "strict mode")
                    // https://github.com/mishoo/UglifyJS/issues/178
                    $octal_len = 0;
                    $ch = $read_while(function ($ch) use (&$octal_len, &$first) {
                        if ($ch >= "0" && $ch <= "7") {
                            if (!isset($first)) {
                                $first = $ch;
                                return ++$octal_len;
                            }
                            elseif ($first <= "3" && $octal_len <= 2) return ++$octal_len;
                            elseif ($first >= "4" && $octal_len <= 1) return ++$octal_len;
                        }
                        return false;
                    });
                    if ($octal_len > 0) $ch = utf8_encode(chr(octdec($ch)));
                    else $ch = $read_escaped_char(true);
                }
                elseif ($ch === $quote) break;
                elseif ($ch === "\n") throw $EX_EOF;
                $ret .= $ch;
            }
            return $token("string", $ret);
        });
    };

    $read_line_comment = function () use (&$S, $next, $find, $token) {
        $next();
        $i = $find("\n");
        if ($i === false) {
            $ret = substr($S['text'], $S['pos']);
            $S['pos'] = strlen($S['text']);
        } else {
            $ret = substr($S['text'], $S['pos'], $i - $S['pos']);
            $S['pos'] = $i;
        }
        return $token("comment1", $ret, true);
    };

    $read_multiline_comment = function () use (&$S, $next, $find, $token, $with_eof_error, $warn) {
        $next();
        return $with_eof_error("Unterminated multiline comment", function () use (&$S, $find, $token, $warn) {
            $i = $find("*/", true);
            $text = substr($S['text'], $S['pos'], $i - $S['pos']);
            $S['pos'] = $i + 2;
            $S['line'] += count(explode("\n", $text)) - 1;
            $S['newline_before'] = $S['newline_before'] || strpos($text, "\n") !== false;

            // https://github.com/mishoo/UglifyJS/issues/#issue/100
            if (strncasecmp($text, '@cc_on', 6) === 0) {
                $warn("WARNING: at line " . $S['line']);
                $warn("*** Found \"conditional comment\": " . $text);
                $warn("*** UglifyJS DISCARDS ALL COMMENTS.  This means your code might no longer work properly in Internet Explorer.");
            }

            return $token("comment2", $text, true);
        });
    };

    $read_name = function () use (
        $KEYWORDS,
        $peek,
        $next,
        $is_identifier_char,
        $is_unicode_identifier_char,
        $parse_error,
        $read_escaped_char
    ) {
        $backslash = false; $name = ''; $escaped = false; $ch = $peek();
        while ($ch !== '') {
            if (!$backslash) {
                if ($ch === "\\") { $escaped = true; $backslash = true; $next(); }
                elseif ($is_identifier_char($ch)) $name .= $next();
                else break;
            }
            else {
                if ($ch !== "u") $parse_error("Expecting UnicodeEscapeSequence -- uXXXX");
                $ch = $read_escaped_char();
                if (!$is_unicode_identifier_char($ch)) {
                    $ord = ord($ch);
                    if ($ord > 127) $ord = hexdec(json_encode($ch));
                    $parse_error("Unicode char: " . $ord . " is not valid in identifier");
                }
                $name .= $ch;
                $backslash = false;
            }
            $ch = $peek();
        }
        if ($escaped && isset($KEYWORDS[$name])) {
            $hex = strtoupper(dechex(ord($name)));
            $name = "\\u" . substr("0000", strlen($hex)) . $hex . substr($name, 1);
        }
        return $name;
    };

    $read_regexp = function ($regexp) use ($next, $token, $read_name, $with_eof_error) {
        return $with_eof_error("Unterminated regular expression", function () use ($regexp, $next, $token, $read_name) {
            $prev_backslash = false; $in_class = false;
            while (($ch = $next(true)) !== '') if ($prev_backslash) {
                $regexp .= "\\" . $ch;
                $prev_backslash = false;
            } elseif ($ch === "[") {
                $in_class = true;
                $regexp .= $ch;
            } elseif ($ch === "]" && $in_class) {
                $in_class = false;
                $regexp .= $ch;
            } elseif ($ch === "/" && !$in_class) {
                break;
            } elseif ($ch === "\\") {
                $prev_backslash = true;
            } else {
                $regexp .= $ch;
            }
            $mods = $read_name();
            return $token("regexp", [ $regexp, $mods ]);
        });
    };

    $read_operator = function ($prefix = '') use ($OPERATORS, $peek, $next, $token) {
        $grow = function ($op) use (&$grow, $OPERATORS, $peek, $next) {
            $ch = $peek();
            if ($ch === '') return $op;
            $bigger = $op . $ch;
            if (isset($OPERATORS[$bigger])) {
                $next();
                return $grow($bigger);
            } else {
                return $op;
            }
        };
        return $token("operator", $grow($prefix ?: $next()));
    };

    $handle_slash = function () use (
        &$S,
        $peek,
        $next,
        $read_line_comment,
        $read_multiline_comment,
        $read_regexp,
        $read_operator,
        &$next_token
    ) {
        $next();
        $regex_allowed = $S['regex_allowed'];
        switch ($peek()) {
          case "/":
            $S['comments_before'][] = $read_line_comment();
            $S['regex_allowed'] = $regex_allowed;
            return $next_token();
          case "*":
            $S['comments_before'][] = $read_multiline_comment();
            $S['regex_allowed'] = $regex_allowed;
            return $next_token();
        }
        return $S['regex_allowed'] ? $read_regexp("") : $read_operator("/");
    };

    $handle_dot = function () use (
        $peek,
        $next,
        $token,
        $read_num
    ) {
        $next();
        return ctype_digit($peek())
            ? $read_num(".")
            : $token("punc", ".");
    };

    $read_word = function () use (
        $KEYWORDS,
        $KEYWORDS_ATOM,
        $OPERATORS,
        $token,
        $read_name
    ) {
        $word = $read_name();
        return !isset($KEYWORDS[$word])
            ? $token("name", $word)
            : (isset($OPERATORS[$word])
            ? $token("operator", $word)
            : (isset($KEYWORDS_ATOM[$word])
            ? $token("atom", $word)
            : $token("keyword", $word)));
    };

    $next_token = function ($force_regexp = null) use (
        $OPERATOR_CHARS,
        $WHITESPACE_CHARS,
        $PUNC_CHARS,
        $peek,
        $next,
        $start_token,
        $token,
        $parse_error,
        $read_num,
        $read_string,
        $read_regexp,
        $read_operator,
        $handle_slash,
        $handle_dot,
        $read_word,
        $is_identifier_start
    ) {
        if ($force_regexp !== null)
            return $read_regexp($force_regexp);
        $ch = $peek();
        while (isset($WHITESPACE_CHARS[$ch])) { $next(); $ch = $peek(); }
        $start_token();
        if ($ch === '') return $token("eof");
        if (ctype_digit($ch)) return $read_num();
        if ($ch === '"' || $ch === "'") return $read_string();
        if (isset($PUNC_CHARS[$ch])) return $token("punc", $next());
        if ($ch === ".") return $handle_dot();
        if ($ch === "/") return $handle_slash();
        if (isset($OPERATOR_CHARS[$ch])) return $read_operator();
        if ($ch === "\\" || $is_identifier_start($ch)) return $read_word();
        $parse_error("Unexpected character '" . $ch . "'");
    };

    $next_token_context = function ($nc = null) use (&$S) {
        if ($nc) return $nc;
        return $S;
    };

    return [ $next_token, 'context' => $next_token_context ];

};

/* -----[ Parser ]----- */

// Remove unknown codes NodeWithToken, embed_tokens, maybe_embed_tokens

$parse = function ($TEXT, $exigent_mode = false) use (
    $tokenizer,
    $UNARY_PREFIX,
    $UNARY_POSTFIX,
    $ASSIGNMENT,
    $PRECEDENCE,
    $STATEMENTS_WITH_LABELS,
    $ATOMIC_START_TOKEN,
    $curry,
    $prog1,
    $js_error,
    $is_token
) {

    $S = [
        'input'         => is_string($TEXT) ? $tokenizer($TEXT, true) : $TEXT,
        'token'         => null,
        'prev'          => null,
        'peeked'        => null,
        'in_function'   => 0,
        'in_directives' => true,
        'in_loop'       => 0,
        'labels'        => []
    ];

    $is = function ($type, $value = null) use (&$S, $is_token) {
        return $is_token($S['token'], $type, $value);
    };

    $peek = function () use (&$S) { return $S['peeked'] ?: ($S['peeked'] = $S['input'][0]()); };

    $next = function () use (&$S, $is) {
        $S['prev'] = $S['token'];
        if ($S['peeked']) {
            $S['token'] = $S['peeked'];
            $S['peeked'] = null;
        } else {
            $S['token'] = $S['input'][0]();
        }
        $S['in_directives'] = $S['in_directives'] && (
            $S['token']['type'] === "string" || $is("punc", ";")
        );
        return $S['token'];
    };

    $S['token'] = $next();

    $croak = function ($msg, $line = null, $col = null, $pos = null) use (&$S, $js_error) {
        $ctx = $S['input']['context']();
        $js_error($msg,
                 $line !== null ? $line : $ctx['tokline'],
                 $col !== null ? $col : $ctx['tokcol'],
                 $pos !== null ? $pos : $ctx['tokpos']);
    };

    $token_error = function ($token, $msg) use ($croak) {
        $croak($msg, $token['line'], $token['col']);
    };

    $unexpected = function ($token = null) use (&$S, $token_error) {
        if ($token === null)
            $token = $S['token'];
        $token_error($token, "Unexpected token: " . $token['type'] . " (" . $token['value'] . ")");
    };

    $expect_token = function ($type, $val) use (&$S, $is, $next, $token_error) {
        if ($is($type, $val)) {
            return $next();
        }
        $token_error($S['token'], "Unexpected token " . $S['token']['type'] . ", expected " . $type);
    };

    $expect = function ($punc) use ($expect_token) { return $expect_token("punc", $punc); };

    $can_insert_semicolon = function () use (&$S, $is, $exigent_mode) {
        return !$exigent_mode && (
            $S['token']['nlb'] || $is("eof") || $is("punc", "}")
        );
    };

    $semicolon = function () use ($is, $next, $unexpected, $can_insert_semicolon) {
        if ($is("punc", ";")) $next();
        elseif (!$can_insert_semicolon()) $unexpected();
    };

    $parenthesised = function () use ($expect, &$expression) {
        $expect("(");
        $ex = $expression();
        $expect(")");
        return $ex;
    };

    $in_loop = function ($cont) use (&$S) {
        try {
            ++$S['in_loop'];
            $ret = $cont();
        } catch (Exception $ex) {
            --$S['in_loop'];
            throw $ex;
        }
        --$S['in_loop'];
        return $ret;
    };

    $vardefs = function ($no_in = false) use (
        &$S,
        $is,
        $next,
        $unexpected,
        &$expression
    ) {
        $a = [];
        for (;;) {
            if (!$is("name"))
                $unexpected();
            $name = $S['token']['value'];
            $next();
            if ($is("operator", "=")) {
                $next();
                $a[] = [ $name, $expression(false, $no_in) ];
            } else {
                $a[] = [ $name, null ];
            }
            if (!$is("punc", ","))
                break;
            $next();
        }
        return $a;
    };

    $var_ = function ($no_in = false) use ($vardefs) {
        return [ "var", $vardefs($no_in) ];
    };

    $const_ = function () use ($vardefs) {
        return [ "const", $vardefs() ];
    };

    $break_cont = function ($type) use (
        &$S,
        $is,
        $next,
        $croak,
        $can_insert_semicolon,
        $semicolon
    ) {
        $name = null;
        if (!$can_insert_semicolon()) {
            $name = $is("name") ? $S['token']['value'] : null;
        }
        if ($name !== null) {
            $next();
            if (!in_array($name, $S['labels']))
                $croak("Label " . $name . " without matching loop or statement");
        }
        elseif ($S['in_loop'] === 0)
            $croak($type . " not inside a loop or switch");
        $semicolon();
        return [ $type, $name ];
    };

    $simple_statement = function () use ($prog1, $semicolon, &$expression) {
        return [ "stat", $prog1($expression, $semicolon) ];
    };

    $statement = function () use (
        &$S,
        $is,
        $peek,
        $next,
        $croak,
        $unexpected,
        $expect_token,
        $can_insert_semicolon,
        $semicolon,
        $parenthesised,
        $in_loop,
        $prog1,
        $is_token,
        $var_,
        $const_,
        $break_cont,
        $simple_statement,
        &$statement,
        &$labeled_statement,
        &$for_,
        &$function_,
        &$if_,
        &$block_,
        &$switch_block_,
        &$try_,
        &$expression
    ) {
        if ($is("operator", "/") || $is("operator", "/=")) {
            $S['peeked'] = null;
            $S['token'] = $S['input'][0](substr($S['token']['value'], 1)); // force regexp
        }
        switch ($S['token']['type']) {
          case "string":
            $dir = $S['in_directives']; $stat = $simple_statement();
            if ($dir && $stat[1][0] === "string" && !$is("punc", ","))
                return [ "directive", $stat[1][1] ];
            return $stat;
          case "num":
          case "regexp":
          case "operator":
          case "atom":
            return $simple_statement();

          case "name":
            return $is_token($peek(), "punc", ":")
                ? $labeled_statement($prog1($S['token']['value'], $next, $next))
                : $simple_statement();

          case "punc":
            switch ($S['token']['value']) {
              case "{":
                return [ "block", $block_() ];
              case "[":
              case "(":
                return $simple_statement();
              case ";":
                $next();
                return [ "block" ];
              default:
                $unexpected();
            }

          case "keyword":
            switch ($prog1($S['token']['value'], $next)) {
              case "break":
                return $break_cont("break");

              case "continue":
                return $break_cont("continue");

              case "debugger":
                $semicolon();
                return [ "debugger" ];

              case "do":
                return call_user_func(function ($body) use (
                    $prog1,
                    $expect_token,
                    $semicolon,
                    $parenthesised
                ) {
                    $expect_token("keyword", "while");
                    return [ "do", $prog1($parenthesised, $semicolon), $body ];
                }, $in_loop($statement));

              case "for":
                return $for_();

              case "function":
                return $function_(true);

              case "if":
                return $if_();

              case "return":
                if ($S['in_function'] === 0)
                    $croak("'return' outside of function");
                return [ "return",
                          $is("punc", ";")
                          ? call_user_func(function () use ($next) { $next(); return null; })
                          : ($can_insert_semicolon()
                          ? null
                          : $prog1($expression, $semicolon)) ];

              case "switch":
                return [ "switch", $parenthesised(), $switch_block_() ];

              case "throw":
                if ($S['token']['nlb'])
                    $croak("Illegal newline after 'throw'");
                return [ "throw", $prog1($expression, $semicolon) ];

              case "try":
                return $try_();

              case "var":
                return $prog1($var_, $semicolon);

              case "const":
                return $prog1($const_, $semicolon);

              case "while":
                return [ "while", $parenthesised(), $in_loop($statement) ];

              case "with":
                return [ "with", $parenthesised(), $statement() ];

              default:
                $unexpected();
            }
        }
    };

    $labeled_statement = function ($label) use (
        &$S,
        $STATEMENTS_WITH_LABELS,
        $unexpected,
        $exigent_mode,
        $statement
    ) {
        $S['labels'][] = $label;
        $start = $S['token']; $stat = $statement();
        if ($exigent_mode && !isset($STATEMENTS_WITH_LABELS[$stat[0]]))
            $unexpected($start);
        array_pop($S['labels']);
        return [ "label", $label, $stat ];
    };

    $regular_for = function ($init) use (
        $is,
        $expect,
        $in_loop,
        $statement,
        &$expression
    ) {
        $expect(";");
        $test = $is("punc", ";") ? null : $expression();
        $expect(";");
        $step = $is("punc", ")") ? null : $expression();
        $expect(")");
        return [ "for", $init, $test, $step, $in_loop($statement) ];
    };

    $for_in = function ($init) use (
        $next,
        $expect,
        $in_loop,
        $statement,
        &$expression
    ) {
        $lhs = $init[0] === "var" ? [ "name", $init[1][0][0] ] : $init;
        $next();
        $obj = $expression();
        $expect(")");
        return [ "for-in", $init, $lhs, $obj, $in_loop($statement) ];
    };

    $for_ = function () use (
        $is,
        $next,
        $croak,
        $expect,
        $var_,
        $regular_for,
        $for_in,
        &$expression
    ) {
        $expect("(");
        $init = null;
        if (!$is("punc", ";")) {
            $init = $is("keyword", "var")
                ? call_user_func(function () use ($next, $var_) { $next(); return $var_(true); })
                : $expression(true, true);
            if ($is("operator", "in")) {
                if ($init[0] === "var" && count($init[1]) > 1)
                    $croak("Only one variable declaration allowed in for..in loop");
                return $for_in($init);
            }
        }
        return $regular_for($init);
    };

    $if_ = function () use ($is, $next, $parenthesised, $statement) {
        $cond = $parenthesised(); $body = $statement(); $belse = null;
        if ($is("keyword", "else")) {
            $next();
            $belse = $statement();
        }
        return [ "if", $cond, $body, $belse ];
    };

    $block_ = function () use ($is, $next, $unexpected, $expect, $statement) {
        $expect("{");
        $a = [];
        while (!$is("punc", "}")) {
            if ($is("eof")) $unexpected();
            $a[] = $statement();
        }
        $next();
        return $a;
    };

    $switch_block_ = $curry($in_loop, function () use (
        $is,
        $next,
        $unexpected,
        $expect,
        $statement,
        &$expression
    ) {
        $expect("{");
        $a = []; $cur = 0;
        while (!$is("punc", "}")) {
            if ($is("eof")) $unexpected();
            if ($is("keyword", "case")) {
                $next();
                $cur = array_push($a, [ $expression(), [] ]);
                $expect(":");
            }
            elseif ($is("keyword", "default")) {
                $next();
                $expect(":");
                $cur = array_push($a, [ null, [] ]);
            }
            else {
                if (!$cur) $unexpected();
                $a[$cur - 1][1][] = $statement();
            }
        }
        $next();
        return $a;
    });

    $function_ = function ($in_statement) use (
        &$S,
        $prog1,
        $is,
        $next,
        $unexpected,
        $expect,
        $block_
    ) {
        $name = $is("name") ? $prog1($S['token']['value'], $next) : null;
        if ($in_statement && !$name)
            $unexpected();
        $expect("(");
        return [ $in_statement ? "defun" : "function",
                 $name,
                 // arguments
                 call_user_func(function ($first, $a) use (&$S, $is, $next, $unexpected, $expect) {
                     while (!$is("punc", ")")) {
                         if ($first) $first = false; else $expect(",");
                         if (!$is("name")) $unexpected();
                         $a[] = $S['token']['value'];
                         $next();
                     }
                     $next();
                     return $a;
                 }, true, []),
                 // body
                 call_user_func(function () use (&$S, $block_) {
                     ++$S['in_function'];
                     $loop = $S['in_loop'];
                     $S['in_directives'] = true;
                     $S['in_loop'] = 0;
                     $a = $block_();
                     --$S['in_function'];
                     $S['in_loop'] = $loop;
                     return $a;
                 }) ];
    };

    $try_ = function () use (
        &$S,
        $is,
        $next,
        $croak,
        $expect,
        $block_
    ) {
        $body = $block_(); $bcatch = $bfinally = null;
        if ($is("keyword", "catch")) {
            $next();
            $expect("(");
            if (!$is("name"))
                $croak("Name expected");
            $name = $S['token']['value'];
            $next();
            $expect(")");
            $bcatch = [ $name, $block_() ];
        }
        if ($is("keyword", "finally")) {
            $next();
            $bfinally = $block_();
        }
        if ($bcatch === null && $bfinally === null)
            $croak("Missing catch/finally blocks");
        return [ "try", $body, $bcatch, $bfinally ];
    };

    $expr_list = function ($closing, $allow_trailing_comma = false, $allow_empty = false) use (
        $is,
        $next,
        $expect,
        &$expression
    ) {
        $first = true; $a = [];
        while (!$is("punc", $closing)) {
            if ($first) $first = false; else $expect(",");
            if ($allow_trailing_comma && $is("punc", $closing)) break;
            if ($allow_empty && $is("punc", ",")) {
                $a[] = [ "atom", "undefined" ];
            } else {
                $a[] = $expression(false);
            }
        }
        $next();
        return $a;
    };

    $array_ = function () use ($expr_list, $exigent_mode) {
        return [ "array", $expr_list("]", !$exigent_mode, true) ];
    };

    $new_ = function () use (
        $is,
        $next,
        $expr_list,
        &$expr_atom,
        &$subscripts
    ) {
        $newexp = $expr_atom(false);
        if ($is("punc", "(")) {
            $next();
            $args = $expr_list(")");
        } else {
            $args = [];
        }
        return $subscripts([ "new", $newexp, $args ], true);
    };

    $expr_atom = function ($allow_calls) use (
        &$S,
        $ATOMIC_START_TOKEN,
        $curry,
        $prog1,
        $is,
        $next,
        $unexpected,
        $expect,
        $function_,
        $new_,
        $array_,
        &$object_,
        &$subscripts,
        &$expression
    ) {
        if ($is("operator", "new")) {
            $next();
            return $new_();
        }
        if ($is("punc")) {
            switch ($S['token']['value']) {
              case "(":
                $next();
                return $subscripts($prog1($expression, $curry($expect, ")")), $allow_calls);
              case "[":
                $next();
                return $subscripts($array_(), $allow_calls);
              case "{":
                $next();
                return $subscripts($object_(), $allow_calls);
            }
            $unexpected();
        }
        if ($is("keyword", "function")) {
            $next();
            return $subscripts($function_(false), $allow_calls);
        }
        if (isset($ATOMIC_START_TOKEN[$S['token']['type']])) {
            $atom = $S['token']['type'] === "regexp"
                ? [ "regexp", $S['token']['value'][0], $S['token']['value'][1] ]
                : [ $S['token']['type'], $S['token']['value'] ];
            return $subscripts($prog1($atom, $next), $allow_calls);
        }
        $unexpected();
    };

    $as_name = function () use (&$S, $prog1, $next, $unexpected) {
        switch ($S['token']['type']) {
          case "name":
          case "operator":
          case "keyword":
          case "atom":
            return $prog1($S['token']['value'], $next);
          default:
            $unexpected();
        }
    };

    $as_property_name = function () use (&$S, $prog1, $next, $as_name) {
        switch ($S['token']['type']) {
          case "num":
          case "string":
            return $prog1($S['token']['value'], $next);
        }
        return $as_name();
    };

    $object_ = function () use (
        &$S,
        $is,
        $next,
        $expect,
        $exigent_mode,
        $function_,
        $as_name,
        $as_property_name,
        &$expression
    ) {
        $first = true; $a = [];
        while (!$is("punc", "}")) {
            if ($first) $first = false; else $expect(",");
            if (!$exigent_mode && $is("punc", "}"))
                // allow trailing comma
                break;
            $type = $S['token']['type'];
            $name = $as_property_name();
            if ($type === "name" && ($name === "get" || $name === "set") && !$is("punc", ":")) {
                $a[] = [ $as_name(), $function_(false), $name ];
            } else {
                $expect(":");
                $a[] = [ $name, $expression(false) ];
            }
        }
        $next();
        return [ "object", $a ];
    };

    $subscripts = function ($expr, $allow_calls) use (
        $curry,
        $prog1,
        $is,
        $next,
        $expect,
        $expr_list,
        $as_name,
        &$subscripts,
        &$expression
    ) {
        if ($is("punc", ".")) {
            $next();
            return $subscripts([ "dot", $expr, $as_name() ], $allow_calls);
        }
        if ($is("punc", "[")) {
            $next();
            return $subscripts([ "sub", $expr, $prog1($expression, $curry($expect, "]")) ], $allow_calls);
        }
        if ($allow_calls && $is("punc", "(")) {
            $next();
            return $subscripts([ "call", $expr, $expr_list(")") ], true);
        }
        return $expr;
    };

    $is_assignable = function ($expr) use ($exigent_mode) {
        if (!$exigent_mode) return true;
        switch ($expr[0]) {
          case "dot":
          case "sub":
          case "new":
          case "call":
            return true;
          case "name":
            return $expr[1] !== "this";
        }
    };

    $make_unary = function ($tag, $op, $expr) use ($croak, $is_assignable) {
        if (($op === "++" || $op === "--") && !$is_assignable($expr))
            $croak("Invalid use of " . $op . " operator");
        return [ $tag, $op, $expr ];
    };

    $maybe_unary = function ($allow_calls) use (
        &$S,
        $UNARY_PREFIX,
        $UNARY_POSTFIX,
        $prog1,
        $is,
        $next,
        $expr_atom,
        $make_unary,
        &$maybe_unary
    ) {
        if ($is("operator") && isset($UNARY_PREFIX[$S['token']['value']])) {
            return $make_unary("unary-prefix",
                              $prog1($S['token']['value'], $next),
                              $maybe_unary($allow_calls));
        }
        $val = $expr_atom($allow_calls);
        while ($is("operator") && isset($UNARY_POSTFIX[$S['token']['value']]) && !$S['token']['nlb']) {
            $val = $make_unary("unary-postfix", $S['token']['value'], $val);
            $next();
        }
        return $val;
    };

    $expr_op = function ($left, $min_prec, $no_in) use (
        &$S,
        $PRECEDENCE,
        $is,
        $next,
        $maybe_unary,
        &$expr_op
    ) {
        $op = $is("operator") ? $S['token']['value'] : null;
        if ($op === "in" && $no_in) $op = null;
        $prec = isset($op, $PRECEDENCE[$op]) ? $PRECEDENCE[$op] : null;
        if ($prec !== null && $prec > $min_prec) {
            $next();
            $right = $expr_op($maybe_unary(true), $prec, $no_in);
            return $expr_op([ "binary", $op, $left, $right ], $min_prec, $no_in);
        }
        return $left;
    };

    $expr_ops = function ($no_in) use ($maybe_unary, $expr_op) {
        return $expr_op($maybe_unary(true), 0, $no_in);
    };

    $maybe_conditional = function ($no_in) use (
        $is,
        $next,
        $expect,
        $expr_ops,
        &$expression
    ) {
        $expr = $expr_ops($no_in);
        if ($is("operator", "?")) {
            $next();
            $yes = $expression(false);
            $expect(":");
            return [ "conditional", $expr, $yes, $expression(false, $no_in) ];
        }
        return $expr;
    };

    $maybe_assign = function ($no_in) use (
        &$S,
        $ASSIGNMENT,
        $is,
        $next,
        $croak,
        $is_assignable,
        $maybe_conditional,
        &$maybe_assign
    ) {
        $left = $maybe_conditional($no_in); $val = $S['token']['value'];
        if ($is("operator") && isset($ASSIGNMENT[$val])) {
            if ($is_assignable($left)) {
                $next();
                return [ "assign", $ASSIGNMENT[$val], $left, $maybe_assign($no_in) ];
            }
            $croak("Invalid assignment");
        }
        return $left;
    };

    $expression = function ($commas = true, $no_in = false) use (
        $is,
        $next,
        $maybe_assign,
        &$expression
    ) {
        $expr = $maybe_assign($no_in);
        if ($commas && $is("punc", ",")) {
            $next();
            return [ "seq", $expr, $expression(true, $no_in) ];
        }
        return $expr;
    };

    return [ "toplevel", call_user_func(function ($a) use ($is, $statement) {
        while (!$is("eof"))
            $a[] = $statement();
        return $a;
    }, []) ];

};

$repeat_string = function ($str, $i) {
    if ($i < 0) return '';
    return str_repeat($str, $i);
};

$HOP = function ($obj, $prop) {
    return array_key_exists($prop, $obj);
};

$defaults = function ($args, $defs) use ($HOP) {
    $ret = [];
    if ($args === true)
        $args = [];
    foreach ($defs as $i => $value) {
        $ret[$i] = ($args && $HOP($args, $i)) ? $args[$i] : $defs[$i];
    }
    return $ret;
};

$is_identifier = function ($name) use (/*$HOP, */$KEYWORDS_ATOM, $RESERVED_WORDS, $KEYWORDS) {
    return preg_match('/^[a-zA-Z_$][a-zA-Z0-9_$]*$/', $name)
        && $name !== "this"
        && !isset($KEYWORDS_ATOM[$name])
        && !isset($RESERVED_WORDS[$name])
        && !isset($KEYWORDS[$name]);
};

// some utilities

$MAP = function($a, $f) {
    $ret = []; $top = []; $count = count($a);
    if (isset($a['scope'])) --$count;
    for ($i = 0; $i < $count; ++$i) {
        $val = $f($a[$i], $i);
        if (isset($val['AtTop'])) {
            $val = $val['v'];
            $top[] = $val;
        }
        else {
            $ret[] = $val;
        }
    }
    return array_merge($top, $ret);
};
$MAP_at_top = function($val) { return [ 'v' => $val, 'AtTop' => true ]; };

/* -----[ helper for AST traversal ]----- */

$ast_walker = function () use ($MAP) {

    $user = [];
    $stack = [];
    $walk = function ($ast = null) use (&$walkers, &$user, &$stack) {
        if ($ast === null)
            return null;
        try {
            $stack[] = $ast;
            $type = $ast[0];
            $gen = isset($user[$type]) ? $user[$type] : null;
            if (isset($ast['scope'])) { $scope = $ast['scope']; unset($ast['scope']); $ast[] = $scope; }
            if ($gen) {
                $ret = call_user_func_array($gen, $ast);
            }
            if (!isset($ret)) {
                $gen = $walkers[$type];
                $ret = call_user_func_array($gen, $ast);
            }
        } catch (Exception $ex) {
            array_pop($stack);
            throw $ex;
        }
        array_pop($stack);
        return $ret;
    };

    $_vardefs = function ($_this_0, $defs) use ($MAP, $walk) {
        return [ $_this_0, $MAP($defs, function ($def) use ($walk) {
            $a = [ $def[0] ];
            if (count($def) > 1)
                $a[1] = $walk($def[1]);
            return $a;
        }) ];
    };
    $_block = function ($_this_0, $statements = null) use ($MAP, $walk) {
        $out = [ $_this_0 ];
        if ($statements !== null)
            $out[] = $MAP($statements, $walk);
        return $out;
    };
    $walkers = [
        "string" => function($_this_0, $str) {
            return [ $_this_0, $str ];
        },
        "num" => function($_this_0, $num) {
            return [ $_this_0, $num ];
        },
        "name" => function($_this_0, $name) {
            return [ $_this_0, $name ];
        },
        "toplevel" => function($_this_0, $statements) use ($MAP, $walk) {
            return [ $_this_0, $MAP($statements, $walk) ];
        },
        "block" => $_block,
        "splice" => $_block, // removed
        "var" => $_vardefs,
        "const" => $_vardefs,
        "try" => function($_this_0, $t, $c = null, $f = null) use ($MAP, $walk) {
            return [
                $_this_0,
                $MAP($t, $walk),
                $c !== null ? [ $c[0], $MAP($c[1], $walk) ] : null,
                $f !== null ? $MAP($f, $walk) : null
            ];
        },
        "throw" => function($_this_0, $expr) use ($walk) {
            return [ $_this_0, $walk($expr) ];
        },
        "new" => function($_this_0, $ctor, $args) use ($MAP, $walk) {
            return [ $_this_0, $walk($ctor), $MAP($args, $walk) ];
        },
        "switch" => function($_this_0, $expr, $body) use ($MAP, $walk) {
            return [ $_this_0, $walk($expr), $MAP($body, function($branch) use ($MAP, $walk) {
                return [ $branch[0] ? $walk($branch[0]) : null,
                         $MAP($branch[1], $walk) ];
            }) ];
        },
        "break" => function($_this_0, $label) {
            return [ $_this_0, $label ];
        },
        "continue" => function($_this_0, $label) {
            return [ $_this_0, $label ];
        },
        "conditional" => function($_this_0, $cond, $t, $e) use ($walk) {
            return [ $_this_0, $walk($cond), $walk($t), $walk($e) ];
        },
        "assign" => function($_this_0, $op, $lvalue, $rvalue) use ($walk) {
            return [ $_this_0, $op, $walk($lvalue), $walk($rvalue) ];
        },
        "dot" => function($_this_0, $expr) use ($walk) {
            return [ $_this_0, $walk($expr) ] + func_get_args();
        },
        "call" => function($_this_0, $expr, $args) use ($MAP, $walk) {
            return [ $_this_0, $walk($expr), $MAP($args, $walk) ];
        },
        "function" => function($_this_0, $name, $args, $body) use ($MAP, $walk) {
            return [ $_this_0, $name, $args, $MAP($body, $walk) ];
        },
        "debugger" => function($_this_0) {
            return [ $_this_0 ];
        },
        "defun" => function($_this_0, $name, $args, $body) use ($MAP, $walk) {
            return [ $_this_0, $name, $args, $MAP($body, $walk) ];
        },
        "if" => function($_this_0, $conditional, $t, $e) use ($walk) {
            return [ $_this_0, $walk($conditional), $walk($t), $walk($e) ];
        },
        "for" => function($_this_0, $init, $cond, $step, $block) use ($walk) {
            return [ $_this_0, $walk($init), $walk($cond), $walk($step), $walk($block) ];
        },
        "for-in" => function($_this_0, $vvar, $key, $hash, $block) use ($walk) {
            return [ $_this_0, $walk($vvar), $walk($key), $walk($hash), $walk($block) ];
        },
        "while" => function($_this_0, $cond, $block) use ($walk) {
            return [ $_this_0, $walk($cond), $walk($block) ];
        },
        "do" => function($_this_0, $cond, $block) use ($walk) {
            return [ $_this_0, $walk($cond), $walk($block) ];
        },
        "return" => function($_this_0, $expr) use ($walk) {
            return [ $_this_0, $walk($expr) ];
        },
        "binary" => function($_this_0, $op, $left, $right) use ($walk) {
            return [ $_this_0, $op, $walk($left), $walk($right) ];
        },
        "unary-prefix" => function($_this_0, $op, $expr) use ($walk) {
            return [ $_this_0, $op, $walk($expr) ];
        },
        "unary-postfix" => function($_this_0, $op, $expr) use ($walk) {
            return [ $_this_0, $op, $walk($expr) ];
        },
        "sub" => function($_this_0, $expr, $subscript) use ($walk) {
            return [ $_this_0, $walk($expr), $walk($subscript) ];
        },
        "object" => function($_this_0, $props) use ($MAP, $walk) {
            return [ $_this_0, $MAP($props, function($p) use ($walk) {
                return count($p) === 2
                    ? [ $p[0], $walk($p[1]) ]
                    : [ $p[0], $walk($p[1]), $p[2] ]; // get/set-ter
            }) ];
        },
        "regexp" => function($_this_0, $rx, $mods) {
            return [ $_this_0, $rx, $mods ];
        },
        "array" => function($_this_0, $elements) use ($MAP, $walk) {
            return [ $_this_0, $MAP($elements, $walk) ];
        },
        "stat" => function($_this_0, $stat) use ($walk) {
            return [ $_this_0, $walk($stat) ];
        },
        "seq" => function($_this_0) use ($MAP, $walk) {
            return array_merge( [ $_this_0 ], $MAP(array_slice(func_get_args(), 1), $walk) );
        },
        "label" => function($_this_0, $name, $block) use ($walk) {
            return [ $_this_0, $name, $walk($block) ];
        },
        "with" => function($_this_0, $expr, $block) use ($walk) {
            return [ $_this_0, $walk($expr), $walk($block) ];
        },
        "atom" => function($_this_0, $name) {
            return [ $_this_0, $name ];
        },
        "directive" => function($_this_0, $dir) {
            return [ $_this_0, $dir ];
        }
    ];

    $with_walkers = function ($walkers, $cont) use (&$user) {
        $save = [];
        foreach ($walkers as $i => $value) {
            if (isset($user[$i])) $save[$i] = $user[$i];
            $user[$i] = $walkers[$i];
        }
        $ret = $cont();
        foreach ($walkers as $i => $value) {
            if (!isset($save[$i])) unset($user[$i]);
            else $user[$i] = $save[$i];
        }
        return $ret;
    };

    return [
        'walk' => $walk,
        'with_walkers' => $with_walkers,
        'parent' => function () use (&$stack) {
            return $stack[count($stack) - 2]; // last one is current node
        },
        'stack' => function () use (&$stack) {
            return $stack;
        }
    ];
};

/* -----[ Scope and mangling ]----- */

if (!isset($DIGITS))
    $DIGITS = 'etnrisouaflchpdvmgybwESxTNCkLAOM_DPHBjFIqRUzWXV$JKQGYZ0516372984';

$base54 = function ($num) use ($DIGITS) {
    $ret = ''; $base = 54;
    do {
        $ret .= $DIGITS[$num % $base];
        $num = floor($num / $base);
        $base = 64;
    } while ($num > 0);
    return $ret;
};

$prototype = [
    'has' => function($name) use ($HOP) {
        for ($s = $this; $s; $s = $s['parent'])
            if ($HOP($s['names'], $name))
                return $s;
    },
    'has_mangled' => function($mname) use ($HOP) {
        for ($s = $this; $s; $s = $s['parent'])
            if ($HOP($s['rev_mangled'], $mname))
                return $s;
    },
    'toJSON' => function() {
        return [
            'names' => $this['names'],
            'uses_eval' => $this['uses_eval'],
            'uses_with' => $this['uses_with']
        ];
    },

    'next_mangled' => function() use ($HOP, $is_identifier, $base54) {
        // we must be careful that the new mangled name:
        //
        // 1. doesn't shadow a mangled name from a parent
        //    scope, unless we don't reference the original
        //    name from this scope OR from any sub-scopes!
        //    This will get slow.
        //
        // 2. doesn't shadow an original name from a parent
        //    scope, in the event that the name is not mangled
        //    in the parent scope and we reference that name
        //    here OR IN ANY SUBSCOPES!
        //
        // 3. doesn't shadow a name that is referenced but not
        //    defined (possibly global defined elsewhere).
        for (;;) {
            $m = $base54(++$this['cname']);

            // case 1.
            $prior = $this['has_mangled']($m);
            if ($prior && (isset($this['refs'][$prior['rev_mangled'][$m]])
                ? $this['refs'][$prior['rev_mangled'][$m]] : null) === $prior)
                continue;

            // case 2.
            $prior = $this['has']($m);
            if ($prior && $prior !== $this && (isset($this['refs'][$m])
                ? $this['refs'][$m] : null) === $prior && !$prior['has_mangled']($m))
                continue;

            // case 3.
            if ($HOP($this['refs'], $m) && $this['refs'][$m] === null)
                continue;

            // I got "do" once. :-/
            if (!$is_identifier($m))
                continue;

            return $m;
        }
    },
    'set_mangle' => function($name, $m) {
        $this['rev_mangled'][$m] = $name;
        return $this['mangled'][$name] = $m;
    },
    'get_mangled' => function($name, $newMangle = false) use ($HOP) {
        if ($this['uses_eval'] || $this['uses_with']) return $name; // no mangle if eval or with is in use
        $s = $this['has']($name);
        if (!$s) return $name; // not in visible scope, no mangle
        if ($HOP($s['mangled'], $name)) return $s['mangled'][$name]; // already mangled in this scope
        if (!$newMangle) return $name;                      // not found and no mangling requested
        return $s['set_mangle']($name, $s['next_mangled']());
    },
    'references' => function($name) {
        return $name && !$this['parent'] || $this['uses_with'] || $this['uses_eval'] || isset($this['refs'][$name]);
    },
    'define' => function($name, $type = null) use ($HOP) {
        if ($name !== null) {
            if ($type === "var" || !$HOP($this['names'], $name))
                $this['names'][$name] = $type ?: "var";
            return $name;
        }
    },
    'active_directive' => function($dir) {
        return in_array($dir, $this['directives']) || $this['parent'] && $this['parent']['active_directive']($dir);
    }
];

$Scope = function ($parent = null) use ($prototype) {

    $_this = new ArrayObject($prototype);
    foreach ($_this as &$cl) $cl = $cl->bindTo($_this);

    $_this['names'] = [];        // names defined in this scope
    $_this['mangled'] = [];      // mangled names (orig.name => mangled)
    $_this['rev_mangled'] = [];  // reverse lookup (mangled => orig.name)
    $_this['cname'] = -1;        // current mangled name
    $_this['refs'] = [];         // names referenced from this scope
    $_this['uses_with'] = false; // will become TRUE if with() is detected in this or any subscopes
    $_this['uses_eval'] = false; // will become TRUE if eval() is detected in this or any subscopes
    $_this['directives'] = [];   // directives activated from this scope
    $_this['parent'] = $parent;  // parent scope
    $_this['children'] = [];     // sub-scopes
    if ($parent) {
        $_this['level'] = $parent['level'] + 1;
        $parent['children'][] = $_this;
    } else {
        $_this['level'] = 0;
    }

    return $_this;
};

$ast_add_scope = function ($ast) use ($MAP, $ast_walker, $Scope) {

    $current_scope = null;
    $w = $ast_walker(); $walk = $w['walk'];
    $having_eval = [];

    $with_new_scope = function ($cont) use ($Scope, &$current_scope) {
        $current_scope = $Scope($current_scope);
        $current_scope['labels'] = $Scope();
        $ret = $current_scope['body'] = $cont();
        $ret['scope'] = $current_scope;
        $current_scope = $current_scope['parent'];
        return $ret;
    };

    $define = function ($name, $type) use (&$current_scope) {
        return $current_scope['define']($name, $type);
    };

    $reference = function ($name) use (&$current_scope) {
        $current_scope['refs'][$name] = true;
    };

    $_lambda = function ($_this_0, $name, $args, $body) use ($MAP, $walk, $with_new_scope, $define) {
        $is_defun = $_this_0 === "defun";
        return [ $_this_0, $is_defun ? $define($name, "defun") : $name, $args,
        $with_new_scope(function () use ($MAP, $walk, $define, $name, $args, $body, $is_defun) {
            if (!$is_defun) $define($name, "lambda");
            $MAP($args, function ($name) use ($define) { $define($name, "arg"); });
            return $MAP($body, $walk);
        })];
    };

    $_vardefs = function ($type) use ($MAP, $define, $reference) {
        return function ($_this_0, $defs) use ($MAP, $define, $reference, $type) {
            $MAP($defs, function ($d) use ($define, $reference, $type) {
                $define($d[0], $type);
                if ($d[1]) $reference($d[0]);
            });
        };
    };

    $_breacont = function ($_this_0, $label = null) use (&$current_scope) {
        if ($label)
            $current_scope['labels']['refs'][$label] = true;
    };

    return $with_new_scope(function () use (
        $MAP,
        $ast,
        &$current_scope,
        $w,
        $walk,
        &$having_eval,
        $define,
        $reference,
        $_lambda,
        $_vardefs,
        $_breacont
    ) {
        // process AST
        $ret = $w['with_walkers']([
            "function" => $_lambda,
            "defun" => $_lambda,
            "label" => function($_this_0, $name, $stat) use (&$current_scope) { $current_scope['labels']['define']($name); },
            "break" => $_breacont,
            "continue" => $_breacont,
            "with" => function($_this_0, $expr, $block) use (&$current_scope) {
                for ($s = $current_scope; $s; $s = $s['parent'])
                    $s['uses_with'] = true;
            },
            "var" => $_vardefs("var"),
            "const" => $_vardefs("const"),
            "try" => function($_this_0, $t, $c = null, $f = null) use ($MAP, $walk, $define) {
                if ($c !== null) return [
                    $_this_0,
                    $MAP($t, $walk),
                    [ $define($c[0], "catch"), $MAP($c[1], $walk) ],
                    $f !== null ? $MAP($f, $walk) : null
                ];
            },
            "name" => function($_this_0, $name) use (&$current_scope, &$having_eval, $reference) {
                if ($name === "eval")
                    $having_eval[] = $current_scope;
                $reference($name);
            }
        ], function() use ($walk, $ast) {
            return $walk($ast);
        });

        // the reason why we need an additional pass here is
        // that names can be used prior to their definition.

        // scopes where eval was detected and their parents
        // are marked with uses_eval, unless they define the
        // "eval" name.
        $MAP($having_eval, function($scope){
            if (!$scope['has']("eval")) while ($scope) {
                $scope['uses_eval'] = true;
                $scope = $scope['parent'];
            }
        });

        // for referenced names it might be useful to know
        // their origin scope.  current_scope here is the
        // toplevel one.
        $fixrefs = function ($scope) use (&$fixrefs) {
            // do children first; order shouldn't matter
            for ($i = count($scope['children']); --$i >= 0;)
                $fixrefs($scope['children'][$i]);
            foreach ($scope['refs'] as $i => $value) {
                // find origin scope and propagate the reference to origin
                for ($origin = $scope['has']($i), $s = $scope; $s; $s = $s['parent']) {
                    $s['refs'][$i] = $origin;
                    if ($s === $origin) break;
                }
            }
        };
        $fixrefs($current_scope);

        return $ret;
    });

};

/* -----[ mangle names ]----- */

$ast_mangle = function ($ast, $options = []) use (
    $HOP,
    $MAP,
    $MAP_at_top,
    $ast_walker,
    $defaults,
    $ast_add_scope
) {
    $w = $ast_walker(); $walk = $w['walk']; $scope = null;
    $options = $defaults($options, [
        'mangle'       => true,
        'toplevel'     => false,
        'defines'      => null,
        'except'       => null,
        'no_functions' => false
    ]);

    $get_mangled = function ($name, $newMangle = false) use ($HOP, $options, &$scope) {
        if (!$options['mangle']) return $name;
        if (!$options['toplevel'] && !$scope['parent']) return $name; // don't mangle toplevel
        if ($options['except'] && in_array($name, $options['except']))
            return $name;
        if ($options['no_functions'] && $HOP($scope['names'], $name) &&
            ($scope['names'][$name] === 'defun' || $scope['names'][$name] === 'lambda'))
            return $name;
        return $scope['get_mangled']($name, $newMangle);
    };

    $get_define = function ($name) use ($HOP, $options, &$scope) {
        if ($options['defines']) {
            // we always lookup a defined symbol for the current scope FIRST, so declared
            // vars trump a DEFINE symbol, but if no such var is found, then match a DEFINE value
            if (!$scope['has']($name)) {
                if ($HOP($options['defines'], $name)) {
                    return $options['defines'][$name];
                }
            }
            return null;
        }
    };

    $with_scope = function ($s, $cont, $extra = null) use (&$scope, $get_mangled) {
        $_scope = $scope;
        $scope = $s;
        if ($extra) foreach ($extra as $i => $value) {
            $s['set_mangle']($i, $value);
        }
        foreach ($s['names'] as $i => $value) {
            $get_mangled($i, true);
        }
        $ret = $cont();
        $ret['scope'] = $s;
        $scope = $_scope;
        return $ret;
    };

    $_lambda = function ($_this_0, $name, $args, $body) use ($MAP, $options, $walk, &$scope, $get_mangled, $with_scope) {
        if (!$options['no_functions'] && $options['mangle']) {
            $is_defun = $_this_0 === "defun"; $extra = null;
            if ($name) {
                if ($is_defun) $name = $get_mangled($name);
                elseif ($body['scope']['references']($name)) {
                    $extra = [];
                    if (!($scope['uses_eval'] || $scope['uses_with']))
                        $name = $extra[$name] = $scope['next_mangled']();
                    else
                        $extra[$name] = $name;
                }
                else $name = null;
            }
        }
        $body = $with_scope($body['scope'], function () use ($MAP, $name, &$args, $body, $walk, $get_mangled) {
            $args = $MAP($args, function ($name) use ($get_mangled) { return $get_mangled($name); });
            return $MAP($body, $walk);
        }, $extra);
        return [ $_this_0, $name, $args, $body ];
    };

    $_vardefs = function ($_this_0, $defs) use ($MAP, $walk, $get_mangled) {
        return [ $_this_0, $MAP($defs, function ($d) use ($walk, $get_mangled) {
            return [ $get_mangled($d[0]), $walk($d[1]) ];
        }) ];
    };

    $_breacont = function ($_this_0, $label = null) use (&$scope) {
        if ($label) return [ $_this_0, $scope['labels']['get_mangled']($label) ];
    };

    return $w['with_walkers']([
        "function" => $_lambda,
        "defun" => function($_this_0) use ($MAP_at_top, $w, $_lambda) {
            // move function declarations to the top when
            // they are not in some block.
            $ast = call_user_func_array($_lambda, func_get_args());
            switch ($w['parent']()[0]) {
              case "toplevel":
              case "function":
              case "defun":
                return $MAP_at_top($ast);
            }
            return $ast;
        },
        "label" => function($_this_0, $label, $stat) use ($walk, &$scope) {
            if (isset($scope['labels']['refs'][$label])) return [
                $_this_0,
                $scope['labels']['get_mangled']($label, true),
                $walk($stat)
            ];
            return $walk($stat);
        },
        "break" => $_breacont,
        "continue" => $_breacont,
        "var" => $_vardefs,
        "const" => $_vardefs,
        "name" => function($_this_0, $name) use ($get_mangled, $get_define) {
            return $get_define($name) ?: [ $_this_0, $get_mangled($name) ];
        },
        "try" => function($_this_0, $t, $c = null, $f = null) use ($MAP, $walk, $get_mangled) {
            return [ $_this_0,
                     $MAP($t, $walk),
                     $c !== null ? [ $get_mangled($c[0]), $MAP($c[1], $walk) ] : null,
                     $f !== null ? $MAP($f, $walk) : null ];
        },
        "toplevel" => function($_this_0, $body, $_this_scope) use ($MAP, $walk, $with_scope) {
            return $with_scope($_this_scope, function() use ($MAP, $walk, $_this_0, $body) {
                return [ $_this_0, $MAP($body, $walk) ];
            });
        },
        "directive" => function($_this_0) use ($MAP_at_top) {
            return $MAP_at_top(func_get_args());
        }
    ], function() use ($walk, $ast_add_scope, $ast) {
        return $walk($ast_add_scope($ast));
    });
};

/* -----[
   - compress foo["bar"] into foo.bar,
   - remove block brackets {} where possible
   - join consecutive var declarations
   - various optimizations for IFs:
   - if (cond) foo(); else bar();  ==>  cond?foo():bar();
   - if (cond) foo();  ==>  cond&&foo();
   - if (foo) return bar(); else return baz();  ==> return foo?bar():baz(); // also for throw
   - if (foo) return bar(); else something();  ==> {if(foo)return bar();something()}
   ]----- */

$best_of = function ($ast1, $ast2) use (&$gen_code) {
    return strlen($gen_code($ast1)) > strlen($gen_code($ast2[0] === "stat" ? $ast2[1] : $ast2)) ? $ast2 : $ast1;
};

$last_stat = function ($b) {
    if ($b[0] === "block" && !empty($b[1]))
        return $b[1][count($b[1]) - 1];
    return $b;
};

$aborts = function ($t) use ($last_stat) {
    if ($t) switch ($last_stat($t)[0]) {
      case "return":
      case "break":
      case "continue":
      case "throw":
        return true;
    }
};

$boolean_expr = function ($expr) use (&$boolean_expr) {
    return ( ($expr[0] === "unary-prefix"
              && in_array($expr[1], [ "!", "delete" ])) ||

             ($expr[0] === "binary"
              && in_array($expr[1], [ "in", "instanceof", "==", "!=", "===", "!==", "<", "<=", ">=", ">" ])) ||

             ($expr[0] === "binary"
              && in_array($expr[1], [ "&&", "||" ])
              && $boolean_expr($expr[2])
              && $boolean_expr($expr[3])) ||

             ($expr[0] === "conditional"
              && $boolean_expr($expr[2])
              && $boolean_expr($expr[3])) ||

             ($expr[0] === "assign"
              && $expr[1] === true
              && $boolean_expr($expr[3])) ||

             ($expr[0] === "seq"
              && $boolean_expr($expr[count($expr) - 1]))
           );
};

$empty = function ($b) {
    return !$b || ($b[0] === "block" && empty($b[1]));
};

$is_string = function ($node) use (&$is_string) {
    return ($node[0] === "string" ||
            $node[0] === "unary-prefix" && $node[1] === "typeof" ||
            $node[0] === "binary" && $node[1] === "+" &&
            ($is_string($node[2]) || $is_string($node[3])));
};

$FloatToString = function ($val) {
    $sign = '';
    if ($val < 0) {
        $val = -$val;
        $sign = '-';
    }
    list($signif, $exp) = explode('e', sprintf('%.16e', $val));
    if (-$exp > 308) list($signif, $exp) = explode('e', sprintf('%.' . (324 + $exp) . 'e', $val));

    $newSignif = substr($signif, 0, -1);
    $newExp = $exp;
    $newSignif = preg_replace_callback('/^([1-9])\.9+$/', function ($matches) use (&$newExp) {
        $ret = $matches[1] + 1;
        if ($ret === 10) {
            $ret = 1;
            $newExp += 1;
            $newExp = ($newExp > 0 ? '+' : '') . $newExp;
        }
        return $ret;
    }, $newSignif, 1, $count);
    if ($count && $val === (float)($newSignif . 'e' . $newExp)) {
        $signif = $newSignif;
        $exp = $newExp;
        goto next;
    }
    $newSignif = rtrim($newSignif, '0.');
    if ($val === (float)($newSignif . 'e' . $exp)) {
        $signif = $newSignif;
        goto next;
    }
    $newSignif = preg_replace_callback('/([0-8])9*$/', function ($matches) {
        return $matches[1] + 1;
    }, $newSignif, 1, $count);
    if ($count && $val === (float)($newSignif . 'e' . $exp)) {
        $signif = $newSignif;
    }

    next:
    $signif = str_replace('.', '', $signif);
    $n = (int)$exp + 1; $k = strlen($signif);
    if ($k <= $n && $n <= 21)
        $ret = $signif . str_repeat('0', $n - $k);
    elseif (0 < $n && $n <= 21)
        $ret = substr_replace($signif, '.', $n, 0);
    elseif (-6 < $n && $n <= 0)
        $ret = '0.' . str_repeat('0', -$n) . $signif;
    elseif ($k === 1)
        $ret = $signif . 'e' . $exp;
    else
        $ret = substr_replace($signif, '.', 1, 0) . 'e' . $exp;
    return $sign . $ret;
};

$when_constant = call_user_func(function () use ($boolean_expr, $is_string, $FloatToString) {

    $NOT_CONSTANT = new Exception();

    // I have to simulate JavaScript operators' behavior
    // Reference: http://www.ecma-international.org/ecma-262/5.1/

    $ToBoolean = function ($val) {
        if ($val === '0') return true;
        if ($val !== $val) return false;
        return (bool)$val;
    };

    $ToNumber = function ($val) {
        if ($val === null) return 0;

        if ($val === true) return 1;
        if ($val === false) return 0;

        if (is_numeric($val)) {

            // PHP 5 handle float(-0) incorrectly
            $ret = 1 * $val;
            if ($ret === 0 && is_string($val) && ltrim($val)[0] === '-') return -1 * 0.0;
            return $ret;
        }

        $int = filter_var($val, FILTER_VALIDATE_INT, FILTER_FLAG_ALLOW_HEX); // For PHP 7
        if ($int !== false) return $int;

        $val = trim($val);
        if ($val === '') return 0;
        if ($val === 'Infinity' || $val === '+Infinity') return INF;
        if ($val === '-Infinity') return -INF;
        return NAN;
    };

    // http://stackoverflow.com/a/2123458
    // Hope this works
    $ToInt32 = function ($val) use ($ToNumber) {
        $val = $ToNumber($val);
        if (!is_finite($val)) return 0;

        $val &= 0xFFFFFFFF;

        if ($val & 0x80000000)
            $val = -((~$val & 0xFFFFFFFF) + 1);
        return $val;
    };
    $ToUint32 = function ($val) use ($ToNumber) {
        $val = $ToNumber($val);
        if (!is_finite($val)) return 0;

        $val = (($val >> 1) & 0x7FFFFFFF) * 2 + (($val >> 0) & 1);
        return $val;
    };

    $MAX_SAFE_INTEGER = pow(2, 53) - 1;

    $ToString = function ($val) use ($MAX_SAFE_INTEGER, $FloatToString) {
        if ($val === null) return 'null';

        if ($val === true) return 'true';
        if ($val === false) return 'false';

        if (is_string($val)) return $val;
        if (is_int($val) && abs($val) > $MAX_SAFE_INTEGER) // 64-bit integer
            $val = (float)$val;
        if (!is_float($val)) return (string)$val;

        if ($val === 0.0) return '0';
        if ($val !== $val) return 'NaN';
        if ($val === INF) return 'Infinity';
        if ($val === -INF) return '-Infinity';

        return $FloatToString($val);
    };

    $Type = function ($val) {
        if (is_null($val)) return 'object';
        if (is_bool($val)) return 'boolean';
        if (is_int($val) || is_float($val)) return 'number';
        if (is_string($val)) return 'string';
        if (is_callable($val)) return 'function'; // just for completeness
        return 'object';
    };

    $Relational = function ($x, $y) use ($ToNumber) {
        if (is_string($x) && is_string($y)) return strcmp($x, $y) < 0;
        $x = $ToNumber($x); $y = $ToNumber($y);
        if ($x !== $x) return null;
        if ($y !== $y) return null;
        return $x < $y;
    };

    $Equality = function ($x, $y) use ($ToNumber, $Type, &$Equality) {
        $Typex = $Type($x); $Typey = $Type($y);
        if ($Typex === $Typey) {
            if ($Typex === 'number') return $x == $y;
            return $x === $y;
        }
        if ($Typex === 'number' && $Typey === 'string') return $x == $ToNumber($y);
        if ($Typex === 'string' && $Typey === 'number') return $ToNumber($x) == $y;
        if ($Typex === 'boolean') return $Equality($ToNumber($x), $y);
        if ($Typey === 'boolean') return $Equality($x, $ToNumber($y));
        return false;
    };

    $StrictEquality = function ($x, $y) use ($Type) {
        $Typex = $Type($x); $Typey = $Type($y);
        if ($Typex !== $Typey) return false;
        if ($Typex === 'number') return $x == $y;
        return $x === $y;
    };

    // this can only evaluate constant expressions.  If it finds anything
    // not constant, it throws $NOT_CONSTANT.
    $evaluate = function ($expr) use (
        $NOT_CONSTANT,
        $ToBoolean,
        $ToNumber,
        $ToInt32,
        $ToUint32,
        $ToString,
        $Type,
        $Relational,
        $Equality,
        $StrictEquality,
        &$evaluate
    ) {
        switch ($expr[0]) {
          case "string":
          case "num":
            return $expr[1];
          case "name":
          case "atom":
            switch ($expr[1]) {
              case "true": return true;
              case "false": return false;
              case "null": return null;
            }
            break;
          case "unary-prefix":
            switch ($expr[1]) {
              case "!": return !$ToBoolean($evaluate($expr[2]));
              case "typeof": return $Type($evaluate($expr[2]));
              case "~": return ~$ToInt32($evaluate($expr[2]));
              case "-": return call_user_func(function ($a) {
              	               if ($a === 0) $a = 0.0;
              	               return -1 * $a;
              	            }, $ToNumber($evaluate($expr[2])));
              case "+": return $ToNumber($evaluate($expr[2]));
            }
            break;
          case "binary":
            $left = $expr[2]; $right = $expr[3];
            switch ($expr[1]) {
              case "&&"  : $lval = $evaluate($left); if ($ToBoolean($lval)) return $evaluate($right); else return $lval;
              case "||"  : $lval = $evaluate($left); if ($ToBoolean($lval)) return $lval; else return $evaluate($right);
              case "|"   : return $ToInt32($evaluate($left)) | $ToInt32($evaluate($right));
              case "&"   : return $ToInt32($evaluate($left)) & $ToInt32($evaluate($right));
              case "^"   : return $ToInt32($evaluate($left)) ^ $ToInt32($evaluate($right));
              case "+"   : return call_user_func(function ($a, $b) use ($ToNumber, $ToString) {
              	               if (is_string($a) || is_string($b)) {
              	                   return $ToString($a) . $ToString($b);
              	               }
              	               return $ToNumber($a) + $ToNumber($b);
              	           }, $evaluate($left), $evaluate($right));
              case "*"   : return call_user_func(function ($a, $b) use ($ToNumber) {
              	               $a = $ToNumber($a); $b = $ToNumber($b);
              	               if ($a === 0) $a = 0.0;
              	               if ($b === 0) $b = 0.0;
              	               return $a * $b;
              	           }, $evaluate($left), $evaluate($right));
              case "/"   : return call_user_func(function ($a, $b) use ($ToNumber) {
              	               $a = $ToNumber($a); $b = $ToNumber($b);
              	               if ($a === 0) $a = 0.0;
              	               if ($b) return $a / $b;
              	               if (!$a || $a !== $a) return NAN;
              	               if ((string)$b === '0') {
              	                   if ($a > 0) return INF;
              	                   return -INF;
              	               } else {
              	                   if ($a < 0) return INF;
              	                   return -INF;
              	               }
              	           }, $evaluate($left), $evaluate($right));
              case "%"   : return call_user_func(function ($a, $b) use ($ToNumber) {
              	               $a = $ToNumber($a); $b = $ToNumber($b);
              	               return fmod($a, $b);
              	           }, $evaluate($left), $evaluate($right));
              case "-"   : return call_user_func(function ($a, $b) use ($ToNumber) {
              	               $a = $ToNumber($a); $b = $ToNumber($b);
              	               return $a - $b;
              	           }, $evaluate($left), $evaluate($right));
              case "<<"  : return call_user_func(function ($a, $b) use ($ToInt32, $ToUint32) {
              	               $a = $ToInt32($a); $b = $ToUint32($b);
              	               $b &= 0x1F;
              	               return $ToInt32($a << $b);
              	           }, $evaluate($left), $evaluate($right));
              case ">>"  : return call_user_func(function ($a, $b) use ($ToInt32, $ToUint32) {
              	               $a = $ToInt32($a); $b = $ToUint32($b);
              	               $b &= 0x1F;
              	               return $a >> $b;
              	           }, $evaluate($left), $evaluate($right));
              case ">>>" : return call_user_func(function ($a, $b) use ($ToInt32, $ToUint32) {
              	               $b = $ToUint32($b);
              	               $b &= 0x1F;
              	               if ($b === 0) return $ToUint32($a);

              	               $a = $ToInt32($a);
              	               // http://stackoverflow.com/a/27263298
              	               if ($a >= 0) return $a >> $b;
              	               return ((~$a) >> $b) ^ (0x7FFFFFFF >> ($b - 1));
              	           }, $evaluate($left), $evaluate($right));
              case "=="  : return $Equality($evaluate($left), $evaluate($right));
              case "===" : return $StrictEquality($evaluate($left), $evaluate($right));
              case "!="  : return !$Equality($evaluate($left), $evaluate($right));
              case "!==" : return !$StrictEquality($evaluate($left), $evaluate($right));
              case "<"   : return (bool)$Relational($evaluate($left), $evaluate($right));
              case "<="  : return call_user_func(function ($r) {
              	               return !($r || $r === null);
              	           }, $Relational($evaluate($right), $evaluate($left)));
              case ">"   : return (bool)$Relational($evaluate($right), $evaluate($left));
              case ">="  : return call_user_func(function ($r) {
              	               return !($r || $r === null);
              	           }, $Relational($evaluate($left), $evaluate($right)));
            }
        }
        throw $NOT_CONSTANT;
    };

    return function($expr, $yes, $no) use (
        $NOT_CONSTANT,
        $ToBoolean,
        $ToString,
        $Type,
        $evaluate,
        $boolean_expr,
        $is_string
    ) {
        try {
            $val = $evaluate($expr);
            switch ($Type($val)) {
              case "string": $ast =  [ "string", $val ]; break;
              case "number": $ast =  [ "num", $val ]; break;
              case "boolean": $ast =  [ "name", $ToString($val) ]; break;
              default:
                if ($val === null) { $ast = [ "atom", "null" ]; break; }
                throw new Exception("Can't handle constant of type: " . $Type($val));
            }
            return call_user_func($yes, $expr, $ast, $val);
        } catch(Exception $ex) {
            if ($ex === $NOT_CONSTANT) {
                if ($expr[0] === "binary"
                    && ($expr[1] === "===" || $expr[1] === "!==")
                    && (($is_string($expr[2]) && $is_string($expr[3]))
                        || ($boolean_expr($expr[2]) && $boolean_expr($expr[3])))) {
                    $expr[1] = substr($expr[1], 0, 2);
                }
                elseif ($no && $expr[0] === "binary"
                         && ($expr[1] === "||" || $expr[1] === "&&")) {
                    // the whole expression is not constant but the lval may be...
                    try {
                        $lval = $evaluate($expr[2]);
                        if ($expr[1] === "&&") $expr = ($ToBoolean($lval) ? $expr[3] : $lval);
                        if ($expr[1] === "||") $expr = ($ToBoolean($lval) ? $lval    : $expr[3]);
                    } catch(Exception $ex2) {
                        // IGNORE... lval is not constant
                    }
                }
                return $no ? call_user_func($no, $expr) : null;
            }
            else throw $ex;
        }
    };

});

$warn_unreachable = function ($ast) use ($warn, $empty, &$gen_code) {
    if (!$empty($ast))
        $warn("Dropping unreachable code: " . $gen_code($ast, true));
};

$prepare_ifs = function ($ast) use ($MAP, $ast_walker, $aborts) {
    $w = $ast_walker(); $walk = $w['walk'];
    // In this first pass, we rewrite ifs which abort with no else with an
    // if-else.  For example:
    //
    // if (x) {
    //     blah();
    //     return y;
    // }
    // foobar();
    //
    // is rewritten into:
    //
    // if (x) {
    //     blah();
    //     return y;
    // } else {
    //     foobar();
    // }
    $redo_if = function ($statements) use ($MAP, $aborts, $walk, &$redo_if) {
        $statements = $MAP($statements, $walk);

        for ($i = 0, $len = count($statements); $i < $len; ++$i) {
            $fi = $statements[$i];
            if ($fi[0] !== "if") continue;

            if ($fi[3]) continue;

            $t = $fi[2];
            if (!$aborts($t)) continue;

            $conditional = $walk($fi[1]);

            $e_body = $redo_if(array_slice($statements, $i + 1));
            $e = count($e_body) === 1 ? $e_body[0] : [ "block", $e_body ];

            return array_merge(array_slice($statements, 0, $i), [ [
                $fi[0],          // "if"
                $conditional,    // conditional
                $t,              // then
                $e               // else
            ] ]);
        }

        return $statements;
    };

    $redo_if_lambda = function ($_this_0, $name, $args, $body) use ($redo_if) {
        $body = $redo_if($body);
        return [ $_this_0, $name, $args, $body ];
    };

    $redo_if_block = function ($_this_0, $statements = null) use ($redo_if) {
        return [ $_this_0, $statements !== null ? $redo_if($statements) : null ];
    };

    return $w['with_walkers']([
        "defun" => $redo_if_lambda,
        "function" => $redo_if_lambda,
        "block" => $redo_if_block,
        "toplevel" => function($_this_0, $statements) use ($redo_if) {
            return [ $_this_0, $redo_if($statements) ];
        },
        "try" => function($_this_0, $t, $c = null, $f = null) use ($redo_if) {
            return [
                $_this_0,
                $redo_if($t),
                $c !== null ? [ $c[0], $redo_if($c[1]) ] : null,
                $f !== null ? $redo_if($f) : null
            ];
        }
    ], function() use ($walk, $ast) {
        return $walk($ast);
    });
};

$squeeze_1 = function ($ast, $options = []) use (
    $MAP,
    $ast_walker,
    $defaults,
    $is_identifier,
    $best_of,
    $aborts,
    $boolean_expr,
    $empty,
    $when_constant,
    $warn,
    $warn_unreachable,
    $prepare_ifs,
    &$gen_code
) {
    $options = $defaults($options, [
        'make_seqs'   => true,
        'dead_code'   => true,
        'no_warnings' => false,
        'keep_comps'  => true,
        'unsafe'      => false
    ]);

    $w = $ast_walker(); $walk = $w['walk'];

    $ToBoolean = function ($val) {
        if ($val === '0') return true;
        if ($val !== $val) return false;
        return (bool)$val;
    };

    $negate = function ($c) use ($options, $best_of, $boolean_expr, &$negate) {
        $not_c = [ "unary-prefix", "!", $c ];
        switch ($c[0]) {
          case "unary-prefix":
            return $c[1] === "!" && $boolean_expr($c[2]) ? $c[2] : $not_c;
          case "seq":
            $c[count($c) - 1] = $negate($c[count($c) - 1]);
            return $c;
          case "conditional":
            return $best_of($not_c, [ "conditional", $c[1], $negate($c[2]), $negate($c[3]) ]);
          case "binary":
            $op = $c[1]; $left = $c[2]; $right = $c[3];
            if (!$options['keep_comps']) switch ($op) {
              case "<="  : return [ "binary", ">", $left, $right ];
              case "<"   : return [ "binary", ">=", $left, $right ];
              case ">="  : return [ "binary", "<", $left, $right ];
              case ">"   : return [ "binary", "<=", $left, $right ];
            }
            switch ($op) {
              case "=="  : return [ "binary", "!=", $left, $right ];
              case "!="  : return [ "binary", "==", $left, $right ];
              case "===" : return [ "binary", "!==", $left, $right ];
              case "!==" : return [ "binary", "===", $left, $right ];
              case "&&"  : return $best_of($not_c, [ "binary", "||", $negate($left), $negate($right) ]);
              case "||"  : return $best_of($not_c, [ "binary", "&&", $negate($left), $negate($right) ]);
            }
            break;
        }
        return $not_c;
    };

    $make_conditional = function ($c, $t, $e) use ($when_constant, $warn_unreachable, $best_of, $negate, $ToBoolean) {
        $make_real_conditional = function() use ($c, $t, $e, $best_of, $negate) {
            if ($c[0] === "unary-prefix" && $c[1] === "!") {
                return $e ? [ "conditional", $c[2], $e, $t ] : [ "binary", "||", $c[2], $t ];
            } else {
                return $e ? $best_of(
                    [ "conditional", $c, $t, $e ],
                    [ "conditional", $negate($c), $e, $t ]
                ) : [ "binary", "&&", $c, $t ];
            }
        };
        // shortcut the conditional if the expression has a constant value
        return $when_constant($c, function($_this, $ast, $val) use ($t, $e, $warn_unreachable, $ToBoolean) {
            $warn_unreachable($ToBoolean($val) ? $e : $t);
            return           ($ToBoolean($val) ? $t : $e);
        }, $make_real_conditional);
    };

    $rmblock = function ($block) {
        if ($block !== null && $block[0] === "block" && isset($block[1])) {
            $count = count($block[1]);
            if ($count === 1)
                $block = $block[1][0];
            elseif ($count === 0)
                $block = [ "block" ];
        }
        return $block;
    };

    // this function does a few things:
    // 1. discard useless blocks
    // 2. join consecutive var declarations
    // 3. remove obviously dead code
    // 4. transform consecutive statements using the comma operator
    $tighten = function ($statements) use (
        $MAP,
        $walk,
        $options,
        $warn,
        $warn_unreachable
    ) {
        $statements = $MAP($statements, $walk);

        $statements = array_reduce($statements, function($a, $stat) {
            if ($stat[0] === "block") {
                if (isset($stat[1])) {
                    $a = array_merge($a, $stat[1]);
                }
            } else {
                $a[] = $stat;
            }
            return $a;
        }, []);

        $statements = call_user_func(function ($a, $prev) use ($statements) {
            array_walk($statements, function ($cur) use (&$a, &$prev) {
                if ($prev && (($cur[0] === "var" && $a[$prev - 1][0] === "var") ||
                              ($cur[0] === "const" && $a[$prev - 1][0] === "const"))) {
                    $a[$prev - 1][1] = array_merge($a[$prev - 1][1], $cur[1]);
                } else {
                    $prev = array_push($a, $cur);
                }
            });
            return $a;
        }, [], 0);

        if ($options['dead_code']) $statements = call_user_func(function ($a, $has_quit) use (
            $statements,
            $options,
            $MAP,
            $warn,
            $warn_unreachable
        ) {
            array_walk($statements, function ($st) use (
                &$a,
                &$has_quit,
                $options,
                $MAP,
                $warn,
                $warn_unreachable
            ) {
                if ($has_quit) {
                    if ($st[0] === "function" || $st[0] === "defun") {
                        $a[] = $st;
                    }
                    elseif ($st[0] === "var" || $st[0] === "const") {
                        if (!$options['no_warnings'])
                            $warn("Variables declared in unreachable code");
                        $st[1] = $MAP($st[1], function ($def) use ($options, $warn_unreachable) {
                            if ($def[1] && !$options['no_warnings'])
                                $warn_unreachable([ "assign", true, [ "name", $def[0] ], $def[1] ]);
                            return [ $def[0] ];
                        });
                        $a[] = $st;
                    }
                    elseif (!$options['no_warnings'])
                        $warn_unreachable($st);
                }
                else {
                    $a[] = $st;
                    if (in_array($st[0], [ "return", "throw", "break", "continue" ]))
                        $has_quit = true;
                }
            });
            return $a;
        }, [], null);

        if ($options['make_seqs']) $statements = call_user_func(function ($a, $prev) use ($statements) {
            array_walk($statements, function ($cur) use (&$a, &$prev) {
                if ($prev && $a[$prev - 1][0] === "stat" && $cur[0] === "stat") {
                    $a[$prev - 1][1] = [ "seq", $a[$prev - 1][1], $cur[1] ];
                } else {
                    $prev = array_push($a, $cur);
                }
            });
            $len = count($a);
            if ($len >= 2
                && $a[$len - 2][0] === "stat"
                && ($a[$len - 1][0] === "return" || $a[$len - 1][0] === "throw")
                && $a[$len - 1][1])
            {
                array_splice($a, $len - 2, 2,
                        [[ $a[$len - 1][0],
                           [ "seq", $a[$len - 2][1], $a[$len - 1][1] ]]]);
            }
            return $a;
        }, [], 0);

        return $statements;
    };

    $_lambda = function ($_this_0, $name, $args, $body) use ($tighten) {
        return [ $_this_0, $name, $args, $tighten($body, "lambda") ];
    };

    $abort_else = function ($c, $t, $e) use ($walk, $negate) {
        $ret = [ [ "if", $negate($c), $e ] ];
        if ($t[0] === "block") {
            if (isset($t[1])) $ret = array_merge($ret, $t[1]);
        } else {
            $ret[] = $t;
        }
        return $walk([ "block", $ret ]);
    };

    $make_real_if = function ($c, $t, $e) use (
        $best_of,
        $aborts,
        $empty,
        $walk,
        $negate,
        $make_conditional,
        $abort_else,
        &$gen_code
    ) {
        $c = $walk($c);
        $t = $walk($t);
        $e = $walk($e);

        if ($empty($e) && $empty($t))
            return [ "stat", $c ];

        if ($empty($t)) {
            $c = $negate($c);
            $t = $e;
            $e = null;
        } elseif ($empty($e)) {
            $e = null;
        } else {
            // if we have both else and then, maybe it makes sense to switch them?
            call_user_func(function() use (&$c, &$t, &$e, $negate, &$gen_code) {
                $a = $gen_code($c);
                $n = $negate($c);
                $b = $gen_code($n);
                if (strlen($b) < strlen($a)) {
                    $tmp = $t;
                    $t = $e;
                    $e = $tmp;
                    $c = $n;
                }
            });
        }
        $ret = [ "if", $c, $t, $e ];
        if ($t[0] === "if" && $empty($t[3]) && $empty($e)) {
            $ret = $best_of($ret, $walk([ "if", [ "binary", "&&", $c, $t[1] ], $t[2] ]));
        }
        elseif ($t[0] === "stat") {
            if ($e) {
                if ($e[0] === "stat")
                    $ret = $best_of($ret, [ "stat", $make_conditional($c, $t[1], $e[1]) ]);
                elseif ($aborts($e))
                    $ret = $abort_else($c, $t, $e);
            }
            else {
                $ret = $best_of($ret, [ "stat", $make_conditional($c, $t[1], null) ]);
            }
        }
        elseif ($e && $t[0] === $e[0] && ($t[0] === "return" || $t[0] === "throw") && $t[1] && $e[1]) {
            $ret = $best_of($ret, [ $t[0], $make_conditional($c, $t[1], $e[1] ) ]);
        }
        elseif ($e && $aborts($t)) {
            $ret = [ [ "if", $c, $t ] ];
            if ($e[0] === "block") {
                if (isset($e[1])) $ret = array_merge($ret, $e[1]);
            }
            else {
                $ret[] = $e;
            }
            $ret = $walk([ "block", $ret ]);
        }
        elseif ($t && $aborts($e)) {
            $ret = $abort_else($c, $t, $e);
        }
        return $ret;
    };

    $make_if = function ($_this_0, $c, $t, $e = null) use (
        $when_constant,
        $warn_unreachable,
        $walk,
        $ToBoolean,
        $make_real_if
    ) {
        return $when_constant($c, function($_this, $ast, $val) use ($t, $e, $warn_unreachable, $walk, $ToBoolean) {
            if ($ToBoolean($val)) {
                $t = $walk($t);
                $warn_unreachable($e);
                return $t ?: [ "block" ];
            } else {
                $e = $walk($e);
                $warn_unreachable($t);
                return $e ?: [ "block" ];
            }
        }, function() use ($c, $t, $e, $make_real_if) {
            return $make_real_if($c, $t, $e);
        });
    };

    $_do_while = function ($_this_0, $cond, $body) use ($when_constant, $warn_unreachable, $walk, $ToBoolean) {
        return $when_constant($cond, function($_this, $cond, $val) use ($body, $warn_unreachable, $walk, $ToBoolean) {
            if (!$ToBoolean($val)) {
                $warn_unreachable($body);
                return [ "block" ];
            } else {
                return [ "for", null, null, null, $walk($body) ];
            }
        }, null);
    };

    return $w['with_walkers']([
        "sub" => function($_this_0, $expr, $subscript) use ($is_identifier, $walk) {
            if ($subscript[0] === "string") {
                $name = $subscript[1];
                if ($is_identifier($name))
                    return [ "dot", $walk($expr), $name ];
                elseif (preg_match('/^[1-9][0-9]*$/', $name) || $name === "0")
                    return [ "sub", $walk($expr), [ "num", +$name ] ];
            }
        },
        "if" => $make_if,
        "toplevel" => function($_this_0, $body) use ($tighten) {
            return [ "toplevel", $tighten($body) ];
        },
        "switch" => function($_this_0, $expr, $body) use ($MAP, $walk, $tighten) {
            $last = count($body) - 1;
            return [ "switch", $walk($expr), $MAP($body, function($branch, $i) use ($walk, $tighten, $last) {
                $block = $tighten($branch[1]);
                if ($i === $last && ($count = count($block)) > 0) {
                    $node = $block[$count - 1];
                    if ($node[0] === "break" && !$node[1])
                        array_pop($block);
                }
                return [ $branch[0] ? $walk($branch[0]) : null, $block ];
            }) ];
        },
        "function" => $_lambda,
        "defun" => $_lambda,
        "block" => function($_this_0, $body = null) use ($rmblock, $tighten) {
            if ($body !== null) return $rmblock([ "block", $tighten($body) ]);
        },
        "binary" => function($_this_0, $op, $left, $right) use ($when_constant, $walk, $best_of) {
            return $when_constant([ "binary", $op, $walk($left), $walk($right) ], function ($_this, $c) use ($walk, $best_of) {
                return $best_of($walk($c), $_this);
            }, function ($_this) use ($op, $left, $right, $walk) {
                return call_user_func(function() use ($op, $left, $right, $walk) {
                    if ($op !== "==" && $op !== "!=") return;
                    $l = $walk($left); $r = $walk($right);
                    if($l && $l[0] === "unary-prefix" && $l[1] === "!" && $l[2][0] === "num")
                        $left = ['num', +!$l[2][1]];
                    elseif ($r && $r[0] === "unary-prefix" && $r[1] === "!" && $r[2][0] === "num")
                        $right = ['num', +!$r[2][1]];
                    return ["binary", $op, $left, $right];
                }) ?: $_this;
            });
        },
        "conditional" => function($_this_0, $c, $t, $e) use ($walk, $make_conditional) {
            return $make_conditional($walk($c), $walk($t), $walk($e));
        },
        "try" => function($_this_0, $t, $c = null, $f = null) use ($tighten) {
            return [
                "try",
                $tighten($t),
                $c !== null ? [ $c[0], $tighten($c[1]) ] : null,
                $f !== null ? $tighten($f) : null
            ];
        },
        "unary-prefix" => function($_this_0, $op, $expr) use ($when_constant, $walk, $best_of, $negate) {
            $expr = $walk($expr);
            $ret = [ "unary-prefix", $op, $expr ];
            if ($op === "!")
                $ret = $best_of($ret, $negate($expr));
            return $when_constant($ret, function($_this, $ast, $val) use ($walk) {
                return $walk($ast); // it's either true or false, so minifies to !0 or !1
            }, function() use ($ret) { return $ret; });
        },
        "name" => function($_this_0, $name) {
            switch ($name) {
              case "true": return [ "unary-prefix", "!", [ "num", 0 ]];
              case "false": return [ "unary-prefix", "!", [ "num", 1 ]];
            }
        },
        "while" => $_do_while,
        "assign" => function($_this_0, $op, $lvalue, $rvalue) use ($walk) {
            $lvalue = $walk($lvalue);
            $rvalue = $walk($rvalue);
            $okOps = [ '+', '-', '/', '*', '%', '>>', '<<', '>>>', '|', '^', '&' ];
            if ($op === true && $lvalue[0] === "name" && $rvalue[0] === "binary" &&
                array_search($rvalue[1], $okOps, true) !== false && $rvalue[2][0] === "name" &&
                $rvalue[2][1] === $lvalue[1]) {
                return [ $_this_0, $rvalue[1], $lvalue, $rvalue[3] ];
            }
            return [ $_this_0, $op, $lvalue, $rvalue ];
        },
        "call" => function($_this_0, $expr, $args) use ($MAP, $walk, $options) {
            $expr = $walk($expr);
            if ($options['unsafe'] && $expr[0] === "dot" && $expr[1][0] === "string" && $expr[2] === "toString") {
                return $expr[1];
            }
            return [ $_this_0, $expr,  $MAP($args, $walk) ];
        },
        "num" => function ($_this_0, $num) {
            if ((string)$num === '-0')
                return [ "unary-prefix", "-", [ "num", 0 ] ];
            if (!is_finite($num))
                return [ "binary", "/", $num === INF
                         ? [ "num", 1 ] : ($num === -INF
                         ? [ "unary-prefix", "-", [ "num", 1 ] ]
                         : [ "num", 0 ]), [ "num", 0 ] ];

            return [ $_this_0, $num ];
        }
    ], function() use ($ast, $walk, $prepare_ifs) {
        return $walk($prepare_ifs($walk($prepare_ifs($ast))));
    });
};

$squeeze_2 = function ($ast, $options = []) use (
    $MAP,
    $ast_walker,
    $curry,
    $ast_add_scope
) {
    $w = $ast_walker(); $walk = $w['walk']; $scope = null;
    $with_scope = function ($s, $cont) use (&$scope) {
        $save = $scope;
        $scope = $s;
        $ret = $cont();
        $scope = $save;
        return $ret;
    };
    $lambda = function ($_this_0, $name, $args, $body) use ($curry, $MAP, $walk, $with_scope) {
        return [ $_this_0, $name, $args, $with_scope($body['scope'], $curry($MAP, $body, $walk)) ];
    };
    return $w['with_walkers']([
        "directive" => function($_this_0, $dir) use (&$scope) {
            if ($scope['active_directive']($dir))
                return [ "block" ];
            $scope['directives'][] = $dir;
        },
        "toplevel" => function($_this_0, $body, $_this_scope) use ($curry, $MAP, $walk, $with_scope) {
            return [ $_this_0, $with_scope($_this_scope, $curry($MAP, $body, $walk)) ];
        },
        "function" => $lambda,
        "defun" => $lambda
    ], function() use ($ast, $walk, $ast_add_scope) {
        return $walk($ast_add_scope($ast));
    });
};

$ast_squeeze = function ($ast, $options = []) use ($squeeze_1, $squeeze_2) {
    $ast = $squeeze_1($ast, $options);
    $ast = $squeeze_2($ast, $options);
    return $ast;
};

$ast_squeeze_more = function ($ast) use (
    $MAP,
    $ast_walker,
    $curry,
    $ast_add_scope
) {
    $w = $ast_walker(); $walk = $w['walk']; $scope = null;
    $with_scope = function ($s, $cont) use (&$scope) {
        $save = $scope;
        $scope = $s;
        $ret = $cont();
        $scope = $save;
        return $ret;
    };
    $_lambda = function ($_this_0, $name, $args, $body) use ($curry, $MAP, $walk, $with_scope) {
        return [ $_this_0, $name, $args, $with_scope($body['scope'], $curry($MAP, $body, $walk)) ];
    };
    return $w['with_walkers']([
        "toplevel" => function($_this_0, $body, $_this_scope) use ($curry, $MAP, $walk, $with_scope) {
            return [ $_this_0, $with_scope($_this_scope, $curry($MAP, $body, $walk)) ];
        },
        "function" => $_lambda,
        "defun" => $_lambda,
        "new" => function($_this_0, $ctor, $args) use ($walk, &$scope) {
            if ($ctor[0] === "name") {
                if ($ctor[1] === "Array" && !$scope['has']("Array")) {
                    if (count($args) !== 1) {
                        return [ "array", $args ];
                    } else {
                        return $walk([ "call", [ "name", "Array" ], $args ]);
                    }
                } elseif ($ctor[1] === "Object" && !$scope['has']("Object")) {
                    if (!count($args)) {
                        return [ "object", [] ];
                    } else {
                        return $walk([ "call", [ "name", "Object" ], $args ]);
                    }
                } elseif (($ctor[1] === "RegExp" || $ctor[1] === "Function" || $ctor[1] === "Error") && !$scope['has']($ctor[1])) {
                    return $walk([ "call", [ "name", $ctor[1] ], $args]);
                }
            }
        },
        "call" => function($_this_0, $expr, $args) use (&$scope) {
            if ($expr[0] === "dot" && $expr[1][0] === "string" && count($args) === 1
                && ($args[0][1] > 0 && $expr[2] === "substring" || $expr[2] === "substr")) {
                return [ "call", [ "dot", $expr[1], "slice"], $args];
            }
            if ($expr[0] === "dot" && $expr[2] === "toString" && count($args) === 0) {
                // foo.toString()  ==>  foo+""
                if ($expr[1][0] === "string") return $expr[1];
                return [ "binary", "+", $expr[1], [ "string", "" ]];
            }
            if ($expr[0] === "name") {
                if ($expr[1] === "Array" && count($args) !== 1 && !$scope['has']("Array")) {
                    return [ "array", $args ];
                }
                if ($expr[1] === "Object" && !count($args) && !$scope['has']("Object")) {
                    return [ "object", [] ];
                }
                if ($expr[1] === "String" && !$scope['has']("String")) {
                    return [ "binary", "+", $args[0], [ "string", "" ]];
                }
            }
        }
    ], function() use ($ast, $walk, $ast_add_scope) {
        return $walk($ast_add_scope($ast));
    });
};

/* -----[ re-generate code from the AST ]----- */

$DOT_CALL_NO_PARENS = $array_to_hash([
    "name",
    "array",
    "object",
    "string",
    "dot",
    "sub",
    "call",
    "regexp",
    "defun"
]);

$to_ascii = function ($str) {
    return preg_replace_callback('/[\x{0080}-\x{FFFF}]/u', function($matches) {
        return trim(json_encode($matches[0]), '"');
    }, $str);
};

$make_string = function ($str, $ascii_only = false) use ($to_ascii) {
    $dq = 0; $sq = 0;
    $str = preg_replace_callback('/[\\\\\b\f\n\r\t\x22\x27\x{2028}\x{2029}\0]/u', function($matches) use (&$dq, &$sq) {
        $s = $matches[0];
        switch ($s) {
          case "\\": return '\\\\';
          case chr(8): return '\\b';
          case "\f": return '\\f';
          case "\n": return '\\n';
          case "\r": return '\\r';
          case json_decode('"\u2028"'): return '\\u2028';
          case json_decode('"\u2029"'): return '\\u2029';
          case '"': ++$dq; return '"';
          case "'": ++$sq; return "'";
          case "\0": return '\\0';
        }
        return $s;
    }, $str);
    if ($ascii_only) $str = $to_ascii($str);
    if ($dq > $sq) return "'" . str_replace("'", "\\'", $str) . "'";
    else return '"' . str_replace('"', '\\"', $str) . '"';
};

$strip_lines = function ($code, $max_line_length = 0) {
    $lines = explode("\n", $code);
    if (!$max_line_length) return join($lines);
    $a = []; $new_line = '';
    foreach ($lines as $line) {
        if (isset($new_line[$max_line_length])) {
            $a[] = $new_line;
            $new_line = '';
        }
        $new_line .= $line;
    }
    $a[] = $new_line;
    return join("\n", $a);
};

$gen_code = function ($ast, $options = []) use (
    $MAP,
    $OPERATORS,
    $PRECEDENCE,
    $DOT_CALL_NO_PARENS,
    $FloatToString,
    $ast_walker,
    $is_identifier_char,
    $is_identifier,
    $repeat_string,
    $defaults,
    $empty,
    $to_ascii,
    $make_string
) {
    $w = $ast_walker();
    $make = $w['walk'];
    $options = $defaults($options, [
        'indent_start' => 0,
        'indent_level' => 4,
        'quote_keys'   => false,
        'space_colon'  => false,
        'beautify'     => false,
        'ascii_only'   => false,
        'inline_script'=> false
    ]);
    $beautify = !!$options['beautify'];
    $indentation = 0; $newline = "\n";
    //$newline = $beautify ? "\n" : "";
    $space = $beautify ? " " : "";

    $MAX_SAFE_INTEGER = pow(2, 53) - 1;

    $ToString = function ($val) use ($MAX_SAFE_INTEGER, $FloatToString) {

        if (is_int($val) && abs($val) > $MAX_SAFE_INTEGER) // 64-bit integer
            $val = (float)$val;
        if (!is_float($val)) return (string)$val;

        if ($val === 0.0) return '0';
        if ($val !== $val) return 'NaN';
        if ($val === INF) return 'Infinity';
        if ($val === -INF) return '-Infinity';

        return $FloatToString($val);
    };

    $encode_string = function ($_this_0, $str) use ($options, $make_string) {
        $ret = $make_string($str, $options['ascii_only']);
        if ($options['inline_script'])
            $ret = preg_replace('/<\x2fscript([>\/\t\n\f\r ])/i', "<\\/script$1", $ret);
        return $ret;
    };

    $make_name = function ($name) use ($options, $to_ascii) {
        if ($options['ascii_only'])
            $name = $to_ascii($name);
        return $name;
    };

    $indent = function ($line = '') use ($options, $beautify, &$indentation, $repeat_string) {
        if ($beautify)
            $line = $repeat_string(' ', $options['indent_start'] + $indentation * $options['indent_level']) . $line;
        return $line;
    };

    $with_indent = function ($cont, $incr = 1) use (&$indentation) {
        $indentation += $incr;
        try { $ret = $cont(); }
        catch (Exception $ex) { $indentation -= $incr; throw $ex; }
        $indentation -= $incr; return $ret;
    };

    $add_spaces = function ($a) use ($beautify, $is_identifier_char) {
        if ($beautify)
            return join(" ", $a);
        $b = [];
        for ($i = 0; ; ++$i) {
            $b[] = $a[$i];
            if (!isset($a[$i + 1])) break;
            $len = strlen($a[$i]); $last_char = $a[$i][$len - 1]; $first_char = $a[$i + 1][0];
            if (($is_identifier_char($last_char) && ($is_identifier_char($first_char)
                                                          || $first_char === "\\")) ||
                 (strpbrk($last_char, '+-') && strpbrk($first_char, '+-') ||
                 $last_char === "/" && $first_char === "/")) {
                $b[] = " ";
            }
        }
        return join($b);
    };

    $add_commas = function ($a) use ($space) {
        return join("," . $space, $a);
    };

    $parenthesize = function ($expr) use ($make) {
        $gen = $make($expr);
        $arguments = func_get_args();
        for ($i = 1, $n = func_num_args(); $i < $n; ++$i) {
            $el = $arguments[$i];
            if (($el instanceof Closure && $el($expr)) || $expr[0] == $el)
                return "(" . $gen . ")";
        }
        return $gen;
    };

    $needs_parens = function ($_this_0) use ($DOT_CALL_NO_PARENS, $w) {
        if ($_this_0 === "function" || $_this_0 === "object") {
            // dot/call on a literal function requires the
            // function literal itself to be parenthesized
            // only if it's the first "thing" in a
            // statement.  This means that the parent is
            // "stat", but it could also be a "seq" and
            // we're the first in this "seq" and the
            // parent is "stat", and so on.  Messy stuff,
            // but it worths the trouble.
            $a = $w['stack'](); $self = array_pop($a); $p = array_pop($a);
            while ($p) {
                $_p_0 = $p[0];
                if ($_p_0 === "stat") return true;
                if ((($_p_0 === "seq" || $_p_0 === "call" || $_p_0 === "dot" || $_p_0 === "sub" || $_p_0 === "conditional") && $p[1] === $self) ||
                    (($_p_0 === "binary" || $_p_0 === "assign" || $_p_0 === "unary-postfix") && $p[2] === $self)) {
                    $self = $p;
                    $p = array_pop($a);
                } else {
                    return false;
                }
            }
        }
        return !isset($DOT_CALL_NO_PARENS[$_this_0]);
    };

    $dechex_limit = min(PHP_INT_MAX * 2 + 1, $MAX_SAFE_INTEGER);

    $make_num = function ($_this_0, $num) use ($dechex_limit, $ToString) {
        $str = $ToString($num); $a = [ preg_replace(['/^0\./', '/e\+/'], ['.', 'e'], $str) ];
        if (is_int($num) || floor($num) === $num) {
            if ($num >= 0) $sign = '';
            else { $num = -$num; $sign = '-'; }
            if ($num <= $dechex_limit) {
                $a[] = $sign . '0x' . dechex($num); // probably pointless
                // no longer octal
            } else {
                static $chars = ['0','1','2','3','4','5','6','7','8','9',
                                 'a','b','c','d','e','f'];
                $h = '';
                do {
                    $remainder = fmod($num, 16);
                    $h = $chars[(int)$remainder] . $h;
                    $num -= $remainder;
                    $num /= 16;
                } while ($num >= 1);
                $a[] = $sign . '0x' . $h; // probably pointless
            }
            if (preg_match('/^(.*?)(0+)$/', $str, $m)) {
                $a[] = $m[1] . "e" . strlen($m[2]);
            }
        } elseif (preg_match('/^0?\.(0+)(.*)$/', $str, $m)) {
            $a[] = $m[2] . "e-" . strlen($m[1] . $m[2]);
        }
        foreach ($a as $item) {
            if (!isset($ret) || strlen($ret) > strlen($item)) $ret = $item;
        }
        return $ret;
    };

    // The squeezer replaces "block"-s that contain only a single
    // statement with the statement itself; technically, the AST
    // is correct, but this can create problems when we output an
    // IF having an ELSE clause where the THEN clause ends in an
    // IF *without* an ELSE block (then the outer ELSE would refer
    // to the inner IF).  This function checks for this case and
    // adds the block brackets if needed.
    $make_then = function ($th = null) use ($make, &$make_block) {
        if ($th === null) return ";";
        if ($th[0] === "do") {
            // https://github.com/mishoo/UglifyJS/issues/#issue/57
            // IE croaks with "syntax error" on code like this:
            //     if (foo) do ... while(cond); else ...
            // we need block brackets around do/while
            return $make_block([ $th ]);
        }
        $b = $th;
        while (true) {
            $type = $b[0];
            if ($type === "if") {
                if (!$b[3])
                    // no else, we must add the block
                    return $make([ "block", [ $th ]]);
                $b = $b[3];
            }
            elseif ($type === "while" || $type === "do") $b = $b[2];
            elseif ($type === "for" || $type === "for-in") $b = $b[4];
            else break;
        }
        return $make($th);
    };

    $make_function = function ($_this_0, $name, $args, $body, $keyword = '', $no_parens = false) use (
        $MAP,
        $make_name,
        $add_spaces,
        $add_commas,
        $needs_parens,
        &$make_block
    ) {
        $out = $keyword ?: "function";
        if ($name) {
            $out .= " " . $make_name($name);
        }
        $out .= "(" . $add_commas($MAP($args, $make_name)) . ")";
        $out = $add_spaces([ $out, $make_block($body) ]);
        return (!$no_parens && $needs_parens($_this_0)) ? "(" . $out . ")" : $out;
    };

    $must_has_semicolon = function ($node) use ($empty, &$must_has_semicolon) {
        switch ($node[0]) {
          case "with":
          case "while":
            return $empty($node[2]) || $must_has_semicolon($node[2]);
          case "for":
          case "for-in":
            return $empty($node[4]) || $must_has_semicolon($node[4]);
          case "if":
            if ($empty($node[2]) && !$node[3]) return true; // `if' with empty `then' and no `else'
            if ($node[3]) {
                if ($empty($node[3])) return true; // `else' present but empty
                return $must_has_semicolon($node[3]); // dive into the `else' branch
            }
            return $must_has_semicolon($node[2]); // dive into the `then' branch
          case "directive":
            return true;
        }
    };

    $make_block_statements = function ($statements, $noindent = false) use (
        $MAP,
        $make,
        $beautify,
        $indent,
        $must_has_semicolon
    ) {
        for ($a = [], $last = count($statements) - 1, $i = 0; $i <= $last; ++$i) {
            $stat = $statements[$i];
            $code = $make($stat);
            if ($code !== ";") {
                if (!$beautify && $i === $last && !$must_has_semicolon($stat)) {
                    $code = preg_replace('/;+\s*$/', '', $code);
                }
                $a[] = $code;
            }
        }
        return $noindent ? $a : $MAP($a, $indent);
    };

    $make_switch_block = function ($body) use (
        $MAP,
        $make,
        $beautify,
        $newline,
        $indent,
        $with_indent,
        $add_spaces,
        $make_block_statements
    ) {
        $n = count($body);
        if ($n === 0) return "{}";
        return "{" . $newline . join($newline, $MAP($body, function ($branch, $i) use (
            $n,
            $make,
            $beautify,
            $newline,
            $indent,
            $with_indent,
            $add_spaces,
            $make_block_statements
        ) {
            $has_body = count($branch[1]) > 0; $code = $with_indent(function () use (
                $make,
                $branch,
                $indent,
                $add_spaces
            ) {
                return $indent($branch[0]
                              ? $add_spaces([ "case", $make($branch[0]) . ":" ])
                              : "default:");
            }, 0.5) . ($has_body ? $newline . $with_indent(function () use (
                $branch,
                $newline,
                $make_block_statements
            ) {
                return join($newline, $make_block_statements($branch[1]));
            }) : "");
            if (!$beautify && $has_body && $i < $n - 1)
                $code .= ";";
            return $code;
        })) . $newline . $indent("}");
    };

    $make_block = function ($statements) use ($newline, $indent, $with_indent, $make_block_statements) {
        if ($statements === null) return ";";
        if ($statements === []) return "{}";
        return "{" . $newline . $with_indent(function () use ($statements, $newline, $make_block_statements) {
            return join($newline, $make_block_statements($statements));
        }) . $newline . $indent("}");
    };

    $make_1vardef = function ($def) use ($make_name, $add_spaces, $parenthesize) {
        $name = $def[0]; $val = $def[1];
        if ($val !== null)
            $name = $add_spaces([ $make_name($name), "=", $parenthesize($val, "seq") ]);
        return $name;
    };

    return $w['with_walkers']([
        "string" => $encode_string,
        "num" => $make_num,
        "name" => function($_this_0, $name) use ($make_name) {
            return $make_name($name);
        },
        "debugger" => function(){ return "debugger;"; },
        "toplevel" => function($_this_0, $statements) use ($newline, $make_block_statements) {
            return join($newline . $newline,
                $make_block_statements($statements));
        },
        "block" => function($_this_0, $statements = null) use ($make_block) {
            return $make_block($statements);
        },
        "var" => function($_this_0, $defs) use ($MAP, $add_commas, $make_1vardef) {
            return "var " . $add_commas($MAP($defs, $make_1vardef)) . ";";
        },
        "const" => function($_this_0, $defs) use ($MAP, $add_commas, $make_1vardef) {
            return "const " . $add_commas($MAP($defs, $make_1vardef)) . ";";
        },
        "try" => function($_this_0, $tr, $ca = null, $fi = null) use ($add_spaces, $make_block) {
            $out = [ "try", $make_block($tr) ];
            if ($ca) array_push($out, "catch", "(" . $ca[0] . ")", $make_block($ca[1]));
            if ($fi) array_push($out, "finally", $make_block($fi));
            return $add_spaces($out);
        },
        "throw" => function($_this_0, $expr) use ($make, $add_spaces) {
            return $add_spaces([ "throw", $make($expr) ]) . ";";
        },
        "new" => function($_this_0, $ctor, $args) use (
            $MAP,
            $ast_walker,
            $add_spaces,
            $add_commas,
            $parenthesize
        ) {
            $args = count($args) > 0 ? "(" . $add_commas($MAP($args, function($expr) use ($parenthesize) {
                return $parenthesize($expr, "seq");
            })) . ")" : "";
            return $add_spaces([ "new", $parenthesize($ctor, "seq", "binary", "conditional", "assign", function($expr) use ($ast_walker) {
                $w = $ast_walker(); $has_call = new Exception();
                try {
                    $w['with_walkers']([
                        "call" => function() use ($has_call) { throw $has_call; },
                        "function" => function() { return func_get_args(); }
                    ], function() use ($w, $expr) {
                        $w['walk']($expr);
                    });
                } catch(Exception $ex) {
                    if ($ex === $has_call)
                        return true;
                    throw $ex;
                }
            }) . $args ]);
        },
        "switch" => function($_this_0, $expr, $body) use ($make, $add_spaces, $make_switch_block) {
            return $add_spaces([ "switch", "(" . $make($expr) . ")", $make_switch_block($body) ]);
        },
        "break" => function($_this_0, $label = null) use ($make_name) {
            $out = "break";
            if ($label !== null)
                $out .= " " . $make_name($label);
            return $out . ";";
        },
        "continue" => function($_this_0, $label = null) use ($make_name) {
            $out = "continue";
            if ($label !== null)
                $out .= " " . $make_name($label);
            return $out . ";";
        },
        "conditional" => function($_this_0, $co, $th, $el) use ($add_spaces, $parenthesize) {
            return $add_spaces([ $parenthesize($co, "assign", "seq", "conditional"), "?",
                                 $parenthesize($th, "seq"), ":",
                                 $parenthesize($el, "seq") ]);
        },
        "assign" => function($_this_0, $op, $lvalue, $rvalue) use ($make, $add_spaces, $parenthesize) {
            if ($op && $op !== true) $op .= "=";
            else $op = "=";
            return $add_spaces([ $make($lvalue), $op, $parenthesize($rvalue, "seq") ]);
        },
        "dot" => function($_this_0, $expr) use ($make, $make_name, $needs_parens) {
            $out = $make($expr); $i = 2;
            if ($expr[0] === "num") {
                if (!preg_match('/[a-f.]/i', $out))
                    $out .= ".";
            } elseif ($expr[0] !== "function" && $needs_parens($expr[0]))
                $out = "(" . $out . ")";
            $arguments = func_get_args(); $length = func_num_args();
            while ($i < $length)
                $out .= "." . $make_name($arguments[$i++]);
            return $out;
        },
        "call" => function($_this_0, $func, $args) use ($MAP, $make, $add_commas, $parenthesize, $needs_parens) {
            $f = $make($func);
            // cannot simply test the first and/or the last characters in the genetic case,
            // because the called expression might look like e.g. `(x || y) && (u || v)`.
            $already_wrapped = ($func[0] === "function" && $f[0] === "(");
            if (!$already_wrapped && $needs_parens($func[0]))
                $f = "(" . $f . ")";
            return $f . "(" . $add_commas($MAP($args, function($expr) use ($parenthesize) {
                return $parenthesize($expr, "seq");
            })) . ")";
        },
        "function" => $make_function,
        "defun" => $make_function,
        "if" => function($_this_0, $co, $th, $el) use ($make, $add_spaces, $make_then) {
            $out = [ "if", "(" . $make($co) . ")", $el ? $make_then($th) : $make($th) ];
            if ($el) {
                array_push($out, "else", $make($el));
            }
            return $add_spaces($out);
        },
        "for" => function($_this_0, $init, $cond, $step, $block) use ($make, $space, $add_spaces) {
            $out = [ "for" ];
            $init = preg_replace('/;*\s*$/', ';' . $space, ($init !== null ? $make($init) : ''), 1);
            $cond = preg_replace('/;*\s*$/', ';' . $space, ($cond !== null ? $make($cond) : ''), 1);
            $step = preg_replace('/;*\s*$/', '', ($step !== null ? $make($step) : ''));
            $args = $init . $cond . $step;
            if ($args === "; ; ") $args = ";;";
            array_push($out, "(" . $args . ")", $make($block));
            return $add_spaces($out);
        },
        "for-in" => function($_this_0, $vvar, $key, $hash, $block) use ($make, $add_spaces) {
            return $add_spaces([ "for", "(" .
                                 ($vvar ? preg_replace('/;+$/', '', $make($vvar)) : $make($key)),
                                 "in",
                                 $make($hash) . ")", $make($block) ]);
        },
        "while" => function($_this_0, $condition, $block) use ($make, $add_spaces) {
            return $add_spaces([ "while", "(" . $make($condition) . ")", $make($block) ]);
        },
        "do" => function($_this_0, $condition, $block) use ($make, $add_spaces) {
            return $add_spaces([ "do", $make($block), "while", "(" . $make($condition) . ")" ]) . ";";
        },
        "return" => function($_this_0, $expr) use ($make, $add_spaces) {
            $out = [ "return" ];
            if ($expr !== null) $out[] = $make($expr);
            return $add_spaces($out) . ";";
        },
        "binary" => function($_this_0, $operator, $lvalue, $rvalue) use (
            $options,
            $beautify,
            $PRECEDENCE,
            $make,
            $add_spaces,
            $needs_parens
        ) {
            $left = $make($lvalue); $right = $make($rvalue);
            // XXX: I'm pretty sure other cases will bite here.
            //      we need to be smarter.
            //      adding parens all the time is the safest bet.
            $_lvalue_0 = $lvalue[0]; $_rvalue_0 = $rvalue[0];
            if (in_array($_lvalue_0, [ "assign", "conditional", "seq" ]) ||
                $_lvalue_0 === "binary" && $PRECEDENCE[$operator] > $PRECEDENCE[$lvalue[1]] ||
                $_lvalue_0 === "function" && $needs_parens($_this_0)) {
                $left = "(" . $left . ")";
            }
            if (in_array($_rvalue_0, [ "assign", "conditional", "seq" ]) ||
                $_rvalue_0 === "binary" && $PRECEDENCE[$operator] >= $PRECEDENCE[$rvalue[1]] &&
                !($rvalue[1] === $operator && in_array($operator, [ "&&", "||", "*" ]))) {
                $right = "(" . $right . ")";
            }
            elseif (!$beautify && $options['inline_script'] && ($operator === "<" || $operator === "<<")
                     && $_rvalue_0 === "regexp" && strncasecmp($rvalue[1], 'script', 6) === 0) {
                $right = " " . $right;
            }
            return $add_spaces([ $left, $operator, $right ]);
        },
        "unary-prefix" => function($_this_0, $operator, $expr) use ($OPERATORS, $make, $needs_parens) {
            $val = $make($expr);
            if (!($expr[0] === "num" || ($expr[0] === "unary-prefix" && !isset($OPERATORS[$operator . $expr[1]])) || !$needs_parens($expr[0])))
                $val = "(" . $val . ")";
            return $operator . (ctype_alnum($operator[0]) ? " " : "") . $val;
        },
        "unary-postfix" => function($_this_0, $operator, $expr) use ($OPERATORS, $make, $needs_parens) {
            $val = $make($expr);
            if (!($expr[0] === "num" || ($expr[0] === "unary-postfix" && !isset($OPERATORS[$operator . $expr[1]])) || !$needs_parens($expr[0])))
                $val = "(" . $val . ")";
            return $val . $operator;
        },
        "sub" => function($_this_0, $expr, $subscript) use ($make, $needs_parens) {
            $hash = $make($expr);
            if ($needs_parens($expr[0]))
                $hash = "(" . $hash . ")";
            return $hash . "[" . $make($subscript) . "]";
        },
        "object" => function($_this_0, $props) use (
            $MAP,
            $options,
            $beautify,
            $newline,
            $is_identifier,
            $encode_string,
            $indent,
            $with_indent,
            $add_spaces,
            $parenthesize,
            $needs_parens,
            $make_num,
            $make_function,
            $ToString
        ) {
            $obj_needs_parens = $needs_parens($_this_0);
            if ($props === [])
                return $obj_needs_parens ? "({})" : "{}";
            $out = "{" . $newline . $with_indent(function() use (
                $MAP,
                $props,
                $options,
                $beautify,
                $newline,
                $is_identifier,
                $encode_string,
                $indent,
                $add_spaces,
                $parenthesize,
                $make_num,
                $make_function,
                $ToString,
                $_this_0
            ) {
                return join("," . $newline, $MAP($props, function($p) use (
                    $options,
                    $beautify,
                    $is_identifier,
                    $encode_string,
                    $indent,
                    $add_spaces,
                    $parenthesize,
                    $make_num,
                    $make_function,
                    $ToString,
                    $_this_0
                ) {
                    if (count($p) === 3) {
                        // getter/setter.  The name is in p[0], the arg.list in p[1][2], the
                        // body in p[1][3] and type ("get" / "set") in p[2].
                        return $indent($make_function($_this_0, $p[0], $p[1][2], $p[1][3], $p[2], true));
                    }
                    $key = $p[0]; $val = $parenthesize($p[1], "seq");
                    if ($options['quote_keys']) {
                        $key = $encode_string($_this_0, $key);
                    } elseif ((is_int($key) || is_float($key) || !$beautify && $ToString((float)$key) === $key)
                               && (float)$key >= 0) {
                        $key = $make_num($_this_0, +$key);
                    } elseif (!$is_identifier($key)) {
                        $key = $encode_string($_this_0, $key);
                    }
                    return $indent($add_spaces($beautify && $options['space_colon']
                                             ? [ $key, ":", $val ]
                                             : [ $key . ":", $val ]));
                }));
            }) . $newline . $indent("}");
            return $obj_needs_parens ? "(" . $out . ")" : $out;
        },
        "regexp" => function($_this_0, $rx, $mods) use ($options, $to_ascii) {
            if ($options['ascii_only']) $rx = $to_ascii($rx);
            return "/" . $rx . "/" . $mods;
        },
        "array" => function($_this_0, $elements) use (
            $MAP,
            $beautify,
            $add_spaces,
            $add_commas,
            $parenthesize
        ) {
            if ($elements === []) return "[]";
            return $add_spaces([ "[", $add_commas($MAP($elements, function($el, $i) use ($elements, $beautify, $parenthesize) {
                if (!$beautify && $el[0] === "atom" && $el[1] === "undefined") return $i === count($elements) - 1 ? "," : "";
                return $parenthesize($el, "seq");
            })), "]" ]);
        },
        "stat" => function($_this_0, $stmt) use ($make) {
            return $stmt !== null
                ? preg_replace('/;*\s*$/', ';', $make($stmt), 1)
                : ";";
        },
        "seq" => function($_this_0) use ($MAP, $make, $add_commas) {
            return $add_commas($MAP(array_slice(func_get_args(), 1), $make));
        },
        "label" => function($_this_0, $name, $block) use ($make, $make_name, $add_spaces) {
            return $add_spaces([ $make_name($name), ":", $make($block) ]);
        },
        "with" => function($_this_0, $expr, $block) use ($make, $add_spaces) {
            return $add_spaces([ "with", "(" . $make($expr) . ")", $make($block) ]);
        },
        "atom" => function($_this_0, $name) use ($make_name) {
            return $make_name($name);
        },
        "directive" => function($_this_0, $dir) use ($make_string) {
            return $make_string($dir) . ";";
        }
    ], function() use ($ast, $make) { return $make($ast); });

};

/* Minify */

function minifyJavascriptCode($text){
	global $parse,$ast_mangle,$ast_squeeze,$strip_lines,$gen_code;
//$text = preg_replace('!^[ \t]*/\*.*?\*/[ \t]*[\r\n]!s', '', $text);
//$text = preg_replace('![ \t]*//.*[ \t]*[\r\n]!', '', $text);
//$text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $text);
//$text = preg_replace('/\s+/', ' ', $text);
//$text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $text);
//$text = preg_replace('/\s\s+/', ' ', $text);

$ast = $parse($text); // parse code and get the initial AST
$ast = $ast_mangle($ast); // get a new AST with mangled names
$ast = $ast_squeeze($ast); // get an AST with compression optimizations
$text = $strip_lines($gen_code($ast)); // compressed code here

return $text;
}

/* Cache file */
$cachexists= true; /* Always rebuild */
    $cachedfile = ABSPATH.'/storage/cache/js-combined.cache';
	$cachedfilemin = str_replace('.cache','-min.cache',$cachedfile);
	//echo $cachedfile;
            if (file_exists($cachedfile)) {			
            $txt = file_get_contents($cachedfile);
			
			}	 else {
            echo "No cache file. Visit the site to generate one".
			$cachexists = false;
			}			

	
//Should stop here but maybe we have a cache 
//file to write?
// Write cache file 
if($cachexists) {
    // Minify
	$txt = minifyJavascriptCode($txt);
	// Create cache file
	$f = fopen($cachedfilemin, 'w');
	fwrite ($f, $txt);
	fclose($f);
    echo("done");	
}
// Print the css
	
?>