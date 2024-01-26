<?php  include_once(CNC . '/ImageResize.php');
use \Gumlet\ImageResize;

if (!is_user()) {
    redirect(site_url() . 'login/');
}
$error = '';
@ini_set('post_max_size', '64M');
@ini_set('upload_max_filesize', '64M');
// SEO Filters
if (isset($_POST['pic-title'])) {
//var_dump($_FILES);
    $savePath = ABSPATH . '/storage/' . get_option('mediafolder') . '/';                                # The folder to save the image
    $saveName = md5(time()) . '-' . user_id();                                    # Without ext
    $allowedExtArray = explode(',', get_option('alimgext', 'jpg,png,gif,jpeg,bmp'));
    $imageQuality = 100;

    if (!is_insecure_file($_FILES['play-img']['name'])) {
        $ext = substr($_FILES['play-img']['name'], strrpos($_FILES['play-img']['name'], '.') + 1);
        if (in_array($ext, $allowedExtArray)) {
            if (move_uploaded_file($_FILES['play-img']['tmp_name'], $savePath . $saveName . '.' . $ext)) {
                $thumb = $savePath . $saveName . '.' . $ext;
                $thumb = str_replace(ABSPATH . '/', '', $thumb);
                $source = str_replace('storage/' . get_option('mediafolder'), '', $thumb);
                $source  = ltrim( $source , '/');
                //Thumnails generator

                $thumbfile = $savePath .'thumb_' .$source ;
                $original = $savePath . $saveName . '.' . $ext;
                $resizer = new ImageResize($original);
                $resizer->quality_jpg = 95;
                $resizer->resizeToWidth(460);
                $resizer->save($thumbfile);
//Do the sql insert
                $db->query("INSERT INTO " . DB_PREFIX . "images (`privacy`,`media`,`category`, `pub`,`source`, `user_id`, `date`,  `title`, `tags` , `views` , `liked` , `description`, `nsfw`) VALUES 
('" . intval(_post('priv')) . "','3','" . intval(_post('categ')) . "','" . intval(get_option('videos-initial')) . "','" . $source . "', '" . user_id() . "', now() , '" . toDb(_post('pic-title')) . "',  '" . toDb(_post('tags')) . "', '0', '0','" . toDb(_post('pic-desc')) . "','" . toDb(_post('nsfw')) . "')");
                $doit = $db->get_row("SELECT id from " . DB_PREFIX . "images where user_id = '" . user_id() . "' order by id DESC limit 0,1");
                add_activity('8', $doit->id);
                $error .= '<div class="msg-info mleft20 mtop20 mright20">' . _post('pic-title') . ' ' . _lang("created successfully.") . ' <a href="' . site_url() . me . '" class="btn btn-primary btn-xs pull-right" style="color:#fff">' . _lang("Manage media.") . '</a></div>';
                if (get_option('videos-initial') <> 1) {
                    $error .= '<div class="msg-info mtop20 mright20">' . _lang("Image requires admin approval before going live.") . '</div>';

                }
//$db->clean_cache();
            } else {
                $error .= '<div class="msg-info mtop20 mright20">' . _lang("Upload failed. Check your image!") . '</div>';

            }
        }
    } else {
        $error .= '<div class="msg-info mtop20 mright20">' . _lang("Insecure file detected. Upload canceled.") . '</div>';

    }
}
function modify_title($text)
{
    return strip_tags(stripslashes($text . ' ' . _lang('share')));
}

$token = md5(user_name() . user_id() . time());
function file_up_support($text)
{
    $text = '<script type="text/javascript" >
function JustCapitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();            
            reader.onload = function (e) {
                $(\'#targetImg\').attr(\'src\', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $("#imgInp").change(function(e){
        readURL(this);
		$( ".Pimg" ).slideDown();
		var imgname  = e.target.files[0].name.replace(/(.*)\.[^.]+$/, \'$1\');
		var punctRE = /[\u2000-\u206F\u2E00-\u2E7F\\\'!"#$%&()*+,\-.\/:;<=>?@\[\]^_`{|}~]/g;
		fname = imgname.replace(punctRE, \' \');
		fname = JustCapitalize(fname);
		$("#title").val(fname);
		$("#description").val(fname);
		var arrfname = fname.replace(punctRE, \',\').toLowerCase();
		$("#tags").importTags(arrfname);
    });

  </script>

';
    return $text;
}

add_filter('filter_extrajs', 'file_up_support');
function modify_content($text)
{
    global $error, $token, $db;
    $data = $error . '
   <div class="clearfix vibe-upload isBoxed row block">			
	<div class=" mright20 mleft20 mtop20 mbot20">
    <div id="formVid" class="nomargin ">
	<form id="validate" action="' . canonical() . '" enctype="multipart/form-data" method="post">
	<input type="hidden" name="vfile" id="vfile"/>	
	<input type="hidden" name="vup" id="vup" value="1"/>	
	<input type="hidden" name="vtoken" id="vtoken" value="' . $token . '"/>
	<div class="row">
	<div class="col-md-6">
	<div class="form-group form-material">
<label class="control-label" for="inputFile">' . _lang("Choose the picture:") . '</label>
<input type="text" class="form-control" placeholder="' . _lang("Browse for image...") . '" readonly="" />
<input type="file" id="imgInp" name="play-img" required=""/>
    </div>
    </div>
    <div class="col-md-5 col-md-offset-1">
	<div class="Pimg" style="display:none;">
	<div class="noted label-success"><i class="icon-ok"></i> </div>
        <img id="targetImg" src="#" />
	</div>	
	</div>	
	</div>	
	<div class="control-group">
	<label class="control-label">' . _lang("Title:") . '</label>
	<div class="controls">
	<input type="text" id="title" data-error="' . _lang("Add a title for people to find this") . '" name="pic-title" class="form-control col-md-12" required value="">
	<div class="help-block with-errors"></div>
	</div>	
	</div>';
    $data .= '
	<div class="control-group mtop20">
	<label class="control-label">' . _lang("Category:") . '</label>
	<div class="controls">
	' . cats_select('categ', "select", "", "3") . '
	  </div>             
	  </div>
	<div class="control-group mtop20">
	<label class="control-label">' . _lang("Description:") . '</label>
	<div class="controls">
	<textarea id="description" name="pic-desc" class="validate[required] form-control col-md-12 auto"></textarea>
	</div>
	</div>
<div class="control-group mtop20 row">
	<div class="form-group mtop20">
	<div class="input-group">
    <span class="input-group-addon">' . _lang("Tags:") . '</span>
	<div class="form-control withtags">
	<input type="text" id="tags" name="tags" class="tags form-control" value="">
	</div>
	</div>
	</div>
	</div>
	<div class="control-group">
	<label class="control-label">' . _lang("NSFW:") . '</label>
	<div class="controls row">
	<div class="radio-custom radio-primary col-md-4">
	<input type="radio" name="nsfw" value="1">
	<label> ' . _lang("Not safe") . ' </label>
	</div>
	<div class="radio-custom radio-primary col-md-4">
	<input type="radio" name="nsfw" value="0" checked>
	<label >' . _lang("Safe") . '</label>
	</div>
	</div>
	</div>
	<div class="control-group">
	<label class="control-label">' . _lang("Privacy:") . ' </label>
	<div class="controls row">
	<div class="radio-custom radio-primary col-md-4">
	<input type="radio" name="priv" value="1">
	<label> ' . _lang("Followers only") . ' </label>
	</div>
	<div class="radio-custom radio-primary col-md-4">
	<input type="radio" name="priv" value="0" checked>
	<label>' . _lang("Public") . ' </label>
	</div>
	</div>
	</div>
	
	<div class="control-group">
	<button id="Subtn" class="btn btn-primary pull-right" type="submit">' . _lang("Upload") . '</button>
	</div>
	</form>
	</div>
	
	</div>
	</div>
	</div>
';
    return $data;
}

add_filter('phpvibe_title', 'modify_title');

if ((get_option('uploadrule') == 1) || is_moderator()) {
    add_filter('the_defaults', 'modify_content');
} else {
    function udisabled()
    {
        return _lang("This uploading section is disabled");
    }

    add_filter('the_defaults', 'udisabled');
}
//Time for design
the_header();
include_once(TPL . '/sharemedia.php');
the_footer();
?>
