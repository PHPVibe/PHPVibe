<?php //HomeBuilder
if(isset($_GET['delete']))
{
 $db->query("DELETE from " . DB_PREFIX . "homepage WHERE id = '" . intval($_GET['delete']) . "'");
 echo '<div class="msg-info">You deleted the home box with id : ' . $_GET['delete'] . '</div>';
}
if(isset($_POST['channels-list'])) {
$insertvideo = $db->query("INSERT INTO " . DB_PREFIX . "homepage (`title`, `type`, `ident`,`total`, `ord`, `querystring` ,`car`) VALUES ('" . $db->escape($_POST['title']) . "', '6', '" . $db->escape($_POST['thequeries']) . "', '" . $db->escape($_POST['number']) . "', '1', '" . $db->escape($_POST['thequeries']) . "', '" . $db->escape($_POST['car']) . "')");
 echo '<div class="msg-info">Block created!</div>';	
}
if(isset($_POST['playlists-list'])) {
$insertvideo = $db->query("INSERT INTO " . DB_PREFIX . "homepage (`title`, `type`, `ident`,`total`, `ord`, `querystring` ,`car`) VALUES ('" . $db->escape($_POST['title']) . "', '7', '" . $db->escape($_POST['thequeries']) . "', '" . $db->escape($_POST['number']) . "', '1', '" . $db->escape($_POST['thequeries']) . "', '" . $db->escape($_POST['car']) . "')");
 echo '<div class="msg-info">Block created!</div>';	
}
if(isset($_POST['media-block']))
{
 $insertvideo = $db->query("INSERT INTO " . DB_PREFIX . "homepage (`title`, `type`, `ident`, `querystring`, `total`, `ord`, `mtype`,`car` ) VALUES ('" . $db->escape($_POST['title']) . "', '2', '" . $db->escape($_POST['ident']) . "', '" . $db->escape($_POST['queries']) . "', '" . $db->escape($_POST['number']) . "', '1', '" . $db->escape($_POST['type']) . "', '" . $db->escape($_POST['car']) . "')");
 echo '<div class="msg-info">Block created!</div>';
}
if(isset($_POST['html-block']))
{
 $insertvideo = $db->query("INSERT INTO " . DB_PREFIX . "homepage (`title`, `type`, `ident`, `ord` ) VALUES ('" . $db->escape($_POST['title']) . "', '1', '" . $db->escape($_POST['html']) . "', '1')");
 echo '<div class="msg-info">Block created!</div>';
}
if(isset($_POST['playlist-block']))
{
 $insertvideo = $db->query("INSERT INTO " . DB_PREFIX . "homepage (`title`, `type`, `ident`,`total`, `ord`,`car` ) VALUES ('" . $db->escape($_POST['title']) . "', '3', '" . $db->escape($_POST['queries']) . "', '" . $db->escape($_POST['number']) . "', '1', '" . $db->escape($_POST['car']) . "')");
 echo '<div class="msg-info">Block created!</div>';
}
if(isset($_POST['channel-block']))
{
 $insertvideo = $db->query("INSERT INTO " . DB_PREFIX . "homepage (`title`, `type`, `ident`,`total`, `ord`, `querystring` ,`car`) VALUES ('" . $db->escape($_POST['title']) . "', '4', '" . $db->escape($_POST['queries']) . "', '" . $db->escape($_POST['number']) . "', '1', '" . $db->escape($_POST['thequeries']) . "', '" . $db->escape($_POST['car']) . "')");
 echo '<div class="msg-info">Block created!</div>';
}
include_once('setheader.php');

?>
<div class="row">

<div class="row-setts panel-body">
<h3>Frontpage builder</h3>
<div class="row" style="margin:30px 0;">
<ul class="nav nav-tabs " id="myTab">
  <li class="active"><a href="#mediablock">Media block</a></li>
  <li><a href="#cblock">Channel content</a></li>
  <li><a href="#chblock">Channels </a></li>
  <li><a href="#plsblock">Playlists</a></li>
  <li><a href="#pblock">Playlist content</a></li>
   <li><a href="#htmlblock">Pure html</a></li>
   <?php do_action('homepage-admin-lis'); ?>
