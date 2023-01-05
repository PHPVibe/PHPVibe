<?php namespace PHPVibe\Video;
class SingleVideo {
	private $id;
    public function __construct($id)     { 
	global $db;
	$this->id = $id;  
	$this->db = $db;  
	$this->videodetails = $db->get_row("SELECT * from ".DB_PREFIX."videos where id = '".intval($id)."' ");
	$this->videodescription = $db->get_var("SELECT description from ".DB_PREFIX."description where vid = '".intval($id)."' ");
    }
	public function id() {
		return $this->id;
	
	}
	public function token() {
		return $this->videodetails->token;		
	}
	public function rawtitle() {
		return $this->videodetails->title;		
	}
	public function title() {
		return _html($this->videodetails->title);		
	}
	public function rawdescription() {
		return $this->videodescription;
		
	}
	public function description() {
		return _html($this->videodescription);	
		
	}
	public function rawthumb() {
		return $this->videodetails->thumb;		
	}
	public function thumb() {
		return thumb_fix($this->videodetails->thumb);		
	}
	public function rawthumbnails() {
		$thumbs = array();
		$tp = ABSPATH.'/storage/'.get_option('mediafolder','media')."/thumbs/";
		$pattern = "{*".$this->token()."*}";
		$vl = glob($tp.$pattern, GLOB_BRACE);

			if($vl) {
				$i = 1;
				foreach($vl as $vidid) {
					$thumbs[$i] = str_replace(ABSPATH.'/' ,'',$vidid);
					$i++;
				}
			}
		return $thumbs;
	}
	public function thumbnails() {
		$thumbs = $this->rawthumbnails();
		$fullthumbs = array();
		$i = 1;
		foreach($thumbs as $thumb) {
			$fullthumbs[$i] = thumb_fix($thumb);
			$i++;
		}
		
		return $fullthumbs;
	}
	public function rawcategory() {
		$cat= null;
		if(not_empty($this->videodetails->category)){
			$cat= explode(',',$this->videodetails->category);
		}
		return $cat;		
	}
	public function category() {
		return $this->videodetails->category;		
				
	}
	public function categories() {	
		$cat= null;
		if(not_empty($this->videodetails->category)){
			$cat= explode(',',$this->videodetails->category);
		}
		return $cat;	
		
	}
	public function tags() {
	$tags = array();
	$tagdetails = $this->db->get_results("SELECT id, tag_name FROM ".DB_PREFIX."tag_names where id in (SELECT tag_id FROM ".DB_PREFIX."tag_rel where media_id = '".intval($this->id)."')");
	$i = 1;
		if($tagdetails) {
			foreach($tagdetails as $tag) {
				$tags[$i]['id'] = $tag->id;
				$tags[$i]['name'] = $tag->tag_name;
				$i++;
			}
			
		}
	return $tags;
	}
	public function prettytags($class='tagslink', $pre='', $post = '') {
	$tags =$this->tags();
	$taglinks = array();
	$i = 1;
		if($tags) {
			foreach($tags as $tag) {				
				$taglinks[$i] = $tag['name'];				
				$i++;
			}			
		}
		$links = implode(',', $taglinks);
	return pretty_tags($links, $class, $pre, $post);
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
	
}
?>