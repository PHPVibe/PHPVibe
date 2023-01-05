<?php
$all_options = get_all_options();

if(_post('PINS1')) {
$adpin = get_option('PINA1',1).get_option('PINA2',2).get_option('PINA3',3).get_option('PINA4',4);
$compare = _post('PINS1')._post('PINS2')._post('PINS3')._post('PINS4');	
if($compare <> $adpin ) {
	echo '<div class="msg-warning mtop20 mbot20">Pin is incorrect!</div>';
} else {
	$_SESSION['admpin'] = $adpin;
	redirect(admin_url());
}
}
?>

<div class="row text-center">
<div style="max-width:800px; text-align:left; margin:40px auto;">
<h3> <i class="material-icons" style="font-size:40px;"> pan_tool </i> PIN required</h3>
<form id="validate" class="form-horizontal styled" action="<?php echo admin_url('pin');?>" enctype="multipart/form-data" method="post">
<fieldset>
<input type="hidden" name="update_options_now" class="hide" value="1" /> 


<div class="form-group form-material">
<label class="control-label">To continue to the administration <strong>please enter your unlock code</strong></label>
 <div class="controls">
<div class="row">
<div class="col-md-2">
<input type="number" name="PINS1" min="0" max="99" class="form-control" value="1">
</div>
<div class="col-md-2">
<input type="number" name="PINS2" min="0" max="99" class="form-control" value="2">
</div>
<div class="col-md-2">
<input type="number" name="PINS3" min="0" max="99" class="form-control" value="3">
</div>
<div class="col-md-2">
<input type="number" name="PINS4"min="0"  max="99" class="form-control" value="4">
</div>
</div>
<span class="help-block" id="limit-text">Enter your code <strong>as set in Login setts</strong>. You can use "tab" to go fast accros fields.</span>
</div>
</div>
<div class="form-group form-material">
<button class="btn btn-large btn-primary pull-right" type="submit">Continue</button>	
</div>	
</fieldset>						
</form>
</div>
</div>