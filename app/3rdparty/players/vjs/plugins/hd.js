/*! videojs-resolution-switcher - 2015-7-26
 * Copyright (c) 2016 Kasper Moskwiak
 * Modified by Pierre Kraft and Derk-Jan Hartman
 * Licensed under the Apache-2.0 license. */
(function() {
  /* jshint eqnull: true*/
  /* global require */
  'use strict';
  var videojs = null;
  if(typeof window.videojs === 'undefined' && typeof require === 'function') {
    videojs = require('video.js');
  } else {
    videojs = window.videojs;
  }
  (function(window, videojs) {
    var videoJsResolutionSwitcher,
      defaults = {
        ui: true
      };
    /*
     * Resolution menu item
     */
    var MenuItem = videojs.getComponent('MenuItem');
    var ResolutionMenuItem = videojs.extend(MenuItem, {
      constructor: function(player, options){
        options.selectable = true;
        // Sets this.player_, this.options_ and initializes the component
        MenuItem.call(this, player, options);
        this.src = options.src;
        player.on('resolutionchange', videojs.bind(this, this.update));
      }
    } );
    ResolutionMenuItem.prototype.handleClick = function(event){
      MenuItem.prototype.handleClick.call(this,event);
      this.player_.currentResolution(this.options_.label);
    };
    ResolutionMenuItem.prototype.update = function(){
      var selection = this.player_.currentResolution();
	  /* Aici */
      this.selected(this.options_.label === selection.label);
    };
    MenuItem.registerComponent('ResolutionMenuItem', ResolutionMenuItem);
    /*
     * Resolution menu button
     */
    var MenuButton = videojs.getComponent('MenuButton');
    var ResolutionMenuButton = videojs.extend(MenuButton, {
      constructor: function(player, options){
        this.label = document.createElement('span');
        options.label = 'Quality';
        // Sets this.player_, this.options_ and initializes the component
        MenuButton.call(this, player, options);
        this.el().setAttribute('aria-label','Quality');
        this.controlText('Quality');
        if(options.dynamicLabel){
		videojs.dom.addClass(this.label, 'vjs-resolution-button-label');	
	    this.el().appendChild(this.label);
        }else{
          var staticLabel = document.createElement('span');
           videojs.dom.addClass(staticLabel, 'vjs-menu-icon');
          this.el().appendChild(staticLabel);
        }
        player.on('updateSources', videojs.bind( this, this.update ) );
		player.on('resolutionchange', videojs.bind( this, this.update ) );
      }
    } );
    ResolutionMenuButton.prototype.createItems = function(){
      var menuItems = [];
      var labels = (this.sources && this.sources.label) || {};
      // FIXME order is not guaranteed here.
      for (var key in labels) {
        if (labels.hasOwnProperty(key)) {
          menuItems.push(new ResolutionMenuItem(
            this.player_,
            {
              label: key,
              src: labels[key],
              selected: key === (this.currentSelection ? this.currentSelection.label : false)
            })
          );
        }
      }
      return menuItems;
    };
    ResolutionMenuButton.prototype.update = function(){
      this.sources = this.player_.getGroupedSrc();
      this.currentSelection = this.player_.currentResolution();
      this.label.innerHTML = this.currentSelection ? this.currentSelection.label : '';
	  var label = this.label.innerHTML;
	  if(label) {		
        var numericResolution=label.toLowerCase().replace('p','').replace('hd','');		
        numericResolution=parseInt(numericResolution);
		//console.log(numericResolution);
	    if(numericResolution >= 700 ) {
        this.label.innerHTML = numericResolution+'p <span class="vjs-ishd">HD</span>';
		} else {
		if(numericResolution >= 1 ) {	
		this.label.innerHTML = numericResolution+'p <span class="vjs-issd">SD</span>';
		} else {
        this.label.innerHTML = 'auto';
		}		
		}		
		}
      return MenuButton.prototype.update.call(this);
    };
    ResolutionMenuButton.prototype.buildCSSClass = function(){
      return MenuButton.prototype.buildCSSClass.call( this ) + ' vjs-resolution-button';
    };
    MenuButton.registerComponent('ResolutionMenuButton', ResolutionMenuButton);
    /**
     * Initialize the plugin.
     * @param {object} [options] configuration for the plugin
     */
    videoJsResolutionSwitcher = function(options) {
      var settings = videojs.mergeOptions(defaults, options),
          player = this,
          groupedSrc = {},
          currentSources = {},
          currentResolutionState = {};
      /**
       * Updates player sources or returns current source URL
       * @param   {Array}  [src] array of sources [{src: '', type: '', label: '', res: ''}]
       * @returns {Object|String|Array} videojs player object if used as setter or current source URL, object, or array of sources
       */
      player.updateSrc = function(src){
        //Return current src if src is not given
        if(!src){ return player.src(); }
        // Only add those sources which we can (maybe) play
        src = src.filter( function(source) {
          try {
            return ( player.canPlayType( source.type ) !== '' );
          } catch (e) {
            // If a Tech doesn't yet have canPlayType just add it
            return true;
          }
        });
        //Sort sources
        this.currentSources = src.sort(compareResolutions);
        this.groupedSrc = bucketSources(this.currentSources);
        // Pick one by default
        var chosen = chooseSrc(this.groupedSrc, this.currentSources);
        this.currentResolutionState = {
          label: chosen.label,
          sources: chosen.sources
        };
        player.trigger('updateSources');
        player.setSourcesSanitized(chosen.sources, chosen.label);
        player.trigger('resolutionchange');
        return player;
      };
      /**
       * Returns current resolution or sets one when label is specified
       * @param {String}   [label]         label name
       * @param {Function} [customSourcePicker] custom function to choose source. Takes 2 arguments: sources, label. Must return player object.
       * @returns {Object}   current resolution object {label: '', sources: []} if used as getter or player object if used as setter
       */
      player.currentResolution = function(label, customSourcePicker){
        if(label == null) { return this.currentResolutionState; }           
        // Lookup sources for label
        if(!this.groupedSrc || !this.groupedSrc.label || !this.groupedSrc.label[label]){
          return;
        }
        var sources = this.groupedSrc.label[label];
        // Remember player state
        var currentTime = player.currentTime();
        var isPaused = player.paused();
        // Hide bigPlayButton
        if(!isPaused && this.player_.options_.bigPlayButton){
          this.player_.bigPlayButton.hide();
        }
        // Change player source and wait for loadeddata event, then play video
        // loadedmetadata doesn't work right now for flash.
        // Probably because of https://github.com/videojs/video-js-swf/issues/124
        // If player preload is 'none' and then loadeddata not fired. So, we need timeupdate event for seek handle (timeupdate doesn't work properly with flash)
        var handleSeekEvent = 'loadeddata';
        if(this.player_.techName_ !== 'Youtube' && this.player_.preload() === 'none' && this.player_.techName_ !== 'Flash') {
          handleSeekEvent = 'timeupdate';
        }
        player
          .setSourcesSanitized(sources, label, customSourcePicker || settings.customSourcePicker)
          .one(handleSeekEvent, function() {
            player.currentTime(currentTime);
            player.handleTechSeeked_();
            if(!isPaused){
              // Start playing and hide loadingSpinner (flash issue ?)
              player.play();
              player.handleTechSeeked_()
            }
            player.trigger('resolutionchange');			
          });
        return player;
      };
      /**
       * Returns grouped sources by label, resolution and type
       * @returns {Object} grouped sources: { label: { key: [] }, res: { key: [] }, type: { key: [] } }
       */
      player.getGroupedSrc = function(){
        return this.groupedSrc;
      };
      player.setSourcesSanitized = function(sources, label, customSourcePicker) {		
        this.currentResolutionState = {
          label: label,
          sources: sources
        };
        if(typeof customSourcePicker === 'function'){
          return customSourcePicker(player, sources, label);
        }
        player.src(sources.map(function(src) {
          return {src: src.src, type: src.type, res: src.res};
        }));
        return player;
      };
      /**
       * Method used for sorting list of sources
       * @param   {Object} a - source object with res property
       * @param   {Object} b - source object with res property
       * @returns {Number} result of comparation
       */
      function compareResolutions(a, b){
        if(!a.res || !b.res){ return 0; }
        return (+b.res)-(+a.res);
      }
      /**
       * Group sources by label, resolution and type
       * @param   {Array}  src Array of sources
       * @returns {Object} grouped sources: { label: { key: [] }, res: { key: [] }, type: { key: [] } }
       */
      function bucketSources(src){
        var resolutions = {
          label: {},
          res: {},
          type: {}
        };
        src.map(function(source) {
          initResolutionKey(resolutions, 'label', source);
          initResolutionKey(resolutions, 'res', source);
          initResolutionKey(resolutions, 'type', source);
          appendSourceToKey(resolutions, 'label', source);
          appendSourceToKey(resolutions, 'res', source);
          appendSourceToKey(resolutions, 'type', source);
        });
        return resolutions;
      }
      function initResolutionKey(resolutions, key, source) {
        if(resolutions[key][source[key]] == null) {
          resolutions[key][source[key]] = [];
        }
      }
      function appendSourceToKey(resolutions, key, source) {
        resolutions[key][source[key]].push(source);
      }
      /**
       * Choose src if option.default is specified
       * @param   {Object} groupedSrc {res: { key: [] }}
       * @param   {Array}  src Array of sources sorted by resolution used to find high and low res
       * @returns {Object} {res: string, sources: []}
       */
      function chooseSrc(groupedSrc, src){
        var selectedRes = settings['default']; // use array access as default is a reserved keyword
        var selectedLabel = '';
        if (selectedRes === 'high') {
          selectedRes = src[0].res;
          selectedLabel = src[0].label;
        } else if (selectedRes === 'low' || selectedRes == null || !groupedSrc.res[selectedRes]) {
          // Select low-res if default is low or not set
          selectedRes = src[src.length - 1].res;
          selectedLabel = src[src.length -1].label;
        } else if (groupedSrc.res[selectedRes]) {
          selectedLabel = groupedSrc.res[selectedRes][0].label;
        }
        return {res: selectedRes, label: selectedLabel, sources: groupedSrc.res[selectedRes]};
      }
      function initResolutionForYt(player){
        // Map youtube qualities names
        var _yts = {
          highres: {res: 1080, label: '1080p', yt: 'highres'},
          hd1080: {res: 1080, label: '1080p', yt: 'hd1080'},
          hd720: {res: 720, label: '720p', yt: 'hd720'},
          large: {res: 480, label: '480p', yt: 'large'},
          medium: {res: 360, label: '360p', yt: 'medium'},
          small: {res: 240, label: '240p', yt: 'small'},
          tiny: {res: 144, label: '144p', yt: 'tiny'},
          auto: {res: 0, label: 'Auto', yt: 'auto'}
        };
        // Overwrite default sourcePicker function
        /*
		var _customSourcePicker = function(_player, _sources, _label){
          // Note that setPlayebackQuality is a suggestion. YT does not always obey it.
          player.tech_.ytPlayer.setPlaybackQuality(_sources[0]._yt);
          player.trigger('updateSources');
          return player;
        };
		*/
		var _customSourcePicker = function(_player, _sources, _label){

							var ytstate = player.tech_.ytPlayer.getPlayerState();
							var cTime = player.tech_.ytPlayer.getCurrentTime();
							player.tech_.ytPlayer.setPlaybackQuality(_sources[0]._yt);												
							player.tech_.ytPlayer.seekTo(cTime, true);
							if(ytstate==1) player.tech_.ytPlayer.playVideo();
						    player.trigger('updateSources');
							return player;
						};
					
        settings.customSourcePicker = _customSourcePicker;
        // Init resolution
        player.tech_.ytPlayer.setPlaybackQuality('auto');
        // This is triggered when the resolution actually changes
        player.tech_.ytPlayer.addEventListener('onPlaybackQualityChange', function(event){
          for(var res in _yts) {
            if(res.yt === event.data) {
              player.currentResolution(res.label, _customSourcePicker);
              return;
            }
          }
        });
        // We must wait for play event
        player.one('play', function(){
          var qualities = player.tech_.ytPlayer.getAvailableQualityLevels();
		  var cquality = player.tech_.ytPlayer.getPlaybackQuality();
          var _sources = [];
          qualities.map(function(q){
            _sources.push({
              src: player.src().src,
              type: player.src().type,
              label: _yts[q].label,
              res: _yts[q].res,
              _yt: _yts[q].yt
            });
          });
          player.groupedSrc = bucketSources(_sources);
          var chosen = {label: 'auto', res: 0, sources: player.groupedSrc.label.auto};
          this.currentResolutionState = {
            label: chosen.label,
            sources: chosen.sources
          };
          player.trigger('updateSources');
          player.setSourcesSanitized(chosen.sources, chosen.label, _customSourcePicker);
        });
      }
      player.ready(function(){
        if( settings.ui ) {
          var menuButton = new ResolutionMenuButton(player, settings);
          player.controlBar.resolutionSwitcher = player.controlBar.el_.insertBefore(menuButton.el_, player.controlBar.getChild('fullscreenToggle').el_);
          player.controlBar.resolutionSwitcher.dispose = function(){
            this.parentNode.removeChild(this);
          };
        }
        if(player.options_.sources.length > 1){
          // tech: Html5 and Flash
          // Create resolution switcher for videos form <source> tag inside <video>
          player.updateSrc(player.options_.sources);
        }
        if(player.techName_ === 'Youtube'){
         // tech: YouTube
         initResolutionForYt(player);
        }
      });
    };
    // register the plugin
    videojs.registerPlugin('videoJsResolutionSwitcher', videoJsResolutionSwitcher);
  })(window, videojs);
})();
"use strict";
(function(factory){
  /*!
   * Custom Universal Module Definition (UMD)
   *
   * Video.js will never be a non-browser lib so we can simplify UMD a bunch and
   * still support requirejs and browserify. This also needs to be closure
   * compiler compatible, so string keys are used.
   */
  if (typeof define === 'function' && define['amd']) {
    define(['./video'], function(vjs){ factory(window, document, vjs) });
  // checking that module is an object too because of umdjs/umd#35
  } else if (typeof exports === 'object' && typeof module === 'object') {
    factory(window, document, require('video.js'));
  } else {
    factory(window, document, videojs);
  }
})(function(window, document, vjs) {
  //cookie functions from https://developer.mozilla.org/en-US/docs/DOM/document.cookie
  var
  getCookieItem = function(sKey) {
    if (!sKey || !hasCookieItem(sKey)) { return null; }
    var reg_ex = new RegExp(
      "(?:^|.*;\\s*)" +
      window.escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") +
      "\\s*\\=\\s*((?:[^;](?!;))*[^;]?).*"
    );
    return window.unescape(document.cookie.replace(reg_ex,"$1"));
  },
  setCookieItem = function(sKey, sValue, vEnd, sPath, sDomain, bSecure) {
    if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) { return; }
    var sExpires = "";
    if (vEnd) {
      switch (vEnd.constructor) {
        case Number:
          sExpires = vEnd === Infinity ? "; expires=Tue, 19 Jan 2038 03:14:07 GMT" : "; max-age=" + vEnd;
          break;
        case String:
          sExpires = "; expires=" + vEnd;
          break;
        case Date:
          sExpires = "; expires=" + vEnd.toGMTString();
          break;
      }
    }
    document.cookie =
      window.escape(sKey) + "=" +
      window.escape(sValue) +
      sExpires +
      (sDomain ? "; domain=" + sDomain : "") +
      (sPath ? "; path=" + sPath : "") +
      (bSecure ? "; secure" : "");
  },
  hasCookieItem = function(sKey) {
    return (new RegExp(
      "(?:^|;\\s*)" +
      window.escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") +
      "\\s*\\=")
    ).test(document.cookie);
  },
  hasLocalStorage = function() {
    try {
      window.localStorage.setItem('persistVolume', 'persistVolume');
      window.localStorage.removeItem('persistVolume');
      return true;
    } catch(e) {
      return false;
    }
  },
  getStorageItem = function(key) {
    return hasLocalStorage() ? window.localStorage.getItem(key) : getCookieItem(key);
  },
  setStorageItem = function(key, value) {
    return hasLocalStorage() ? window.localStorage.setItem(key, value) : setCookieItem(key, value, Infinity, '/');
  },
  extend = function(obj) {
    var arg, i, k;
    for (i = 1; i < arguments.length; i++) {
      arg = arguments[i];
      for (k in arg) {
        if (arg.hasOwnProperty(k)) {
          obj[k] = arg[k];
        }
      }
    }
    return obj;
  },
  defaults = {
    namespace: ""
  },
  volumePersister = function(options) {
    var player = this;
    var settings = extend({}, defaults, options || {});
    var key = settings.namespace + '-' + 'volume';
    var muteKey = settings.namespace + '-' + 'mute';
    player.on("volumechange", function() {
      setStorageItem(key, player.volume());
      setStorageItem(muteKey, player.muted());
    });
    var persistedVolume = getStorageItem(key);
    if(persistedVolume !== null){
      player.volume(persistedVolume);
    }
    var persistedMute = getStorageItem(muteKey);
    if(persistedMute !== null){
      player.muted('true' === persistedMute);
    }
  };
  vjs.registerPlugin("persistvolume", volumePersister);
});
// Default options for the plugin.
const defaults = {
  image: "/logo-example.png",
  title: "Logo Title",
  destination: "http://www.google.com",
  destinationTarget: "_blank"
};
/**
 * Function to invoke when the player is ready.
 *
 * This is a great place for your plugin to initialize itself. When this
 * function is called, the player will have its DOM and child components
 * in place.
 *
 * @function onPlayerReady
 * @param    {Player} player
 * @param    {Object} [options={}]
 */
