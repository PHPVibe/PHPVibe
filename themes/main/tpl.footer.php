<?php /* MediaVibe's footer */
function footer_add(){
global $db, $cachedb,$next;
$footer =  '
</div>
</div>
</div>
</div>
<a href="#" id="linkTop" class="backtotop"><i class="material-icons">arrow_drop_up</i></a>
<div id="footer" class="row block full oboxed">';
$footer .='<div class="row footer-holder">
<div class="container footer-inner">
<div class="row">
<div class="col-md-12 text-center">
<div class="row row-socials">
';
if(is_home()){
$footer .='
<ul class="socialfooter">';
 if(not_empty(get_option("our_facebook", "#"))) {
  $footer .= '<li class="facebook">
    <a rel="nofollow" class="tipS" href="'.get_option("our_facebook").'" target="_blank" title="'._lang("Facebook").'"></a>
    </a>
  </li>';
  }
if(not_empty(get_option("our_googleplus", "#"))) {
  $footer .= '<li class="googleplus">
    <a rel="nofollow" class="tipS" href="'.get_option("our_googleplus").'" target="_blank" title="'._lang("Google Plus").'"></a>
    </a>
  </li>';
  }
  if(not_empty(get_option("our_youtube", "#"))) {
  $footer .= '<li class="youtube">
    <a rel="nofollow" class="tipS" href="'.get_option("our_youtube").'" target="_blank" title="'._lang("Youtube").'"></a>
    </a>
  </li>';
  }
if(not_empty(get_option("our_pinterest", "#"))) {
  $footer .= '<li class="pinterest">
    <a rel="nofollow" class="tipS" href="'.get_option("our_pinterest").'" target="_blank" title="'._lang("Pinterest").'"></a>
    </a>
  </li>';
  }
if(not_empty(get_option("our_twitter", "#"))) {
  $footer .= '<li class="twitter">
    <a rel="nofollow" class="tipS" href="'.get_option("our_twitter").'" target="_blank" title="'._lang("Twitter").'"></a>
    </a>
  </li>';
  }
  if(not_empty(get_option("our_rss", "#"))) {
  $footer .= '<li class="rss">
    <a rel="nofollow" class="tipS" href="'.get_option("our_rss").'" target="_blank" title="'._lang("Feedburner").'"></a>
    </a>
  </li>';
  }
if(not_empty(get_option("our_skype", "#"))) {
  $footer .= '<li class="skype">
    <a rel="nofollow" class="tipS" href="'.get_option("our_skype").'" target="_blank" title="'._lang("Skype").'"></a>
    </a>
  </li>';
  }
if(not_empty(get_option("our_vimeo", "#"))) {
  $footer .= '<li class="vimeo">
    <a rel="nofollow" class="tipS" href="'.get_option("our_vimeo").'" target="_blank" title="'._lang("Vimeo").'"></a>
    </a>
  </li>';
  }
 if(not_empty(get_option("our_dribbble", "#"))) {
  $footer .= '<li class="dribbble">
    <a rel="nofollow" class="tipS" href="'.get_option("our_dribbble").'" target="_blank" title="'._lang("Dribbble").'"></a>
    </a>
  </li>';
  }
if(not_empty(get_option("our_flickr", "#"))) {
  $footer .= '<li class="flickr">
    <a rel="nofollow" class="tipS" href="'.get_option("our_flickr").'" target="_blank" title="'._lang("Flickr").'"></a>
    </a>
  </li>';
  }
 if(not_empty(get_option("our_linkedin", "#"))) {
  $footer .= '<li class="linkedin">
    <a rel="nofollow" class="tipS" href="'.get_option("our_linkedin").'" target="_blank" title="'._lang("Linked in").'"></a>
    </a>
  </li>';
  }
$footer .='</ul>
';
}
$footer .= '</div>
<div class="mouse-cursor cursor-outer"></div>
    <div class="mouse-cursor cursor-inner"></div>
<div class="row row-rights">
'.site_copy().'
</div>
</div>
</div>
';
$footer .='</div>
</div>
</div>
</div>

'.login_modal().'
<!-- Start Search Modal -->
<div class="modal fade" id="search-now" aria-hidden="true" data-backdrop="false" aria-labelledby="search-now" role="dialog" tabindex="-1">
<div class="modal-dialog modal-sidebar modal-searcher">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">×</span>
</button>
</div>
<div class="modal-body">
<div class="panel panel-transparent">
<div class="row search-now-clone">


</div>
</div>
</div>
</div>
</div>
</div>
<!-- End Search Modal -->
<script type="text/javascript">
var site_url = \''.site_url().'\';
var nv_lang = \''._lang("Next video starting soon").'\';
var select2choice = \''._lang("Select option").'\';
var delete_com_text = \''._lang("Delete this comment?").'\';
</script>
<script type="text/javascript" src="'.site_url().'app/minify/js.php?t='.THEME.'&sign=phpvibe.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId='.Fb_Key.'";
fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>
'.extra_js().'
'._pjs(get_option('googletracking')).'
</body>
</html>';
return $footer;
}
function login_modal(){
if(is_user()) {return '';}
$socials = '';
if(get_option('allowfb',0) == 1 ) {
$socials .= '<div class="social-login fb-login">
<a href="'.site_url().'?action=login&type=facebook" class="btn btn-block btn-raised social-facebook"><i class="icon jssico-facebook bd-facebook"></i><em> '._lang("Login with").'</em> '._lang("Facebook").'</a>
</div>';
}
if(get_option('allowg',0) == 1 ) {
$socials .= '<div class="social-login google-login">
<a href="'.site_url().'?action=login&type=google" class="btn btn-block social-google-plus"><i class="icon jssico-google-plus bd-google-plus"></i><em> '._lang("Login with").'</em> '._lang("Google Plus").'</a>
</div>';
}
$lg = '<!-- Start Login Modal -->
<div class="modal fade" id="login-now" aria-hidden="true" aria-labelledby="login-now" role="dialog" tabindex="-1">
<div class="modal-dialog modal-sidebar modal-sm">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">×</span>
</button>
<div class="text-center">
<h4 class="modal-title">'._lang('Login').'</h4>
<div class="blc mtop20 odet fs300">'._lang('Share videos, music and pictures, follow friends and keep track of what you enjoy!').'</div>
</div>
</div>
<div class="modal-body">
<div class="panel">
<div class="row">'.$socials.'
<form method="post" action="'.site_url().'login" class="mtop10 modal-form">
<div class="form-group form-material floating">
<input type="email" autocomplete="email" class="form-control" name="email" required data-error="'._lang("Your e-mail must be valid.").'" />
<label class="floating-label">'._lang("Email").'</label>
</div>
<div class="form-group form-material floating">
<input type="password" autocomplete="current-password" class="form-control" name="password" required />
<label class="floating-label">'._lang("Password").'</label>
</div>
<div class="form-group clearfix">
<div class="checkbox-custom checkbox-inline checkbox-primary checkbox-lg pull-left">
<input type="checkbox" id="inputCheckbox" name="remember" checked=checked>
<label for="inputCheckbox">'._lang("Remember me").'</label>
</div>
<a class="pull-right" data-target="#forgot-pass" data-toggle="modal" href="javascript:void(0)">'._lang("Forgot password?").'</a>
</div>
<button type="submit" class="btn btn-primary btn-block mtop20">'._lang("Sign In").'</button>
</form>
<p class="mtop10">'._lang("Still no account? Please go to").' <a href="'.site_url().'register">'._lang("Sign up").'</a></p>
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default btn-block" data-dismiss="modal">'._lang("Close").'</button>


</div>
</div>
</div>
</div>
<!-- End Login Modal -->
<!-- Start Recover Modal -->
<div class="modal fade" id="forgot-pass" aria-hidden="true" aria-labelledby="forgot-pass" role="dialog" tabindex="-1">
<div class="modal-dialog modal-sidebar modal-sm">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">×</span>
</button>
<h4 class="modal-title">'._lang('Forgotten password').'</h4>
</div>
<div class="modal-body">
<div class="panel">
<div class="row">
<form method="post" action="'.site_url().'login" class="modal-form">
<div class="form-group form-material floating">
<input type="hidden" name="forgot-pass" value="1"/>
<input type="email" class="form-control" name="remail" data-error="'._lang("Your e-mail must be valid.").'" required/>
<label class="floating-label">'._lang("Your e-mail").'</label>
</div>
<button type="submit" class="btn btn-primary btn-block mtop20">'._lang("Recover now").'</button>
</form>
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default btn-block" data-dismiss="modal">'._lang("Close").'</button>
</div>
</div>
</div>
</div>
<!-- End Recover Modal -->

';


return $lg;
}

?>