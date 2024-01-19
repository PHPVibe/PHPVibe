<?php require_once(CNC . '/class.players.php'); /* Players support */
/* Spicy */
function removeComTags($input)
{
    // Tags to remove
    $commonWords = array('a', 'able', 'about', 'above', 'abroad', 'according', 'accordingly', 'across', 'actually', 'adj', 'after', 'afterwards', 'again', 'against', 'ago', 'ahead', 'ain\'t', 'all', 'allow', 'allows', 'almost', 'alone', 'along', 'alongside', 'already', 'also', 'although', 'always', 'am', 'amid', 'amidst', 'among', 'amongst', 'an', 'and', 'another', 'any', 'anybody', 'anyhow', 'anyone', 'anything', 'anyway', 'anyways', 'anywhere', 'apart', 'appear', 'appreciate', 'appropriate', 'are', 'aren\'t', 'around', 'as', 'a\'s', 'aside', 'ask', 'asking', 'associated', 'at', 'available', 'away', 'awfully', 'b', 'back', 'backward', 'backwards', 'be', 'became', 'because', 'become', 'becomes', 'becoming', 'been', 'before', 'beforehand', 'begin', 'behind', 'being', 'believe', 'below', 'beside', 'besides', 'best', 'better', 'between', 'beyond', 'both', 'brief', 'but', 'by', 'c', 'came', 'can', 'cannot', 'cant', 'can\'t', 'caption', 'cause', 'causes', 'certain', 'certainly', 'changes', 'clearly', 'c\'mon', 'co', 'co.', 'com', 'come', 'comes', 'concerning', 'consequently', 'consider', 'considering', 'contain', 'containing', 'contains', 'corresponding', 'could', 'couldn\'t', 'course', 'c\'s', 'currently', 'd', 'dare', 'daren\'t', 'definitely', 'described', 'despite', 'did', 'didn\'t', 'different', 'directly', 'do', 'does', 'doesn\'t', 'doing', 'done', 'don\'t', 'down', 'downwards', 'during', 'e', 'each', 'edu', 'eg', 'eight', 'eighty', 'either', 'else', 'elsewhere', 'end', 'ending', 'enough', 'entirely', 'especially', 'et', 'etc', 'even', 'ever', 'evermore', 'every', 'everybody', 'everyone', 'everything', 'everywhere', 'ex', 'exactly', 'example', 'except', 'f', 'fairly', 'far', 'farther', 'few', 'fewer', 'fifth', 'first', 'five', 'followed', 'following', 'follows', 'for', 'forever', 'former', 'formerly', 'forth', 'forward', 'found', 'four', 'from', 'further', 'furthermore', 'g', 'get', 'gets', 'getting', 'given', 'gives', 'go', 'goes', 'going', 'gone', 'got', 'gotten', 'greetings', 'h', 'had', 'hadn\'t', 'half', 'happens', 'hardly', 'has', 'hasn\'t', 'have', 'haven\'t', 'having', 'he', 'he\'d', 'he\'ll', 'hello', 'help', 'hence', 'her', 'here', 'hereafter', 'hereby', 'herein', 'here\'s', 'hereupon', 'hers', 'herself', 'he\'s', 'hi', 'him', 'himself', 'his', 'hither', 'hopefully', 'how', 'howbeit', 'however', 'hundred', 'i', 'i\'d', 'ie', 'if', 'ignored', 'i\'ll', 'i\'m', 'immediate', 'in', 'inasmuch', 'inc', 'inc.', 'indeed', 'indicate', 'indicated', 'indicates', 'inner', 'inside', 'insofar', 'instead', 'into', 'inward', 'is', 'isn\'t', 'it', 'it\'d', 'it\'ll', 'its', 'it\'s', 'itself', 'i\'ve', 'j', 'just', 'k', 'keep', 'keeps', 'kept', 'know', 'known', 'knows', 'l', 'last', 'lately', 'later', 'latter', 'latterly', 'least', 'less', 'lest', 'let', 'let\'s', 'like', 'liked', 'likely', 'likewise', 'little', 'look', 'looking', 'looks', 'low', 'lower', 'ltd', 'm', 'made', 'mainly', 'make', 'makes', 'many', 'may', 'maybe', 'mayn\'t', 'me', 'mean', 'meantime', 'meanwhile', 'merely', 'might', 'mightn\'t', 'mine', 'minus', 'miss', 'more', 'moreover', 'most', 'mostly', 'mr', 'mrs', 'much', 'must', 'mustn\'t', 'my', 'myself', 'n', 'name', 'namely', 'nd', 'near', 'nearly', 'necessary', 'need', 'needn\'t', 'needs', 'neither', 'never', 'neverf', 'neverless', 'nevertheless', 'new', 'next', 'nine', 'ninety', 'no', 'nobody', 'non', 'none', 'nonetheless', 'noone', 'no-one', 'nor', 'normally', 'not', 'nothing', 'notwithstanding', 'novel', 'now', 'nowhere', 'o', 'obviously', 'of', 'off', 'often', 'oh', 'ok', 'okay', 'old', 'on', 'once', 'one', 'ones', 'one\'s', 'only', 'onto', 'opposite', 'or', 'other', 'others', 'otherwise', 'ought', 'oughtn\'t', 'our', 'ours', 'ourselves', 'out', 'outside', 'over', 'overall', 'own', 'p', 'particular', 'particularly', 'past', 'per', 'perhaps', 'placed', 'please', 'plus', 'possible', 'presumably', 'probably', 'provided', 'provides', 'q', 'que', 'quite', 'qv', 'r', 'rather', 'rd', 're', 'really', 'reasonably', 'recent', 'recently', 'regarding', 'regardless', 'regards', 'relatively', 'respectively', 'right', 'round', 's', 'said', 'same', 'saw', 'say', 'saying', 'says', 'second', 'secondly', 'see', 'seeing', 'seem', 'seemed', 'seeming', 'seems', 'seen', 'self', 'selves', 'sensible', 'sent', 'serious', 'seriously', 'seven', 'several', 'shall', 'shan\'t', 'she', 'she\'d', 'she\'ll', 'she\'s', 'should', 'shouldn\'t', 'since', 'six', 'so', 'some', 'somebody', 'someday', 'somehow', 'someone', 'something', 'sometime', 'sometimes', 'somewhat', 'somewhere', 'soon', 'sorry', 'specified', 'specify', 'specifying', 'still', 'sub', 'such', 'sup', 'sure', 't', 'take', 'taken', 'taking', 'tell', 'tends', 'th', 'than', 'thank', 'thanks', 'thanx', 'that', 'that\'ll', 'thats', 'that\'s', 'that\'ve', 'the', 'their', 'theirs', 'them', 'themselves', 'then', 'thence', 'there', 'thereafter', 'thereby', 'there\'d', 'therefore', 'therein', 'there\'ll', 'there\'re', 'theres', 'there\'s', 'thereupon', 'there\'ve', 'these', 'they', 'they\'d', 'they\'ll', 'they\'re', 'they\'ve', 'thing', 'things', 'think', 'third', 'thirty', 'this', 'thorough', 'thoroughly', 'those', 'though', 'three', 'through', 'throughout', 'thru', 'thus', 'till', 'to', 'together', 'too', 'took', 'toward', 'towards', 'tried', 'tries', 'truly', 'try', 'trying', 't\'s', 'twice', 'two', 'u', 'un', 'under', 'underneath', 'undoing', 'unfortunately', 'unless', 'unlike', 'unlikely', 'until', 'unto', 'up', 'upon', 'upwards', 'us', 'use', 'used', 'useful', 'uses', 'using', 'usually', 'v', 'value', 'various', 'versus', 'very', 'via', 'viz', 'vs', 'w', 'want', 'wants', 'was', 'wasn\'t', 'way', 'we', 'we\'d', 'welcome', 'well', 'we\'ll', 'went', 'were', 'we\'re', 'weren\'t', 'we\'ve', 'what', 'whatever', 'what\'ll', 'what\'s', 'what\'ve', 'when', 'whence', 'whenever', 'where', 'whereafter', 'whereas', 'whereby', 'wherein', 'where\'s', 'whereupon', 'wherever', 'whether', 'which', 'whichever', 'while', 'whilst', 'whither', 'who', 'who\'d', 'whoever', 'whole', 'who\'ll', 'whom', 'whomever', 'who\'s', 'whose', 'why', 'will', 'willing', 'wish', 'with', 'within', 'without', 'wonder', 'won\'t', 'would', 'wouldn\'t', 'x', 'y', 'yes', 'yet', 'you', 'you\'d', 'you\'ll', 'your', 'you\'re', 'yours', 'yourself', 'yourselves', 'you\'ve', 'z', 'zero');
    return preg_replace('/\b(' . implode('|', $commonWords) . ')\b/', '', $input);
}

