<?php
// This class contains the code only used with visitors in Genesis Connect

class GConnect_visitor {
	var $theme = null;
	var $signup_min;
	var $time;
	var $register;

	function __construct( $theme, $register = '' ) {
		$this->theme = $theme;
		$this->register = $register;
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
	}
	function after_setup_theme() {
		$this->signup_min = $this->get_option( 'register_time' );
		$this->time = time();

		add_filter( 'register', array( $this, 'register_filter' ) );
		add_filter( 'wpmu_validate_user_signup', array( $this, 'signup_time_check' ) );
		add_action( 'bp_before_account_details_fields', array( $this, 'signup_time_field' ) );
		add_action( 'bp_adminbar_menus', array( $this, 'bp_adminbar_menus' ), 1 );
		add_action( 'bp_after_register_page', array( $this, 'bp_after_register_page' ) );
		add_action( 'signup_hidden_fields', array( $this, 'signup_time_field' ) );
		add_action( 'signup_extra_fields', array( $this, 'signup_time_error' ) );
		add_action( 'wp', array( &$this, 'signup_redirect' ), 2 );

		if( !is_multisite() && strpos( $_SERVER['SCRIPT_NAME'], 'wp-login.php' ) !== false )
			add_filter( 'pre_option_users_can_register', array( $this, 'users_can_register' ) );

		if( isset( $_GET['action'] ) && $_GET['action'] == 'register' )
			remove_action( 'init', 'bp_core_wpsignup_redirect' );

		if( false !== strpos( $_SERVER['SCRIPT_NAME'], 'wp-signup.php') ) {
			add_filter( 'pre_site_option_registration', array( &$this, 'registration' ) );
			remove_action( 'wp', 'bp_core_wpsignup_redirect' );
		}
	}
	function get_register() {
		return $this->register;
	}
	function registration() {
		return 'none';
	}
	function register_filter( $setting ) {
		return $this->signup_url();
	}
	function users_can_register() {
		return '0';
	}
	function signup_time_check( $result ) {
		$hextime = substr( dechex( $this->time ), 0, -4 ) . substr( $_POST[ 'gconnect_signup_hash' ], 0, 4 );
		$orig_time = hexdec( $hextime );
		if( substr( $_POST[ 'gconnect_signup_hash' ], 4 ) != $this->signup_hash( $hextime ) || $this->time - $orig_time < $this->signup_min ) {
			$result['errors']->add( 'gconnect_time', __('Please try again', 'genesis-connect' ) );
			add_action( 'bp_signup_validate', array( &$this, 'bp_signup_validate' ) );
		}
		return $result;
	}
	function signup_time_field() {
		$hextime = dechex( $this->time );
		$hash = substr( $hextime, -4 );
	?>
	<input name="gconnect_signup_hash" type="hidden" value="<?php echo $hash . $this->signup_hash( $hextime ); ?>" />
	<?php
		global $bp;
		if( isset( $bp ) && isset( $bp->signup->errors['gconnect_time'] ) )
			echo '<h3>' . $bp->signup->errors['gconnect_time'] . '</h3> ';
	}
	function signup_time_error( $errors ) {
		if( ( $msg = $errors->get_error_message( 'gconnect_time' ) ) ) {
			echo '<p class="error">' . $msg . '</p> ';
		}
	}
	function bp_signup_validate() {
		global $bp;
		if( isset( $bp ) )
			$bp->signup->errors['gconnect_time'] = __( 'Please try again', 'genesis-connect' );
	}
	function signup_hash( $seed ) {
		$hash = '';
		if( $seed ) {
			$base_hash = sha1( wp_salt( 'nonce' ) . $seed ) . sha1( $_SERVER['HTTP_USER_AGENT'] );
			$index = 0;
			$base_len = strlen( $base_hash );
			for( $i = 1; $i < $base_len && $index < $base_len; $i++ ) {
				$hash .= substr( $base_hash, $index, 1 );
				$index += $i;
			}
		}
		return $hash;
	}
	function signup_url() {
		$permalink = get_option( 'siteurl' ) . $this->get_option( 'register_slug' );
		$title = $this->get_option( 'register_title' );
		if( $permalink && $title )
			return "<li><a href='$permalink' title='$title' rel='noindex,nofollow'>$title</a></li>\n";

		return '';
	}
	function signup_redirect() {
		global $bp;

		$slug = $this->get_option( 'register_slug' );

		if( $bp->current_component == BP_REGISTER_SLUG && '/' . BP_REGISTER_SLUG != $slug )
			bp_core_redirect( $bp->root_domain );

		$register_slug = preg_replace( '|(https?://[^/]+)|', '', get_option( 'siteurl' ) ) . $slug;
		if( substr( $_SERVER[ 'REQUEST_URI' ], 0, strlen( $register_slug ) ) == $register_slug ) {
			$bp->current_component = BP_REGISTER_SLUG;
			add_action( 'genesis_meta', array( &$this, 'signup_noindex' ), 9 );
		}
	}
	function signup_noindex() {
		remove_action('genesis_meta','genesis_index_follow_logic');
		echo "\n<meta name=\"robots\" content=\"noindex\" />\n";
	}
	function bp_adminbar_menus() {
		global $bp;

		if( $this->get_option( 'login_adminbar' ) )
			echo '<li class="bp-login no-arrow"><a href="' . site_url( 'wp-login.php', 'login' ) . '?redirect_to=' . urlencode( $bp->root_domain ) . '">' . __( 'Log In', 'buddypress' ) . '</a></li>';

		if ( bp_get_signup_allowed() && 'adminbar' == $this->register ) {
			$signup = $this->signup_url();
			if( strlen( $signup ) > 3 )
				echo substr( $signup, 0, 3 ) . ' class="bp-signup no-arrow"' . substr( $signup, 3 );
		}

		remove_action( 'bp_adminbar_menus', 'bp_adminbar_login_menu', 2 );
	}
	function bp_after_register_page() { ?>
	<script type="text/javascript">
		jQuery(document).ready( function() {
			if ( jQuery('div#blog-details').length )
				jQuery('div#blog-details').toggle();

			jQuery( 'input#signup_with_blog' ).click( function() {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>
<?php	}
	function get_option( $key ) {
		return $this->theme->get_option( $key );
	}
}
