<?php $activity = $db->get_results($vq);
if ($activity) {
    $did = array();
    echo '<div class="row">
<ul id="user-timeline" class="timelist user-timeline">
';
    $licon = array();
    $licon["1"] = "icon-heart";
    $licon["2"] = "icon-share";
    $licon["3"] = "icon-youtube-play";
    $licon["4"] = "icon-upload";
    $licon["5"] = "icon-rss";
    $licon["6"] = "icon-comments";
    $licon["7"] = "icon-thumbs-up";
    $licon["8"] = "icon-camera";
    $licon["9"] = "icon-star";
    $lback = array();
    $lback["1"] = $lback["9"] = "bg-smooth";
    $lback["2"] = "bg-success";
    $lback["3"] = "bg-flat";
    $lback["4"] = $lback["8"] = "bg-default";
    $lback["5"] = "bg-default";
    $lback["6"] = "bg-info";
    $lback["7"] = "bg-smooth";
    $lasttime = 0;
    foreach ($activity as $buzz) {
        $did = get_activity($buzz);
        if (isset($did["what"]) && !nullval($did["what"])) {
            echo '
<li class="cul-' . $buzz->type . ' t-item">
 <div class="user-timeline-time">';
            if (time_ago($buzz->date) !== $lasttime) {
                time_ago($buzz->date);
                $lasttime = time_ago($buzz->date);
                echo $lasttime;
            }
            echo '</div>
<i class="icon ' . $licon[$buzz->type] . ' user-timeline-icon ' . $lback[$buzz->type] . '"></i>
<div class="user-timeline-content">
<p><a href="' . canonical() . '">' . _lang('You') . '</a>  ' . $did["what"] . '</p>
';
            if (isset($did["content"]) && !nullval($did["content"])) {
                echo '<div class="timeline-media">' . $did["content"] . '</div>';
            }
            echo '</div>

</li>';
            unset($did);
        }
    }
    echo '</ul><br style="clear:both;"/></div>';
}
?>
