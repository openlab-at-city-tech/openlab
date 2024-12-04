/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/theme/js/kube/slideshow/customizable-slideshow.js":
/*!**********************************************************************!*\
  !*** ./assets/src/theme/js/kube/slideshow/customizable-slideshow.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ CustomizableSlideshow)
/* harmony export */ });
/* harmony import */ var _kubio_scripts_src_background_slideshow_slideshow__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @kubio/scripts/src/background/slideshow/slideshow */ "./kubio-plugin/src/packages/scripts/src/background/slideshow/slideshow.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var CustomizableSlideshow = /*#__PURE__*/function (_Slideshow) {
  _inherits(CustomizableSlideshow, _Slideshow);

  var _super = _createSuper(CustomizableSlideshow);

  function CustomizableSlideshow() {
    _classCallCheck(this, CustomizableSlideshow);

    return _super.apply(this, arguments);
  }

  _createClass(CustomizableSlideshow, [{
    key: "start",
    value: function start() {
      if (!this.customizerBinded && typeof wp !== 'undefined') {
        this.opts = jQuery.extend(true, {}, this.opts, Colibri.getData(this.opts.kubioId) || {});
        this.wpCustomize(wp.customize);
        this.customizerBinded = true;
      }

      _get(_getPrototypeOf(CustomizableSlideshow.prototype), "start", this).call(this);
    }
  }, {
    key: "wpCustomize",
    value: function wpCustomize(api) {
      var _this = this;

      var _loop = function _loop(opt) {
        if (_this.opts.wpSettings.hasOwnProperty(opt)) {
          var setting = _this.opts.wpSettings[opt];

          _this.wpSettingBind(setting, function (newValue) {
            _this.opts[opt] = parseInt(newValue).toString();

            _this.stop();

            setTimeout(function () {
              _this.start();
            }, 100);
          });
        }
      };

      for (var opt in this.opts.wpSettings) {
        _loop(opt);
      }
    }
  }, {
    key: "wpSettingBind",
    value: function wpSettingBind(setting_id, callback) {
      window.wp.customize(setting_id, function (setting) {
        setting.bind(callback);
      });
    }
  }]);

  return CustomizableSlideshow;
}(_kubio_scripts_src_background_slideshow_slideshow__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./assets/src/theme/js/kube/video-background/customizable-video-background.js":
/*!************************************************************************************!*\
  !*** ./assets/src/theme/js/kube/video-background/customizable-video-background.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ CustomizableVideoBackground)
/* harmony export */ });
/* harmony import */ var _kubio_scripts_src_background_video_video_bg__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @kubio/scripts/src/background/video/video-bg */ "./kubio-plugin/src/packages/scripts/src/background/video/video-bg.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var CustomizableVideoBackground = /*#__PURE__*/function (_VideoBackground) {
  _inherits(CustomizableVideoBackground, _VideoBackground);

  var _super = _createSuper(CustomizableVideoBackground);

  function CustomizableVideoBackground(element, options) {
    var _this;

    _classCallCheck(this, CustomizableVideoBackground);

    _this = _super.call(this, element, options);
    _this.opts.wpSettings = Colibri.getData(_this.opts.kubioId) || {};
    return _possibleConstructorReturn(_this, _assertThisInitialized(_this));
  }

  _createClass(CustomizableVideoBackground, [{
    key: "wpCustomize",
    value: function wpCustomize(api) {
      var _this2 = this;

      this.API_URL = colibri_ADDITIONAL_JS_DATA.api_url;

      var _loop = function _loop(opt) {
        if (_this2.opts.wpSettings.hasOwnProperty(opt)) {
          var setting = _this2.opts.wpSettings[opt];

          _this2.wpSettingBind(setting, function (newValue) {
            if (opt === "externalUrl") {
              _this2.restartYouTubeVideo(newValue);
            }

            if (opt === "internalUrl") {
              _this2.restartSelfHostedVideo(newValue);
            }

            if (opt === "videoType") {
              var videoType = "native";
              if (newValue === "external") videoType = "youtube";

              _this2.changeProvider(videoType);
            }

            if (opt === "posterUrl") {
              _this2.$element.css({
                backgroundImage: "url(".concat(newValue, ")")
              });

              _this2.videoData.poster = newValue;
            }
          });
        }
      };

      for (var opt in this.opts.wpSettings) {
        _loop(opt);
      }
    }
  }, {
    key: "changeProvider",
    value: function changeProvider(newValue) {
      if (newValue === "youtube") {
        this.restartYouTubeVideo(wp.customize(this.opts.wpSettings['externalUrl']).get());
      } else {
        this.restartSelfHostedVideo(wp.customize(this.opts.wpSettings['internalUrl']).get());
      }
    }
  }, {
    key: "restartYouTubeVideo",
    value: function restartYouTubeVideo(value) {
      this.videoData.videoUrl = value;
      this.videoData.mimeType = "video/x-youtube";

      _get(_getPrototypeOf(CustomizableVideoBackground.prototype), "generateVideo", this).call(this);
    }
  }, {
    key: "restartSelfHostedVideo",
    value: function restartSelfHostedVideo(value) {
      var _this3 = this;

      if (!value) {
        this.videoData.videoUrl = "";
        this.videoData.mimeType = "video/mp4";

        _get(_getPrototypeOf(CustomizableVideoBackground.prototype), "generateVideo", this).call(this);
      } else {
        this.$.getJSON("".concat(this.API_URL, "/attachment-data/").concat(value), function (data) {
          _this3.videoData.videoUrl = data.url;
          _this3.videoData.mimeType = data.mime_type;

          _get(_getPrototypeOf(CustomizableVideoBackground.prototype), "generateVideo", _this3).call(_this3);
        });
      }
    }
  }]);

  return CustomizableVideoBackground;
}(_kubio_scripts_src_background_video_video_bg__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./assets/src/theme/js/menu/accordion-menu.js":
/*!****************************************************!*\
  !*** ./assets/src/theme/js/menu/accordion-menu.js ***!
  \****************************************************/
/***/ (() => {

(function ($, Colibri) {
  var className = "accordion-menu";

  var Component = function Component(element, options) {
    this.namespace = className;
    this.defaults = {
      menuSelector: ".kubio-menu",
      offCanvasWrapper: ".kubio-menu-container",
      linkSelector: ".menu-item-has-children > a, .page_item_has_children > a",
      linkLeafsSelector: "li:not(.menu-item-has-children):not(.page_item_has_children) > a",
      arrowSelector: "svg",
      $menu: null
    }; // Parent Constructor

    Colibri.apply(this, arguments); // Initialization

    this.initBindedFunctions();
    this.initEventListenersData();
    this.start();
  };

  Component.prototype = {
    start: function start() {
      var $menu = this.$element.find(this.opts.menuSelector).first();
      this.opts.$menu = $menu;
      var firstPageLoadItem = $menu.find("> ul > li.current-menu-item").get(0);
      this.opts.$menu.find("a").data("allow-propagation", true);
      this.opts.$menu.find(this.opts.arrowSelector).attr("tabIndex", 0);
      this.removeEventListeners();
      this.addEventListeners();
      this.addMenuScrollSpy($menu, firstPageLoadItem);
      var openedParent = this.opts.$menu.find(".current-menu-parent").first();

      if (openedParent.length) {
        this.openDropDown(openedParent);
      }

      this.addFocusListener();
    },
    initBindedFunctions: function initBindedFunctions() {
      this.debounceApplyDropdownLogic = $.debounce(this.applyDropdownLogic.bind(this), 10);
      this.bindedLinkEventHandler = this.linkEventHandler.bind(this);
      this.bindedLinkArrowEventHandler = this.linkArrowEventHandler.bind(this);
    },
    initEventListenersData: function initEventListenersData() {
      var menuNamespace = ".accordion-menu";
      var events = ["click", "tap"];
      var eventBase = events.map(function (event) {
        return "".concat(event).concat(menuNamespace);
      });
      var linkSelectorEvent = eventBase.map(function (item) {
        return item + ".link-selector";
      }).join(" ");
      var arrowSelectorEvent = eventBase.concat(["keyup".concat(menuNamespace)]).map(function (item) {
        return item + " svg";
      }).join(" ");
      var offCanvasEvent = eventBase.map(function (item) {
        return item + ".off-canvas";
      }).join(" ");
      this._eventOptions = {
        menuNamespace: menuNamespace,
        linkSelectorEvent: linkSelectorEvent,
        arrowSelectorEvent: arrowSelectorEvent,
        offCanvasEvent: offCanvasEvent
      };
    },
    toggleFocus: function toggleFocus(item) {
      var enable = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      while (this.opts.$menu[0] !== item) {
        if ("li" === item.tagName.toLowerCase()) {
          if (!enable) {
            $(item).closest("li.menu-item-has-children").children("a").removeClass("hover");
            item.classList.remove("hover");
            $(item).children("a").removeClass("hover");
          } else {
            $(item).closest("li.menu-item-has-children").children("a").addClass("hover");
            item.classList.add("hover");
            $(item).children("a").addClass("hover");
          }
        }

        item = item.parentElement;
      }
    },
    addFocusListener: function addFocusListener() {
      var _this = this;

      var links = this.opts.$menu.find("a");
      links.on("focus", function (event) {
        _this.toggleFocus(event.currentTarget);
      });
      links.on("blur", function (event) {
        _this.toggleFocus(event.currentTarget, false);
      });
    },
    addEventListeners: function addEventListeners() {
      var $menu = this.opts.$menu;
      var eventOptions = this._eventOptions;
      $menu.on(eventOptions.arrowSelectorEvent, this.opts.arrowSelector, this.bindedLinkArrowEventHandler);

      if (window.wp && window.wp.customize) {
        $menu.off(eventOptions.linkSelectorEvent, this.opts.linkSelector);
      }

      $menu.on(eventOptions.linkSelectorEvent, this.opts.linkSelector, this.bindedLinkEventHandler);
      $menu.on(eventOptions.offCanvasEvent, this.opts.linkLeafsSelector, this.closeOffcanvasPanel);
      $(document).on("keyup." + this.namespace, $.proxy(this.handleKeyboard, this));
    },
    removeEventListeners: function removeEventListeners() {
      var $menu = this.opts.$menu;
      var eventOptions = this._eventOptions;
      $menu.off(eventOptions.menuNamespace);
      $(document).on("keyup." + this.namespace);
    },
    stop: function stop() {
      this.removeEventListeners();
      this.removeAllSvgArrows();
    },
    handleKeyboard: function handleKeyboard(e) {
      var item = e.target;

      if (item.tagName.toLowerCase() === "svg") {
        item = item.parentNode;
      }

      if ($.contains(this.opts.$menu[0], item)) {
        if ($(item).siblings("ul").length) {
          if (e.which === 37) {
            this.closeDropDown($(item).closest("li"));
          }

          if (e.which === 39) {
            this.openDropDown($(item).closest("li"));
          }
        }
      }
    },
    openDropDown: function openDropDown(item) {
      if (!item) {
        return;
      }

      if ($(item).is("a")) {
        item = $(item).closest("li");
      } else {
        item = $(item);
      }

      item.addClass("open");
      item.children("ul").slideDown(100);
    },
    closeDropDown: function closeDropDown(item) {
      if (!item) {
        return;
      }

      if ($(item).is("a")) {
        item = $(item).closest("li");
      } else {
        item = $(item);
      }

      item.removeClass("open");
      item.children("ul").slideUp(100);
    },
    isDropDownOpen: function isDropDownOpen($parent) {
      return $parent.is(".open");
    },
    closeOffcanvasPanel: function closeOffcanvasPanel() {
      if (window.wp && window.wp.customize) {}
      /*
      //some mobile menus do not work without this timeout, because the panel gets hidden before the link logic happens
      //and some browser stop the link for security reasons because it got fired from hidden elements.
      setTimeout(() => {
      $('.offscreen-overlay').trigger('click');
      }, 500);
      */

    },
    linkEventHandler: function linkEventHandler(event, isForArrow) {
      var inCustomizer = window.wp && window.wp.customize;

      if (inCustomizer) {
        event.preventDefault();
      }

      var $this = $(event.target);
      var $li = $this.closest("li");
      var hasChildren = $li.find("ul").length !== 0;

      if (!hasChildren) {
        this.closeOffcanvasPanel();
        return;
      }

      if (!isForArrow && $li.hasClass("open") && !inCustomizer) {
        this.closeOffcanvasPanel();
        return;
      } //when the arrows are clicked the link should not redirect you, or when the item li is not opened. also stop
      //propagation to the link event handler


      if (isForArrow || !isForArrow && !$li.hasClass("open")) {
        event.preventDefault(); // do not trigger bubbling events e.g for offcanvas

        event.stopPropagation();
      } // event.stopPropagation();

      /**
       * For mobile devices the event handler function is called two times one for the click event and the other time for
       * tap event. Because of this we had to split the logic in things that needs to be called for every call and things
       * that needs to be called once when the tap/click events are called at the same time. We use the debounce function
       * to apply the dropdown logic only once
       */


      this.debounceApplyDropdownLogic(event, isForArrow);
    },
    linkArrowEventHandler: function linkArrowEventHandler(event) {
      if (event.type === "keyup") {
        // is pressed enter
        if (event.which === 13) {
          this.linkEventHandler(event, true);
        }
      } else {
        this.linkEventHandler(event, true);
      }
    },
    applyDropdownLogic: function applyDropdownLogic(event, isForArrow) {
      var $this = $(event.target);
      var $li = $this.closest("li");
      var hasChildren = $li.find("ul").length !== 0;

      if (!hasChildren) {
        this.closeOffcanvasPanel();
        return;
      }

      if (isForArrow && this.isDropDownOpen($li)) {
        this.closeDropDown($li);
      } else {
        this.openDropDown($li);
      }
    },
    removeAllSvgArrows: function removeAllSvgArrows() {
      if (this.opts.$menu) {
        this.opts.$menu.find(this.opts.arrowSelector).remove();
      }
    },
    addMenuScrollSpy: function addMenuScrollSpy(startFrom, firstPageLoadItem) {
      var $menu = startFrom;
      var _offset = 20;
      var component = this;

      if ($.fn.kubioScrollSpy) {
        var linkSelector = component.opts.linkSelector;
        var arrowSelector = component.opts.arrowSelector;
        $menu.find("a").not(linkSelector).not(arrowSelector).kubioScrollSpy({
          onChange: function onChange() {
            $menu.find(".current-menu-item,.current_page_item").removeClass("current-menu-item current_page_item");
            $(this).closest("li").addClass("current-menu-item current_page_item");
          },
          onLeave: function onLeave() {
            $(this).closest("li").removeClass("current-menu-item current_page_item");

            if (!$menu.find(".current-menu-item, .current_page_item").length && firstPageLoadItem) {
              $(firstPageLoadItem).addClass("current-menu-item current_page_item");
            }
          },
          clickCallback: function clickCallback() {
            component.closeOffcanvasPanel();
          },
          smoothScrollAnchor: true,
          offset: function offset() {
            var $fixed = $menu.closest('[data-kubio-component="navigation"]');

            if ($fixed.length) {
              return $fixed[0].getBoundingClientRect().height + _offset;
            }

            return _offset;
          }
        });
      }

      $(window).trigger("smoothscroll.update");
    }
  };
  Component.inherits(Colibri);
  Colibri[className] = Component;
  Colibri.Plugin.create(className);
  Colibri.Plugin.autoload(className); // eslint-disable-next-line no-undef
})(jQuery, Colibri);

/***/ }),

/***/ "./assets/src/theme/js/menu/dropdown-menu.js":
/*!***************************************************!*\
  !*** ./assets/src/theme/js/menu/dropdown-menu.js ***!
  \***************************************************/
/***/ (() => {

(function ($, Colibri) {
  var className = "dropdown-menu";

  var Component = function Component() {
    this.namespace = className;
    this.defaults = {
      menuSelector: ".kubio-menu",
      $menu: null
    }; // Parent Constructor

    Colibri.apply(this, arguments); // Initialization

    this.start();
  };

  Component.prototype = {
    start: function start() {
      var $menu = this.$element.find(this.opts.menuSelector).first();
      this.opts.$menu = $menu;
      var firstPageLoadItem = $menu.find("> ul > li.current-menu-item").get(0);
      this.stop();
      this.addListener();
      this.addFocusListener();
      this.addReverseMenuLogic();
      /** TODO @catalin table menu logic needs work because it does not work*/

      this.addTabletMenuLogic();
      this.addMenuScrollSpy($menu, firstPageLoadItem);
    },
    toggleFocus: function toggleFocus(item) {
      var enable = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      while (this.opts.$menu[0] !== item) {
        if ("li" === item.tagName.toLowerCase()) {
          if (!enable) {
            $(item).closest("li.menu-item-has-children").children("a").removeClass("hover");
            item.classList.remove("hover");
            $(item).children("a").removeClass("hover");
          } else {
            $(item).closest("li.menu-item-has-children").children("a").addClass("hover");
            item.classList.add("hover");
            $(item).children("a").addClass("hover");
          }
        }

        item = item.parentElement;
      }
    },
    addFocusListener: function addFocusListener() {
      var _this = this;

      var lis = this.opts.$menu.find("li");
      lis.on("mouseover", function (event) {
        _this.toggleFocus(event.currentTarget);
      });
      lis.on("mouseout", function (event) {
        _this.toggleFocus(event.currentTarget, false);
      });
      var links = this.opts.$menu.find("li > a"); /// keyboard focus / blur

      links.on("focus", function (event) {
        _this.toggleFocus(event.currentTarget);
      });
      links.on("blur", function (event) {
        _this.toggleFocus(event.currentTarget, false);
      });
    },
    stop: function stop() {
      this.removeListeners();
    },
    copyLiEventTaA: function copyLiEventTaA(e) {
      var tagName = "";

      if (e.target && e.target.tagName) {
        tagName = e.target.tagName;
      }

      if (tagName.toLowerCase() === "a") {
        return;
      }

      var a = $(e.currentTarget).find("> a");
      a[0].click();
    },
    addListener: function addListener() {
      this.opts.$menu.find("li").on("click", this.copyLiEventTaA);
    },
    removeListeners: function removeListeners() {
      var $menu = this.opts.$menu;
      $menu.off("mouseover.navigation");
      $menu.find("li").off("click", this.copyLiEventTaA);
      this.removeTabletLogic();
    },
    removeTabletLogic: function removeTabletLogic() {
      var $menu = this.opts.$menu;
      $menu.off("tap.navigation");
    },
    addReverseMenuLogic: function addReverseMenuLogic() {
      var $menu = this.opts.$menu;
      var self = this;
      $menu.on("mouseover.navigation", "li", function () {
        $menu.find("li.hover").removeClass("hover");
        self.setOpenReverseClass($menu, $(this));
      });
    },
    setOpenReverseClass: function setOpenReverseClass($menu, $item) {
      // level 0 - not in dropdown
      if (this.getItemLevel($menu, $item) > 0) {
        var $submenu = $item.children("ul");
        var subItemDoesNotFit = $submenu.length && $item.offset().left + $item.width() + 300 > window.innerWidth;
        var parentsAreReversed = $submenu.length && $item.closest(".open-reverse").length;

        if (subItemDoesNotFit || parentsAreReversed) {
          $submenu.addClass("open-reverse");
        } else if ($submenu.length) {
          $submenu.removeClass("open-reverse");
        }
      }
    },
    getItemLevel: function getItemLevel($menu, $item) {
      var menuSelector = this.opts.menuSelector;
      var temp2 = $item.parentsUntil(menuSelector);
      var temp = temp2.filter("li");
      return temp.length;
    },
    addTabletMenuLogic: function addTabletMenuLogic() {
      var self = this;
      var $menu = this.opts.$menu;

      if (!this.opts.clickOnLink) {
        this.opts.clickOnLink = this.clickOnLink.bind(this);
      }

      if (!this.opts.clickOnArrow) {
        this.opts.clickOnArrow = this.clickOnArrow.bind(this);
      }

      $menu.off("tap.navigation", this.opts.clickOnArrow);
      $menu.on("tap.navigation", "li.menu-item > a svg", this.opts.clickOnArrow);
      $menu.off("tap.navigation", this.opts.clickOnLink);
      $menu.on("tap.navigation", "li.menu-item > a", this.opts.clickOnLink);
    },
    clickOnLink: function clickOnLink(event) {
      var arrowWasClicked = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var self = this;
      var $this = $(event.target);
      var $item = $this.closest("li");
      var $link = $this.closest("a");
      var $menu = this.opts.$menu;
      var $submenu = $item.children("ul");

      if ($submenu.length) {
        if (self.isSelectedItem($item)) {
          var href = $link.attr("href"); // do nothing if nothing

          if (href.indexOf("#") === 0) {
            var anchor = href.replace("#", "").trim();

            if (!anchor || !$("#" + anchor).length) {
              return;
            }
          }

          event.stopPropagation();

          if (arrowWasClicked) {
            event.preventDefault();
          }

          self.deselectItems($menu, $item);
        } else {
          event.stopPropagation();
          event.preventDefault();
          self.selectItem($menu, $item);
        }
      } else {
        event.stopPropagation();

        if (arrowWasClicked || !arrowWasClicked && self.isSelectedItem($item)) {
          event.preventDefault();
        }

        self.deselectItems($menu, $item);
      }
    },
    clickOnArrow: function clickOnArrow(event) {
      this.clickOnLink(event, true);
    },
    selectItem: function selectItem($menu, $item) {
      this.deselectItems($menu, $item);
      $item.attr("data-selected-item", true);
      this.clearMenuHovers($menu, $item);
      $item.addClass("hover");
      this.setOpenReverseClass($menu, $item);
      var self = this;
      $("body").on("tap.navigation-clear-selection", "*", function () {
        var $this = jQuery(this);
        self.clearSelectionWhenTapOutside($this, $menu);
      });
      $(window).on("scroll.navigation-clear-selection", function () {
        var $this = jQuery(this);
        self.clearSelectionWhenTapOutside($this, $menu);
      });
    },
    deselectItems: function deselectItems($menu, $item) {
      $item.removeClass("hover");
      $menu.find("[data-selected-item]").each(function () {
        var $itemTmp = $(this);
        $itemTmp.removeAttr("data-selected-item");
        var $submenu = $menu.children("ul"); //TODO @catalin, check if this mobile menu code is needed

        if ($menu.is(".mobile-menu")) {
          $submenu.slideDown();
        }
      });
    },
    isSelectedItem: function isSelectedItem($item) {
      return $item.is("[data-selected-item]");
    },
    clearMenuHovers: function clearMenuHovers($menu, except) {
      var self = this;
      $menu.find("li.hover").each(function () {
        if (except && self.containsSelectedItem($(this))) {
          return;
        }

        $(this).removeClass("hover");
      });
    },
    containsSelectedItem: function containsSelectedItem($item) {
      return $item.find("[data-selected-item]").length > 0 || $item.is("[data-selected-item]");
    },
    clearSelectionWhenTapOutside: function clearSelectionWhenTapOutside($this, $menu) {
      $("body").off("tap.navigation-clear-selection");
      $(window).off("scroll.navigation-clear-selection");

      if ($this.is($menu) || $.contains($menu[0], this)) {
        return;
      }

      this.clearMenuHovers($menu);
    },
    addMenuScrollSpy: function addMenuScrollSpy(startFrom, firstPageLoadItem) {
      var $menu = startFrom;

      if ($.fn.kubioScrollSpy) {
        $menu.find("a").kubioScrollSpy({
          onChange: function onChange() {
            $menu.find(".current-menu-item, .current_page_item").removeClass("current-menu-item current_page_item");
            $(this).closest("li").addClass("current-menu-item current_page_item");
          },
          onLeave: function onLeave() {
            var $fixed = $menu.closest(".h-navigation_sticky");
            $(this).closest("li").removeClass("current-menu-item current_page_item hover");

            if (!$menu.find(".current-menu-item, .current_page_item").length && firstPageLoadItem) {
              if (!$fixed) {
                $menu.find(".current-menu-item, .current_page_item").removeClass("current-menu-item current_page_item");
              }

              $(firstPageLoadItem).addClass("current-menu-item current_page_item");
            }
          },
          smoothScrollAnchor: true,
          offset: function offset() {
            //offset is needed only for sticky menu
            var $fixed = $menu.closest(".h-navigation_sticky");

            if ($fixed.length) {
              return $fixed[0].getBoundingClientRect().height;
            }

            return 0;
          }
        });
      }

      $(window).trigger("smoothscroll.update");
    }
  };
  Component.inherits(Colibri);
  Colibri[className] = Component;
  Colibri.Plugin.create(className);
  Colibri.Plugin.autoload(className); // eslint-disable-next-line no-undef
})(jQuery, Colibri);

/***/ }),

/***/ "./assets/src/theme/js/menu/index.js":
/*!*******************************************!*\
  !*** ./assets/src/theme/js/menu/index.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _dropdown_menu__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./dropdown-menu */ "./assets/src/theme/js/menu/dropdown-menu.js");
