<?php
/**
 * Reusable conditional-visibility helper for admin forms.
 *
 * The WP Settings API has no equivalent of Drupal's `#states` — fields
 * are rendered unconditionally and any "show only when X" UX is on us.
 * This helper centralizes that pattern in two flavors, both gated by
 * named-input value matches and toggled by one script printed once per
 * page:
 *
 *   - Row markers (`print_marker()`): a hidden span placed inside a
 *     field's cell. The script walks up to the enclosing <tr> and
 *     toggles its display. Use when each conditional field is its own
 *     `add_settings_field()` row (or hand-rolled <tr>) and the entire
 *     row should appear or disappear as a unit.
 *
 *   - Block wrappers (`open_block()` / `close_block()`): a <div> the
 *     caller controls, which carries the data attributes itself and is
 *     toggled directly. Use when the conditional content lives inside
 *     another field's <td> — there's no separate row to hide, only the
 *     inline sub-control.
 *
 * Degrades safely with JS off — everything stays visible, server-side
 * validators are the trust boundary.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Form;

defined( 'ABSPATH' ) || exit;

/**
 * Conditional-visibility helper for admin form tables and inline blocks.
 */
final class ConditionalFields {

	/**
	 * Class applied to each row-level marker element. Stable contract —
	 * the toggle script's selector and any caller-side CSS rely on it.
	 */
	const MARKER_CLASS = 'ed11y-conditional-marker';

	/**
	 * Class applied to each inline block wrapper. Stable contract — the
	 * toggle script's selector and any caller-side CSS rely on it.
	 */
	const BLOCK_CLASS = 'ed11y-conditional-block';

	/**
	 * Print a hidden marker that ties an enclosing <tr>'s visibility to
	 * a named input's current value.
	 *
	 * Place inside the conditional field's <th> or <td> cell. The toggle
	 * script walks up to the closest <tr>; if no <tr> ancestor exists,
	 * the marker is silently ignored.
	 *
	 * Works for text inputs, selects, single checkboxes, and radio
	 * groups (radios share `name`; the script reads the checked one's
	 * value). For comparing against a checkbox's "off" state, use
	 * an empty string for $when_value.
	 *
	 * @param string $input_name Exact `name` attribute of the input
	 *                            whose value gates this row.
	 * @param string $when_value Show the row when the input's current
	 *                            value equals this string.
	 */
	public static function print_marker( string $input_name, string $when_value ): void {
		printf(
			'<span class="%s" hidden data-when-input="%s" data-when-value="%s"></span>',
			esc_attr( self::MARKER_CLASS ),
			esc_attr( $input_name ),
			esc_attr( $when_value )
		);
	}

	/**
	 * Open a conditional block whose own visibility is gated by a named
	 * input. Use for sub-controls rendered inline within a parent
	 * field's cell (no separate row to hide).
	 *
	 * The opening <div> carries the data attributes the toggle script
	 * reads; pair every call with `close_block()`.
	 *
	 * Works for the same input shapes as `print_marker()`: text inputs,
	 * selects, single checkboxes, and radio groups.
	 *
	 * @param string $input_name    Exact `name` attribute of the input
	 *                               whose value gates this block.
	 * @param string $when_value    Show the block when the input's
	 *                               current value equals this string.
	 * @param string $extra_classes Optional additional class names
	 *                               appended to the wrapper div, for
	 *                               caller-side styling. Whitespace-
	 *                               separated, no leading/trailing
	 *                               space required.
	 */
	public static function open_block( string $input_name, string $when_value, string $extra_classes = '' ): void {
		$classes = self::BLOCK_CLASS;
		if ( '' !== $extra_classes ) {
			$classes .= ' ' . $extra_classes;
		}
		printf(
			'<div class="%s" data-when-input="%s" data-when-value="%s">',
			esc_attr( $classes ),
			esc_attr( $input_name ),
			esc_attr( $when_value )
		);
	}

	/** Close a conditional block opened with `open_block()`. */
	public static function close_block(): void {
		echo '</div>';
	}

	/**
	 * Print the toggle script. Inert if the page contains neither
	 * markers nor blocks.
	 *
	 * Call once per admin form. Safe to call on pages with no
	 * conditional fields — the script's selectors return nothing and
	 * the IIFE exits.
	 */
	public static function print_script(): void {
		?>
		<script>
		(function () {
			'use strict';
			var rules = [];
			document.querySelectorAll(
				'.<?php echo esc_js( self::MARKER_CLASS ); ?>[data-when-input][data-when-value]'
			).forEach( function ( marker ) {
				var row = marker.closest( 'tr' );
				if ( ! row ) {
					return;
				}
				rules.push( {
					target: row,
					name:   marker.getAttribute( 'data-when-input' ),
					value:  marker.getAttribute( 'data-when-value' ),
				} );
			} );
			document.querySelectorAll(
				'.<?php echo esc_js( self::BLOCK_CLASS ); ?>[data-when-input][data-when-value]'
			).forEach( function ( block ) {
				rules.push( {
					target: block,
					name:   block.getAttribute( 'data-when-input' ),
					value:  block.getAttribute( 'data-when-value' ),
				} );
			} );
			if ( ! rules.length ) {
				return;
			}
			rules = rules.filter( function ( rule ) {
				// Backslash-escape any embedded double quote so the
				// attribute selector closes on the right boundary.
				rule.inputs = document.querySelectorAll(
					'[name="' + rule.name.replace( /"/g, '\\"' ) + '"]'
				);
				return rule.inputs.length > 0;
			} );
			if ( ! rules.length ) {
				return;
			}
			var currentValue = function ( inputs ) {
				var first = inputs[ 0 ];
				if ( 'radio' === first.type ) {
					for ( var i = 0; i < inputs.length; i++ ) {
						if ( inputs[ i ].checked ) {
							return inputs[ i ].value;
						}
					}
					return '';
				}
				if ( 'checkbox' === first.type ) {
					return first.checked ? first.value : '';
				}
				return first.value;
			};
			var sync = function () {
				rules.forEach( function ( rule ) {
					rule.target.style.display = ( currentValue( rule.inputs ) === rule.value ) ? '' : 'none';
				} );
			};
			rules.forEach( function ( rule ) {
				rule.inputs.forEach( function ( input ) {
					input.addEventListener( 'change', sync );
				} );
			} );
			sync();
		})();
		</script>
		<?php
	}
}
