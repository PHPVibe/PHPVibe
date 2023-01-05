<?php if(isset($_POST['action'])) {
var_dump($_POST);
$target= 'yt_'.$_POST['action'];
$pageVars = array_filter($_POST);
$pageVars['p'] = 1;
unset($pageVars['action']);
$pc = admin_url($target).'&'.urldecode(http_build_query($pageVars));
redirect($pc);
exit();
}

$users = $db->get_results("SELECT id, name FROM  ".DB_PREFIX."users where id <> '".user_id()."' order by id asc limit 0,200");
$cats = cats_select("categ","select","");
 ?>
<h2 class=""> Youtube Importer</h2>
<?php
if(nullval(get_option('youtubekey',null))){ ?>
<div class="msg-warning">Your Youtube API key is empty.</div>
<div class="msg-info">Set your key <a href="<?php echo admin_url('ytsetts');?>"><strong>here</strong> first</a>!</div>	
<?php } ?>

<ul class="nav nav-tabs" id="myTab">
  <li class="active"><a href="#search">Search Videos</a></li>
  <li><a href="#playlist">Playlist Videos</a></li>
  <li><a href="#channel">Channel Videos</a></li>
</ul>

<div class="tab-content" style="min-height:900px;">
  <div class="tab-pane active" id="search">
  <div class="row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('yt');?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="action" value="search">
<div class="form-group form-material">
<label class="control-label"><i class="icon-search"></i>Term to search</label>
<div class="controls">
<input type="text" name="q" class="validate[required] col-md-8" value=""> 						
</div>	
</div>
<div class="form-group form-material">
	<label class="control-label">Results type</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="type" class="styled" value="video" checked> Videos </label>
	<label class="radio inline"><input type="radio" name="type" class="styled" value="playlist"> Playlists </label>
	<label class="radio inline"><input type="radio" name="type" class="styled" value="channel"> Channels </label>

	<span class="help-block">What type of resources should this search return? </span>				

	</div>
	</div>	
<div class="form-group form-material">
	<label class="control-label">Video import mode</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="imode" class="styled" value="1"> Automated (Save all results directly) </label>
	<label class="radio inline"><input type="radio" name="imode" class="styled" value="2" checked> List (Display & save by choice)</label>
	<span class="help-block">You wish to import all the videos, or just pick what to import by hand? </span>				
	</div>
	</div>	
<div class="form-group form-material">
	<label class="control-label">Items per page</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="50"> 50 </label>
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="45"> 45 </label>
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="40"> 40 </label>
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="35"> 35 </label>
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="30"> 30 </label>
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="25" checked> 25 </label>
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="20"> 20 </label>
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="15"> 15 </label>
    <label class="radio inline"><input type="radio" name="bpp" class="styled" value="10"> 10 </label>
    <label class="radio inline"><input type="radio" name="bpp" class="styled" value="5"> 5 </label>
	<span class="help-block">Browse per page (mostly for videos) </span>				

	</div>
	</div>	
<?php
echo '<div class="form-group form-material">
	<label class="control-label">Store videos in:</label> 	<div class="controls"> 	'.$cats.'  </div> 
	<span class="help-block">Pick your category for this import. </span></div>';
?>	  
	<div class="form-group form-material">
	<label class="control-label">Order by</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="order" class="styled" value="relevance" checked /> Relevance </label>
	<label class="radio inline"><input type="radio" name="order" class="styled" value="date" />Date</label>
	<label class="radio inline"><input type="radio" name="order" class="styled" value="viewCount" /> Views Count </label>
	<label class="radio inline"><input type="radio" name="order" class="styled" value="rating" />Rating</label>
		<label class="radio inline"><input type="radio" name="order" class="styled" value="title" />Alphabetically by title</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Add to owner</label>
	<div class="controls">
	<?php
	echo '<select data-placeholder="'._lang("Choose owner:").'" name="owner" id="clear-results" class="select validate[required]" tabindex="2"> 	';
    echo'<option value="'.user_id().'" selected>'.user_name().'</option>';
    if($users) {
    foreach ($users as $cat) {	echo'<option value="'.intval($cat->id).'">'._html($cat->name).'</option>'; 	}
    }
    echo '</select>'; 	
	?>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Safe search</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="safeSearch" class="styled" value="strict" /> Strict </label>
	<label class="radio inline"><input type="radio" name="safeSearch" class="styled" value="moderate" checked />Moderate</label>
	<label class="radio inline"><input type="radio" name="safeSearch" class="styled" value="none" /> None </label>

	</div>
	</div>
