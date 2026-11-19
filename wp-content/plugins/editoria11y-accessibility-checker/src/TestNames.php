<?php
/**
 * Static catalog of editoria11y test metadata.
 *
 * Pure data — no DB, no runtime state, no side effects. Each method returns
 * a translated lookup table keyed by the test result key (e.g. 'HEADING_EMPTY',
 * 'LINK_STOPWORD'). Mirrors Drupal\editoria11y\TestNames so per-test labels,
 * default-on/off lists, and category groupings stay parallel between the two
 * codebases — the same test keys flow from the bundled JS library through
 * either backend.
 *
 * Adding a new dictionary: add a new public static method here, then call it
 * from wherever it's needed. Translators see all editoria11y test strings in
 * one file in the .pot, which keeps the translation queue coherent.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TestNames
 *
 * Mirrors Drupal\editoria11y\TestNames. Method names follow WordPress
 * snake_case convention; data values match Drupal's byte-for-byte so result
 * keys, group identifiers, and form-section assignments line up exactly.
 */
class TestNames {

	/**
	 * Human-readable labels for each test result key.
	 *
	 * Used by the settings UI, the dashboard, and the CSV export to render a
	 * test's name in the user's language. Keys must match the `result_key`
	 * values the bundled JS library writes to the database (UPPER_SNAKE_CASE
	 * starting in v3 of the library).
	 *
	 * @return array<string, string> Map of result key → translated label.
	 */
	public static function core_names(): array {
		return array(
			'ALT_FILE_EXT'                     => __( 'This alt text is a filename, not a description', 'editoria11y' ),
			'ALT_MAYBE_BAD'                    => __( 'Is this a clear and concise description of the image?', 'editoria11y' ),
			'ALT_MAYBE_BAD_WARNING'            => __( 'Is this a clear and concise description of the image?', 'editoria11y' ),
			'ALT_PLACEHOLDER'                  => __( 'This alt text may be a placeholder', 'editoria11y' ),
			'ALT_UNPRONOUNCEABLE'              => __( 'This alt text is unpronounceable', 'editoria11y' ),
			'ARIA_INPUT_FIELD_NAME'            => __( 'This custom input field is missing a label', 'editoria11y' ),
			'BTN_EMPTY'                        => __( 'Button is missing an accessible label', 'editoria11y' ),
			'BTN_EMPTY_LABELLEDBY'             => __( 'Button has an invalid ARIA label', 'editoria11y' ),
			'BTN_ROLE_IN_NAME'                 => __( 'Button name repeats the word "button"', 'editoria11y' ),
			'BTN_UNPRONOUNCEABLE'              => __( 'This button is unpronounceable', 'editoria11y' ),
			'CONTRAST_ERROR'                   => __( 'Text does not have enough contrast to be easily legible', 'editoria11y' ),
			'CONTRAST_ERROR_GRAPHIC'           => __( 'Graphic or icon does not have enough contrast', 'editoria11y' ),
			'CONTRAST_INPUT'                   => __( 'Input does not provide enough contrast to be easily legible', 'editoria11y' ),
			'CONTRAST_PLACEHOLDER'             => __( 'Placeholder text does not have enough contrast to be easily legible', 'editoria11y' ),
			'CONTRAST_PLACEHOLDER_UNSUPPORTED' => __( 'Does this placeholder text have enough contrast?', 'editoria11y' ),
			'CONTRAST_WARNING'                 => __( 'Does this text have enough contrast?', 'editoria11y' ),
			'CONTRAST_WARNING_GRAPHIC'         => __( 'Does this graphic or icon have enough contrast?', 'editoria11y' ),
			'DUPLICATE_ID'                     => __( 'Duplicate ID attribute', 'editoria11y' ),
			'DUPLICATE_TITLE'                  => __( 'This link has a tooltip with the same text as the link', 'editoria11y' ),
			'EMBED_AUDIO'                      => __( 'Does this audio have a transcript?', 'editoria11y' ),
			'EMBED_DATA_VIZ'                   => __( 'Is this visualization accessible?', 'editoria11y' ),
			'EMBED_GENERAL'                    => __( 'Embedded iframes need manual checks', 'editoria11y' ),
			'EMBED_MISSING_TITLE'              => __( 'Frame missing "title" attribute', 'editoria11y' ),
			'EMBED_UNFOCUSABLE'                => __( 'Frame with tabindex="-1" will not be keyboard accessible.', 'editoria11y' ),
			'EMBED_VIDEO'                      => __( 'Is this video accurately captioned?', 'editoria11y' ),
			'HEADING_EMPTY'                    => __( 'This heading has no text', 'editoria11y' ),
			'HEADING_EMPTY_WITH_IMAGE'         => __( 'This image is used as a heading, so it needs alt text', 'editoria11y' ),
			'HEADING_FIRST'                    => __( 'The first heading on this page is a subheading', 'editoria11y' ),
			'HEADING_LONG'                     => __( 'Can this heading be shorter?', 'editoria11y' ),
			'HEADING_MISSING_ONE'              => __( 'This page is missing a Heading 1', 'editoria11y' ),
			'HEADING_SKIPPED_LEVEL'            => __( 'This heading is tagged at the wrong level', 'editoria11y' ),
			'HIDDEN_FOCUSABLE'                 => __( 'This element cannot be described by screen readers', 'editoria11y' ),
			'HEADING_UNPRONOUNCEABLE'          => __( 'This heading is unpronounceable', 'editoria11y' ),
			'IMAGE_ALT_TOO_LONG'               => __( 'Can this alt text be shorter?', 'editoria11y' ),
			'IMAGE_DECORATIVE'                 => __( 'Is this image actually meaningless?', 'editoria11y' ),
			'IMAGE_DECORATIVE_CAROUSEL'        => __( 'Image in a carousel or gallery marked as decorative', 'editoria11y' ),
			'IMAGE_FIGURE_DECORATIVE'          => __( 'This captioned image has no alt text', 'editoria11y' ),
			'IMAGE_FIGURE_DUPLICATE_ALT'       => __( 'Alt text should not be the same as caption text', 'editoria11y' ),
			'LABELS_ARIA_LABEL_INPUT'          => __( 'Is there a visible label for this field?', 'editoria11y' ),
			'LABELS_PLACEHOLDER'               => __( 'Prefer visible labels to placeholders', 'editoria11y' ),
			'LABELS_INPUT_RESET'               => __( 'Is this reset button needed?', 'editoria11y' ),
			'LABELS_MISSING_LABEL'             => __( 'This input has an empty label', 'editoria11y' ),
			'LABEL_IN_NAME'                    => __( 'Visible label does not match invisible label', 'editoria11y' ),
			'LABELS_MISSING_IMAGE_INPUT'       => __( 'This image input is missing alt text', 'editoria11y' ),
			'LABELS_NO_FOR_ATTRIBUTE'          => __( 'This input is not connected to a label', 'editoria11y' ),
			'LANG_MISMATCH'                    => __( 'Language tag does not match the content', 'editoria11y' ),
			'LANG_OF_PARTS'                    => __( 'This content appears to be in a different language', 'editoria11y' ),
			'LANG_OF_PARTS_ALT'                => __( 'This alt text appears to be in a different language', 'editoria11y' ),
			'LINK_ALT_FILE_EXT'                => __( 'Alt text used as a link should not be a URL', 'editoria11y' ),
			'LINK_ALT_MAYBE_BAD'               => __( 'This linked alt might not be clear and concise', 'editoria11y' ),
			'LINK_ALT_MAYBE_BAD_WARNING'       => __( 'This linked alt might not be clear and concise', 'editoria11y' ),
			'LINK_ALT_UNPRONOUNCEABLE'         => __( 'Linked images need pronounceable alt text', 'editoria11y' ),
			'LINK_CLICK_HERE'                  => __( 'Manual check: link contains "click here"', 'editoria11y' ),
			'LINK_DOI'                         => __( 'Link article titles, not DOI numbers', 'editoria11y' ),
			'LINK_EMPTY'                       => __( 'This link contains no words', 'editoria11y' ),
			'LINK_EMPTY_LABELLEDBY'            => __( 'Link with invalid "aria-labelledby" attribute', 'editoria11y' ),
			'LINK_EMPTY_NO_LABEL'              => __( 'This link needs a label', 'editoria11y' ),
			'LINK_FILE_EXT'                    => __( 'Link points to a file without warning', 'editoria11y' ),
			'LINK_IDENTICAL_NAME'              => __( 'Links with the same text link to different pages', 'editoria11y' ),
			'LINK_IMAGE_ALT'                   => __( 'Does this alt text describe the link or the image?', 'editoria11y' ),
			'LINK_IMAGE_ALT_AND_TEXT'          => __( 'Does this alt text make sense as part of this link?', 'editoria11y' ),
			'LINK_IMAGE_LONG_ALT'              => __( 'Can this linked alt text be shorter?', 'editoria11y' ),
			'LINK_IMAGE_NO_ALT_TEXT'           => __( 'This linked image needs alt text', 'editoria11y' ),
			'LINK_IMAGE_TEXT'                  => __( 'Does this linked image need a description?', 'editoria11y' ),
			'LINK_MAYBE_BUTTON'                => __( 'Is this link actually a button?', 'editoria11y' ),
			'LINK_NEW_TAB'                     => __( 'Does this link open a new tab without warning?', 'editoria11y' ),
			'LINK_PLACEHOLDER_ALT'             => __( 'This linked alt text may be a placeholder', 'editoria11y' ),
			'LINK_STOPWORD'                    => __( 'This link only contains generic words', 'editoria11y' ),
			'LINK_STOPWORD_ARIA'               => __( 'The purpose of this link is visually hidden', 'editoria11y' ),
			'LINK_SUS_ALT'                     => __( "Does this image's alt describe the image or the link?", 'editoria11y' ),
			'LINK_SYMBOLS'                     => __( 'Are the symbols or emoji in this link meaningful?', 'editoria11y' ),
			'LINK_UNPRONOUNCEABLE'             => __( 'This link is unpronounceable', 'editoria11y' ),
			'LINK_URL'                         => __( 'Link text should not be a URL', 'editoria11y' ),
			'META_LANG'                        => __( 'Meta tag for page language missing', 'editoria11y' ),
			'META_LANG_SUGGEST'                => __( 'Did you mean a different language code?', 'editoria11y' ),
			'META_LANG_VALID'                  => __( 'Invalid language code', 'editoria11y' ),
			'PAGE_LANG_CONFIDENCE'             => __( 'Page language may not match the content', 'editoria11y' ),
			'META_MAX'                         => __( 'Meta tag limits how much users can enlarge text', 'editoria11y' ),
			'META_REFRESH'                     => __( 'Meta tag automatically refreshes page', 'editoria11y' ),
			'META_SCALABLE'                    => __( 'Meta tag prevents users from enlarging text', 'editoria11y' ),
			'META_TITLE'                       => __( 'Meta tag for page title missing', 'editoria11y' ),
			'MISSING_ALT'                      => __( 'Invalid HTML: image has no alt attribute', 'editoria11y' ),
			'MISSING_ALT_LINK'                 => __( 'Invalid HTML: linked image missing alt attribute', 'editoria11y' ),
			'MISSING_ALT_LINK_HAS_TEXT'        => __( 'Invalid HTML: image in link missing alt attribute', 'editoria11y' ),
			'QA_BAD_LINK'                      => __( 'This link target may be invalid', 'editoria11y' ),
			'QA_BLOCKQUOTE'                    => __( 'Should this quote be a heading?', 'editoria11y' ),
			'QA_DOCUMENT'                      => __( 'Has this document been tagged for screen readers?', 'editoria11y' ),
			'QA_FAKE_HEADING'                  => __( 'Should this bold text be a heading?', 'editoria11y' ),
			'QA_FAKE_LIST'                     => __( 'Should this have list formatting?', 'editoria11y' ),
			'QA_IN_PAGE_LINK'                  => __( 'Broken same-page link', 'editoria11y' ),
			'QA_JUSTIFY'                       => __( 'Do not justify text', 'editoria11y' ),
			'QA_NESTED_COMPONENTS'             => __( 'Nested interactive layout components', 'editoria11y' ),
			'QA_PDF'                           => __( 'Is there an alternative for this PDF?', 'editoria11y' ),
			'QA_SMALL_TEXT'                    => __( 'Text is too small', 'editoria11y' ),
			'QA_STRONG_ITALICS'                => __( 'Large blocks of emphasized text are harder to read', 'editoria11y' ),
			'QA_SUBSCRIPT'                     => __( 'Do not use sub/superscript as visual formatting', 'editoria11y' ),
			'QA_UNDERLINE'                     => __( 'Only links should be underlined', 'editoria11y' ),
			'QA_UPPERCASE'                     => __( 'Is this uppercase text needed?', 'editoria11y' ),
			'SUS_ALT'                          => __( 'Are there redundant words in this alt text?', 'editoria11y' ),
			'TABINDEX_ATTR'                    => __( 'Tabindex overrides interrupt the focus order', 'editoria11y' ),
			'TABLES_EMPTY_HEADING'             => __( 'This header cell needs text', 'editoria11y' ),
			'TABLES_INVALID_HEADERS_REF'       => __( 'This table has an invalid headers attribute', 'editoria11y' ),
			'TABLES_MISSING_HEADINGS'          => __( 'This table needs a header row and/or column', 'editoria11y' ),
			'TABLES_SEMANTIC_HEADING'          => __( 'Content headings should not be used inside tables', 'editoria11y' ),
			'UNCONTAINED_LI'                   => __( 'Invalid HTML list', 'editoria11y' ),
		);
	}

