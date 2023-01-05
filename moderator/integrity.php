<div class="row">
<div class="panel panel-bordered col-md-12 col-xs-12">
<div class="panel-heading">
<h3 class="panel-title">PHPVibe's integrity check</h3>
</div>
<div class="panel-body nopad">
<ul class="list-group iconed-xlist">
<?php
if (is_readable(ABSPATH.'/setup')) {
echo '<li class="list-group-item"><i class="icon-fire-extinguisher redText"></i>Setup folder <em>(/setup)</em> exists. Delete it!</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Setup folder is not present</li>';
}
if (!is_writable(ABSPATH.'/'.ADMINCP.'/cache')) {
echo '<li class="list-group-item"><i class="icon-fire-extinguisher redText"></i>Admin cache & assets folder ('.ABSPATH.'/'.ADMINCP.'/cache/) is not writeable</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Admin cache is ok.</li>';
}
if (!is_writable(ABSPATH.'/storage/cache')) {
echo '<li class="list-group-item">Cache folder (/storage/cache)is not writeable</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Cache is ok.</li>';
}
if (!is_writable(ABSPATH.'/storage/cache/html')) {
echo '<li class="list-group-item"><i class="icon-fire-extinguisher redText"></i>Fullcache folder (/storage/cache/html)is not writeable</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Full cache is ok.</li>';
}
if (!is_writable(ABSPATH.'/storage/langs')) {
echo '<li class="list-group-item"><i class="icon-fire-extinguisher redText"></i>Languages folder (/storage/langs)is not writeable. Langs can\'t be edited. <em>(This is not an issue if is not writeable for security reasons)</em>.</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Langs can be edited.</li>';
}
if (!is_writable(ABSPATH.'/storage/'.get_option('mediafolder'))) {
echo '<li class="list-group-item"><i class="icon-fire-extinguisher redText"></i>Media storage folder (/storage/'.get_option('mediafolder').')is not writeable</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Media storage is ok.</li>';
}
if (!is_writable(ABSPATH.'/storage/'.get_option('mediafolder').'/thumbs')) {
echo '<li class="list-group-item">Media thumbs storage folder (/storage/'.get_option('mediafolder').'/thumbs)is not writeable</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Media thumbs storage is ok.</li>';
}
if (!is_writable(ABSPATH.'/storage/cache/thumbs')) {
echo '<li class="list-group-item"><i class="icon-fire-extinguisher redText"></i>Thumbs folder (/storage/cache/thumbs) is not writeable</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Thumbs storage is ok.</li>';
}
if (!is_writable(ABSPATH.'/storage/uploads')) {
echo '<li class="list-group-item"><i class="icon-fire-extinguisher redText"></i>Uploads folder ('.ABSPATH.'/storage/uploads)is not writeable</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Uploads folder is ok.</li>';
}
if (!is_writable(ABSPATH.'/'.ADMINCP.'/alog.txt')) {
echo '<li class="list-group-item"><i class="icon-fire-extinguisher redText"></i>Admin log ('.ABSPATH.'/'.ADMINCP.'/alog.txt)is not writeable</li>';
} else {
echo '<li class="list-group-item"><i class="icon-check greenText"></i>Admin log is writeable.</li>';
}
?>
</ul>
</div>				
</div>
</div>	