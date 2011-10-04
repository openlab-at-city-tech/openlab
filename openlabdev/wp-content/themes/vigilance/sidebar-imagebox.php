<?php global $vigilance;
	$custom_field_url = wp_filter_post_kses(get_post_meta($post->ID, 'sideimg-url', $single = true));
	$custom_field_alt = stripslashes(wp_filter_post_kses(get_post_meta($post->ID, 'sideimg-alt', $single = true)));
	$custom_field_link = wp_filter_post_kses(get_post_meta($post->ID, 'sideimg-link', $single = true));
	$custom_field_height = wp_filter_post_kses(get_post_meta($post->ID, 'sideimg-height', $single = true));
	$custom_field_status = get_post_meta($post->ID, 'sideimg-status', $single = true);
?>
<?php //------Page and Post Specific---------------------------------------------------------------------------------//
if (($custom_field_status !== 'hidden' && $custom_field_url !== '' ) || ($vigilance->sideimgState() == 'specific' )) : ?>
	<?php if ($custom_field_status !== 'hidden' && $custom_field_url !== '' ) { ?>
		<div id="sidebar-image">
				<?php if ($custom_field_link !== '' ) {?>
				<a href="<?php echo $custom_field_link; ?>">
				<?php } ?>
					<?php if (is_single() || is_page() && $custom_field_url !== '' ) { ?>
					<img src="<?php echo $custom_field_url; ?>" width="300" height="<?php if ($custom_field_height !== '' ) echo $custom_field_height; else echo $vigilance->sideimgHeight(); ?>" alt="<?php if ($custom_field_alt !== '' ) echo $custom_field_alt; else echo the_title(); ?>"/>
					<?php } ?>
				<?php if ($custom_field_link !== '' ) {?>
				</a>
				<?php } ?>
		</div><!--end sidebar-image-->
	<?php } ?>
<?php //------Static Image---------------------------------------------------------------------------------//
elseif ($vigilance->sideimgState() == 'static' && $vigilance->sideimgUrl() !== '' ) : ?>
	<div id="sidebar-image">
		<?php if ($vigilance->sideimgLink() !== '' ) {?>
			<a href="<?php echo $vigilance->sideimgLink(); ?>">
		<?php } ?>
			<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sidebar/<?php echo $vigilance->sideimgUrl(); ?>" width="300" <?php if ($vigilance->sideimgHeight() != '' ) { ?> height="<?php echo $vigilance->sideimgHeight(); ?>" <?php }; ?> alt="<?php if ($vigilance->sideimgAlt() !== '' ) echo $vigilance->sideimgAlt(); else echo bloginfo( 'name' ); ?>"/>
		<?php if ($vigilance->sideimgLink() !== '' ) {?>
			</a>
		<?php } ?>
	</div><!--end sidebar-image-->

<?php //------Custom Code---------------------------------------------------------------------------------//
elseif ($vigilance->sideimgState() == 'custom' ) : ?>
		<div id="sidebar-image">
			<?php echo $vigilance->sideimgCustom(); ?>
		</div><!--end sidebar-image-->

<?php //------Rotating Images---------------------------------------------------------------------------------//
else : ?>
		<div id="sidebar-image">
			<?php if ($vigilance->sideimgLink() !== '' ) {?>
				<a href="<?php echo $vigilance->sideimgLink(); ?>">
			<?php } ?>
			<img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sidebar/rotate.php" width="300" <?php if ($vigilance->sideimgHeight() != '' ) { ?> height="<?php echo $vigilance->sideimgHeight(); ?>" <?php }; ?> alt="<?php if ($vigilance->sideimgAlt() !== '' ) echo $vigilance->sideimgAlt(); else echo bloginfo( 'name' ); ?>"/>
			<?php if ($vigilance->sideimgLink() !== '' ) {?>
				</a>
			<?php } ?>
		</div><!--end sidebar-image-->
<?php endif; ?>