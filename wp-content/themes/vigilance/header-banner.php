<?php global $vigilance;
	$custom_field_url = get_post_meta($post->ID, 'img-url', $single = true);
	$custom_field_alt = stripslashes(wp_filter_post_kses(get_post_meta($post->ID, 'img-alt', $single = true)));
	$custom_field_height = wp_filter_post_kses(get_post_meta($post->ID, 'img-height', $single = true));
	$custom_field_status = get_post_meta($post->ID, 'img-status', $single = true);
?>

<?php //-----Page and Post Specific---------------------------------------------------------------------------------//
if ((is_single() || is_page()) && ($custom_field_url !== '' && !is_home() && $custom_field_status !== 'hidden' ) || ($vigilance->bannerState() == 'specific' && !is_home())) : ?>
	<?php if ((is_single() || is_page()) && ($custom_field_url !== '' && $custom_field_status !== 'hidden' )) { ?>
		<div id="menu">
			<img src="<?php echo $custom_field_url; ?>" width="596" height="<?php if ($custom_field_height !== '' ) echo $custom_field_height; else echo $vigilance->bannerHeight(); ?>" alt="<?php if ($custom_field_alt !== '' ) echo $custom_field_alt; else echo the_title(); ?>"/>
		</div><!--end menu-->
	<?php } ?>
<?php //-----Home Page---------------------------------------------------------------------------------//
elseif ($vigilance->bannerHome() != '' && is_home()) : ?>
	<div id="menu">
		<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/top-banner/<?php echo $vigilance->bannerHome(); ?>" width="596" <?php if ($vigilance->bannerHeight() != '' ) { ?> height="<?php echo $vigilance->bannerHeight(); ?>" <?php }; ?> alt="<?php if ($vigilance->bannerAlt() !== '' ) echo $vigilance->bannerAlt(); else echo bloginfo( 'name' ); ?>"/>
	</div><!--end menu-->

<?php //-----Static Image---------------------------------------------------------------------------------//
elseif ($vigilance->bannerState() == 'static' && $vigilance->bannerUrl() !== '' ) : ?>
	<div id="menu">
		<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/top-banner/<?php echo $vigilance->bannerUrl(); ?>" width="596" <?php if ($vigilance->bannerHeight() != '' ) { ?> height="<?php echo $vigilance->bannerHeight(); ?>" <?php }; ?> alt="<?php if ($vigilance->bannerAlt() !== '' ) echo $vigilance->bannerAlt(); else echo bloginfo( 'name' ); ?>"/>
	</div><!--end menu-->

<?php //------Custom Code---------------------------------------------------------------------------------//
elseif ($vigilance->bannerState() == 'custom' ) : ?>
	<div id="menu">
		<?php echo $vigilance->bannerCustom(); ?>
	</div><!--end menu-->

<?php //-----Rotating Image---------------------------------------------------------------------------------//
else : ?>
	<?php if ($vigilance->bannerState() == 'rotate' ) : ?>
		<div id="menu">
			<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/top-banner/rotate.php" width="596" <?php if ($vigilance->bannerHeight() != '' ) { ?> height="<?php echo $vigilance->bannerHeight(); ?>" <?php }; ?> alt="<?php if ($vigilance->bannerAlt() !== '' ) echo $vigilance->bannerAlt(); else echo bloginfo( 'name' ); ?>"/>
		</div><!--end menu-->
	<?php endif; ?>
<?php endif; ?>