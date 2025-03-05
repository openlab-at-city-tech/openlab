/*!
 * @package WP Pointer Tour
 * @author Joachim Jensen <jv@intox.dk>
 * @license GPLv3
 * @copyright 2016 by Joachim Jensen
 */

(function($) {

	var wp_pointer_tour = {

		$overlay: $('<div style="background:rgba(0,0,0,0.4);z-index:1000;height:100%;position:fixed;width:100%;"></div>'),
		$pointers: [],
		currentPointer: 0,

		init: function() {
			this.createPointers();
		},

		pointerSettings: function(settings) {
			var pointerSettings = {
				buttons: function (event, t) {
					var $closeButton;
					if(settings.dismiss !== false || typeof settings.dismiss == "string") {
						$closeButton = $('<a style="margin:0 5px;" class="button-secondary">' + (typeof settings.dismiss == "string" ? settings.dismiss : WP_PT.close) + '</a>');
						$closeButton.bind('click.pointer', function (e) {
							e.preventDefault();
							wp_pointer_tour.currentPointer = WP_PT.pointers.length-1;
							t.element.pointer('close');
						});
					}
					return $closeButton;
				},
				close: this.finishTour
			};
			return $.extend(settings,pointerSettings);
		},

		finishTour: function(e,t) {
			if(wp_pointer_tour.currentPointer == WP_PT.pointers.length-1) {
				wp_pointer_tour.$overlay.remove();
				$.ajax({
					url: ajaxurl,
					data:{
						'action': 'cas_finish_tour',
						'nonce': WP_PT.nonce,
					},
					dataType: 'JSON',
					type: 'POST',
					success:function(data){
					},
					error: function(xhr, desc, e) {
						console.log(xhr.responseText);
					}
				});
			}
		},

		createPointers: function() {
			var i = 0, ilen = WP_PT.pointers.length;
			for(i; i < ilen;i++) {
				WP_PT.pointers[i] = this.pointerSettings(WP_PT.pointers[i]);
				var $widget = $(WP_PT.pointers[i].ref_id);
				$widget.pointer(WP_PT.pointers[i]);
				this.$pointers.push($widget);
			}
			this.$overlay.prependTo('body');
			this.openPointer();
		},

		openPointer: function() {

			if(this.currentPointer < 0 || 
				this.currentPointer >= WP_PT.pointers.length) return;

			this.finishTour();

			var $widget = this.$pointers[this.currentPointer],
				pointerSettings = WP_PT.pointers[this.currentPointer];

			$widget
			.css("z-index",1001)
			.pointer('open');
			//bluehost-wordpress-plugin sets global .wp-pointer{display:none;}, so override that
			$widget.pointer('widget')[0].style.setProperty("display", "block", "important");

			$('html, body').animate({
				scrollTop: $widget.offset().top-50
			}, 1000);

			var next = typeof pointerSettings.next != 'undefined' ? pointerSettings.next : WP_PT.next,
				prev = typeof pointerSettings.prev != 'undefined' ? pointerSettings.prev : WP_PT.prev;

			if(prev && this.currentPointer > 0) {
				if(prev.indexOf(".") === 0 || prev.indexOf("#") === 0) {
					$("body").one(pointerSettings.prevEvent,prev,this.prevStep);
				} else {
					var $prevButton = $('<a style="margin:0 5px;float:left;" class="button-secondary">' + prev + '</a>');
					$widget.pointer('widget').find('.wp-pointer-buttons').append($prevButton);
					$prevButton.on("click.pointer",this.prevStep);
				}
			}
			if(next && this.currentPointer < WP_PT.pointers.length-1) {
				if(next.indexOf(".") === 0 || next.indexOf("#") === 0) {
					$("body").one(pointerSettings.nextEvent,next,this.nextStep);
				} else {
					var $nextButton = $('<a style="margin:0 5px;" class="button-primary">' + next + '</a>');
					$widget.pointer('widget').find('.wp-pointer-buttons').append($nextButton);
					$nextButton.on("click.pointer",this.nextStep);
				}
			}
		},

		prevStep: function(e) {
			e.preventDefault();
			wp_pointer_tour.changeStep(-1);
		},

		nextStep: function(e) {
			e.preventDefault();
			wp_pointer_tour.changeStep(1);
		},

		changeStep: function(i) {
			this.$pointers[wp_pointer_tour.currentPointer].css("z-index","auto");
			this.$pointers[wp_pointer_tour.currentPointer].pointer('close');
			this.currentPointer += i;
			this.openPointer();
		}

	};

	$(window).on('load.wp-pointers', wp_pointer_tour.init());

})(jQuery);
