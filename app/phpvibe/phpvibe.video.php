<?php 
namespace PHPVibe\Video;
class SingleVideo {
    var $error  = null;
    var $errorlog  = null;
    var $db  = null;
var $videodetails = null;
	private $id;

    public function __construct($id)     { 
	global $db;
	$this->error = false; //Let's be optimistic
	$this->errorlog = null;
	//Handle missing video
		if(is_empty(intval($id)) || (intval($id) < 0) ) {
			$this->errorlog = "Invalid video id (".$id."). ";
            $this->error = true; // No longer optimistic			
			return false;
		}
	$this->id = intval($id);  
	$this->db = $db; 
	$mediaid = get_jcm_folder($this->id).'video-'.$this->id;
	if(jc_exists($mediaid)) {
		//send video from cache
			$this->videodetails = jc_get($mediaid);
        if(!$this->videodetails || empty($this->videodetails)){
            $this->errorlog = "This video may have been deleted (id: ".$id."). ";
            $this->error = true; // No longer optimistic
            layout('404');
            return false;
        }
	} else {   	
		//Queries
		//Video & desc.
			$this->videodetails  = $this->db->get_row("SELECT ".DB_PREFIX."videos.*, ".DB_PREFIX."description.description FROM ".DB_PREFIX."videos 
			LEFT JOIN ".DB_PREFIX."description ON ".DB_PREFIX."videos.id =".DB_PREFIX."description.vid WHERE ".DB_PREFIX."videos.`id` = '".$this->id."'");
				// Handle missing video
				if(!$this->videodetails || empty($this->videodetails)) {
					$this->errorlog = "The single video with id ".$id." is not found. ";
					$this->error = true; // No longer optimistic
            $this->errorlog = "This video may have been deleted (id: ".$id."). ";
            $this->error = true; // No longer optimistic
            layout('404');
            return false;
            //die();
        }
		//Tags
			$this->videodetails->tagdetails = $this->db->get_results("SELECT id, tag_name FROM ".DB_PREFIX."tag_names where id in (SELECT tag_id FROM ".DB_PREFIX."tag_rel where media_id = '".intval($this->id)."')");
		//Categories
			$this->videodetails->channels = null;
			if(not_empty($this->videodetails->category)) { 
				$this->videodetails->channels = $this->db->get_results("SELECT cat_id, cat_name FROM ".DB_PREFIX."channels WHERE cat_id in (".$this->videodetails->category.")");
			}	
		//Author
			$this->videodetails->author = null;
			if(not_empty($this->videodetails->user_id)) { 
				$this->videodetails->author = $this->db->get_row("SELECT avatar, group_id, name from ".DB_PREFIX."users where id='".$this->videodetails->user_id."'");
			}
		//Cache it now
		jc_put($mediaid,$this->videodetails);
	}
    // End construct
	}
	public function isvalid() {
		return !$this->error;	
	}
	public function error() {
		return $this->errorlog;	
	}
	public function id() {
		return $this->id;	
	}

	public function rawtitle() {
		return $this->videodetails->title;		
	}

	public function title() {
		return _html($this->videodetails->title);		
	}

	public function rawdescription() {
		return $this->videodetails->description;
		
	}

	public function description() {
		return _html($this->videodetails->description);	
		
	}

	public function rawthumb() {
		return $this->videodetails->thumb;		
	}

	public function thumb() {
		return thumb_fix($this->videodetails->thumb);		
	}

	public function thumbnails() {
			$thumbs = $this->rawthumbnails();
			$fullthumbs = array();
			$i = 0;
			foreach($thumbs as $thumb) {
				$fullthumbs[$i] = thumb_fix($thumb);
				$i++;
			}
		
		return $fullthumbs;
	}

	public function rawthumbnails() {
			$thumbs = array();
			if($this->isyoutube()){
				$linkul = explode('/mq', $this->videodetails->thumb);			
				$thumbs[0] = $this->videodetails->thumb;
				for ($i = 1; $i < 4 ; $i++) {
				$thumbs[$i] = $linkul[0].'/mq'.$i.'.jpg';
				}
		}elseif($this->islink()) { 
			$thumbs[1] = $this->videodetails->thumb;
		}elseif(!$this->islink()) {			
			
			$tp = ABSPATH.'/storage/'.get_option('mediafolder','media')."/thumbs/";
			$pattern = "{*".$this->token()."*}";
			$vl = glob($tp.$pattern, GLOB_BRACE);

				if($vl) {
					$i = 0;
					foreach($vl as $vidid) {
						$thumbs[$i] = str_replace(ABSPATH.'/' ,'',$vidid);
						$i++;
					}
				}
			
		}
		return $thumbs;
	}

	public function isyoutube() {
		return ( _contains($this->source(), 'youtube') || _contains($this->source(), 'youtu.be'));	
	}

	public function source() {
		return ($this->videodetails->source);
	}

	public function islink() {
		return (filter_var($this->videodetails->source, FILTER_VALIDATE_URL));	
	}

	public function token() {
		return $this->videodetails->token;		
	}

	public function rawcategorylist() {
		return $this->videodetails->category;		
	}

	public function categories($glue = '', $class ='categorylink', $li = false, $liclass='categorylinks') {
			$list= '';
			$cats = $this->categorylinks($class,$li,$liclass);
			foreach($cats as $cat) {
				$list .= $cat.$glue;
			}
		return $list;
		
	}

	public function categorylinks($class ='categorylink', $li = false, $liclass='categorylinks') {
		$links = array();
		$i= 0;		
		$cats = $this->rawcategories();
			foreach($cats as $cat) {
				if(not_empty($cat['name'])) {
				$links[$i] = '';
				if($li) { $links[$i] = '<li class='.$liclass.'>';}
				$links[$i] .= '<a class='.$class.' href="'.channel_url($cat['id'],$cat['name']).'">'._html($cat['name']).'</a>';
				if($li) { $links[$i] .= '</li>';}
				$i++;
				}
			}
		
		return $links;
	}

	public function rawcategories() {
		$cats = array();
		$i= 0;
		if(is_array($this->videodetails->channels)) {
			foreach($this->videodetails->channels as $channel) {
				$cats[$i]['id'] = $channel->cat_id;
				$cats[$i]['name'] = $channel->cat_name;
				$i++;
			}
		}
		return $cats;		
				
	}

	public function prettytags($class='tagslink', $pre='', $post = '') {
		$tags =$this->tags();
		$taglinks = array();
		$i = 0;
			if($tags) {
				foreach($tags as $tag) {				
					$taglinks[$i] = $tag['name'];				
					$i++;
				}			
			}
			$links = implode(',', $taglinks);
		return pretty_tags($links, $class, $pre, $post);
	}

	public function tags() {
		$tags = array();
		$tagdetails = $this->videodetails->tagdetails;
		$i = 0;
			if($tagdetails) {
				foreach($tagdetails as $tag) {
					if(not_empty($tag->tag_name)) {
					$tags[$i]['id'] = $tag->id;
					$tags[$i]['name'] = $tag->tag_name;
					$i++;
					}
				}
				
			}
		return $tags;
	}

	public function seconds() {
		return $this->videodetails->duration;
	}	

	public function duration() {
		return	video_time($this->videodetails->duration);
	}

	public function views() {
		return	$this->videodetails->views;
	}

	public function likes() {
		return	$this->videodetails->liked;
	}

	public function dislikes() {
		return	$this->videodetails->disliked;
	}

	public function published() {
		return ($this->videodetails->pub == 1);
	}

	public function owner() {
		return $this->videodetails->user_id;
	}

	public function rawauthor() {
		return $this->videodetails->author->name;
	}

	public function author() {
		return _html($this->videodetails->author->name);
	}

	public function authorgroup() {
		return $this->videodetails->author->group_id;
	}

	public function rawavatar() {
		return $this->videodetails->author->avatar;
	}

	public function avatar() {
		return thumb_fix($this->videodetails->author->avatar);
	}

	public function authorlink() {
		return profile_url($this->videodetails->user_id, $this->videodetails->author->name);
	}

	public function isvideo() {
		return ($this->videodetails->media < 2);
	}

	public function ismusic() {
		return ($this->videodetails->media > 1);
	}

	public function ispremium() {
		return ($this->videodetails->ispremium == 1);
	}

	public function ispublished() {
		return ($this->videodetails->pub == 1);
	}

	public function isfeatured() {
		return ($this->videodetails->featured == 1);
	}

	public function ispublic() {
		return ($this->videodetails->stayprivate == 0);
	}

	public function isprivate() {
		return ($this->videodetails->stayprivate == 1);
	}

	public function isremote() {
		return (not_empty($this->videodetails->remote));
	}

	public function remote() {
		return	$this->videodetails->remote;
	}

	public function isembed() {
		return (not_empty($this->videodetails->embed));
	}

	public function embed() {
		return	$this->videodetails->embed;
	}

	public function hassource() {
		return (not_empty($this->videodetails->source));
	}

	public function isupload() {
		return ( ($this->source() == 'up') || _contains($this->source(), 'localfile'));	
	}
	public function nsfw() {
		return ($this->videodetails->nsfw == 1);
	}
	public function added() {
		return $this->videodetails->date;
	}
	public function timeago() {
		return time_ago($this->videodetails->date);
	}
	public function hassrt() {
		return (not_empty($this->videodetails->srt));
	}
	public function srt() {
		if(not_empty($this->videodetails->srt)) {
		return $this->videodetails->srt;
		} else {
			return false;
		}
	}
}
/* Video updater */
class VideoUpdate {
	private $id;
	private $items;
    public function __construct($id)     { 
	global $db;
	$this->db = $db; 
	$this->error = false; //Let's be optimistic
	$this->errorlog = null;
	//Handle missing video
		if(is_empty(intval($id)) || (intval($id) < 0) ) {
			// Not a valid id
			$this->errorlog = "Invalid video id (".$id."). ";
            $this->error = true; 			
			return false;
		}
		$this->id = intval($id);
		$this->newdata = array();
	}
	public function add($items) {
		if(is_array($items)) {
			$this->newdata = array_merge($items, $this->newdata);
		} else {
		$this->error = 	'Unformated input'.$items;
		}
	}
	public function isdone() {
		return !$this->error;	
	}
	public function error() {
		return $this->errorlog;	
	}