</ul>
</div>
<div class="row">
<div class="col-md-6 col-xs-12">
<div class="tab-content">
<div class="box-element tab-pane active" id="mediablock">
    <div class="box-head-light"><h3>Create a media block</h3></div>
    <div class="box-content">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('homepage');?>" enctype="multipart/form-data" method="post">
    <input type="hidden" name="media-block" value="1">
    <div class="form-group form-material">
    <label class="control-label">Block title</label>
    <div class="controls">
    <input type="text" id="title" name="title" class="col-md-12" value="">
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label">Results limit</label>
    <div class="controls">
    <input type="text" id="number" name="number" class="col-md-4 validate[required]" value="24">
    <span class="help-block" id="limit-text">Number of items per block.</span>
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label">Query:</label>
    <div class="controls">
    <select data-placeholder="Select type" name="queries" id="queris" class="select validate[required]" tabindex="2">
    <option value="most_viewed">Most viewed </option>
<option value="top_rated">Most liked</option>
<option value="viral" selected>Recent</option>
<option value="featured">Featured</option>
<option value="random">Random </option>

    </select>

    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label"><i class="icon-sort"></i>Media type</label>
    <div class="controls">
    <label class="radio inline"><input type="radio" name="type" data-rel="1" class="styled trigger" value="1" checked>Video</label>
    <label class="radio inline"><input type="radio" name="type" data-rel="2" class="styled trigger" value="2">Music</label>
    <label class="radio inline"><input type="radio" name="type" data-rel="3" class="styled trigger" value="3">Images</label>
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label"><i class="icon-sort"></i>Carousel</label>
    <div class="controls">
    <label class="radio inline"><input type="radio" name="car" class="styled" value="1">Yes</label>
    <label class="radio inline"><input type="radio" name="car" class="styled" value="0" checked>No</label>
    </div>
    </div>    
<?php
echo '
<div class="form-group form-material">
    <label class="control-label">' . _lang("Category:") . '</label>
    <div class="controls">
    <select name="ident" id="ident" class="select">
    ';
$categories = $db->get_results("SELECT cat_id as id, type, cat_name as name FROM  " . DB_PREFIX . "channels order by type,cat_name asc limit 0,10000");
if($categories)
{
 $tt = array(
  "" => "[Video] ",
  "1" => "[Videos] ",
  "2" => "[Songs] ",
  "3" => "[Images] "
 );
 foreach($categories as $cat)
 {
  echo '<option value="' . intval($cat->id) . '">' . $tt[$cat->type] . ' ' . _html($cat->name) . '</option>';
 }
}
echo '<option value="" selected>-- None --</option>';
echo '      
      </select>
          <span class="help-block" id="limit-text"> Optional: Restrict media in block to a category.</span>
      </div>             
      </div>
';
echo '
 <div class="box-bottom clearfix"> <button class="btn btn-primary btn-mini pull-right">Add block</button>  </div>
</form>
</div>';
?>
</div>
<div class="box-element tab-pane" id="chblock">
    <div class="box-head-light"><i class="icon-play"></i><h3>New Channels block</h3></div>
    <div class="box-content">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('homepage');?>" enctype="multipart/form-data" method="post">
        <input type="hidden" name="channels-list" value="1">
    <div class="form-group form-material">
    <label class="control-label">Block title</label>
    <div class="controls">
    <input type="text" id="title" name="title" class="col-md-12" value="">
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label">Results limit</label>
    <div class="controls">
    <input type="text" id="number" name="number" class="col-md-4 validate[required]" value="24">
    <span class="help-block" id="limit-text">Number of items per this block.</span>
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label"><i class="icon-sort"></i>Carousel</label>
    <div class="controls">
    <label class="radio inline"><input type="radio" name="car" class="styled" value="1">Yes</label>
    <label class="radio inline"><input type="radio" name="car" class="styled" value="0" checked>No</label>

    </div>
    </div>
    <div class="form-group form-material">
    <label class="control-label">Channel Query:</label>
    <div class="controls">
    <select data-placeholder="Select type" name="thequeries" id="thequeris" class="select validate[required]" tabindex="2">
    <option value="most_viewed" selected>Most viewed </option>
<option value="top_rated">Most active</option>
<option value="viral">Recent</option>
    </select>

    </div>
    </div>    
 <div class="box-bottom clearfix"> <button class="btn btn-primary btn-mini pull-right">Add block</button>  </div>