	/**
	 * Test keys that are visible to content editors (any mode).
	 *
	 * In free mode these are the only keys exposed in the settings UI as
	 * toggleable on/off; the rest are developer-only and disabled. In CSA
	 * mode every key in core_names() can be assigned to either profile via
	 * the 3-way select widget.
	 *
	 * NOTE: Drupal's contentTests() also lists 'EMBED_CUSTOM', which is not
	 * defined in coreNames() and therefore silently filtered out by Drupal's
	 * downstream array_intersect_key. Dropping it here so the invariant
	 * "content_tests ⊂ core_names" holds — this is the only intentional
	 * divergence from Drupal in this file.
	 *
	 * @return array<int, string>
	 */
	public static function content_tests(): array {
		return array(
			'ALT_FILE_EXT',
			'ALT_MAYBE_BAD',
			'ALT_MAYBE_BAD_WARNING',
			'ALT_PLACEHOLDER',
			'ALT_UNPRONOUNCEABLE',
			'EMBED_AUDIO',
			'EMBED_DATA_VIZ',
			'EMBED_GENERAL',
			'EMBED_MISSING_TITLE',
			'EMBED_VIDEO',
			'HEADING_EMPTY',
			'HEADING_EMPTY_WITH_IMAGE',
			'HEADING_LONG',
			'HEADING_SKIPPED_LEVEL',
			'IMAGE_ALT_TOO_LONG',
			'IMAGE_DECORATIVE',
			'IMAGE_FIGURE_DECORATIVE',
			'IMAGE_FIGURE_DUPLICATE_ALT',
			'LINK_ALT_FILE_EXT',
			'LINK_ALT_MAYBE_BAD',
			'LINK_ALT_MAYBE_BAD_WARNING',
			'LINK_ALT_UNPRONOUNCEABLE',
			'LINK_DOI',
			'LINK_EMPTY',
			'LINK_EMPTY_NO_LABEL',
			'LINK_IMAGE_ALT',
			'LINK_IMAGE_ALT_AND_TEXT',
			'LINK_IMAGE_LONG_ALT',
			'LINK_IMAGE_NO_ALT_TEXT',
			'LINK_IMAGE_TEXT',
			'LINK_NEW_TAB',
			'LINK_PLACEHOLDER_ALT',
			'LINK_STOPWORD',
			'LINK_SUS_ALT',
			'LINK_SYMBOLS',
			'LINK_URL',
			'LINK_UNPRONOUNCEABLE',
			'MISSING_ALT',
			'MISSING_ALT_LINK',
			'MISSING_ALT_LINK_HAS_TEXT',
			'QA_BAD_LINK',
			'QA_BLOCKQUOTE',
			'QA_DOCUMENT',
			'QA_FAKE_HEADING',
			'QA_FAKE_LIST',
			'QA_IN_PAGE_LINK',
			'QA_JUSTIFY',
			'QA_PDF',
			'QA_STRONG_ITALICS',
			'QA_SUBSCRIPT',
			'QA_UNDERLINE',
			'QA_UPPERCASE',
			'SUS_ALT',
			'TABLES_EMPTY_HEADING',
			'TABLES_MISSING_HEADINGS',
			'TABLES_SEMANTIC_HEADING',
		);
	}

