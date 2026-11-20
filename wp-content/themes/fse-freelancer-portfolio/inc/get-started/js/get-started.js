/* global fseFreelancerPortfolioGetStarted */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		initCountdown();
		initCopyButtons();
	} );

	function getSettings() {
		return ( typeof fseFreelancerPortfolioGetStarted !== 'undefined' ) ? fseFreelancerPortfolioGetStarted : {};
	}

	function initCountdown() {
		var wrap = document.querySelector( '[data-countdown]' );
		if ( ! wrap ) {
			return;
		}

		var daysEl    = wrap.querySelector( '[data-countdown-days]' );
		var hoursEl   = wrap.querySelector( '[data-countdown-hours]' );
		var minutesEl = wrap.querySelector( '[data-countdown-minutes]' );
		var secondsEl = wrap.querySelector( '[data-countdown-seconds]' );

		var settings = getSettings();
		var minHours = parseInt( settings.countdownMinHours, 10 );
		var maxHours = parseInt( settings.countdownMaxHours, 10 );
		if ( ! minHours || minHours <= 0 ) {
			minHours = 48;
		}
		if ( ! maxHours || maxHours < minHours ) {
			maxHours = 72;
		}

		function randomWindowMs() {
			var hours = minHours + Math.random() * ( maxHours - minHours );
			return Math.round( hours * 60 * 60 * 1000 );
		}

		var deadline = Date.now() + randomWindowMs();

		function pad( value ) {
			return String( value ).padStart( 2, '0' );
		}

		function tick() {
			var remaining = deadline - Date.now();

			if ( remaining <= 0 ) {
				deadline = Date.now() + randomWindowMs();
				remaining = deadline - Date.now();
			}

			var totalSeconds = Math.floor( remaining / 1000 );
			var days    = Math.floor( totalSeconds / 86400 );
			var hrs     = Math.floor( ( totalSeconds % 86400 ) / 3600 );
			var mins    = Math.floor( ( totalSeconds % 3600 ) / 60 );
			var secs    = totalSeconds % 60;

			if ( daysEl ) daysEl.textContent = pad( days );
			if ( hoursEl ) hoursEl.textContent = pad( hrs );
			if ( minutesEl ) minutesEl.textContent = pad( mins );
			if ( secondsEl ) secondsEl.textContent = pad( secs );
		}

		tick();
		setInterval( tick, 1000 );
	}

	function initCopyButtons() {
		var buttons = document.querySelectorAll( '.pm-copy-btn' );
		if ( ! buttons.length ) {
			return;
		}

		buttons.forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var text = button.getAttribute( 'data-copy-text' ) || '';
				if ( ! text ) {
					return;
				}
				copyText( text ).then( function () {
					flashCopied( button );
				} );
			} );
		} );
	}

	function copyText( text ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			return navigator.clipboard.writeText( text ).catch( function () {
				legacyCopy( text );
			} );
		}
		legacyCopy( text );
		return Promise.resolve();
	}

	function legacyCopy( text ) {
		var textarea = document.createElement( 'textarea' );
		textarea.value = text;
		textarea.setAttribute( 'readonly', '' );
		textarea.style.position = 'absolute';
		textarea.style.left = '-9999px';
		document.body.appendChild( textarea );
		textarea.select();
		try {
			document.execCommand( 'copy' );
		} catch ( e ) {
			// Clipboard access unavailable; nothing further to do.
		}
		document.body.removeChild( textarea );
	}

	function flashCopied( button ) {
		var icon = button.querySelector( '.dashicons' );
		if ( ! icon ) {
			return;
		}
		icon.classList.remove( 'dashicons-clipboard' );
		icon.classList.add( 'dashicons-yes-alt' );
		setTimeout( function () {
			icon.classList.remove( 'dashicons-yes-alt' );
			icon.classList.add( 'dashicons-clipboard' );
		}, 1500 );
	}
} )();
