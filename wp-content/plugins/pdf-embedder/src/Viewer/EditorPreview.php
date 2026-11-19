<?php

namespace PDFEmbedder\Viewer;

use PDFEmbedder\Shortcodes\PdfEmbedder as Shortcode;

/**
 * Self-contained iframe page used by the Block Editor preview when Premium
 * is not active. Mirrors the Premium ?pdfemb-data= contract so the block JS
 * can build one URL regardless of plan.
 *
 * @since 5.0.0
 */
class EditorPreview {

	/**
	 * Register hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks(): void {

		add_action( 'template_redirect', [ $this, 'maybe_render' ], 0 );
	}

	/**
	 * Nonce action that guards the editor-preview route. The block editor
	 * builds an iframe URL with a nonce minted under this action; only the
	 * Block Editor surface (with `edit_posts`) can produce one.
	 *
	 * @since 5.0.0
	 */
	public const NONCE_ACTION = 'pdfemb-editor-preview';

	/**
	 * Detect the editor-preview URL and emit a self-contained PDF viewer page.
	 * No-op when Premium is active (Premium\Viewer\Viewer::load_viewer handles it).
	 *
	 * @since 5.0.0
	 */
	public function maybe_render(): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		if ( pdf_embedder()->is_premium() ) {
			return;
		}

		// Multilingual plugins (Polylang/WPML directory mode) can resolve the
		// admin's `home_url('/')` to a language-prefixed path where
		// `is_front_page()` is false on the iframe load; auth (cap + nonce,
		// below) is the real boundary.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['pdfemb-data'] ) ) {
			return;
		}

		// Editor-only route — require a Block Editor capability and a nonce
		// minted by the editor session. Anonymous / non-editor visitors get
		// the regular front-end response by falling through to WP routing.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$nonce = isset( $_GET['pdfemb-nonce'] ) ? sanitize_key( wp_unslash( (string) $_GET['pdfemb-nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$atts = $this->decode( sanitize_text_field( wp_unslash( (string) $_GET['pdfemb-data'] ) ) );

		if ( empty( $atts ) ) {
			echo '<!-- PDF Embedder: Invalid attributes. -->';

			exit;
		}

		$pdf_id = isset( $atts['pdfID'] ) ? (int) $atts['pdfID'] : 0;

		if ( $pdf_id <= 0 ) {
			return;
		}

		// `read_post` walks up to the parent post for attachments. Without
		// this gate, every editor (e.g. contributor) could trigger a render
		// of any PDF their nonce reaches — including PDFs attached to other
		// authors' draft/private posts.
		if ( ! current_user_can( 'read_post', $pdf_id ) ) {
			return;
		}

		// Re-derive the URL from the attachment ID server-side. Trusting the
		// payload's `url` (or `pdfemb-serveurl`) would let an editor bind one
		// pdfID with a `url` pointing at a different secure PDF —
		// Pro::add_viewer_strings would then mint a per-URL secureNonce for
		// that injected URL. Forcing the URL to match the verified pdfID
		// closes that smuggling route. Lite has no secure path itself, but
		// the same contract applies for parity with Premium.
		$true_url = wp_get_attachment_url( $pdf_id );

		if ( ! $true_url ) {
			return;
		}

		$atts['url'] = $true_url;

		unset( $atts['pdfemb-serveurl'], $atts['_editorPreview'] );

		// Lite registers `pdfemb_pdfjs` and `pdfemb_embed_pdf` (plus the
		// `pdfemb_trans` localization) on the `wp_enqueue_scripts` action,
		// which is fired by wp_head() and therefore runs *after* this
		// template_redirect handler. Register them explicitly so the
		// shortcode's enqueue_inline_assets() and our wp_print_scripts()
		// calls have something to print.
		pdf_embedder()->enqueue_scripts();

		// Run the same shortcode pipeline used on the front-end so the editor
		// preview matches what visitors will see.
		$html = ( new Shortcode() )->render( $atts );

		$this->emit_iframe_doc( $html );

		exit;
	}

	/**
	 * Decode the base64url-encoded JSON payload back into an attributes array.
	 * Matches the JS-side `btoa(<utf8-bytes>)` with `+/` swapped for `-_` and
	 * `=` padding stripped.
	 *
	 * @since 5.0.0
	 *
	 * @param string $encoded Base64url string from the query parameter.
	 */
	private function decode( string $encoded ): array {

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$json = base64_decode( strtr( $encoded, '-_', '+/' ), true );

		if ( $json === false || $json === '' ) {
			return [];
		}

		$atts = json_decode( $json, true );

		return is_array( $atts ) ? $atts : [];
	}

	/**
	 * Emit the standalone HTML document that wraps the shortcode output so the
	 * pdfemb_embed_pdf JS can attach PDF.js inside the iframe body.
	 *
	 * @since 5.0.0
	 *
	 * @param string $body_html HTML produced by the shortcode render pipeline.
	 */
	private function emit_iframe_doc( string $body_html ): void {

		nocache_headers();

		header( 'X-Robots-Tag: noindex, nofollow' );
		header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );

		?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex,nofollow">
	<title><?php esc_html_e( 'PDF Preview', 'pdf-embedder' ); ?></title>
	<style>
		/*
		 * `overflow: hidden` suppresses the iframe's own scrollbars. The
		 * viewer manages internal scrolling on .pdfemb-pagescontainer, and
		 * the outer ResizeObserver keeps the host iframe sized to the
		 * viewer's natural height, so the iframe-level scrollbar would
		 * only appear during sub-pixel render transitions. When it does,
		 * the lite viewer's checkForResize polling sees the inner viewport
		 * shrink by ~15px (scrollbar width), recomputes wantWidth from a
		 * narrower body.width(), and re-renders every prerendered page —
		 * repeatedly, because the next render fits and the scrollbar
		 * disappears, restoring the original width and flipping the cycle.
		 * Hiding the scrollbar at the document level breaks that loop.
		 */
		html, body { margin: 0; padding: 0; overflow: hidden; }
		/*
		 * `.pdfemb-viewer` carries a 1px black border (intended). With the
		 * default `box-sizing: content-box`, viewer-core.js setting
		 * `style.width: 384px` against a 384px iframe inner width renders
		 * the viewer at 386px, and the body's `overflow: hidden` (above)
		 * clips the right border — visually mismatching the front-end,
		 * which has the same border but no clipping ancestor. Switching to
		 * `border-box` in this iframe-only context absorbs the borders
		 * into the declared width so the rendered viewer matches the
		 * iframe inner width exactly. The canvas inside is still sized to
		 * the unadjusted `wantWidth`, so its right ~2px is clipped by the
		 * viewer's own `overflow: hidden` (~0.5% of page width — invisible).
		 */
		body.pdfemb-editor-preview .pdfemb-viewer {
			display: block;
			box-sizing: border-box;
			max-width: 100%;
		}
	</style>
	<?php
	wp_print_styles( [ 'pdfemb_embed_pdf_css' ] );
	wp_print_scripts( [ 'pdfemb_pdfjs', 'pdfemb_embed_pdf' ] );
	?>
