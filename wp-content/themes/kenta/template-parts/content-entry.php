<?php
/**
 * Template part for displaying posts entry.
 *
 * @package Kenta
 */

use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

$entry_structure = CZ::layers( 'kenta_card_structure' );
$layout          = CZ::get( 'kenta_archive_layout' );

$card_attrs = [
	'id'               => 'post-' . get_the_ID(),
	'class'            => Utils::clsx(
		get_post_class( [ 'card overflow-hidden h-full' ] ),
		[ 'kenta-scroll-reveal' => CZ::checked( 'kenta_card_scroll_reveal' ) ]
	),
	'data-card-layout' => $layout,
];

if ( is_customize_preview() ) {
	$card_attrs['data-shortcut']          = 'dashed-border';
	$card_attrs['data-shortcut-location'] = 'kenta_archive:kenta_archive_card_section';
}

?>

<div class="card-wrapper w-full">
    <article <?php Utils::print_attribute_string( $card_attrs ); ?>>
		<?php kenta_post_structure( 'entry', $entry_structure, CZ::layers( 'kenta_entry_metas' ), [
			'title_link'   => true,
			'title_tag'    => CZ::get( 'kenta_entry_title_tag' ),
			'excerpt_type' => CZ::get( 'kenta_entry_excerpt_type' ),
		] ); ?>
    </article>
</div>
