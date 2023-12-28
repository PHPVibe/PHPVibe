<?php  
$form = '';
$paypalURL     = 'https://www.paypal.com/cgi-bin/webscr';
$successURL = site_url().'payment/success/';
$cancelURL     = site_url().'payment/';
$notifyURL     = site_url().'payment/noty/?ppin=1';
$itemPrice = get_option("monthlyprice", "1");
$itemCurrency = get_option("monthlycurrency", "USD");
$itemName = _lang("Premium status");
$item_number = $item_id = 1;
include_once( INC.'/paypal.class.php' ); 
?>

<?php
$show_form=1;
//FORM SUBMISSION PROCESSING 
	# PLEASE DO NOT EDIT FOLLOWING LINES ------->
	if(!empty($_POST["process"]) && $_POST["process"]=="yes"){
				if(!empty($_POST["amount"]) && is_numeric($_POST["amount"] )){ 	
				$amount = (!empty($_POST["amount"]))?strip_tags(str_replace("'","",$_POST["amount"])):'';
				$show_form=0;			 
				$paypal = new paypal_class;
				$paypal->add_field('business', PPMail);
				$paypal->add_field('return', $successURL);
				$paypal->add_field('cancel_return', $cancelURL);
				$paypal->add_field('notify_url', $notifyURL );
				$paypal->add_field('item_name_1', strip_tags($itemName));
				$paypal->add_field('amount_1', 1);
				$paypal->add_field('item_number_1', $item_id);
				$paypal->add_field('quantity_1', $_POST["months"]);
				$paypal->add_field('custom', user_id());
				$paypal->add_field('upload', 1);
				$paypal->add_field('cmd', '_cart'); 
				$paypal->add_field('txn_type', 'cart'); 
				$paypal->add_field('num_cart_items', 1);
				$paypal->add_field('payment_gross', $amount);
				$paypal->add_field('currency_code', strip_tags($itemCurrency));
			  	$paypal->submit_paypal_post(); // submit the fields to paypal
				$show_form=0;
				} elseif(!is_numeric($_POST["amount"]) || empty($_POST["amount"])) { 
					$mess="<span class='redBack'>Please type amount!</span>"; 
					$show_form=1; 
				} 
			}



if(is_empty(token()) && $show_form==1) {
if(is_user()) {	

$form = '	<script>
function getSubsPrice(obj){
    var month = obj.value;
    var price = (month * '.$itemPrice.');
    document.getElementById(\'subPrice\').innerHTML = price+\' '.$itemCurrency.'\';
    document.getElementById(\'paypalAmt\').value = price;
	document.getElementById(\'paypalmmt\').value = month;
}
</script>
<div class="block text-center row">
<div class="isBoxed text-left" style="margin:5% auto; padding:30px">
<h2>'._lang("Get premium").'</h2>
<h3 class="mbot20">'._lang("Get a cool premium badge, access to oremium videos, remove ads and get other benefits!").'</h3>
<p class="block mtop20">'._lang("Choose Validity:").'
    <select name="validity" onchange="getSubsPrice(this);">
        <option value="1" selected="selected">'._lang("1 Month").'</option>
		<option value="2">'._lang("2 Months").'</option>
        <option value="3">'._lang("3 Months").'</option>
        <option value="6">'._lang("6 Months").'</option>
        <option value="9">'._lang("9 Months").'</option>
        <option value="12">'._lang("Full Year").'</option>
    </select>
</p>
<p>'._lang("Price").': <span id="subPrice" class="badge">'.$itemPrice.' '.$itemCurrency.'</span></p>
<form id="ff1" name="ff1" method="post" class="main" action="" enctype="multipart/form-data">
   <input id="paypalAmt" type="hidden" name="amount" value="'.$itemPrice.'" />
   <input id="paypalmmt" type="hidden" value="1" name="months" />
   <input type="hidden" name="process" value="yes" />	
 <input class="btn btn-success paypal_button" type="submit" value="'._lang("Become premium").'">
	
</form>
</div>
</div>
	';
if(is_moderator() || has_premium()) {
$form = '<h2>'._lang("We're happy to announce you that you are already one of our our elite users").'</h2>';		
$form .= _lang("Nothing to do for you here!");
}	
} else {
$form .= '<h2>'._lang("We're happy your want to join our elite users").'</h2>';		
$form .= _lang("but please login first");	
}
}
if(token() == "success") {
$id = user_id();
user::RefreshUser($id);	
if(has_premium()) {
$form = _lang("Congratulations! You are a premium user");	
} else {
$form = _lang("It may take a bit for PayPal to process a payment!");	
$form .= '<a class="btn btn-primary" href="'.canonical().'">'._lang("Recheck").'</a>';
}
}
if(token() == "noty") {
$paypal = new paypal_class;
 if ($paypal->validate_ipn()) { 
 $subscr_id = $custom = $paypal->pp_data['custom'];
 $paypal->pp_data['payment_date'];
 $payment_status=  $paypal->pp_data['payment_status'];
 $payer_email = $paypal->pp_data['payer_email'];
 $txn_id = $paypal->pp_data['txn_id'];
 $payment_gross = $paypal->pp_data['mc_gross'];
 $currency_code = $paypal->pp_data['mc_currency'];
 $unitPrice = get_option("monthlyprice", "1");
 if(isset($paypal->pp_data['quantity_1']) && ($paypal->pp_data['quantity_1'] > 0)) {
  $subscr_month = $paypal->pp_data['quantity_1'];	 
 } else {
 $subscr_month = ($payment_gross/$unitPrice);
 }
    $subscr_days = ($subscr_month*30);
    $subscr_date_from = date("Y-m-d H:i:s");
    $subscr_date_to = date("Y-m-d H:i:s", strtotime($subscr_date_from. ' + '.$subscr_days.' days'));
 
 $insert = $db->query("INSERT INTO ".DB_PREFIX."user_subscriptions(user_id,validity,valid_from,valid_to,item_number,txn_id,payment_gross,currency_code,subscr_id,payment_status,payer_email) VALUES('".$custom."','".$subscr_month."','".$subscr_date_from."','".$subscr_date_to."','".$item_number."','".$txn_id."','".$payment_gross."','".$currency_code."','".$subscr_id."','".$payment_status."','".$payer_email."')");
 $premium = premium_group();
 if($premium > 0) {
$db->query ("UPDATE  ".DB_PREFIX."users SET group_id='".toDb($premium)."' WHERE id ='" . sanitize_int($custom) . "'");
 }
 header("HTTP/1.1 200 OK");
 exit();
 }
}

function modify_content( ) {
	global $form;
	return $form;
}
add_filter( 'the_defaults', 'modify_content' );
//Time for design
 the_header();
include_once(TPL.'/default-full.php');
the_footer();	
?>