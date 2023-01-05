/**
 * Video.js Social Share
 * Created by Justin McCraw for New York Media LLC
 * License information: https://github.com/jmccraw/videojs-socialShare/blob/master/LICENSE
 * Plugin details: https://github.com/jmccraw/videojs-socialShare
 */

(function(videojs) {
  'use strict';

  videojs.plugin('socialShare', function(opts) {
    opts = opts || {};
    var player = this;
    var _ss;
    var fbIcon = '<svg class="vjs-social-share-svg" xmlns="http://www.w3.org/2000/svg" role="presentation" width="36" height="36" viewBox="0 0 36 36" preserveAspectRatio="xMinYMin meet"><path fill-rule="evenodd" clip-rule="evenodd" fill="#3E5C9B" d="M5.4 0h25.2c3 0 5.4 2.4 5.4 5.4v25.2c0 3-2.4 5.4-5.4 5.4h-25.2c-3 0-5.4-2.4-5.4-5.4v-25.2c0-3 2.4-5.4 5.4-5.4z"></path><path fill="#fff" d="M19.4 28v-9.2h4l.6-3.3h-4.6v-2.4c0-1.1.3-1.8 2-1.8h2.6v-3.1c-.4 0-1.1-.2-2.6-.2-3.1 0-5.7 1.8-5.7 5v2.5h-3.7v3.3h3.7v9.2h3.7z"></path></svg>';
    var twIcon = '<svg class="vjs-social-share-svg" xmlns="http://www.w3.org/2000/svg" role="presentation" width="36" height="36" viewBox="0 0 36 36" preserveAspectRatio="xMinYMin meet"><path fill-rule="evenodd" clip-rule="evenodd" fill="#28A9E1" d="M5.4 0h25.2c3 0 5.4 2.4 5.4 5.4v25.2c0 3-2.4 5.4-5.4 5.4h-25.2c-3 0-5.4-2.4-5.4-5.4v-25.2c0-3 2.4-5.4 5.4-5.4z"></path><path fill="#fff" d="M28.2 12.3c-.7.3-1.4.5-2.2.6.8-.5 1.4-1.2 1.7-2.1-.7.4-1.5.7-2.4.9-.7-.7-1.7-1.2-2.8-1.2-2.1 0-3.8 1.7-3.8 3.8 0 .3 0 .6.1.9-3.1-.2-5.9-1.7-7.8-3.9-.3.6-.5 1.2-.5 1.9 0 1.3.7 2.5 1.7 3.1-.6 0-1.2-.2-1.7-.5 0 1.8 1.3 3.3 3 3.7-.3.1-.6.1-1 .1-.2 0-.5 0-.7-.1.5 1.5 1.9 2.6 3.5 2.6-1.3 1-2.9 1.6-4.7 1.6-.3 0-.6 0-.9-.1 1.7 1.1 3.6 1.7 5.8 1.7 6.9 0 10.7-5.7 10.7-10.7v-.5c.8-.4 1.5-1 2-1.8z"></path></svg>';

    /**
     * Launches the Twitter Web Intent form in a new window
     * @type {function}
     */
    function launchTweet(e) {
      e.preventDefault();

      window.open(
        'http://twitter.com/intent/tweet' +
          '?text=' + encodeURIComponent(opts.twitter.shareText ? opts.twitter.shareText : '') +
          '&url=' + encodeURIComponent(opts.twitter.shareUrl ? opts.twitter.shareUrl : window.location.href) +
          '&via=' + (opts.twitter.handle ? opts.twitter.handle : ''),
        'Share This Video to Twitter',
        'width=600,height=300,left=' + Math.ceil((window.innerWidth / 2) - 300) + ',top=' +
          Math.ceil((window.innerHeight / 2) - 127)
      );
    }

    /**
     * Launches the Facebook modal
     * @type {function}
     */
    function launchFacebook(e) {
      e.preventDefault();
      var url = opts.facebook.shareUrl ? opts.facebook.shareUrl : window.location.href;

      if (typeof FB !== 'undefined') {
        // assumes you have the proper og metadata filled out for your site
        FB.ui({
          method: 'share',
          href: url,
          picture: opts.facebook.shareImage ? opts.facebook.shareImage : '',
          name: '',
          caption: '',
          description: opts.facebook.shareText ? opts.facebook.shareText : ''
        }, function (response) {
        });
      } else if (!!document.querySelector('meta[property="fb:app_id"]')) {
        // since the FB object doesn't exist, try to scrape the page for og information and use a new window URL method
        window.open(
          'https://www.facebook.com/dialog/share' +
            '?app_id=' + document.querySelector('meta[property="fb:app_id"]').content +
            '&display=popup' +
            '&href=' + encodeURIComponent(url) +
            '&redirect_uri=' + encodeURIComponent(url),
          'Share This Video to Facebook',
          'width=600,height=300,left=' + Math.ceil((window.innerWidth / 2) - 300) + ',top=' +
            Math.ceil((window.innerHeight / 2) - 127)
        );
      } else {
        // Facebook isn't implemented properly in your site's metadata, so we'll just hide this element
        // a little jarring, but better than failing, perhaps
        this.style.display = 'none';
      }
    }

    /**
     * Generate the DOM elements for the social share tool
     * @type {function}
     */
    function constructSocialShareContent() {
      var _frag = document.createDocumentFragment();
      var _aside = document.createElement('aside');
      var _button;

      // fill in specific Twitter settings, if available
      if (opts.twitter) {
        _button = document.createElement('a');
        _button.className = 'vjs-social-share-link';
        _button.setAttribute('data-network', 'twitter');
        _button.innerHTML = twIcon;
        _button.addEventListener('click', launchTweet, false);
        _aside.appendChild(_button);
      }
      // fill in specific Facebook settings, if available
      if (opts.facebook) {
        _button = document.createElement('a');
        _button.className = 'vjs-social-share-link';
        _button.setAttribute('data-network', 'facebook');
        _button.innerHTML = fbIcon;
        _button.addEventListener('click', launchFacebook, false);
        _aside.appendChild(_button);
      }

      _aside.className = 'vjs-social-share';
      _ss = _aside;
      _frag.appendChild(_aside);

      player.el().appendChild(_frag);
    }

    // attach VideoJS event handlers
    player.on('mouseover', function() {
      // on hover, fade in the social share tools
      _ss.classList.add('is-visible');
    }).on('mouseout', function() {
      // when not hovering, fade share tools back out
      _ss.classList.remove('is-visible');
    });

    player.ready(function() {
      if (opts.facebook || opts.twitter) {
        constructSocialShareContent();
      }
    });

  });
}(window.videojs));