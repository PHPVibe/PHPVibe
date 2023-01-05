<?php  require '../load.php';
use PHPVibe\Video;
$newvalues = array();
$newvalues = array(
		  'ispremium' => '1',		  
		  'pub' => '1',
		  'date' => '2019-02-22 16:49:39',
		  'featured' => '0',
		  'stayprivate' => '0',
		  'source' => 'https://www.youtube.com/watch?v=m7Bc3pLyij0',
		  'title' => 'Marshmello ft. Bastille - Happier (Official Music)',
		  'thumb' => 'https://i.ytimg.com/vi/m7Bc3pLyij0/mqdefault.jpg',
		  'duration' => '233',
		  'category' => '1,2,3',
		  'views' => '89',
		  'liked' => '0',
		  'disliked' => '0',
		  'nsfw' => '0',
		  'embed' => NULL,
		  'remote' => NULL,
		  'description' => 'Hai la craiova',
		  'tags' => array( array('name' => '22marshmello'),
						  array('name' => '10bastille'),
						  array('name' => '1happier'),
						  array('name' => '10official'),
						  array('name' => '1music'),
						  array('name' => '10video')
						),
		  'srt' => NULL);
$updater = new Video\VideoUpdate(1);
$updater->add($newvalues);
echo '<pre><code>';
print_r($updater->doupdate());
echo '</code></pre>';
//for ($j = 0; $j < 20; $j++){
$video = new Video\SingleVideo(1492);
//print_r($video);
if($video->isvalid()) {
echo $video->id();
echo $video->rawtitle().'<br>'; //Untouched title
echo $video->title().'<br>'; //Html ready title
echo $video->rawdescription().'<br>'; //Untouched description
echo $video->description().'<br>'; //Html ready description
echo $video->token().'<br>';
echo $video->rawthumb().'<br>'; //Untouched thumb value
echo $video->thumb().'<br>'; //Link to thumb
echo '<pre><code>';
print_r($video->thumbnails()); // Array of thumbnail links
echo '</code></pre>';
echo '<br>';
echo $video->categories('<li>&nbsp;</li>','classforlink', true, 'itemclass');
echo '<br>';
echo '<pre><code>';
print_r($video->categorylinks('categorie', true, 'listitem'));
echo '</code></pre>';
echo '<pre><code>';
print_r($video->tags());
echo '</code></pre>';
echo $video->seconds().'<br>';
echo $video->duration().'<br>';
echo $video->added().'<br>';
echo $video->timeago().'<br>';
echo $video->prettytags('taglink', '&nbsp;','&nbsp;').'<br>';
echo $video->views().'<br>';
echo $video->likes().'<br>';
echo $video->dislikes().'<br>';
if($video->hassource()) {
echo $video->source().'<br>';
}
if($video->isembed()) {
echo $video->embed().'<br>';
}
if($video->isremote()) {
echo $video->remote().'<br>';
}
if($video->isupload()) {
echo 'Is uploaded locally<br>';
}
if($video->ispremium()) {echo "Premium";} else { echo "Common";}
echo '<br>';
if($video->isfeatured()) {echo "Featured";} else { echo "Not Featured";}
echo '<br>';
if($video->ispublic()) {echo "Public";} else { echo "Private";}
echo '<br>';
if($video->isprivate()) {echo "is Private";} else { echo "Is public";}
echo '<br>';
if($video->nsfw()) {echo "is NSFW";} else { echo "is Safe";}
echo '<br>';
if($video->hassrt()) {echo $video->srt();} else { echo "No subtitle";}
echo '<br>';
if($video->islink()) {echo 'Is link';} else { echo "No link";}
echo '<br>';
if($video->isyoutube()) {echo 'from Youtube';} else { echo "Not from Youtube";}
echo '<br>';
echo $video->owner().'<br>';
echo $video->rawauthor().'<br>';
echo $video->author().'<br>';
echo $video->authorgroup().'<br>';
echo $video->rawavatar().'<br>';
echo $video->avatar().'<br>';
echo $video->authorlink().'<br>';
} else {
	echo 'We have a problem: ['.$video->error().']';
}
?>