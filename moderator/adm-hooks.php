<?php /* Auto-hooks */
$hdir = ADM."/hooks/";
if (is_dir($hdir)) {
$hooks = false;
$hooks = glob($hdir."{*.hook.php*}", GLOB_BRACE);
if(not_empty($hooks)) {
foreach ($hooks as $filename)  {
include_once($filename);
}
}
}

/* file for custom hooks and filters */

?>