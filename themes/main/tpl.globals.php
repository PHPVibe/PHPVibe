<?php /* PHPVibe v6 www.phpvibe.com */
function extra_js() {
return apply_filter( 'filter_extrajs', false );
}
function extra_css() {
return apply_filter( 'filter_extracss', false );
}
function wrapper_class(){
$cls = "container-fluid";
if(is_com('conversation')) {
/* Fluid container for Messenger
   */
$cls = "container-fluid";
} elseif(com() == profile) {
$cls = "block full profile-container";
}
return apply_filters("wrapper-class",$cls );
}
/* Individual functions */
include_once(TPL.'/tpl.header.php');
include_once(TPL.'/tpl.footer.php');

//CSS Placeholders
function fakePlace($number = 5, $shape= 'videoloop') {
	switch ($shape) {
		case 'videolist':
		  $faketemplate ='
		  <div class="prelist vpreload">
		  <span class="fvideothumb prelist-left"></span>
		  <div class="prelist-right">
		  <span class="fvideotxt"></span>
		   <span class="fvideotxt"></span>
		   <span class="fvideotxt"></span>
		   </div>
		   </div>
		  ';
		break;
		default:
		$faketemplate ='
		  <div class="video vpreload">
		  <span class="fvideothumb"></span>
		  <span class="fvideotxt"></span>
		   <span class="fvideotxt"></span>
		   </div>
		  ';
		break;


	}
	$result = '<div class="fake'.$shape.'">';
	for( $i = 0; $i<$number; $i++ ) { 	$result .= $faketemplate;  }
	$result.= '</div>';
	return $result;
}

?>