/* harmony import */ var _dropdown_menu__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_dropdown_menu__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _accordion_menu__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./accordion-menu */ "./assets/src/theme/js/menu/accordion-menu.js");
/* harmony import */ var _accordion_menu__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_accordion_menu__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _offcanvas__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./offcanvas */ "./assets/src/theme/js/menu/offcanvas.js");
/* harmony import */ var _offcanvas__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_offcanvas__WEBPACK_IMPORTED_MODULE_2__);




/***/ }),

/***/ "./assets/src/theme/js/menu/offcanvas.js":
/*!***********************************************!*\
  !*** ./assets/src/theme/js/menu/offcanvas.js ***!
  \***********************************************/
/***/ (() => {

(function ($, Colibri) {
  var className = "offcanvas";

  var Component = function Component(element, options) {
    this.namespace = "offcanvas";
    this.defaults = {
      target: null,
      // selector
      push: true,
      // boolean
      width: "250px",
      // string
      direction: "left",
      // string: left or right
      toggleEvent: "click",
      clickOutside: true,
      // boolean
      animationOpen: "slideInLeft",
      animationClose: "slideOutLeft",
      callbacks: ["open", "opened", "close", "closed"],
      offcanvasOverlayId: null,
      $overlayElement: null,
      targetId: null
    }; // Parent Constructor

    Colibri.apply(this, arguments); // Services

    this.utils = new Colibri.Utils();
    this.detect = new Colibri.Detect(); // Initialization

    this.start();
  }; // Functionality


  Component.prototype = {
    start: function start() {
      if (!this.hasTarget()) {
        return;
      }

      var overlayId = this.opts.offcanvasOverlayId;
      this.opts.$overlayElement = $("#" + overlayId); // this.stop();

      this.buildTargetWidth();
      this.buildAnimationDirection();
      this.$close = this.getCloseLink();
      this.$element.on(this.opts.toggleEvent + "." + this.namespace, $.proxy(this.toggle, this));
      this.$target.addClass("offcanvas");
      this.$target.trigger("kubio.offcanvas.ready");
      this.moveOffcanvas();
      this.addOffcanvasOverlayLogic();
    },
    stop: function stop() {
      this.closeAll();
      this.removeOffcanvasElements();
      this.$element.off("." + this.namespace);

      if (this.$close) {
        this.$close.off("." + this.namespace);
      }

      $(document).off("." + this.namespace);
    },
    removeOffcanvasElements: function removeOffcanvasElements() {
      // var targetId = this.opts.targetId;
      // var $targetElement = $('#' + targetId + '.h-offcanvas-panel');
      this.$target.remove();
      this.opts.$overlayElement.remove(); // if ($targetElement && $targetElement.length > 0) {
      //   for (var i = 0; i < $targetElement.length; i++) {
      //     var offcanvasPanel = $targetElement[i];
      //     var offcanvasPanelParent = offcanvasPanel.parentNode;
      //     if (offcanvasPanelParent && offcanvasPanelParent.tagName === 'BODY') {
      //       offcanvasPanelParent.removeChild(offcanvasPanel);
      //     }
      //   }
      // }
      //
      // var overlayElements = this.opts.$overlayElement;
      // if (overlayElements && overlayElements.length > 0) {
      //   for (var j = 0; j < overlayElements.length; j++) {
      //     var overlayElement = overlayElements[j];
      //     var overlayElementParent = overlayElement.parentNode;
      //     if (overlayElementParent && overlayElementParent.tagName === 'BODY') {
      //       overlayElementParent.removeChild(overlayElement);
      //     }
      //   }
      // }
    },
    moveOffcanvas: function moveOffcanvas() {
      var offcanvasPanel = this.$target[0];
      document.querySelector("html > body").appendChild(offcanvasPanel);
      var overlayElement = this.opts.$overlayElement[0];
      document.querySelector("html > body").appendChild(overlayElement);
    },
    addOffcanvasOverlayLogic: function addOffcanvasOverlayLogic() {
      var $overlayElement = this.opts.$overlayElement;
      var $offCanvasWrapper = this.$target;

      if ($offCanvasWrapper.length) {
        $overlayElement.on("scroll touchmove mousewheel", function (e) {
          e.preventDefault();
          e.stopPropagation();
          return false;
        });
        $offCanvasWrapper.on("kubio.offcanvas.open", function () {
          $overlayElement.addClass("h-offcanvas-opened");
        });
        $offCanvasWrapper.on("kubio.offcanvas.close", function () {
          $overlayElement.removeClass("h-offcanvas-opened");
        });
      }
    },
    toggle: function toggle(e) {
      if (this.isOpened()) {
        this.close(e);
      } else {
        this.open(e);
      }
    },
    buildTargetWidth: function buildTargetWidth() {
      this.opts.width = $(window).width() < parseInt(this.opts.width) ? "100%" : this.opts.width;
    },
    buildAnimationDirection: function buildAnimationDirection() {
      if (this.opts.direction === "right") {
        this.opts.animationOpen = "slideInRight";
        this.opts.animationClose = "slideOutRight";
      }
    },
    getCloseLink: function getCloseLink() {
      return this.$target.find(".close");
    },
    open: function open(e) {
      if (e) {
        e.preventDefault();
      }

      if (!this.isOpened()) {
        this.closeAll();
        this.callback("open");
        this.$target.addClass("offcanvas-" + this.opts.direction);
        this.$target.css("width", Math.min(parseInt(this.opts.width), window.innerWidth - 100));
        this.$target.css("right", "-" + Math.min(parseInt(this.opts.width), window.innerWidth - 100)); //this.pushBody();

        this.$target.trigger("kubio.offcanvas.open"); // this.$target.animation(this.opts.animationOpen, $.proxy(this.onOpened, this));

        Colibri.animate(this.$target, this.opts.animationOpen, $.proxy(this.onOpened, this));
        this.$element.trigger("kubio.offcanvas.open");
      }
    },
    closeAll: function closeAll() {
      var $elms = $(document).find(".offcanvas");

      if ($elms.length !== 0) {
        $elms.each(function () {
          var $el = $(this);

          if ($el.hasClass("open")) {
            $el.css("width", "");
            Colibri.animate($el, "hide");
            $el.removeClass("open offcanvas-left offcanvas-right");
          }
        });
        $(document).off("." + this.namespace);
        $("body").css("left", "");
      }
    },
    close: function close(e) {
      if (e) {
        var $el = $(e.target);
        var isTag = $el[0].tagName === "A" || $el[0].tagName === "INPUT" || $el[0].tagName === "BUTTON" || $el.parents("button, a").length;

        if (isTag && $el.closest(".offcanvas").length !== 0 && !$el.hasClass("close") && window.location.href !== e.target.href) {
          return;
        }

        e.preventDefault();
      }

      if (this.isOpened()) {
        // this.utils.enableBodyScroll();
        this.callback("close"); //this.pullBody();

        this.$target.trigger("kubio.offcanvas.close"); // this.$target.animation(this.opts.animationClose, $.proxy(this.onClosed, this));

        Colibri.animate(this.$target, this.opts.animationClose, $.proxy(this.onClosed, this));
      }
    },
    isOpened: function isOpened() {
      return this.$target.hasClass("open");
    },
    onOpened: function onOpened() {
      this.$target.find("a").eq(0).focus();
      this.$target.removeClass("hide");

      if (this.opts.clickOutside) {
        $(document).on("click." + this.namespace + " tap." + this.namespace, $.proxy(this.close, this));
      }

      if (!this.detect.isDesktopScreen()) {
        $("html").addClass("no-scroll");
      }

      $(document).on("keyup." + this.namespace, $.proxy(this.handleKeyboard, this));
      $(document).on("keydown." + this.namespace, $.proxy(this.handleKeyDown, this));
      this.$close.on("click." + this.namespace, $.proxy(this.close, this)); // this.utils.disableBodyScroll();

      this.$target.addClass("open");
      this.callback("opened");
    },
    onClosed: function onClosed() {
      $("html").removeClass("no-scroll");
      this.$target.css("width", "").removeClass("offcanvas-" + this.opts.direction);
      this.$close.off("." + this.namespace);
      $(document).off("." + this.namespace);
      this.$target.removeClass("open");
      this.callback("closed");
      this.$target.trigger("kubio.offcanvas.closed");
    },
    handleKeyboard: function handleKeyboard(e) {
      if (e.which === 27) {
        // eslint-disable-next-line @wordpress/no-global-active-element
        if (document.activeElement) {
          if ( // eslint-disable-next-line @wordpress/no-global-active-element
          $(document.activeElement).closest(".offcanvas").length) {
            this.$element.focus();
          }
        }

        this.close();
      }
    },
    handleKeyDown: function handleKeyDown(e) {
      if (e.which === 9) {
        var $links = this.$target.find("a:visible");
        var isShift = e.shiftKey;

        if ($links.last().is(e.target) && !isShift) {
          $links.first().focus();
          e.preventDefault();
          e.stopPropagation();
          return;
        }

        if ($links.first().is(e.target) && isShift) {
          $links.last().focus();
          e.preventDefault();
          e.stopPropagation();
        }
      }
    }
    /*		pullBody() {
    if (this.opts.push) {
    $('body').animate({ left: 0 }, 350, function () {
    	$(this).removeClass('offcanvas-push-body');
    });
    }
    },*/

    /*		pushBody() {
    if (this.opts.push) {
    const properties =
    	this.opts.direction === 'left'
    		? { left: this.opts.width }
    		: { left: '-' + this.opts.width };
    $('body')
    	.addClass('offcanvas-push-body')
    	.animate(properties, 200);
    }
    },*/

  };
  Component.inherits(Colibri);
  Colibri[className] = Component;
  Colibri.Plugin.create(className);
  Colibri.Plugin.autoload(className); // eslint-disable-next-line no-undef
})(jQuery, Colibri);

/***/ }),

/***/ "./assets/src/theme/js/theme.js":
/*!**************************************!*\
  !*** ./assets/src/theme/js/theme.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _kubio_scripts_src_base__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @kubio/scripts/src/base */ "./kubio-plugin/src/packages/scripts/src/base/index.js");
/* harmony import */ var _kubio_scripts_src_detect_element_resize__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @kubio/scripts/src/detect-element-resize */ "./kubio-plugin/src/packages/scripts/src/detect-element-resize.js");
/* harmony import */ var _kubio_scripts_src_detect_element_resize__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_kubio_scripts_src_detect_element_resize__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _kubio_scripts_src_jquery_extensions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @kubio/scripts/src/jquery-extensions */ "./kubio-plugin/src/packages/scripts/src/jquery-extensions.js");
/* harmony import */ var domready__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! domready */ "./node_modules/domready/ready.js");
/* harmony import */ var domready__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(domready__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _kube_slideshow_customizable_slideshow__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./kube/slideshow/customizable-slideshow */ "./assets/src/theme/js/kube/slideshow/customizable-slideshow.js");
/* harmony import */ var _kube_video_background_customizable_video_background__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./kube/video-background/customizable-video-background */ "./assets/src/theme/js/kube/video-background/customizable-video-background.js");
/* harmony import */ var _kubio_block_library_src_navigation_frontend_index_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @kubio/block-library/src/navigation/frontend/index.js */ "./kubio-plugin/src/packages/block-library/src/navigation/frontend/index.js");
/* harmony import */ var _kubio_block_library_src_hero_blocks_down_arrow_frontend_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @kubio/block-library/src/hero/blocks/down-arrow/frontend.js */ "./kubio-plugin/src/packages/block-library/src/hero/blocks/down-arrow/frontend.js");
/* harmony import */ var _kubio_block_library_src_hero_blocks_down_arrow_frontend_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_kubio_block_library_src_hero_blocks_down_arrow_frontend_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _menu_index_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./menu/index.js */ "./assets/src/theme/js/menu/index.js");
/* harmony import */ var _kubio_scripts_src_masonry_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @kubio/scripts/src/masonry.js */ "./kubio-plugin/src/packages/scripts/src/masonry.js");
/* harmony import */ var _kubio_scripts_src_masonry_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_kubio_scripts_src_masonry_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _kubio_scripts_src_kubio_smoothscroll_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @kubio/scripts/src/kubio-smoothscroll.js */ "./kubio-plugin/src/packages/scripts/src/kubio-smoothscroll.js");
/* harmony import */ var _kubio_scripts_src_kubio_smoothscroll_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_kubio_scripts_src_kubio_smoothscroll_js__WEBPACK_IMPORTED_MODULE_10__);













