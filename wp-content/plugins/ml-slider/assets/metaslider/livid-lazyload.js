/**
 * Front-end behavior for Livid slides:
 *  - click-to-play for lazy-loaded slides (see MetaLividSlide::build_lazy_placeholder())
 *    - the poster fades out before the embed is swapped in, instead of an
 *      instant cut, since the embed itself can take a moment to appear
 *  - pause the video in place (position preserved, iframe never torn down or
 *    reloaded) once its slide is no longer the visible one, regardless of why
 *    (autoplay advanced, or the visitor clicked next/prev/a dot) - for both
 *    lazy-loaded and eager (non-lazy) slides. The embed is never unloaded or
 *    reset as a "fallback" - if a pause command isn't honored, the video just
 *    keeps playing rather than ever losing its position or restarting.
 *
 * Playback is controlled over the player.js protocol, which Livid's own API
 * docs confirm its embeds implement (https://support.livid.com/article/66-api
 * -> https://github.com/embedly/player.js). Same approach as MetaSlider Pro's
 * Vimeo/YouTube slide types, which drive a real player API rather than
 * guessing at a message format. Three details matter and are easy to get
 * wrong: every message needs a "context": "player.js" field, the player only
 * emits an event after the parent has explicitly registered for it, and
 * anything sent before the embed is listening is dropped rather than
 * buffered - hence the repeated registration and the command queue below.
 *  - while a video is playing and the slideshow itself is set to auto play, hold
 *    that auto-advance until the video ends, then resume it - same idea as
 *    MetaSlider Pro's YouTube slide type (FlexSlider's own manualPause/manualPlay
 *    flags). A safety-net timeout resumes auto-advance even if we never see an
 *    "ended" signal, so a slideshow can never get stuck on one slide.
 */
