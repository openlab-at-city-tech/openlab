<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package ElementsKit_Lite
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$enable_skip_link = apply_filters( 'elementskit_enable_skip_link', true );
$skip_link_url = apply_filters( 'elementskit_skip_link_url', '#content' );
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php if (! current_theme_supports('title-tag')) : ?>
		<title>
			<?php echo esc_html(wp_get_document_title()); ?>
		</title>
	<?php endif; ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php wp_body_open(); ?>

	<?php if ($enable_skip_link) { ?>
		<a class="skip-link screen-reader-text" href="<?php echo esc_url($skip_link_url); ?>">
			<?php echo esc_html__('Skip to content', 'elementskit-lite'); ?>
		</a>
	<?php } ?>

	<?php do_action('elementskit/template/before_header'); ?>

	<div class="ekit-template-content-markup ekit-template-content-header ekit-template-content-theme-support">
		<?php
		$template = \ElementsKit_Lite\Modules\Header_Footer\Activator::template_ids();
		echo \ElementsKit_Lite\Utils::render_elementor_content($template[0]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering
		?>
	</div>

	<?php do_action('elementskit/template/after_header'); ?>