<?php
/**
 * Template part for displaying a post's title
 *
 * @package kadence
 */

namespace Kadence;

do_action( 'kadence_single_before_entry_title' );
the_title( '<h1 class="entry-title">', '</h1>' );
do_action( 'kadence_single_after_entry_title' );