	/**
	 * Test keys that ship disabled by default.
	 *
	 * These are checks that produce a high false-positive rate or require
	 * editorial judgement; admins opt them in per-site.
	 *
	 * @return array<int, string>
	 */
	public static function off_by_default(): array {
		return array(
			'CONTRAST_WARNING_GRAPHIC',
			'LINK_CLICK_HERE',
			'LINK_IMAGE_ALT',
		);
	}

	/**
	 * Test keys that describe markup the editor cannot fix without developer
	 * intervention (contrast, ARIA wiring, page-level meta, form structure,
	 * …) and are only meaningful when the CSA developer profile is active.
	 *
	 * In CSA-inactive installs the static-settings helper appends these keys
	 * to `ignoreTests` so the library does not run them in the browser —
	 * without the explicit disable, the upstream library defaults would
	 * silently surface them as active in `Drupal.Ed11y.State.option.checks`.
	 *
	 * Mirrors Drupal's TestNames::templateTests().
	 *
	 * @return array<int, string>
	 */
	public static function template_tests(): array {
		return array_values(
			array_diff(
				array_keys( self::core_names() ),
				self::content_tests()
			)
		);
	}

	/**
	 * Test keys that ship enabled in the upstream library but are inactive
	 * here. The static-settings helper always appends this list to
	 * `ignoreTests`, regardless of CSA state.
	 *
	 * Two categories:
	 *
	 * - `LINK_LABEL`: an upstream artifact, not a real test. It appears in
	 *   the library defaults but no rule implementation runs, so the entry
	 *   only pollutes `Drupal.Ed11y.State.option.checks`. Intentionally
	 *   absent from core_names().
	 * - `LANG_MISMATCH`, `LANG_OF_PARTS`, `LANG_OF_PARTS_ALT`,
	 *   `PAGE_LANG_CONFIDENCE`: real upstream checks, but they require
	 *   `langOfPartsPlugin`. The WP port does not enable that plugin yet,
	 *   so the keys surface as active in State.option but are never
	 *   actually called. They remain in core_names() because the catalogue
	 *   should describe the long-term intent, but they are added to the
	 *   always-ignore list until the plugin is wired up.
	 *
	 * Mirrors Drupal's TestNames::libraryArtifacts().
	 *
	 * @return array<int, string>
	 */
	public static function library_artifacts(): array {
		return array(
			'LINK_LABEL',
			'LANG_MISMATCH',
			'LANG_OF_PARTS',
			'LANG_OF_PARTS_ALT',
			'PAGE_LANG_CONFIDENCE',
		);
	}

