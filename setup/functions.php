<?php  
function SetupRemoteData($url) {
		$ch = curl_init();
		$timeout = 15;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
//check if value is null
function nullval($value){
	if(is_null($value) || $value=="" || empty($value)){
	return true;  }
	else { return false;
	}
}
//Alias null
function is_empty($v){
	return 	nullval($v);
}
//Not null
function not_empty($v){
	return 	!nullval($v);
}
function str_replace_first($search, $replace, $subject) {
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}
function _error() {
	global $error;
	$error++;
	//echo $error;
}
function SQLSplit($queries){
		$start = 0;
		$open = false;
		$open_char = '';
		$end = strlen($queries);
		$query_split = array();
		for($i=0;$i<$end;$i++) {
			$current = substr($queries,$i,1);
			if(($current == '"' || $current == '\'')) {
				$n = 2;
				while(substr($queries,$i - $n + 1, 1) == '\\' && $n < $i) {
					$n ++;
				}
				if($n%2==0) {
					if ($open) {
						if($current == $open_char) {
							$open = false;
							$open_char = '';
						}
					} else {
						$open = true;
						$open_char = $current;
					}
				}
			}
			if(($current == ';' && !$open)|| $i == $end - 1) {
				$query_split[] = substr($queries, $start, ($i - $start + 1));
				$start = $i + 1;
			}
		}

		return $query_split;
	}
function get_domain($url)
{
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return $regs['domain'];
  }
  if((strpos($url, 'localhost') !== false) || (strpos($url, '127.0. 0.1') !== false)) {return 'localhost';}
  return false;
}
function hasA_license() {
	global $site_url;
if( !defined( 'phpVibeKey')) {
	return false;
} else {
	$test = phpVibeKey;
	if(empty($test) || is_null($test) || (strpos($test, '-') === false) || (substr_count($test, '-') < 2)) {
	return false;
	}	
}

return validatekey(phpVibeKey, get_domain($site_url));
}
function validatekey($key, $domain){
	if($domain == "localhost") {return true;}
	
	$domain = str_replace('.', '',$domain);
	$domain = preg_replace('/[[:punct:]]/', '', $domain);
	$checdom = str_split(substr($domain, 1, 4));
	$ckey = explode('-', strtolower($key));
	$checkey =	str_split(end($ckey));
	sort($checdom);
	sort($checkey);

	if ($checdom===$checkey) { 
		return true;
	} else {
	return false;	
	}
}
function do_remove_file_now($filename) {
	if(is_readable($filename)) {
	chmod($filename, 0777);
	if (unlink($filename)){
	echo '<div class="msg-info">'.$filename.' removed.</div>';
	} else {
	echo '<div class="msg-warning">'.$filename.' was not removed. Check server permisions for "unlink" function.</div>';
	}
	}
}
?>