<div class="form-group form-material">
	<label class="control-label">Grab all returned results</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="0" checked>NO</label>
	<span class="help-block">Youtube API returns up to maximum 500 results for a search. Note: This may be server heavy. </span>				

	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label">Allow already existing videos</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="0" checked>NO</label>
	<span class="help-block">If set to NO it will search if video is already in the database and skip it. </span>				
		
	</div>
	</div>	
	<div class="control-group" style="padding:40px 0 30px 10px;">
<button type="submit" class="btn btn-large btn-primary">Start importing</button> 						

</div>	
		<div class="form-group form-material">
<label class="control-label">Restrict video results to channel id</label>
<div class="controls">
<input type="text" name="channelID" class="col-md-4" value=""> 	
<span class="help-block"> If set, indicates that the API response should only contain resources created by the channel id. Example: <span class="redText">UCYXoMlSvybbjLxoyVORBThg</span> <br>
Details at <a href="https://developers.google.com/youtube/v3/docs/search/list#channelId	" target="_blank">Youtube API (channelId)</a> </span>				
					
</div>	
</div>
	<div class="form-group form-material">
<label class="control-label">Region Code</label>
<div class="controls">
<input type="text" name="regionCode" class="col-md-1" value=""> 	
<span class="help-block"> The parameter value is an <a href="http://www.iso.org/iso/country_codes/iso_3166_code_lists/country_names_and_code_elements.htm" target="_blank">ISO 3166-1 alpha-2</a> country code.<br>
Details at <a href="https://developers.google.com/youtube/v3/docs/search/list#regionCode" target="_blank">Youtube API (RegionCode)</a> </span>				
					
</div>	
</div>
	<div class="form-group form-material">
<label class="control-label">Relevance Language</label>
<div class="controls">
<input type="text" name="relevanceLanguage" class="col-md-1" value=""> 	
<span class="help-block">  The parameter value is typically an <a href="http://www.loc.gov/standards/iso639-2/php/code_list.php" target="_blank">ISO 639-1 two-letter language code</a>.<br>
Details at <a href="https://developers.google.com/youtube/v3/docs/search/list#regionCode" target="_blank">Youtube API (relevanceLanguage)</a> </span>				
					
</div>	
</div>
		

  
	</form>    
    </div>
   </div> 
   
   <div class="tab-pane" id="playlist">
  <div class="row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('yt');?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="action" value="playlistsearch">
<div class="form-group form-material">
<label class="control-label"><i class="icon-search"></i>Playlist ID</label>
<div class="controls">
<input type="text" name="pID" class="validate[required] col-md-8" value=""> 						
</div>	
</div>
<div class="form-group form-material">
	<label class="control-label">Video import mode</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="imode" class="styled" value="1"> Automated (Save all results directly) </label>
	<label class="radio inline"><input type="radio" name="imode" class="styled" value="2" checked> List (Display & save by choice)</label>
	<span class="help-block">You wish to import all the videos, or just pick what to import by hand? </span>				
	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label">Items per page</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="15" checked> 15 </label>
		</div>
	</div>
	<?php
echo '<div class="form-group form-material">
	<label class="control-label">Store videos in:</label> 	<div class="controls"> 	'.$cats.'  </div> 
	<span class="help-block">Pick your category for this import. </span></div>';
