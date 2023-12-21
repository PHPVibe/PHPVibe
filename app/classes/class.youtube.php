<?php

/**
 * Youtube Data API V3 for PHPVibe
 * @version 3.4
 */
class YoutubeLite
{
    /**
     * @var string
     */
    protected $youtube_key; //pass in by constructor

    /**
     * @var string
     */
    protected $referer;

    /**
     * @var string
     */
    private $nextoken;

    /**
     * @array
     */
    private $tokenlist;

    /**
     * @var array
     */
    var $APIs = array(
        'videos.list' => 'https://www.googleapis.com/youtube/v3/videos',
        'search.list' => 'https://www.googleapis.com/youtube/v3/search',
        'channels.list' => 'https://www.googleapis.com/youtube/v3/channels',
        'playlists.list' => 'https://www.googleapis.com/youtube/v3/playlists',
        'playlistItems.list' => 'https://www.googleapis.com/youtube/v3/playlistItems',
        'activities' => 'https://www.googleapis.com/youtube/v3/activities',
    );

    /**
     * @var array
     */
    public $page_info = array();

    /**
     * Constructor
     * $youtube = new YoutubeLite(array('key' => 'KEY HERE'))
     *
     * @param array $params
     * @throws \Exception
     */
    public function __construct($params = array())
    {
        if (!is_array($params)) {
            throw new \InvalidArgumentException('The configuration options must be an array.');
        }

        if (!array_key_exists('key', $params)) {
            throw new \InvalidArgumentException('Google API key is required, please visit http://code.google.com/apis/console');
        }
        $this->youtube_key = isset($params['key']) ? $params['key'] : '';
        $this->nextoken = isset($params['pageToken']) ? $params['pageToken'] : '';
        $this->tokenlist = array();
        if (array_key_exists('referer', $params)) {
            $this->referer = $params['referer'];
        }
    }

    /**
     * @param $vId
     * @return \StdClass
     * @throws \Exception
     */
    public function getVideoInfo($vId)
    {
        $API_URL = $this->getApi('videos.list');
        $params = array(
            'id' => $vId,
            'key' => $this->youtube_key,
            'part' => 'id, snippet, contentDetails,status'
        );

        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeSingle($apiData);
    }


    /**
     * Parse a youtube URL to get the youtube Vid.
     * Support both full URL (www.youtube.com) and short URL (youtu.be)
     *
     * @param string $youtube_url
     * @return string Video Id
     * @throws \Exception
     */
    public static function parseVIdFromURL($youtube_url)
    {
        if (strpos($youtube_url, 'youtube.com')) {
            $params = static::_parse_url_query($youtube_url);
            return $params['v'];
        } else if (strpos($youtube_url, 'youtu.be')) {
            $path = static::_parse_url_path($youtube_url);
            $vid = substr($path, 1);
            return $vid;
        } else {
            throw new \Exception('The supplied URL does not look like a Youtube URL');
        }
    }

    /*
    *  Internally used Methods, set visibility to public to enable more flexibility
    */

    /**
     * @param $name
     * @return mixed
     */
    public function getApi($name)
    {
        return $this->APIs[$name];
    }