_kubio_scripts_src_base__WEBPACK_IMPORTED_MODULE_0__.ColibriFrontend.registerPlugin(_kube_slideshow_customizable_slideshow__WEBPACK_IMPORTED_MODULE_4__["default"]);
_kubio_scripts_src_base__WEBPACK_IMPORTED_MODULE_0__.ColibriFrontend.registerPlugin(_kube_video_background_customizable_video_background__WEBPACK_IMPORTED_MODULE_5__["default"]);

_kubio_scripts_src_base__WEBPACK_IMPORTED_MODULE_0__.ColibriFrontend.getData = function (id) {
  if (window.kubioFrontendData && window.kubioFrontendData[id]) {
    return window.kubioFrontendData[id];
  }

  return {};
};

_kubio_scripts_src_base__WEBPACK_IMPORTED_MODULE_0__.ColibriFrontend.domReady = (domready__WEBPACK_IMPORTED_MODULE_3___default());
window.Colibri = _kubio_scripts_src_base__WEBPACK_IMPORTED_MODULE_0__.ColibriFrontend; // require("@/page-components/navigation/scripts/fixto");
// require("@/page-components/navigation/scripts/overlap");
// require("@/common/libraries/mesmerize-smoothscroll");
// require("@/page-components/menu/scripts/dropdown-menu");
// require("@/page-components/menu/scripts/accordion-menu");
// require("@/page-components/menu/scripts/offcanvas");
//
// //
// // // sticky
// //
//("@kubio/scripts/navigation/scripts/navigation");
// require("@/page-components/navigation/scripts/fixto");
// require("@/page-components/navigation/scripts/overlap");
// require("@/page-components/common/scripts/masonry.js");
// require("@/page-components/footer/scripts/footer-paralax.js");
// require("@/page-components/menu/scripts/offcanvas.js");

/***/ }),

/***/ "./kubio-plugin/src/packages/block-library/src/hero/blocks/down-arrow/frontend.js":
/*!****************************************************************************************!*\
  !*** ./kubio-plugin/src/packages/block-library/src/hero/blocks/down-arrow/frontend.js ***!
  \****************************************************************************************/
/***/ (() => {

/* global jQuery, Colibri */
(function ($, Colibri) {
  var className = 'downarrow';

  var Component = function Component() {
    this.namespace = className;
    this.defaults = {
      arrowSelector: '',
      scrollTargetSelector: ''
    }; // Parent Constructor

    Colibri.apply(this, arguments); // Initialization

    this.start();
  };

  Component.prototype = {
    start: function start() {
      if (!(this.opts && this.opts.arrowSelector && this.opts.scrollTargetSelector)) {
        return;
      }

      this.$arrow = this.$element.find(this.opts.arrowSelector);
      var $scrollTarget = $(this.opts.scrollTargetSelector);
      this.$arrow.smoothScrollAnchor({
        target: $scrollTarget
      });
    },
    stop: function stop() {
      if (this.$arrow) {
        this.$arrow.off('click.smooth-scroll tap.smooth-scroll');
      }
    },
    reset: function reset(self) {},
    restart: function restart() {
      this.stop();
      this.start();
    },
    ready: function ready() {
      //const { type } = this.opts;
      if (this.opts.firstTime) {
        return;
      }

      this.opts.firstTime = true;
    }
  };
  Component.inherits(Colibri);
  Colibri[className] = Component;
  Colibri.Plugin.create(className);
  Colibri.Plugin.autoload(className);
})(jQuery, Colibri);

/***/ }),

/***/ "./kubio-plugin/src/packages/block-library/src/navigation/frontend/fixto.js":
/*!**********************************************************************************!*\
  !*** ./kubio-plugin/src/packages/block-library/src/navigation/frontend/fixto.js ***!
  \**********************************************************************************/
/***/ (() => {

(function ($, window, document) {
  // Start Computed Style. Please do not modify this module here. Modify it from its own repo. See address below.

  /*! Computed Style - v0.1.0 - 2012-07-19
   * https://github.com/bbarakaci/computed-style
   * Copyright (c) 2012 Burak Barakaci; Licensed MIT */
  var computedStyle = function () {
    var computedStyle = {
      getAll: function getAll(element) {
        return document.defaultView.getComputedStyle(element);
      },
      get: function get(element, name) {
        return this.getAll(element)[name];
      },
      toFloat: function toFloat(value) {
        return parseFloat(value, 10) || 0;
      },
      getFloat: function getFloat(element, name) {
        return this.toFloat(this.get(element, name));
      },
      _getAllCurrentStyle: function _getAllCurrentStyle(element) {
        return element.currentStyle;
      }
    };

    if (document.documentElement.currentStyle) {
      computedStyle.getAll = computedStyle._getAllCurrentStyle;
    }

    return computedStyle;
  }(); // End Computed Style. Modify whatever you want to.


  var mimicNode = function () {
    /*
      Class Mimic Node
      Dependency : Computed Style
      Tries to mimick a dom node taking his styles, dimensions. May go to his repo if gets mature.
      */
    function MimicNode(element) {
      this.element = element;
      this.replacer = document.createElement('div');
      this.replacer.style.visibility = 'hidden';
      this.hide();
      element.parentNode.insertBefore(this.replacer, element);
    }

    MimicNode.prototype = {
      replace: function replace() {
        var rst = this.replacer.style;
        var styles = computedStyle.getAll(this.element); // rst.width = computedStyle.width(this.element) + 'px';
        // rst.height = this.element.offsetHeight + 'px';
        // Setting offsetWidth

        rst.width = this._width();
        rst.height = this._height(); // Adopt margins

        rst.marginTop = styles.marginTop;
        rst.marginBottom = styles.marginBottom;
        rst.marginLeft = styles.marginLeft;
        rst.marginRight = styles.marginRight; // Adopt positioning

        rst.cssFloat = styles.cssFloat;
        rst.styleFloat = styles.styleFloat; //ie8;

        rst.position = styles.position;
        rst.top = styles.top;
        rst.right = styles.right;
        rst.bottom = styles.bottom;
        rst.left = styles.left; // rst.borderStyle = styles.borderStyle;

        rst.display = styles.display;
      },
      hide: function hide() {
        this.replacer.style.display = 'none';
      },
      _width: function _width() {
        return this.element.getBoundingClientRect().width + 'px';
      },
      _widthOffset: function _widthOffset() {
        return this.element.offsetWidth + 'px';
      },
      _height: function _height() {
        return jQuery(this.element).outerHeight() + 'px';
      },
      _heightOffset: function _heightOffset() {
        return this.element.offsetHeight + 'px';
      },
      destroy: function destroy() {
        $(this.replacer).remove(); // set properties to null to break references

        for (var prop in this) {
          if (this.hasOwnProperty(prop)) {
            this[prop] = null;
          }
        }
      }
    };
    var bcr = document.documentElement.getBoundingClientRect();

    if (!bcr.width) {
      MimicNode.prototype._width = MimicNode.prototype._widthOffset;
      MimicNode.prototype._height = MimicNode.prototype._heightOffset;
    }

    return {
      MimicNode: MimicNode,
      computedStyle: computedStyle
    };
  }(); // Class handles vendor prefixes


  function Prefix() {
    // Cached vendor will be stored when it is detected
    this._vendor = null; //this._dummy = document.createElement('div');
  }

  Prefix.prototype = {
    _vendors: {
      webkit: {
        cssPrefix: '-webkit-',
        jsPrefix: 'Webkit'
      },
      moz: {
        cssPrefix: '-moz-',
        jsPrefix: 'Moz'
      },
      ms: {
        cssPrefix: '-ms-',
        jsPrefix: 'ms'
      },
      opera: {
        cssPrefix: '-o-',
        jsPrefix: 'O'
      }
    },
    _prefixJsProperty: function _prefixJsProperty(vendor, prop) {
      return vendor.jsPrefix + prop[0].toUpperCase() + prop.substr(1);
    },
    _prefixValue: function _prefixValue(vendor, value) {
      return vendor.cssPrefix + value;
    },
    _valueSupported: function _valueSupported(prop, value, dummy) {
      // IE8 will throw Illegal Argument when you attempt to set a not supported value.
      try {
        dummy.style[prop] = value;
        return dummy.style[prop] === value;
      } catch (er) {
        return false;
      }
    },

    /**
     * Returns true if the property is supported
     *
     * @param {string} prop Property name
     * @return {boolean}
     */
    propertySupported: function propertySupported(prop) {
      // Supported property will return either inine style value or an empty string.
      // Undefined means property is not supported.
      return document.documentElement.style[prop] !== undefined;
    },

    /**
     * Returns prefixed property name for js usage
     *
     * @param {string} prop Property name
     * @return {string|null}
     */
    getJsProperty: function getJsProperty(prop) {
      // Try native property name first.
      if (this.propertySupported(prop)) {
        return prop;
      } // Prefix it if we know the vendor already


      if (this._vendor) {
        return this._prefixJsProperty(this._vendor, prop);
      } // We don't know the vendor, try all the possibilities


      var prefixed;

      for (var vendor in this._vendors) {
        prefixed = this._prefixJsProperty(this._vendors[vendor], prop);

        if (this.propertySupported(prefixed)) {
          // Vendor detected. Cache it.
          this._vendor = this._vendors[vendor];
          return prefixed;
        }
      } // Nothing worked


      return null;
    },

    /**
     * Returns supported css value for css property. Could be used to check support or get prefixed value string.
     *
     * @param {string} prop  Property
     * @param {string} value Value name
     * @return {string|null}
     */
    getCssValue: function getCssValue(prop, value) {
      // Create dummy element to test value
      var dummy = document.createElement('div'); // Get supported property name

      var jsProperty = this.getJsProperty(prop); // Try unprefixed value

      if (this._valueSupported(jsProperty, value, dummy)) {
        return value;
      }

      var prefixedValue; // If we know the vendor already try prefixed value

      if (this._vendor) {
        prefixedValue = this._prefixValue(this._vendor, value);

        if (this._valueSupported(jsProperty, prefixedValue, dummy)) {
          return prefixedValue;
        }
      } // Try all vendors


      for (var vendor in this._vendors) {
        prefixedValue = this._prefixValue(this._vendors[vendor], value);

        if (this._valueSupported(jsProperty, prefixedValue, dummy)) {
          // Vendor detected. Cache it.
          this._vendor = this._vendors[vendor];
          return prefixedValue;
        }
      } // No support for value


      return null;
    }
  };
  var prefix = new Prefix(); // We will need this frequently. Lets have it as a global until we encapsulate properly.

  var transformJsProperty = prefix.getJsProperty('transform'); // Will hold if browser creates a positioning context for fixed elements.

  var fixedPositioningContext; // Checks if browser creates a positioning context for fixed elements.
  // Transform rule will create a positioning context on browsers who follow the spec.
  // Ie for example will fix it according to documentElement
  // TODO: Other css rules also effects. perspective creates at chrome but not in firefox. transform-style preserve3d effects.

  function checkFixedPositioningContextSupport() {
    var support = false;
    var parent = document.createElement('div');
    var child = document.createElement('div');
    parent.appendChild(child);
    parent.style[transformJsProperty] = 'translate(0)'; // Make sure there is space on top of parent

    parent.style.marginTop = '10px';
    parent.style.visibility = 'hidden';
    child.style.position = 'fixed';
    child.style.top = 0;
    document.body.appendChild(parent);
    var rect = child.getBoundingClientRect(); // If offset top is greater than 0 meand transformed element created a positioning context.

    if (rect.top > 0) {
      support = true;
    } // Remove dummy content


    document.body.removeChild(parent);
    return false; //support;
  } // It will return null if position sticky is not supported


  var nativeStickyValue = prefix.getCssValue('position', 'sticky'); // It will return null if position fixed is not supported

  var fixedPositionValue = prefix.getCssValue('position', 'fixed'); // Dirty business

  var ie = navigator.appName === 'Microsoft Internet Explorer';
  var ieversion;

  if (ie) {
    ieversion = parseFloat(navigator.appVersion.split('MSIE')[1]);
  }

  function FixTo(child, parent, options) {
    this.child = child;
    this._$child = $(child);
    this.parent = parent;
    this.options = {
      className: 'fixto-fixed',
      startAfterNode: {
        enabled: false,
        selector: ''
      },
      animations: {
        enabled: false,
        currentInAnimationClass: '',
        currentOutAnimationClass: '',
        allInAnimationsClasses: '',
        allOutAnimationsClasses: '',
        duration: 0
      },
      top: 0,
      zIndex: ''
    };

    this._setOptions(options);

    this._initAnimations();
  }

  FixTo.prototype = {
    // Returns the total outerHeight of the elements passed to mind option. Will return 0 if none.
    _mindtop: function _mindtop() {
      var top = 0;

      if (this._$mind) {
        var el;
        var rect;
        var height;

        for (var i = 0, l = this._$mind.length; i < l; i++) {
          el = this._$mind[i];
          rect = el.getBoundingClientRect();

          if (rect.height) {
            top += rect.height;
          } else {
            var styles = computedStyle.getAll(el);
            top += el.offsetHeight + computedStyle.toFloat(styles.marginTop) + computedStyle.toFloat(styles.marginBottom);
          }
        }
      }

      return top;
    },
    _updateOutAnimationDuration: function _updateOutAnimationDuration() {
      var animationDuration = this.options.animations.duration;

      if (isNaN(animationDuration)) {
        animationDuration = 0;
      }

      this._animationDuration = animationDuration;
    },
    _initAnimations: function _initAnimations() {
      var animations = this.options.animations;

      this._$child.removeClass(animations.allInAnimationsClasses);

      this._$child.removeClass(animations.allOutAnimationsClasses);

      var self = this;

      this._updateOutAnimationDuration();

      this._animationOutDebounce = $.debounce(function () {
        self._$child.removeClass(self.options.animations.allOutAnimationsClasses);

        self._inOutAnimation = false;

        self._unfix();

        self._removeTransitionFromOutAnimation();
      }, 100);
      this._animationInDebounce = $.debounce(function () {
        self._inInAnimation = false;

        self._$child.removeClass(self.options.animations.allInAnimationsClasses);
      }, this._animationDuration);
    },
    _removeTransitionFromOutAnimation: function _removeTransitionFromOutAnimation() {
      var noTransitionClass = 'h-global-transition-disable';

      this._$child.addClass(noTransitionClass);

      var childTransitionDuration = this._$child.css('transition-duration');

      var isNumberRegex = /\d+/;
      var transitionDurationInS = childTransitionDuration.match(isNumberRegex)[0];

      if (!transitionDurationInS) {
        transitionDurationInS = 0;
      }

      var transitionDurationInMs = transitionDurationInS * 1000;
      var transitionBuffer = 500;
      var transitionDuration = transitionDurationInMs + transitionBuffer;
      var self = this;
      setTimeout(function () {
        if (!self._$child) {
          return;
        }

        self._$child.removeClass(noTransitionClass);
      }, transitionDuration);
    },
    _passedStartAfterNode: function _passedStartAfterNode() {
      var $startAfterNode = this._$startAfterNode;

      if ($startAfterNode && $startAfterNode.length > 0) {
        var offsetTop = this._afterElementOffsetTop;
        var height = $startAfterNode.outerHeight();
        return this._scrollTop > offsetTop + height;
      }

      return true;
    },
    // Public method to stop the behaviour of this instance.
    stop: function stop() {
      this._stop();

      this._running = false;
    },
    // Public method starts the behaviour of this instance.
    start: function start() {
      // Start only if it is not running not to attach event listeners multiple times.
      if (!this._running) {
        this._start();

        this._running = true;
      }
    },
    //Public method to destroy fixto behaviour
    destroy: function destroy() {
      this.stop();

      this._destroy(); // Remove jquery data from the element


      this._$child.removeData('fixto-instance'); // set properties to null to break references


      for (var prop in this) {
        if (this.hasOwnProperty(prop)) {
          this[prop] = null;
        }
      }
    },
    _setOptions: function _setOptions(options) {
      $.extend(true, this.options, options);

      if (this.options.mind) {
        this._$mind = $(this.options.mind);
      }

      if (this.options.startAfterNode.enabled && this.options.startAfterNode.selector) {
        this._$startAfterNode = $(this.options.startAfterNode.selector);
      }
    },
    setOptions: function setOptions(options) {
      this._setOptions(options);

      this.refresh();
    },
    // Methods could be implemented by subclasses
    _stop: function _stop() {},
    _start: function _start() {},
    _destroy: function _destroy() {},
    refresh: function refresh() {}
  }; // Class FixToContainer

  function FixToContainer(child, parent, options) {
    /**
     * FIXME If you have a saved navigation with sticky, when you enter the page, this class creates two objects
     * and because of that there are two events listeners. There should be only one instance of this class for each
     * navigation
     */
    //The script still is called two times but now both of the calls pass the check
    // if (!child || !this._scriptCallIsValid(child)) {
    //   return;
    // }
    FixTo.call(this, child, parent, options);
    this._replacer = new mimicNode.MimicNode(child);
    this._ghostNode = this._replacer.replacer;

    this._saveStyles();

    this._saveViewportHeight(); // Create anonymous functions and keep references to register and unregister events.


    this._proxied_onscroll = this._bind(this._onscroll, this);
    this._proxied_onresize = this._bind(this._onresize, this);
    this.start();
  }

  FixToContainer.prototype = new FixTo();
  $.extend(FixToContainer.prototype, {
    // Returns an anonymous function that will call the given function in the given context
    _bind: function _bind(fn, context) {
      return function () {
        return fn.call(context);
      };
    },
    // at ie8 maybe only in vm window resize event fires everytime an element is resized.
    _toresize: ieversion === 8 ? document.documentElement : window,
    //TODO @catalin this is a temporary workaround, until the issue: #0030376 is fixed
    _scriptCallIsValid: function _scriptCallIsValid(child) {
      var isInCustomizer = Colibri.isCustomizerPreview();

      if (!isInCustomizer) {
        return true;
      }

      var vueNavSelector = '.h-navigation_outer';
      var vueNav = $(child).closest(vueNavSelector).get(0);

      if (!vueNav) {
        return true;
      }

      if (vueNav.__vue__) {
        return true;
      }

      return false;
    },
    _onscroll: function _onscroll() {
      /**
       * TODO @catalin, now sometimes the child height is 0, other times is correct that ruins the out animation logic,
       * until that is fixed this is a workaround to that problem. When the child height will always be correct remove
       * this condition.
       */
      this._scrollingContainer = $('.edit-site-visual-editor')[0];
      this._scrollTop = document.documentElement.scrollTop || document.body.scrollTop || (this._scrollingContainer ? this._scrollingContainer.scrollTop : 0);
      this._parentBottom = this.parent.offsetHeight + this._fullOffset('offsetTop', this.parent);

      if (this.options.startAfterNode && !this._passedStartAfterNode()) {
        if (this.fixed && !this._inOutAnimation) {
          this._unfixFromScrollListener();
        }

        return;
      } // if (this.options.mindBottomPadding !== false) {
      //     this._parentBottom -= computedStyle.getFloat(this.parent, 'paddingBottom');
      // }
      // if (this.options.toBottom) {
      //     this._fix();
      //     this._adjust();
      //     return
      // }
      // if (this.options.toBottom) {
      //     this.options.top = this._viewportHeight - computedStyle.toFloat(computedStyle.getAll(this.child).height) - this.options.topSpacing;
      // }


      if (!this.fixed) {
        var childStyles = computedStyle.getAll(this.child);

        if (this._scrollTop < this._parentBottom && this._scrollTop > this._fullOffset('offsetTop', this.child) - this.options.top - this._mindtop() && this._viewportHeight > this.child.offsetHeight + computedStyle.toFloat(childStyles.marginTop) + computedStyle.toFloat(childStyles.marginBottom) || this.options.toBottom) {
          this._fix();

          this._adjust();
        }
      } else {
        if (this.options.toBottom) {
          if (this._scrollTop >= this._fullOffset('offsetTop', this._ghostNode)) {
            this._unfixFromScrollListener();

            return;
          }
        } else if (this._scrollTop > this._parentBottom || this._scrollTop <= this._fullOffset('offsetTop', this._ghostNode) - this.options.top - this._mindtop()) {
          this._unfixFromScrollListener();

          return;
        }

        this._adjust();
      }
    },
    _adjust: function _adjust() {
      var top = 0;

      var mindTop = this._mindtop();

      var diff = 0;
      var childStyles = computedStyle.getAll(this.child);
      var context = null;

      if (fixedPositioningContext) {
        // Get positioning context.
        context = this._getContext();

        if (context) {
          // There is a positioning context. Top should be according to the context.
          top = Math.abs(context.getBoundingClientRect().top);
        }
      }

      diff = this._parentBottom - this._scrollTop - (this.child.offsetHeight + computedStyle.toFloat(childStyles.marginBottom) + mindTop + this.options.top);

      if (diff > 0) {
        diff = 0;
      }

      if (this.options.toBottom) {// this.child.style.top = (diff + mindTop + top + this.options.top) - computedStyle.toFloat(childStyles.marginTop) + 'px';
      } else {
        var _top = this.options.top;

        if (_top === 0) {
          _top = $('body').offset().top;
        }

        this.child.style.top = Math.round(diff + mindTop + top + _top - computedStyle.toFloat(childStyles.marginTop)) + 'px';
      }
    },
    // Calculate cumulative offset of the element.
    // Optionally according to context
    _fullOffset: function _fullOffset(offsetName, elm, context) {
      var offset = elm[offsetName];
      var offsetParent = elm.offsetParent; // Add offset of the ascendent tree until we reach to the document root or to the given context

      while (offsetParent !== null && offsetParent !== context) {
        offset = offset + offsetParent[offsetName];
        offsetParent = offsetParent.offsetParent;
      }

      return offset;
    },
    // Get positioning context of the element.
    // We know that the closest parent that a transform rule applied will create a positioning context.
    _getContext: function _getContext() {
      var parent;
      var element = this.child;
      var context = null;
      var styles; // Climb up the treee until reaching the context

      while (!context) {
        parent = element.parentNode;

        if (parent === document.documentElement) {
          return null;
        }

        styles = computedStyle.getAll(parent); // Element has a transform rule

        if (styles[transformJsProperty] !== 'none') {
          context = parent;
          break;
        }

        element = parent;
      }

      return context;
    },
    _fix: function _fix() {
      var child = this.child;
      var childStyle = child.style;
      var childStyles = computedStyle.getAll(child);
      var left = child.getBoundingClientRect().left;
      var width = childStyles.width;

      this._$child.trigger('fixto-add');

      this._saveStyles();

      if (document.documentElement.currentStyle) {
        // Function for ie<9. When hasLayout is not triggered in ie7, he will report currentStyle as auto, clientWidth as 0. Thus using offsetWidth.
        // Opera also falls here
        width = child.offsetWidth;

        if (childStyles.boxSizing !== 'border-box') {
          width = width - (computedStyle.toFloat(childStyles.paddingLeft) + computedStyle.toFloat(childStyles.paddingRight) + computedStyle.toFloat(childStyles.borderLeftWidth) + computedStyle.toFloat(childStyles.borderRightWidth));
        }

        width += 'px';
      } // Ie still fixes the container according to the viewport.


      if (fixedPositioningContext) {
        var context = this._getContext(); // if(context) {
        //     // There is a positioning context. Left should be according to the context.
        //     left = child.getBoundingClientRect().left - context.getBoundingClientRect().left;
        // } else {


        left = this._$child.offset().left; // }
      }

      this._replacer.replace();

      childStyle.left =
      /*left + "px"; */
      left - computedStyle.toFloat(childStyles.marginLeft) + 'px';
      childStyle.width = width;
      childStyle.position = 'fixed';

      if (this.options.toBottom) {
        childStyle.top = '';
        childStyle.bottom = this.options.top + computedStyle.toFloat(childStyles.marginBottom) + 'px';
      } else {
        childStyle.bottom = '';
        var _top = this.options.top;

        if (_top === 0) {
          _top = $('body').offset().top;
        }

        childStyle.top = this._mindtop() + _top - computedStyle.toFloat(childStyles.marginTop) + 'px';
      }

      if (this.options.zIndex) {
        this.child.style.zIndex = this.options.zIndex;
      }

      this._$child.addClass(this.options.className);

      var animations = this.options.animations;

      this._$child.removeClass(animations.allInAnimationsClasses);

      if (animations.enabled) {
        this._$child.addClass(animations.currentInAnimationClass);

        if (!this._inInAnimation) {
          this._inInAnimation = true;

          this._animationInDebounce();
        }
      }

      this.fixed = true;

      this._$child.trigger('fixto-added');
    },
    _unfixFromScrollListener: function _unfixFromScrollListener() {
      this._$child.trigger('fixto-unnfix-from-scroll');

      if (this.options.animations.enabled) {
        this._unfixTriggerAnimation();
      } else {
        this._unfix();
      }
    },
    _getAfterElementOffsetTop: function _getAfterElementOffsetTop() {
      var $node = this._$startAfterNode;
      var defaultValue = 0;

      if ($node && $node.length > 0) {
        var elem = $node.get(0);
        var distance = 0;

        do {
          // Increase our distance counter
          distance += elem.offsetTop; // Set the element to it's parent

          elem = elem.offsetParent;
        } while (elem);

        distance = distance < defaultValue ? defaultValue : distance;
        return distance;
      }

      return defaultValue;
    },
    _unfix: function _unfix() {
      this._replacer.hide();

      var childStyle = this.child.style;
      childStyle.position = this._childOriginalPosition;
      childStyle.top = this._childOriginalTop;
      childStyle.bottom = this._childOriginalBottom;
      childStyle.width = this._childOriginalWidth;
      childStyle.left = this._childOriginalLeft;
      childStyle.zIndex = this._childOriginalZIndex;

      if (!this.options.always) {
        this._$child.removeClass(this.options.className);

        this._$child.trigger('fixto-removed');
      }

      this.fixed = false;
    },
    _unfixTriggerAnimation: function _unfixTriggerAnimation() {
      this._$child.trigger('fixto-animated-remove');

      this._animationInDebounce.flush();

      var animations = this.options.animations;

      this._$child.removeClass(animations.allInAnimationsClasses);

      this._$child.removeClass(animations.allOutAnimationsClasses);

      if (animations.enabled) {
        this._$child.addClass(animations.currentOutAnimationClass);
      }

      this._inOutAnimation = true;

      this._animationOutDebounce();
    },
    _saveStyles: function _saveStyles() {
      this._animationOutDebounce.flush();

      var childStyle = this.child.style;
      this._childOriginalPosition = childStyle.position;

      if (this.options.toBottom) {
        this._childOriginalTop = '';
        this._childOriginalBottom = childStyle.bottom;
      } else {
        this._childOriginalTop = childStyle.top;
        this._childOriginalBottom = '';
      }

      this._childOriginalWidth = childStyle.width;
      this._childOriginalLeft = childStyle.left;
      this._childOriginalZIndex = childStyle.zIndex;
      this._afterElementOffsetTop = this._getAfterElementOffsetTop();
    },
    _onresize: function _onresize() {
      this.refresh();
    },
    _saveViewportHeight: function _saveViewportHeight() {
      // ie8 doesn't support innerHeight
      this._viewportHeight = window.innerHeight || document.documentElement.clientHeight;
    },
    _stop: function _stop() {
      // Unfix the container immediately.
      this._unfix(); // remove event listeners


      window.removeEventListener('scroll', this._proxied_onscroll);
      window.removeEventListener('mousewheel', this._proxied_onscroll);
    },
    _start: function _start() {
      // Trigger onscroll to have the effect immediately.
      this._onscroll();

      window.addEventListener('scroll', this._proxied_onscroll, {
        passive: true
      });
      window.addEventListener('mousewheel', this._proxied_onscroll, {
        passive: true
      });
      $(this._toresize).on('resize.fixto', this._proxied_onresize);
    },
    _destroy: function _destroy() {
      // Destroy mimic node instance
      this._replacer.destroy();
    },
    refresh: function refresh() {
      this._saveViewportHeight();

      this._unfix();

      this._onscroll();
    }
  });

  function NativeSticky(child, parent, options) {
    FixTo.call(this, child, parent, options);
    this.start();
  }

  NativeSticky.prototype = new FixTo();
  $.extend(NativeSticky.prototype, {
    _start: function _start() {
      var childStyles = computedStyle.getAll(this.child);
      this._childOriginalPosition = childStyles.position;
      this._childOriginalTop = childStyles.top;
      this.child.style.position = nativeStickyValue;
      this.refresh();
    },
    _stop: function _stop() {
      this.child.style.position = this._childOriginalPosition;
      this.child.style.top = this._childOriginalTop;
    },
    refresh: function refresh() {
      this.child.style.top = this._mindtop() + this.options.top + 'px';
    }
  });

  var fixTo = function fixTo(childElement, parentElement, options) {
    if (nativeStickyValue && !options || nativeStickyValue && options && options.useNativeSticky !== false) {
      // Position sticky supported and user did not disabled the usage of it.
      return new NativeSticky(childElement, parentElement, options);
    } else if (fixedPositionValue) {
      // Position fixed supported
      if (fixedPositioningContext === undefined) {
        // We don't know yet if browser creates fixed positioning contexts. Check it.
        fixedPositioningContext = checkFixedPositioningContextSupport();
      }

      return new FixToContainer(childElement, parentElement, options);
    }

    return 'Neither fixed nor sticky positioning supported';
  };
  /*
   No support for ie lt 8
   */


  if (ieversion < 8) {
    fixTo = function fixTo() {
      return 'not supported';
    };
  } // Let it be a jQuery Plugin


  $.fn.fixTo = function (targetSelector, options) {
    var $targets = $(targetSelector);
    var i = 0;
    return this.each(function () {
      // Check the data of the element.
      var instance = $(this).data('fixto-instance'); // If the element is not bound to an instance, create the instance and save it to elements data.

      if (!instance) {
        $(this).data('fixto-instance', fixTo(this, $targets[i], options));
      } else {
        // If we already have the instance here, expect that targetSelector parameter will be a string
        // equal to a public methods name. Run the method on the instance without checking if
        // it exists or it is a public method or not. Cause nasty errors when necessary.
        var method = targetSelector;
        instance[method].call(instance, options);
      }

      i++;
    });
  };
  /*
       Expose
   */


  return {
    FixToContainer: FixToContainer,
    fixTo: fixTo,
    computedStyle: computedStyle,
    mimicNode: mimicNode
  };
})(window.jQuery, window, document);

/***/ }),

