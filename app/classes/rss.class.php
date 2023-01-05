<?php
	
	/**
	 * Feed RSS class.
	 *
	 * @file feed.class.php
	 * @brief Feed RSS class.
	 * @update 2010-09-17 12:10:00 (Fri Sep 17, 2010 at 12:10 AM)
	 * @author Paolo Rovelli
	 **/
	class FeedRSS {
		var $articles = array();

		//RSS channel info:
		var $title = "";  		// the title of the RSS channel
		var $link = "";  		// the link of the RSS channel
		var $description = "";  // the description of the RSS channel
		var $language = "";  	// the language of the RSS channel
		
		//Optional TAG:
		//var $rating = "";
		var $copyright = "";  		// the copyright string for the RSS channel
		var $pubDate = "";  		// the publication date for the content in the RSS channel
		var $lastBuildDate = "";  	// the last time the content of the RSS channel changed
		var $generator = "";  		// the software that generates the RSS channel
		//var $docs = "";  			// URL of documentation for the format used in the RSS file
		//var $cloud = "";  		// allows processes to register with a cloud
		//var $ttl = "";  			// TTL (Time To Live)
		var $managingEditor = "";  	// the e-Mail address of the person responsible for the publication in the RSS channel
		var $webMaster = "";  		// the e-Mail address of the webmaster of the RSS channel
		//var $skipHours = "";  	// the hours (GMT) in the day when the channel is unlikely to be updated (if it is omitted => updated hourly)
		//var $skipDays = "";  		// a list of days (of the week) when your channel will not be updated
		
		var $image = array('url' => "", 'title' => "", 'link' => "", 'width' => 0, 'height' => 0, 'description' => "");  // the RSS channel image
		

		
		/** 
		 * Initialize the RSS channel info.
		 * 
		 * @param $title  the title of the RSS channel
		 * @param $link  the link of the RSS channel
		 * @param $description  the description of the RSS channel
		 * @param $language  the language of the RSS channel
		 * @param $copyright  the copyright string for the RSS channel
		 * @param $pubDate  the publication date for the content in the RSS channel
		 * @param $lastBuildDate  the last time the content of the RSS channel changed
		 * @param $generator  the software that generates the RSS channel
		 * @param $managingEditor  the responsible for the publication in the RSS channel
		 * @param $webMaster  the webmaster of the RSS channel
		 * @author Paolo Rovelli
		 **/
		function FeedRSS($title, $link, $description, $language, $copyright, $pubDate, $lastBuildDate, $generator, $managingEditor, $webMaster) {
			$this->title = $title;
			$this->link = $link;
			$this->description = $description;
			$this->language = $language;
			$this->copyright = $copyright;
			//$this->rating = $rating;
			$this->pubDate = $pubDate;
			$this->lastBuildDate = $lastBuildDate;
			$this->generator = $generator;
			//$this->docs = $docs;
			//$this->cloud = $cloud;
			//$this->ttl = $ttl;
			$this->managingEditor = $managingEditor;
			$this->webMaster = $webMaster;
			//$this->skipHours = $managingEditor;
			//$this->skipDays = $managingEditor;
		}
		

		
		/** 
		 * Add channel image.
		 * 
		 * @param $title  the title of the channel image
		 * @param $url  the location (URL) to load the channel image
		 * @param $link  the link of the RSS channel
		 * @param $width  the width-size of the channel image (range: 1-144 - default value: 88)
		 * @param $height  the height-size of the channel image (range: 1-400 - default value: 31)
		 * @param $description  the description of the RSS channel
		 * @author Paolo Rovelli
		 **/
		function AddImage($title, $url, $link, $width, $height, $description = "") {
			$this->image['title'] = $title;
			$this->image['url'] = $url;
			$this->image['link'] = $link;
			
			if ( $width == "" || $height == "" || $width < 1 || $width > 144 || $height < 1 || $height > 400 ) {
				if ( $tmp = @getimagesize($url) ) {
					$this->image['width'] = ($tmp[0] > 144) ? 144 : $tmp[0];
					$this->image['height'] = ($tmp[1] > 400) ? 400 : $tmp[1];
				}
				else {  // !($tmp = @getimagesize($url))
					$this->image['width'] = 88;
					$this->image['height'] = 31;
				}
			}
			else {  //  $width != "" && $height != "" && $width >= 1 && $width <= 144 && $height >= 1 && $height <= 400
				$this->image['width'] = $width;
				$this->image['height'] = $height;
			}
			
			$this->image['description'] = $description;
		}
		

		
		/** 
		 * Add channel items (articles, posts, ...).
		 * 
		 * @param $title  the title of the item
		 * @param $link  the link of the item
		 * @param $author  the author of the item
		 * @param $category  the category of the item
		 * @param $categoryDomain  the URL of the category page of the item
		 * @param $pubDate  the date indicating when the item was published
		 * @author Paolo Rovelli
		 **/
		function AddArticle($title, $link, $pubDate, $author, $category, $categoryDomain, $description, $content) {
			//Add a new article/post:
			$item = array_push($this->articles, array('title' => $title, 'link' => $link, 'guid' => $link, 'pubDate' => $pubDate, 'author' => $author, 'category' => $category, 'categoryDomain' => $categoryDomain, 'description' => $description, 'content' => $content));
		}
		
		
		
		/** 
		 * Publish the RSS channel.
		 * 
		 * @param $save  true if the output has to store on the file system, false if the output has to print on screen
		 * @param $path  the path in which stored the output on the file system
		 * @author Paolo Rovelli
		 **/
		function Output() {
		 	//XML header:
			$out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
			$out .= "\n";
			

			//RSS header:
			$out .= "<rss version=\"2.0\" xmlns:content=\"purl.org/rss/1.0/modules/content/\" xmlns:wfw=\"wellformedweb.org/CommentAPI/\" xmlns:dc=\"purl.org/dc/elements/1.1/\" xmlns:atom=\"www.w3.org/2005/Atom\" xmlns:sy=\"purl.org/rss/1.0/modules/syndication/\" xmlns:slash=\"purl.org/rss/1.0/modules/slash/\">";
			$out .= "\n";
			$out .= "\t".'<channel>';
			$out .= "\n";
			

			//RSS channel info:
			$out .= "\t\t<title>". $this->title ."</title>\n";
			$out .= "\t\t<atom:link href=\"". $this->link ."\" rel=\"self\" type=\"application/rss+xml\" />";
			$out .= "\t\t<link>". $this->link ."</link>\n";
			$out .= "\t\t<description>". $this->description ."</description>\n";
			$out .= "\t\t<language>". $this->language ."</language>\n";
			
			if ( $this->copyright != "" ) {  // is there a copyright...
				$out .= "\t\t<copyright>". $this->copyright ."</copyright>\n";
			}
			
			if ( $this->pubDate != "" ) {  // is there a publication date...
				$out .= "\t\t<pubDate>". $this->pubDate ."</pubDate>\n";
			}
			
			if ( $this->lastBuildDate != "" ) {  // is there a last build date...
				$out .= "\t\t<lastBuildDate>". $this->lastBuildDate ."</lastBuildDate>\n";
			}
			
			if ( $this->generator != "" ) {  // is there a generator...
				$out .= "\t\t<generator>". $this->generator ."</generator>\n";
			}
			
			if ( $this->managingEditor != "" ) {  // is there a managing editor...
				$out .= "\t\t<managingEditor>". $this->managingEditor ."</managingEditor>\n";
			}
			
			if ( $this->webMaster != "" ) {  // is there a web master...
				$out .= "\t\t<webMaster>". $this->webMaster ."</webMaster>\n";
			}
			
			$out .= "\t\t<sy:updatePeriod>hourly</sy:updatePeriod>\n";
			$out .= "\t\t<sy:updateFrequency>1</sy:updateFrequency>\n";
			
			
			//RSS channel image:
			if ( $this->image['title'] && $this->image['url'] && $this->image['link'] ) {
			 	$out .= "\n"; 
				$out .= "\t\t<image>\n";
				$out .= "\t\t<title>". $this->image['title'] ."</title>\n";
				$out .= "\t\t\t<url>". $this->image['url'] ."</url>\n";
				$out .= "\t\t\t<link>". $this->image['link'] ."</link>\n";
				
				if ( $this->image['description'] ) {
					$out .= "\t\t\t<description>". $this->image['description'] ."</description>\n";
				}  // $this->image['description']
				
				if ( $this->image['width'] && $this->image['height'] ) {
					$out .= "\t\t\t<width>". $this->image['width'] ."</width>\n";
					$out .= "\t\t\t<height>". $this->image['height'] ."</height>\n";
				}  // $this->image['w'] && $this->image['h']
				
				$out .= "\t\t</image>\n";
			}  // $this->image['title'] && $this->image['url'] && $this->image['link']
			
			$out .= "\n";

			
			//RSS items:
			for($i=0; $i < count($this->articles); $i++) {
				$out .= "\t\t<item>\n";
				//$item = array_push($this->articles, array('title' => $title, 'link' => $link, 'description' => $description, 'author' => $author, 'category' => "<![CDATA[$category]]>", 'pubDate' => $pubDate));

				$out .= "\t\t\t<title><![CDATA[". $this->articles[$i]['title'] ."]]></title>\n";
				$out .= "\t\t\t<link>". $this->articles[$i]['link'] ."</link>\n";
				$out .= "\t\t\t<guid isPermaLink=\"true\">". $this->articles[$i]['guid'] ."</guid>\n";
				$out .= "\t\t\t<pubDate>". $this->articles[$i]['pubDate'] ."</pubDate>\n";
				//$out .= "\t\t\t<author>". $this->articles[$i]['author'] ."</author>\n";
				//$out .= "\t\t\t<dc:creator>". $this->articles[$i]['author'] ."</dc:creator>\n";
				$out .= "\t\t\t<category domain=\"". $this->articles[$i]['categoryDomain'] ."\"><![CDATA[". $this->articles[$i]['category'] ."]]></category>\n";
				$out .= "\t\t\t<description><![CDATA[". $this->articles[$i]['description'] ."]]></description>\n";
				$out .= "\t\t\t<content:encoded><![CDATA[". $this->articles[$i]['content'] ."]]></content:encoded>\n";
				
				$out .= "\t\t</item>\n";
			}
			

			//RSS footer:
			$out .= "\t</channel>\n";
			$out .= "</rss>";
			
			
			//Feed XML header:
			header("Content-type: application/xml");
			
			//Feed RSS body:
			echo $out;
			
			
			return true;
		}
	}
	
?>