    /**
     * Decode the response from youtube, extract the single resource object.
     * (Don't use this to decode the response containing list of objects)
     *
     * @param string $apiData the api response from youtube
     * @return \StdClass  an Youtube resource object
     * @throws \Exception
     */
    public function decodeSingle(&$apiData)
    {
        $itemsArray = false;
        $resObj = json_decode($apiData);


        if (isset($resObj->error)) {
            $msg = "Error " . $resObj->error->code . " " . $resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : " . $resObj->error->errors[0]->reason;
            }
            throw new \Exception($msg, $resObj->error->code);
        } else {
            if (isset($resObj->items)) {
                $itemsArray = $resObj->items;
            }
            if (!is_array($itemsArray) || count($itemsArray) == 0) {
                return false;
            } else {
                return $itemsArray[0];
            }
        }
    }

    /**
     * Queries a single video
     * @param $video
     * @return \Array
     **/
    public function Single($id = false)
    {
        if ($id) {
            return $this->MakeVideoPretty($this->getVideoInfo($id));
        }
    }

    /**
     * Returns a basic video array
     * @param $video
     * @return \Array
     **/
    public function MakeVideoPretty($video)
    {
        $v = array();

        $v['videoid'] = $v['id'] = $video->id;
        $v['url'] = 'https://www.youtube.com/watch?v=' . $video->id;
        $v['thumb'] = $v['thumbnail'] = $video->snippet->thumbnails->medium->url;
        $v['title'] = htmlentities($video->snippet->title, ENT_QUOTES, "UTF-8");
        $v['description'] = htmlentities($video->snippet->description, ENT_QUOTES, "UTF-8");
        $v['duration'] = $this->getDurationSeconds($video->contentDetails->duration);
        $v['ptime'] = $video->contentDetails->duration;
        $v['privacy'] = $video->status->privacyStatus;
        $v['embeddable'] = (bool)$video->status->embeddable;
        $v['ytChannelID'] = $video->snippet->channelId;
        $v['author'] = $v['ytChannelTitle'] = $video->snippet->channelTitle;
        $v['ytPublished'] = $video->snippet->publishedAt;

        return $v;
    }

    /**
     * Decodes PT*M*S to seconds
     * @param $duration
     * @return \String
     **/
    public function getDurationSeconds($duration)
    {
        preg_match_all('/[0-9]+[HMS]/', $duration, $matches);
        $duration = 0;
        foreach ($matches as $match) {
            foreach ($match as $portion) {
                $unite = substr($portion, strlen($portion) - 1);
                switch ($unite) {
                    case 'H':
                        {
                            $duration += substr($portion, 0, strlen($portion) - 1) * 60 * 60;
                        }
                        break;
                    case 'M':
                        {
                            $duration += substr($portion, 0, strlen($portion) - 1) * 60;
                        }
                        break;
                    case 'S':
                        {
                            $duration += substr($portion, 0, strlen($portion) - 1);
                        }
                        break;
                }
            }
        }
        return $duration - 1;
	}
        /**
         * Using CURL to issue a GET request
         *
         * @param $url
         * @param $params
         * @return mixed
         * @throws \Exception
         */
        public function api_get($url, $params)
        {

            //jcache
            $jkey = preg_replace('/\s+/', '', implode('-', $params));
            $jkey = str_replace(',', '', $jkey);
            $jkey = get_jcm_folder($jkey, 'yt') . '-' . $jkey;
            if (jc_exists($jkey)) {
                //send video from cache
                //echo 'Cached result';
                return jc_get($jkey);
            } else {

                //set the youtube key
                $params['key'] = $this->youtube_key;

                //boilerplates for CURL
                $tuCurl = curl_init();
                curl_setopt($tuCurl, CURLOPT_URL, $url . (strpos($url, '?') === false ? '?' : '') . http_build_query($params));
                if (strpos($url, 'https') === false) {
                    curl_setopt($tuCurl, CURLOPT_PORT, 80);
                } else {
                    curl_setopt($tuCurl, CURLOPT_PORT, 443);
                }
                if ($this->referer !== null) {
                    curl_setopt($tuCurl, CURLOPT_REFERER, $this->referer);
                }
                curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
                $tuData = curl_exec($tuCurl);
                if (curl_errno($tuCurl)) {
                    throw new \Exception('Curl Error : ' . curl_error($tuCurl));
                }
                //Cache it now
                jc_put($jkey, $tuData);
                //send
                return $tuData;
            }
        }

        /**
         * Parse the input url string and return just the path part
         *
         * @param string $url the URL
         * @return string      the path string
         */
        public static function _parse_url_path($url)
        {
            $array = parse_url($url);
            return $array['path'];
        }

        /**
         * Parse the input url string and return an array of query params
         *
         * @param string $url the URL
         * @return array      array of query params
         */
        public static function _parse_url_query($url)
        {
            $array = parse_url($url);
            $query = $array['query'];

            $queryParts = explode('&', $query);

            $params = array();
            foreach ($queryParts as $param) {
                $item = explode('=', $param);
                $params[$item[0]] = empty($item[1]) ? '' : $item[1];
            }
            return $params;
        }
    }

    /** End class */

    /** Helpers */
    function ytExists($id = null)
    {
        /* Alias */
        return has_youtube_duplicate($id);
    }

    function has_youtube_duplicate($y_id = null)
    {
        global $db;
        if (!nullval($y_id)) {
            $sub = $db->get_row("Select count(*) as nr from " . DB_PREFIX . "videos where source  like '%youtube.com/watch?v=" . $y_id . "'");
            return (bool)$sub->nr;
        }
        /* Return true if no id to prevent importing */
        return true;
    }

    function youtube_import($video = array(), $cat = null, $owner = null)
    {
        global $db;
        /* Import a Youtube video to PHPVibe */
        if (is_null($owner)) {
            $owner = user_id();
        }
        if (!isset($video["state"])) {
            $video["state"] = intval(get_option('videos-initial'));
            if (is_moderator()) {
                $video["state"] = 1;
            }
        }
        if (isset($video["videoid"]) && isset($video["title"])) {
            $video["path"] = (isset($video["url"])) ? $video["url"] : 'https://www.youtube.com/watch?v=' . $video["videoid"];
            if (!isset($video["thumbnail"]) || is_empty($video["thumbnail"])) {
                $video["thumbnail"] = "https://i4.ytimg.com/vi/" . $video['videoid'] . "/hqdefault.jpg";
                if (!validateRemote($video["thumbnail"])) {
                    $video["thumbnail"] = "https://i4.ytimg.com/vi/" . $video['videoid'] . "/default.jpg";
                }
            }
            $tags = array_unique(explode('-', nice_tag(removeCommonWords($video["title"]))));
            if (!isset($video["tags"]) || nullval($video["tags"])) {
                $video["tags"] = implode(',', $tags);
            } else {
                $video["tags"] .= ',' . implode(',', $tags);
            }

            if (!isset($video["featured"])) {
                $video["featured"] = 0;
            }
            $token = md5($video["videoid"] . time());
            if (is_array($cat)) {
                $cat = implode(',', $cat);
            }
            $db->query("INSERT INTO " . DB_PREFIX . "videos (`token`,`featured`,`pub`,`source`, `user_id`, `date`, `thumb`, `title`, `duration`, `views` , `liked` , `category`,`nsfw`) VALUES 
	('" . $token . "','" . $video["featured"] . "','" . $video["state"] . "','" . $video["path"] . "', '" . $owner . "', now() , '" . $video["thumbnail"] . "', '" . toDb($video["title"]) . "', '" . intval($video["duration"]) . "', '0', '0','" . toDb($cat) . "','0')");
            //Recover new id
            $theid = getVideobyToken($token);
            if ($theid) {
                //Add tags
                foreach (explode(',', $video["tags"]) as $tagul) {
                    save_tag($tagul, $theid);
                }
                //Add description
                save_description($theid, $video["description"]);
            }
            //Done
            //var_dump($video);
        } else {
            echo '<p><span class="redText">Missing video id or title </span></p>';
        }
    }