(function ($) {
	'use strict'

	var PLAYERJS_CONTEXT = 'player.js'
	var PLAYERJS_VERSION = '0.0.11'
	var MAX_HOLD_MS = 3 * 60 * 1000

	var PLAYERJS_EVENTS = ['ready', 'play', 'pause', 'ended']

	var holds = [] // { $el, $metaslider, timer }
	var players = [] // { iframe, origin, listener, ready, queue }
	var advanceOnEnd = {} // slideshow element id -> whether its own Auto Play is on
	var loopMode = {} // slideshow element id -> its Loop setting ('', 'stopOnFirst', 'stopOnLast')
	var transitioned = {} // slideshow element id -> has it moved off the slide it loaded on

	/**
	 * Track one embed iframe's player.js state. Livid's own API docs confirm
	 * its embeds implement the player.js spec
	 * (https://support.livid.com/article/66-api ->
	 * https://github.com/embedly/player.js).
	 *
	 * @param {HTMLIFrameElement} iframe
	 * @return {Object|null}
	 */
	function playerFor(iframe) {
		if (!iframe) {
			return null
		}

		for (var i = 0; i < players.length; i++) {
			if (players[i].iframe === iframe) {
				return players[i]
			}
		}

		var player = {
			iframe: iframe,
			origin: iframeOrigin(iframe),
			// The reference client sends a listener id with every
			// registration and the receiver echoes it back on each event.
			listener: 'ms-livid-' + players.length + '-' + Date.now(),
			ready: false,
			queue: []
		}

		players.push(player)

		return player
	}

	/**
	 * @param {HTMLIFrameElement} iframe
	 * @return {string} The iframe's own origin, or "*" if it can't be read.
	 */
	function iframeOrigin(iframe) {
		try {
			return new URL(iframe.getAttribute('src'), window.location.href).origin
		} catch (e) {
			return '*'
		}
	}

	function post(player, data) {
		if (!player.iframe.contentWindow) {
			return
		}

		data.context = PLAYERJS_CONTEXT
		data.version = PLAYERJS_VERSION

		player.iframe.contentWindow.postMessage(JSON.stringify(data), player.origin)
	}

	/**
	 * Send a player.js command, holding it back until the embed has told us
	 * it's ready. Anything sent before the player has attached its own
	 * message listener is simply dropped on the floor, which is why commands
	 * need queueing rather than firing and hoping - the reference client
	 * queues for the same reason.
	 *
	 * @param {HTMLIFrameElement} iframe
	 * @param {string} method e.g. "play", "pause"
	 * @param {string} [value]
	 */
	function sendPlayerCommand(iframe, method, value) {
		var player = playerFor(iframe)
		if (!player) {
			return
		}

		var data = { method: method, listener: player.listener }
		if (undefined !== value) {
			data.value = value
		}

		if (!player.ready) {
			player.queue.push(data)
			return
		}

		post(player, data)
	}

	/**
	 * Subscribe to the events this file acts on. player.js only emits an
	 * event once the parent has explicitly registered for it, so this has to
	 * land before anything will ever be reported back. Registrations are sent
	 * unqueued (they're what gets us to "ready" in the first place) and
	 * repeated, since a registration that arrives before the embed is
	 * listening is lost rather than buffered.
	 *
	 * @param {HTMLIFrameElement} iframe
	 */
	function bindIframe(iframe) {
		var player = playerFor(iframe)
		if (!player || player.bound) {
			return
		}
		player.bound = true

		var register = function () {
			for (var i = 0; i < PLAYERJS_EVENTS.length; i++) {
				post(player, {
					method: 'addEventListener',
					value: PLAYERJS_EVENTS[i],
					listener: player.listener
				})
			}
		}

		$(iframe).on('load', register)
		register()
		setTimeout(register, 500)
		setTimeout(register, 1500)
	}

	/**
	 * The embed reported in - flush anything that was waiting on it.
	 *
	 * @param {Object} player
	 */
	function markPlayerReady(player) {
		if (player.ready) {
			return
		}

		player.ready = true

		// Mute is applied here, over the API, rather than in the embed URL: a
		// muted=true param locks the player muted for good, so the visitor
		// could never turn sound on. It also has to land before any queued
		// play, since browsers only allow unattended playback while muted.
		var $el = $(player.iframe).closest('.ms-livid-lazy-wrap, .ms-livid-eager')
		if ('1' === $el.attr('data-mute')) {
			post(player, { method: 'mute', listener: player.listener })
		}

		while (player.queue.length) {
			post(player, player.queue.shift())
		}
	}

	function playWrap($wrap) {
		var embed = $wrap.attr('data-embed')
		if (!embed) {
			return
		}

		$wrap.attr('data-playing', '1')
		$wrap.html(embed)

		var iframe = $wrap[0].querySelector('iframe')
		bindIframe(iframe)

		// The click is itself a play request. Sent as a command rather than an
		// autoplay URL param, so mute stays under the visitor's control - it
		// waits in the queue until the player reports ready.
		sendPlayerCommand(iframe, 'play')
	}

	/**
	 * Pause a video where it is via a real Player.js "pause" command, so
	 * position is preserved. The embed is never unloaded/reset as a
	 * fallback - if this particular embed doesn't honor the command, the
	 * video keeps playing rather than ever losing its position.
	 *
	 * @param {jQuery} $el
	 */
	function pauseVideo($el) {
		var iframe = $el[0].querySelector('iframe')
		if (!iframe) {
			return
		}

		sendPlayerCommand(iframe, 'pause')

		releaseAutoplayHold($el)
	}

	/**
	 * Only resumes playback on its own when the slide is marked
	 * data-autoplay-on-active (the Auto Play setting is on for it) -
	 * otherwise a paused video stays paused exactly as it was left, even
	 * once its slide becomes active again. Never autoplay when the setting
	 * is off.
	 *
	 * @param {jQuery} $el
	 */
	function resumeVideo($el) {
		if ('1' !== $el.attr('data-autoplay-on-active')) {
			return
		}

		var iframe = $el[0].querySelector('iframe')
		if (!iframe) {
			return
		}

		sendPlayerCommand(iframe, 'play')
	}

	/**
	 * Stop the slideshow advancing while this slide's video plays, so it gets
	 * watched to the end instead of being cut off mid-play.
	 *
	 * Called both from the PHP-injected after/start callbacks the moment we
	 * land on an auto-playing video slide (so the hold doesn't depend on the
	 * player reporting anything - MetaSlider Pro's Vimeo slide type calls
	 * flexslider('pause') in that same callback for exactly this reason) and
	 * from the Player.js "play" event, which also covers a visitor pressing
	 * play on the player's own controls.
	 *
	 * @param {jQuery} $el
	 */
	function holdAutoplayHold($el) {
		var $metaslider = $el.closest('[id^="metaslider_"]')
		var flexslider = $metaslider.data('flexslider')

		if (!flexslider) {
			return
		}

		releaseAutoplayHold($el)

		// The flag alone only stops it resuming - pause() stops the timer
		// that's already running, which is what actually keeps it on this
		// slide until the video is done.
		$metaslider.flexslider('pause')
		flexslider.manualPause = true
		flexslider.manualPlay = false

		// Safety net: if no "ended" ever arrives, stop holding the slideshow
		// so it can resume advancing on its own rather than sit forever.
		var timer = setTimeout(function () {
			releaseAutoplayHold($el)
			$metaslider.flexslider('play')
		}, MAX_HOLD_MS)

		holds.push({ $el: $el, $metaslider: $metaslider, timer: timer })
	}

	function releaseAutoplayHold($el) {
		for (var i = holds.length - 1; i >= 0; i--) {
			if (holds[i].$el.is($el)) {
				clearTimeout(holds[i].timer)

				var flexslider = holds[i].$metaslider.data('flexslider')
				if (flexslider) {
					flexslider.manualPause = false
					flexslider.manualPlay = false
				}

				holds.splice(i, 1)
			}
		}
	}

	/**
	 * Move on to the next slide once a video finishes - but only when the
	 * slideshow's own Auto Play is enabled (registered from PHP, see
	 * MetaLividSlide::flex_slider_parameters()), and never past the end of a
	 * slideshow that isn't set to loop. Same conditions MetaSlider Pro's
	 * YouTube slide type applies in its own onPlayerEnded handler.
	 *
	 * @param {jQuery} $el
	 */
	function advanceSlideshow($el) {
		var $metaslider = $el.closest('[id^="metaslider_"]')
		var id = $metaslider.attr('id')

		if (!id || !advanceOnEnd[id]) {
			return
		}

		var flexslider = $metaslider.data('flexslider')
		if (!flexslider) {
			return
		}

		// "stopOnLast" is just animationLoop:false as far as FlexSlider is
		// concerned, so it's readable straight off the instance.
		var lastSlideAndNoLoop = !flexslider.vars.animationLoop && flexslider.currentSlide === flexslider.count - 1

		// "stopOnFirst" has no FlexSlider option: MetaFlexSlider adds its own
		// "after" callback that pauses once slide 0 comes back around. Only
		// arriving there counts - a slideshow that loaded on slide 0 and hasn't
		// moved yet is on its first pass and should still advance, so this
		// waits for a transition rather than testing currentSlide alone.
		var firstSlideAndStopOnFirst = 'stopOnFirst' === loopMode[id] &&
			0 === flexslider.currentSlide &&
			transitioned[id]

		if (lastSlideAndNoLoop || firstSlideAndStopOnFirst) {
			return
		}

		flexslider.manualPause = false
		flexslider.manualPlay = false
		$metaslider.flexslider('next')
		$metaslider.flexslider('play')
	}

	function findPlayingElByWindow(win) {
		var found = null

		$('.ms-livid-lazy-wrap[data-playing="1"], .ms-livid-eager').each(function () {
			var iframe = this.querySelector('iframe')
			if (iframe && iframe.contentWindow === win) {
				found = $(this)
				return false
			}
		})

		return found
	}

	/**
	 * Is this slide the one currently shown? FlexSlider keeps every slide
	 * display:block, side by side in a horizontally-scrolling track clipped by
	 * overflow:hidden - it only ever toggles a "flex-active-slide" class, so
	 * jQuery's :visible (which just checks display/dimensions) reports every
	 * slide as visible and can't tell them apart. ResponsiveSlides.js marks
	 * its active <li> with a "{namespace}{n}_on" class instead (the instance
	 * number varies per slideshow on the page, so match the pattern, not one
	 * fixed class name).
	 *
	 * @param {jQuery} $el
	 * @return {boolean}
	 */
	function isSlideActive($el) {
		var $slide = $el.closest('li')

		if (!$slide.length) {
			// Can't tell - don't force-pause a video we can't place in the DOM.
			return true
		}

		if ($slide.hasClass('flex-active-slide')) {
			return true
		}

		var classes = ($slide.attr('class') || '').split(/\s+/)
		for (var i = 0; i < classes.length; i++) {
			if (/_on$/.test(classes[i])) {
				return true
			}
		}

		return false
	}

	// Exposed so MetaLividSlide::flex_slider_parameters() (metaslider_flex_slider_parameters
	// filter, PHP-injected into FlexSlider's own before/after/start callbacks -
	// the same architecture MetaSlider Pro's YouTube/Vimeo slide types use) can
	// drive pause/resume/autoplay for Flex slideshows without duplicating the
	// Player.js protocol handling inline as a raw string.
	window.metaslider = window.metaslider || {}
	window.metaslider.livid = {
		// PHP-injected code (see flex_slider_parameters()) passes a raw DOM
		// element from a plain jQuery .each() callback - accept either that
		// or an actual jQuery object.
		pause: function (el) {
			pauseVideo($(el))
		},
		resume: function (el) {
			resumeVideo($(el))
		},
		// Hold the slideshow on this slide while its video plays.
		hold: function (el) {
			holdAutoplayHold($(el))
		},
		triggerAutoplay: function (wrap) {
			var $wrap = $(wrap)
			if ('1' !== $wrap.attr('data-autoplay-triggered')) {
				$wrap.attr('data-autoplay-triggered', '1')
				$wrap.find('.ms-livid-play-button').trigger('click')
			}
		},
		// Whether this slideshow's own Auto Play is on, so a finished video
		// knows whether it should advance to the next slide, and its Loop
		// setting, which decides where advancing has to stop. Registered from
		// PHP, which is where the slideshow settings actually live.
		setAdvanceOnEnd: function (slideshowElementId, enabled, loop) {
			advanceOnEnd[slideshowElementId] = !!enabled
			loopMode[slideshowElementId] = loop || ''
		},
		// Called from the injected "after" callback, which only ever runs once
		// the slideshow has actually moved off the slide it loaded on.
		markTransitioned: function (slideshowElementId) {
			transitioned[slideshowElementId] = true
		}
	}

	function isFlexSlideshow($el) {
		return !!$el.closest('[id^="metaslider_"]').data('flexslider')
	}

	$(document).on('click', '.ms-livid-play-button', function (event) {
		event.preventDefault()

		var $wrap = $(this).closest('.ms-livid-lazy-wrap')

		// Fade the poster out before swapping in the embed, instead of an
		// instant cut, since the embed itself can take a moment to appear.
		$wrap.find('.ms-livid-lazy').fadeOut(200, function () {
			playWrap($wrap)
		})
	})

	// Eager (non-lazy) slides already have a real iframe on page load -
	// register for its Player.js events straight away.
	$(function () {
		$('.ms-livid-eager iframe').each(function () {
			bindIframe(this)
		})
	})

	// Flex slideshows get pause/resume/autoplay entirely from PHP-injected
	// before/after/start callbacks (see flex_slider_parameters()) - this poll
	// is the fallback for Responsive, which has no equivalent hook to inject
	// through (confirmed: MetaSlider Pro registers one for its own video
	// types, but nothing in Pro or this plugin ever actually applies that
	// filter, so it is unreachable there too - not a corner cut here).
	// Edge-triggered (only acts when active/inactive actually changes) -
	// calling pauseVideo() every tick would keep resetting its own
	// acknowledgment fallback timer, so it would never get a chance to fire.
	setInterval(function () {
		$('.ms-livid-lazy-wrap[data-autoplay-on-active="1"]:not([data-playing="1"])').each(function () {
			var $wrap = $(this)
			if (!isFlexSlideshow($wrap) && isSlideActive($wrap)) {
				window.metaslider.livid.triggerAutoplay($wrap)
			}
		})

		$('.ms-livid-lazy-wrap[data-playing="1"], .ms-livid-eager').each(function () {
			var $el = $(this)

			if (isFlexSlideshow($el)) {
				return
			}

			var active = isSlideActive($el)
			var wasActive = 'inactive' !== $el.attr('data-livid-slide-state')

			if (active === wasActive) {
				return
			}

			$el.attr('data-livid-slide-state', active ? 'active' : 'inactive')

			if (active) {
				resumeVideo($el)
			} else {
				pauseVideo($el)
			}
		})
	}, 400)

	window.addEventListener('message', function (event) {
		var data = event.data

		if ('string' === typeof data) {
			try {
				data = JSON.parse(data)
			} catch (e) {
				return
			}
		}

		if (!data || PLAYERJS_CONTEXT !== data.context || !data.event) {
			return
		}

		var $el = findPlayingElByWindow(event.source)
		if (!$el) {
			return
		}

		// Any player.js message at all proves the embed is listening, so
		// release anything queued for it - not just an explicit "ready".
		var player = playerFor($el[0].querySelector('iframe'))
		if (player) {
			markPlayerReady(player)
		}

		if ('play' === data.event) {
			holdAutoplayHold($el)
		} else if ('pause' === data.event) {
			releaseAutoplayHold($el)
		} else if ('ended' === data.event) {
			releaseAutoplayHold($el)
			advanceSlideshow($el)
		}
	})
})(window.metaslider_jQuery || jQuery)
