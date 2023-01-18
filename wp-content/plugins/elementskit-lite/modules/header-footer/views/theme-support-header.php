<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title>
			<?php echo esc_html( wp_get_document_title() ); ?>
		</title>
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php do_action( 'elementskit/template/before_header' ); ?>
<div class="ekit-template-content-markup ekit-template-content-header ekit-template-content-theme-support">
<?php
	$template = \ElementsKit_Lite\Modules\Header_Footer\Activator::template_ids();
	echo \ElementsKit_Lite\Utils::render_elementor_content( $template[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering
?>
</div>
<?php do_action( 'elementskit/template/after_header' ); ?>