/***/ "./kubio-plugin/src/packages/block-library/src/navigation/frontend/index.js":
/*!**********************************************************************************!*\
  !*** ./kubio-plugin/src/packages/block-library/src/navigation/frontend/index.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _fixto__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./fixto */ "./kubio-plugin/src/packages/block-library/src/navigation/frontend/fixto.js");
/* harmony import */ var _fixto__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_fixto__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _navigation__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./navigation */ "./kubio-plugin/src/packages/block-library/src/navigation/frontend/navigation.js");
/* harmony import */ var _navigation__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_navigation__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _overlap__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./overlap */ "./kubio-plugin/src/packages/block-library/src/navigation/frontend/overlap.js");
/* harmony import */ var _overlap__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_overlap__WEBPACK_IMPORTED_MODULE_2__);




/***/ }),

/***/ "./kubio-plugin/src/packages/block-library/src/navigation/frontend/navigation.js":
/*!***************************************************************************************!*\
  !*** ./kubio-plugin/src/packages/block-library/src/navigation/frontend/navigation.js ***!
  \***************************************************************************************/
/***/ (() => {

(function ($, Colibri) {
  var className = 'navigation';
  var animations = {
    slideDown: {
      "in": 'slideInDown',
      out: 'slideOutDownNavigation'
    },
    fade: {
      "in": 'fadeIn',
      out: 'fadeOut'
    },
    none: {
      "in": 'h-global-transition-disable',
      out: 'h-global-transition-disable'
    }
  };

  var Component = function Component(element, options) {
    this.namespace = className;
    this.scrollingContainer = $('.edit-site-visual-editor');
    this.defaults = {
      sticky: {
        className: 'h-navigation_sticky',
        topSpacing: 0,
        top: this.scrollingContainer.length ? this.scrollingContainer.offset().top : 0,
        stickyOnMobile: true,
        stickyOnTablet: true,
        startAfterNode: {
          enabled: false,
          selector: '.wp-block-kubio-header'
        },
        animations: {
          enabled: false,
          name: 'none',
          duration: 0
        },
        zIndex: 9999,
        responsiveWidth: true,
        center: true,
        useShrink: true,
        toBottom: false,
        useNativeSticky: false,
        always: false,
        prepare: true,
        onShow: false
      },
      overlap: false,
      overlapIsActive: false
    }; // Parent Constructor

    Colibri.apply(this, arguments);
    this.computeOverlapPaddingDelayed = jQuery.debounce(this.computeOverlapPadding.bind(this), 10); // Initialization

    this.start();
  };

  Component.prototype = {
    getStickyData: function getStickyData() {
      var animation = this.opts.sticky.animations;
      var duration = animation.duration;
      return $.extend(true, {}, this.opts.sticky, {
        animations: {
          allInAnimationsClasses: 'slideInDown fadeIn h-global-transition-disable',
          allOutAnimationsClasses: 'slideOutDownNavigation fadeOut h-global-transition-disable',
          currentInAnimationClass: animations[animation.name]["in"],
          currentOutAnimationClass: animations[animation.name].out,
          duration: animation.name === 'none' ? 0 : duration * 1000
        }
      });
    },
    start: function start() {
      // console.error('this.opts.sticky->', this.opts.sticky);
      if (this.opts.sticky) {
        this.startSticky(this.getStickyData());
      }

      if (this.opts.overlap) {
        this.startOverlap();
      }
    },
    startOverlap: function startOverlap() {
      var self = this;
      var $target = this.$element.closest('.h-navigation_overlap'); //for backward compatibility reasons

      if ($target.length === 0) {
        $target = this.$element;
      }

      this.overlapTarget = $target.get(0);
      this.overlapIsActive = true;
      $(window).on('resize.overlap orientationchange.overlap', this.computeOverlapPaddingDelayed);
      window.addResizeListener(this.overlapTarget, this.computeOverlapPaddingDelayed);
      self.computeOverlapPadding();
    },
    stopOverlap: function stopOverlap() {
      this.overlapIsActive = false;

      if (this.$sheet) {
        document.head.removeChild(this.$sheet);
        this.$sheet = null;
      }

      $(window).off('.overlap');
      window.removeResizeListener(this.overlapTarget, this.computeOverlapPaddingDelayed);
    },
    computeOverlapPadding: function computeOverlapPadding() {
      if (!this.overlapIsActive) {
        return;
      }

      if (!this.$sheet) {
        this.$sheet = document.createElement('style');
        document.head.appendChild(this.$sheet);
      }

      var paddingTop = this.overlapTarget.offsetHeight + 'px !important;';
      this.$sheet.innerHTML = '.h-navigation-padding{padding-top:' + paddingTop + '}';
    },
    startSticky: function startSticky(data) {
      var self = this;
      this.$element.data('stickData', data);
      this.$element.fixTo('body', data); // console.warn('move ->', this.opts);

      this.opts.sticky.prepare && this.prepareSticky();
      this.$element.on('fixto-added.sticky', function () {
        self.$element.attr('data-in-sticky-state', true);
      });
      this.$element.on('fixto-add.sticky', function () {
        self.clearResetTimeouts();
        var navOuter = self.navigationWrapper();
        navOuter.css('animation-duration', '');
        navOuter.css('min-height', navOuter[0].offsetHeight);
      });
      this.$element.on('fixto-removed.sticky', function () {
        self.$element.removeAttr('data-in-sticky-state');
        self.resetParentHeight();
      });
      $(window).on('resize.sticky orientationchange.sticky', function () {
        setTimeout(self.resizeCallback.bind(self), 50);
      });
      $(window).trigger('resize.sticky');
    },
    stopSticky: function stopSticky() {
      var instance = this.fixToInstance();

      if (instance) {
        // console.error('stop sticky', instance, this.$element);
        this.$element.off('.sticky');
        instance.destroy();
        $(window).off('.sticky');
        this.$element.removeData('fixto-instance');
        this.resetParentHeight();
      }
    },
    navigationWrapper: function navigationWrapper() {
      return this.$element.closest('[class*=navigation__outer]');
    },
    resetParentHeight: function resetParentHeight() {
      this.clearResetTimeouts();
      var navOuter = this.navigationWrapper();
      var delay = parseFloat(this.$element.css('animation-duration')) * 1000;
      navOuter.css('animation-duration', '0s');
      this.resetTimeoutHeight = setTimeout(function () {
        navOuter.css('min-height', '');
      }, 1000);
      this.resetTimeoutAnimation = setTimeout(function () {
        navOuter.css('animation-duration', '');
      }, delay + 50);
    },
    clearResetTimeouts: function clearResetTimeouts() {
      clearTimeout(this.resetTimeoutHeight);
      clearTimeout(this.resetTimeoutAnimation);
    },
    stop: function stop() {
      // console.error('stop sticky');
      this.stopSticky();
      this.stopOverlap();
    },
    prepareSticky: function prepareSticky() {
      var self = this;
      this.normal = this.$element.find('[data-nav-normal]');
      this.sticky = this.$element.find('[data-nav-sticky]');
      this.sticky.find('span[data-placeholder]').each(function () {
        $(this).parent().attr('data-placeholder', $(this).attr('data-placeholder'));
        $(this).remove();
      });

      if (!this.sticky.length || !this.sticky.children().length) {
        return;
      }

      this.$element.on('fixto-added.sticky', function () {
        self.moveElementsToSticky();
      });
      this.$element.on('fixto-removed.sticky', function () {
        self.moveElementsToNormal();
      });
    },
    moveElementsToSticky: function moveElementsToSticky() {
      var stickyEls = this.sticky.find('[data-placeholder]');
      var self = this;
      stickyEls.each(function (index, el) {
        $this = $(this);
        var type = $this.attr('data-placeholder');
        var content = self.normal.find('[data-placeholder-provider=' + type + '] .h-column__content >');
        var stickyEquiv = $this;

        if (stickyEquiv && content.length) {
          $(stickyEquiv).append(content);
        }
      });
      this.normal.hide();
      this.sticky.show();
    },
    moveElementsToNormal: function moveElementsToNormal() {
      var stickyEls = this.sticky.find('[data-placeholder]');
      var self = this;
      stickyEls.each(function (index, el) {
        $this = $(this);
        var type = $this.attr('data-placeholder');
        var content = self.sticky.find('[data-placeholder=' + type + '] >');
        var equiv = self.normal.find('[data-placeholder-provider=' + type + '] .h-column__content');

        if (equiv && content.length) {
          $(equiv).append(content);
        }
      });
      this.normal.show();
      this.sticky.hide();
    },
    fixToInstance: function fixToInstance() {
      var data = this.$element.data();

      if (data && data.fixtoInstance) {
        return data.fixtoInstance;
      }

      return false;
    },
    resizeCallback: function resizeCallback() {
      if (window.innerWidth < 1024) {
        var data = this.$element.data();
        var stickData = data.stickData;

        if (!stickData) {
          return;
        }

        var fixToInstance = data.fixtoInstance;

        if (!fixToInstance) {
          return true;
        }

        if (window.innerWidth <= 767) {
          if (!stickData.stickyOnMobile) {
            fixToInstance.stop();
          }
        } else if (!stickData.stickyOnTablet) {
          fixToInstance.stop();
        }
      } else {
        var data = this.$element.data();

        if (!data) {
          return;
        }

        var fixToInstance = data.fixtoInstance;

        if (!fixToInstance) {
          return true;
        }

        fixToInstance.refresh();
        fixToInstance.start();
      }
    }
  };
  Component.inherits(Colibri);
  Colibri[className] = Component;
  Colibri.Plugin.create(className);
  Colibri.Plugin.autoload(className);
})(jQuery, Colibri);

/***/ }),

