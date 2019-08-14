/**
 * Functions for the Fixed TOC Plugin
 *
 * @since 3.0.0
 */

var fixedtoc = (function ($) {
	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Set fixedtocOption.
	 *
	 * @since 3.0.0 
	 */
	var option = (function () {
		var init = function () {
			fixedtocOption.scrollDuration = 500;
			fixedtocOption.fadeTriggerDuration = 5000;
			fixedtocOption.scrollOffset = parseToInt(fixedtocOption.scrollOffset);
			fixedtocOption.fixedOffsetX = parseToInt(fixedtocOption.fixedOffsetX);
			fixedtocOption.fixedOffsetY = parseToInt(fixedtocOption.fixedOffsetY);
			fixedtocOption.contentsFixedHeight = parseToInt(fixedtocOption.contentsFixedHeight);
			fixedtocOption.contentsWidthInPost = parseToInt(fixedtocOption.contentsWidthInPost);
			fixedtocOption.contentsHeightInPost = parseToInt(fixedtocOption.contentsHeightInPost);
			fixedtocOption.triggerBorderWidth = getBorderWidth(fixedtocOption.triggerBorder);
			fixedtocOption.contentsBorderWidth = getBorderWidth(fixedtocOption.contentsBorder);
			fixedtocOption.triggerSize = parseToInt(fixedtocOption.triggerSize);
		};

		var set = function (name, val, type) {
			if ('int' == type) {
				fixedtocOption[name] = parseToInt(val);
			} else if ('float' == type) {
				fixedtocOption[name] = parseToFloat(val);
			} else {
				fixedtocOption[name] = val;
			}
		};

		var update = function (name, val, type) {
			set(name, val, type);
		};

		var remove = function (name) {
			if (undefined !== fixedtocOption[name]) {
				delete fixedtocOption[name];
			}
		};

		var getBorderWidth = function (border) {
			switch (border) {
				case 'thin':
					return 1;
				case 'medium':
					return 2;
				case 'bold':
					return 5;
				default:
					return 0;
			}
		};

		return {
			init: init,
			set: set,
			update: update,
			remove: remove
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Condition methods to control on/off.
	 *
	 * @since 3.0.0
	 */
	var conditionObj = {
		inWidgetProp: undefined,

		showAdminbar: function () {
			return fixedtocOption.showAdminbar;
		},

		isQuickMin: function () {
			return fixedtocOption.isQuickMin;
		},

		isEscMin: function () {
			return fixedtocOption.isEscMin;
		},

		isEnterMax: function () {
			return fixedtocOption.isEnterMax;
		},

		isNestedList: function () {
			return fixedtocOption.isNestedList;
		},

		isColExpList: function () {
			return fixedtocOption.isColExpList;
		},

		showColExpIcon: function () {
			return fixedtocOption.showColExpIcon;
		},

		isAccordionList: function () {
			return fixedtocOption.isAccordionList;
		},

		showTargetHint: function () {
			return true;
		},

		supportInPost: function () {
			return fixedtocOption.inPost;
		},

		inWidget: function () {
			if (!fixedtocOption.inWidget) {
				return false;
			}

			if (undefined === this.inWidgetProp) {
				this.inWidgetProp = $('#ftwp-widget-container').length ? true : false;
			}

			return this.inWidgetProp;
		},

		fixedWidget: function () {
			if (!this.inWidget()) {
				return false;
			}

			return fixedtocOption.fixedWidget;
		},

		isAutoHeightFixedToPost: function () {
			return (0 == fixedtocOption.contentsFixedHeight);
		},

		isFloat: function () {
			if ('none' != fixedtocOption.contentsFloatInPost) {
				return true;
			} else {
				return false;
			}
		},

		isAutoHeightInPost: function () {
			return (0 == fixedtocOption.contentsHeightInPost);
		},

		isPositionAtFixed: function (position) {
			if (-1 == fixedtocOption.fixedPosition.indexOf(position)) {
				return false;
			} else {
				return true;
			}
		},
		
		isDebug: function() {
			if ( 1 == fixedtocOption.debug ) {
				return true;
			} else {
				return false;
			}
		},
		
		isNotBlur: function() {
			var ua = navigator.userAgent.toLowerCase();
			return ( ua.indexOf( "android" ) > -1 ) || ( ua.indexOf( 'firefox' ) > -1 );
		},
		
		isMobile: function() {
			if ( dataObj.data.window.width <= 768 )
				return true;
			else
				return false;
		}
		
	}; // End conditionObj.

	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Common functions. 
	 *
	 * @since 3.0.0
	 */

	/**
	 * Parse to integer.
	 *
	 * @since 3.0.0
	 *
	 * @param {string} str
	 * @return int
	 */
	function parseToInt(str) {
		return parseInt(str) || 0;
	}

	/**
	 * Parse to float number.
	 *
	 * @since 3.0.0
	 *
	 * @param {string} str
	 * @return float
	 */
	function parseToFloat(str) {
		return parseFloat(str) || 0;
	}

	/**
	 * Get height of a fixed element.
	 *
	 * @since 3.0.0
	 *
	 * @param {Element} eles
	 * @return float
	 */
	function getFixedHeight(eles) {
		if ( ! eles.length ) {
			return 0;
		}
		
		var fixedHeight = 0;
		eles.each(function(i) {
			var thisEle = $(this);
			if ( 'fixed' == thisEle.css('position') ) {
				fixedHeight += parseToInt( thisEle.outerHeight() );
			}
		});

		return fixedHeight;
	}

	/**
	 * Prevent default event.
	 *
	 * @since 3.0.0
	 *
	 * @param {event} evt
	 * @return undefinde
	 */
	function preventDefaultEvt(evt) {
		evt.preventDefault();
	}


	/**
	 * Return undefined.
	 *
	 * @since 3.0.0
	 *
	 * @return undefined.
	 */
	function __return() {
		return;
	}


	/**
	 * Get contents height.
	 * Exclude padding and border.
	 *
	 * @since 3.0.1
	 *
	 * @param {int} contents outer height.
	 * @return int.
	 */
	function getContentsHeight(contentsOuterHeight) {
		return parseToInt(contentsOuterHeight - 2 * fixedtocOption.contentsBorderWidth);
	}
	

	/**
	 * Output console.log result if debug enable.
	 *
	 * @since 3.1.0
	 *
	 * @param {string}
	 */
	function consoleLog( str ) {
		if ( conditionObj.isDebug() ) {
			console.log( str );
		}
	}


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Create elements.
	 *
	 * @since 3.0.0
	 */
	var $e;

	function createElements() {
		$e = {
			window: $(window),
			document: $(document),
			body: $('body'),
			container: $('#ftwp-container'),
			trigger: $('#ftwp-trigger'),
			contents: $('#ftwp-contents'),
			header: $('#ftwp-header'),
			minIcon: $('#ftwp-header-minimize'),
			list: $('#ftwp-list'),
			postContent: $('#ftwp-postcontent'),
			headings: $('.ftwp-heading')
		};
		$e.anchors = $e.list.find('.ftwp-anchor').not('.ftwp-otherpage-anchor');

		if (conditionObj.isNestedList()) {
			$e.hasSubItems = $e.list.find('.ftwp-has-sub');
		}

		if (conditionObj.showColExpIcon()) {
			$e.colExpIcons = $e.list.find('.ftwp-icon-expand, .ftwp-icon-collapse');
		}

		if (conditionObj.inWidget()) {
			$e.widget = $('.ftwp-widget');
			$e.widgetContainer = $('#ftwp-widget-container');
		}

		if (conditionObj.supportInPost()) {
			$e.containerOuter = $('#ftwp-container-outer');
		}

		 consoleLog( $e );
	}


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Set index data to anchor element.
	 *
	 * @since 3.0.0
	 */
	function setAnchorIndex() {
		$e.anchors.each(function (i) {
			$(this).data('index', i);
		});
	}


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Data
	 *
	 * @since 3.0.0
	 */
	var dataObj = (function () {
		var data = {};

		// Set scroll top.
		var setScrollTop = function () {
			data.scrollTop = $e.window.scrollTop();
		};

		// Set window width and height.
		var setWindowSize = function () {
			data.window = {};

			data.window.width = window.innerWidth;
			data.window.height = window.innerHeight;
		};
        
        // Set document height.
        var setDocumentHeight = function() {
            data.document = {};
            data.document.height = Math.round( $e.document.height() );
        };

		// Set adminbar height.
		var setAdminbarHeight = function () {
			data.adminbarHeight = conditionObj.showAdminbar() ? getFixedHeight( $('#wpadminbar') ) : 0;
		};

		// Set fixed menu height.
		var setFixedMenuHeight = function () {
			data.fixedMenuHeight = getFixedHeight( $(fixedtocOption.fixedMenu) );
		};

		// Set fixed height. Include adminbar and fixed menu.
		var setFixedHeight = function () {
			if ( fixedtocOption.fixedMenu ) {
				data.fixedHeight = data.adminbarHeight + data.fixedMenuHeight;
			} else {
				data.fixedHeight = data.adminbarHeight;
			}
		};

		// Set offset top to window at fixed position.
		var setFixedOffsetTop = function () {
			data.fixedOffsetTop = data.fixedHeight + fixedtocOption.fixedOffsetY;
		};

		// Offset top of heading
		var setHeadingOffset = function () {
			data.headingOffset = data.fixedHeight + fixedtocOption.scrollOffset;
		};

		// Set heading top data
		var setHeadingsTop = function () {
			data.headingsTop = [];
			$.each($e.anchors, function () {
				var $heading = $($(this).attr('href'));
				var headingTop = $heading.length ? parseToInt($heading.offset().top - data.headingOffset) : NaN;
				if ( ! isNaN( headingTop ) ) {
					data.headingsTop.push({
						headingTop: headingTop,
						anchorEle: $(this)
					});
				}
			});
		};

		// Set post rectangle data relative to the page.
		var setPostRect = function () {
			data.postRect = {};

			var postContentOffset = $e.postContent.offset();
			var postContentWidth = $e.postContent.outerWidth();
			var postContentHeight = $e.postContent.outerHeight();
			
			data.postRect.left = postContentOffset.left;
			data.postRect.top = postContentOffset.top;
			data.postRect.width = postContentWidth;
			data.postRect.right = data.postRect.left + data.postRect.width;
			data.postRect.bottom = postContentHeight + data.postRect.top;
			data.postRect.height = data.postRect.bottom - data.postRect.top;
		};

		// Set fixed TOC Y range.
		var setFtocRangeY = function () {
			data.ftocRangeY = {};

			if (conditionObj.supportInPost()) {
				data.ftocRangeY.top = data.inPostRangeY.bottom;
			} else {
				data.ftocRangeY.top = data.postRect.top - data.fixedHeight;
			}

			 data.ftocRangeY.bottom = data.postRect.bottom - data.window.height;
//			data.ftocRangeY.bottom = data.postRect.bottom;
		};

		// Set height of the container outer.
		var containerOuterHeight = (function () {
			var set = function () {
				var height;
				
				if ( conditionObj.isAutoHeightInPost() || fixedtocOption.contentsColexpInit ) {
					$e.container.css('position', 'static');
					height = $e.containerOuter.outerHeight();
					$e.container.css('position', '');
				} else {
					height = fixedtocOption.contentsHeightInPost;
				}

				$e.containerOuter.css('height', height + 'px');

				data.containerOuterHeight = height;
			};

			var update = function () {
				if (!actionObj.location.inPost) {
					return;
				}

				if (conditionObj.isAutoHeightInPost()) {
					auto();
				} else {
					if ('collapse' == $e.contents.data('colexp')) {
						auto();
					} else {
						$e.containerOuter.css('height', fixedtocOption.contentsHeightInPost + 'px');
						$e.contents.css('height', fixedtocOption.contentsHeightInPost + 'px');
						listHeight.set(getContentsHeight(fixedtocOption.contentsHeightInPost));
						data.containerOuterHeight = $e.containerOuter.outerHeight();
//						$e.containerOuter.css('height', '');
//						$e.contents.css('height', '');
//						listHeight.unset();
					}
				}

				function auto() {
					$e.containerOuter.css('height', 'auto');
					$e.contents.css('height', 'auto');
					listHeight.setAuto();
					data.containerOuterHeight = $e.containerOuter.outerHeight();
//					$e.containerOuter.css('height', '');
//					$e.contents.css('height', '');
//					listHeight.unset();
				}
			};

			return {
				set: set,
				update: update
			};
		})();

		// Set Y range fot in post location.
		var setInPostRangeY = function () {
			data.inPostRangeY = {};

			data.inPostRangeY.top = 0;
			data.inPostRangeY.bottom = $e.containerOuter.offset().top + data.containerOuterHeight - data.fixedHeight;
		};

		// Set min viewport width in widget
		var setInWidgetMinWidth = function () {
			data.inWidgetMinWidth = data.postRect.width + $e.widgetContainer.outerWidth();
		};

		// Set fixed widget Y range.
		var setFixedWidgetRangeY = function () {
			data.fixedWidgetRangeY = {};

			data.fixedWidgetRangeY.top = $e.widgetContainer.offset().top - data.fixedHeight;
			data.fixedWidgetRangeY.bottom = data.ftocRangeY.bottom;
		};

		// Set fixed TOC rectangle data in widget.
		var ftocRectInWidget = (function () {
			var set = function () {
				data.ftocRectInWidget = {
					left: $e.widgetContainer.offset().left,
					top: data.fixedHeight,
					width: $e.widgetContainer.outerWidth(),
					height: getHeight(),
				};
			};

			var getHeight = function () {
				var height;

				if ('collapse' == $e.contents.data('colexp')) {
					$e.contents.css('height', 'auto');
					height = $e.contents.outerHeight();
					$e.contents.css('height', '');
				} else {
					height = window.innerHeight - data.fixedHeight;
				}

				return height;
			};

			var updateOnResize = function () {
				set();
			};

			var updateHeight = function () {
				data.ftocRectInWidget.height = getHeight();
			};

			return {
				set: set,
				updateOnResize: updateOnResize,
				updateHeight: updateHeight
			};
		})();
        
		// Update data on window resize.
		var updateOnResize = function () {
				setWindowSize();
				setScrollTop();
				setAdminbarHeight();
				if (fixedtocOption.fixedMenu) {
						setFixedMenuHeight();
				}
				setFixedHeight();
				setFixedOffsetTop();
				if (conditionObj.supportInPost()) {
						containerOuterHeight.update();
						setInPostRangeY();
				}
				setHeadingOffset();
				setPostRect();
				setFtocRangeY();
				if (conditionObj.inWidget()) {
						setInWidgetMinWidth();
				}
				if (conditionObj.fixedWidget()) {
						setFixedWidgetRangeY();
						ftocRectInWidget.updateOnResize();
				}
				setHeadingsTop();

				consoleLog( data );
		};

		// Update data on window scroll.
		var prevFixedMenuHeight;
		var updateOnScroll = function () {
			setScrollTop();

			if (!fixedtocOption.fixedMenu) {
				return;
			}

			if (undefined === prevFixedMenuHeight) {
				prevFixedMenuHeight = data.fixedMenuHeight;
			}

			setFixedMenuHeight();

			if (prevFixedMenuHeight !== data.fixedMenuHeight) {
				setFixedHeight();
				setFixedOffsetTop();
				if (conditionObj.supportInPost()) {
					setInPostRangeY();
				}
				setHeadingOffset();
				setPostRect();
				setFtocRangeY();
				if (conditionObj.inWidget()) {
					setInWidgetMinWidth();
				}
				if (conditionObj.fixedWidget()) {
					setFixedWidgetRangeY();
					ftocRectInWidget.updateOnResize();
				}
				setHeadingsTop();

				prevFixedMenuHeight = data.fixedMenuHeight;

				consoleLog( data );
			}
		};
        
		// Update data on document height changing.
		var preDocumentHeight;
		var updateOnDocumentHeightChange = function() {
				setDocumentHeight();

				var curDocumentHeight = data.document.height;
				if ( curDocumentHeight != preDocumentHeight ) {
						fixedtoc.reload();

						preDocumentHeight = data.document.height;

//						consoleLog( 'Document Height: ' + data.document.height );
				}           
		};

		// Return and set public API.
		return {
			data: data,

			ftocRectInWidget: ftocRectInWidget,

			// Create data on initial
			createOnInit: function () {
				setWindowSize();
				setScrollTop();
				setAdminbarHeight();
				if (fixedtocOption.fixedMenu) {
					setFixedMenuHeight();
				}
				setFixedHeight();
				setFixedOffsetTop();
				if (conditionObj.supportInPost()) {
					containerOuterHeight.set();
					setInPostRangeY();
				}
				setHeadingOffset();
				setPostRect();
				setFtocRangeY();
				if (conditionObj.inWidget()) {
					setInWidgetMinWidth();
				}
				if (conditionObj.fixedWidget()) {
					setFixedWidgetRangeY();
					ftocRectInWidget.set();
				}
				setHeadingsTop();

				consoleLog( this.data );
			},

			// Update data on resize window
			updateOnResize: updateOnResize,

			// Update data on scroll
			updateOnScroll: updateOnScroll,

			// Update data in post
			updateInPost: function () {
				containerOuterHeight.update();
				setInPostRangeY();
				setHeadingOffset();
				setHeadingsTop();
				setPostRect();
				setFtocRangeY();
			},
            
			// Update on document height change.
			updateOnDocumentHeightChange: updateOnDocumentHeightChange
		};

	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Location
	 *
	 * @since 3.0.0
	 */
	var locationObj = (function () {
		// Display in widget.
		var inWidget = function () {
//			if (conditionObj.inWidget() && dataObj.data.window.width >= dataObj.data.inWidgetMinWidth) {
			if ( conditionObj.inWidget() && ! conditionObj.isMobile() ) {
				return true;
			} else {
				return false;
			}
		};

		// Fixed to widget.
		var fixedWidget = function () {
			if (!conditionObj.fixedWidget()) {
				return false;
			}

			if (!inWidget()) {
				return false;
			}

			if (dataObj.data.fixedWidgetRangeY.top <= dataObj.data.scrollTop && dataObj.data.fixedWidgetRangeY.bottom > dataObj.data.scrollTop) {
				return true;
			} else {
				return false;
			}
		};

		// Display in post.
		var inPost = function () {
			if (!conditionObj.supportInPost()) {
				return false;
			}

			if (dataObj.data.inPostRangeY.bottom > dataObj.data.scrollTop) {
				return true;
			} else {
				return false;
			}
		};

		// Fixed to post
		var fixedToPost = function () {
			if (dataObj.data.ftocRangeY.top <= dataObj.data.scrollTop && dataObj.data.ftocRangeY.bottom > dataObj.data.scrollTop) {
				return true;
			} else {
				return false;
			}
		};

		return {
			fixedWidget: fixedWidget,
			inWidget: inWidget,
			inPost: inPost,
			fixedToPost: fixedToPost
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Actions.
	 *
	 * @since 3.0.0
	 */
	var actionObj = (function () {
		var location = {
			fixedWidget: false,
			inWidget: false,
			inPost: false,
			fixedToPost: false,
			hidden: false
		};

		var events = ['common', 'hidden', 'fixedToPost', 'inPost', 'inWidget', 'fixedWidget'];

		// Register an action with event and handler.
		var register = function (event, handler) {
			if (-1 == $.inArray(event, events)) {
				consoleLog( 'Not support this event: ' + event );
				return;
			}

			if (undefined !== handler._construct) {
				$e.container.on('ftoc_' + event, handler._construct);
			}

			if ('common' != event && undefined !== handler._destruct) {
				$e.container.on('_ftoc_' + event, handler._destruct);
			}
		};

		// Active actions by location
		var activeByLocation = function (eventType) {
			if (locationObj.fixedWidget()) {
				if (!location.fixedWidget) {
					setLocation('fixedWidget');
					activeThe('fixedWidget');

					consoleLog( location );
				}
			} else if (locationObj.inWidget()) {
				if (!location.inWidget) {
					setLocation('inWidget');
					activeThe('inWidget');

					consoleLog( location );
				}
			} else if (locationObj.inPost()) {
				if (!location.inPost) {
					setLocation('inPost');
					activeThe('inPost');

					consoleLog( location );
				}
			} else if (locationObj.fixedToPost()) {
				if (!location.fixedToPost) {
					setLocation('fixedToPost');
					activeThe('fixedToPost');

					consoleLog( location );
				}
			} else {
				if (!location.hidden) {
					setLocation('hidden');
					activeThe('hidden');

					consoleLog( location );
				}
			}

			// Active the event
			function activeThe(event) {
				var len = events.length;
				var eventParam = {
					location: event,
					eventType: eventType
				};

				// Deactive other events.
				for (var j = 1; j < len; j++) {
					if (event == events[j]) {
						continue;
					}

					$e.container.trigger('_ftoc_' + events[j], eventParam);
				}

				// Active the event.
				$e.container.trigger('ftoc_' + event, eventParam);
			}

			// Set location variable.
			function setLocation(event) {
				for (var i = 1, len = events.length; i < len; i++) {
					if (undefined !== event && event == events[i]) {
						location[event] = true;
					} else {
						location[events[i]] = false;
					}
				}
			}

		};

		// Public API
		return {
			// Location
			location: location,

			// Register custom event.
			register: register,

			// Update events on resize.
			updateOnResize: function () {
				activeByLocation('resize');
			},

			// Update events on scroll
			updateOnScroll: function () {
				activeByLocation('scroll');
			},

			// Initial events.
			init: function () {
				$e.container.trigger('ftoc_common');
				activeByLocation('init');
			}
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Set list height.
	 *
	 * @since 3.0.0
	 */
	var listHeight = (function () {
		var set = function (h) {
			var contentsHeight;
			if (undefined !== h) {
				contentsHeight = h;
			} else {
				contentsHeight = $e.contents.height();
			}

			$e.list.css('height', (contentsHeight - $e.header.outerHeight()) + 'px');
		};

		var setAuto = function () {
			$e.list.css('height', 'auto');
		};

		var unset = function () {
			$e.list.css('height', '');
		};

		// Public API
		return {
			set: set,
			setAuto: setAuto,
			unset: unset
		};
	})();


	if (conditionObj.isColExpList()) {
		/** ---------------------------------------------------------------------------------------------------------------------------------------
		 * Collapse/expand sub list.
		 *
		 * @since 3.0.0
		 */
		var colExpSubList = (function () {
			// Constructor
			var _construct = function () {
				if (conditionObj.showColExpIcon() && conditionObj.isAccordionList()) {
					$e.colExpIcons.on('click', accordionHandler);
					$e.container.on('ftocAfterScrollToTarget', accordionHandler);
				} else if (conditionObj.showColExpIcon()) {
					$e.colExpIcons.on('click', toggleHandler);
					$e.container.on('ftocAfterScrollToTarget', toggleHandler);
				} else {
					$e.container.on('ftocAfterScrollToTarget', accordionHandler);
				}

				if (conditionObj.showColExpIcon()) {
					$e.colExpIcons.on('mousedown', preventDefaultEvt);
				}

				$e.container.on('ftocAfterTargetIndicated', function (evt, $anchor) {
					expandSubListOnIndicator($anchor);
				});

				consoleLog( 'Actived colExpSubList().' );
			};

			// Toggle handler.
			var toggleHandler = function (evt, $anchor) {
				$currentTarget = undefined === $anchor ? $(this) : $anchor;
				var $hasSubItem = $currentTarget.parent('.ftwp-has-sub');
				if (!$hasSubItem.length) {
					return;
				}

				if ($currentTarget.hasClass('ftwp-anchor')) {
					expand($hasSubItem, $currentTarget.prev('button'));
				} else {
					toggle($hasSubItem, $currentTarget);
				}
			};

			// Accordion handler.
			var accordionHandler = function (evt, $anchor) {
				$currentTarget = undefined === $anchor ? $(this) : $anchor;
				var $item = $currentTarget.parent('.ftwp-item');
				if (!$item.length) {
					return;
				}

				if ($currentTarget.hasClass('ftwp-anchor')) {
					accordion($item, $currentTarget.prev('button'));
				} else {
					toggle($item, $currentTarget);
					collapseOther($item);
				}
			};

			// Toggle
			var toggle = function ($hasSubItem, $icon) {
				if ($hasSubItem.hasClass('ftwp-collapse')) {
					expand($hasSubItem, $icon);
				} else if ($hasSubItem.hasClass('ftwp-expand')) {
					collapse($hasSubItem, $icon);
				}
			};

			// Accordion
			var accordion = function ($item, $icon) {
				collapseOther($item);
				if ($item.hasClass('ftwp-has-sub')) {
					expand($item, $icon);
				}
			};

			// Collapse
			var collapse = function ($hasSubItem, $icon) {
				$hasSubItem.removeClass('ftwp-expand').addClass('ftwp-collapse');
				if ($icon.length) {
					$icon.removeClass('ftwp-icon-expand').addClass('ftwp-icon-collapse');
				}
			};

			// Expand
			var expand = function ($hasSubItem, $icon) {
				$hasSubItem.removeClass('ftwp-collapse').addClass('ftwp-expand');
				if ($icon.length) {
					$icon.removeClass('ftwp-icon-collapse').addClass('ftwp-icon-expand');
				}
			};

			// Collapse other
			var collapseOther = function ($item) {
				$e.hasSubItems.each(function () {
					$thisItem = $(this);
					if ($thisItem.get(0) == $item.get(0)) {
						return;
					}

					if ($thisItem.find($item).length) {
						return;
					}

					collapse($thisItem, $thisItem.children('button'));
				});
			};

			// Get icon element.
			var getIconEle = function ($this) {
				var $icon;
				if ($this.hasClass('ftwp-anchor')) {
					$icon = $this.prev('button');
				} else if ('button' == $this.prop('tagName').toLowerCase()) {
					$icon = $this;
				}

				return $icon;
			};

			// Expand sub list on targetIndicator
			var expandSubListOnIndicator = function ($anchor) {
				// Collapse other
				if (conditionObj.isAccordionList()) {
					collapseOther($anchor.parent('.ftwp-item'));
				}

				// Expand
				var $hasSubItems = $anchor.parents('.ftwp-has-sub');
				if ($hasSubItems) {
					$hasSubItems.each(function () {
						var $hasSubItem = $(this);
						var $icon = $hasSubItem.children('button');
						expand($hasSubItem, $icon);
					});
				}
			};

			// Public API
			return {
				_construct: _construct
			};
		})();
	}


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Scroll to target.
	 *
	 * @since 3.0.0
	 */
	var scrollToTarget = (function () {
		// Constructor
		var _construct = function () {
			$e.anchors.on('click', function (evt) {
				evt.preventDefault();
				var $anchor = $(evt.currentTarget);
				animateScroll($anchor);
			});
			
			$e.anchors.on( 'mousedown', preventDefaultEvt );

			consoleLog( 'Actived scrollToTarget().' );
		};

		var animateScrolling = false;

		// Animated scroll
		var animateScroll = function ($anchor) {
			var hash = $anchor.attr('href');
			$targetObj = $( hash );
			var $target = $targetObj.target;
			var index = $anchor.data('index');
			var headingsTop = dataObj.data.headingsTop[index];
			if ( undefined === headingsTop ) {
				return;
			}
			var top = headingsTop.headingTop;
			
			// Adding hash without scrolling by browser default behaviour.
			var headingID = hash.substr(1);
			$e.headings.removeClass( 'ftwp-heading-target' );
			$targetObj.attr( 'id', '' ).addClass( 'ftwp-heading-target' );
			window.location.hash = hash;
			$targetObj.attr('id', headingID);

			$('html, body').animate({
				scrollTop: top
			}, {
				duration: fixedtocOption.scrollDuration,
				start: function () {
					animateScrolling = true;
				}
			}).promise().then(function () {
				animateScrolling = false;
				
				// Fixing scroll to incorrectly position as the document height is changed.
				var curHeadingTop = dataObj.data.headingsTop[ index ].headingTop;
				if ( top != curHeadingTop ) {
					$('html, body').animate( {
						scrollTop: curHeadingTop
					}, 100, function() {
						var curHeadingTop2 = dataObj.data.headingsTop[ index ].headingTop;
						if ( curHeadingTop != curHeadingTop2 ) {
							$('html, body').animate({
								scrollTop: curHeadingTop2
							}, 1, function() {
							});
						}
					} );
				}
				
//				consoleLog( 'Fixed scrolling to target!!!' );
				
				activeCurrent($anchor);

				// After scroll to heading target.
				$e.container.trigger('ftocAfterScrollToTarget', [$anchor, top]);
			});
		};

		// Defined actived element.
		var activedEle;

		// Active the current elements.
		var activeCurrent = function ($anchor) {
			// Deactive the prevous elements.
			deactivePrev();

			// Active anchor.
			activedEle = $anchor;
			$anchor.addClass('ftwp-active');
			if (actionObj.location.fixedToPost || actionObj.location.fixedWidget) {
				if (!$anchor.is(':focus') && !animateScrolling) {
					$anchor.focus();
				}
			}

			// Active icon
			if (conditionObj.showColExpIcon()) {
				var $icon = $anchor.prev();
				if ($icon.length) {
					$icon.addClass('ftwp-active');
				}
			}
		};

		// Deactive the prev elements.
		var deactivePrev = function () {
			if (activedEle) {
				activedEle.removeClass('ftwp-active').blur();
				if (conditionObj.showColExpIcon()) {
					var $icon = activedEle.prev();
					if ($icon.length) {
						$icon.removeClass('ftwp-active');
					}
				}
			}
		};

		// Deactive all elements.
		var deactiveAll = function () {
			activedEle = undefined;
			$e.anchors.removeClass('ftwp-active').blur();
			if (conditionObj.showColExpIcon()) {
				$e.colExpIcons.removeClass('ftwp-active');
			}
		};

		// Public API
		return {
			_construct: _construct,
			activeCurrent: activeCurrent,
			deactiveAll: deactiveAll,
			deactivePrev: deactivePrev
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * target indicator.
	 *
	 * @since 3.0.0
	 */
	var targetIndicator = (function () {
		// Constructor
		var _construct = function () {
			$e.window.on('ftocScroll', hint).on('ftocResize', hint);

			consoleLog( 'Actived targetIndicator().' );
		};

		var start = function () {
			$e.window.on('ftocScroll', hint).on('ftocResize', hint);
		};

		var stop = function () {
			$e.window.off('ftocScroll', hint).off('ftocResize', hint);
		};

		// Present previous heading.
		var prevHeading;
		var preDocumentHeight;

		// Hint
		var hint = function () {
			var headingsTop = dataObj.data.headingsTop;
			var scrollTop = dataObj.data.scrollTop;
			

			if (headingsTop[0].headingTop > scrollTop || dataObj.data.ftocRangeY.bottom < scrollTop) {
				if (undefined !== prevHeading) {
					scrollToTarget.deactiveAll();
				}

				prevHeading = undefined;
				return;
			}
			
			
			if (undefined !== preDocumentHeight && preDocumentHeight != dataObj.data.document.height) {
				activeCurrentAnchor();
				
				consoleLog( 'Fixed target indicator!!' );
				
				return;
			}
//			
			if (undefined !== prevHeading && prevHeading[0].headingTop <= scrollTop && prevHeading[1].headingTop > scrollTop) {
				return;
			}
			
			activeCurrentAnchor();
			
			function activeCurrentAnchor() {
				$.each(headingsTop, function (i) {
					if (undefined === headingsTop[i + 1] && this.headingTop <= dataObj.data.ftocRangeY.bottom ) {
						prevHeading = [this, headingsTop[i], i];
						preDocumentHeight = dataObj.data.document.height;
						
						// After target indicated.
						scrollToTarget.activeCurrent(this.anchorEle);

						$e.container.trigger('ftocAfterTargetIndicated', [this.anchorEle, scrollTop]);
						
						return false;
					}

					if (this.headingTop <= scrollTop && headingsTop[i + 1].headingTop > scrollTop) {
						prevHeading = [this, headingsTop[i + 1], i];
						preDocumentHeight = dataObj.data.document.height;

						// After target indicated.
						scrollToTarget.activeCurrent(this.anchorEle);

						$e.container.trigger('ftocAfterTargetIndicated', [this.anchorEle, scrollTop]);

						return false;
					}
				});				
			}
		};

		// Public API
		return {
			_construct: _construct,
			start: start,
			stop: stop
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Only scrolling the list prevent scrolling the window.
	 *
	 * @since 3.0.0
	 */
	var noScrollWindow = (function () {
		// Constructor
		var _construct = function (evt, eventParam) {
			init();

			if ('fixedToPost' == eventParam.location) {
				$e.container.on('ftocAfterMaximize', on);
				$e.container.on('ftocAfterMinimize', off);
			}

			consoleLog( 'Actived noScrollWindow().' );
		};

		// Destruct
		var _destruct = function () {
			off();
			$e.container.off('ftocAfterMaximize', on);
			$e.container.off('ftocAfterMinimize', off);

			consoleLog( 'Deactived noScrollWindow().' );			
		};

		// Init
		var init = function () {
			on();
		};

		// On
		var on = function () {
			$e.list.on('scroll', start);
			$e.list.on('mouseleave', stop);
			$e.document.on('click', clickDocumentHandler);
			$e.window.on('scroll', stop);
		};

		// Off
		var off = function () {
			$e.body.removeClass('ftwp-no-scroll');
			$e.list.off('scroll', start);
			$e.list.off('mouseleave', stop);
			$e.document.off('click', clickDocumentHandler);
			$e.window.off('scroll', stop);
		};

		// Start
		var start = function () {
			$e.body.addClass('ftwp-no-scroll');
		};

		// Stop
		var stop = function () {
			if ($e.body.hasClass('ftwp-no-scroll')) {
				$e.list.off('scroll', start);
				$e.body.removeClass('ftwp-no-scroll');
				setTimeout(function () {
					$e.list.on('scroll', start);
				}, 100);
			}
		};

		// Click document handler.
		var clickDocumentHandler = function (evt) {
			if (!($.contains($e.list.get(0), evt.target))) {
				stop();
			}
		};

		// Public API
		return {
			_construct: _construct,
			_destruct: _destruct,
			on: on,
			off: off
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Hide TOC.
	 *
	 * @since 3.0.0
	 */
	var hideToc = (function () {
		// Constructor
		var _construct = function () {
			$e.container.addClass('ftwp-hidden-state');

			consoleLog( 'Actived hideToc().' );
		};

		// Destructor
		var _destruct = function () {
			$e.container.removeClass('ftwp-hidden-state');

			consoleLog( 'Deactived hideToc().' );
		};

		// Public API
		return {
			_construct: _construct,
			_destruct: _destruct
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Fixed TOC show in.
	 *
	 * @since 3.0.0
	 */
	var ftocInOut = (function () {
		// Constructor
		var _construct = function () {
			init();

			// Set location
			location.set();
			$e.window.on('ftocResize', location.set);

			// Effect
			effect.in();
			$e.container.on('ftocAfterMinMax', effect.inOut);

			consoleLog( 'Actived ftocInOut().' );
		};

		// Destructor
		var _destruct = function () {
			uninit();

			location.unset();
			$e.window.off('ftocResize', location.set);

			contentsHeight.unset();

			listHeight.unset();

			effect.out();
			$e.container.off('ftocAfterMinMax', effect.inOut);

			consoleLog( 'Deactived ftocInOut().' );
		};

		// Initialize
		var init = function () {
			$e.container.addClass('ftwp-fixed-to-post');

			if (!$e.container.parent().is($e.body)) {
				$e.container.appendTo($e.body);
			} // Append to body.

			$e.minIcon.addClass('ftwp-icon-minimize');
			
			// Make sure minimize the TOC on mobile
			if ( conditionObj.isMobile() ) {
				if ( $e.container.hasClass( 'ftwp-maximize' ) ) {
					$e.container.removeClass( 'ftwp-maximize' ).addClass( 'ftwp-minimize' );
				}
			}
		};

		// Uninitialize
		var uninit = function () {
			$e.container.removeClass('ftwp-fixed-to-post');
			$e.minIcon.removeClass('ftwp-icon-minimize');
		};

		// Set location
		var location = (function () {
			var setPosition = function () {
				effect.setTransformOrigin(); // Set transform origin.

				if (conditionObj.isPositionAtFixed('left')) {
					var right = dataObj.data.window.width - dataObj.data.postRect.left + fixedtocOption.fixedOffsetX;
					var leftSpace = dataObj.data.postRect.left - fixedtocOption.fixedOffsetX;
					setLeftStyle($e.trigger, right, leftSpace);
					setLeftStyle($e.contents, right, leftSpace);
				} else {
					var left = dataObj.data.postRect.right + fixedtocOption.fixedOffsetX;
					var rightSpace = dataObj.data.window.width - left;
					setRightStyle($e.trigger, left, rightSpace);
					setRightStyle($e.contents, left, rightSpace);
				}

				resetTopStyle();

				// After set fixed location.
				contentsHeight.reset();
			};

			var isEdge = function (ele, space) {
				var eleWidth = ele.outerWidth();
				if (space <= eleWidth) {
					return true;
				} else {
					return false;
				}
			};

			var setLeftStyle = function (ele, right, space) {
				if (isEdge(ele, space)) {
					ele.css({
						left: '0px',
						right: 'auto'
					});

					effect.reverseTransformOrigin(ele); // reverse transform origin.
				} else {
					ele.css({
						right: right + 'px',
						left: 'auto'
					});
				}
			};

			var setRightStyle = function (ele, left, space) {
				if (isEdge(ele, space)) {
					ele.css({
						right: '0px',
						left: 'auto'
					});

					effect.reverseTransformOrigin(ele); // Reverse transform origin.
				} else {
					ele.css({
						left: left + 'px',
						right: 'auto'
					});
				}
			};

			var resetTopStyle = function () {
				if (conditionObj.isPositionAtFixed('top')) {
					$e.trigger.css('top', dataObj.data.fixedOffsetTop + 'px');
					$e.contents.css('top', dataObj.data.fixedOffsetTop + 'px');
				} else if (conditionObj.isPositionAtFixed('middle')) {
					$e.contents.css('top', dataObj.data.fixedHeight + 'px');
				} else {
					$e.trigger.css('top', '');
					$e.contents.css('top', '');
				}
			};

			var unsetPosition = function () {
				$e.trigger.css({
					left: '',
					right: '',
					top: ''
				});

				$e.contents.css({
					left: '',
					right: '',
					top: ''
				});

				effect.removeTransformOrigin(); // Remove transform origin.
			};

			return {
				set: setPosition,
				unset: unsetPosition
			};
		})();

		// Reset height of contents.
		var contentsHeight = (function () {
			// Reset
			var reset = function () {
				// Get contents original height.
				var originalHeight = getOriginal();

				// Reset to fit the window.
				var newHeight;
				if (conditionObj.isPositionAtFixed('middle')) {
					newHeight = dataObj.data.window.height - dataObj.data.fixedHeight;
				} else {
					newHeight = dataObj.data.window.height - dataObj.data.fixedOffsetTop;
				}

				var height;
				if (newHeight < originalHeight) {
					height = newHeight;
				} else {
					height = originalHeight;
				}

				$e.contents.css('height', height + 'px');

				// After reset contents height.
				var contentsHeight = getContentsHeight(height);
				listHeight.set(contentsHeight);
			};

			// Unset
			var unset = function () {
				$e.contents.css('height', '');
			};

			// Detect if is reset when top position.
			var isTopReset = function (originalHeight) {
				var top = dataObj.data.fixedOffsetTop;
				if (!top) {
					return false;
				}

				if ((top + originalHeight) > dataObj.data.window.height) {
					return true;
				}

				return false;
			};

			// Detect if is reset when bottom position.
			var isBottomReset = function (originalHeight) {
				if (!conditionObj.isPositionAtFixed('bottom')) {
					return false;
				}

				var offsetBottom = fixedtocOption.fixedOffsetY;
				if (0 == offsetBottom) {
					return false;
				}

				if ((offsetBottom + originalHeight) > dataObj.data.window.height) {
					return true;
				}

				return false;
			};

			// Get original contents height.
			var getOriginal = function () {
				var contentsHeight;
				if (conditionObj.isAutoHeightFixedToPost()) {
					if (conditionObj.isColExpList()) {
						contentsHeight = window.innerHeight;
					} else {
						listHeight.setAuto();
						$e.contents.css('height', 'auto');
						contentsHeight = $e.contents.outerHeight();
						unset();
						listHeight.unset();
					}
				} else {
					contentsHeight = fixedtocOption.contentsFixedHeight;
				}

				return contentsHeight;
			};

			// Public API
			return {
				reset: reset,
				unset: unset
			};
		})();

		// Animate in/out effect.
		var effect = (function () {
			var inCls = 'ftwp-animate-' + fixedtocOption.inOutEffect + '-in';
			var inOutCls = 'ftwp-animate-' + fixedtocOption.inOutEffect + '-inOut';

			// Show in
			var showIn = function () {
				$e.container.addClass(inCls);
			};

			// Show/hide in out
			var showInOut = function () {
				$e.container.removeClass(inCls + ' ' + inOutCls);
				void $e.container.offsetWidth;
				$e.container.addClass(inOutCls);
				setTimeout(function () {
					$e.container.removeClass(inOutCls);
				}, 1000);
			};

			// Hide out
			var hideOut = function () {
				$e.container.removeClass(inCls + ' ' + inOutCls);
			};

			// Set transform origin
			var transformCls, newTransformCls;
			var setTransformOrigin = function () {
				var re = fixedtocOption.fixedPosition.match(/(\w+)\-(\w+)/i);
				if (re) {
					var horizontal = re[2];
					var vertical = re[1];
					if ('left' == horizontal) {
						horizontal = 'right';
					} else if ('right' == horizontal) {
						horizontal = 'left';
					}
					if ('middle' == vertical) {
						vertical = 'center';
					}
					transformCls = 'ftwp-transform-' + horizontal + '-' + vertical;
					$e.trigger.removeClass(newTransformCls).addClass(transformCls);
					$e.contents.removeClass(newTransformCls).addClass(transformCls);
				}
			};

			var reverseTransformOrigin = function (ele) {
				if (transformCls.match(/left/i)) {
					newTransformCls = transformCls.replace('left', 'right');
				} else {
					newTransformCls = transformCls.replace('right', 'left');
				}

				ele.removeClass(transformCls).addClass(newTransformCls);
			};

			var removeTransformOrigin = function () {
				$e.trigger.removeClass(transformCls + ' ' + newTransformCls);
				$e.contents.removeClass(transformCls + ' ' + newTransformCls);
			};

			// Public API
			return {
				inCls: inCls,
				in: showIn,
				inOut: showInOut,
				out: hideOut,
				setTransformOrigin: setTransformOrigin,
				reverseTransformOrigin: reverseTransformOrigin,
				removeTransformOrigin: removeTransformOrigin
			};
		})();

		// Public API
		return {
			_construct: _construct,
			_destruct: _destruct,
			effectInCls: effect.inCls
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Minimize/maximize the FTOC.
	 *
	 * @since 3.0.0
	 */
	var minMaxFtoc = (function () {
		// Constructor
		var _construct = function (event) {
			$e.trigger.on('click', maximize);
			$e.minIcon.on('click', minimize);

			$e.trigger.on('mousedown', preventDefaultEvt);
			$e.minIcon.on('mousedown', preventDefaultEvt);

			if (conditionObj.isQuickMin()) {
				$e.document.on('click touchstart', quickMin);
			}

			if (conditionObj.isEscMin()) {
				$e.document.on('keyup', escMin);
			}

			if (conditionObj.isEnterMax()) {
				$e.document.on('keyup', enterMax);
			}

			consoleLog( 'Actived minMaxFtoc().' );
		};

		// Destructor
		var _destruct = function (event) {
			$e.trigger.off('click', maximize);
			$e.minIcon.off('click', minimize);

			$e.trigger.off('mousedown', preventDefaultEvt);
			$e.minIcon.off('mousedown', preventDefaultEvt);

			if (conditionObj.isQuickMin()) {
				$e.document.off('click', quickMin);
			}

			if (conditionObj.isEscMin()) {
				$e.document.off('keyup', escMin);
			}

			if (conditionObj.isEnterMax()) {
				$e.document.off('keyup', enterMax);
			}

			consoleLog( 'Deactived minMaxFtoc.' );
		};

		// Maximize
		var maximize = function () {
			$e.container.removeClass('ftwp-minimize').addClass('ftwp-maximize');

			$e.container.trigger('ftocAfterMinMax');
			$e.container.trigger('ftocAfterMaximize');

			consoleLog( 'Maximized FTOC.' );
		};

		// Minimize
		var minimize = function () {
			$e.container.removeClass('ftwp-maximize').addClass('ftwp-minimize');

			$e.container.trigger('ftocAfterMinMax');
			$e.container.trigger('ftocAfterMinimize');

			consoleLog( 'Minimized FTOC.' );
		};

		// Click anywhere except the container.
		var quickMin = function (evt) {
			if ( ( evt.type == 'touchstart' ) && ( dataObj.data.window.width > 768 ) ) {
				return;
			}
			
			if ($e.container.hasClass('ftwp-maximize') && !($.contains($e.container.get(0), evt.target))) {
				minimize();
			}
		};

		// Press the esc keyboard to minimize the container.
		var escMin = function (evt) {
			if ($e.container.hasClass('ftwp-maximize') && 27 == evt.keyCode) {
				minimize();
			}
		};

		// Press the enter keyboard to maximize the container.
		var enterMax = function (evt) {
			if ($e.container.hasClass('ftwp-minimize') && 13 == evt.keyCode) {
				maximize();
			}
		};

		// Detect if it is maximized.
		var isMax = function () {
			if ($e.container.hasClass('ftwp-maximize')) {
				return true;
			} else {
				return false;
			}
		};

		var isMin = function () {
			if ($e.container.hasClass('ftwp-minimize')) {
				return true;
			} else {
				return false;
			}
		};

		// Public API
		return {
			_construct: _construct,
			_destruct: _destruct,
			isMax: isMax,
			isMin: isMin,
			minimize: minimize
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Fade trigger button.
	 *
	 * @since 3.0.0
	 */
	var fadeTrigger = (function () {
		var fadeTimeoutId;
		var fadeCls = 'ftwp-fade-trigger';
		var unfadeCls = 'ftwp-unfade-trigger';

		// Constructor
		var _construct = function () {
			if ($e.container.hasClass('ftwp-minimize')) {
				start();
			}

			$e.trigger.on('mouseenter', pause).on('mouseleave', restart);
			$e.container.on('ftocAfterMinimize', start).on('ftocAfterMaximize', stop);

			consoleLog( 'Actived fadeTrigger().' );
		};

		// Destructor
		var _destruct = function () {
			stop();

			$e.trigger.off('mouseenter', mouseEnter);
			$e.trigger.off('mouseleave', mouseLeave);
			$e.container.off('ftocAfterMinimize', start);
			$e.container.off('ftocAfterMaximize', stop);

			consoleLog( 'Deactived fadeTrigger().' );
		};

		// Start
		var start = function () {
			if (undefined === fadeTimeoutId) {
				setTimeout(function () {
					$e.container.removeClass(ftocInOut.effectInCls);
				}, 500);

				fadeTimeoutId = setTimeout(function () {
					$e.trigger.addClass(fadeCls);
				}, fixedtocOption.fadeTriggerDuration);
			}
		};

		// Stop
		var stop = function () {
			if (undefined !== fadeTimeoutId) {
				clearTimeout(fadeTimeoutId);
				fadeTimeoutId = undefined;
				$e.trigger.removeClass(fadeCls + ' ' + unfadeCls);
			}
		};

		// Pause
		var pause = function () {
			if (undefined !== fadeTimeoutId) {
				clearTimeout(fadeTimeoutId);
				$e.trigger.removeClass(fadeCls).addClass(unfadeCls);
			}
		};

		// Restart
		var restart = function () {
			if (undefined !== fadeTimeoutId) {
				stop();
				start();
			}
		};

		// Mouse enter
		var mouseEnter = function () {
			pause();
		};

		// Mouse leave
		var mouseLeave = function () {
			restart();
		};

		// Public API
		return {
			_construct: _construct,
			_destruct: _destruct,
			stop: stop,
			start: start,
			restart: restart,
			mouseLeave: mouseLeave
		};
	})();


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Blur background.
	 *
	 * @since 3.0.0
	 */
	var blurBackground = (function () {
		// Start blut
		var start = function (ele) {
			if ( conditionObj.isNotBlur() ) {
				return;
			}
			
			if (ele && ele.length) {
				ele.removeClass('ftwp-unblur').addClass('ftwp-blur');
			}
		};

		// Stop blur
		var stop = function (ele) {
			if (ele && ele.length && ele.hasClass('ftwp-blur')) {
				ele.removeClass('ftwp-blur').addClass('ftwp-unblur');
				setTimeout(function () {
					ele.removeClass('ftwp-unblur');
				}, 500);
			}
		};

		// Clear blur
		var clear = function (ele) {
			if (ele && ele.length) {
				ele.removeClass('ftwp-blur ftwp-unblur');
			}
		};

		// Public API
		return {
			start: start,
			stop: stop,
			clear: clear
		};
	})();

	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Blur body.
	 *
	 * @since 3.0.0
	 */
	var blurBody = (function () {
		var $targets;

		// Constructor
		var _construct = function () {
			init();
			$e.window.on('ftocResize', initOnResize);
			$e.container.on('ftocAfterMaximize', afterMax);
			$e.container.on('ftocAfterMinimize', afterMin);
			$e.container.on('ftocAfterScrollToTarget', afterScrollToTarget);

			consoleLog( 'Actived blurBody().' );
		};

		// Destructor
		var _destruct = function () {
			uninit();
			$e.window.off('ftocResize', initOnResize);
			$e.container.off('ftocAfterMaximize', afterMax);
			$e.container.off('ftocAfterMinimize', afterMin);
			$e.container.off('ftocAfterScrollToTarget', afterScrollToTarget);

			consoleLog( 'Deactived blurBody().' );
		};

		var init = function () {
			$targets = $e.container.siblings(':not("script, style")');

			if (minMaxFtoc.isMax() && isBlur()) {
				blurBackground.start($targets);
			}
		};

		var initOnResize = function () {
			if (minMaxFtoc.isMax() && isBlur()) {
				blurBackground.start($targets);
			} else {
				blurBackground.stop($targets);
			}
		};

		var afterMax = function () {
			if (isBlur()) {
				blurBackground.start($targets);
			}
		};

		var afterMin = function () {
			blurBackground.stop($targets);
		};

		var afterScrollToTarget = function (evt, $anchor) {
			if (isBlur() && $e.container.hasClass('ftwp-maximize')) {
				minMaxFtoc.minimize();
				blurBackground.stop($targets);
				$anchor.blur();
			}
		};

		var uninit = function () {
			blurBackground.clear($targets);
		};

		var isBlur = function () {
			if ($e.window.width() * 0.6 <= $e.contents.outerWidth()) {
				return true;
			} else {
				return false;
			}
		};

		// Public API
		return {
			_construct: _construct,
			_destruct: _destruct
		};
	})();


	if (conditionObj.fixedWidget()) {
		/** ---------------------------------------------------------------------------------------------------------------------------------------
		 * Blur widgets.
		 *
		 * @since 3.0.0
		 */
		var blurWidgets = (function () {
			var $targets;

			// Constructor
			var _construct = function () {
				$targets = $e.widget.siblings('.widget');

				init();
				$e.contents.on('ftocAfterExpandContents', startBlur);
				$e.contents.on('ftocAfterCollapseContents', stopBlur);

				consoleLog( 'Actived blurWidgets().' );
			};

			// Destructor
			var _destruct = function () {
				uninit();
				$e.contents.off('ftocAfterExpandContents', startBlur);
				$e.contents.off('ftocAfterCollapseContents', stopBlur);

				consoleLog( 'Deactived blurWidgets().' );
			};

			// Init
			var init = function () {
				if ('expand' == $e.contents.data('colexp')) {
					blurBackground.start($targets);
				}
			};

			// Start blur
			var startBlur = function () {
				blurBackground.start($targets);
			};

			// Stop blur
			var stopBlur = function () {
				blurBackground.stop($targets);
			};

			// Uninit
			var uninit = function () {
				blurBackground.clear($targets);
			};

			// Public API
			return {
				_construct: _construct,
				_destruct: _destruct
			};
		})();
	}


	if (conditionObj.inWidget() || conditionObj.supportInPost()) {
		/** ---------------------------------------------------------------------------------------------------------------------------------------
		 * Collapse/expand contents.
		 *
		 * @since 3.0.0
		 */
		var colExpContents = (function () {
			var funcExp, funcCol, firstInit = true; 

			// Constructor
			var construct = function (expFunc, colFunc) {
				funcExp = expFunc;
				funcCol = colFunc;

				init();

				$e.minIcon.on('mousedown', preventDefaultEvt);
				$e.minIcon.on('click', toggle);
			};

			// Destructor
			var destruct = function () {
				uninit();

				$e.minIcon.off('mousedown', preventDefaultEvt);
				$e.minIcon.off('click', toggle);
			};

			// Init
			var init = function () {
                if (conditionObj.isMobile() && firstInit) {
                    collapse(0, funcCol);
                    controlObj.reload();
                } else if (isExpand()) {
					expand(0, funcExp);
				} else {
					collapse(0, funcCol);
				}
                
                firstInit = false;
			};

			// Uninit
			var uninit = function () {
				$e.list.show(0);
				$e.minIcon.removeClass('ftwp-icon-collapse ftwp-icon-expand');
			};

			// Toggle
			var toggle = function (evt) {
				if (isExpand()) {
					collapse(100, funcCol, evt);
				} else {
					expand(100, funcExp, evt);
				}
			};

			// Detect if it is expand state.
			var isExpand = function () {              
				var colExpData = $e.contents.data('colexp');
				if ('expand' == colExpData || undefined === colExpData) {
					return true;
				} else {
					return false;
				}
			};

			// Collapse
			var collapse = function (duration, func, evt) {
				$e.list.hide(duration, function () {
					$e.minIcon.removeClass('ftwp-icon-expand').addClass('ftwp-icon-collapse');
					if (undefined !== func) func(evt);
				});

				$e.contents.data('colexp', 'collapse');
				$e.contents.trigger('ftocAfterCollapseContents');

				consoleLog( 'Collapsed contents.' );
			};

			// Expand
			var expand = function (duration, func, evt) {
				$e.list.show(duration, function () {
					$e.minIcon.removeClass('ftwp-icon-collapse').addClass('ftwp-icon-expand');
					if (undefined !== func) func(evt);
				});

				$e.contents.data('colexp', 'expand');
				$e.contents.trigger('ftocAfterExpandContents');

				consoleLog( 'Expanded contents.' );
			};

			return {
				construct: construct,
				destruct: destruct
			};
		})();
	}


	if (conditionObj.inWidget()) {
		/** ---------------------------------------------------------------------------------------------------------------------------------------
		 * Collapse/expand contents in widget.
		 *
		 * @since 3.0.0
		 */
		var colExpConentsInWidget = (function () {
			// Constructor
			var _construct = function () {
				colExpContents.construct();

				consoleLog( 'Actived colExpConentsInWidget().' );
			};

			// Destructor
			var _destruct = function () {
				colExpContents.destruct();

				consoleLog( 'Deactived colExpConentsInWidget().' );
			};

			// Public API
			return {
				_construct: _construct,
				_destruct: _destruct
			};
		})();
	}


	if (conditionObj.fixedWidget()) {
		/** ---------------------------------------------------------------------------------------------------------------------------------------
		 * Collapse/expand contents in fixed widget.
		 *
		 * @since 3.0.0
		 */
		var colExpConentsInFixedWidget = (function () {
			// Constructor
			var _construct = function () {
				colExpContents.construct(funcExp, funcCol);

				consoleLog( 'Actived colExpConentsInFixedWidget().' );
			};

			// Destructor
			var _destruct = function () {
				colExpContents.destruct();

				consoleLog( 'Deactived colExpConentsInFixedWidget().' );
			};

			// Expend function.
			var funcExp = function () {
				dataObj.ftocRectInWidget.updateHeight();
				fixedInWidget.setFixed();
				//				listHeight.set();
			};

			// Collapse function.
			var funcCol = function () {
				dataObj.ftocRectInWidget.updateHeight();
				fixedInWidget.setFixed();
				//				listHeight.set();
			};

			// Public API
			return {
				_construct: _construct,
				_destruct: _destruct
			};
		})();
	}


	if (conditionObj.inWidget()) {
		/** ---------------------------------------------------------------------------------------------------------------------------------------
		 * Display in widget.
		 *
		 * @since 3.0.0
		 */
		var displayInWidget = (function () {
			// Constructor
			var _construct = function () {
				init();

				consoleLog( 'Actived displayInWidget().' );
			};

			// Destruct
			var _destruct = function () {
				$e.contents.css('height', '');

				consoleLog( 'Deactived displayInWidget().' );
			};

			var init = function () {
				if (!$e.container.parent().is($e.widgetContainer)) {
					$e.container.appendTo($e.widgetContainer);
				}

				$e.contents.css('height', 'auto');
			};

			// Public API
			return {
				_construct: _construct,
				_destruct: _destruct,
				init: init,
			};
		})();
	}


	if (conditionObj.fixedWidget()) {
		/** ---------------------------------------------------------------------------------------------------------------------------------------
		 * Fixed in widget.
		 *
		 * @since 3.0.0
		 */
		var fixedInWidget = (function () {
			// Constructor
			var _construct = function () {
				init();

				setFixed();
				$e.window.on('ftocResize', setFixed);

				consoleLog( 'Actived fixedInWidget().' );
			};

			// Destruct
			var _destruct = function () {
				init();

				unsetFixed();
				$e.window.off('ftocResize', setFixed);

				listHeight.unset();

				consoleLog( 'Deactived fixedInWidget().' );
			};

			// Init
			var init = function () {
				displayInWidget.init();
			};

			// Uninit
			var uninit = function () {
				displayInWidget.uninit();
			};

			// Set fixed position.
			var setFixed = function () {
				$e.widget.addClass('ftwp-widget-fixed');

				var rect = dataObj.data.ftocRectInWidget;
				$e.contents.css({
					left: rect.left,
					top: rect.top,
					width: rect.width + 'px',
					height: rect.height + 'px'
				});

				// After set fixed position.
				var contentsHeight = getContentsHeight(rect.height);
				listHeight.set(contentsHeight);
			};

			// Unset fixed position
			var unsetFixed = function () {
				$e.widget.removeClass('ftwp-widget-fixed');

				$e.contents.css({
					left: '',
					top: '',
					width: '',
					height: ''
				});
			};

			// Public API
			return {
				_construct: _construct,
				_destruct: _destruct,
				setFixed: setFixed
			};
		})();
	}


	if (conditionObj.supportInPost()) {
		/** ---------------------------------------------------------------------------------------------------------------------------------------
		 * Display in post
		 *
		 * @since 3.0.0
		 */
		var displayInPost = (function () {
			// Constructor
			var _construct = function () {
				init();
				$e.window.on('ftocResize', setSize);

				consoleLog( 'Actived displayInPost().' );
			};

			// Destructor
			var _destruct = function () {
				uninit();
				$e.window.off('ftocResize', setSize);

				consoleLog( 'Deactived displayInPost().' );
			};

			// Init
			var init = function () {
				if (!$e.container.parent().is($e.containerOuter)) {
					$e.container.appendTo($e.containerOuter);
				}

				setSize();
			};

			// Uninit
			var uninit = function () {
				$e.contents.css('height', '');
				listHeight.unset();
			};

			// Set width and height
			var containerOuterWidth;
			var setSize = function () {
				// Width
				if (0 == fixedtocOption.contentsWidthInPost && conditionObj.isFloat()) {
					$e.containerOuter.css('width', '');
//					containerOuterWidth = $e.containerOuter.outerWidth() + 1;
					containerOuterWidth = $e.containerOuter.outerWidth();
					$e.containerOuter.css('width', containerOuterWidth + 'px');
				} else {
					$e.containerOuter.css('width', '');
					//					containerOuterWidth = undefined;
				}

				// Height
				$e.containerOuter.css('height', dataObj.data.containerOuterHeight + 'px');
				$e.contents.css('height', dataObj.data.containerOuterHeight + 'px');

				var contentsHeight = getContentsHeight(dataObj.data.containerOuterHeight);
				listHeight.set(contentsHeight);
			};

			// Public API
			return {
				_construct: _construct,
				_destruct: _destruct
			};
		})();


		/* Collapse/expand contents in post.
		 *
		 * @since 3.0.0
		 */
		var colExpConentsInPost = (function () {
			// Constructor
			var _construct = function () {
				colExpContents.construct(funcExp, funcCol);

				consoleLog( 'Actived colExpConentsInFixedWidget().' );
			};

			// Destructor
			var _destruct = function () {
				colExpContents.destruct();

				consoleLog( 'Deactived colExpConentsInFixedWidget().' );
			};

			// Expend function.
			var funcExp = function (evt) {
				funcCol(evt);
			};

			// Collapse function.
			var funcCol = function (evt) {
				if (undefined !== evt && 'click' == evt.type) {
					dataObj.updateInPost();
				}

//				$e.containerOuter.css('height', dataObj.data.containerOuterHeight + 'px');
//				$e.contents.css('height', dataObj.data.containerOuterHeight + 'px');
//				listHeight.set(getContentsHeight(dataObj.data.containerOuterHeight));
			};

			// Public API
			return {
				_construct: _construct,
				_destruct: _destruct
			};
		})();
	}


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Control the whole.
	 *
	 * @since 3.0.0
	 */
	var controlObj = (function () {
		// Handler for 'ready' event
		var onReady = function () {
			// Set extra options.
			option.init();

			// Create elements.
			createElements();

			// Set index data to anchors.
			setAnchorIndex();

			// Trigget ftoc_ready event.
			$e.container.trigger('ftocReady');

			// Bind load event.
//			$e.window.load(onLoad);
            onLoad();
            setTimeout(reload, 100);    // Force to refresh data if the page isn't still loaded.
            $e.window.load(reload);
            
			// Output fixedtocOption in console.
			consoleLog( fixedtocOption );
		};

		// Handler for 'load' event
		var onLoad = function () {
			// Create data.
			dataObj.createOnInit();

			// Register actions.
			if (conditionObj.isColExpList()) {
				actionObj.register('common', colExpSubList);
			}
			actionObj.register('common', scrollToTarget);
			actionObj.register('common', targetIndicator);

			actionObj.register('hidden', hideToc);

			if (conditionObj.fixedWidget()) {
				actionObj.register('fixedWidget', colExpConentsInFixedWidget);
				actionObj.register('fixedWidget', fixedInWidget);
				actionObj.register('fixedWidget', blurWidgets);
			}

			if (conditionObj.inWidget()) {
				actionObj.register('inWidget', displayInWidget);
				actionObj.register('inWidget', colExpConentsInWidget);
			}

			if (conditionObj.supportInPost()) {
				actionObj.register('inPost', displayInPost);
				actionObj.register('inPost', colExpConentsInPost);
			}

			actionObj.register('fixedToPost', ftocInOut);
			actionObj.register('fixedToPost', minMaxFtoc);
			actionObj.register('fixedToPost', fadeTrigger);
			actionObj.register('fixedToPost', blurBody);
			
			// Initial actions.
			actionObj.init();

			// Bind resize event.
			$e.window.resize(onResize);

			// Bind scroll event.
			$e.window.scroll(onScroll);
		};

		// Handler for 'resize' event.
		var onResize = function () {
			// Update data and actions on resize.
			dataObj.updateOnResize();
			actionObj.updateOnResize();
			$e.window.trigger('ftocResize');

			//			$( '#ftwp-header-title' ).text( dataObj.data.window.height );
		};

		// handler for 'scroll' event.
		var prevWindowHeight;
		var onScroll = function () {
			// Update data and actions on scroll.
            dataObj.updateOnDocumentHeightChange();
			dataObj.updateOnScroll();
            
			actionObj.updateOnScroll();
            
			$e.window.trigger('ftocScroll');
		};

		// Reload
		var reload = function () {
            // Output fixedtocOption in console.
            consoleLog( fixedtocOption );
            
			dataObj.updateOnResize();
			actionObj.updateOnResize();
			$e.window.trigger('ftocResize');
		};

		// Public API
		return {
			option: option,
			onReady: onReady,
			reload: reload
		};
	})(); // End controlObj

	// Bind ready event.
	$(document).ready(controlObj.onReady);


	/** ---------------------------------------------------------------------------------------------------------------------------------------
	 * Public API.
	 *
	 * @since 3.0.0
	 */
	return {
		option: controlObj.option,
		reload: controlObj.reload
	};

})(jQuery);