/* End Spicy*/
function PHPVibeSources()
{
    $hostings = array('youtube', 'vimeo', 'dailymotion', 'twitch', 'soundcloud', 'facebook', 'localfile', 'localimage', 'up');
    return apply_filter('vibe-video-sources', $hostings);
}

//constants
define('UNKNOWN_PROVIDER', _lang('Unknown provider or incorrect URL. Please try again.'));
define('INVALID_URL', _lang('This URL is invalid or the video is removed by the provider.'));
$qualities = array();

class Vibe_Providers
{
    protected $height = 300;
    protected $width = 600;
    protected $link = "";

    function __construct($width = null, $height = null)
    {
        $this->setDimensions($width, $height);
    }

    private function setDimensions($width = null, $height = null)
    {
        if ((!is_null($width)) && ($width != "")) {
            $this->width = $width;
        }
        if ((!is_null($height)) && ($height != "")) {
            $this->height = $height;
        }
    }

    //check if video link is valid

    public function theLink()
    {
        if (isset($this->link)) {
            return $this->link;
        }
    }

    // getEmbedCode

    public function isValid($videoLink)
    {
        $this->link = $videoLink;
        $videoProvider = $this->decideVideoProvider();
        if (!empty($videoProvider) && $videoProvider != "") {
            return true;
        } else {
            return false;
        }
    }

