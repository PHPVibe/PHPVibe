if (typeof jQuery == 'undefined') {  
var jq = document.createElement('script'); jq.type = 'text/javascript';
  jq.src = 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js';
  document.getElementsByTagName('head')[0].appendChild(jq);
}
var scriptSource = (function(scripts) {
    var scripts = document.getElementsByTagName('script'),
        script = scripts[scripts.length - 1];

    if (script.getAttribute.length !== undefined) {
        return script.src
    }

    return script.getAttribute('src', -1)
}());
function get_hostname(url) {
    var m = url.match(/^(http|https):\/\/[^/]+/);
    return m ? m[0] : null;
}
var domainorigin = get_hostname(scriptSource);
$(document).on('ready', function(){
//console.log("document.URL : "+document.URL);
//console.log("document.location.hostname : "+document.location.hostname);
//console.log("document.location.origin : "+ scriptSource);
//console.log("document.location.origindomain : "+ domainorigin);
var tiframe = $("iframe[src*='"+ domainorigin +"']");
//console.log(tiframe);
if(tiframe) {
$(tiframe).width('100%');	
var ew = $(tiframe).width();
var eh = Math.round((ew/16)*9) + 35;
$(tiframe).height(eh); 
}  
});