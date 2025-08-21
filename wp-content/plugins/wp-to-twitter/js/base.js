(function ($) {
	let post_this = document.querySelectorAll( 'input[name=_wpt_post_this]' );
	let wrapper   = document.querySelector( '.wpt-options-metabox' );

	post_this.forEach( (el) => { 
		if ( el && el.checked && el.value === 'no' ) {
			wrapper.style.display = 'none';
		}
		el.addEventListener( 'change', function() {
			if ( el.checked && el.value == 'yes' ) {
				wrapper.style.display = 'grid';
			} else {
				wrapper.style.display = 'none';
			}
		});
	});

	let add_image = document.querySelectorAll( 'input[name=_wpt_image]' );
	let image_holder = document.querySelector( '.wpt_custom_image' );

	add_image.forEach( (el) => { 
		if ( el && el.checked && el.value === '1' ) {
			image_holder.style.display = 'none';
		}
		el.addEventListener( 'change', function() {
			if ( el.checked && el.value == '0' ) {
				image_holder.style.display = 'block';
			} else {
				image_holder.style.display = 'none';
			}
		});
	});
	$('#wpt_custom_tweet, #wpt_custom_update, #wpt_retweet_0, #wpt_retweet_1, #wpt_retweet_3').charCount({
		allowed: wptSettings.allowed,
		x_limit: wptSettings.x_limit,
		mastodon_limit: wptSettings.mastodon_limit,
		bluesky_limit: wptSettings.bluesky_limit,
		counterText: wptSettings.text
	});

	const variants = $( '.service-selection-variants .service-selector input' );
	let status_update = $( '#wpt_custom_tweet' ).val();
	if ( '' === status_update ) {
		status_update = $( 'pre.wpt-template' ).text();
	}
	variants.each( function() {
		$( this ).on( 'change', function() {
			let status = $( this ).is( ':checked' );
			let val    = $( this ).val();
			if ( true === status ) {
				$( '#wpt_custom_tweet_' + val ).parent( 'p' ).removeClass( 'hidden' );
				$(  '#wpt_custom_tweet_' + val ).val( status_update );
			} else {
				$( '#wpt_custom_tweet_' + val ).parent( 'p' ).addClass( 'hidden' );
			}
		});
		
	});

	// debugging
	$( 'button.toggle-debug' ).on( 'click', function() {
		var next = $( this ).next( 'pre' );
		if ( next.is( ':visible' ) ) {
			next.hide();
			$( this ).attr( 'aria-expanded', 'false' );
		} else {
			next.show();
			$( this ).attr( 'aria-expanded', 'true' );
		}
	});
	// tweet history log
	$('#wp2t .history').hide();
	$('#wp2t .history-toggle').on('click', function (e) {
		let dashicon = $( '#wp2t .history-toggle span ');
		if ( $( '#wp2t .history' ).is( ':visible' ) ) {
			dashicon.addClass( 'dashicons-plus' );
			dashicon.removeClass( 'dashicons-minus' );
			dashicon.parent( 'button' ).attr( 'aria-expanded', 'false' );
		} else {
			dashicon.removeClass( 'dashicons-plus' );
			dashicon.addClass( 'dashicons-minus' );
			dashicon.parent( 'button' ).attr( 'aria-expanded', 'true' );
		}
		$('#wp2t .history').toggle( 300 );
	});

	const templateTags = document.querySelectorAll( '#wp2t .inline-list button' );
	let   custom       = document.getElementById( 'wpt_custom_tweet' );
	let   template     = document.querySelector( '#wp2t pre.wpt-template' );
	let   customText   = ( null !== custom ) ? custom.value : '';
	let   templateText = ( null !== template ) ? template.innerText : '';
	templateTags.forEach((el) => {
		el.addEventListener( 'click', function(e) {
			customText   = ( null !== custom ) ? custom.value : '';
			let pressed  = el.getAttribute( 'aria-pressed' );
			let tag      = el.innerText;
			templateText = ( customText ) ? customText : templateText;
			if ( 'true' === pressed ) {
				let newText  = templateText.replace( tag, '' ).trim();
				templateText = newText;
				custom.value = newText;
				el.setAttribute( 'aria-pressed', 'false' );
			} else {
				templateText = templateText + ' ' + tag;
				custom.value = templateText;
				el.setAttribute( 'aria-pressed', 'true' );			
			}
			wp.a11y.speak( wptSettings.updated );
		});
	});
}(jQuery));