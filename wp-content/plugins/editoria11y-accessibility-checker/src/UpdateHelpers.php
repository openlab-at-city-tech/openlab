<?php
/**
 * Translation tables for v2 → v3 result_key and result_name migration.
 *
 * Pure-data static class. Mirrors Drupal\editoria11y\UpdateHelpers's
 * updateVersion3() data fragments byte-for-byte so the canonical mapping has
 * one source of truth across both codebases. If Drupal updates its tables,
 * port the diff over here.
 *
 * Two tables:
 *   - old_keys()  — v2 camelCase result_key → v3 UPPER_SNAKE result_key
 *                   (used by the 1.2 → 2.0 migration worker for rows whose
 *                   result_key was written by the v2 JS library).
 *   - old_names() — ancient localized result_name strings → v3 UPPER_SNAKE
 *                   result_key (used for very old rows that stored the
 *                   user-visible name instead of a key, before the JS library
 *                   exposed one).
 *
 * @package Editoria11y
 *
 * @see /Users/jj/Sites/ed11yddev/web/modules/custom/editoria11y/src/UpdateHelpers.php
 */

namespace Editoria11y;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UpdateHelpers
 *
 * Direct port of Drupal\editoria11y\UpdateHelpers::updateVersion3('oldKeys'|'oldNames').
 */
class UpdateHelpers {

	/**
	 * Map of v2 camelCase result_key → v3 UPPER_SNAKE result_key.
	 *
	 * @return array<string, string>
	 */
	public static function old_keys(): array {
		return array(
			'altDeadspace'                => 'ALT_UNPRONOUNCEABLE',
			'altEmptyLinked'              => 'LINK_IMAGE_NO_ALT_TEXT',
			'altImageOf'                  => 'SUS_ALT',
			'altImageOfLinked'            => 'LINK_SUS_ALT',
			'altLong'                     => 'IMAGE_ALT_TOO_LONG',
			'altLongLinked'               => 'LINK_IMAGE_LONG_ALT',
			'altMeaningless'              => 'ALT_PLACEHOLDER',
			'altMeaninglessLinked'        => 'LINK_PLACEHOLDER_ALT',
			'altMissing'                  => 'MISSING_ALT',
			'altNull'                     => 'IMAGE_DECORATIVE',
			'altPartOfLinkWithText'       => 'LINK_IMAGE_ALT_AND_TEXT',
			'altURL'                      => 'ALT_FILE_EXT',
			'altURLLinked'                => 'LINK_ALT_FILE_EXT',
			'blockquoteIsShort'           => 'QA_BLOCKQUOTE',
			'embedAudio'                  => 'EMBED_AUDIO',
			'embedCustom'                 => 'EMBED_GENERAL',
			'embedVideo'                  => 'EMBED_VIDEO',
			'embedVisualization'          => 'EMBED_DATA_VIZ',
			'headingEmpty'                => 'HEADING_EMPTY',
			'headingIsLong'               => 'HEADING_LONG',
			'headingLevelSkipped'         => 'HEADING_SKIPPED_LEVEL',
			'linkDocument'                => 'QA_DOCUMENT',
			'linkNewWindow'               => 'LINK_NEW_TAB',
			'linkNoLabel'                 => 'LINK_EMPTY_NO_LABEL',
			'linkNoText'                  => 'LINK_EMPTY',
			'linkTextIsGeneric'           => 'LINK_STOPWORD',
			'linkTextIsURL'               => 'LINK_URL',
			'tableContainsContentHeading' => 'TABLES_SEMANTIC_HEADING',
			'tableEmptyHeaderCell'        => 'TABLES_EMPTY_HEADING',
			'tableNoHeaderCells'          => 'TABLES_MISSING_HEADINGS',
			'textPossibleHeading'         => 'QA_FAKE_HEADING',
			'textPossibleList'            => 'QA_FAKE_LIST',
			'textUppercase'               => 'QA_UPPERCASE',
		);
	}

	/**
	 * Map of ancient localized result_name → v3 UPPER_SNAKE result_key.
	 *
	 * Only relevant for rows old enough to have stored the human-readable
	 * test name in lieu of a key. Translation lookup is exact-match on the
	 * English string; localized installs that ran the v1 schema in another
	 * language are not covered (they get the default fallback instead).
	 *
	 * Verified byte-for-byte against Drupal UpdateHelpers.php `oldNames`
	 * (2026-07). Apparent oddities are deliberate parity, not typos:
	 * 'Manual check: is the linked document accessible?' maps to QA_PDF
	 * while the linkDocument KEY maps to QA_DOCUMENT (both are real,
	 * distinct v3 tests), and 'Image has no alternative text attribute' →
	 * LINK_IMAGE_LONG_ALT matches the upstream mapping exactly. Do not
	 * "fix" entries here without changing Drupal first.
	 *
	 * @return array<string, string>
	 */
	public static function old_names(): array {
		return array(
			'Alt text is meaningless'                      => 'ALT_PLACEHOLDER',
			"Image's text alternative is unpronounceable"  => 'ALT_UNPRONOUNCEABLE',
			'Linked Image has no alt text'                 => 'LINK_IMAGE_NO_ALT_TEXT',
			'Manual check: possibly redundant text in alt' => 'SUS_ALT',
			'Manual check: possibly redundant text in linked image' => 'LINK_SUS_ALT',
			'Manual check: very long alternative text'     => 'IMAGE_ALT_TOO_LONG',
			'Image has no alternative text attribute'      => 'LINK_IMAGE_LONG_ALT',
			'Manual check: image has no alt text'          => 'IMAGE_DECORATIVE',
			'Manual check: link contains both text and an image' => 'LINK_IMAGE_ALT_AND_TEXT',
			"Image's text alternative is a URL"            => 'ALT_FILE_EXT',
			"Linked image's text alternative is a URL"     => 'LINK_ALT_FILE_EXT',
			'Manual check: is this a blockquote?'          => 'QA_BLOCKQUOTE',
			'Manual check: is an accurate transcript provided?' => 'EMBED_AUDIO',
			'Manual check: is this embedded content accessible?' => 'EMBED_GENERAL',
			'Manual check: is this video accurately captioned?' => 'EMBED_VIDEO',
			'Manual check: is this visualization accessible?' => 'EMBED_DATA_VIZ',
			'Heading tag without any text'                 => 'HEADING_EMPTY',
			'Manual check: long heading'                   => 'HEADING_LONG',
			'Manual check: was a heading level skipped?'   => 'HEADING_SKIPPED_LEVEL',
			'Manual check: is the linked document accessible?' => 'QA_PDF',
			'Manual check: is opening a new window expected?' => 'LINK_NEW_TAB',
			'Link with no accessible text'                 => 'LINK_EMPTY',
			'Manual check: is this link meaningful and concise?' => 'LINK_STOPWORD',
			'Manual check: is this link text a URL?'       => 'LINK_URL',
			'Content heading inside a table'               => 'TABLES_SEMANTIC_HEADING',
			'Empty table header cell'                      => 'TABLES_EMPTY_HEADING',
			'Table has no header cells'                    => 'TABLES_MISSING_HEADINGS',
			'Manual check: should this be a heading?'      => 'QA_FAKE_HEADING',
			'Manual check: should this have list formatting?' => 'QA_FAKE_LIST',
			'Manual check: is this uppercase text needed?' => 'QA_UPPERCASE',
		);
	}
}