</form>
    </div>
    </div>
 <div class="box-element tab-pane" id="cblock">
    <div class="box-head-light"><i class="icon-play"></i><h3>New Channel's media block</h3></div>
    <div class="box-content">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('homepage');?>" enctype="multipart/form-data" method="post">
        <input type="hidden" name="channel-block" value="1">
    <div class="form-group form-material">
    <label class="control-label">Block title</label>
    <div class="controls">
    <input type="text" id="title" name="title" class="col-md-12" value="">
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label">Results limit</label>
    <div class="controls">
    <input type="text" id="number" name="number" class="col-md-4 validate[required]" value="24">
    <span class="help-block" id="limit-text">Number of items per this block.</span>
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label"><i class="icon-sort"></i>Carousel</label>
    <div class="controls">
    <label class="radio inline"><input type="radio" name="car" class="styled" value="1">Yes</label>
    <label class="radio inline"><input type="radio" name="car" class="styled" value="0" checked>No</label>

    </div>
    </div>
    <div class="form-group form-material">
    <label class="control-label">Channel</label>
    <select data-placeholder="Select playlist" name="queries" id="queris" class="select validate[required]" tabindex="2">
    <?php
$c = $cachedb->get_results("Select id,name FROM " . DB_PREFIX . "users order by views desc limit 0,1000 ");
if($c)
{
 foreach($c as $cl)
 {
?>
   <option value="<?php  echo $cl->id;?>">
   <?php  echo _html($cl->name);?> 
   </option>
    <?php
 }
}
?>
   </select>
</div>
    <div class="form-group form-material">
    <label class="control-label">Query:</label>
    <div class="controls">
    <select data-placeholder="Select type" name="thequeries" id="thequeris" class="select validate[required]" tabindex="2">
    <option value="most_viewed">Most viewed </option>
<option value="top_rated">Most liked</option>
<option value="viral" selected>Recent</option>
<option value="featured">Featured</option>
<option value="random">Random </option>

    </select>

    </div>
    </div>    
 <div class="box-bottom clearfix"> <button class="btn btn-primary btn-mini pull-right">Add block</button>  </div>
</form>
    </div>
    </div>
	<div class="box-element tab-pane" id="plsblock">
                     <div class="box-head-light"><i class="icon-mixcloud"></i><h3>Playlists</h3></div>
                    <div class="box-content">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('homepage');
?>" enctype="multipart/form-data" method="post">
        <input type="hidden" name="playlists-list" value="1">
    <div class="form-group form-material">
    <label class="control-label">Block title</label>
    <div class="controls">
    <input type="text" id="title" name="title" class="col-md-12" value="">
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label">Results limit</label>
    <div class="controls">
    <input type="text" id="number" name="number" class="col-md-4 validate[required]" value="24">
    <span class="help-block" id="limit-text">Number of items per this block.</span>
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label"><i class="icon-sort"></i>Carousel</label>
    <div class="controls">
    <label class="radio inline"><input type="radio" name="car" class="styled" value="1">Yes</label>
    <label class="radio inline"><input type="radio" name="car" class="styled" value="0" checked>No</label>

    </div>
    </div>
<div class="form-group form-material">
    <label class="control-label">Playlist selection Query:</label>
    <div class="controls">
    <select data-placeholder="Select type" name="thequeries" id="thequeris" class="select validate[required]" tabindex="2">
    <option value="most_viewed" selected>Most played playlist </option>
    <option value="viral">Recent playlists</option>
	<option value="alb_recent" >Recent galleries</option>
	<option value="alb_mv">Most Viewed Galleries</option>
    </select>

    </div>
    </div> 
 <div class="box-bottom clearfix"> <button class="btn btn-primary btn-mini pull-right">Add block</button>  </div>
</form>
    </div>
    </div>
<div class="box-element tab-pane" id="htmlblock">
                     <div class="box-head-light"><i class="icon-code"></i><h3>New html block</h3></div>
                    <div class="box-content">
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('homepage'); ?>" enctype="multipart/form-data" method="post">
    <input type="hidden" name="html-block" value="1">
    <div class="form-group form-material">
    <label class="control-label">Block title</label>
    <div class="controls">
    <input type="text" id="title" name="title" class="col-md-12" value="">
    </div>
    </div>    
    <div class="form-group form-material">
<label class="control-label">Html content</label>
<div class="controls">
<textarea rows="5" cols="5" name="html" class="col-md-12" style="word-wrap: break-word; resize: horizontal; min-height: 128px;"></textarea>                    
</div>    
</div>
 <div class="box-bottom clearfix"> <button class="btn btn-primary btn-mini pull-right">Add block</button>  </div>
