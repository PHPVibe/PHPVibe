<?php
if(isset($_POST['update_options_now'])){
foreach($_POST as $key=>$value)
{
update_option($key, $value);
}
$db->clean_cache();
  echo '<div class="msg-win">FFMPEG options have been updated.</div>';
}
$all_options = get_all_options();
include_once('setheader.php');
?>
<div class="row">

<div class="row-setts panel-body">
<h3>FFMPEG Settings</h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('ffmpeg');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="update_options_now" class="hide" value="1" /> 

	<div class="form-group form-material">
	<label class="control-label"><i class="icon-check"></i>Enable ffmpeg conversion</label>
	<div class="controls">
	<label class="radio inline"><input type="radio" name="ffa" class="styled" value="1" <?php if(get_option('ffa') == 1 ) { echo "checked"; } ?>>Yes</label>
	<label class="radio inline"><input type="radio" name="ffa" class="styled" value="0" <?php if(get_option('ffa') == 0 ) { echo "checked"; } ?>>No</label>
	<span class="help-block" id="limit-text"><code>Please</code> make sure you have FFMPEG installed on server and <code>tested working</code> before enabling this</span>
	</div>
	</div>

<h3>Server paths</h3>
<div class="row">
<div class="col-md-6 col-xs-12">
<div class="form-group form-material">
<label class="control-label"><i class="icon-magic"></i>FFmpeg executable</label>
 <div class="controls">
<input type="text" name="ffmpeg-cmd" class="col-md-12" value="<?php echo get_option('ffmpeg-cmd','ffmpeg'); ?>">
<span class="help-block">FFMPEG comand to run, ex: <code>ffmpeg</code>, <code>usr/bin/ffmpeg</code>. Make sure it works. </span>
</div>
</div>
</div>
<div class="col-md-6 col-xs-12">
<div class="form-group form-material">
	<label class="control-label"><i class="icon-wrench"></i>Server bin path</label>
	<div class="controls">
	<input type="text" name="binpath" class=" col-md-6" value="<?php echo get_option('binpath','/usr/bin/php'); ?>" />
	<span class="help-block" id="limit-text">PHP Bin path for ffmpeg conversion tasks. Ex: <code>/usr/bin/php</code> <em>Note: Also make sure videocron.php has execute permissions (chmod : 0555)</em></span>
	</div>
	</div>
</div>
</div>	
<h3>Video qualities</h3>
 <div class="form-group form-material">
	<label class="control-label"><i class="icon-resize-full"></i>Enabled qualities:</label>
	<div class="controls">
	<input type="text" id="tags" name="ffmeg-qualities" class="tags col-md-12" value="<?php echo get_option('ffmeg-qualities','144, 360,720'); ?>">
	</div>
	<span class="help-block" id="limit-text"><code style="color:red">Warning!!!</code> Do not be greedy. Converting to multiple qualities and high qualities with FFMPEG can easy put down a good dedicated. <code>Youtube uses 360p</code> for 70% of their traffic.  </span>
	<span class="help-block" id="limit-text"><code style="color:red">Numbers only!</code> Ex: <code>240</code> for 240p (Reffers to 240px height). </strong>Press Enter after each quality</strong>	</span>
	<span class="help-block" id="limit-text"><code style="color:red">240p and 480p</code> cannot be converted with <code>-vf scale -1:</code> dues to FFMPEG limitations. </strong>Use the actual width size instead of -1</strong> 240 : <code>426×240</code> 480 : <code>854×480</code>	</span>
	</div>

<h3>Video qualities conversion calls</h3>
<p><code>{ffmpeg-cmd}</code> is being replaced with the actual ffmpeg executable.
<code>%sizer%</code> is being replaced with the height size for that quality.
<code>{output}</code> is being replaced with the video token.
<code>{input}</code> is being replaced with the uploaded video.
</p>
<?php $fftheme ="{ffmpeg-cmd} -i {input} -vf scale=-1:%sizer% -c:v libx264 -preset veryfast -crf 28 -threads 2 -movflags faststart {output}-%sizer%.mp4 2>&1";
$enabledq = array();
$enabledq = @explode(',', get_option('ffmeg-qualities','144, 360,720'));
$commonq = array(240,360,480,720,1080,1440,2160);
$qualities = array_unique(array_merge($enabledq,$commonq), SORT_NUMERIC);
sort($qualities);
foreach ($qualities as $call) {
$output = get_option('fftheme-'.$call,str_replace('%sizer%',$call,$fftheme)); 	
?>
<div class="form-group form-material">
<label class="control-label"><i class="icon-youtube-play"></i><?php echo $call; ?></label>
<div class="controls">
<input type="text" name="fftheme-<?php echo $call; ?>" class="col-md-12" value="<?php echo get_option('fftheme-'.$call, $output); ?>" /> 
<span class="help-block" id="limit-text">Ffmpeg command used on videos if <code><?php echo $call; ?></code> is enabled in qualities.</span>						
</div>	
</div>	
<?php } ?>

