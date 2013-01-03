<?php
// silence the theme notice in BP 1.5
define( 'BP_SILENCE_THEME_NOTICE', true );
/*
 * The admin class
 */
class GConnect_Admin {
	var $theme = null;
	var $pagehook = null;
	var $activity_fieldname = 'gc-activity-page';

	function GConnect_Admin( &$theme, $tp_active = false ) {
		$this->__construct( $theme, $tp_active );
	}
	function  __construct( &$theme, $tp_active = false ) {
		if( $tp_active ) {
			remove_action( 'admin_notices', 'bp_tpack_admin_notices' );
			return add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}
		$this->theme = $theme;
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 20 );
		add_filter( 'pre_update_option_' . $theme->settings_key, array( &$this, 'options_filter' ), 10, 2 );
		add_filter( 'screen_layout_columns', array( &$this, 'layout_columns' ), 10, 2 );
	}
	function admin_notice() { ?>
		<div id="message" class="updated fade">
			<p><?php printf( __( 'Please <a href="%s">deactivate</a> the BP Template Pack plugin to enable the BuddyPress features in GenesisConnect.', 'genesis-connect' ), admin_url( 'plugins.php' ) ) ?></p>
		</div>
<?php	}
	function admin_init() {
		register_setting( $this->theme->settings_key, $this->theme->settings_key );
	}
	function admin_menu() {
		$cap = ( is_multisite() ? 'manage_network_options' : 'manage_options' );
		$this->pagehook = add_submenu_page( 'genesis', __( 'Connect Settings', 'genesis-connect' ), __( 'Connect Settings', 'genesis-connect' ), $cap, 'connect-settings', array( &$this, 'theme_options' ) );
		add_action( 'load-' . $this->pagehook, array( &$this, 'add_metabox' ) );
	}
	function add_metabox() {
		add_meta_box('connect-theme-settings-nav', __('Navigation', 'genesis-connect'), array( &$this, 'metabox_navigation' ), $this->pagehook, 'column1');
		add_meta_box('connect-theme-settings-registration', __('Registration', 'genesis-connect'), array( &$this, 'metabox_registration' ), $this->pagehook, 'column1');
		add_meta_box('connect-theme-settings-layout', __('Layout', 'genesis-connect'), array( &$this, 'metabox_layout' ), $this->pagehook, 'column1');
	}
	function layout_columns( $columns, $screen ) {
		if ( $screen == $this->pagehook )
			$columns[$this->pagehook] = 1;

		return $columns;
	}
	function theme_options() {
		global $gconnect_theme, $screen_layout_columns;
	
		$width = 'width: 99%;';
		$hide2 = $hide3 = ' display: none;';
?>	
<div id="connect-theme-settings" class="wrap genesis-metaboxes">
<form method="post" action="options.php">
<?php		wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false );
		settings_fields( $this->theme->settings_key );
		
		screen_icon('options-general'); ?>
	<h2>
		<?php _e('Genesis - Connect Settings', 'genesis-connect'); ?>
		<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'genesis-connect') ?>" />
	</h2>

		<div class="metabox-holder">
			<div class="postbox-container" style="<?php echo $width; ?>">
				<?php do_meta_boxes( $this->pagehook, 'column1', null ); ?>
				<div class="bottom-buttons">
					<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'genesis-connect') ?>" />
				</div>
			</div>
		</div>
		<div class="inside">
		</div>
		<div class="postbox-container" style="<?php echo $width; echo $hide2; ?>">
		<div class="inside">
		</div>
		
	</form>
	</div>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $this->theme->settings_key; ?>');
		});
		//]]>
	</script>
	<?php
	}
	function metabox_navigation() {

		if ( ! bp_use_wp_admin_bar() ) {
?>
			<p><?php _e( 'BuddyPress Adminbar Navigation:', 'genesis-connect' ); ?>
			<select name="<?php echo $this->theme->settings_key; ?>[adminbar]">
			<option value=""><?php _e( ' -- None -- ', 'genesis-connect' ); ?></option>
<?php
			$menus = wp_get_nav_menus( array('orderby' => 'name') );
			foreach ( $menus as $menu ) {
				printf( '<option value="%d" %s>%s</option>', $menu->term_id, selected( $this->get_option( 'adminbar' ), $menu->term_id, false ), esc_html( $menu->name ) );
			} ?>
			</select></p>
			<hr class="div" />
			<p><input type="checkbox" name="<?php echo $this->theme->settings_key; ?>[home_adminbar]" id="<?php echo $this->theme->settings_key; ?>[home_adminbar]" value="1" <?php checked(1, $this->get_option('home_adminbar')); ?> />
				<label for="<?php echo $this->theme->settings_key; ?>[home_adminbar]"><?php _e('Show the adminbar on the home page', 'genesis-connect'); ?></label></p>
<?php
		}
?>
			<p><input type="checkbox" name="<?php echo $this->theme->settings_key; ?>[login_sidebar]" id="<?php echo $this->theme->settings_key; ?>[login_sidebar]" value="1" <?php checked(1, $this->get_option('login_sidebar')); ?> />
				<label for="<?php echo $this->theme->settings_key; ?>[login_sidebar]"><?php _e('Show the login form at the top of the main sidebar', 'genesis-connect'); ?></label></p>
<?php

		if ( ! bp_use_wp_admin_bar() ) {
?>
			<p><input type="checkbox" name="<?php echo $this->theme->settings_key; ?>[login_adminbar]" id="<?php echo $this->theme->settings_key; ?>[login_adminbar]" value="1" <?php checked(1, $this->get_option('login_adminbar')); ?> />
				<label for="<?php echo $this->theme->settings_key; ?>[login_adminbar]"><?php _e('Show the login link in the adminbar', 'genesis-connect'); ?></label></p>
<?php
		}
	}
	function metabox_layout() {
		global $gconnect_theme;
		$rabp_home = $this->get_option( 'home' );
		$bp_before_content = $this->get_option( 'before_content' );
		$custom_subnav = array( 'visitor' => __( 'Visitors', 'genesis-connect' ), 'user' => __( 'Logged in Users', 'genesis-connect' ) );
		$bpnav_subnav = $this->get_option( 'subnav' );

		?><p><label><input type="checkbox" name="<?php echo $this->theme->settings_key; ?>[home]" value="activity" <?php checked( 'activity', $rabp_home ); ?> />&nbsp;<?php 
		_e( 'Show activity on the front page', 'genesis-connect' ); 
		?></label></p>
		<p><label><input type="checkbox" name="<?php echo $this->theme->settings_key; ?>[forum_layout]" value="full-width-content" <?php checked( 'full-width-content', $this->get_option( 'forum_layout' ) ); ?> />&nbsp;<?php 
		_e( 'Full Width Content for Group Forum layout', 'genesis-connect' ); 
		?></label></p>
			<p><?php _e('BuddyPress Before Content:', 'genesis-connect'); ?>
			<select name="<?php echo $this->theme->settings_key; ?>[before_content]">
				<option style="padding-right:10px;" value="none" <?php selected( 'none', $bp_before_content ); ?>><?php _e('None', 'genesis-connect'); ?></option>
				<option style="padding-right:10px;" value="widget" <?php selected( 'widget', $bp_before_content ); ?>><?php _e('Widget Area', 'genesis-connect'); ?></option>
				<option style="padding-right:10px;" value="genesis" <?php selected( 'genesis', $bp_before_content ); ?>><?php _e('Genesis Before Content', 'genesis-connect'); ?></option>
			</select></p>
	<?php	if( ( $do_sidebars = defined( 'SS_SETTINGS_FIELD' ) ) || $gconnect_theme->do_custom_subnav() ) {
			foreach( $custom_subnav as $k => $v ) {
				echo '<hr class="div" />';
				$key = "{$k}_";
				echo '<p><h4>' . $v . '</h4>';
				if( $gconnect_theme->do_custom_subnav() )
					$gconnect_theme->custom_subnav->print_menu_select( $this->theme->settings_key . "[{$key}subnav]", $this->get_option( $key . 'subnav' ), '', 'padding-right: 10px;' );
				if( $do_sidebars )
					$this->print_sidebar_select( array( $key . 'sidebar' => __( 'Primary Sidebar', 'genesis-connect' ), $key . 'sidebar_alt' => __( 'Secondary Sidebar', 'genesis-connect' ) ) );
				echo '</p>';
			}
		}
	}
	function metabox_registration() {
		if ( !bp_get_signup_allowed() ) {
			printf( '<p>' . __( '<a href="%s">Registration</a> is currently disabled', 'genesis-connect' ) . '</p>', is_multisite() ? network_admin_url( 'settings.php' ) : admin_url( 'options-general.php' ) );
			foreach( array( 'custom_register', 'register_slug', 'register_title', 'register_time') as $field )
				echo '<input type="hidden" name="' . $this->theme->settings_key . '[' . $field . ']" value="' .$this->get_option( $field ) . '" />';
			return;
		}
		$custom_register = $this->get_option( 'custom_register' );
?>
			<p><?php _e('Custom Registration Permalink:', 'genesis-connect'); ?>
			<select name="<?php echo $this->theme->settings_key; ?>[custom_register]">
				<option style="padding-right:10px;" value="none" <?php selected( 'none', $custom_register ); ?>><?php _e('None', 'genesis-connect'); ?></option>
				<option style="padding-right:10px;" value="before_pages" <?php selected( 'before_pages', $custom_register ); ?>><?php _e('Before Pages', 'genesis-connect'); ?></option>
				<option style="padding-right:10px;" value="after_pages" <?php selected( 'after_pages', $custom_register ); ?>><?php _e('After Pages', 'genesis-connect'); ?></option>
				<option style="padding-right:10px;" value="adminbar" <?php selected( 'adminbar', $custom_register ); ?>><?php _e('On the Adminbar', 'genesis-connect'); ?></option>
			</select></p>
			<small><strong><?php _e( 'Using the custom registration disables the default BuddyPress registration page', 'genesis-connect' ); ?></strong></small></p>
			<p><?php _e( 'Custom Permalink:', 'genesis-connect' ); ?><br />
			<input type="text" name="<?php echo $this->theme->settings_key; ?>[register_slug]" value="<?php echo esc_attr( $this->get_option('register_slug') ); ?>" size="40" /><br />
			<small><strong><?php printf( __( "Don't include the %s", 'genesis-connect' ), get_option( 'siteurl' ) ); ?></strong></small></p>

			<p><?php _e("Custom Title:", 'genesis-connect'); ?><br />
			<input type="text" name="<?php echo $this->theme->settings_key; ?>[register_title]" value="<?php echo esc_attr( $this->get_option('register_title') ); ?>" size="40" /><br />
			<small><strong><?php _e("The title for the link", 'genesis-connect'); ?></strong></small></p>

			<p><?php _e("Registration Time:", 'genesis-connect'); ?><br />
			<input type="text" name="<?php echo $this->theme->settings_key; ?>[register_time]" value="<?php echo esc_attr( $this->get_option('register_time') ); ?>" size="5" /><br />
			<small><strong><?php _e("Minimum number of seconds for a human signup", 'genesis-connect'); ?></strong></small></p>
<?php	}
	function page_metabox() {
		global $post;
		$activity_page = get_option( 'gconnect_activity_page' ); ?>
		<p><input type="checkbox" name="<?php echo $this->activity_fieldname; ?>" id="<?php echo $this->activity_fieldname; ?>" value="1" <?php checked( $post->ID, $activity_page ); ?> />
		<label for="<?php echo $this->activity_fieldname; ?>"><?php _e( 'Use this for the Recent Activity Page', 'genesis-connect' ); ?></label></p>
<?php	}
	function print_sidebar_select( $bp_sidebars ) {
		$_sidebars = stripslashes_deep( get_option( SS_SETTINGS_FIELD ) );
		foreach( (array)$bp_sidebars as $field_name => $description ) {
			$selected = $this->get_option( $field_name );
	?>
		<p>
			<label for="<?php echo $this->theme->settings_key . "[$field_name]"; ?>"><span><?php echo $description; ?><span></label>
			<select name="<?php echo $this->theme->settings_key . "[$field_name]"; ?>" id="<?php echo $this->theme->settings_key . "[$field_name]"; ?>">
				<option style="padding-right:10px;" value=""><?php _e( 'Default', 'genesis-connect' ); ?></option>
	<?php		foreach ( (array)$_sidebars as $id => $info ) {
				printf( '<option style="padding-right:10px;" value="%s" %s>%s</option>', esc_attr( $id ), selected( $id, $selected, false), esc_html( $info['name'] ) );
			} ?>
			</select>
		</p>
	<?php	}
	}

	function save_post( $post_id, $post ) {
		//	don't try to save the data under autosave, ajax, or future post.
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if ( defined('DOING_AJAX') && DOING_AJAX ) return;
		if ( defined('DOING_CRON') && DOING_CRON ) return;
		if ( $post->post_type == 'revision' ) return;

		if ( current_user_can( 'edit_post', $post_id ) ) {
			$activity_page = get_option( 'gconnect_activity_page' );
			if( $activity_page == $post_id && ( empty( $_POST[$this->activity_fieldname] ) || $post->post_status != 'publish' ) )
				update_option( 'gconnect_activity_page', '' );
			elseif( !empty( $_POST[$this->activity_fieldname] ) )
				update_option( 'gconnect_activity_page', $post_id );
		}
		return $post_id;
	}

	function options_filter( $settings ) {
		if( isset( $settings[ 'register_slug' ] ) ) {
			$slug = preg_replace( '|(https?://[^/]+)|', '', $settings[ 'register_slug' ] );
			$base = preg_replace( '|(https?://[^/]+)|', '', get_option( 'siteurl' ) );
			if( substr( $slug, 0, 1 ) != '/' )
				$slug = '/' . $slug;
			if( ( $index = strpos( '$slug', '?' ) !== false ) )
				$slug = substr( $slug, 0, $index );
			if( substr( $slug, 0, strlen( $base ) ) == $base )
				$slug = substr( $slug, strlen( $base ) );
			if( strlen( $slug ) > 2 )
				$settings[ 'register_slug' ] = esc_url( untrailingslashit( str_replace( ' ', '-', $slug ) ) );
			else
				unset( $settings[ 'register_slug' ] );
		}
		if( isset( $settings[ 'register_time' ] ) )
			$settings[ 'register_time' ] = (int) $settings[ 'register_time' ];

		if( !$settings['register_time'] || !$settings['register_slug'] || !$settings['register_title'] )
			 $settings['custom_register'] = 'none';

		return $settings;
	}
	function get_option( $key ) {
		return $this->theme->get_option( $key );
	}
}
