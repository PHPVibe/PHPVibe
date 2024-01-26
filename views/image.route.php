<?php $v_id = token_id();
if (_get('nsfw') == 1) {
    $_SESSION['nsfw'] = 1;
}
$embedCode = '';
//Query this image
if (intval($v_id) > 0) {
    if (this_page() > 1) {
//return only comments ;)
        echo comments('img-' . $v_id, this_page());
        exit();
    }
    $cache_name = "image-" . $v_id;
    $image = $db->get_row("SELECT " . DB_PREFIX . "channels.cat_name as channel_name," . DB_PREFIX . "images.*," . DB_PREFIX . "users.avatar, " . DB_PREFIX . "users.name as owner, " . DB_PREFIX . "users.avatar FROM " . DB_PREFIX . "images 
LEFT JOIN " . DB_PREFIX . "channels ON " . DB_PREFIX . "images.category =" . DB_PREFIX . "channels.cat_id
LEFT JOIN " . DB_PREFIX . "users ON " . DB_PREFIX . "images.user_id = " . DB_PREFIX . "users.id WHERE " . DB_PREFIX . "images.`id` = '" . $v_id . "' limit 0,1");
    $cache_name = null; //reset
    $is_owner = false;
    if ($image) {

        if (is_user()) {
            /* Check if current user is the owner */
            if ($image->user_id == user_id()) {
                $is_owner = true;
            }
        }
// Canonical url
        $canonical = image_url($image->id, $image->title);
//Check for local thumbs
        $image->thumb = $source = str_replace('localimage', '', $image->source);
        $image->thumb = ltrim( $image->thumb, '/');
        $image->thumb = site_url().'storage/'.get_option('mediafolder').'/thumb_'. $image->thumb;

//Check if it's private 
        if (($image->ispremium == 1) && !has_premium()) {
//Premium video
            $embedimage = '<div class="vprocessing">
<div class="vpre">' . _lang("Premium image!") . '</div> 
<div class="vex">' . _lang("Become a premium user for as low as ") . ' ' . get_option("monthlycurrency", "USD") . ' ' . get_option("monthlyprice", "1") . '
<div class="full text-center mtop20"><a class="btn btn-primary" href="' . site_url() . 'payment">' . _lang("Upgrade") . '</a></div>
</div>
</div>';
        } elseif ((($image->privacy == 1) || $image->private == 1) && !im_following($image->user_id)) {
//Video is not public
            $embedimage = '<div class="vprocessing">
<div class="vpre">' . _lang("This image is for subscribers only!") . '</div> 
<div class="vex"><a href="' . profile_url($image->user_id, $image->owner) . '">' . _lang("Please subscribe to ") . ' ' . $image->owner . ' ' . _lang("to see this image") . '</a>
</div>
</div>';
        } else {
            //$real_link = site_url() . 'storage/' . get_option('mediafolder') . '/' . $image->source;
            $real_link = $source = str_replace('localimage', '', $image->source);
            $real_link = ltrim( $real_link, '/');
            $real_link = site_url().'storage/'.get_option('mediafolder').'/'. $real_link;
            $embedimage = '<a rel="lightbox" class="media-href img-responsive" title="' . _html($image->title) . '" href="' . $real_link . '">
<img class="media-img" src="' . $real_link . '" />
</a>';
        }
        if (nsfilter()) {
            $embedimage .= '<div class="nsfw-warn"><span>' . _lang("This image is not safe") . '</span>
<a href="' . $canonical . '?nsfw=1">' . ("I understand and I am over 18") . '</a><a href="' . site_url() . '">' . _lang("I am under 18") . '</a>
</div>';
        }
//Lightbox support
        function lbox()
        {
            $lightbox = '
<script type="text/javascript" src="' . tpl() . 'scripts/jquery.fluidbox.min.js"></script>
<script type="text/javascript">
$(function () {
	var
			// ACTIVITY INDICATOR

			activityIndicatorOn = function()
			{
				$( \'<div id="imagelightbox-loading"><div></div></div>\' ).appendTo( \'body\' );
			},
			activityIndicatorOff = function()
			{
				$( \'#imagelightbox-loading\' ).remove();
			},


			// OVERLAY

			overlayOn = function()
			{
				$( \'<div id="imagelightbox-overlay"></div>\' ).appendTo( \'body\' );
			},
			overlayOff = function()
			{
				$( \'#imagelightbox-overlay\' ).remove();
			},


			// CLOSE BUTTON

			closeButtonOn = function( instance )
			{
				$( \'<button type="button" id="imagelightbox-close" title="Close"></button>\' ).appendTo( \'body\' ).on( \'click touchend\', function(){ $( this ).remove(); instance.quitImageLightbox(); return false; });
			},
			closeButtonOff = function()
			{
				$( \'#imagelightbox-close\' ).remove();
			},


			// CAPTION

			captionOn = function()
			{
				var description = $( \'a[href="\' + $( \'#imagelightbox\' ).attr( \'src\' ) + \'"] img\' ).attr( \'alt\' );
				if( description.length > 0 )
					$( \'<div id="imagelightbox-caption">\' + description + \'</div>\' ).appendTo( \'body\' );
			},
			captionOff = function()
			{
				$( \'#imagelightbox-caption\' ).remove();
			},


			// NAVIGATION

			navigationOn = function( instance, selector )
			{
				var images = $( selector );
				if( images.length )
				{
					var nav = $( \'<div id="imagelightbox-nav"></div>\' );
					for( var i = 0; i < images.length; i++ )
						nav.append( \'<button type="button"></button>\' );

					nav.appendTo( \'body\' );
					nav.on( \'click touchend\', function(){ return false; });

					var navItems = nav.find( \'button\' );
					navItems.on( \'click touchend\', function()
					{
						var $this = $( this );
						if( images.eq( $this.index() ).attr( \'href\' ) != $( \'#imagelightbox\' ).attr( \'src\' ) )
							instance.switchImageLightbox( $this.index() );

						navItems.removeClass( \'active\' );
						navItems.eq( $this.index() ).addClass( \'active\' );

						return false;
					})
					.on( \'touchend\', function(){ return false; });
				}
			},
			navigationUpdate = function( selector )
			{
				var items = $( \'#imagelightbox-nav button\' );
				items.removeClass( \'active\' );
				items.eq( $( selector ).filter( \'[href="\' + $( \'#imagelightbox\' ).attr( \'src\' ) + \'"]\' ).index( selector ) ).addClass( \'active\' );
			},
			navigationOff = function()
			{
				$( \'#imagelightbox-nav\' ).remove();
			},


			// ARROWS

			arrowsOn = function( instance, selector )
			{
				var $arrows = $( \'<button type="button" class="imagelightbox-arrow imagelightbox-arrow-left"></button><button type="button" class="imagelightbox-arrow imagelightbox-arrow-right"></button>\' );

				$arrows.appendTo( \'body\' );

				$arrows.on( \'click touchend\', function( e )
				{
					e.preventDefault();

					var $this	= $( this ),
						$target	= $( selector + \'[href="\' + $( \'#imagelightbox\' ).attr( \'src\' ) + \'"]\' ),
						index	= $target.index( selector );

					if( $this.hasClass( \'imagelightbox-arrow-left\' ) )
					{
						index = index - 1;
						if( !$( selector ).eq( index ).length )
							index = $( selector ).length;
					}
					else
					{
						index = index + 1;
						if( !$( selector ).eq( index ).length )
							index = 0;
					}

					instance.switchImageLightbox( index );
					return false;
				});
			},
			arrowsOff = function()
			{
				$( \'.imagelightbox-arrow\' ).remove();
			};
  var instanceC =  $( \'a[rel="lightbox"]\' ).imageLightbox(
	{
				quitOnDocClick:	true,
				onStart:		function() { closeButtonOn( instanceC ); overlayOn();  },
				onEnd:			function() { closeButtonOff(); activityIndicatorOff(); overlayOff(); },
				onLoadStart: 	function() { activityIndicatorOn(); },
				onLoadEnd:	 	function() { activityIndicatorOff(); }
	});
})
</script>
';
            return apply_filters('the_lightbox', $lightbox);
        }

        function lboxcss()
        {
            return '<link href="' . tpl() . 'styles/fluidbox.min.css" rel="stylesheet" />';
        }

        add_filter('filter_extracss', 'lboxcss');
        add_filter('filter_extrajs', 'lbox');
// SEO Filters
        function modify_title($text)
        {
            global $image;
            return strip_tags(_html(get_option('seo-image-pre', '') . $image->title . get_option('seo-image-post', '')));
        }

        function modify_desc($text)
        {
            global $image;
            return _cut(strip_tags(_html($image->description)), 160);
        }

        add_filter('phpvibe_title', 'modify_title');
        add_filter('phpvibe_desc', 'modify_desc');
//Time for design
        the_header();
        if (intval($image->media) <> 3) {
            /* Is image or music */
            include_once(TPL . '/image.php');
        } else {
            /* Is image */
            include_once(TPL . '/single_image.php');
        }
        the_footer();

    } else {
//Oups, not found
        layout('404');
    }
} else {
//Oups, not found
    layout('404');
}
?>