	/**
	 * Routes a test key to one of 11 group identifiers.
	 *
	 * The order of checks matters: the first match wins, which is why e.g. QA
	 * is tested before LINK (so that QA_BAD_LINK lands in legibility, not
	 * links), and alts is tested before links (so MISSING_ALT_LINK lands in
	 * alts, not links).
	 *
	 * @param string $key Test key (e.g. 'HEADING_EMPTY').
	 * @return string Group identifier (e.g. 'headings', 'alts').
	 */
	public static function group_for_key( string $key ): string {
		if ( str_contains( $key, 'LINK_ALT' )
			|| str_contains( $key, 'LINK_IMAGE' )
			|| str_contains( $key, 'ALT_LINK' ) ) {
			return 'link_alt';
		}
		if (
			'TABINDEX_ATTR' === $key
			|| str_starts_with( $key, 'FOCUSABLE' )
			|| str_contains( $key, 'LABELLEDBY' )
			|| str_contains( $key, 'FOCUS' )
			|| 'QA_NESTED_COMPONENTS' === $key
		) {
			return 'aria';
		}
		if ( str_starts_with( $key, 'META' ) ) {
			return 'meta';
		}
		if ( str_starts_with( $key, 'HEADING' ) || 'QA_BLOCKQUOTE' === $key || 'QA_FAKE_HEADING' === $key ) {
			return 'headings';
		}
		if ( str_starts_with( $key, 'CONTRAST' ) ) {
			return 'contrast';
		}
		if ( str_starts_with( $key, 'TABLES' ) ) {
			return 'tables';
		}
		if (
			str_contains( $key, 'ALT_' )
			|| str_contains( $key, '_ALT' )
			|| str_starts_with( $key, 'IMAGE_' )
			|| str_starts_with( $key, 'MISSING_ALT' )
			|| 'SUS_ALT' === $key
		) {
			return 'alts';
		}
		if ( str_starts_with( $key, 'EMBED' ) ) {
			return 'embeds';
		}
		if ( str_starts_with( $key, 'LABEL' ) || str_starts_with( $key, 'BTN' ) ) {
			return 'forms';
		}
		if (
			str_contains( $key, 'LINK' )
			|| 'DUPLICATE_ID' === $key
			|| str_contains( $key, 'PDF' )
			|| str_contains( $key, 'DOI' )
			|| 'DUPLICATE_TITLE' === $key
		) {
			return 'links_content';
		}
		return 'legibility';
	}

