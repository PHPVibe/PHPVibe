<?php $error='';
 if(is_user()){
  redirect(site_url().'dashboard/');	  
  }
/** Login via mail request **/

if(_get('do') && (_get('do') == "autologin") && _get('m') && _get('k')) {
$mail = base64_decode(_get('m'));
$key = base64_decode(_get('k'));
if(!_contains($key,'banned')) {
$check = user::CheckMail($mail);
if($check > 0 ) {
$result = $db->get_row("SELECT id FROM ".DB_PREFIX."users WHERE email ='" . toDb($mail) . "' and pass = '" . toDb($key) . "'");
if($result && $result->id) {
user::LoginUser($result->id);
		if (is_user()) { 
		$_SESSION['loggedfrommail'] = 1;
		redirect(site_url().me.'?sk=edit');
		} else {
		$error = '<div class="msg-warning mleft20 mright20 mtop20">'._lang('Something went wrong. Try refreshing this page').'</div>';
		}
} else {
$error = '<div class="msg-warning mleft20 mright20 mtop20">'._lang('Something went wrong. Wrong credentials').'</div>';
}
} else {
$error = '<div class="msg-warning">'._lang('Something went wrong. That email is wrong').'</div>';
}
} else {
$error = '<div class="msg-warning mleft20 mright20 mtop20">'._lang('This account is banned for infringing our rules!').'</div>';

}	
}
 
/** Actual login **/
if(_post('password') && _post('email')){
if(user::loginbymail(_post('email'),_post('password') )) {
if(_get('return')) {
redirect(site_url()._get('return').'/');
} else {
redirect(site_url().'dashboard/');
}
} else {
if(user::UserIsBanned(_post('email'))) {	
$error = '<div class="msg-warning mleft20 mright20 mtop20">'._lang('This account is banned for infringing our rules!').'</div>';
} else {
$error = '<div class="msg-warning mleft20 mright20 mtop20">'._lang('Wrong username or password.').'</div>';
}
}
}
/** New password request / Activation request **/
if((_post('forgot-pass') || _get('verifyemail')) && _post('remail')){
$check = user::CheckMail(_post('remail'));
if($check > 0 ) {
$omail = toDb(_post('remail'));
$result = $db->get_row("SELECT pass, name FROM ".DB_PREFIX."users WHERE email ='" . toDb($omail) . "'");
if($result) {
$key = base64_encode($result->pass);
$link = site_url().'login?do=autologin&m='.base64_encode($omail).'&k='.$key;
if(_get('verifyemail')) {
$message = _lang('In order to activate your account please follow this link:');
} else {
$message = _lang('In order to change your password please follow this link:');	
}
$message .= '<br /> <a href="##link##">##link##</a> <br />';
$message .= _lang('Regards, Webmaster');
$message .= '<br> '.site_url();
$message = str_replace("##link##",$link,$message);	
//echo $link;
$mail = new PHPMailer;
if(isset($mvm_useSMTP) && $mvm_useSMTP  ) {
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = $mvm_host;  
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = $mvm_user;                 
$mail->Password = $mvm_pass;                           // SMTP password
$mail->SMTPSecure = $mvm_secure;                            
$mail->Port = $mvm_port;                                    
}
if(isset($adminMail) && not_empty($adminMail)) {
$mail->From = $adminMail;
} else {
$mail->From = "noreply@".ltrim(cookiedomain(),".");	
}
$mail->FromName = get_option('site_logo-text');	
$mail->addAddress($omail, toDb($result->name));
$mail->WordWrap = 50;  
if(_get('verifyemail')) {
$mail->Subject = $result->name.' '._lang('Activate your account');	
} else {
$mail->Subject = _lang('Password change for'). ' '.$result->name;
}
$mail->Body    = $message;
$mail->AltBody = $message;
if(!$mail->send()) {
$error = '<div class="msg-warning mleft20 mright20 mtop20">'.toDb(_post('remail')).' '._lang('Message could not be sent.').$mail->ErrorInfo.'</div>';
} else {
$error = '<div class="msg-note mleft20 mright20 mtop20">'._lang('An e-mail has been sent to your account. Please also check the "spam" folder.').'</div>';					
}
}
} else {
$error = '<div class="msg-warning mleft20 mright20 mtop20">'.toDb(_post('remail')).' '._lang('is not registered to any account.').'</div>';
}
}

