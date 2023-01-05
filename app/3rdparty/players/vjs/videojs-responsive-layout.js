/* jshint esnext:true */
import videojs from 'video.js';
const debounce = require('throttle-debounce').debounce;

// Default options for the plugin.
const defaults = {
  debounceDelay: 200,
  layoutMap: [
    { layoutClassName: 'vjs-layout-tiny', width: 2},
    { layoutClassName: 'vjs-layout-x-small', width: 3},
    { layoutClassName: 'vjs-layout-small', width: 4},
    { layoutClassName: 'defaults', width: 5}
  ]
};

/**
 * Retrieve the outerWidth of an element, including margins
 *
 * @function getElementOuterWidth
 * @param    {Element} el to measure
 * @return   {number} the width of the element in pixels
 */
const getElementOuterWidth = function(el) {
  let width = el.offsetWidth;
  let style = getComputedStyle(el);

  width += parseInt(style.marginLeft, 10) + parseInt(style.marginRight, 10);
  return width;
};

/**
 * Retrieve the width an element
 *
 * @function getElementWidth
 * @param    {Element} el to measure
 * @return   {number} the width of the element in pixels
 */
const getElementWidth = function(el) {
  return parseInt(getComputedStyle(el).width, 10);
};

/**
 * Check if an element is currently visible.
 *
 * Use this to filter on elements that should be taken into account during calculations.
 *
 * @function isElementVisible
 * @param    {Element} el to test
 * @return   {boolean} true if el is visible
 */
const isElementVisible = function(el) {
  return (el.offsetWidth > 0 || el.offsetHeight > 0);
};

const dimensionsCheck = function() {
  /**
   * Set a layout class on a video-js element
   *
   * @function setLayout
   * @param    {Player} player to apply the layout to
   */
  const setLayout = function(layouter) {
    let el = layouter.player.el();
    let layoutDefinition = layouter.options.layoutMap[layouter.currentLayout_];

    if (layoutDefinition.layoutClassName !== 'defaults') {
      videojs.addClass(el, layoutDefinition.layoutClassName);
    }
    layouter.options.layoutMap.forEach(function(element, index) {
      if (index !== layouter.currentLayout_ && element.layoutClassName !== 'defaults') {
        videojs.removeClass(el, element.layoutClassName);
      }
    });
  };

  /**
   * Calculate for the giving dimensions which layout class of the layoutMap should be
   * used
   *
   * @function setLayout
   * @param    {Player} player to apply the layout to
   */
  const calculateLayout = function(layouter, playerWidth, controlBarWidth, controlWidth) {
    let layoutMap = layouter.options.layoutMap;

    if (controlBarWidth > playerWidth && layouter.currentLayout_ > 0) {
      // smaller
      layouter.currentLayout_--;
      setLayout(layouter);
      window.setTimeout(dimensionsCheck.bind(layouter), 1);
    } else if (layouter.currentLayout_ < layoutMap.length - 1 &&
      playerWidth >= layoutMap[layouter.currentLayout_ + 1].width * controlWidth
    ) {
      // bigger
      layouter.currentLayout_++;
      setLayout(layouter);
      window.setTimeout(dimensionsCheck.bind(layouter), 1);
    }
  };

  if (!this.el || this.player.usingNativeControls() ||
    !isElementVisible(this.el.querySelectorAll('.vjs-control-bar')[0])
  ) {
    return;
  }
  let playerWidth = this.getPlayerWidth();
  let controlWidth = this.getControlWidth();
  let controlBarWidth = this.getControlBarWidth();

  if (this.options.calculateLayout) {
    this.options.calculateLayout(this, playerWidth, controlBarWidth, controlWidth);
  } else {
    calculateLayout(this, playerWidth, controlBarWidth, controlWidth);
  }
};

class Layouter {
  constructor(player, options) {
    this.player_ = player;
    this.options_ = options;
    this.currentLayout_ = options.layoutMap.length - 1;
    this.debouncedCheckSize_ = debounce(options.debounceDelay, dimensionsCheck);
  }

  ready() {
    this.player.addClass('vjs-responsive-layout');

    this.windowResizeListener_ = window.addEventListener(
      'resize',
      () => this.debouncedCheckSize_()
    );

    this.player.on(['play', 'resize'], () => this.debouncedCheckSize_());
    this.player.on('dispose', function() {
      window.removeEventListener('resize', this.windowResizeListener_);
    });

    // Let's do the first measure
    this.player.trigger('resize');
  }

  /**
   * Retrieve player to which this Layouter object belongs
   *
   * @property player
   * @return   {number} the width of the controlbar in pixels
   */
  get player() {
    return this.player_;
  }

  get el() {
    return this.player_.el();
  }

  get options() {
    return this.options_;
  }

  /**
   * Retrieve current width of a control in the video.js controlbar
   *
   * This function relies on the presence of the play control. If you
   * mess with it's visibility, things likely will break :)
   *
   * @function getControlWidth
   * @return   {number} the width of the controlbar in pixels
   */
  getControlWidth() {
    return getElementOuterWidth(this.el.querySelectorAll('.vjs-play-control')[0]);
  }

  /**
   * Retrieve current width of the video.js controlbar
   *
   * @function getControlBarWidth
   * @return   {number} the width of the controlbar in pixels
   */
  getControlBarWidth() {
    let controlBarWidth = 0;
    let cbElements = this.el.querySelectorAll('.vjs-control-bar > *');

    Array.from(cbElements).forEach(function(el) {
      if (isElementVisible(el)) {
        controlBarWidth += getElementOuterWidth(el);
      }
    });
    return controlBarWidth;
  }

  /**
   * Retrieve current width of the video.js player element
   *
   * @function getPlayerWidth
   * @return   {number} the width of the player in pixels
   */
  getPlayerWidth() {
    return getElementWidth(this.el);
  }

  /**
   * Retrieve the outerWidth of an element, including margins
   *
   * @function outerWidth
   * @param    {Element} el to measure
   * @return   {number} the width of the element in pixels
   */
  static getElementOuterWidth(el) {
    return getElementOuterWidth(el);
  }

  /**
   * Retrieve the width an element
   *
   * @function getElementWidth
   * @param    {Element} el to measure
   * @return   {number} the width of the element in pixels
   */
  static getElementWidth(el) {
    return getElementWidth(el);
  }

  /**
   * Check if an element is currently visible.
   *
   * Use this to filter on elements that should be taken into account during calculations.
   *
   * @function isElementVisible
   * @param    {Element} el to test
   * @return   {boolean} true if el is visible
   */
  static isElementVisible(el) {
    return isElementVisible(el);
  }
}

/**
 * A video.js plugin.
 *
 * In the plugin function, the value of `this` is a video.js `Player`
 * instance. You cannot rely on the player being in a "ready" state here,
 * depending on how the plugin is invoked. This may or may not be important
 * to you; if not, remove the wait for "ready"!
 *
 * @function responsiveLayout
 * @param    {Object} [options={}]
 *           An object of options left to the plugin author to define.
 */
const responsiveLayout = function(options) {
  let layout = new Layouter(this, videojs.mergeOptions(defaults, options));

  this.ready(() => {
    layout.ready();
  });
};

// Register the plugin with video.js.
videojs.plugin('responsiveLayout', responsiveLayout);

export default responsiveLayout;