	/**
	 * Group identifiers and translated labels in display order.
	 *
	 * The Drupal settings form renders these as section headers in the
	 * "Modify content tests" / "Modify template tests" details elements.
	 *
	 * @return array<string, string>
	 */
	public static function group_labels(): array {
		return array(
			'links_content' => __( 'Links', 'editoria11y' ),
			'link_alt'      => __( 'Linked images', 'editoria11y' ),
			'alts'          => __( 'Images', 'editoria11y' ),
			'headings'      => __( 'Headings', 'editoria11y' ),
			'tables'        => __( 'Tables', 'editoria11y' ),
			'legibility'    => __( 'Text formatting', 'editoria11y' ),
			'embeds'        => __( 'Video, audio, frames', 'editoria11y' ),
			'contrast'      => __( 'Contrast', 'editoria11y' ),
			'forms'         => __( 'Forms', 'editoria11y' ),
			'aria'          => __( 'ARIA and focus', 'editoria11y' ),
			'meta'          => __( 'Page metadata', 'editoria11y' ),
		);
	}

	/**
	 * Maps a group identifier to one of the two settings-form sections.
	 *
	 * Content tests render under "Modify content tests"; template tests under
	 * "Modify template tests". Defaults to content_tests for unknown groups.
	 *
	 * @param string $group Group identifier from group_for_key().
	 * @return string 'content_tests' or 'template_tests'.
	 */
	public static function group_set( string $group ): string {
		$sets = array(
			'headings'      => 'content_tests',
			'alts'          => 'content_tests',
			'link_alt'      => 'content_tests',
			'tables'        => 'content_tests',
			'legibility'    => 'content_tests',
			'embeds'        => 'content_tests',
			'links_content' => 'content_tests',
			'contrast'      => 'template_tests',
			'forms'         => 'template_tests',
			'aria'          => 'template_tests',
			'meta'          => 'template_tests',
		);
		return $sets[ $group ] ?? 'content_tests';
	}
}
