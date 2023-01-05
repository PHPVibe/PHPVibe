<?php require("../load.php");
if(is_admin()) {
$action 				= toDb($_POST['action']); 
$updateRecordsArray 	= $_POST['recordsArray'];
//var_dump($updateRecordsArray );
if ($action == "updateRecordsListings"){
	
	$listingCounter = 1;
	foreach ($updateRecordsArray as $recordIDValue) {
		
		$query = "UPDATE ".DB_PREFIX."homepage SET `ord` = " . $listingCounter . " WHERE id = " . $recordIDValue;
		$db->query($query);
		$listingCounter = $listingCounter + 1;	
	}
	
	//echo '<pre>';
	//print_r($updateRecordsArray);
	//echo '</pre>';
	echo '<div class="msg-info">Order updated.</div>';
}
}
?>