/***/ "./kubio-plugin/src/packages/block-library/src/navigation/frontend/overlap.js":
/*!************************************************************************************!*\
  !*** ./kubio-plugin/src/packages/block-library/src/navigation/frontend/overlap.js ***!
  \************************************************************************************/
/***/ (() => {

(function ($, Colibri) {
  var className = 'overlap';

  var Component = function Component() {
    this.namespace = className;
    this.defaults = {
      data: {}
    }; // Parent Constructor

    Colibri.apply(this, arguments); // Initialization

    this.start();
  };

  Component.prototype = {
    start: function start() {
      $(window).on('resize.overlap orientationchange.overlap', this.computePadding);
      this.overlapTarget = this.$element[0];
      new ResizeObserver(this.computePadding).observe(this.overlapTarget);
    },
    stop: function stop() {
      $(window).off('.overlap');

      if (this.$sheet) {
        document.head.removeChild(this.$sheet);
        this.$sheet = null;
      }
    },
    computePadding: function computePadding(entries) {
      if (!entries || !entries[0] || !entries[0].target) {
        return;
      }

      if (!this.$sheet) {
        this.$sheet = document.createElement('style');
        document.head.appendChild(this.$sheet);
      }

      var paddingTop = entries[0].target.offsetHeight + 'px !important;';
      this.$sheet.innerHTML = '.h-navigation-padding{padding-top:' + paddingTop + '}';
    },
    resizeCallback: function resizeCallback() {
      this.computePadding();
    }
  };
  Component.inherits(Colibri);
  Colibri[className] = Component;
  Colibri.Plugin.create(className);
  Colibri.Plugin.autoload(className);
})(jQuery, Colibri);

/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/background/slideshow/slideshow.js":
/*!*********************************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/background/slideshow/slideshow.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Slideshow)
/* harmony export */ });
/* harmony import */ var lodash_debounce__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash.debounce */ "./node_modules/lodash.debounce/index.js");
/* harmony import */ var lodash_debounce__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash_debounce__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../base */ "./kubio-plugin/src/packages/scripts/src/base/index.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }




var Slideshow = /*#__PURE__*/function (_ColibriFrontComponen) {
  _inherits(Slideshow, _ColibriFrontComponen);

  var _super = _createSuper(Slideshow);

  function Slideshow() {
    _classCallCheck(this, Slideshow);

    return _super.apply(this, arguments);
  }

  _createClass(Slideshow, [{
    key: "init",
    value: function init() {
      var _this = this;

      this.currentIndex = 0;
      this.interval = -1;
      this.debouncedRestart = lodash_debounce__WEBPACK_IMPORTED_MODULE_0___default()(function () {
        _this.stop();

        _this.start();
      }, 500);
    }
  }, {
    key: "addImageEffect",
    value: function addImageEffect(image, index) {
      var duration = this.opts.duration.replace('ms', '');
      var speed = this.opts.speed.replace('ms', '');
      var delay = parseInt(duration) - parseInt(speed);

      if (delay < 0) {
        delay = 0;
      }

      this.$(image).css({
        transition: "opacity ".concat(speed, "ms ease ").concat(delay, "ms"),
        zIndex: this.$images.length - index
      });
    }
  }, {
    key: "slideImage",
    value: function slideImage() {
      this.$images.eq(this.currentIndex).removeClass('current');
      var nextIndex = this.currentIndex + 1 === this.$images.length ? 0 : this.currentIndex + 1;
      this.$images.eq(nextIndex).addClass('current').removeClass('next');
      this.currentIndex = nextIndex;
      var futureIndex = this.currentIndex + 1 === this.$images.length ? 0 : this.currentIndex + 1;
      this.$images.eq(futureIndex).addClass('next');
    }
  }, {
    key: "restart",
    value: function restart() {
      this.debouncedRestart();
    }
  }, {
    key: "start",
    value: function start() {
      var _this2 = this;

      this.$images = this.$element.find('.slideshow-image');
      this.$images.removeClass('current');

      if (this.$images.length <= 1) {
        return;
      }

      this.$images.eq(0).addClass('current');
      this.currentIndex = 0;
      this.$images.each(function (index, image) {
        _this2.addImageEffect(image, index);
      });
      this.interval = setInterval(function () {
        _this2.slideImage();
      }, parseInt(this.opts.duration));
    }
  }, {
    key: "stop",
    value: function stop() {
      clearInterval(this.interval);
      this.$images.css({
        transition: '',
        opacity: ''
      });
      this.$images.removeClass('current next');
      this.$images.eq(0).addClass('current');
      this.currentIndex = 0;
    }
  }], [{
    key: "componentName",
    value: function componentName() {
      return 'slideshow';
    }
  }]);

  return Slideshow;
}(_base__WEBPACK_IMPORTED_MODULE_1__.ColibriFrontComponent);



/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/background/video/handlers/base-handler.js":
/*!*****************************************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/background/video/handlers/base-handler.js ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ BaseHandler)
/* harmony export */ });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var BaseHandler = /*#__PURE__*/function () {
  function BaseHandler(element, settings) {
    _classCallCheck(this, BaseHandler);

    this.settings = settings;
    this.element = element;
    this.isPlaying = false;
    this.ready();
  }

  _createClass(BaseHandler, [{
    key: "ready",
    value: function ready() {}
  }, {
    key: "play",
    value: function play() {}
  }, {
    key: "pause",
    value: function pause() {}
  }, {
    key: "isPaused",
    value: function isPaused() {}
  }, {
    key: "setVideo",
    value: function setVideo(node) {
      node.className = 'kubio-video-background-item';
      this.element.innerHTML = '';
      this.element.appendChild(node);
      this.addResizeBind();
    }
  }, {
    key: "trigger",
    value: function trigger(name) {
      var evt;

      if ('function' === typeof window.Event) {
        evt = new Event(name);
      } else {
        evt = document.createEvent('Event');
        evt.initEvent(name, true, true);
      }

      this.element.dispatchEvent(evt);
    }
  }, {
    key: "loaded",
    value: function loaded() {
      this.trigger('video-bg-loaded');
    }
  }, {
    key: "addResizeBind",
    value: function addResizeBind() {
      var _this = this;

      this.trigger('video-bg-resize');
      this.onResize(function () {
        _this.trigger('video-bg-resize');
      });
    }
  }, {
    key: "onLoad",
    value: function onLoad(callback) {
      jQuery(this.element).on('video-bg-loaded', callback);
    }
  }, {
    key: "onResize",
    value: function onResize(callback) {
      var debounce = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 100;
      callback = jQuery.debounce(callback, debounce);
      jQuery(window).resize(callback);
      jQuery(window).on('orientationchange', callback);
    }
  }], [{
    key: "test",
    value: function test() {
      return false;
    }
  }]);

  return BaseHandler;
}();



/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/background/video/handlers/handlers.js":
/*!*************************************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/background/video/handlers/handlers.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _native_handler__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./native-handler */ "./kubio-plugin/src/packages/scripts/src/background/video/handlers/native-handler.js");
/* harmony import */ var _youtube_handler__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./youtube-handler */ "./kubio-plugin/src/packages/scripts/src/background/video/handlers/youtube-handler.js");


var Handlers = {
  "native": _native_handler__WEBPACK_IMPORTED_MODULE_0__["default"],
  youtube: _youtube_handler__WEBPACK_IMPORTED_MODULE_1__["default"]
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Handlers);

/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/background/video/handlers/native-handler.js":
/*!*******************************************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/background/video/handlers/native-handler.js ***!
  \*******************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ NativeHandler)
/* harmony export */ });
/* harmony import */ var _base_handler__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./base-handler */ "./kubio-plugin/src/packages/scripts/src/background/video/handlers/base-handler.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var NativeHandler = /*#__PURE__*/function (_BaseHandler) {
  _inherits(NativeHandler, _BaseHandler);

  var _super = _createSuper(NativeHandler);

  function NativeHandler(element, settings) {
    var _this;

    _classCallCheck(this, NativeHandler);

    _this = _super.call(this, element, settings);
    return _possibleConstructorReturn(_this, _assertThisInitialized(_this));
  }

  _createClass(NativeHandler, [{
    key: "isPaused",
    value: function isPaused() {
      return this.video.paused;
    }
  }, {
    key: "ready",
    value: function ready() {
      var _this2 = this;

      if (this.settings.poster) {
        this.element.style.backgroundImage = "url(\"".concat(this.settings.poster, "\")");
      }

      if (!this.settings.videoUrl) {
        return;
      }

      var video = document.createElement('video');
      video.id = this.settings.id || ''; // video.autoplay = 'autoplay';

      video.loop = 'loop';
      video.muted = 'muted';
      video.autoplay = 'autoplay';
      video.setAttribute('playsinline', true);

      if (this.settings.width) {
        video.width = this.settings.width;
      }

      if (this.settings.height) {
        video.height = this.settings.height;
      }

      video.addEventListener('play', function () {
        _this2.trigger('play');
      });
      video.addEventListener('pause', function () {
        _this2.trigger('pause');
      });
      video.addEventListener('loadeddata', function () {
        _this2.loaded();
      });
      this.video = video;
      this.setVideo(video);
      video.src = this.settings.videoUrl;
    }
  }, {
    key: "pause",
    value: function pause() {
      this.video.pause();
    }
  }, {
    key: "stopVideo",
    value: function stopVideo() {
      this.video.pause();
      this.video.currentTime = 0;
    }
  }, {
    key: "play",
    value: function play() {
      this.video.play();
    }
  }], [{
    key: "test",
    value: function test(settings) {
      var video = document.createElement('video');
      return video.canPlayType(settings.mimeType);
    }
  }]);

  return NativeHandler;
}(_base_handler__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/background/video/handlers/youtube-handler.js":
/*!********************************************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/background/video/handlers/youtube-handler.js ***!
  \********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ YouTubeHandler)
/* harmony export */ });
/* harmony import */ var _base_handler__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./base-handler */ "./kubio-plugin/src/packages/scripts/src/background/video/handlers/base-handler.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

/**
 * @global
 */

var VIDEO_ID_REGEX = /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|&v(?:i)?=))([^#&?]*).*/;

var YouTubeHandler = /*#__PURE__*/function (_BaseHandler) {
  _inherits(YouTubeHandler, _BaseHandler);

  var _super = _createSuper(YouTubeHandler);

  function YouTubeHandler(element, settings) {
    var _this;

    _classCallCheck(this, YouTubeHandler);

    _this = _super.call(this, element, settings);
    return _possibleConstructorReturn(_this, _assertThisInitialized(_this));
  }

  _createClass(YouTubeHandler, [{
    key: "ready",
    value: function ready() {
      var _this2 = this;

      if (this.settings.poster) {
        this.element.style.backgroundImage = "url(\"".concat(this.settings.poster, "\")");
      }

      if ('YT' in window) {
        window.YT.ready(function () {
          _this2.loadVideo();
        });
      } else {
        var tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';

        tag.onload = function () {
          window.YT.ready(function () {
            _this2.loadVideo();
          });
        };

        document.getElementsByTagName('head')[0].appendChild(tag);
      }
    }
  }, {
    key: "getVideoID",
    value: function getVideoID() {
      var matches = this.settings.videoUrl.match(VIDEO_ID_REGEX);

      if (matches && matches.length >= 2) {
        return matches[1];
      }

      return null;
    }
  }, {
    key: "getYTOptions",
    value: function getYTOptions() {
      var _this3 = this;

      var options = {
        videoId: this.getVideoID(),
        events: {
          onReady: function onReady(e) {
            var ytVideo = e.target; //added mute param, not sure if this mute function call is needed anymore.

            ytVideo.mute();
            top.yt1 = ytVideo;
            ytVideo.setPlaybackQuality('auto');

            _this3.play();

            _this3.loaded();
          },
          onStateChange: function onStateChange(e) {
            if (window.YT.PlayerState.PLAYING === e.data) {
              _this3.trigger('play');
            } else if (window.YT.PlayerState.PAUSED === e.data) {
              _this3.trigger('pause');
            } else if (window.YT.PlayerState.ENDED === e.data) {
              e.target.playVideo();
            }
          },
          onError: function onError(e) {
            _this3.player.getIframe().style.display = 'none';
          }
        },
        playerVars: {
          autoplay: 1,
          controls: 0,
          disablekb: 1,
          fs: 0,
          iv_load_policy: 3,
          loop: 1,
          modestbranding: 1,
          playsinline: 1,
          rel: 0,
          showinfo: 0,

          /**
           * Sometimes the mute function used in the onRead event did not work, but using this options the videos are
           * always muted
           */
          mute: 1
        }
      };

      if (this.settings.height) {
        options.height = this.settings.height;
      } else {
        options.height = 1080;
      }

      if (this.settings.width) {
        options.width = this.settings.width;
      } else {
        options.width = 1920;
      } // height: this.settings.height,
      // width: this.settings.width,


      return options;
    }
  }, {
    key: "loadVideo",
    value: function loadVideo() {
      var video = document.createElement('div'),
          YT = window.YT;
      this.setVideo(video);
      this.player = new window.YT.Player(video, this.getYTOptions());
    }
  }, {
    key: "updateVideoSize",
    value: function updateVideoSize() {
      if (!this.player) {
        return;
      }

      var $iframe = jQuery(this.player.getIframe()),
          size = this.calcVideosSize();
      $iframe.css(size);
      $iframe.addClass('ready');
    }
  }, {
    key: "calcVideosSize",
    value: function calcVideosSize() {
      var width = jQuery(this.element).outerWidth(),
          height = jQuery(this.element).outerHeight(),
          aspectRatio = '16:9'.split(':'),
          proportion = aspectRatio[0] / aspectRatio[1],
          keepWidth = width / height > proportion,
          magnifier = 1;
      return {
        width: magnifier * (keepWidth ? width : height * proportion),
        height: magnifier * (keepWidth ? width / proportion : height)
      };
    }
  }, {
    key: "play",
    value: function play() {
      if (!!this.player && !!this.player.playVideo) {
        if (!this.isPlaying) {
          this.isPlaying = true;
          this.player.playVideo();
        }
      }
    }
  }, {
    key: "stopVideo",
    value: function stopVideo() {
      if (!!this.player && !!this.player.stopVideo) {
        if (this.isPlaying) {
          this.isPlaying = false;
          this.player.stopVideo();
        }
      }
    }
  }, {
    key: "pause",
    value: function pause() {
      if (!!this.player && !!this.player.pauseVideo && !this.isPlaying) {
        this.isPlaying = false;
        this.player.pauseVideo();
      }
    }
  }, {
    key: "isPaused",
    value: function isPaused() {
      return YT.PlayerState.PAUSED === this.player.getPlayerState();
    }
  }, {
    key: "loaded",
    value: function loaded() {
      this.updateVideoSize();

      _get(_getPrototypeOf(YouTubeHandler.prototype), "loaded", this).call(this);
    }
  }, {
    key: "addResizeBind",
    value: function addResizeBind() {
      var _this4 = this;

      this.onResize(function () {
        return _this4.updateVideoSize();
      }, 50);

      _get(_getPrototypeOf(YouTubeHandler.prototype), "addResizeBind", this).call(this);
    }
  }], [{
    key: "test",
    value: function test(settings) {
      return 'video/x-youtube' === settings.mimeType;
    }
  }]);

  return YouTubeHandler;
}(_base_handler__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/background/video/video-bg.js":
/*!****************************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/background/video/video-bg.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ VideoBackground)
/* harmony export */ });
/* harmony import */ var _handlers_handlers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./handlers/handlers */ "./kubio-plugin/src/packages/scripts/src/background/video/handlers/handlers.js");
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../base */ "./kubio-plugin/src/packages/scripts/src/base/index.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }




