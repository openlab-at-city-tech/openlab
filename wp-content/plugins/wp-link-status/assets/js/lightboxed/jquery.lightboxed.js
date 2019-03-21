/*
* $ lightboxed 1.0
* By Pau Iglesias on seedplugins.com
*
* Based on lightbox_me 2.4 by Buck Wilson
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

(function($) {



	var zIndex = false, fronts = [];



	$(document).on('keyup', observeKeyPress);

	function observeKeyPress(e) {
		if (fronts.length && (e.keyCode == 27 || (e.DOM_VK_ESCAPE == 27 && e.which==0))) {
			fronts[fronts.length - 1].trigger('escPress');
		}
	}




	$.fn.wplnst_lightboxed = function(options) {

		return this.each(function() {



			var settings = $.extend({}, $.fn.wplnst_lightboxed.defaults, options);
			var $self = $(this), $overlay;



			function init() {

				fronts.push($self);
				$('body').append($self.hide());

				zIndex = (false === zIndex)? settings.zIndex : zIndex + 1;

				if (settings.showOverlay) {

					$overlay = $('<div class="' + settings.classPrefix + '_overlay"/>');

					$('body').append($overlay);

					setOverlayHeight();
					$(window).resize(setOverlayHeight);

					$overlay.css({
						position	: 'absolute',
						width		: '100%',
						top			: 0,
						left		: 0,
						right		: 0,
						bottom		: 0,
						zIndex		: zIndex,
						display		: 'none',
					});

					$overlay.css(settings.overlayCSS);

					$overlay.fadeIn(settings.overlaySpeed, function() {
						setSelfPosition();
						$self[settings.appearEffect](settings.lightboxSpeed, function() {
							raiseOnLoad();
							if (settings.closeClickOutside) {
								$overlay.click(function(e) {
									closeLightbox();
									e.preventDefault;
									return false;
								});
							}
						});
					});

				} else {

					setSelfPosition();
					$self[settings.appearEffect](settings.lightboxSpeed, function() {
						$self.show();
						raiseOnLoad();
						if (settings.closeClickOutside) {
							$(document).on('click', onClickOutside);
						}
					});
				}

				if (settings.parentLightbox) {
					settings.parentLightbox.fadeOut(200);
				}

				$(window).resize(setSelfPosition).scroll(setSelfPosition);
				$(document).on('click', settings.closeSelector, onCloseSelector);

				$self.on('close', closeLightbox);
				$self.on('escPress', onEscPress);
				$self.on('reposition', setSelfPosition);
			}



			function closeLightbox() {

				fronts.pop();

				if (settings.showOverlay) {
					$overlay.remove();
					$(window).unbind('resize', setOverlayHeight);
				} else if (settings.closeClickOutside) {
					$(document).off('click', onClickOutside);
				}

				if (settings.parentLightbox) {
					settings.parentLightbox.fadeIn(200);
				}

				if (settings.preventScroll) {
					$('body').css('overflow', '');
				}

				$(document).off('click', settings.closeSelector, onCloseSelector);

				$self.off('close', closeLightbox);
				$self.off('escPress', onEscPress);
				$self.off('reposition', setSelfPosition);

				$(window).unbind('resize', setSelfPosition);
				$(window).unbind('scroll', setSelfPosition);

				$self[settings.disappearEffect](settings.lightboxSpeed, function() {
					raiseOnClose();
				});

				//$self.hide();
			}



			function setOverlayHeight() {
				($(window).height() < $(document).height())? $overlay.css({ height : $(document).height() + 'px' }) : $overlay.css({ height : '100%' });
			}



			function setSelfPosition() {

				$self.css({
					left 		: '50%',
					marginLeft	: ($self.outerWidth() / 2) * -1,
					zIndex		: zIndex + 1
				});

				if (($self.height() + 80 >= $(window).height()) && ($self.css('position') != 'absolute')) {

					var topOffset = $(document).scrollTop() + 40;

					$self.css({
						position  : 'absolute',
						top 	  : topOffset + 'px',
						marginTop : 0
					});

				} else if ($self.height() + 80 < $(window).height()) {

					settings.centered? $self.css({
						position  : 'fixed',
						top		  : '50%',
						marginTop : ($self.outerHeight() / 2) * -1
					}) : $self.css({
						position  : 'fixed'
					}).css(settings.modalCSS);

					if (settings.preventScroll) {
						$('body').css('overflow', 'hidden');
					}
				}
			}



			function onEscPress() {
				if (settings.closeEsc) {
					closeLightbox();
				}
			}



			function onClickOutside(e) {
				if (!$self.is(e.target) && 0 === $self.has(e.target).length) {
					closeLightbox();
					e.preventDefault;
					return false;
				}
			}



			function onCloseSelector(e) {
				if ($self.has(e.target).length) {
					closeLightbox();
					e.preventDefault();
					return false;
				}
			}



			function raiseOnLoad() {
				$self.trigger('lightboxedLoad');
				$self.off('lightboxedLoad');
			}



			function raiseOnClose() {
				$self.trigger('lightboxedClose');
				$self.off('lightboxedClose');
			}



			// Start
			init();



		});
	};



	$.fn.wplnst_lightboxed.defaults = {

		// Animation
		appearEffect		: 'fadeIn',
		appearEase			: '',
		disappearEffect		: 'fadeOut',
		disappearEase		: '',
		lightboxSpeed		: 200,
		overlaySpeed		: 250,

		// Close
		closeSelector		: '.wplnst_lightboxed_close',
		closeClickOutside	: true,
		closeEsc			: true,

		// Behavior
		showOverlay			: true,
		parentLightbox		: false,
		preventScroll		: true,

		// Style
		centered			: false,
		classPrefix			: 'wplnst_lbx',
		zIndex				: 9999,
		modalCSS 			: { top : '40px' },
		overlayCSS			: { background : 'black', opacity : .3 }
	}



})(jQuery);