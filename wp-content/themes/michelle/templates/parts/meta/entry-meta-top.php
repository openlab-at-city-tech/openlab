<?php
/**
 * Post meta info: date, author, category.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if (
	post_password_required()
	|| ! in_array( get_post_type( get_the_ID() ), (array) Setup\Post_Type::get_feature( 'entry_meta' ) )
) {
	return;
}

?>

<div class="entry-meta"><?php // Must be `<div>`, as `<footer>` throws HTML error if displayed before `<header>`.

	get_template_part( 'templates/parts/meta/entry-meta-item', 'date' );
	get_template_part( 'templates/parts/meta/entry-meta-item', 'author' );
	get_template_part( 'templates/parts/meta/entry-meta-item', 'category' );

?></div>