</head>
<body class="pdfemb-editor-preview">
	<?php
	// Already escaped inside Viewer::render().
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $body_html;
	?>
	<script>
		// Auto-fit the host iframe to the rendered viewer's natural height so
		// `data-height="max"` matches the front-end behavior (Lite computes
		// height from page aspect ratio — see viewer-core.js resizeViewer).
		// Mirrors Premium's triggerIframeHeightChange.
		//
		// Feedback-loop guard: writing to `frame.style.height` changes the
		// iframe's outer size, which can make the Block Editor canvas show
		// or hide its vertical scrollbar. That toggles the iframe's inner
		// width by ~15px, the lite viewer's resizeViewer responds with a
		// proportional ~20px aspect-ratio change in body height, and every
		// page canvas re-renders. Without dampening the scrollbar
		// ping-pongs indefinitely. We debounce the observer, never write
		// the same height twice, and refuse to shrink by less than 32px
		// (comfortably above any scrollbar width) — genuine width-driven
		// height changes still pass.
		( function () {
			var frame = window.frameElement;
			if ( ! frame ) {
				return;
			}
			var lastSetHeight = -1;
			var debounceTimer = 0;
			var fit = function () {
				debounceTimer = 0;
				var node = document.querySelector( '.pdfemb-viewer' );
				if ( ! node ) {
					return;
				}
				var h = Math.ceil( node.getBoundingClientRect().height );
				if ( h <= 0 || h === lastSetHeight ) {
					return;
				}
				if ( lastSetHeight > 0 && h < lastSetHeight && lastSetHeight - h < 32 ) {
					return;
				}
				lastSetHeight = h;
				frame.style.height = h + 'px';
			};
			var schedule = function () {
				if ( debounceTimer ) {
					return;
				}
				debounceTimer = setTimeout( fit, 100 );
			};
			// PDF.js renders asynchronously, so observe the body for size
			// changes and re-fit until everything settles.
			new ResizeObserver( schedule ).observe( document.body );
		} )();
	</script>
</body>
</html>
		<?php
	}
}