// SEO Filters
function modify_title( $text ) {
 return strip_tags(stripslashes($text.' '._lang('login')));
}
$socials = '';
if(get_option('allowfb') == 1 ) {
$socials .= '<p><a href="'.site_url().'?action=login&type=facebook" class="btn btn-block btn-labeled  social-facebook"><span class="btn-label"><i class="icon icon-facebook" aria-hidden="true"></i></span>'._lang("Signin with <strong>Facebook</strong>").'</a></p>';	
}
if(get_option('allowg') == 1 ) {
$socials .= '<p><a href="'.site_url().'?action=login&type=google" class="btn btn-block btn-labeled  social-google-plus"><span class="btn-label"><i class="icon icon-google-plus" aria-hidden="true"></i></span>'._lang("Signin with <strong>Google</strong>").'</a></p>';
}

if(get_option('allowlocalreg') == 1 ) {
$registerhere = '<a href="'.site_url().'register"><strong>'._lang('register here').'</strong></a>';
} else {
$registerhere = _lang("by choosing a social service you use.");	
}
function modify_content( $text ) {
global $error, $socials,$registerhere;
if(_get('return')) { $rt ='&'._get('return'); } else { $rt = '';}; 
return $error.'
<div class="row text-center clearfix odet mbot20 mtop20">
'.((_get("justlogin") ? _lang("<h1>Thank you for joining!</h1>") : "")).'
<h2>'._lang("Login now").'</h2>
'._lang("Sign in to your account").' 
 <a class="btn btn-primary btn-block mtop20" href="javascript:showLogin()">'._lang("Show login box").'</a>
</div>
';
}
function forgot_content() {
global $error;
$html = $error.'
<div id="validate" class="form-signin">
		
	
		<div class="row clearfix" style="padding:50px 40px;">
	<div class="col-md-12 block">';
	if(_get('verifyemail')) {
			$html .= '<h3>'._lang("Resend activation").'</h3> '._lang("Please activate your account via provided email!").' <p>'._lang("If the email doesn't arrive in your inbox, please also check the spam folders.").'</p>';
	} else {
			$html .= '<h3>'._lang("Recover password").'</h3> '._lang("Recover your password by e-mail. If you\'ve used an social service, just click on the button for that service on the login page.");
	
	                        }
				$html .= '				
				</div>
				<div class="col-md-12 block text-center mtop20 mbot20">
				<div class="inner signin-with">
					<form class="styled" action="'.canonical().'" enctype="multipart/form-data" method="post">
					<input type="hidden" name="forgot-pass" value="1"/>	
					<input type="email" name="remail" required class="form-control" placeholder="'._lang("Email address").'"'; 
					if(_get('verifyemail') && _get('mail')) {
					$html .= 'value="'._get('mail').'"';	
					}
					$html .= '> 
						<div class="row text-center mtop20 mbot20">';
						if(_get('verifyemail')) {
							$html .= '	<button class="btn btn-large btn-primary" type="submit">'._lang("Resend verification").'</button>';
						} else {
						$html .= '	<button class="btn btn-large btn-primary" type="submit">'._lang("Recover").'</button>';
						}
						$html .= '</div>
						
					</form>
		
				</div>
			</div>
			
			
		
      </div>
	</div>';
return $html;	
}
add_filter( 'phpvibe_title', 'modify_title' );
if((_get('do') && (_get('do') == "forgot")) || _get('verifyemail')) {
add_filter( 'the_defaults', 'forgot_content' );
} else {
add_filter( 'the_defaults', 'modify_content' );
}
//Time for design
 the_header();
include_once(TPL.'/default-full.php');
the_footer();
?>
