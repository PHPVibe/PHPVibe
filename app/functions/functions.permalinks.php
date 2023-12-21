<?php function com(){
/* Returns the current component used */	
global $route;
if(isset($route) && $route) {
return toDb($route->getTarget());
}
return false;
}
function token(){
/* Returns the token in the url */	
global $route;
if(isset($route) && $route) {
$idx = $route->getParameters();
if(isset($idx["section"])) {
$idx["section"] = current(explode("/", $idx["section"]));
$idx["section"] = trim(str_replace("/","",$idx["section"]));
return $idx["section"];
}
}
return false;
}
function token_id(){
/* Returns the id or hashed id in the url */	
	
global $route;
if(isset($route) && $route) {
$idx = $route->getParameters();
if(isset($idx["id"])) {
return $idx["id"];
} elseif(isset($idx["hid"])) {
return _dHash($idx["hid"]);	
}
}
return false;
}
// Sef rewrite tag function
function nice_tag($tag){
$tag = strip_punctuation($tag);	
$tag = preg_replace(array('~^-+|-+$~', '~-+~'), array('', '-'), $tag);
return strtolower($tag);
}
function strip_punctuation($string, $rep ="-") {
    $string = strtolower($string);
    $string = preg_replace('/[[:punct:]]/', $rep, $string);
    $string = str_replace(" ", $rep, $string);
	$string = preg_replace('/\s+/', $rep, $string);
	$string = str_replace($rep.$rep, $rep, $string);
	$string = rtrim(ltrim($string, $rep), $rep);
    return $string;
}
// Sef rewrite function	
function nice_url($iniurl) {
$string ='';
// translate utf-8
$url = url_translate($iniurl);
// remove all chars
$url = strip_punctuation(strtolower($url));
$url = preg_replace("/\W+/", " ", $url);
$url = preg_replace("/[^a-z0-9]+/","-",strtolower($url));
// Test it
$test = str_replace('-','',$url);
if(is_empty($test)) {
// Fallback 
$url = brute_url_translate($iniurl);
}
return urlencode(strtolower($url));
}
// New
function url_translate($text)
{

    if(is_empty($text)) {return '';}
	//	Generate a SEF URL from a given text
   $chset= "UTF-8";
   $txt= "UTF-8";
	//	Do you know your ABCs?
	$characterHash = array (
		'a'	=>	array ('a', 'A', 'à', 'À', 'á', 'Á', 'â', 'Â', 'ã', 'Ã', 'ä', 'Ä', 'å', 'Å', 'ª', 'ą', 'Ą', 'а', 'А', 'ạ', 'Ạ', 'ả', 'Ả', 'Ầ', 'ầ', 'Ấ', 'ấ', 'Ậ', 'ậ', 'Ẩ', 'ẩ', 'Ẫ', 'ẫ', 'Ă', 'ă', 'Ắ', 'ắ', 'Ẵ', 'ẵ', 'Ặ', 'ặ', 'Ằ', 'ằ', 'Ẳ', 'ẳ', 'а', 'А'),
		'ae'	=>	array ('æ', 'Æ'),
		'b'	=>	array ('b', 'B', 'б', 'Б'),
		'c'	=>	array ('c', 'C', 'ç', 'Ç', 'ć', 'Ć', 'č', 'Č', 'ц', 'Ц'),
		'd'	=>	array ('d', 'D', 'Ð', 'đ', 'Đ', 'ď', 'Ď', 'д', 'Д'),
		'e'	=>	array ('e', 'E', 'è', 'È', 'é', 'É', 'ê', 'Ê', 'ë', 'Ë', 'ę', 'Ę', 'е', 'Е', 'ё', 'Ё', 'э', 'Э', 'Ẹ', 'ẹ', 'Ẻ', 'ẻ', 'Ẽ', 'ẽ', 'Ề', 'ề', 'Ế', 'ế', 'Ệ', 'ệ', 'Ể', 'ể', 'Ễ', 'ễ', 'ε', 'Ε', 'ě', 'Ě', 'е', 'Е'),
		'f'	=>	array ('f', 'F', 'ф', 'Ф'),
		'g'	=>	array ('g', 'G', 'ğ', 'Ğ', 'г', 'Г'),
		'h'	=>	array ('h', 'H', 'х', 'Х'),
		'i'	=>	array ('i', 'I', 'ì', 'Ì', 'í', 'Í', 'î', 'Î', 'ï', 'Ï', 'ı', 'İ', 'Ị', 'ị', 'Ỉ', 'ỉ', 'Ĩ', 'ĩ', 'Ι', 'ι', 'и', 'И'),
		'j'	=>	array ('j', 'J', 'й', 'Й'),
		'k'	=>	array ('k', 'K', 'к', 'К', 'κ', 'Κ', 'к', 'К'),
		'l'	=>	array ('l', 'L', 'ł', 'Ł', 'л', 'Л'),
		'm'	=>	array ('m', 'M', 'м', 'М', 'Μ', 'м', 'М'),
		'n'	=>	array ('n', 'N', 'ñ', 'Ñ', 'ń', 'Ń', 'ň', 'Ň', 'н', 'Н'),
		'o'	=>	array ('o', 'O', 'ò', 'Ò', 'ó', 'Ó', 'ô', 'Ô', 'õ', 'Õ', 'ö', 'Ö', 'ø', 'Ø', 'º', 'о', 'О', 'Ọ', 'ọ', 'Ỏ', 'ỏ', 'Ộ', 'ộ', 'Ố', 'ố', 'Ỗ', 'ỗ', 'Ồ', 'ồ', 'Ổ', 'ổ', 'Ơ', 'ơ', 'Ờ', 'ờ', 'Ớ', 'ớ', 'Ợ', 'ợ', 'Ở', 'ở', 'Ỡ', 'ỡ', 'ο', 'Ο', 'о', 'О'),
		'p'	=>	array ('p', 'P', 'п', 'П'),
		'q'	=>	array ('q', 'Q'),
		'r'	=>	array ('r', 'R', 'ř', 'Ř', 'р', 'Р'),
		's'	=>	array ('s', 'S', 'ş', 'Ş', 'ś', 'Ś', 'š', 'Š', 'с', 'С'),
		'ss'	=>	array ('ß'),
		't'	=>	array ('t', 'T', 'т', 'Т', 'τ', 'Τ', 'ţ', 'Ţ', 'ť', 'Ť', 'т', 'Т'),
		'u'	=>	array ('u', 'U', 'ù', 'Ù', 'ú', 'Ú', 'û', 'Û', 'ü', 'Ü', 'Ụ', 'ụ', 'Ủ', 'ủ', 'Ũ', 'ũ', 'Ư', 'ư', 'Ừ', 'ừ', 'Ứ', 'ứ', 'Ự', 'ự', 'Ử', 'ử', 'Ữ', 'ữ', 'ů', 'Ů', 'у', 'У'),
		'v'	=>	array ('v', 'V', 'в', 'В'),
		'w'	=>	array ('w', 'W'),
		'x'	=>	array ('x', 'X', '×'),
		'y'	=>	array ('y', 'Y', 'ý', 'Ý', 'ÿ', 'Ỳ', 'ỳ', 'Ỵ', 'ỵ', 'Ỷ', 'ỷ', 'Ỹ', 'ỹ', 'ы', 'Ы'),
		'z'	=>	array ('z', 'Z', 'ż', 'Ż', 'ź', 'Ź', 'ž', 'Ž', 'Ζ', 'з', 'З'),
		'jo' => array ('ё', 'Ё'),
		'zh' => array ('ж', 'Ж'),
		'ch' => array ('ч', 'Ч'),
		'sh' => array ('ш', 'Ш'),
		'sch' => array ('щ', 'Щ'),
		'eh' => array ('э', 'Э'),
		'yu' => array ('ю', 'Ю'),
		'ya' => array ('я', 'Я'),
		'' => array ('ъ', 'Ъ', 'ь', 'Ь', '?', '«', '»', ':', '&', '+', '@', '%', '^', '№', '#'),
		'-'	=>	array ('-', ' ', '.', ','),
		'_'	=>	array ('_'),
		'!'	=>	array ('!'),
		'~'	=>	array ('~'),
		'*'	=>	array ('*'),
		"\x12"	=>	array ("'", '"'),
		'('	=>	array ('(', '{', '['),
		')'	=>	array (')', '}', ']'),
		'$'	=>	array ('$'),
		'0'	=>	array ('0'),
		'1'	=>	array ('1', '¹'),
		'2'	=>	array ('2', '²'),
		'3'	=>	array ('3', '³'),
		'4'	=>	array ('4'),
		'5'	=>	array ('5'),
		'6'	=>	array ('6'),
		'7'	=>	array ('7'),
		'8'	=>	array ('8'),
		'9'	=>	array ('9'),
	);

	//	Get the database encoding
	 if (preg_match('~.~su', $text))
		$encoding = 'UTF-8';
	//	or sadly... we may have to assume Latin-1
	else
		$encoding = 'ISO-8859-1';

	//	If the database encoding isn't UTF-8 and multibyte string functions are available, try converting the text to UTF-8
	if ($encoding != 'UTF-8' && function_exists('mb_convert_encoding'))
		$text = mb_convert_encoding($text, 'UTF-8', $encoding);
	//	Or maybe we can convert with iconv
	else if ($encoding != 'UTF-8' && function_exists('iconv'))
		$text = iconv($encoding, 'UTF-8', $text);
	//	Fix Turkish
	else if ($encoding == 'ISO-8859-9')
	{
		$text = str_replace(array("\xD0", "\xDD", "\xDE", "\xF0", "\xFD", "\xFE"), array('g', 'i', 's', 'g', 'i', 's'), $text);
		$text = utf8_encode($text);
	}
	//	Latin-1 can be converted easily
	else if ($encoding == 'ISO-8859-1')
		$text = utf8_encode($text);

	//	Change the entities back to normal characters
	$text = str_replace(array('&amp;', '&quot;'), array('&', '"'), $text);
	$seftext = '';

	//	Split up $text into UTF-8 letters
	preg_match_all("~.~su", $text, $characters);
	foreach ($characters[0] as $aLetter)
	{
		foreach ($characterHash as $replace => $search)
		{
			//	Found a character? Replace it!
			if (in_array($aLetter, $search))
			{
				$seftext .= $replace;
				break;
			}
		}
	}
	//	Remove unwanted '-'s
	$seftext = preg_replace(array('~^-+|-+$~', '~-+~'), array('', '-'), $seftext);
	return $seftext;
}



