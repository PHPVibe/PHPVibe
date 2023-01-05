<?php function com(){
global $route;
if(isset($route) && $route) {
return toDb($route->getTarget());
}
}
function token(){
global $route;
if(isset($route) && $route) {
$idx = $route->getParameters();
if(isset($idx["section"])) {
$idx["section"] = current(explode("/", $idx["section"]));
$idx["section"] = trim(str_replace("/","",$idx["section"]));
return $idx["section"];
}
}
}
function token_id(){	
global $route;
if(isset($route) && $route) {
$idx = $route->getParameters();
if(isset($idx["id"])) {
return $idx["id"];
}
}
}
?>