    //Providers

    private function decideVideoProvider()
    {
        if ($this->link == "up") {
            return "up";
        }
        $videoProvider = "";
        //providers list
        //hook for more sources
        $hostings = $this->Hostings();
        //check	provider
        $parse = parse_url($this->link);

        for ($i = 0; $i < count($hostings); $i++) {
            if (isset($hostings[$i])) {
                if (isset($parse['host'])) {
                    if (is_numeric(strpos($parse['host'], $hostings[$i]))) {
                        $videoProvider = $hostings[$i];
                    }
                }
            }
        }

        return $videoProvider;
    }

    // decide video provider

    public function Hostings()
    {
        return PHPVibeSources();
    }

    // generate video ıd from link

    public function getEmbedCode($videoLink, $width = null, $height = null)
    {
	$provider = $this->decideVideoProvider();
        // Plugins need this
        $VibeProvider = $provider;
        $VibeLink =$this->link; 
	// End plugins    
        $this->setDimensions($width, $height);
        if ($videoLink != "") {
            if (!is_numeric(strpos($videoLink, "http://")) && !is_numeric(strpos($videoLink, "https://"))) {
                $videoLink = "https://" . $videoLink;
            }
            $this->link = $videoLink;
            $embedCode = "";
            $videoProvider = $this->decideVideoProvider();
            if ($videoProvider == "") {
                $embedCode = UNKNOWN_PROVIDER;
            } else {
                $embedCode = $this->generateEmbedCode($videoProvider);
            }
        } else {
            $embedCode = INVALID_URL;
        }
        return $embedCode;
    }