var VideoBackground = /*#__PURE__*/function (_ColibriFrontComponen) {
  _inherits(VideoBackground, _ColibriFrontComponen);

  var _super = _createSuper(VideoBackground);

  function VideoBackground() {
    _classCallCheck(this, VideoBackground);

    return _super.apply(this, arguments);
  }

  _createClass(VideoBackground, [{
    key: "init",
    value: function init() {
      var _this = this;

      this.videoData = {};
      this.handler = false; // eslint-disable-next-line no-undef

      this.debouncedSetPosition = jQuery.debounce(this.updateVideoBackground.bind(this), 100);

      this.resizeObserve = function (e) {
        _this.debouncedSetPosition();
      };

      this.resizeObserver = new window.ResizeObserver(this.resizeObserve);
    }
  }, {
    key: "generateVideo",
    value: function generateVideo() {
      var _this2 = this;

      for (var handle in _handlers_handlers__WEBPACK_IMPORTED_MODULE_0__["default"]) {
        if (_handlers_handlers__WEBPACK_IMPORTED_MODULE_0__["default"].hasOwnProperty(handle) && _handlers_handlers__WEBPACK_IMPORTED_MODULE_0__["default"][handle].test(this.videoData)) {
          this.$element.empty();
          this.handler = new _handlers_handlers__WEBPACK_IMPORTED_MODULE_0__["default"][handle](this.$element[0], this.videoData);
          break;
        }
      }

      if (!this.handler) {
        return;
      }

      this.handler.onLoad(function () {
        // this.$element.children('iframe,video').addClass('h-hide-sm-force');
        _this2.debouncedSetPosition();

        _this2.handler.onResize(function () {
          return _this2.debouncedSetPosition();
        });

        _this2.resizeObserver.observe(_this2.handler.element);
      });

      if (window.hop) {
        window.addResizeListener(this.$element.closest('.background-wrapper').parent()[0], this.debouncedSetPosition);
        this.debouncedSetPosition();
      }
    }
  }, {
    key: "stopVideo",
    value: function stopVideo() {
      if (this.handler.stopVideo) {
        this.handler.stopVideo();
      }
    }
  }, {
    key: "play",
    value: function play() {
      if (this.handler.play) {
        this.handler.play();
      }
    }
  }, {
    key: "updateVideoBackground",
    value: function updateVideoBackground() {
      if (this.handler.updateVideoSize) {
        this.handler.updateVideoSize();
      }

      this.setPosition();
    }
  }, {
    key: "setPosition",
    value: function setPosition() {
      var _this3 = this;

      this.handler.pause();

      if (this.$element.children('iframe,video').eq(0).css('display') === 'none') {
        return;
      }

      var $video = this.$element.children('iframe,video').eq(0),
          posX = $video.is('iframe') ? 50 : this.opts.positionX,
          posY = $video.is('iframe') ? 50 : this.opts.positionY,
          x = Math.max($video.width() - this.$element.width(), 0) * parseFloat(posX) / 100,
          y = Math.max($video.height() - this.$element.height(), 0) * parseFloat(posY) / 100;
      $video.css({
        transform: "translate(-".concat(x, "px,-").concat(y, "px)"),
        '-webkit-transform': "translate(-".concat(x, "px,-").concat(y, "px)")
      });
      this.$element.addClass('visible');
      setTimeout(function () {
        _this3.handler.play();
      }, 100);
    }
  }, {
    key: "start",
    value: function start() {
      this.videoData = {
        mimeType: this.opts.mimeType,
        videoUrl: this.opts.video
      };

      if (typeof this.opts.poster === 'string') {
        this.poster = this.opts.poster;
      }

      this.generateVideo();
    }
  }, {
    key: "stop",
    value: function stop() {
      window.removeResizeListener(this.$element.closest('.background-wrapper').parent()[0], this.debouncedSetPosition);
    }
  }, {
    key: "restart",
    value: function restart() {
      this.stop();
      this.start();
    }
  }], [{
    key: "componentName",
    value: function componentName() {
      return 'video-background';
    }
  }]);

  return VideoBackground;
}(_base__WEBPACK_IMPORTED_MODULE_1__.ColibriFrontComponent);



/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/base/colibri-kube-component.js":
/*!******************************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/base/colibri-kube-component.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ColibriFrontComponent)
/* harmony export */ });
/* harmony import */ var _colibri__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./colibri */ "./kubio-plugin/src/packages/scripts/src/base/colibri.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }



var ColibriFrontComponent = /*#__PURE__*/function () {
  function ColibriFrontComponent(element, options) {
    _classCallCheck(this, ColibriFrontComponent);

    this.$ = jQuery;
    this.namespace = this.constructor.componentName();
    this.utils = new _colibri__WEBPACK_IMPORTED_MODULE_0__["default"].Utils();
    this.detect = new _colibri__WEBPACK_IMPORTED_MODULE_0__["default"].Detect();
    this.init();
    _colibri__WEBPACK_IMPORTED_MODULE_0__["default"].apply(this, arguments);
    this.start();

    if (this.isCustomizerPreview()) {
      this.wpCustomize(wp.customize);
    }

    return this;
  }

  _createClass(ColibriFrontComponent, [{
    key: "init",
    value: function init() {}
  }, {
    key: "isCustomizerPreview",
    value: function isCustomizerPreview() {
      return _colibri__WEBPACK_IMPORTED_MODULE_0__["default"].isCustomizerPreview();
    }
  }, {
    key: "wpCustomize",
    value: function wpCustomize(api) {}
  }, {
    key: "wpSettingBind",
    value: function wpSettingBind(setting_id, callback) {
      window.wp.customize(setting_id, function (setting) {
        setting.bind(callback);
      });
    }
  }, {
    key: "updateData",
    value: function updateData(data) {
      this.opts = jQuery.extend({}, this.opts, data);
      this.restart();
    }
  }, {
    key: "restart",
    value: function restart() {}
  }, {
    key: "start",
    value: function start() {}
  }], [{
    key: "componentName",
    value: function componentName() {
      throw new TypeError('name getter should be implemented');
    }
  }]);

  return ColibriFrontComponent;
}();



/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/base/colibri.js":
/*!***************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/base/colibri.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function ColibriBase() {
  var $ = jQuery;

  if (typeof jQuery === 'undefined') {
    throw new Error('Colibri requires jQuery');
  }

  (function () {
    var version = $.fn.jquery.split('.');

    if (version[0] === 1 && version[1] < 8) {
      throw new Error('Colibri requires at least jQuery v1.8');
    }
  })();

  function debounce(func) {
    var _this = this;

    var timeout = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 300;
    var timer;
    return function () {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      clearTimeout(timer);
      timer = setTimeout(function () {
        func.apply(_this, args);
      }, timeout);
    };
  }

  var Colibri;
  var libName = 'kubio';
  var libPrefix = libName + '.';
  var settingsAttr = libName + '-settings';

  (function () {
    // Inherits
    Function.prototype.inherits = function (parent) {
      var F = function F() {};

      F.prototype = parent.prototype;
      var f = new F();

      for (var prop in this.prototype) {
        f[prop] = this.prototype[prop];
      }

      this.prototype = f;
      this.prototype["super"] = parent.prototype;
    }; // Core Class


    Colibri = function Colibri(element, options) {
      options = _typeof(options) === 'object' ? options : {};
      this.$element = $(element);
      var elementData = this.$element.data();
      this.settings = this.$element.data(settingsAttr) || {};
      this.opts = $.extend(true, {}, this.defaults, $.fn[libPrefix + this.namespace].options, elementData, this.settings, options);
      this.$target = typeof this.opts.target === 'string' ? $(this.opts.target) : null;
    };

    Colibri.getScrollingElement = function () {
      var element = window;

      if (this.isBlockEditor() && top === window) {
        element = document.querySelector('.interface-interface-skeleton__content');
      }

      return element;
    };

    Colibri.isCustomizerPreview = function () {
      return !!window.colibriCustomizerPreviewData;
    };

    Colibri.isBlockEditor = function () {
      var _top, _top$wp, _top2, _top2$kubio;

      //the block library is added because of this https://mantis.iconvert.pro/view.php?id=54821. Some hosting providers add the blockEditor package in the frontend
      return !!((_top = top) !== null && _top !== void 0 && (_top$wp = _top.wp) !== null && _top$wp !== void 0 && _top$wp.blockEditor) && ((_top2 = top) === null || _top2 === void 0 ? void 0 : (_top2$kubio = _top2.kubio) === null || _top2$kubio === void 0 ? void 0 : _top2$kubio.blockLibrary);
    }; // Core Functionality


    Colibri.prototype = {
      updateOpts: function updateOpts(updatedData) {
        var newSetting = this.$element.attr('data-' + settingsAttr);

        if (newSetting) {
          this.settings = JSON.parse(newSetting);
        }

        var instanceData = $.extend(true, {}, this.defaults, this.settings);
        var updatedDataWithDefault = updatedData ? updatedData : {};
        this.opts = $.extend(true, this.opts, instanceData, updatedDataWithDefault);
      },
      getInstance: function getInstance() {
        return this.$element.data('fn.' + this.namespace);
      },
      hasTarget: function hasTarget() {
        return !(this.$target === null);
      },
      callback: function callback(type) {
        var args = [].slice.call(arguments).splice(1); // on element callback

        if (this.$element) {
          args = this._fireCallback($._data(this.$element[0], 'events'), type, this.namespace, args);
        } // on target callback


        if (this.$target) {
          args = this._fireCallback($._data(this.$target[0], 'events'), type, this.namespace, args);
        } // opts callback


        if (this.opts && this.opts.callbacks && typeof this.opts.callbacks[type] === 'function') {
          return this.opts.callbacks[type].apply(this, args);
        }

        return args;
      },
      _fireCallback: function _fireCallback(events, type, eventNamespace, args) {
        var value;

        if (events && typeof events[type] !== 'undefined') {
          var len = events[type].length;

          for (var i = 0; i < len; i++) {
            var namespace = events[type][i].namespace;

            if (namespace === eventNamespace) {
              value = events[type][i].handler.apply(this, args);
            }
          }
        }

        return typeof value === 'undefined' ? args : value;
      }
    };
  })();

  (function (Colibri_) {
    Colibri_.Plugin = {
      create: function create(classname, pluginname) {
        pluginname = typeof pluginname === 'undefined' ? classname.toLowerCase() : pluginname;
        pluginname = libPrefix + pluginname;

        $.fn[pluginname] = function (method, options) {
          var args = Array.prototype.slice.call(arguments, 1);
          var name = 'fn.' + pluginname;
          var val = [];
          this.each(function () {
            var $this = $(this);
            var data = $this.data(name);
            options = _typeof(method) === 'object' ? method : options;

            if (!data) {
              // Initialization
              $this.data(name, {});
              data = new Colibri_[classname](this, options);
              $this.data(name, data);
            } // Call methods


            if (typeof method === 'string') {
              if ($.isFunction(data[method])) {
                var methodVal = data[method].apply(data, args);

                if (methodVal !== undefined) {
                  val.push(methodVal);
                }
              } else {
                $.error('No such method "' + method + '" for ' + classname);
              }
            }
          }); // eslint-disable-next-line no-nested-ternary

          return val.length === 0 || val.length === 1 ? val.length === 0 ? this : val[0] : val;
        };

        $.fn[pluginname].options = {};
        return this;
      },
      autoload: function autoload(pluginname) {
        var arr = pluginname.split(',');
        var len = arr.length;

        for (var i = 0; i < len; i++) {
          var name = arr[i].toLowerCase().split(',').map(function (s) {
            return libPrefix + s.trim();
          }).join(',');
          this.autoloadQueue.push(name);
        }

        return this;
      },
      autoloadQueue: [],
      startAutoload: function startAutoload() {
        if (!window.MutationObserver || this.autoloadQueue.length === 0) {
          return;
        }

        if (this.observer) {
          this.observer.disconnect();
        }

        var self = this;
        var observer = new MutationObserver(function (mutations) {
          mutations.forEach(function (mutation) {
            var newNodes = mutation.addedNodes;

            if (newNodes.length === 0 || newNodes.length === 1 && newNodes[0].nodeType === 3) {
              return;
            }

            self.startAutoloadOnceDebounced();
          });
        });
        this.observer = observer; // pass in the target node, as well as the observer options

        var observedElement = document.querySelector('.editor-styles-wrapper .is-root-container.block-editor-block-list__layout');

        if (!observedElement) {
          observedElement = document;
        }

        observer.observe(observedElement, {
          subtree: true,
          childList: true
        });
      },
      startAutoloadOnceDebounced: debounce(function () {
        Colibri.Plugin.startAutoloadOnce();
      }, 300),
      startAutoloadOnce: function startAutoloadOnce() {
        var self = this;
        var attrName = libName + '-component';
        var $nodes = $('[data-' + attrName + ']:not([data-loaded]):not([data-disabled])');
        $nodes.each(function () {
          var $el = $(this);
          var pluginname = libPrefix + $el.data(attrName);

          if (self.autoloadQueue.indexOf(pluginname) !== -1) {
            $el.attr('data-loaded', true);

            try {
              $el[pluginname]();
            } catch (e) {
              // eslint-disable-next-line no-console
              console.error(e);
            }
          }
        });
      },
      stopWatcher: function stopWatcher() {
        var _this$observer, _this$observer$discon;

        (_this$observer = this.observer) === null || _this$observer === void 0 ? void 0 : (_this$observer$discon = _this$observer.disconnect) === null || _this$observer$discon === void 0 ? void 0 : _this$observer$discon.call(_this$observer);
      },
      watch: function watch() {
        Colibri_.Plugin.startAutoloadOnce();
        Colibri_.Plugin.startAutoload();
      },
      init: function init() {
        if (window.isKubioBlockEditor && !window.isInsideIframe) {
          return;
        }

        if ($.isReady) {
          Colibri_.Plugin.watch();
        } else {
          $(document).ready(Colibri_.Plugin.watch);
        }
      }
    };
    Colibri_.Plugin.init();
  })(Colibri);

  (function (Colibri_) {
    Colibri_.Animation = function (element, effect, callback) {
      this.namespace = 'animation';
      this.defaults = {}; // Parent Constructor

      Colibri_.apply(this, arguments); // Initialization

      this.effect = effect;
      this.completeCallback = typeof callback === 'undefined' ? false : callback;
      this.prefixes = ['', '-moz-', '-o-animation-', '-webkit-'];
      this.queue = [];
      this.start();
    };

    Colibri_.Animation.prototype = {
      start: function start() {
        if (this.isSlideEffect()) {
          this.setElementHeight();
        }

        this.addToQueue();
        this.clean();
        this.animate();
      },
      addToQueue: function addToQueue() {
        this.queue.push(this.effect);
      },
      setElementHeight: function setElementHeight() {
        this.$element.height(this.$element.outerHeight());
      },
      removeElementHeight: function removeElementHeight() {
        this.$element.css('height', '');
      },
      isSlideEffect: function isSlideEffect() {
        return this.effect === 'slideDown' || this.effect === 'slideUp';
      },
      isHideableEffect: function isHideableEffect() {
        var effects = ['fadeOut', 'slideUp', 'flipOut', 'zoomOut', 'slideOutUp', 'slideOutRight', 'slideOutLeft'];
        return $.inArray(this.effect, effects) !== -1;
      },
      isToggleEffect: function isToggleEffect() {
        return this.effect === 'show' || this.effect === 'hide';
      },
      storeHideClasses: function storeHideClasses() {
        if (this.$element.hasClass('hide-sm')) {
          this.$element.data('hide-sm-class', true);
        } else if (this.$element.hasClass('hide-md')) {
          this.$element.data('hide-md-class', true);
        }
      },
      revertHideClasses: function revertHideClasses() {
        if (this.$element.data('hide-sm-class')) {
          this.$element.addClass('hide-sm').removeData('hide-sm-class');
        } else if (this.$element.data('hide-md-class')) {
          this.$element.addClass('hide-md').removeData('hide-md-class');
        } else {
          this.$element.addClass('hide');
        }
      },
      removeHideClass: function removeHideClass() {
        if (this.$element.data('hide-sm-class')) {
          this.$element.removeClass('hide-sm');
        } else if (this.$element.data('hide-md-class')) {
          this.$element.removeClass('hide-md');
        } else {
          this.$element.removeClass('hide');
          this.$element.removeClass('force-hide');
        }
      },
      animate: function animate() {
        this.storeHideClasses();

        if (this.isToggleEffect()) {
          return this.makeSimpleEffects();
        }

        this.$element.addClass('kubio-animated');
        this.$element.addClass(this.queue[0]);
        this.removeHideClass();

        var _callback = this.queue.length > 1 ? null : this.completeCallback;

        this.complete('AnimationEnd', $.proxy(this.makeComplete, this), _callback);
      },
      makeSimpleEffects: function makeSimpleEffects() {
        if (this.effect === 'show') {
          this.removeHideClass();
        } else if (this.effect === 'hide') {
          this.revertHideClasses();
        }

        if (typeof this.completeCallback === 'function') {
          this.completeCallback(this);
        }
      },
      makeComplete: function makeComplete() {
        if (this.$element.hasClass(this.queue[0])) {
          this.clean();
          this.queue.shift();

          if (this.queue.length) {
            this.animate();
          }
        }
      },
      complete: function complete(type, make, callback) {
        var events = type.split(' ').map(function (type_) {
          return type_.toLowerCase() + ' webkit' + type_ + ' o' + type_ + ' MS' + type_;
        });
        this.$element.one(events.join(' '), $.proxy(function () {
          if (typeof make === 'function') {
            make();
          }

          if (this.isHideableEffect()) {
            this.revertHideClasses();
          }

          if (this.isSlideEffect()) {
            this.removeElementHeight();
          }

          if (typeof callback === 'function') {
            callback(this);
          }

          this.$element.off(events.join(' '));
        }, this));
      },
      clean: function clean() {
        this.$element.removeClass('kubio-animated').removeClass(this.queue[0]);
      }
    }; // Inheritance

    Colibri_.Animation.inherits(Colibri_);
  })(Colibri);

  (function () {
    var animationName = libPrefix + 'animation';

    $.fn[animationName] = function (effect, callback) {
      var name = 'fn.animation';
      return this.each(function () {
        var $this = $(this);
        $this.data(name, {});
        $this.data(name, new Colibri.Animation(this, effect, callback));
      });
    };

    $.fn[animationName].options = {};

    Colibri.animate = function ($target, effect, callback) {
      $target[animationName](effect, callback);
      return $target;
    };
  })();

  (function (Colibri_) {
    Colibri_.Detect = function () {};

    Colibri_.Detect.prototype = {
      isMobile: function isMobile() {
        return /(iPhone|iPod|BlackBerry|Android)/.test(navigator.userAgent);
      },
      isDesktop: function isDesktop() {
        return !/(iPhone|iPod|iPad|BlackBerry|Android)/.test(navigator.userAgent);
      },
      isMobileScreen: function isMobileScreen() {
        return $(window).width() <= 768;
      },
      isTabletScreen: function isTabletScreen() {
        return $(window).width() >= 768 && $(window).width() <= 1024;
      },
      isDesktopScreen: function isDesktopScreen() {
        return $(window).width() > 1024;
      }
    };
  })(Colibri);

  (function (Colibri_) {
    Colibri_.Utils = function () {};

    Colibri_.Utils.prototype = {
      disableBodyScroll: function disableBodyScroll() {
        var $body = $('html');
        var windowWidth = window.innerWidth;

        if (!windowWidth) {
          var documentElementRect = document.documentElement.getBoundingClientRect();
          windowWidth = documentElementRect.right - Math.abs(documentElementRect.left);
        }

        var isOverflowing = document.body.clientWidth < windowWidth;
        var scrollbarWidth = this.measureScrollbar();
        $body.css('overflow', 'hidden');

        if (isOverflowing) {
          $body.css('padding-right', scrollbarWidth);
        }
      },
      measureScrollbar: function measureScrollbar() {
        var $body = $('body');
        var scrollDiv = document.createElement('div');
        scrollDiv.className = 'scrollbar-measure';
        $body.append(scrollDiv);
        var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
        $body[0].removeChild(scrollDiv);
        return scrollbarWidth;
      },
      enableBodyScroll: function enableBodyScroll() {
        $('html').css({
          overflow: '',
          'padding-right': ''
        });
      }
    };
  })(Colibri);

  return Colibri;
}

var Base = ColibriBase();
window.Colibri = Base;
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Base);

/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/base/index.js":
/*!*************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/base/index.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "ColibriFrontend": () => (/* reexport safe */ _colibri__WEBPACK_IMPORTED_MODULE_0__["default"]),
/* harmony export */   "ColibriFrontComponent": () => (/* reexport safe */ _colibri_kube_component__WEBPACK_IMPORTED_MODULE_1__["default"])
/* harmony export */ });
/* harmony import */ var _colibri__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./colibri */ "./kubio-plugin/src/packages/scripts/src/base/colibri.js");
/* harmony import */ var _colibri_kube_component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./colibri-kube-component */ "./kubio-plugin/src/packages/scripts/src/base/colibri-kube-component.js");