	public function doupdate() {
		$defaults = $this->defaults();
		$changes = $tagchanges = array();
		$new = null;
		$added = false;
	 
	 // Separate tags
	 if(array_key_exists('tags', $this->newdata)) {
		 $newtags = $this->newdata['tags'];
		 unset($this->newdata['tags']);	
		 
		 if(isset($newtags) && not_empty($newtags)) {
			// Process tags
			  $oldtags = array();
			  $j = 0;
			   if(isset($defaults['tags']) && not_empty($defaults['tags'])) {
					  foreach ($defaults['tags'] as $a) {
						   $oldtags[$j]['name'] =$a['name'];
						   $j++;		  
					  }
			   }
			  
			  /* New tags */
			  sort($newtags);
			  sort($oldtags);
			  //print_r($newtags);
			  //print_r($oldtags);			  

			  if($newtags !== $oldtags) {
				 clear_tags($this->id);
				foreach ($newtags as $aname) {
				//echo $aname['name'];		
				  save_tag($aname['name'], $this->id);
				  }
				  $added = true;
				  
			  }
			}
		 }
	 
	 // Check for other changed fields (new data)
	 $changes = array_diff_assoc($this->newdata,$defaults['data']);
	 
	 // Check and update description table
	 if(not_empty( $changes )) {
	 if(array_key_exists("description", $changes)) {
		 if($changes["description"] !== $defaults['data']['description'] ) {
			$this->updatedescription($changes["description"]);	
				unset($changes["description"]);	
			$added = true;	
		 }		
	 }
	 
	 foreach ($changes as $change => $value) {
		 $new .= $change." = '". $value."', ";
	 }
	 $newquery = rtrim($new,', ');
	 
	 // Make changes [Video update query]
	  if(not_empty( $newquery )) {
			$this->db->query("UPDATE ".DB_PREFIX."videos SET ".$newquery." WHERE id = ".$this->id."");
			$added = true;			
		  }
	 
	 }
			 if( $added) {
				//Clear and refresh video cache
				refresh_multimedia($this->id);
				//Return true
				$send = _lang('Changes saved');
				} else {
				//No change
				$send =_lang('No changes to save');	
			}
	 return $send;
	}