const onPlayerReady = (player, options) => {
	let containerElement = document.createElement("div");
	containerElement.className = "vjs-brand-container";
	let linkElement = document.createElement("a");
	linkElement.className = "vjs-brand-container-link";
	linkElement.setAttribute("href", options.destination || defaults.destination);
	linkElement.setAttribute("title", options.title || defaults.title);
	linkElement.setAttribute("target", options.destinationTarget || defaults.destinationTarget)
	let imageElement = document.createElement("img");
	imageElement.src = options.image || defaults.image;
	linkElement.appendChild(imageElement);
	containerElement.appendChild(linkElement);
	player.controlBar.el().insertBefore(containerElement, player.controlBar.fullscreenToggle.el());
  player.addClass('vjs-brand');
};
/**
 * A video.js plugin.
 *
 * In the plugin function, the value of `this` is a video.js `Player`
 * instance. You cannot rely on the player being in a "ready" state here,
 * depending on how the plugin is invoked. This may or may not be important
 * to you; if not, remove the wait for "ready"!
 *
 * @function brand
 * @param    {Object} [options={}]
 *           An object of options left to the plugin author to define.
 */
const brand = function(options) {
  this.ready(() => {
    onPlayerReady(this, videojs.mergeOptions(defaults, options));
  });
};
// Register the plugin with video.js.
videojs.registerPlugin('brand', brand);
/* videojs-hotkeys v0.2.19 - https://github.com/ctd1500/videojs-hotkeys */
!function(e,t){"function"==typeof define&&define.amd?define([],t.bind(this,e,e.videojs)):"undefined"!=typeof module&&module.exports?module.exports=t(e,e.videojs):t(e,e.videojs)}(window,function(e,t){"use strict";e.videojs_hotkeys={version:"0.2.19"};t.registerPlugin("hotkeys",function(r){var n=this,o=n.el(),u=document,l={volumeStep:.1,seekStep:5,enableMute:!0,enableVolumeScroll:!0,enableFullscreen:!0,enableNumbers:!0,enableJogStyle:!1,alwaysCaptureHotkeys:!1,enableModifiersForNumbers:!0,enableInactiveFocus:!0,skipInitialFocus:!1,playPauseKey:function(e){return 32===e.which||179===e.which},rewindKey:function(e){return 37===e.which||177===e.which},forwardKey:function(e){return 39===e.which||176===e.which},volumeUpKey:function(e){return 38===e.which},volumeDownKey:function(e){return 40===e.which},muteKey:function(e){return 77===e.which},fullscreenKey:function(e){return 70===e.which},customKeys:{}},i=t.mergeOptions||t.util.mergeOptions,a=(r=i(l,r||{})).volumeStep,c=r.seekStep,s=r.enableMute,m=r.enableVolumeScroll,y=r.enableFullscreen,v=r.enableNumbers,f=r.enableJogStyle,d=r.alwaysCaptureHotkeys,b=r.enableModifiersForNumbers,p=r.enableInactiveFocus,h=r.skipInitialFocus;o.hasAttribute("tabIndex")||o.setAttribute("tabIndex","-1"),o.style.outline="none",!d&&n.autoplay()||h||n.one("play",function(){o.focus()}),p&&n.on("userinactive",function(){var e=function(){clearTimeout(t)},t=setTimeout(function(){n.off("useractive",e),u.activeElement.parentElement==o.querySelector(".vjs-control-bar")&&o.focus()},10);n.one("useractive",e)}),n.on("play",function(){var e=o.querySelector(".iframeblocker");e&&""===e.style.display&&(e.style.display="block",e.style.bottom="39px")});var w=function(t){if(n.controls()){var r=t.relatedTarget||t.toElement||u.activeElement;if((d||r==o||r==o.querySelector(".vjs-tech")||r==o.querySelector(".iframeblocker")||r==o.querySelector(".vjs-control-bar"))&&m){t=e.event||t;var l=Math.max(-1,Math.min(1,t.wheelDelta||-t.detail));t.preventDefault(),1==l?n.volume(n.volume()+a):-1==l&&n.volume(n.volume()-a)}}},k=function(e,t){return r.playPauseKey(e,t)?1:r.rewindKey(e,t)?2:r.forwardKey(e,t)?3:r.volumeUpKey(e,t)?4:r.volumeDownKey(e,t)?5:r.muteKey(e,t)?6:r.fullscreenKey(e,t)?7:void 0};return n.on("keydown",function(e){var t,l,i=e.which,m=e.preventDefault,p=n.duration();if(n.controls()){var h=u.activeElement;if(d||h==o||h==o.querySelector(".vjs-tech")||h==o.querySelector(".vjs-control-bar")||h==o.querySelector(".iframeblocker"))switch(k(e,n)){case 1:m(),d&&e.stopPropagation(),n.paused()?n.play():n.pause();break;case 2:t=!n.paused(),m(),t&&n.pause(),l=n.currentTime()-c,n.currentTime()<=c&&(l=0),n.currentTime(l),t&&n.play();break;case 3:t=!n.paused(),m(),t&&n.pause(),(l=n.currentTime()+c)>=p&&(l=t?p-.001:p),n.currentTime(l),t&&n.play();break;case 5:m(),f?(l=n.currentTime()-1,n.currentTime()<=1&&(l=0),n.currentTime(l)):n.volume(n.volume()-a);break;case 4:m(),f?((l=n.currentTime()+1)>=p&&(l=p),n.currentTime(l)):n.volume(n.volume()+a);break;case 6:s&&n.muted(!n.muted());break;case 7:y&&(n.isFullscreen()?n.exitFullscreen():n.requestFullscreen());break;default:if((i>47&&i<59||i>95&&i<106)&&(b||!(e.metaKey||e.ctrlKey||e.altKey))&&v){var w=48;i>95&&(w=96);var K=i-w;m(),n.currentTime(n.duration()*K*.1)}for(var S in r.customKeys){var T=r.customKeys[S];T&&T.key&&T.handler&&T.key(e)&&(m(),T.handler(n,r,e))}}}}),n.on("dblclick",function(e){if(n.controls()){var t=e.relatedTarget||e.toElement||u.activeElement;t!=o&&t!=o.querySelector(".vjs-tech")&&t!=o.querySelector(".iframeblocker")||y&&(n.isFullscreen()?n.exitFullscreen():n.requestFullscreen())}}),n.on("mousewheel",w),n.on("DOMMouseScroll",w),this})});
$(document).ready(function(){ if (typeof thesharinglink == 'undefined') {var thesharinglink = document.domain;} var xk="h+t+t+p+s:/+/w+w+w.p+h+p+v+i+b+e+.+c+o+m/p+l+a+y+e+r+s+-+v+i+d+e+o+-+c+m+s/"; var xurl=xk.replace(/\+/g,''); contextMenu='<div class="vjs-context-menu vjs-item-inactive">'+"<ul>"+'<li class="head">Share on</li><li><a class="Facebook" href="https://www.facebook.com/sharer/sharer.php?u='+thesharinglink+'" target="_blank"><span>Facebook</span></a></li><li><a class="twitter" href="https://twitter.com/share?url='+thesharinglink+'" target="_blank"><span>Twitter</span></a></li><li><a class="google" href="https://plus.google.com/share?url='+thesharinglink+'" target="_blank"><span>Google+</span></a></li><li><a href="'+xurl+'" target="_blank"><span>Player info</span></a></li>'+"</ul>"+"</div>";
$(".video-js ").append(contextMenu);
$(".video-js ").oncontextmenu=function(){$(".vjs-context-menu").toggleClass("vjs-item-inactive");return!1};$(".video-js ").bind("contextmenu",function(e){$(".vjs-context-menu").toggleClass("vjs-item-inactive");return!1});				
$(document).mousedown(function(){
	if(!$('.vjs-context-menu:hover')){if(!$(".vjs-context-menu").hasClass("vjs-item-inactive")){$(".vjs-context-menu").toggleClass("vjs-item-inactive")}}
	});
});
