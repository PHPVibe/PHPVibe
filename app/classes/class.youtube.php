<?php
/**
 * Youtube Data API V3 for PHPVibe 
 * @version 3.4
 */
class Youtube
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
     * $youtube = new Youtube(array('key' => 'KEY HERE'))
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
     * @param $vIds
     * @return \StdClass
     * @throws \Exception
     */
    public function getVideosInfo($vIds)
    {
        $ids = is_array($vIds) ? implode(',', $vIds) : $vIds;
        $API_URL = $this->getApi('videos.list');
        $params = array(
            'id' => $ids,
            'part' => 'id, snippet, contentDetails, player, statistics, status'
        );

        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeList($apiData);
    }

    /**
     * Simple search interface, this search all stuffs
     * and order by relevance
     *
     * @param $q
     * @param int $maxResults
     * @return array
     */
    public function search($q, $maxResults = 10)
    {
        $params = array(
            'q' => $q,
            'part' => 'id, snippet',
            'maxResults' => $maxResults
        );
        return $this->searchAdvanced($params);
    }

    /**
     * Search only videos
     *
     * @param  string $q Query
     * @param  integer $maxResults number of results to return
     * @param  string $order Order by
     * @return \StdClass  API results
     */
    public function searchVideos($q, $maxResults = 10, $order = null)
    {
        $params = array(
            'q' => $q,
            'type' => 'video',
            'part' => 'id, snippet',
            'maxResults' => $maxResults
        );
        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params);
    }

    /**
     * Search only videos in the channel
     *
     * @param  string $q
     * @param  string $channelId
     * @param  integer $maxResults
     * @param  string $order
     * @return object
     */
    public function searchChannelVideos($q, $channelId, $maxResults = 10, $order = null)
    {
        $params = array(
            'q' => $q,
            'type' => 'video',
            'channelId' => $channelId,
            'part' => 'id, snippet',
            'maxResults' => $maxResults
        );
        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->searchAdvanced($params);
    }

    /**
     * Generic Search interface, use any parameters specified in
     * the API reference
     *
     * @param $params
     * @param $pageInfo
     * @return array
     * @throws \Exception
     */
    public function searchAdvanced($params, $pageInfo = false)
    {
        $API_URL = $this->getApi('search.list');

        if (empty($params) || !isset($params['q'])) {
            throw new \InvalidArgumentException('at least the Search query must be supplied');
        }

        $apiData = $this->api_get($API_URL, $params);
        if ($pageInfo) {
            return array(                
                'results' => $this->decodeList($apiData),
                'info'    => $this->page_info
            );
        } else {
            return $this->decodeList($apiData);
        }
    }

     /**
     * Generic page tokens, depends on $bpp (browse per page)
     * the <<dirty>> non-API reference to guess nextPageToken.
     * @param $bpp
     * @return array
     */
	public function ytokens($bpp = 25) {	
	 $pagination = array(); 
     $pagination[50] = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CDIQAA', ), 3 => array ( 'token' => 'CGQQAA', ), 4 => array ( 'token' => 'CJYBEAA', ), 5 => array ( 'token' => 'CMgBEAA', ), 6 => array ( 'token' => 'CPoBEAA', ), 7 => array ( 'token' => 'CKwCEAA', ), 8 => array ( 'token' => 'CN4CEAA', ), 9 => array ( 'token' => 'CJADEAA', ), 10 => array ( 'token' => 'CMIDEAA', ), ); 
     $pagination[45] = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CC0QAA', ), 3 => array ( 'token' => 'CFoQAA', ), 4 => array ( 'token' => 'CIcBEAA', ), 5 => array ( 'token' => 'CLQBEAA', ), 6 => array ( 'token' => 'COEBEAA', ), 7 => array ( 'token' => 'CI4CEAA', ), 8 => array ( 'token' => 'CLsCEAA', ), 9 => array ( 'token' => 'COgCEAA', ), 10 => array ( 'token' => 'CJUDEAA', ), 11 => array ( 'token' => 'CMIDEAA', ), 12 => array ( 'token' => 'CO8DEAA', ), ); 
     $pagination[40] = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CCgQAA', ), 3 => array ( 'token' => 'CFAQAA', ), 4 => array ( 'token' => 'CHgQAA', ), 5 => array ( 'token' => 'CKABEAA', ), 6 => array ( 'token' => 'CMgBEAA', ), 7 => array ( 'token' => 'CPABEAA', ), 8 => array ( 'token' => 'CJgCEAA', ), 9 => array ( 'token' => 'CMACEAA', ), 10 => array ( 'token' => 'COgCEAA', ), 11 => array ( 'token' => 'CJADEAA', ), 12 => array ( 'token' => 'CLgDEAA', ), 13 => array ( 'token' => 'COADEAA', ), ); 
     $pagination[35] = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CCMQAA', ), 3 => array ( 'token' => 'CEYQAA', ), 4 => array ( 'token' => 'CGkQAA', ), 5 => array ( 'token' => 'CIwBEAA', ), 6 => array ( 'token' => 'CK8BEAA', ), 7 => array ( 'token' => 'CNIBEAA', ), 8 => array ( 'token' => 'CPUBEAA', ), 9 => array ( 'token' => 'CJgCEAA', ), 10 => array ( 'token' => 'CLsCEAA', ), 11 => array ( 'token' => 'CN4CEAA', ), 12 => array ( 'token' => 'CIEDEAA', ), 13 => array ( 'token' => 'CKQDEAA', ), 14 => array ( 'token' => 'CMcDEAA', ), 15 => array ( 'token' => 'COoDEAA', ), ); 
     $pagination[30] = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CB4QAA', ), 3 => array ( 'token' => 'CDwQAA', ), 4 => array ( 'token' => 'CFoQAA', ), 5 => array ( 'token' => 'CHgQAA', ), 6 => array ( 'token' => 'CJYBEAA', ), 7 => array ( 'token' => 'CLQBEAA', ), 8 => array ( 'token' => 'CNIBEAA', ), 9 => array ( 'token' => 'CPABEAA', ), 10 => array ( 'token' => 'CI4CEAA', ), 11 => array ( 'token' => 'CKwCEAA', ), 12 => array ( 'token' => 'CMoCEAA', ), 13 => array ( 'token' => 'COgCEAA', ), 14 => array ( 'token' => 'CIYDEAA', ), 15 => array ( 'token' => 'CKQDEAA', ), 16 => array ( 'token' => 'CMIDEAA', ), 17 => array ( 'token' => 'COADEAA', ), ); 
     $pagination[25] = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CBkQAA', ), 3 => array ( 'token' => 'CDIQAA', ), 4 => array ( 'token' => 'CEsQAA', ), 5 => array ( 'token' => 'CGQQAA', ), 6 => array ( 'token' => 'CH0QAA', ), 7 => array ( 'token' => 'CJYBEAA', ), 8 => array ( 'token' => 'CK8BEAA', ), 9 => array ( 'token' => 'CMgBEAA', ), 10 => array ( 'token' => 'COEBEAA', ), 11 => array ( 'token' => 'CPoBEAA', ), 12 => array ( 'token' => 'CJMCEAA', ), 13 => array ( 'token' => 'CKwCEAA', ), 14 => array ( 'token' => 'CMUCEAA', ), 15 => array ( 'token' => 'CN4CEAA', ), 16 => array ( 'token' => 'CPcCEAA', ), 17 => array ( 'token' => 'CJADEAA', ), 18 => array ( 'token' => 'CKkDEAA', ), 19 => array ( 'token' => 'CMIDEAA', ), 20 => array ( 'token' => 'CNsDEAA', ), ); 
     $pagination[20] = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CBQQAA', ), 3 => array ( 'token' => 'CCgQAA', ), 4 => array ( 'token' => 'CDwQAA', ), 5 => array ( 'token' => 'CFAQAA', ), 6 => array ( 'token' => 'CGQQAA', ), 7 => array ( 'token' => 'CHgQAA', ), 8 => array ( 'token' => 'CIwBEAA', ), 9 => array ( 'token' => 'CKABEAA', ), 10 => array ( 'token' => 'CLQBEAA', ), 11 => array ( 'token' => 'CMgBEAA', ), 12 => array ( 'token' => 'CNwBEAA', ), 13 => array ( 'token' => 'CPABEAA', ), 14 => array ( 'token' => 'CIQCEAA', ), 15 => array ( 'token' => 'CJgCEAA', ), 16 => array ( 'token' => 'CKwCEAA', ), 17 => array ( 'token' => 'CMACEAA', ), 18 => array ( 'token' => 'CNQCEAA', ), 19 => array ( 'token' => 'COgCEAA', ), 20 => array ( 'token' => 'CPwCEAA', ), 21 => array ( 'token' => 'CJADEAA', ), 22 => array ( 'token' => 'CKQDEAA', ), 23 => array ( 'token' => 'CLgDEAA', ), 24 => array ( 'token' => 'CMwDEAA', ), 25 => array ( 'token' => 'COADEAA', ), ); 
     $pagination[15] = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CA8QAA', ), 3 => array ( 'token' => 'CB4QAA', ), 4 => array ( 'token' => 'CC0QAA', ), 5 => array ( 'token' => 'CDwQAA', ), 6 => array ( 'token' => 'CEsQAA', ), 7 => array ( 'token' => 'CFoQAA', ), 8 => array ( 'token' => 'CGkQAA', ), 9 => array ( 'token' => 'CHgQAA', ), 10 => array ( 'token' => 'CIcBEAA', ), 11 => array ( 'token' => 'CJYBEAA', ), 12 => array ( 'token' => 'CKUBEAA', ), 13 => array ( 'token' => 'CLQBEAA', ), 14 => array ( 'token' => 'CMMBEAA', ), 15 => array ( 'token' => 'CNIBEAA', ), 16 => array ( 'token' => 'COEBEAA', ), 17 => array ( 'token' => 'CPABEAA', ), 18 => array ( 'token' => 'CP8BEAA', ), 19 => array ( 'token' => 'CI4CEAA', ), 20 => array ( 'token' => 'CJ0CEAA', ), 21 => array ( 'token' => 'CKwCEAA', ), 22 => array ( 'token' => 'CLsCEAA', ), 23 => array ( 'token' => 'CMoCEAA', ), 24 => array ( 'token' => 'CNkCEAA', ), 25 => array ( 'token' => 'COgCEAA', ), 26 => array ( 'token' => 'CPcCEAA', ), 27 => array ( 'token' => 'CIYDEAA', ), 28 => array ( 'token' => 'CJUDEAA', ), 29 => array ( 'token' => 'CKQDEAA', ), 30 => array ( 'token' => 'CLMDEAA', ), 31 => array ( 'token' => 'CMIDEAA', ), 32 => array ( 'token' => 'CNEDEAA', ), 33 => array ( 'token' => 'COADEAA', ), 34 => array ( 'token' => 'CO8DEAA', ), ); 
     $pagination[10] = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CAoQAA', ), 3 => array ( 'token' => 'CBQQAA', ), 4 => array ( 'token' => 'CB4QAA', ), 5 => array ( 'token' => 'CCgQAA', ), 6 => array ( 'token' => 'CDIQAA', ), 7 => array ( 'token' => 'CDwQAA', ), 8 => array ( 'token' => 'CEYQAA', ), 9 => array ( 'token' => 'CFAQAA', ), 10 => array ( 'token' => 'CFoQAA', ), 11 => array ( 'token' => 'CGQQAA', ), 12 => array ( 'token' => 'CG4QAA', ), 13 => array ( 'token' => 'CHgQAA', ), 14 => array ( 'token' => 'CIIBEAA', ), 15 => array ( 'token' => 'CIwBEAA', ), 16 => array ( 'token' => 'CJYBEAA', ), 17 => array ( 'token' => 'CKABEAA', ), 18 => array ( 'token' => 'CKoBEAA', ), 19 => array ( 'token' => 'CLQBEAA', ), 20 => array ( 'token' => 'CL4BEAA', ), 21 => array ( 'token' => 'CMgBEAA', ), 22 => array ( 'token' => 'CNIBEAA', ), 23 => array ( 'token' => 'CNwBEAA', ), 24 => array ( 'token' => 'COYBEAA', ), 25 => array ( 'token' => 'CPABEAA', ), 26 => array ( 'token' => 'CPoBEAA', ), 27 => array ( 'token' => 'CIQCEAA', ), 28 => array ( 'token' => 'CI4CEAA', ), 29 => array ( 'token' => 'CJgCEAA', ), 30 => array ( 'token' => 'CKICEAA', ), 31 => array ( 'token' => 'CKwCEAA', ), 32 => array ( 'token' => 'CLYCEAA', ), 33 => array ( 'token' => 'CMACEAA', ), 34 => array ( 'token' => 'CMoCEAA', ), 35 => array ( 'token' => 'CNQCEAA', ), 36 => array ( 'token' => 'CN4CEAA', ), 37 => array ( 'token' => 'COgCEAA', ), 38 => array ( 'token' => 'CPICEAA', ), 39 => array ( 'token' => 'CPwCEAA', ), 40 => array ( 'token' => 'CIYDEAA', ), 41 => array ( 'token' => 'CJADEAA', ), 42 => array ( 'token' => 'CJoDEAA', ), 43 => array ( 'token' => 'CKQDEAA', ), 44 => array ( 'token' => 'CK4DEAA', ), 45 => array ( 'token' => 'CLgDEAA', ), 46 => array ( 'token' => 'CMIDEAA', ), 47 => array ( 'token' => 'CMwDEAA', ), 48 => array ( 'token' => 'CNYDEAA', ), 49 => array ( 'token' => 'COADEAA', ), 50 => array ( 'token' => 'COoDEAA', ), ); 
     $pagination[5]  = array ( 1 => array ( 'token' => NULL, ), 2 => array ( 'token' => 'CAUQAA', ), 3 => array ( 'token' => 'CAoQAA', ), 4 => array ( 'token' => 'CA8QAA', ), 5 => array ( 'token' => 'CBQQAA', ), 6 => array ( 'token' => 'CBkQAA', ), 7 => array ( 'token' => 'CB4QAA', ), 8 => array ( 'token' => 'CCMQAA', ), 9 => array ( 'token' => 'CCgQAA', ), 10 => array ( 'token' => 'CC0QAA', ), 11 => array ( 'token' => 'CDIQAA', ), 12 => array ( 'token' => 'CDcQAA', ), 13 => array ( 'token' => 'CDwQAA', ), 14 => array ( 'token' => 'CEEQAA', ), 15 => array ( 'token' => 'CEYQAA', ), 16 => array ( 'token' => 'CEsQAA', ), 17 => array ( 'token' => 'CFAQAA', ), 18 => array ( 'token' => 'CFUQAA', ), 19 => array ( 'token' => 'CFoQAA', ), 20 => array ( 'token' => 'CF8QAA', ), 21 => array ( 'token' => 'CGQQAA', ), 22 => array ( 'token' => 'CGkQAA', ), 23 => array ( 'token' => 'CG4QAA', ), 24 => array ( 'token' => 'CHMQAA', ), 25 => array ( 'token' => 'CHgQAA', ), 26 => array ( 'token' => 'CH0QAA', ), 27 => array ( 'token' => 'CIIBEAA', ), 28 => array ( 'token' => 'CIcBEAA', ), 29 => array ( 'token' => 'CIwBEAA', ), 30 => array ( 'token' => 'CJEBEAA', ), 31 => array ( 'token' => 'CJYBEAA', ), 32 => array ( 'token' => 'CJsBEAA', ), 33 => array ( 'token' => 'CKABEAA', ), 34 => array ( 'token' => 'CKUBEAA', ), 35 => array ( 'token' => 'CKoBEAA', ), 36 => array ( 'token' => 'CK8BEAA', ), 37 => array ( 'token' => 'CLQBEAA', ), 38 => array ( 'token' => 'CLkBEAA', ), 39 => array ( 'token' => 'CL4BEAA', ), 40 => array ( 'token' => 'CMMBEAA', ), 41 => array ( 'token' => 'CMgBEAA', ), 42 => array ( 'token' => 'CM0BEAA', ), 43 => array ( 'token' => 'CNIBEAA', ), 44 => array ( 'token' => 'CNcBEAA', ), 45 => array ( 'token' => 'CNwBEAA', ), 46 => array ( 'token' => 'COEBEAA', ), 47 => array ( 'token' => 'COYBEAA', ), 48 => array ( 'token' => 'COsBEAA', ), 49 => array ( 'token' => 'CPABEAA', ), 50 => array ( 'token' => 'CPUBEAA', ), 51 => array ( 'token' => 'CPoBEAA', ), 52 => array ( 'token' => 'CP8BEAA', ), 53 => array ( 'token' => 'CIQCEAA', ), 54 => array ( 'token' => 'CIkCEAA', ), 55 => array ( 'token' => 'CI4CEAA', ), 56 => array ( 'token' => 'CJMCEAA', ), 57 => array ( 'token' => 'CJgCEAA', ), 58 => array ( 'token' => 'CJ0CEAA', ), 59 => array ( 'token' => 'CKICEAA', ), 60 => array ( 'token' => 'CKcCEAA', ), 61 => array ( 'token' => 'CKwCEAA', ), 62 => array ( 'token' => 'CLECEAA', ), 63 => array ( 'token' => 'CLYCEAA', ), 64 => array ( 'token' => 'CLsCEAA', ), 65 => array ( 'token' => 'CMACEAA', ), 66 => array ( 'token' => 'CMUCEAA', ), 67 => array ( 'token' => 'CMoCEAA', ), 68 => array ( 'token' => 'CM8CEAA', ), 69 => array ( 'token' => 'CNQCEAA', ), 70 => array ( 'token' => 'CNkCEAA', ), 71 => array ( 'token' => 'CN4CEAA', ), 72 => array ( 'token' => 'COMCEAA', ), 73 => array ( 'token' => 'COgCEAA', ), 74 => array ( 'token' => 'CO0CEAA', ), 75 => array ( 'token' => 'CPICEAA', ), 76 => array ( 'token' => 'CPcCEAA', ), 77 => array ( 'token' => 'CPwCEAA', ), 78 => array ( 'token' => 'CIEDEAA', ), 79 => array ( 'token' => 'CIYDEAA', ), 80 => array ( 'token' => 'CIsDEAA', ), 81 => array ( 'token' => 'CJADEAA', ), 82 => array ( 'token' => 'CJUDEAA', ), 83 => array ( 'token' => 'CJoDEAA', ), 84 => array ( 'token' => 'CJ8DEAA', ), 85 => array ( 'token' => 'CKQDEAA', ), 86 => array ( 'token' => 'CKkDEAA', ), 87 => array ( 'token' => 'CK4DEAA', ), 88 => array ( 'token' => 'CLMDEAA', ), 89 => array ( 'token' => 'CLgDEAA', ), 90 => array ( 'token' => 'CL0DEAA', ), 91 => array ( 'token' => 'CMIDEAA', ), 92 => array ( 'token' => 'CMcDEAA', ), 93 => array ( 'token' => 'CMwDEAA', ), 94 => array ( 'token' => 'CNEDEAA', ), 95 => array ( 'token' => 'CNYDEAA', ), 96 => array ( 'token' => 'CNsDEAA', ), 97 => array ( 'token' => 'COADEAA', ), 98 => array ( 'token' => 'COUDEAA', ), 99 => array ( 'token' => 'COoDEAA', ), 100 => array ( 'token' => 'CO8DEAA', ), ); 
    if(isset($pagination[$bpp])) return $pagination[$bpp];
	//falback
	return $pagination[25];
	}
	/**
     * [Deprecated] This page's token 
     * @param $bpp
	 * @param $page
     * @return string
     */
	 public function thisToken($bpp = 25, $page = 1) {
		 return $this->NextToken();		
	 }
	 /**
     * Return the next page token 
     * @param $bpp
	 * @param $page
     * @return string
     */
	 public function SetNextToken($new) {
		$this->nextoken = $new;
		$this->tokenlist[] = $new;
	 }
    /**
     * Return the next page token 
     * @param $bpp
	 * @param $page
     * @return string
     */
	 public function NextToken() {
		return $this->nextoken;
	 }
	 /**
     * Renders a full pagination 
     * @param $bpp
	 * @param $url
	 * @param $ulClass
	 * @param $liClass
	 * @param $liActive
     * @returns string
     */
	 public function YPaginate($url = '',$bpp = 25, $ulClass = 'Pages',$liClass = '', $liActive = 'current' ) {
	 return false;
	 }
	 /**
     * Get the number of pages for current $bpp 
     * @param $bpp
     * @return integer
     */
	 public function countPages($bpp = 25) {		 
		 return false;		
	 }
	 
    /**
     * @param $username
     * @return \StdClass
     * @throws \Exception
     */
    public function getChannelByName($username, $optionalParams = false)
    {
        $API_URL = $this->getApi('channels.list');
        $params = array(
            'forUsername' => $username,
            'part' => 'id,snippet,contentDetails,statistics'
        );
        if($optionalParams){
            $params = array_merge($params, $optionalParams);
        }
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeSingle($apiData);
    }

    /**
     * @param $id
     * @return \StdClass
     * @throws \Exception
     */
    public function getChannelById($id, $optionalParams = false)
    {
        $API_URL = $this->getApi('channels.list');
        $params = array(
            'id' => $id,
            'part' => 'id,snippet,contentDetails,statistics'
        );
        if($optionalParams){
            $params = array_merge($params, $optionalParams);
        }
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeSingle($apiData);
    }

    /**
     * @param $channelId
     * @param array $optionalParams
     * @return array
     * @throws \Exception
     */
    public function getPlaylistsByChannelId($channelId, $optionalParams = array())
    {
        $API_URL = $this->getApi('playlists.list');
        $params = array(
            'channelId' => $channelId,
            'part' => 'id, snippet, status'
        );
        if ($optionalParams) {
            $params = array_merge($params, $optionalParams);
        }
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeList($apiData);
    }

    /**
     * @param $id
     * @return \StdClass
     * @throws \Exception
     */
    public function getPlaylistById($id)
    {
        $API_URL = $this->getApi('playlists.list');
        $params = array(
            'id' => $id,
            'part' => 'id, snippet, status'
        );
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeSingle($apiData);
    }

    /**
     * @param $playlistId
     * @return array
     * @throws \Exception
     */
    public function getPlaylistItemsByPlaylistId($playlistId, $maxResults = 50, $pt = null)
    {
        $API_URL = $this->getApi('playlistItems.list');
        $params = array(
            'playlistId' => $playlistId,
            'part' => 'contentDetails, status',
            'maxResults' => $maxResults,
			'pageToken' => $pt
        );
		
		//print_r($pt);
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeList($apiData);
    }

    /**
     * @param $channelId
     * @return array
     * @throws \Exception
     */
    public function getActivitiesByChannelId($channelId)
    {
        if (empty($channelId)) {
            throw new \InvalidArgumentException('ChannelId must be supplied');
        }
        $API_URL = $this->getApi('activities');
        $params = array(
            'channelId' => $channelId,
            'part' => 'id, snippet, contentDetails'
        );
        $apiData = $this->api_get($API_URL, $params);
        return $this->decodeList($apiData);
    }

    /**
     * Parse a youtube URL to get the youtube Vid.
     * Support both full URL (www.youtube.com) and short URL (youtu.be)
     *
     * @param  string $youtube_url
     * @throws \Exception
     * @return string Video Id
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

    /**
     * Get the channel object by supplying the URL of the channel page
     *
     * @param  string $youtube_url
     * @throws \Exception
     * @return object Channel object
     */
    public function getChannelFromURL($youtube_url)
    {
        if (strpos($youtube_url, 'youtube.com') === false) {
            throw new \Exception('The supplied URL does not look like a Youtube URL');
        }

        $path = static::_parse_url_path($youtube_url);
        if (strpos($path, '/channel') === 0) {
            $segments = explode('/', $path);
            $channelId = $segments[count($segments) - 1];
            $channel = $this->getChannelById($channelId);
        } else if (strpos($path, '/user') === 0) {
            $segments = explode('/', $path);
            $username = $segments[count($segments) - 1];
            $channel = $this->getChannelByName($username);
        } else {
            throw new \Exception('The supplied URL does not look like a Youtube Channel URL');
        }

        return $channel;
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
     * @param  string $apiData the api response from youtube
     * @throws \Exception
     * @return \StdClass  an Youtube resource object
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
            throw new \Exception($msg,$resObj->error->code);
        } else {           
            if(isset($resObj->items)) {
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
	public function Single($id = false) {
	if($id){
return $this->MakeVideoPretty($this->getVideoInfo($id));
	}		
	}
	/**
	 * Returns a basic video array
	 * @param $video
     * @return \Array
	**/ 
	public function MakeVideoPretty($video) {		
		$v = array();
		
        $v['videoid'] = $v['id'] = 	$video->id;
        $v['url'] = 'https://www.youtube.com/watch?v='.$video->id;
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
    public function getDurationSeconds($duration){
    preg_match_all('/[0-9]+[HMS]/',$duration,$matches);
    $duration=0;
    foreach($matches as $match){    
        foreach($match as $portion){        
            $unite=substr($portion,strlen($portion)-1);
            switch($unite){
                case 'H':{  
                    $duration +=    substr($portion,0,strlen($portion)-1)*60*60;            
                }break;             
                case 'M':{                  
                    $duration +=substr($portion,0,strlen($portion)-1)*60;           
                }break;             
                case 'S':{                  
                    $duration +=    substr($portion,0,strlen($portion)-1);          
                }break;
            }
        }
    }
     return $duration -1;
    /* seems to add +1 to actual duration */
    }
    /**
     * Decode the response from youtube, extract the list of resource objects
     *
     * @param  string $apiData response string from youtube
     * @throws \Exception
     * @return array Array of StdClass objects
     */
    public function decodeList(&$apiData)
    {
        $resObj = json_decode($apiData);
		
		//print_R($resObj);
		//print_r($resObj->nextPageToken.' || ');
		if (isset($resObj->nextPageToken)) {
			$this->SetNextToken($resObj->nextPageToken);
			$this->nextoken = $resObj->nextPageToken;
		}
		print_r($this->NextToken().' || ');
        if (isset($resObj->error)) {
            $msg = "Error " . $resObj->error->code . " " . $resObj->error->message;
            if (isset($resObj->error->errors[0])) {
                $msg .= " : " . $resObj->error->errors[0]->reason;
            }
            throw new \Exception($msg,$resObj->error->code);
        } else {
             $this->page_info = array(
                'resultsPerPage' => $resObj->pageInfo->resultsPerPage,
                'totalResults'   => $resObj->pageInfo->totalResults,
                'kind'           => $resObj->kind,
                'etag'           => $resObj->etag,
                'prevPageToken'	 => NULL,
				'nextPageToken'	 => NULL
            );
            if(isset($resObj->prevPageToken)){
                $this->page_info['prevPageToken'] = $resObj->prevPageToken;
            }
            if(isset($resObj->nextPageToken)){
                $this->page_info['nextPageToken'] = $resObj->nextPageToken;
            }

            $itemsArray = $resObj->items;
            if (!is_array($itemsArray) || count($itemsArray) == 0) {
                return false;
            } else {
                return $itemsArray;
            }
        }
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
		$jkey = preg_replace('/\s+/', '', implode('-',$params));
		$jkey = str_replace(',','',$jkey);
		$jkey = get_jcm_folder($jkey, 'yt').'-'.$jkey;      
		if(jc_exists($jkey)) {
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
			jc_put($jkey,$tuData);
			//send
			return $tuData;
		}
	}

    /**
     * Parse the input url string and return just the path part
     *
     * @param  string $url the URL
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
     * @param  string $url the URL
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
function ytExists($id = null){
/* Alias */
	return has_youtube_duplicate($id);
}
function has_youtube_duplicate($y_id = null){
	global $db;
	if(!nullval($y_id)) {
	$sub = $db->get_row("Select count(*) as nr from ".DB_PREFIX."videos where source  like '%youtube.com/watch?v=".$y_id."'");
	return (bool)$sub->nr;
	}
	/* Return true if no id to prevent importing */
	return true;
}
function youtube_import($video=array(), $cat = null, $owner = null) {
	global $db;
	/* Import a Youtube video to PHPVibe */
	if(is_null($owner)) {
		$owner = user_id();
		}
	if(!isset($video["state"])) {
	$video["state"] = intval(get_option('videos-initial'));
	if(is_moderator()) {
		$video["state"] = 1;
		}
	}
	if(isset($video["videoid"]) && isset($video["title"]) ) {
		$video["path"] = (isset($video["url"])) ? $video["url"] : 'https://www.youtube.com/watch?v='.$video["videoid"];
	if(!isset($video["thumbnail"]) || is_empty($video["thumbnail"])) {
		$video["thumbnail"] = "https://i4.ytimg.com/vi/" . $video['videoid'] . "/hqdefault.jpg";
	if(!validateRemote($video["thumbnail"])){
		$video["thumbnail"] = "https://i4.ytimg.com/vi/" . $video['videoid'] . "/default.jpg";	
	}
	}
	$tags = array_unique(explode('-',nice_tag(removeCommonWords($video["title"]))));
	if(!isset($video["tags"]) || nullval($video["tags"])) {
		$video["tags"] = implode(',',$tags);
		} else {
		$video["tags"] .= ','.implode(',',$tags);	
	}

	if(!isset($video["featured"])) { $video["featured"] = 0;}
	$token = md5($video["videoid"].time());
	if(is_array($cat)) {
	$cat = implode(',',$cat);	
	}
	$db->query("INSERT INTO ".DB_PREFIX."videos (`token`,`featured`,`pub`,`source`, `user_id`, `date`, `thumb`, `title`, `duration`, `views` , `liked` , `category`,`nsfw`) VALUES 
	('".$token."','".$video["featured"]."','".$video["state"]."','".$video["path"]."', '".$owner."', now() , '".$video["thumbnail"]."', '".toDb($video["title"]) ."', '".intval($video["duration"])."', '0', '0','".toDb($cat)."','0')");	
	//Recover new id
	$theid = getVideobyToken($token);
	if($theid) {
		//Add tags
			foreach (explode(',',$video["tags"]) as $tagul){
				save_tag($tagul,$theid);
			}	
		//Add description
			save_description($theid,$video["description"]);	
	}
	//Done
	//var_dump($video);
	} else {
			echo '<p><span class="redText">Missing video id or title </span></p>';
	}
}
