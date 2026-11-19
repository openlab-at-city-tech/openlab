<?php

namespace Imagely\NGG\Display;

/**
 * Ships a gallery's styles and scripts along with its markup when a page builder renders it over AJAX.
 *
 * Elementor renders a widget that was just added or edited through an `elementor_ajax` request and injects the
 * returned HTML into its already-loaded preview iframe. The display type's assets are only enqueued during that
 * request, and nothing prints them, so the preview shows the gallery unstyled (Thumbnails) or empty (Slideshow)
 * until the editor reloads. GitHub #1175.
 *
 * For such a request, render() returns one inline script that adds whatever the iframe is still missing: every
 * stylesheet and script is printed by WordPress as usual, keyed by its `<handle>-css` / `<handle>-js` id, and a
 * handle whose tag the iframe already has is skipped, so jQuery or ngg_common are never loaded a second time. For
 * the handles a gallery enqueued itself, the inline data and CSS still run, which is how window.galleries learns
 * about the new gallery. Once everything has loaded it fires the same `refreshed` / `nextgen_page_refreshed`
 * events as AJAX pagination, so lightboxes and display types bind to the new markup.
 *
 * Other builders that inject server-rendered HTML the same way can opt in through the `ngg_inline_gallery_assets`
 * filter.
 */
class PreviewAssets {

	/**
	 * Style and script handles NextGEN enqueued for the galleries rendered so far in this request.
	 *
	 * Kept for the whole request, so a second gallery in the same widget still carries the shared handles (ngg_common
	 * and its data) the first one enqueued.
	 *
	 * @var array{styles: string[], scripts: string[]}
	 */
	private static $handles = [
		'styles'  => [],
		'scripts' => [],
	];

	/**
	 * Whether the gallery being rendered must carry its own assets.
	 *
	 * @return bool
	 */
	public static function is_needed() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- only identifies the request type; Elementor verifies its own nonce.
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';

		/**
		 * Filters whether a rendered gallery prints its own styles and scripts after its markup.
		 *
		 * Use it for a page builder that renders galleries over AJAX and injects the HTML into a page that is
		 * already loaded. The markup must be inserted with jQuery (or anything else that runs inline scripts).
		 *
		 * @param bool $needed True for Elementor's editor AJAX render.
		 */
		return (bool) apply_filters( 'ngg_inline_gallery_assets', wp_doing_ajax() && 'elementor_ajax' === $action );
	}

	/**
	 * Returns the current style and script queues, to compare against once the gallery is rendered.
	 *
	 * @return array{styles: string[], scripts: string[]}
	 */
	public static function snapshot() {
		return [
			'styles'  => wp_styles()->queue,
			'scripts' => wp_scripts()->queue,
		];
	}

	/**
	 * Returns the inline loader for the handles enqueued since $before.
	 *
	 * @param array{styles: string[], scripts: string[]} $before Queues returned by snapshot() before rendering.
	 * @return string
	 */
	public static function render( $before ) {
		self::$handles['styles']  = array_values( array_unique( array_merge( self::$handles['styles'], array_diff( wp_styles()->queue, $before['styles'] ) ) ) );
		self::$handles['scripts'] = array_values( array_unique( array_merge( self::$handles['scripts'], array_diff( wp_scripts()->queue, $before['scripts'] ) ) ) );

		$assets = array_merge(
			self::get_assets( wp_styles(), self::$handles['styles'], 'css' ),
			self::get_assets( wp_scripts(), self::$handles['scripts'], 'js' )
		);

		if ( empty( $assets ) ) {
			return '';
		}

		$data = wp_json_encode( $assets, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );

		return '<script>(' . self::get_loader() . ')(' . $data . ');</script>';
	}

	/**
	 * Prints each handle, and the dependencies it needs, the way WordPress prints it on a page.
	 *
	 * @param \WP_Dependencies $dependencies wp_styles() or wp_scripts().
	 * @param string[]         $handles      Handles NextGEN enqueued.
	 * @param string           $suffix       'css' or 'js', the suffix WordPress gives the tag's id.
	 * @return array<int, array{id: string, own: bool, html: string}>
	 */
	private static function get_assets( $dependencies, $handles, $suffix ) {
		if ( empty( $handles ) ) {
			return [];
		}

		// Resolve the dependency order without disturbing what WordPress itself still has to print.
		$to_do = $dependencies->to_do;
		$done  = $dependencies->done;

		$dependencies->to_do = [];
		$dependencies->done  = [];
		$dependencies->all_deps( $handles );
		$ordered = $dependencies->to_do;

		$assets = [];
		foreach ( $ordered as $handle ) {
			ob_start();
			$dependencies->do_item( $handle );
			$html = trim( ob_get_clean() );

			if ( '' !== $html ) {
				$assets[] = [
					'id'   => $handle . '-' . $suffix,
					// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					'own'  => in_array( $handle, $handles ),
					'html' => $html,
				];
			}
		}

		$dependencies->to_do = $to_do;
		$dependencies->done  = $done;

		return $assets;
	}

	/**
	 * The script that adds the assets to the page the markup was injected into.
	 *
	 * @return string
	 */
	private static function get_loader() {
		return <<<'JS'
function (assets) {
	var doc = document;
	var queue = [];
	var galleries = window.galleries;

	assets.forEach(function (asset) {
		var present = !!doc.getElementById(asset.id);
		// A dependency the page already has is left alone; for NextGEN's own handles only the tag is skipped.
		if (present && !asset.own) {
			return;
		}
		var box = doc.createElement('div');
		box.innerHTML = asset.html;
		Array.prototype.forEach.call(box.children, function (node) {
			if (!(present && node.id === asset.id)) {
				queue.push(node);
			}
		});
	});

	// ngg_common's data starts with "var galleries = {};", which would drop the galleries already on the page.
	var keepGalleries = function () {
		if (galleries && window.galleries !== galleries) {
			for (var key in window.galleries) {
				galleries[key] = window.galleries[key];
			}
			window.galleries = galleries;
		}
	};

	var next = function () {
		var node = queue.shift();
		if (!node) {
			if (window.jQuery) {
				window.jQuery(doc).trigger('refreshed');
			}
			doc.dispatchEvent(new Event('nextgen_page_refreshed'));
			return;
		}
		if ('SCRIPT' !== node.nodeName) {
			doc.head.appendChild(node);
			return next();
		}
		var script = doc.createElement('script');
		Array.prototype.forEach.call(node.attributes, function (attribute) {
			script.setAttribute(attribute.name, attribute.value);
		});
		if (node.src) {
			// Wait for it: the scripts after it may depend on it.
			script.onload = script.onerror = next;
			doc.head.appendChild(script);
			return;
		}
		script.text = node.text;
		doc.head.appendChild(script);
		keepGalleries();
		next();
	};

	next();
}
JS;
	}
}