_colibri__WEBPACK_IMPORTED_MODULE_0__["default"].registerPlugin = function (name, plugin, autoload) {
  if (typeof name.componentName === 'function') {
    autoload = plugin;
    plugin = name;
    name = plugin.componentName();
  }

  _colibri__WEBPACK_IMPORTED_MODULE_0__["default"][name] = plugin; // Colibri[name].inherits(Colibri);

  _colibri__WEBPACK_IMPORTED_MODULE_0__["default"].Plugin.create(name);

  if (autoload !== false) {
    _colibri__WEBPACK_IMPORTED_MODULE_0__["default"].Plugin.autoload(name);
  }
};



/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/detect-element-resize.js":
/*!************************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/detect-element-resize.js ***!
  \************************************************************************/
/***/ (() => {

/**
 * Detect Element Resize
 *
 * https://github.com/sdecima/javascript-detect-element-resize
 * Sebastian Decima
 *
 * version: 0.5.3
 */
var attachEvent = document.attachEvent,
    stylesCreated = false;

function resetTriggers(element) {
  var triggers = element.__resizeTriggers__,
      expand = triggers.firstElementChild,
      contract = triggers.lastElementChild,
      expandChild = expand.firstElementChild;
  contract.scrollLeft = contract.scrollWidth;
  contract.scrollTop = contract.scrollHeight;
  expandChild.style.width = expand.offsetWidth + 1 + 'px';
  expandChild.style.height = expand.offsetHeight + 1 + 'px';
  expand.scrollLeft = expand.scrollWidth;
  expand.scrollTop = expand.scrollHeight;
}

function checkTriggers(element) {
  return element.offsetWidth != element.__resizeLast__.width || element.offsetHeight != element.__resizeLast__.height;
}

function scrollListener(e) {
  var element = this;
  resetTriggers(this);

  if (this.__resizeRAF__) {
    cancelFrame(this.__resizeRAF__);
  }

  this.__resizeRAF__ = requestFrame(function () {
    if (checkTriggers(element)) {
      element.__resizeLast__.width = element.offsetWidth;
      element.__resizeLast__.height = element.offsetHeight;

      element.__resizeListeners__.forEach(function (fn) {
        fn.call(element, e);
      });
    }
  });
}

if (!attachEvent) {
  var requestFrame = function () {
    var raf = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || function (fn) {
      return window.setTimeout(fn, 20);
    };

    return function (fn) {
      return raf(fn);
    };
  }();

  var cancelFrame = function () {
    var cancel = window.cancelAnimationFrame || window.mozCancelAnimationFrame || window.webkitCancelAnimationFrame || window.clearTimeout;
    return function (id) {
      return cancel(id);
    };
  }();
  /* Detect CSS Animations support to detect element display/re-attach */


  var animation = false,
      animationstring = 'animation',
      keyframeprefix = '',
      animationstartevent = 'animationstart',
      domPrefixes = 'Webkit Moz O ms'.split(' '),
      startEvents = 'webkitAnimationStart animationstart oAnimationStart MSAnimationStart'.split(' '),
      pfx = '';
  {
    var elm = document.createElement('fakeelement');

    if (elm.style.animationName !== undefined) {
      animation = true;
    }

    if (animation === false) {
      for (var i = 0; i < domPrefixes.length; i++) {
        if (elm.style[domPrefixes[i] + 'AnimationName'] !== undefined) {
          pfx = domPrefixes[i];
          animationstring = pfx + 'Animation';
          keyframeprefix = '-' + pfx.toLowerCase() + '-';
          animationstartevent = startEvents[i];
          animation = true;
          break;
        }
      }
    }
  }
  var animationName = 'resizeanim';
  var animationKeyframes = '@' + keyframeprefix + 'keyframes ' + animationName + ' { from { opacity: 0; } to { opacity: 0; } } ';
  var animationStyle = keyframeprefix + 'animation: 1ms ' + animationName + '; ';
}

function createStyles() {
  if (!stylesCreated) {
    //opacity:0 works around a chrome bug https://code.google.com/p/chromium/issues/detail?id=286360
    var css = (animationKeyframes ? animationKeyframes : '') + '.resize-triggers { ' + (animationStyle ? animationStyle : '') + 'visibility: hidden; opacity: 0; } ' + '.resize-triggers, .resize-triggers > div, .contract-trigger:before { content: " "; display: block; position: absolute; top: 0; left: 0; height: 100%; width: 100%; overflow: hidden; } .resize-triggers > div { background: #eee; overflow: auto; } .contract-trigger:before { width: 200%; height: 200%; }',
        head = document.head || document.getElementsByTagName('head')[0],
        style = document.createElement('style');
    style.type = 'text/css';

    if (style.styleSheet) {
      style.styleSheet.cssText = css;
    } else {
      style.appendChild(document.createTextNode(css));
    }

    head.appendChild(style);
    stylesCreated = true;
  }
}

window.addResizeListener = function (element, fn) {
  if (attachEvent) {
    element.attachEvent('onresize', fn);
  } else {
    if (!element.__resizeTriggers__) {
      if (getComputedStyle(element).position == 'static') {
        element.style.position = 'relative';
      }

      createStyles();
      element.__resizeLast__ = {};
      element.__resizeListeners__ = [];
      (element.__resizeTriggers__ = document.createElement('div')).className = 'resize-triggers';
      element.__resizeTriggers__.innerHTML = '<div class="expand-trigger"><div></div></div>' + '<div class="contract-trigger"></div>';
      element.appendChild(element.__resizeTriggers__);
      resetTriggers(element);
      element.addEventListener('scroll', scrollListener, {
        passive: true
      });
      /* Listen for a css animation to detect element display/re-attach */

      if (animationstartevent) {
        element.__resizeTriggers__.addEventListener(animationstartevent, function (e) {
          if (e.animationName == animationName) {
            resetTriggers(element);
          }
        });
      }
    }

    element.__resizeListeners__.push(fn);
  }
};

window.removeResizeListener = function (element, fn) {
  if (attachEvent) {
    element.detachEvent('onresize', fn);
  } else {
    if (!(element && element.__resizeListeners__ && element.__resizeTriggers__)) {
      return;
    }

    element.__resizeListeners__.splice(element.__resizeListeners__.indexOf(fn), 1);

    if (!element.__resizeListeners__.length) {
      element.removeEventListener('scroll', scrollListener);
      element.__resizeTriggers__ = !element.removeChild(element.__resizeTriggers__);
    }
  }
};

/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/jquery-extensions.js":
/*!********************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/jquery-extensions.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var lodash_debounce__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash.debounce */ "./node_modules/lodash.debounce/index.js");
/* harmony import */ var lodash_debounce__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash_debounce__WEBPACK_IMPORTED_MODULE_0__);


(function ($) {
  if (!$.throttle) {
    $.throttle = function (fn, threshhold, scope) {
      threshhold || (threshhold = 250);
      var last, deferTimer;
      return function () {
        var context = scope || this;
        var now = +new Date(),
            args = arguments;

        if (last && now < last + threshhold) {
          // hold on to it
          clearTimeout(deferTimer);
          deferTimer = setTimeout(function () {
            last = now;
            fn.apply(context, args);
          }, threshhold);
        } else {
          last = now;
          fn.apply(context, args);
        }
      };
    };
  }

  if (!$.debounce) {
    $.debounce = (lodash_debounce__WEBPACK_IMPORTED_MODULE_0___default()); // $.debounce = function(func, wait, immediate) {
    //   var timeout;
    //   return function() {
    //     var context = this,
    //       args = arguments;
    //     var later = function() {
    //       timeout = null;
    //       if (!immediate) {
    //         func.apply(context, args);
    //       }
    //     };
    //     var callNow = immediate && !timeout;
    //     clearTimeout(timeout);
    //     timeout = setTimeout(later, wait);
    //     if (callNow) {
    //       func.apply(context, args);
    //     }
    //   };
    // };
  }

  if (!$.event.special.tap) {
    $.event.special.tap = {
      setup: function setup(data, namespaces) {
        var $elem = $(this);
        $elem.on('touchstart', $.event.special.tap.handler).on('touchmove', $.event.special.tap.handler).on('touchend', $.event.special.tap.handler);
      },
      teardown: function teardown(namespaces) {
        var $elem = $(this);
        $elem.off('touchstart', $.event.special.tap.handler).off('touchmove', $.event.special.tap.handler).off('touchend', $.event.special.tap.handler);
      },
      handler: function handler(event) {
        var $elem = $(this);
        $elem.data(event.type, 1);

        if (event.type === 'touchend' && !$elem.data('touchmove')) {
          event.type = 'tap';
          $.event.dispatch.call(this, event);
        } else if ($elem.data('touchend')) {
          $elem.removeData('touchstart touchmove touchend');
        }
      }
    };
  } //is not supported on ie


  if (!$.fn.respondToVisibility) {
    $.fn.respondToVisibility = function (callback) {
      //check for ie
      if (!('IntersectionObserver' in window) || !('IntersectionObserverEntry' in window) || !('intersectionRatio' in window.IntersectionObserverEntry.prototype)) {
        return null;
      }

      var observer = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
          callback(entry.intersectionRatio > 0);
        });
      });
      observer.observe(this.get(0));
      return observer;
    };
  }
})(window.jQuery);

/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/kubio-smoothscroll.js":
/*!*********************************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/kubio-smoothscroll.js ***!
  \*********************************************************************/
/***/ (() => {

var linksDefineSamePage = function linksDefineSamePage(link1, link2) {
  var _ref = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {},
      _ref$compareQuery = _ref.compareQuery,
      compareQuery = _ref$compareQuery === void 0 ? false : _ref$compareQuery,
      _ref$compareHash = _ref.compareHash,
      compareHash = _ref$compareHash === void 0 ? false : _ref$compareHash;

  if (!link1 || !link2) {
    return true;
  }

  var url1 = null;
  var url2 = null;

  try {
    url1 = new URL(link1);
    url2 = new URL(link2);
  } catch (e) {
    return false;
  }

  var result = url1.origin === url2.origin && url1.pathname === url2.pathname;

  if (compareQuery) {
    result = result && url1.search === url2.search;
  }

  if (compareHash) {
    result = result && url1.hash === url2.hash;
  }

  return result;
};

(function ($) {
  function isInsideKubioEditor() {
    try {
      var _top, _top$wp;

      return window.isKubioBlockEditor || ((_top = top) === null || _top === void 0 ? void 0 : (_top$wp = _top.wp) === null || _top$wp === void 0 ? void 0 : _top$wp.blockEditor);
    } catch (e) {
      return false;
    }
  }

  if (window.location.hash === '#page-top') {
    changeUrlHash('', 5);
  }

  var __toCheckOnScroll = {
    items: {},
    eachCategory: function eachCategory(callback) {
      for (var id in this.items) {
        if (!this.items.hasOwnProperty(id)) {
          continue;
        }

        callback(this.items[id]);
      }
    },
    addItem: function addItem(id, item) {
      if (!this.items[id]) {
        this.items[id] = [];
      }

      this.items[id].push(item);
    },
    all: function all() {
      var result = [];

      for (var id in this.items) {
        if (!this.items.hasOwnProperty(id)) {
          continue;
        }

        result = result.concat(this.items[id]);
      }

      return result;
    }
  };
  var __alreadyScrolling = false;

  function getScrollToValue(elData) {
    var offset = !isNaN(parseFloat(elData.options.offset)) ? elData.options.offset : elData.options.offset.call(elData.target);
    var scrollToValue = elData.target.offset().top - offset - $('body').offset().top;
    return scrollToValue;
  }

  function changeUrlHash(hash, timeout) {
    if (hash === location.hash.replace('#', '') || hash === 'page-top' && '' === location.hash.replace('#', '')) {
      return;
    }

    setTimeout(function () {
      if (hash) {
        if (hash === 'page-top') {
          hash = ' ';
        } else {
          hash = '#' + hash;
        }
      } else {
        hash = ' ';
      }

      if (history && history.replaceState) {
        history.replaceState({}, '', hash);
      }
    }, timeout || 100);
    /* safari issue fixed by throtteling the event */
  }

  function scrollItem(elData) {
    if (__alreadyScrolling) {
      return;
    }

    __alreadyScrolling = true;
    var scrollToValue = getScrollToValue(elData);
    $('html, body').animate({
      scrollTop: scrollToValue
    }, {
      easing: 'linear',
      complete: function complete() {
        // check for any updates
        var scrollToValue = getScrollToValue(elData);
        $('html, body').animate({
          scrollTop: scrollToValue
        }, {
          easing: 'linear',
          duration: 100,
          complete: function complete() {
            __alreadyScrolling = false;
            changeUrlHash(elData.id, 5);
          }
        });
      }
    });
  }

  function getPageBaseUrl() {
    return [location.protocol, '//', location.host, location.pathname].join('');
  }

  function fallbackUrlParse(url) {
    return url.split('?')[0].split('#')[0];
  }

  function getABaseUrl(element) {
    var href = jQuery(element)[0].href || '';
    var url = '#';

    try {
      var _url = new window.URL(href);

      url = [_url.protocol, '//', _url.host, _url.pathname].join('');
    } catch (e) {
      url = fallbackUrlParse(href);
    }

    return url;
  }

  function getTargetForEl(element) {
    var targetId = (element.attr('href') || '').split('#').pop(),
        hrefBase = getABaseUrl(element),
        target = null,
        pageURL = getPageBaseUrl();

    if (hrefBase.length && hrefBase !== pageURL) {
      return target;
    }

    if (targetId.trim().length) {
      try {
        target = $('[id="' + targetId + '"]');
      } catch (e) {}
    }

    if (target && target.length) {
      return target;
    }

    return null;
  }

  $.fn.smoothScrollAnchor = function (options) {
    if (isInsideKubioEditor()) {
      return;
    }

    var elements = $(this);
    options = jQuery.extend({
      offset: function offset() {
        var $fixed = $('.h-navigation_sticky');

        if ($fixed.length) {
          return $fixed[0].getBoundingClientRect().height;
        }

        return 0;
      }
    }, options);
    elements.each(function () {
      var element = $(this); //if the target options is not set or the href is not for the same page don't add smoothscroll

      if (!options.target && !linksDefineSamePage(document.location.href, this.href)) {
        return;
      }

      var target = options.target || getTargetForEl(element);

      if (target && target.length && !target.attr('skip-smooth-scroll')) {
        var targetId = target.attr('id');
        var targetSel = null;

        if (targetId) {
          targetSel = '[id="' + targetId.trim() + '"]';
        }

        var elData = {
          element: element,
          options: options,
          target: target,
          targetSel: options.targetSel || targetSel,
          id: (target.attr('id') || '').trim()
        };
        element.off('click.smooth-scroll tap.smooth-scroll').on('click.smooth-scroll tap.smooth-scroll', function (event) {
          if ($(this).data('skip-smooth-scroll') || $(event.target).data('skip-smooth-scroll')) {
            return;
          }

          event.preventDefault();

          if (!$(this).data('allow-propagation')) {
            event.stopPropagation();
          }

          scrollItem(elData);

          if (elData.options.clickCallback) {
            elData.options.clickCallback.call(this, event);
          }
        });
      }
    });
  };

  $.fn.kubioScrollSpy = function (options) {
    if (isInsideKubioEditor()) {
      return;
    }

    var elements = $(this);
    var id = 'spy-' + parseInt(Date.now() * Math.random());
    elements.each(function () {
      var element = $(this);
      var settings = jQuery.extend({
        onChange: function onChange() {},
        onLeave: function onLeave() {},
        clickCallback: function clickCallback() {},
        smoothScrollAnchor: false,
        offset: 0
      }, options);

      if (element.is('a') && (element.attr('href') || '').indexOf('#') !== -1 && (element.attr('href') || '').replace('#', '').length) {
        var target = getTargetForEl(element);

        if (target && !target.attr('skip-scroll-spy')) {
          var elData = {
            element: element,
            options: settings,
            target: target,
            targetSel: '[id="' + target.attr('id').trim() + '"]',
            id: target.attr('id').trim()
          };

          __toCheckOnScroll.addItem(id, elData);

          element.data('scrollSpy', elData);

          if (options.smoothScrollAnchor) {
            element.smoothScrollAnchor(options);
          }
        }
      }
    });
  };

  function update() {
    __toCheckOnScroll.eachCategory(function (items) {
      var ordered = items.sort(function (itemA, itemB) {
        return itemA.target.offset().top - itemB.target.offset().top;
      });
      var lastItem = ordered.filter(function (item) {
        var scrollY = window.pageYOffset !== undefined ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
        return item.target.offset().top <= scrollY + window.innerHeight * 0.5;
      }).pop();
      ordered.forEach(function (item) {
        if (lastItem && item.element.is(lastItem.element)) {
          changeUrlHash(item.id, 5);
          item.options.onChange.call(item.element);
        } else {
          item.options.onLeave.call(item.element);
        }
      });
    });
  }

  function goToCurrentHash() {
    var hash = window.location.hash.replace('#', '');

    var currentItem = __toCheckOnScroll.all().filter(function (item) {
      return item.targetSel === '[id="' + decodeURIComponent(hash).trim() + '"]';
    });

    if (!(document.readyState === 'complete' || document.readyState === 'interactive')) {
      $(window).on('load', function () {
        if (currentItem.length) {
          scrollItem(currentItem[0]);
        }

        update();
      });
    }
  }

  if (!isInsideKubioEditor()) {
    window.addEventListener('scroll', update, {
      passive: true
    });
    $(window).on('smoothscroll.update', update);
    $(window).on('smoothscroll.update', goToCurrentHash);
    $(goToCurrentHash);
  }
})(jQuery);

/***/ }),

