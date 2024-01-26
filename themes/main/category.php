<div id="channel-content" class="main-holder pad-holder col-md-12 top10 nomargin">
    <?php
    $heading = '';
    $heading_meta = '';
    $subies = array();
    if($subchannels) {
        $heading_meta .= ' <div class="fake-padding black-slider">
<ul id="carousel" class="owl-carousel">';
        foreach ( $subchannels as $more ) {
            $subies[] = $more->cat_id;
            $heading_meta .= '<div class="subitem">
<a href="'.channel_url($more->cat_id,$more->cat_name).'" title="'.$more->cat_name.'">';
            if(is_empty($more->picture)) {
                $heading_meta .= '<img alt="'.$more->cat_name.'" data-name="'.$more->cat_name.'" class="img-circle NoAvatar"/>';
            } else {
                $heading_meta .= '<img alt="'.$more->cat_name.'" data-name="'.$more->cat_name.'" class="img-circle cartistic" src="'.thumb_fix($more->picture, true, 82, 110).'"/>';
            }
            $heading_meta .= '<span class="btn btn-block">'._html($more->cat_name).'</span>
</a>
</div>';
        }
        $heading_meta .= '</ul></div>';
    }
    $heading_meta .= '
<div class="row bottom20 category-head top20">
<div class="col-md-1 col-xs-1 text-center">';
    if(is_empty($channel->picture)) {
        $heading_meta .= '<img class="img-responsive img-circle mtop20 NoAvatar" data-name="'.$channel->cat_name.'"  />';
    } else {
        $heading_meta .= '<img class="img-responsive img-circle mtop20" data-name="'.$channel->cat_name.'" src="'.thumb_fix($channel->picture, true, 80, 80).'" />';
    }
    $heading_meta .= '</div>
<div class="col-md-9 col-xs-10 left10">
<h1>'._html($channel->cat_name).'</h1>
<p>'._html($channel->cat_desc).'</p>
</div>
</div>
';
    if($typeofC <> 3) {
        /* Music or videos */
        $ormore = '';
        //Subcats
        //print_r($subies);
        if(isset($subies)) {
            foreach ($subies as $su) {
                if(not_empty($su)) {
                    $ormore = "or find_in_set(".$su." ,".DB_PREFIX."videos.category) ";
                }
            }

        }
        $options = (($typeofC > 1)? DB_PREFIX."videos.liked," : "").DB_PREFIX."videos.id,".DB_PREFIX."videos.media,".DB_PREFIX."videos.date,".DB_PREFIX."videos.title,".DB_PREFIX."videos.user_id,".DB_PREFIX."videos.thumb,".DB_PREFIX."videos.views,".DB_PREFIX."videos.duration";
        $vq = "select ".$options.", ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."videos LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."videos.user_id = ".DB_PREFIX."users.id
WHERE (find_in_set(".$channel->cat_id." ,".DB_PREFIX."videos.category) ".$ormore.")
and ".DB_PREFIX."videos.pub > 0 and ".DB_PREFIX."videos.date < now() ORDER BY ".DB_PREFIX."videos.id DESC ".this_limit(bpp());
        //echo $vq;
        // Single
        /*
        WHERE ".DB_PREFIX."videos.category in (SELECT cat_id from ".DB_PREFIX."channels where cat_id = '".$channel->cat_id."' or cat_id in  (select  cat_id
        from    (select * from ".DB_PREFIX."channels
        order by child_of, cat_id) cats_sorted,
        (select @pv := '".$channel->cat_id."') initialisation
        where   find_in_set(child_of, @pv) > 0
        and     @pv := concat(@pv, ',', cat_id)) ) and ".DB_PREFIX."videos.pub > 0 and ".DB_PREFIX."videos.date < now() ORDER BY ".DB_PREFIX."videos.id DESC ".this_limit(bpp());
        echo $vq;
         */
        if($typeofC > 1) {
            include_once(TPL.'/music-loop.php');
        } else {
            include_once(TPL.'/video-loop.php');
        }
    } else {
        /* Images */
        $options = DB_PREFIX."images.id, ".DB_PREFIX."images.source, ".DB_PREFIX."images.date,".DB_PREFIX."images.title,".DB_PREFIX."images.user_id,".DB_PREFIX."images.views";
        $vq = "select ".$options.", ".DB_PREFIX."users.avatar, ".DB_PREFIX."users.name as owner FROM ".DB_PREFIX."images LEFT JOIN ".DB_PREFIX."users ON ".DB_PREFIX."images.user_id = ".DB_PREFIX."users.id
WHERE ".DB_PREFIX."images.category in (SELECT cat_id from ".DB_PREFIX."channels where cat_id = '".$channel->cat_id."' or cat_id in  (select  cat_id
from    (select * from ".DB_PREFIX."channels
         order by child_of, cat_id) cats_sorted,
        (select @pv := '".$channel->cat_id."') initialisation
where   find_in_set(child_of, @pv) > 0
and     @pv := concat(@pv, ',', cat_id)) ) and ".DB_PREFIX."images.pub > 0 and ".DB_PREFIX."images.date < now() ORDER BY ".DB_PREFIX."images.id DESC ".this_limit(bpp());
        $blockextra = '<div class="mtop20">&nbsp;</div>';
        include_once(TPL.'/images-loop.php');
    }
    ?>
</div>