?>	  
	<div class="form-group form-material">
	<label class="control-label">Order by</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="order" class="styled" value="relevance" checked /> Relevance </label>
	<label class="radio inline"><input type="radio" name="order" class="styled" value="date" />Date</label>
	<label class="radio inline"><input type="radio" name="order" class="styled" value="viewCount" /> Views Count </label>
	<label class="radio inline"><input type="radio" name="order" class="styled" value="rating" />Rating</label>
		<label class="radio inline"><input type="radio" name="order" class="styled" value="title" />Alphabetically by title</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Add to owner</label>
	<div class="controls">
	<?php
	echo '<select data-placeholder="'._lang("Choose owner:").'" name="owner" id="clear-results" class="select validate[required]" tabindex="2"> 	';
    echo'<option value="'.user_id().'" selected>'.user_name().'</option>';
    if($users) {
    foreach ($users as $cat) {	echo'<option value="'.intval($cat->id).'">'._html($cat->name).'</option>'; 	}
    }
    echo '</select>'; 	
	?>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Grab all returned results</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="0" checked>NO</label>
	<span class="help-block">Youtube API returns up to maximum 500 results for a search. Note: This may be server heavy. </span>				

	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label">Allow already existing videos</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="0" checked>NO</label>
	<span class="help-block">If set to NO it will search if video is already in the database and skip it. </span>				
		
	</div>
	</div>	
	<div class="control-group" style="padding:40px 0 30px 10px;">
<button type="submit" class="btn btn-large btn-primary">Start importing</button> 						

</div>	
</form>
</div>
</div>
    
   <div class="tab-pane" id="channel">
  <div class="row">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('yt');?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="action" value="bc">
<div class="form-group form-material">
<label class="control-label"><i class="icon-search"></i>Channel</label>
<div class="controls">
  <div class="row">
  <div class="col-md-4">
<input type="text" name="c" class="col-md-12" value="" placeholder="Channel ID"> 
</div>
<div class="col-md-1"> <strong class="redText">OR</strong> </div>	
<div class="col-md-4">					
<input type="text" name="chName" class="col-md-12" value="" placeholder="Channel Name"> 						
</div>
</div>
</div>	
</div>
<div class="form-group form-material">
	<label class="control-label">Video import mode</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="imode" class="styled" value="1"> Automated (Save all results directly) </label>
	<label class="radio inline"><input type="radio" name="imode" class="styled" value="2" checked> List (Display & save by choice)</label>
	<span class="help-block">You wish to import all the videos, or just pick what to import by hand? </span>				
	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label">Items per page</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="bpp" class="styled" value="15" checked> 15 </label>
		</div>
	</div>
	<?php
echo '<div class="form-group form-material">
	<label class="control-label">Store videos in:</label> 	<div class="controls"> 	'.$cats.'  </div> 
	<span class="help-block">Pick your category for this import. </span></div>';
?>	  
	<div class="form-group form-material">
	<label class="control-label">Order by</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="order" class="styled" value="relevance" checked /> Relevance </label>
	<label class="radio inline"><input type="radio" name="order" class="styled" value="date" />Date</label>
	<label class="radio inline"><input type="radio" name="order" class="styled" value="viewCount" /> Views Count </label>
	<label class="radio inline"><input type="radio" name="order" class="styled" value="rating" />Rating</label>
		<label class="radio inline"><input type="radio" name="order" class="styled" value="title" />Alphabetically by title</label>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Add to owner</label>
	<div class="controls">
	<?php
	echo '<select data-placeholder="'._lang("Choose owner:").'" name="owner" id="clear-results" class="select validate[required]" tabindex="2"> 	';
    echo'<option value="'.user_id().'" selected>'.user_name().'</option>';
    if($users) {
    foreach ($users as $cat) {	echo'<option value="'.intval($cat->id).'">'._html($cat->name).'</option>'; 	}
    }
    echo '</select>'; 	
	?>
	</div>
	</div>
	<div class="form-group form-material">
	<label class="control-label">Grab all returned results</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="auto" class="styled" value="0" checked>NO</label>
	<span class="help-block">Youtube API returns up to maximum 500 results for a search. Note: This may be server heavy. </span>				

	</div>
	</div>	
	<div class="form-group form-material">
	<label class="control-label">Allow already existing videos</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="1"> YES </label>
	<label class="radio inline"><input type="radio" name="allowduplicates" class="styled" value="0" checked>NO</label>
	<span class="help-block">If set to NO it will search if video is already in the database and skip it. </span>				
		
	</div>
	</div>	
	<div class="control-group" style="padding:40px 0 30px 10px;">
<button type="submit" class="btn btn-large btn-primary">Start importing</button> 						

</div>	
</form>
</div>
</div>
 
  </div>
 
