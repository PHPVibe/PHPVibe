<script>
    function minijJS() {
        $.ajax({
            url: '<?php echo admin_url(); ?>minijs.php',
            beforeSend: function (xhr) {
                $('a#vv').text('Started in background...this may take a while');
            }
        })
            .done(function (data) {
                //alert(data);
                if (data == 'done') {
                    $('a#vv').text('Done.');
                    $('code#secod').remove();
                } else {
                    $('a#vv').text('Failed! Try again.');
                    $('a#vv').removeClass('btn-default').addClass('btn-outline btn-danger');
                    $('code#secod').text(data).addClass('btn-outline btn-danger active');
                }
            });
    }
</script>
<div class="row">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Clear static cache</h3>
        </div>
        <div class="panel-body">
            <p>This cleans cache and puts live some changes. Empties full cache, js + css cache and ezsql's cache.</p>
            <div>
                <a class="btn btn-raised btn-primary pull-right"
                   href="<?php echo admin_url('clean-cache'); ?>&clearit=1">Clean cache</a>
            </div>
            <p><code>It's a good practice to clean cache from time to time!</code></p>
        </div>
    </div>
</div>
<div class="row" style="padding:30px 0;">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">JS combined file</h3>
        </div>
        <div class="panel-body">
            <p>Once cache is cleared so is the file cache for minified javascripts.</p>
            <div>
                <a class="btn btn-raised btn-primary pull-right" id="vv" href="javascript:minijJS()">Rebuild Minified
                    javascript</a>
            </div>
            <p><code id="secod">This process takes time and is server heavy but reduces by up to 20% the js file
                    size.</code></p>
            <p><a class="btn btn-raised btn-default" href="<?php echo tpl(); ?>styles/minjs.php" target="_blank">SEE
                    FILE</a> (Opens in a new tab) Use ctrl/command + shift + r to clear the browser's file cache</p>
        </div>
    </div>
</div>
<?php
if (_get('clearit')) {
    $cInc = ABSPATH;
    if (!defined('vSTATIC_FOLD'))
        define('vSTATIC_FOLD', '/cache/html/');
    require_once(CNC . '/fullcache.php');
    $debug1 = FullCache::ClearAll();
    foreach ($debug1 as $d1) {
        echo str_replace($cInc, '', $d1);
    }
    $debug = $db->clean_cache();
    foreach ($debug as $d) {
        if (not_empty($d)) {
            echo str_replace($cInc, '', $d);
        }
    }
    $debug2 = $db->clean_cache(true);
    foreach ($debug2 as $d2) {
        if (not_empty($d2)) {
            echo str_replace($cInc, '', $d2);
        }
    }
    $jdebug = jc_purge();
    foreach ($jdebug as $dj) {
        if (not_empty($dj)) {
            echo str_replace($cInc, '', $dj);
        }
    }
    echo '<div class="msg-win">Cache cleared</div>';
}
?>
