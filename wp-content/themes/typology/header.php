<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<header id="typology-header" class="typology-header">
			<div class="container">
					<?php get_template_part('template-parts/header/layout-' . typology_get_option( 'header_layout' ) ); ?>
			</div>
		</header>