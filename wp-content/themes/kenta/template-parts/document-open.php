<?php
/**
 * Open HTML document
 *
 * @package Kenta
 */

use LottaFramework\Facades\CZ;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php kenta_html_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>
        data-kenta-scroll-reveal="<?php echo esc_attr( wp_json_encode( kenta_scroll_reveal_args() ) ); ?>">
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content">
	<?php esc_html_e( 'Skip to content', 'kenta' ); ?>
</a>
<div data-sticky-container class="<?php \LottaFramework\Utils::the_clsx( [
	'kenta-site-wrap'     => true,
	'kenta-has-site-wrap' => CZ::checked( 'kenta_enable_site_wrap' ),
	'z-[1]'               => true,
] ); ?>">
