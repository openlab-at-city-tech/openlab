<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">

	<head>
	    <meta charset="<?php bloginfo( 'charset' ); ?>">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link rel="profile" href="https://gmpg.org/xfn/11" />
	    <?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

	<div class="johannes-wrapper">

	    <?php if ( johannes_get( 'display', 'header' ) ): ?>
		    
		    <header class="johannes-header johannes-header-main d-none d-lg-block">
		    	<?php if ( johannes_get( 'header', 'top' ) ): ?>
					<?php get_template_part( 'template-parts/header/top' ); ?>
				<?php endif; ?>

		        <?php get_template_part( 'template-parts/header/layout-' . johannes_get( 'header', 'layout' ) ); ?>
		    	
		    </header>

		    <?php get_template_part( 'template-parts/header/mobile' ); ?>

		    <?php if ( johannes_get( 'header', 'sticky' ) ): ?>
		    	
		    	<?php get_template_part( 'template-parts/header/sticky' ); ?>

		    <?php endif; ?>

	    <?php endif; ?>

