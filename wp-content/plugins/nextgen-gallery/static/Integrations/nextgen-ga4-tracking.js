/**
 * NextGEN Gallery — Google Analytics 4 (GA4) Tracking
 *
 * Sends the following events to GA4 when gtag.js is present on the page
 * (e.g. loaded via MonsterInsights, Site Kit, or a manual snippet).
 * The Measurement ID and per-event toggles come from window.ngg_ga4_config,
 * injected via wp_localize_script.
 *
 *   gallery_view   — gallery_id, gallery_name
 *   image_click    — image_id, image_name, gallery_id, gallery_name
 *   image_download — image_id, image_name, gallery_id, gallery_name
 *   add_to_cart    — currency, value, items[]
 *   purchase       — transaction_id, value, currency, items[]
 */
( function () {
	'use strict';

	var config = window.ngg_ga4_config;
	if ( ! config || ! config.measurement_id ) {
		return;
	}
	if ( typeof gtag !== 'function' ) {
		return;
	}

	var mid = config.measurement_id;
	var track = config.track || {};

	// Register the plugin's Measurement ID with gtag so events sent via send_to are accepted,
	// even when the page loads gtag.js under a different ID. send_page_view: false prevents
	// a duplicate page_view event alongside any already configured by the host integration.
	gtag( 'config', mid, { send_page_view: false } );

	var sendEvent = function ( eventName, params ) {
		params = params || {};
		params.send_to = params.send_to || mid;
		gtag( 'event', eventName, params );
	};

	// All gallery wrapper selectors across Lite and Pro display types.
	var gallerySelectors =
		'.ngg-galleryoverview, .ngg-imagebrowser, .ngg-slideshow, ' +
		'[data-nextgen-gallery-id], [data-gallery-id], [data-ngg-pro-mosaic-id], ' +
		'.ngg-pro-mosaic-container, .ngg-pro-masonry, .ngg-pro-masonry-wrapper, ' +
		'.nextgen_pro_thumbnail_grid, .ngg-galleria-parent, .ngg-pro-album, ' +
		'.nextgen_pro_film, .nextgen_pro_sidescroll, .tiled-gallery, .nextgen_pro_blog_gallery';

	// Narrower selector used only to locate the gallery container that carries the GA4 data
	// attributes. Scoped to attribute presence so closest() always resolves to the element
	// that actually holds gallery_id/gallery_name, skipping inner wrappers that share a
	// class name but carry no data (e.g. .ngg-pro-masonry inside .ngg-pro-masonry-wrapper).
	var galleryDataSelectors = '[data-nextgen-gallery-id], [data-gallery-id], [data-ngg-pro-mosaic-id]';

	function getGalleryInfo( el ) {
		var container = el && el.closest ? el.closest( galleryDataSelectors ) : null;
		if ( ! container ) {
			return { gallery_id: 0, gallery_name: '' };
		}
		var gidRaw =
			container.getAttribute( 'data-gallery-id' ) ||
			container.getAttribute( 'data-ngg-pro-mosaic-id' ) ||
			container.getAttribute( 'data-nextgen-gallery-id' ) ||
			'';
		// data-gallery-id is read first because data-nextgen-gallery-id can hold a displayed-gallery
		// transient hash used by NggPaginatedGallery for AJAX pagination; parseInt() on a hash returns 0.
		var gid = parseInt( gidRaw, 10 ) || 0;
		var gname = container.getAttribute( 'data-gallery-name' ) || '';
		return { gallery_id: gid, gallery_name: gname };
	}

	function getImageName( link ) {
		return (
			link.getAttribute( 'data-image-name' ) ||
			link.getAttribute( 'data-title' ) ||
			( ( link.getAttribute( 'href' ) || '' ).split( '/' ).pop() || '' ).split( '?' )[ 0 ] ||
			''
		);
	}

	// gallery_view — fires once per unique gallery when it first enters the viewport.
	if ( track.gallery_view ) {
		var observedGalleries = {};

		if ( 'IntersectionObserver' in window ) {
			var galleryObserver = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( ! entry.isIntersecting ) {
							return;
						}
						var el = entry.target;
						var gidRaw =
							el.getAttribute( 'data-nextgen-gallery-id' ) ||
							el.getAttribute( 'data-gallery-id' ) ||
							el.getAttribute( 'data-ngg-pro-mosaic-id' ) ||
							'';
						// Use the integer gallery ID as the deduplication key; skip if none resolved.
						var gid = parseInt( gidRaw, 10 ) || 0;
						if ( ! gid || observedGalleries[ gid ] ) {
							return;
						}
					observedGalleries[ gid ] = true;
					galleryObserver.unobserve( el );
					var gname = el.getAttribute( 'data-gallery-name' ) || '';
					sendEvent( 'gallery_view', {
						gallery_id: gid,
						gallery_name: gname
					});
					});
				},
				{ rootMargin: '50px', threshold: 0.1 }
			);

			function observeGalleries() {
				var nodes = document.querySelectorAll( gallerySelectors );
				nodes.forEach( function ( node ) {
					var gidRaw =
						node.getAttribute( 'data-nextgen-gallery-id' ) ||
						node.getAttribute( 'data-gallery-id' ) ||
						node.getAttribute( 'data-ngg-pro-mosaic-id' ) ||
						'';
					var gid = parseInt( gidRaw, 10 ) || 0;
					if ( gid && ! observedGalleries[ gid ] ) {
						galleryObserver.observe( node );
					}
				});
			}

			if ( document.readyState === 'loading' ) {
				document.addEventListener( 'DOMContentLoaded', observeGalleries );
			} else {
				observeGalleries();
			}
			document.addEventListener( 'nextgen_page_refreshed', observeGalleries );
		}
	}

	// image_click — fires when a gallery image link is clicked (e.g. to open the lightbox).
	// Detected via data-image-id on the anchor, which all Lite and Pro display types set
	// on every image link, regardless of display type or layout.
	if ( track.image_click ) {
		document.addEventListener(
			'click',
			function ( e ) {
			var link = e.target.closest ? e.target.closest( 'a' ) : null;
			if ( ! link ) {
				return;
			}
			if ( ! link.closest( gallerySelectors ) ) {
				return;
			}
			// All gallery image links carry data-image-id; skip anything that doesn't.
			var imageIdRaw = link.getAttribute( 'data-image-id' );
			if ( ! imageIdRaw ) {
				return;
			}
			// Skip download links — those are handled by the image_download listener.
			// File extension is intentionally not checked: lightbox thumbnail hrefs point to image
			// files directly (.jpg etc.) and would produce false positives if matched by extension.
			if ( link.hasAttribute( 'download' ) || link.getAttribute( 'data-ngg-download' ) === 'true' ) {
				return;
			}
			var info = getGalleryInfo( link );
			sendEvent( 'image_click', {
				image_id: parseInt( imageIdRaw, 10 ) || 0,
				image_name: getImageName( link ),
				gallery_id: info.gallery_id,
				gallery_name: info.gallery_name
			});
			},
			true
		);
	}

	// image_download — fires when a download link is clicked.
	// Links tagged with data-ngg-download="true" (e.g. Digital Downloads page after purchase)
	// are tracked regardless of gallery context. Links that carry only the HTML download
	// attribute must be inside a gallery container to avoid capturing unrelated site downloads.
	if ( track.image_download ) {
		document.addEventListener(
			'click',
			function ( e ) {
			var link = e.target.closest ? e.target.closest( 'a' ) : null;
			if ( ! link ) {
				return;
			}
			var isNggDownload = link.getAttribute( 'data-ngg-download' ) === 'true';
			var hasDownloadAttr = link.hasAttribute( 'download' );
			if ( ! isNggDownload && ! hasDownloadAttr ) {
				return;
			}
			// NGG-tagged links are allowed outside gallery containers; all others must be inside one.
			if ( ! isNggDownload && ! link.closest( gallerySelectors ) ) {
				return;
			}
			var info = getGalleryInfo( link );
			var href = link.getAttribute( 'href' ) || '';
			// Resolve the filename: data-image-name → data-title → HTML download attr → URL segment.
			var fileName =
				link.getAttribute( 'data-image-name' ) ||
				link.getAttribute( 'data-title' ) ||
				link.getAttribute( 'download' ) ||
				( href.split( '/' ).pop() || '' ).split( '?' )[ 0 ];
			sendEvent( 'image_download', {
				image_id:    parseInt( link.getAttribute( 'data-image-id' ) || '0', 10 ) || 0,
				image_name:  fileName,
				gallery_id:   info.gallery_id,
				gallery_name: info.gallery_name
			});
			},
			true
		);
	}

	// add_to_cart — fired via a custom DOM event dispatched by Cart.js when an item is added.
	if ( track.add_to_cart ) {
		document.addEventListener( 'ngg_ga4_add_to_cart', function ( e ) {
			if ( e.detail ) {
				sendEvent( 'add_to_cart', e.detail );
			}
		});
	}

	// purchase — the Thanks page outputs window.ngg_ga4_purchase_data inline in the body
	// before this footer script runs. Reading it at init time guarantees the event fires
	// even when the page is navigated away from immediately after load.
	if ( track.purchase ) {
		if ( window.ngg_ga4_purchase_data ) {
			sendEvent( 'purchase', window.ngg_ga4_purchase_data );
			window.ngg_ga4_purchase_data = null;
		}
	}
})();
