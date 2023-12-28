<?php
/**
 * More link HTML.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$has_more_tag = Entry\Component::has_more_tag();
$more_tag     = ( $has_more_tag ) ? ( '#more-' . get_the_ID() ) : ( '' );

?>

<div class="link-more-container">
	<a href="<?php the_permalink(); echo esc_url( $more_tag ); ?>" class="link-more" aria-label="<?php

		echo esc_attr( sprintf(
			/* translators: %s: Name of current post */
			__( 'Continue reading %s', 'michelle' ),
			the_title_attribute( array( 'echo' => false ) )
		) );

	?>"><?php

	if ( is_string( $has_more_tag ) ) {
		echo esc_html( $has_more_tag );
	} else {
		esc_html_e( 'Continue reading&hellip;', 'michelle' );
	}

	?></a>
</div>
