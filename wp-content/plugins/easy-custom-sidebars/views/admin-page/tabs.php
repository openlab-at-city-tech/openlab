<?php 
/**
 * Admin Tabs
 *
 * This file is responsible for generating the tabs 
 * on the admin settings page. Allows the user to
 * navigate between screens on the settings page.
 * 
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
?>
<!-- Screen Navigation -->
<?php screen_icon(); ?>
<h2 class="nav-tab-wrapper">
	<a href="<?php echo $this->admin_url; ?>" class="nav-tab <?php if ( $this->is_edit_screen() || $this->is_create_screen() ) { echo 'nav-tab-active'; } ?>"> 
		<?php esc_html_e( 'Edit Sidebars', 'easy-custom-sidebars' ); ?>
	</a>
	<a href="<?php echo $this->manage_url; ?>" class="nav-tab <?php if ( $this->is_manage_screen() ) { echo 'nav-tab-active'; } ?>">
		<?php esc_html_e( 'Manage Sidebar Replacements', 'easy-custom-sidebars' ); ?>
	</a>
	<!-- Uncomment to enable the advanced tab -->
	<!--
	<a href="<?php echo $this->advanced_url; ?>" class="nav-tab <?php if ( $this->is_advanced_screen() ) { echo 'nav-tab-active'; } ?>">
		<?php esc_html_e( 'Advanced', 'easy-custom-sidebars' ); ?>
	</a> -->
</h2>
