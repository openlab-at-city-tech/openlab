/*global soliloquy_ajax, MediaElementPlayer */
/**
* soliloquy.js is a placeholder, which CodeKit attaches the following JS files to, before compiling as min/soliloquy-min.js:
* - lib/bxslider.js
* - lib/mousewheel.js
*
* To load more JS resources:
* - Add them to the lib subfolder
* - Add the to the imports directive of this file in CodeKit
*/
// Mobile checker function.
function soliloquyIsMobile() {
	var check = false;
	(function (a) {
		if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true;
	})(navigator.userAgent || navigator.vendor || window.opera);
	return check;
}

// Video functions.
function soliloquyYouTubeVids(data, id, width, height, holder, $, $current) {
	// Immediately make the holder visible and increase z-index to overlay the player icon.
	$('#' + holder).show().css({ 'display': 'block', 'z-index': '1210' });
	player = $current;

	// Load a new video into the slider.
	if (YT.Player) {

		// Only init YouTube player if we haven't done so already for this video ID
		// If we have multiple sliders on a single page, this function is called twice,
		// so if we re-init the player a second time, everything breaks.
		if (typeof soliloquy_youtube[id] === 'undefined') {
			soliloquy_youtube[id] = new YT.Player(holder, {
				videoId: id,
				playerVars: data,
				events: {
					'onStateChange': soliloquyYouTubeOnStateChange
				}
			});
		}
	}
}
function soliloquyYouTubeOnStateChange(event) {
	var id = jQuery(event.target.getIframe()).data('soliloquy-slider-id');

	// If the video has been paused or has finished playing, restart the slider.
	if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
		if (soliloquy_slider[id]) {
			if (soliloquy_slider[id].getSetting('auto')) {
				soliloquy_slider[id].startAuto();
			}
		}
	}

	// If the video is playing or buffering, pause the slider.
	if (event.data === YT.PlayerState.PLAYING || event.data === YT.PlayerState.BUFFERING) {
		if (soliloquy_slider[id]) {
			soliloquy_slider[id].stopAuto();
		}
	}
}
function onYouTubeIframeAPIReady() { }
function soliloquyVimeoVids(data, id, width, height, holder, $) {

	// Immediately make the holder visible and increase z-index to overlay the player icon.
	$('#' + holder).show().css({ 'display': 'block', 'z-index': '1210' });
	// Load a new video into the slider.
	if ($f) {
		var attrs = {};

		$.each($('#' + holder)[0].attributes, function (idx, attr) {
			attrs[attr.nodeName] = attr.nodeValue;
		});

		// Add iframe specific attributes.
		data.player_id = holder;
		attrs.src = '//player.vimeo.com/video/' + id + '?' + $.param(data);
		attrs.frameborder = 0;

		// Convert the holder to the video.
		$('#' + holder).replaceWith(function () {
			return $('<iframe />', attrs).append($(this).contents());
		});

		// Store a reference to the video object for use with the API.
		soliloquy_vimeo[id] = $f($('#' + holder)[0]);
		var slider_id = $('#' + holder).data('soliloquy-slider-id');

		soliloquy_vimeo[id].addEvent('ready', function () {
			//stopAuto when video ready, prevents autoplay while buffering
			if (soliloquy_slider[slider_id]) {
				soliloquy_slider[slider_id].stopAuto();
			}
			soliloquy_vimeo[id].addEvent('play', function () {

				if (soliloquy_slider[slider_id]) {
					soliloquy_slider[slider_id].stopAuto();
				}
			});
			soliloquy_vimeo[id].addEvent('pause', function () {
				if (soliloquy_slider[slider_id].getSetting('auto')) {
					soliloquy_slider[slider_id].startAuto();
				}
			});
			soliloquy_vimeo[id].addEvent('finish', function () {
				if (soliloquy_slider[slider_id].getSetting('auto')) {
					soliloquy_slider[slider_id].startAuto();
				}
			});
		});
	}

}
function soliloquyVimeoSliderPause(vid) {
	var id = jQuery('#' + vid).data('soliloquy-slider-id');
	if (soliloquy_slider[id]) {
		soliloquy_slider[id].stopAuto();
	}
}
function soliloquyVimeoSliderStart(vid) {
	var id = jQuery('#' + vid).data('soliloquy-slider-id');
	if (soliloquy_slider[id]) {
		if (soliloquy_slider[id].getSetting('auto')) {
			soliloquy_slider[id].startAuto();
		}
	}
}
function soliloquyWistiaVids(data, id, width, height, holder, $) {
	// Immediately make the holder visible and increase z-index to overlay the player icon.
	$('#' + holder).show().css({ 'display': 'block', 'z-index': '1210' });

	if (wistiaEmbeds) {
		var attrs = {};
		$.each($('#' + holder)[0].attributes, function (idx, attr) {
			attrs[attr.nodeName] = attr.nodeValue;
		});

		// Add iframe specific attributes.
		data.container = holder;
		attrs.src = '//fast.wistia.net/embed/iframe/' + id + '?' + $.param(data);
		attrs.frameborder = 0;

		// Convert the holder to the video.
		$('#' + holder).replaceWith(function () {
			return $('<iframe />', attrs).addClass('wistia_embed').append($(this).contents());
		});

		wistiaEmbeds.onFind(function (video) {
			if (id === video.hashedId()) {
				soliloquy_wistia[id] = video;
				soliloquy_wistia[id].bind('play', function () {
					var id = $(this.container).data('soliloquy-slider-id');
					if (soliloquy_slider[id]) {
						soliloquy_slider[id].stopAuto();
					}
				});
				soliloquy_wistia[id].bind('pause', function () {
					var id = $(this.container).data('soliloquy-slider-id');
					if (soliloquy_slider[id]) {
						if (soliloquy_slider[id].getSetting('auto')) {
							soliloquy_slider[id].startAuto();
						}
					}
				});
				soliloquy_wistia[id].bind('end', function () {
					var id = $(this.container).data('soliloquy-slider-id');
					if (soliloquy_slider[id]) {
						if (soliloquy_slider[id].getSetting('auto')) {
							soliloquy_slider[id].startAuto();
						}
					}
				});
				video.play();
			}
		});
	}
}
function soliloquyLocalVids(data, id, width, height, holder, $) {
	// Immediately make the holder visible and increase z-index to overlay the player icon and caption.
	$('#' + holder).show().css({ 'display': 'block', 'z-index': '1210' });

	// Build atts
	var attrs = {};
	$.each($('#' + holder)[0].attributes, function (idx, attr) {
		attrs[attr.nodeName] = attr.nodeValue;
	});

	// Build features for MediaElementPlayer
	var features = [];
	if (data.playpause === 1) {
		features.push('playpause');
	}
	if (data.progress === 1) {
		features.push('progress');
	}
	if (data.current === 1) {
		features.push('current');
	}
	if (data.duration === 1) {
		features.push('duration');
	}
	if (data.volume === 1) {
		features.push('volume');
	}
	if (data.fullscreen === 1) {
		features.push('fullscreen');
	}

	// Init MediaElementPlayer
	soliloquy_local[id] = new MediaElementPlayer('video#' + holder, {
		features: features,
		success: function (mediaElement, domObject) {

			if (data.autoplay == 1) {

				mediaElement.play();

			}

		}
	});

}

/**
* Developer function: call this to initialise any new sliders that
* have appeared on screen after the initial page load.
*
* You'd typically use this with an Infinite Scroll plugin or AJAX call
* - most plugins let you specify a callback, at which point you can
* just call soliloquyInitManually()
*/
function soliloquyInitManually() {

	jQuery(document).ready(function ($) {
		// Find all sliders with data-soliloquy-loaded=0
		var soliloquy_sliders = [];
		$(".soliloquy-outer-container[data-soliloquy-loaded='0']").each(function () {
			soliloquy_sliders.push($('.soliloquy-container', $(this)).attr('id').replace(/^\D+/g, ''));
		});

		if (soliloquy_sliders.length > 0) {
			// Send list of Soliloquy slider IDs via AJAX call to soliloquy_ajax_init_sliders()
			$.post(
				soliloquy_ajax.ajax,
				{
					action: 'soliloquy_init_sliders',
					ajax_nonce: soliloquy_ajax.ajax_nonce,
					ids: soliloquy_sliders,
				},
				function (response) {
					if (response !== '-1' && response !== '0') {
						eval(response);
					}
				}
			);
		}

	});

}