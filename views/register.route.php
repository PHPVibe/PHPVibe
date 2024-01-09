<?php if (is_user()) {
    redirect(site_url() . 'dashboard/');
}
$error = '';

//If submited
if (get_option('allowlocalreg') == 1) {
    if (_post('name') && _post('password') && _post('email')) {

        if (filter_var(_post('email'), FILTER_VALIDATE_EMAIL)) {

            if (_post('password') == _post('password2')) {
                $avatar = 'uploads/def-avatar.jpg';
                if (isset($_FILES['avatar']) && $_FILES['avatar']) {
                    $formInputName = 'avatar';                            # This is the name given to the form's file input
                    $savePath = ABSPATH . '/storage/uploads';                                # The folder to save the image
                    $saveName = md5(time()) . '-' . user_id();                                    # Without ext
                    $allowedExtArray = array('.jpg', '.png', '.gif');    # Set allowed file types
                    $imageQuality = 100;
                    $uploader = new FileUploader($formInputName, $savePath, $saveName, $allowedExtArray);
                    if ($uploader->getIsSuccessful()) {
                        $uploader->resizeImage(180, 180, 'crop');
                        $uploader->saveImage($uploader->getTargetPath(), $imageQuality);
                        $thumb = $uploader->getTargetPath();
                        $avatar = str_replace(ABSPATH . '/', '', $thumb);
                    }
                }
                if (user::CheckMail(_post('email')) < 1) {
                    $keys_values = array("passKey" => 'uinactive-' . md5(uniqid()),
                        "avatar" => $avatar,
                        "local" => _post('city'),
                        "country" => _post('country'),
                        "name" => _post('name'),
                        "email" => _post('email'),
                        "password" => sha1(_post('password')),
                        "type" => "core");
                    $id = user::AddUser($keys_values);

                    if (user::CheckMail(_post('email')) > 0) {
                        redirect(site_url() . 'login/?verifyemail=1&mail=' . urlencode(_post('email')));
                    } else {
                        $error = '<div class="msg-warning">' . _lang('Something went wrong, try again!') . '</div>';
                    }

                } else {
                    $error = '<div class="msg-warning">' . _lang('Email already in use') . '</div>';
                }

            } else {
                $error = '<div class="msg-warning">' . _lang('Passwords are not the same') . '</div>';
            }

        } else {
            $error = '<div class="msg-warning">' . _lang('Invalid e-mail detected.') . '</div>';
        }
        if (is_user()) {
            redirect(site_url() . 'dashboard/');
        }


    }
}
//if (is_user()) { redirect(site_url().me);}
if (get_option('allowlocalreg') == 0) {
    redirect(site_url());
}

// SEO Filters
function modify_title($text)
{
    return strip_tags(stripslashes($text . ' ' . _lang('registration')));
}

function modify_content($text)
{
    global $error, $captcha, $socials;

    $lg =$error . '
<div class="row text-center clearfix odet mbot20 mtop20">
<h2>' . _lang("Register") . '</h2>
'._lang("Thank you for choosing to register, just one step...").' 
</div>
<div class="panel">
<div class="panel-body">
<div class="col-md-6 col-md-offset-3">'.$socials;
if(get_option('allowlocalreg') == 1 ) {
$lg .= '
<form method="post" action="'.site_url().'register" class="mtop10 modal-form">
<div class="form-group form-material floating">
<input type="name" class="form-control" name="name" required/>
<label class="floating-label">'._lang("Your name").'</label>
</div>
<div class="form-group form-material floating">
<input type="email" class="form-control" name="email" required/>
<label class="floating-label">'._lang("Email").'</label>
</div>

<div class="form-group form-material floating">
<input type="password" id="password1" class="form-control" name="password" required/>
<label class="floating-label">'._lang("Password").'</label>
</div>
<div class="form-group form-material floating">
<input type="password" class="form-control" name="password2" data-match="#password1" data-match-error="'._lang("Passwords do not match").'" required/>
<label class="floating-label">'._lang("Repeat password").'</label>
<div class="help-block with-errors"></div>
</div>';

$lg .= '<button type="submit" class="btn btn-primary mtop20">'._lang("Create account").'</button></form>';
}
    $lg .= '</div></div>
</div>';
    return $lg;
}

add_filter('phpvibe_title', 'modify_title');
add_filter('the_defaults', 'modify_content');

//Time for design
the_header();
include_once(TPL . '/default-full.php');
the_footer();
?>