function brute_url_translate($string) {
    if(is_empty($string)) {return '';}
$specialchars = array(
// Latin
'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
'ß' => 'ss',
'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
'ÿ' => 'y',
// Latin symbols
'©' => '(c)',
/* German */
'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
'ẞ' => 'SS',
// Greek
'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
'Ϋ' => 'Y',
'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
// Turkish
'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
// Russian
'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
'Я' => 'Ya',
'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
'я' => 'ya',
// Ukrainian
'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
// Czech
'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
'Ž' => 'Z',
'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
'ž' => 'z',
// Polish
'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
'Ż' => 'Z',
'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
'ż' => 'z',
// Latvian
'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
'š' => 's', 'ū' => 'u', 'ž' => 'z',
/* Lithuanian */
'ą' => 'a', 'č' => 'c', 'ę' => 'e', 'ė' => 'e', 'į' => 'i', 'š' => 's', 'ų' => 'u', 'ū' => 'u', 'ž' => 'z',
'Ą' => 'A', 'Č' => 'C', 'Ę' => 'E', 'Ė' => 'E', 'Į' => 'I', 'Š' => 'S', 'Ų' => 'U', 'Ū' => 'U', 'Ž' => 'Z',
/* Vietnamese */
'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ằ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ầ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A',
'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E',
'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O',
'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U',
'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
'Đ' => 'D', 'đ' => 'd',
/* Arabic */
'أ' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'g', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd',
'ذ' => 'th', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd', 'ط' => 't',
'ظ' => 'th', 'ع' => 'aa', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'k', 'ك' => 'k', 'ل' => 'l', 'م' => 'm',
'ن' => 'n', 'ه' => 'h', 'و' => 'o', 'ي' => 'y', 'ة'=> 'o',
/* Serbian */
'ђ' => 'dj', 'ј' => 'j', 'љ' => 'lj', 'њ' => 'nj', 'ћ' => 'c', 'џ' => 'dz', 'đ' => 'dj',
'Ђ' => 'Dj', 'Ј' => 'j', 'Љ' => 'Lj', 'Њ' => 'Nj', 'Ћ' => 'C', 'Џ' => 'Dz', 'Đ' => 'Dj',
/* Azerbaijani */
'ç' => 'c', 'ə' => 'e', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
'Ç' => 'C', 'Ə' => 'E', 'Ğ' => 'G', 'İ' => 'I', 'Ö' => 'O', 'Ş' => 'S', 'Ü' => 'U'			
);

$url = strtr($string,$specialchars);

$url = strip_punctuation(strtolower($url));
$url = preg_replace("/\W+/", " ", $url);
$url = preg_replace("/[^a-z0-9]+/","-",strtolower($url));
//	Remove unwanted '-'s
$url = preg_replace(array('~^-+|-+$~', '~-+~'), array('', '-'), $url);
return $url;
}
// Rewrite urls for coms
function video_url($id, $title, $list=null){
$post = '';
if(!is_null($list)){ $post= '?list='.$list; }
$or = get_option('video-seo-url','/video/:id/:name');
$or = str_replace(':name',nice_url($title),$or);
$or = str_replace(':id',$id,$or);
$or = str_replace(':hid',_mHash($id),$or);
$or = str_replace(':section','',$or);
$or .= (substr($or, -1) == '/' ? '' : '/');
$url = sef_url().$or.$post;
return $url;
}
function image_url($id, $title, $list=null){
$or = get_option('image-seo-url','/image/:id/:name');
$or = str_replace(':name',nice_url($title),$or);
$or = str_replace(':id',$id,$or);
$or = str_replace(':hid',_mHash($id),$or);
$or = str_replace(':section','',$or);
$or .= (substr($or, -1) == '/' ? '' : '/');
$url = sef_url().$or;
return $url;
}
function profile_url($id, $title){
$or = get_option('profile-seo-url','/profile/:name/:id/:section');
$or = str_replace(':name',nice_url($title),$or);
$or = str_replace(':id',$id,$or);
$or = str_replace(':hid',_mHash($id),$or);
$or = str_replace(':section','',$or);
$or .= (substr($or, -1) == '/' ? '' : '/');
$url = sef_url().$or;
return $url;
}
function playlist_url($id, $title){
return site_url().playlist.url_split.nice_url($title).url_split.$id.'/';
}
function channel_url($id, $title, $type =1){
	if($type == 1) {
$or = get_option('channel-seo-url','/channel/:name/:id/:section');
	} elseif($type == 2) {
$or = get_option('music-channel-seo-url','/musicfilter/:name/:id/:section');		
	
	} else {
$or = get_option('image-channel-seo-url','/imagefilter/:name/:id/:section');		
	}
$or = str_replace(':name',nice_url($title),$or);
$or = str_replace(':id',$id,$or);
$or = str_replace(':hid',_mHash($id),$or);
$or = str_replace(':section','',$or);
$or .= (substr($or, -1) == '/' ? '' : '/');
$url = sef_url().$or;
return $url;
}
function bc_url($id, $title){
return site_url().blogcat.url_split.nice_url($title).url_split.$id.'/';
}
function note_url($id, $title=null){
if(!is_null($title)) {
return site_url().note.url_split.$id.url_split.nice_url($title).'/';
}
return site_url().note.url_split.$id.'/';
}
function list_url($part){
return site_url().videos.url_split.$part.'/';
}
function hub_url($part){
return site_url().premiumhub.url_split.$part.'/';
}
function images_url($part){
return site_url().'images'.url_split.$part.'/';
}
function music_url($part){
return site_url().'music'.url_split.$part.'/';
}
function page_url($id, $title=null){
$or = get_option('page-seo-url','/read/:name/:id');
$or = str_replace(':name',nice_url($title),$or);
$or = str_replace(':id',$id,$or);
$or = str_replace(':hid',_mHash($id),$or);
$or = str_replace(':section','',$or);
$or .= (substr($or, -1) == '/' ? '' : '/');
$url = sef_url().$or;
return $url;
}
function article_url($id, $title=null){
$or = get_option('article-seo-url','/article/:name/:id');
$or = str_replace(':name',nice_url($title),$or);
$or = str_replace(':id',$id,$or);
$or = str_replace(':hid',_mHash($id),$or);
$or = str_replace(':section','',$or);
$or .= (substr($or, -1) == '/' ? '' : '/');
$url = sef_url().$or;
return $url;
}
//Video & Music tags
function pretty_tags($tags, $class='', $pre='', $post = ''){
$list ='';
$keywords_array = explode(',', $tags);
if (count($keywords_array) > 0){
foreach ($keywords_array as $keyword){
if (not_empty(trim($keyword))){
$k_url = site_url().thetags.'/'.nice_tag(trim($keyword)).'/';
$list .=  '<a class="'.$class.'" href="'.$k_url.'">'.$pre.$keyword.'</a>'.$post;
}
}
}
return $list;
}
//Image tags
function pretty_imgtags($tags, $class='', $pre='', $post = ''){
$list ='';
$keywords_array = explode(',', $tags);
if (count($keywords_array) > 0){
foreach ($keywords_array as $keyword){
if (not_empty(trim($keyword))){
$k_url = images_url('tag').'?tag='.nice_tag(trim($keyword));
$list .=  $pre.'<a class="'.$class.'" href="'.$k_url.'">'.$keyword.'</a>'.$post;
}
}
}
return $list;
}
?>