	private function defaults() {

		$video = new SingleVideo($this->id);
		//Main video data
		$fields['data'] =  array(
		  'ispremium' => ($video->ispremium() ? '1' : '0'),
		  'pub' =>  ($video->ispublished() ? '1' : '0'),
		  'date' => $video->added(),
		  'featured' => ($video->isfeatured() ? '1' : '0'),
		  'stayprivate' => ($video->isprivate() ? '1' : '0'),
		  'source' => $video->source(),
		  'title' => $video->rawtitle(),
		  'thumb' => $video->rawthumb(),
		  'duration' => $video->seconds(),
		  'category' => $video->rawcategorylist(),
		  'views' => $video->views(),
		  'liked' =>$video->likes(),
		  'disliked' => $video->dislikes(),
		  'nsfw' => ($video->nsfw() ? '1' : '0'),
		  'embed' =>  $video->embed(),
		  'remote' => $video->remote(),
		  'description' => $video->rawdescription(),
		  'srt' => $video->srt());
		  //Separate tags
		  $fields['tags'] = array();
		  $fields['tags'] = $video->tags() ;
		 
		  return $fields;
	}  

	private function updatedescription($text) {
		$this->db->query("UPDATE ".DB_PREFIX."description SET description='".toDb($text)."' WHERE vid = '".$this->id."'");
		//$this->db->debug();
	}

	
	}
	

?>