    private function generateEmbedCode($videoProvider)
    {
        global $video, $qualities;
        $embedCode = "";
        switch ($videoProvider) {
            case 'up':
                $token = $video->token();
                $folder = ABSPATH . '/storage/' . get_option('mediafolder', 'media') . '/';
                /* Get list of video files attached */
                $pattern = "{*" . $token . "*}";
                $vl = glob($folder . $pattern, GLOB_BRACE);
                $qualities = array();
                if (!isIOS() && (get_option('hide-mp4', 0) > 0)) {
                    /* Get hidden by php urls */
                    $link = site_url() . 'stream.php?sk=' . $token . '&q=';
                    foreach ($vl as $vids) {
                        /* Aviod 0 size */
                        if (filesize($vids) > 300000) {
                            $file = str_replace($folder, '', $vids);
                            if (strpos($file, $token . '-') !== false) {
                                $size = str_replace(array($token . '-', '.mp4'), "", $file);
                                $qualities[trim($size)] = $link . trim($size);
                            } else {
                                $qualities["default"] = $link . 'default';
                            }
                        }
                    } /* End foreach */
                } else {
                    /* Get clean mp4 urls */
                    $link = site_url() . 'storage/' . get_option('mediafolder') . '/';
                    foreach ($vl as $vids) {
                        $file = str_replace($folder, '', $vids);
                        if (strpos($file, $token . '-') !== false) {
                            $size = str_replace(array($token . '-', '.mp4'), "", $file);
                            $qualities[trim($size)] = $link . $file;
                        } else {
                            $qualities["default"] = $link . $file;
                        }
                    } /* End foreach */
                }
                if (empty($qualities)) {
                    return false;
                }
                $max = max(array_keys($qualities));
                $min = min(array_keys($qualities));
                $qualities["hd"] = $qualities[$max];
                $qualities["sd"] = $qualities[$min];
                $ext = 'mp4';
                //Cut the doubles
                if (isset($qualities["default"])) {
                    if ($qualities["sd"] == $qualities["default"]) {
                        unset($qualities["default"]);
                    }
                }
                if ($qualities["sd"] == $qualities["hd"]) {
                    unset($qualities["hd"]);
                }
                if (count($qualities) < 2) {
                    $real_link = $qualities["sd"];
                    $extra = '';
                } else {
                    /* We have multiple qualities*/
                    $real_link = (isset($qualities["hd"])) ? $qualities["hd"] : $qualities["sd"];
                    $extra = $qualities;
                }
                krsort($qualities);
                /* Sent to Player */
                $choice = get_option('choosen-player', 1);
                if ($choice == 1) {
                    $embedCode = _jwplayer($real_link, $video->thumb(), thumb_fix(get_option('player-logo')), $ext, $extra);
                } elseif ($choice == 2) {
                    $embedCode = flowplayer($real_link, $video->thumb(), thumb_fix(get_option('player-logo')), $ext, $extra);
                } elseif ($choice == 6) {
                    $embedCode = vjsplayer($real_link, $video->thumb(), thumb_fix(get_option('player-logo')), $ext, $extra);
                } else {
                    $embedCode = _jpcustom($real_link, $video->thumb(), $extra);
                }
                break;
                break;
            case 'localimage':
                $path = $this->getVideoId("localimage/") . '@@' . get_option('mediafolder');
                $real_link = site_url() . 'stream.php?type=1&file=' . base64_encode(base64_encode($path));
                $embedCode .= '<a rel="lightbox" class="media-href" title="' . stripslashes($video->rawtitle()) . '" href="' . $real_link . '"><img class="media-img" src="' . $real_link . '" /></a>';
                break;
            case 'localfile':
                $path = $this->getVideoId("localfile/") . '@@storage/' . get_option('mediafolder');
                if (!isIOS() && (get_option('hide-mp4', 0) > 0)) {
                    $real_link = site_url() . 'stream.php?file=' . base64_encode(base64_encode($path));
                } else {
                    $real_link = thumb_fix('storage/' . get_option('mediafolder') . '/' . $this->getVideoId("localfile/"));
                }
                //$ext = explode(".", $this->link);
                //$ext = $ext[1];
                $pieces_array = explode('.', $this->link);
                $ext = end($pieces_array);
                $choice = get_option('choosen-player', 1);
                $mobile_supported = array("mp4", "mp3", "webm", "ogv", "m3u8", "ts", "tif");
                if (!in_array($ext, $mobile_supported)) {
                    /*force jwplayer always on non-mobi formats, as other players are just html5 */
                    $choice = 1;
                }
                if ($ext == "mp3") {
                    $embedCode = vjswaveplayer($real_link);

                } else {
                    if ($choice == 1) {
                        $embedCode = _jwplayer($real_link, $video->thumb(), thumb_fix(get_option('player-logo')), $ext);
                    } elseif ($choice == 2) {
                        $embedCode = flowplayer($real_link, $video->thumb(), thumb_fix(get_option('player-logo')), $ext);
                    } elseif ($choice == 6) {
                        $embedCode = vjsplayer($real_link, $video->thumb(), thumb_fix(get_option('player-logo')), $ext);
                    } else {
                        $embedCode = _jpcustom($real_link, $video->thumb());
                    }
                }
                break;
            case 'vine':
                $videoId = $this->getVideoId("/v/");
                if ($videoId != null) {
                    $embedCode .= '<iframe class="vine-embed" src="https://vine.co/v/' . $videoId . '/embed/simple?audio=1" width="' . $this->width . '" height="' . $this->height . '" frameborder="0"></iframe><script async src="//platform.vine.co/static/scripts/embed.js" charset="utf-8"></script>';
                    $embedCode .= _ad('1');
                } else {
                    $embedCode = INVALID_URL;
                }
                break;
            case 'facebook':
                $videoId = $this->getVideoId("v=", "&");
                if (empty($videoId)) {
                    if (strpos($this->link, '/?') !== false) {
                        list($real, $junk) = @explode('/?', $this->link);
                    } else {
                        $real = $this->link;
                    }
                    if (isset($real)) {
                        $videoId = $this->getLastNr(rtrim($real, '/'));
                    }
                }
                if ($videoId != null) {
                    $embedCode .= '<div class="fb-video" data-href="https://www.facebook.com/video.php?v=' . $videoId . '" " data-width="1280" data-allowfullscreen="true"></div>';
                    $embedCode .= _ad('1');
                } else {
                    $embedCode = INVALID_URL;
                }
                break;
            case 'youtube':
                $videoId = $this->getVideoId("v=", "&");
                if ($videoId != null) {
                    $choice = get_option('youtube-player');
                    if ($choice < 1) {
                        $embedCode .= "<iframe id=\"ytplayer\" width=\"" . $this->width . "\" height=\"" . $this->height . "\" src=\"https://www.youtube.com/embed/" . $videoId . "?enablejsapi=1&amp;version=3&amp;html5=1&amp;iv_load_policy=3&amp;modestbranding=1&amp;nologo=1&amp;vq=large&amp;autoplay=1&amp;ps=docs&amp;rel=0&amp;showinfo=0\" frameborder=\"0\" allowfullscreen=\"true\"></iframe>";
                        $embedCode .= '<script>
							 var tag = document.createElement(\'script\');
							 tag.src = "https://www.youtube.com/iframe_api";
					         var firstScriptTag = document.getElementsByTagName(\'script\')[0];
                             firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
							 var player;
							 function onYTPlayerReady(event) {
							event.target.playVideo();
							}
							function onYTPlayerStateChange(event) {
							if(event.data === 0) {					
							startNextVideo();	
							}
							}
							 function onYouTubeIframeAPIReady() {
								player = new YT.Player(\'ytplayer\', {
								events: {
									\'onReady\': onYTPlayerReady,
									\'onStateChange\': onYTPlayerStateChange
										}
									});
							}
									
                  </script>';

                        $embedCode .= _ad('1');
                    } elseif ($choice < 3) {
                        $real_link = 'https://www.youtube.com/watch?v=' . $videoId;
                        $img = 'https://img.youtube.com/vi/' . $videoId . '/mqdefault.jpg';
                        $embedCode = _jwplayer($real_link, $img, thumb_fix(get_option('player-logo')));
                    } else {
                        $real_link = 'https://www.youtube.com/watch?v=' . $videoId;
                        $img = 'https://img.youtube.com/vi/' . $videoId . '/mqdefault.jpg';
                        $embedCode = vjsplayer($real_link, $img);
                    }

                } else {
                    $embedCode = INVALID_URL;
                }
                break;
            case 'vimeo':
                $videoIdForChannel = $this->getVideoId('#');
                if (strlen($videoIdForChannel) > 0) {
                    $videoId = $videoIdForChannel;
                } else {
                    $videoId = $this->getVideoId(".com/");
                }
                //$videoId = $videoForChannel;
                if ($videoId != null) {
                    $embedCode .= '<iframe id="vimvideo" src="https://player.vimeo.com/video/' . $videoId . '?title=0&amp;player_id=vimvideo&amp;byline=0&amp;portrait=0&amp;color=cc181e&amp;autoplay=1" width="' . $this->width . '" height="' . $this->height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                    $embedCode .= '<script src="https://f.vimeocdn.com/js/froogaloop2.min.js"></script>';
                    $embedCode .= '<script>
					 var nextPlay;
						$(document).ready(function() {
						if($("li#playingNow").html()) {	
						nextPlay = $("li#playingNow").next().find("a.clip-link").attr("href");
						}					
						});
					 var iframe = $("#vimvideo")[0],
                     player = $f(iframe);
				     player.addEvent(\'ready\', function() {		
		             player.addEvent(\'finish\', onFinish);
	                 });
                    function onFinish(id) {
					startNextVideo();
                    }
					 </script>';
                    $embedCode .= _ad('1');
                } else {
                    $embedCode = INVALID_URL;
                }
                break;
            case 'soundcloud':
                if ($this->link) {
                    $embedCode .= '<iframe width="100%" height="400" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?visual=true&url=' . $this->link . '&show_artwork=false&buying=false&sharing=false&show_comments=false"></iframe>';
                    $embedCode .= _ad('1');
                } else {
                    $embedCode = INVALID_URL;
                }
                break;

            case 'dailymotion':
                $videoId = $this->getVideoId("video/");
                if ($videoId != null) {
                    $embedCode .= '<iframe frameborder="0" width="' . $this->width . '" height="' . $this->height . '" src="https://www.dailymotion.com/embed/video/' . $videoId . '"></iframe>';
                    $embedCode .= _ad('1');
                } else {
                    $embedCode = INVALID_URL;
                }
                break;

            case 'twitch':
                $videoId = $this->getVideoId(".tv/videos/");
                if (is_empty($videoId)) {
                    $videoId = $twch_ch = $this->getVideoId(".tv/");
                }
                if ($videoId != null) {
                    //$embedCode .= '<iframe  src="https://player.twitch.tv/?autoplay=true&parent=meta.tag&player=twitter&video='.$videoId.'&parent=meta.tag"     width="' . $this->width . '" height="' . $this->height . '"  allowfullscreen></iframe>';

                    $embedCode .= '<div id="twitch-embed"></div>
    <script src="https://embed.twitch.tv/embed/v1.js"></script>

    <script type="text/javascript">
      var embed = new Twitch.Embed("twitch-embed", {
        width: 854,
        height: 480,';
                    if (isset($twch_ch)) {
                        $embedCode .= ' channel: "' . $twch_ch . '",';
                    } else {
                        $embedCode .= ' video: "' . $videoId . '",';
                    }
                    $embedCode .= '   layout: "video",
        autoplay: true
      });

      embed.addEventListener(Twitch.Embed.VIDEO_READY, () => {
        var player = embed.getPlayer();
        player.play();
      });
    </script>';


                    $embedCode .= _ad('1');
                } else {
                    $embedCode = INVALID_URL;
                }
                break;
            default:
                if (has_filter('EmbedModify')) {
                    $embedCode = apply_filters('EmbedModify', false);
                } else {
                    $embedCode = INVALID_URL;
                }
                break;
        }
        return $embedCode;
    }

    public function getVideoId($operand, $optionaOperand = null)
    {
        $videoId = null;
        $startPosCode = strpos($this->link, $operand);
        if ($startPosCode != null) {
            $videoId = substr($this->link, $startPosCode + strlen($operand), strlen($this->link) - 1);
            if (!is_null($optionaOperand)) {
                $startPosCode = strpos($videoId, $optionaOperand);
                if ($startPosCode > 0) {
                    $videoId = substr($videoId, 0, $startPosCode);
                }
            }
        }
        return $videoId;
    }

    // generate video embed code via using standart templates

    public function getLastNr($url)
    {
        $pieces_array = explode('/', $url);
        $end_piece = end($pieces_array);
        $id_pieces = explode('-', $end_piece);
        $last_piece = end($id_pieces);
        $videoId = preg_replace("/[^0-9]/", "", $last_piece);
        return $videoId;
    }

    // get id from weird rewrites

    public function VideoProvider($link = null)
    {


        if (is_null($link)) {
            $thisProvider = $this->decideVideoProvider();
        } else {
            $this->link = $link;
            $thisProvider = $this->decideVideoProvider();
        }
        return $thisProvider;
    }

    public function remotevideo($url)
    {
        global $video;
        $embedCode = '';
        if ($url) {
            $pieces_array = explode('.', $url);
            $ext = end($pieces_array);
            $choice = get_option('remote-player', 1);
            $mobile_supported = array("mp4", "mp3", "webm", "ogv", "m3u8", "ts", "tif");
            if (!in_array($ext, $mobile_supported)) {
                /*force jwplayer always on non-mobi formats, as others are just html5 */
                $choice = 1;
            }
            if ($choice == 1) {
                $embedCode = _jwplayer($url, $video->thumb(), thumb_fix(get_option('player-logo')), $ext);
            } elseif ($choice == 2) {
                $embedCode = flowplayer($url, $video->thumb(), thumb_fix(get_option('player-logo')), $ext);
            } elseif ($choice == 6) {
                $embedCode = vjsplayer($url, $video->thumb(), thumb_fix(get_option('player-logo')), $ext);
            } else {
                $embedCode = _jpcustom($url, $video->thumb());
            }
        }
        return $embedCode;
    }

    function get_data()
    {
        $default = array('thumbnail' => '', 'title' => '', 'tags' => '', 'description' => '', 'duration' => '');
        $details = $this->get_details();
        if (is_array($details)) {
            return array_replace($default, $details);
        } else {
            return $default;
        }
    }

    function get_details()
    {
        global $VibeProvider, $VibeLink;
        $provider = $this->decideVideoProvider();
        // Plugins need this
        $VibeProvider = $provider;
        $VibeLink =$this->link;
        // End plugin globals
        //var_dump($provider);
        switch ($provider) {


            case 'soundcloud':
                $video = get_soundcloud($this->link);
                return $video;
                break;
            case 'vimeo':
                $json_url = "https://vimeo.com/api/v2/video/" . $this->getLastNr($this->link) . ".json";
                //echo  $json_url ;
                $content = $this->getDataFromUrl($json_url);
                $video = json_decode($content, true);
                $video[0]['thumbnail'] = $video[0]['thumbnail_medium'];
                return $video[0];
                break;
            case 'youtube':
                if (!nullval(get_option('youtubekey', null))) {
                    $yt = new YoutubeLite(array('key' => get_option('youtubekey')));
                    $id = $yt->parseVIdFromURL($this->link);
                    $video = $yt->Single($id);
                    $tags = array_unique(explode('-', nice_tag(removeCommonWords($video["title"]))));
                    $video["tags"] = implode(',', $tags);
                    return $video;
                }
                break;
            case 'metacafe':
                $idvid = $this->getVideoId("watch/", "/");
                $file_data = "https://www.metacafe.com/api/item/" . $idvid;
                $video = array();
                $xml = new SimpleXMLElement(file_get_contents($file_data));
                $title_query = $xml->xpath('/rss/channel/item/title');
                $video['title'] = $title_query ? strval($title_query[0]) : '';
                $description_query = $xml->xpath('/rss/channel/item/media:description');
                $video['description'] = $description_query ? strval($description_query[0]) : '';
                $tags_query = $xml->xpath('/rss/channel/item/media:keywords');
                $video['tags'] = $tags_query ? explode(',', strval(trim($tags_query[0]))) : null;
                if (isset($video['tags']) && !empty($video['tags'])) {
                    $video['tags'] = implode(', ', $video['tags']);
                } else {
                    $video['tags'] = '';
                }
                $date_published_query = $xml->xpath('/rss/channel/item/pubDate');
                $video['uploaded'] = $date_published_query ? ($date_published_query[0]) : null;
                $thumbnails_query = $xml->xpath('/rss/channel/item/media:thumbnail/@url');
                if (isset($thumbnails_query[0])) {
                    $video['thumbnail'] = strval($thumbnails_query[0]);
                } else {
                    $video['thumbnail'] = '';
                }
                $video['duration'] = null;
                return $video;
                break;
            case 'dailymotion':
                if (preg_match('#https://www.dailymotion.com/video/([A-Za-z0-9]+)#s', $this->link, $match)) {
                    $idvid = $match[1];
                }
                $file_data = "https://www.dailymotion.com/rss/video/" . $idvid;
                $video = array();
                $xml = new SimpleXMLElement(file_get_contents($file_data));
                $title_query = $xml->xpath('/rss/channel/item/title');
                $video['title'] = $title_query ? strval($title_query[0]) : '';
                $description_query = $xml->xpath('/rss/channel/item/description');
                $video['description'] = $description_query ? strval($description_query[0]) : '';
                $tags_query = $xml->xpath('/rss/channel/item/media:keywords');
                if (!empty($tags_query) && $tags_query) {
                    $video['tags'] = $tags_query ? explode(',', strval(trim($tags_query[0]))) : null;
                    $video['tags'] = implode(', ', $video['tags']);
                } else {
                    $video['tags'] = '';
                }
                $date_published_query = $xml->xpath('/rss/channel/item/pubDate');
                $video['uploaded'] = $date_published_query ? ($date_published_query[0]) : null;
                $thumbnails_query = $xml->xpath('/rss/channel/item/media:thumbnail/@url');
                $video['thumbnail'] = strval($thumbnails_query[0]);
                $duration_query = $xml->xpath('/rss/channel/item/media:group/media:content/@duration');
                $video['duration'] = $duration_query ? intval($duration_query[0]) : null;
                return $video;
            case 'myspace':
                # Get XML data URL
                $file_data = "https://mediaservices.myspace.com/services/rss.ashx?type=video&videoID=" . $this->getLastNr($this->link);
                # XML
                $xml = new SimpleXMLElement(file_get_contents($file_data));
                $video = array();
                # Get video title
                $title_query = $xml->xpath('/rss/channel/item/title');
                $video['title'] = $title_query ? strval($title_query[0]) : '';
                # Get video description
                $description_query = $xml->xpath('/rss/channel/item/media:content/media:description');
                $video['description'] = $description_query ? strval($description_query[0]) : '';
                # Get video tags
                $tags_query = $xml->xpath('/rss/channel/item/media:keywords');
                $video['tags'] = $tags_query ? explode(',', strval(trim($tags_query[0]))) : null;
                $video['tags'] = implode(', ', $video['tags']);
                # Fet video duration
                $duration_query = $xml->xpath('/rss/channel/item/media:content/@duration');
                $video['duration'] = $duration_query ? intval($duration_query[0]) : null;
                # Get video publication date
                $date_published_query = $xml->xpath('/rss/channel/item/pubDate');
                $video['uploaded'] = $date_published_query ? ($date_published_query[0]) : null;
                # Get video thumbnails
                $thumbnails_query = $xml->xpath('/rss/channel/item/media:thumbnail/@url');
                $video['thumbnail'] = strval($thumbnails_query[0]);
                return $video;
                break;
            default:

                $video = array();
                $video['description'] = '';
                $video['title'] = '';
                $video['duration'] = '';
                $video['thumbnail'] = '';

                if ($video) {
                    require CNC . '/opengraph.php';
                    $graphs = OpenGraph::fetch($this->link);
                    if(not_empty($graphs)) {
                        foreach ($graphs as $key => $value) {
                            $newkey = str_replace(':', '_', $key);
                            $newkey = str_replace('tw_tter', 'tw', $key);
                            $graph[$newkey] = $value;
                        }

                        //var_dump($graph);
                        if (isset($graph['image'])) {
                            $video['thumbnail'] = $graph['image'];
                        }
                        if (isset($graph['video:duration'])) {
                            $video['duration'] = $graph['video:duration'];
                        }
                        if (isset($graph['description'])) {
                            $video['description'] = $graph['description'];
                        }
                        if (isset($graph['title'])) {
                            $video['title'] = $graph['title'];
                        }
                    }
                    //var_dump($video);


                    /* End null check */
                }
                if (has_filter('EmbedDetails')) {
                    $video = apply_filters('EmbedDetails', $video );
                }
                return $video;
                break;
        }
    }

    function getDataFromUrl($url)
    {
        $ch = curl_init();
        $timeout = 15;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // add this one, it seems to spawn redirect 301 header
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'); // spoof
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    private function match($regex, $str, $i = 0)
    {
        if (preg_match($regex, $str, $match) == 1) {
            return $match[$i];
        } else {
            return null;
        }
    }
}