<div class="row page-footer">
<button class="btn btn-large btn-primary pull-right" type="submit"><?php echo _lang("Update settings"); ?></button>	
</div>	
</fieldset>						
</form>
</div>
</div>

<div class="container-fluid ">
<div class="row row-setts">
<h3>Helpers</h3>
<div class="row">
<section class="panel col-md-6">
<div class="panel-body">
<p>The <a target="_blank" href="http://ffmpeg.org/ffmpeg-filters.html#scale">scale video filter</a> is for resizing the video. You just set one size – which is the height in this example – and use <code>-1</code> for the other dimension. Ffmpeg will recalculate the correct value automatically while preserving the aspect ratio.</p>
<p>Quality controlled with the <code>-crf</code> option: </p>
<pre>
 The range of the quantizer scale is 0-51: where 0 is lossless, 23 is
  default, and 51 is worst possible. A lower value is a higher quality
  and a subjectively sane range is 18-28. Consider 18 to be visually
  lossless or nearly so: it should look the same or nearly the same as
  the input but it isn't technically lossless.
  <br>The range is exponential, so increasing the CRF value +6 is roughly
  half the bitrate while -6 is roughly twice the bitrate.
  <br>General usage is to choose the highest CRF value that still provides an acceptable
  quality. If the output looks good, then try a higher value and if it
  looks bad then choose a lower value.
</pre>
<p>More info in the <a target="_blank" href="http://trac.ffmpeg.org/wiki/x264EncodingGuide">x264 encoding guide</a>.</p>
<p>You can control the tradeoff between video encoding speed and compression efficiency with the <code>-preset</code> options. Those are <code>ultrafast, superfast, veryfast, faster, fast, medium, slow, slower, veryslow</code>. Default is <strong>medium</strong>. The <code>veryslow</code> option offers the best compression efficiency (resulting in a smaller file size for the same quality) but it is very slow – as the name says.</p>
</div>	
</section>
<section class="panel col-md-5 pull-right">
<div class="panel-body">
<h3>Need more tips?</h3>
<p>We found this interesting:</p>
<p><a href="https://ffmpeg.org/ffmpeg.html" target="_blank">FFMPEG's official documentation</a></p>
<p><a href="https://trac.ffmpeg.org/wiki/Encode/H.264" target="_blank">FFmpeg and H.264 Encoding Guide</a></p>
<p><a href="https://support.google.com/youtube/answer/6375112" target="_blank">Youtube recommended resolution & aspect ratios</a></p>
<p><a href="http://rodrigopolo.com/ffmpeg/cheats.php#X264_Presets" target="_blank">FFMPEG cheats</a></p>

<p>Some ffmpeg samples</p>
<strong>Default:</strong> 
<pre><code class="html"><?php echo $fftheme; ?></code></pre>
<p>Restricts videos to original upload size.
<strong>Kept proportions:</strong> 
<pre><code class="html">{ffmpeg-cmd} -i {input} -c:v libx264 -preset slow -crf 28 -vf yadif -strict -2 -movflags faststart {output}.mp4</code></pre>
<p>Keeps the input videos sizes (no scaling, no resizing). You can edit -crf 28 (<a href="http://slhck.info/articles/crf" target="_blank">Constant Rate Factor</a>) to a lower value <span class="redText">(bigger & quality videos,big duration and server load )</span>.
This is pretty server heavy!!! But will keep HD in HD. <a href="http://www.phpvibe.com/forum/tutorials/ffmpeg-transcoding-for-quality/" target="_blank">Read more</a></p>
</div>	
</section>
</div>
<h3>Ffmpeg & bin path info from your server</h3>
<?php
if(function_exists('exec')) {
echo "<div class=\"control-group\"><p>Attempting a 'which php' command:</p><pre><code class=\"html hljs xml\">";
echo exec('which php');
echo "</code></pre></div>"; 
echo "<div class=\"control-group\"><p>Attempting a 'which ffmpeg' command:</p><pre><code class=\"html hljs xml\">";
echo exec('which ffmpeg');
echo "</code></pre></div>";
echo "<div class=\"msg-info\">This values are not 100% reliable. Please check them with your hosting.</div>";
echo "<div class=\"msg-info\">Newer versions of FFMPEG use the command <strong>ffmpeg</strong> in most cases.</div>";

} else {
echo "<div class=\"msg-warning\">Exec is disabled</div>";	
}
?>
</div>

</div>