</form>
    </div>
    </div>
     <div class="box-element tab-pane" id="pblock">
                     <div class="box-head-light"><i class="icon-mixcloud"></i><h3>New playlist content block</h3></div>
                    <div class="box-content">
<form id="validate" class="form-horizontal styled" action="<?php
echo admin_url('homepage');
?>" enctype="multipart/form-data" method="post">
        <input type="hidden" name="playlist-block" value="1">
    <div class="form-group form-material">
    <label class="control-label">Block title</label>
    <div class="controls">
    <input type="text" id="title" name="title" class="col-md-12" value="">
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label">Results limit</label>
    <div class="controls">
    <input type="text" id="number" name="number" class="col-md-4 validate[required]" value="24">
    <span class="help-block" id="limit-text">Number of items per this block.</span>
    </div>
    </div>    
    <div class="form-group form-material">
    <label class="control-label"><i class="icon-sort"></i>Carousel</label>
    <div class="controls">
    <label class="radio inline"><input type="radio" name="car" class="styled" value="1">Yes</label>
    <label class="radio inline"><input type="radio" name="car" class="styled" value="0" checked>No</label>

    </div>
    </div>
    <div class="form-group form-material">
    <label class="control-label">Playlist</label>
    <select data-placeholder="Select playlist" name="queries" id="queris" class="select validate[required]" tabindex="2">
    <?php
$p = $cachedb->get_results("select " . DB_PREFIX . "playlists.*, " . DB_PREFIX . "users.name as user from " . DB_PREFIX . "playlists LEFT JOIN " . DB_PREFIX . "users ON " . DB_PREFIX . "playlists.owner = " . DB_PREFIX . "users.id order by views DESC limit 0,1000");
$plx = array(
 "" => "v/m",
 "1" => "v/m",
 "2" => "pics"
);
if($p)
{
 foreach($p as $pl)
 {
?>
   <option value="<?php
  echo $pl->id;
?>"><?php
  echo '[ ' . $plx[$pl->ptype] . ' ]   ' . _html($pl->title);
?> by <?php
  echo _html($pl->user);
?> </option>
    <?php
 }
}
?>
   </select>
</div>
 <div class="box-bottom clearfix"> <button class="btn btn-primary btn-mini pull-right">Add block</button>  </div>
</form>
    </div>
    </div>
<?php
do_action('homepage-admin-end');
?>    
</div>
</div>
  <div class="col-md-5 col-md-offset-1 col-xs-12">    
 <div class="box-element">
<div class="box-head-light"><h3>Blocks</h3></div>
<div class="box-content">    
 <div id="easyhome">
<ul id="sortable" class="droptrue">
<?php
$boxes_sql = $db->get_results("SELECT * FROM " . DB_PREFIX . "homepage order by `ord` ASC limit 0,1000000");
if($boxes_sql)
{
 $bt = array(
  "" => "Media",
  "2" => "Media",
  "1" => "Html",
  "3" => "Playlist content",
  "4" => "Channel content",
  "6" => "Channels",
  "7" => "Playlists"
 );
 foreach($boxes_sql as $box)
 {
  echo '
<li id="recordsArray_' . $box->id . '" class="sortable clearfix">
<div class="ns-row pull-left">
<div class="ns-title">
<i class="icon-sort" style="margin-right:8px;"></i>
' . _html($box->title) . ' <p><em style="font-size:9px">[' . $bt[$box->type] . ']</em></p>
</div>
<div class="btn-group pull-right">';
  if($box->type == 2)
  {
   echo '<a href="' . admin_url('edit-block') . '&id=' . $box->id . '" class="tipS delete-menu pull-right btn btn-pure btn-sm btn-primary" title="Edit" ><i class="icon-pencil"></i></a>';
  }
  echo '<a href="' . admin_url('homepage') . '&delete=' . $box->id . '" class="tipS delete-menu pull-right btn btn-pure btn-sm btn-danger confirm" style="margin:0 5px;" title="Delete"><i class="icon-trash"></i></a>
</div>
</div>
</li>';
 }
}
?>
 </ul>
 <div id="respo" style="display:none;"></div>    
</div>    

                </div>    
</div>
 
        </div>
		</div>
 
        </div>