/***/ "./kubio-plugin/src/packages/scripts/src/masonry.js":
/*!**********************************************************!*\
  !*** ./kubio-plugin/src/packages/scripts/src/masonry.js ***!
  \**********************************************************/
/***/ (() => {

(function ($, Colibri) {
  var className = 'masonry';

  var Component = function Component(element, options) {
    this.namespace = className;
    this.defaults = {}; // Parent Constructor

    Colibri.apply(this, arguments);
    this.addResizeObserver();
    this.bindedRestart = $.debounce(this.restart.bind(this), 50);

    if (this.showMasonry()) {
      this.start();
    }
  };

  function attributeExistsAndFalse($node, attrName) {
    if ($node[0].hasAttribute(attrName) && $node.attr(attrName) !== 'true') {
      return true;
    }
  }

  Component.prototype = {
    start: function start() {
      this.stop();
      var masonry = this.$element;

      if (!this.$element.parent().length) {
        this.stop(); // stop for elements not attached to dom
      }

      if (this.settings.targetSelector) {
        masonry = this.$element.find(this.settings.targetSelector).first();
      }

      this.$masonry = masonry;

      if (!this.$masonry.masonry) {
        return;
      }

      this.$masonry.masonry({
        itemSelector: this.settings.itemSelector,
        columnWidth: this.settings.columnWidth,
        percentPosition: true
      });
      this.addEventListeners();

      (function () {
        var images = masonry.find('img');
        var loadedImages = 0;
        var completed = 0;

        function imageLoaded() {
          loadedImages++;

          if (images.length === loadedImages) {
            try {
              masonry.data().masonry.layout();
            } catch (e) {
              console.error(e);
            }
          }
        }

        images.each(function () {
          if (this.complete) {
            completed++;
            imageLoaded();
          } else {
            $(this).on('load', imageLoaded);
            $(this).on('error', imageLoaded);
          }
        });

        if (images.length !== completed) {
          if (document.readyState === 'complete') {
            setTimeout(function () {
              masonry.data().masonry.layout();
            }, 10);
          }
        }

        $(function () {
          masonry.data().masonry.layout();
        });
      })();
    },
    showMasonry: function showMasonry() {
      if (attributeExistsAndFalse(this.$element, 'data-show-masonry') || attributeExistsAndFalse(this.$element, 'show-masonry')) {
        return false;
      }

      return this.settings.enabled;
    },
    stop: function stop() {
      this.removeEventListeners();

      try {
        if (this.$masonry.data().masonry) {
          this.$masonry.masonry('destroy');
        }
      } catch (e) {}
    },
    restart: function restart() {
      this.stop();
      this.start();
    },
    addEventListeners: function addEventListeners() {
      this.addResizeListener();
      this.$element.on('colibriContainerOpened', this.bindedRestart);
    },
    removeEventListeners: function removeEventListeners() {
      this.removeResizeListener();
      this.$element.off('colibriContainerOpened', this.bindedRestart);
    },
    addResizeListener: function addResizeListener() {
      this.resizeCount = 0;

      try {
        this.resizeObserver.observe(this.$masonry.children().get(0));
      } catch (e) {}
    },
    removeResizeListener: function removeResizeListener() {
      var _this$resizeObserver;

      this === null || this === void 0 ? void 0 : (_this$resizeObserver = this.resizeObserver) === null || _this$resizeObserver === void 0 ? void 0 : _this$resizeObserver.disconnect();
    },
    addResizeObserver: function addResizeObserver() {
      var self = this;
      this.resizeObserver = new ResizeObserver(function (entries) {
        if (self.resizeCount === 0) {
          self.resizeCount++;
          return;
        }

        self.restart();
      });
    },
    loadImages: function loadImages() {}
  };
  Component.inherits(Colibri);
  Colibri[className] = Component;
  Colibri.Plugin.create(className);
  Colibri.Plugin.autoload(className);
})(jQuery, Colibri);

/***/ }),

/***/ "./node_modules/domready/ready.js":
/*!****************************************!*\
  !*** ./node_modules/domready/ready.js ***!
  \****************************************/
/***/ ((module) => {

/*!
  * domready (c) Dustin Diaz 2014 - License MIT
  */
!function (name, definition) {

  if (true) module.exports = definition()
  else {}

}('domready', function () {

  var fns = [], listener
    , doc = document
    , hack = doc.documentElement.doScroll
    , domContentLoaded = 'DOMContentLoaded'
    , loaded = (hack ? /^loaded|^c/ : /^loaded|^i|^c/).test(doc.readyState)


  if (!loaded)
  doc.addEventListener(domContentLoaded, listener = function () {
    doc.removeEventListener(domContentLoaded, listener)
    loaded = 1
    while (listener = fns.shift()) listener()
  })

  return function (fn) {
    loaded ? setTimeout(fn, 0) : fns.push(fn)
  }

});


/***/ }),

/***/ "./node_modules/lodash.debounce/index.js":
/*!***********************************************!*\
  !*** ./node_modules/lodash.debounce/index.js ***!
  \***********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

/**
 * lodash (Custom Build) <https://lodash.com/>
 * Build: `lodash modularize exports="npm" -o ./`
 * Copyright jQuery Foundation and other contributors <https://jquery.org/>
 * Released under MIT license <https://lodash.com/license>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 */

/** Used as the `TypeError` message for "Functions" methods. */
var FUNC_ERROR_TEXT = 'Expected a function';

/** Used as references for various `Number` constants. */
var NAN = 0 / 0;

/** `Object#toString` result references. */
var symbolTag = '[object Symbol]';

/** Used to match leading and trailing whitespace. */
var reTrim = /^\s+|\s+$/g;

/** Used to detect bad signed hexadecimal string values. */
var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;

/** Used to detect binary string values. */
var reIsBinary = /^0b[01]+$/i;

/** Used to detect octal string values. */
var reIsOctal = /^0o[0-7]+$/i;

/** Built-in method references without a dependency on `root`. */
var freeParseInt = parseInt;

/** Detect free variable `global` from Node.js. */
var freeGlobal = typeof __webpack_require__.g == 'object' && __webpack_require__.g && __webpack_require__.g.Object === Object && __webpack_require__.g;

/** Detect free variable `self`. */
var freeSelf = typeof self == 'object' && self && self.Object === Object && self;

/** Used as a reference to the global object. */
var root = freeGlobal || freeSelf || Function('return this')();

/** Used for built-in method references. */
var objectProto = Object.prototype;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var objectToString = objectProto.toString;

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeMax = Math.max,
    nativeMin = Math.min;

/**
 * Gets the timestamp of the number of milliseconds that have elapsed since
 * the Unix epoch (1 January 1970 00:00:00 UTC).
 *
 * @static
 * @memberOf _
 * @since 2.4.0
 * @category Date
 * @returns {number} Returns the timestamp.
 * @example
 *
 * _.defer(function(stamp) {
 *   console.log(_.now() - stamp);
 * }, _.now());
 * // => Logs the number of milliseconds it took for the deferred invocation.
 */
var now = function() {
  return root.Date.now();
};

/**
 * Creates a debounced function that delays invoking `func` until after `wait`
 * milliseconds have elapsed since the last time the debounced function was
 * invoked. The debounced function comes with a `cancel` method to cancel
 * delayed `func` invocations and a `flush` method to immediately invoke them.
 * Provide `options` to indicate whether `func` should be invoked on the
 * leading and/or trailing edge of the `wait` timeout. The `func` is invoked
 * with the last arguments provided to the debounced function. Subsequent
 * calls to the debounced function return the result of the last `func`
 * invocation.
 *
 * **Note:** If `leading` and `trailing` options are `true`, `func` is
 * invoked on the trailing edge of the timeout only if the debounced function
 * is invoked more than once during the `wait` timeout.
 *
 * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
 * until to the next tick, similar to `setTimeout` with a timeout of `0`.
 *
 * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
 * for details over the differences between `_.debounce` and `_.throttle`.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Function
 * @param {Function} func The function to debounce.
 * @param {number} [wait=0] The number of milliseconds to delay.
 * @param {Object} [options={}] The options object.
 * @param {boolean} [options.leading=false]
 *  Specify invoking on the leading edge of the timeout.
 * @param {number} [options.maxWait]
 *  The maximum time `func` is allowed to be delayed before it's invoked.
 * @param {boolean} [options.trailing=true]
 *  Specify invoking on the trailing edge of the timeout.
 * @returns {Function} Returns the new debounced function.
 * @example
 *
 * // Avoid costly calculations while the window size is in flux.
 * jQuery(window).on('resize', _.debounce(calculateLayout, 150));
 *
 * // Invoke `sendMail` when clicked, debouncing subsequent calls.
 * jQuery(element).on('click', _.debounce(sendMail, 300, {
 *   'leading': true,
 *   'trailing': false
 * }));
 *
 * // Ensure `batchLog` is invoked once after 1 second of debounced calls.
 * var debounced = _.debounce(batchLog, 250, { 'maxWait': 1000 });
 * var source = new EventSource('/stream');
 * jQuery(source).on('message', debounced);
 *
 * // Cancel the trailing debounced invocation.
 * jQuery(window).on('popstate', debounced.cancel);
 */
function debounce(func, wait, options) {
  var lastArgs,
      lastThis,
      maxWait,
      result,
      timerId,
      lastCallTime,
      lastInvokeTime = 0,
      leading = false,
      maxing = false,
      trailing = true;

  if (typeof func != 'function') {
    throw new TypeError(FUNC_ERROR_TEXT);
  }
  wait = toNumber(wait) || 0;
  if (isObject(options)) {
    leading = !!options.leading;
    maxing = 'maxWait' in options;
    maxWait = maxing ? nativeMax(toNumber(options.maxWait) || 0, wait) : maxWait;
    trailing = 'trailing' in options ? !!options.trailing : trailing;
  }

  function invokeFunc(time) {
    var args = lastArgs,
        thisArg = lastThis;

    lastArgs = lastThis = undefined;
    lastInvokeTime = time;
    result = func.apply(thisArg, args);
    return result;
  }

  function leadingEdge(time) {
    // Reset any `maxWait` timer.
    lastInvokeTime = time;
    // Start the timer for the trailing edge.
    timerId = setTimeout(timerExpired, wait);
    // Invoke the leading edge.
    return leading ? invokeFunc(time) : result;
  }

  function remainingWait(time) {
    var timeSinceLastCall = time - lastCallTime,
        timeSinceLastInvoke = time - lastInvokeTime,
        result = wait - timeSinceLastCall;

    return maxing ? nativeMin(result, maxWait - timeSinceLastInvoke) : result;
  }

  function shouldInvoke(time) {
    var timeSinceLastCall = time - lastCallTime,
        timeSinceLastInvoke = time - lastInvokeTime;

    // Either this is the first call, activity has stopped and we're at the
    // trailing edge, the system time has gone backwards and we're treating
    // it as the trailing edge, or we've hit the `maxWait` limit.
    return (lastCallTime === undefined || (timeSinceLastCall >= wait) ||
      (timeSinceLastCall < 0) || (maxing && timeSinceLastInvoke >= maxWait));
  }

  function timerExpired() {
    var time = now();
    if (shouldInvoke(time)) {
      return trailingEdge(time);
    }
    // Restart the timer.
    timerId = setTimeout(timerExpired, remainingWait(time));
  }

  function trailingEdge(time) {
    timerId = undefined;

    // Only invoke if we have `lastArgs` which means `func` has been
    // debounced at least once.
    if (trailing && lastArgs) {
      return invokeFunc(time);
    }
    lastArgs = lastThis = undefined;
    return result;
  }

  function cancel() {
    if (timerId !== undefined) {
      clearTimeout(timerId);
    }
    lastInvokeTime = 0;
    lastArgs = lastCallTime = lastThis = timerId = undefined;
  }

  function flush() {
    return timerId === undefined ? result : trailingEdge(now());
  }

  function debounced() {
    var time = now(),
        isInvoking = shouldInvoke(time);

    lastArgs = arguments;
    lastThis = this;
    lastCallTime = time;

    if (isInvoking) {
      if (timerId === undefined) {
        return leadingEdge(lastCallTime);
      }
      if (maxing) {
        // Handle invocations in a tight loop.
        timerId = setTimeout(timerExpired, wait);
        return invokeFunc(lastCallTime);
      }
    }
    if (timerId === undefined) {
      timerId = setTimeout(timerExpired, wait);
    }
    return result;
  }
  debounced.cancel = cancel;
  debounced.flush = flush;
  return debounced;
}

/**
 * Checks if `value` is the
 * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
 * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(_.noop);
 * // => true
 *
 * _.isObject(null);
 * // => false
 */
function isObject(value) {
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

/**
 * Checks if `value` is object-like. A value is object-like if it's not `null`
 * and has a `typeof` result of "object".
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 * @example
 *
 * _.isObjectLike({});
 * // => true
 *
 * _.isObjectLike([1, 2, 3]);
 * // => true
 *
 * _.isObjectLike(_.noop);
 * // => false
 *
 * _.isObjectLike(null);
 * // => false
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/**
 * Checks if `value` is classified as a `Symbol` primitive or object.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a symbol, else `false`.
 * @example
 *
 * _.isSymbol(Symbol.iterator);
 * // => true
 *
 * _.isSymbol('abc');
 * // => false
 */
function isSymbol(value) {
  return typeof value == 'symbol' ||
    (isObjectLike(value) && objectToString.call(value) == symbolTag);
}

/**
 * Converts `value` to a number.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to process.
 * @returns {number} Returns the number.
 * @example
 *
 * _.toNumber(3.2);
 * // => 3.2
 *
 * _.toNumber(Number.MIN_VALUE);
 * // => 5e-324
 *
 * _.toNumber(Infinity);
 * // => Infinity
 *
 * _.toNumber('3.2');
 * // => 3.2
 */
function toNumber(value) {
  if (typeof value == 'number') {
    return value;
  }
  if (isSymbol(value)) {
    return NAN;
  }
  if (isObject(value)) {
    var other = typeof value.valueOf == 'function' ? value.valueOf() : value;
    value = isObject(other) ? (other + '') : other;
  }
  if (typeof value != 'string') {
    return value === 0 ? value : +value;
  }
  value = value.replace(reTrim, '');
  var isBinary = reIsBinary.test(value);
  return (isBinary || reIsOctal.test(value))
    ? freeParseInt(value.slice(2), isBinary ? 2 : 8)
    : (reIsBadHex.test(value) ? NAN : +value);
}

module.exports = debounce;


/***/ }),

/***/ "./assets/src/customizer/css/preview.scss":
/*!************************************************!*\
  !*** ./assets/src/customizer/css/preview.scss ***!
  \************************************************/
/***/ (() => {

"use strict";
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/theme/css/theme.scss":
/*!*****************************************!*\
  !*** ./assets/src/theme/css/theme.scss ***!
  \*****************************************/
/***/ (() => {

"use strict";
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/theme/css/fse-base-style.scss":
/*!**************************************************!*\
  !*** ./assets/src/theme/css/fse-base-style.scss ***!
  \**************************************************/
/***/ (() => {

"use strict";
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/customizer/css/customizer.scss":
/*!***************************************************!*\
  !*** ./assets/src/customizer/css/customizer.scss ***!
  \***************************************************/
/***/ (() => {

"use strict";
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/admin/css/admin.scss":
/*!*****************************************!*\
  !*** ./assets/src/admin/css/admin.scss ***!
  \*****************************************/
/***/ (() => {

"use strict";
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/theme/theme": 0,
/******/ 			"customizer/customizer": 0,
/******/ 			"admin/admin": 0,
/******/ 			"theme/fse-base-style": 0,
/******/ 			"theme/theme": 0,
/******/ 			"customizer/preview": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkIds[i]] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkgutentag_theme"] = self["webpackChunkgutentag_theme"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["customizer/customizer","admin/admin","theme/fse-base-style","theme/theme","customizer/preview"], () => (__webpack_require__("./assets/src/theme/js/theme.js")))
/******/ 	__webpack_require__.O(undefined, ["customizer/customizer","admin/admin","theme/fse-base-style","theme/theme","customizer/preview"], () => (__webpack_require__("./assets/src/theme/css/theme.scss")))
/******/ 	__webpack_require__.O(undefined, ["customizer/customizer","admin/admin","theme/fse-base-style","theme/theme","customizer/preview"], () => (__webpack_require__("./assets/src/theme/css/fse-base-style.scss")))
/******/ 	__webpack_require__.O(undefined, ["customizer/customizer","admin/admin","theme/fse-base-style","theme/theme","customizer/preview"], () => (__webpack_require__("./assets/src/customizer/css/customizer.scss")))
/******/ 	__webpack_require__.O(undefined, ["customizer/customizer","admin/admin","theme/fse-base-style","theme/theme","customizer/preview"], () => (__webpack_require__("./assets/src/admin/css/admin.scss")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["customizer/customizer","admin/admin","theme/fse-base-style","theme/theme","customizer/preview"], () => (__webpack_require__("./assets/src/customizer/css/preview.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
