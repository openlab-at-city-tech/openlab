<?php

/**
* The admin-specific functionality of the plugin.
*
* @link       https://www.therealbenroberts.com
* @since      1.0.0
*
* @package    BP_Toolkit
* @subpackage BP_Toolkit/admin
*/

/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    BP_Toolkit
* @subpackage BP_Toolkit/admin
* @author     Ben Roberts <me@therealbenroberts.com>
*/
class BP_Toolkit_Admin {

	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $bp_toolkit    The ID of this plugin.
	*/
	private $bp_toolkit;

	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	* @param      string    $bp_toolkit       The name of this plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $bp_toolkit, $version ) {

		$this->bp_toolkit = $bp_toolkit;
		$this->version = $version;

	}

	/**
	* Register the stylesheets for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in BP_Toolkit_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The BP_Toolkit_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		wp_enqueue_style( $this->bp_toolkit, plugin_dir_url( __FILE__ ) . 'css/bp-toolkit-admin.css', array(), $this->version, 'all' );

	}

	/**
	* Register the JavaScript for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in BP_Toolkit_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The BP_Toolkit_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		wp_enqueue_script( $this->bp_toolkit, plugin_dir_url( __FILE__ ) . 'js/bp-toolkit-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	* Set up the admin page menu link.
	*
	* @since    1.0.1
	*/
	public function add_options_page() {
		add_options_page(
			__( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' ),
			__( 'Block Suspend Report', 'bp-toolkit' ),
			'manage_options',
			'bp-toolkit',
			array( $this, 'display_options_page' )
		);
	}

	/**
	* Display the admin page.
	*
	* @since    1.0.1
	*/
	public function display_options_page() { ?>

		<div class="wrap">
			<h1><?php _e( 'Block, Suspend, Report for BuddyPress', 'bp-toolkit' ) ?></h1>
			<!-- <h3><?php _e( 'Add the main description here', 'bp-toolkit' ) ?></h3> -->

			<?php
			if( isset( $_GET[ 'tab' ] ) )
			{
				$active_tab = $_GET[ 'tab' ];
			}else{
				// set our block tab as a default tab.
				$active_tab = 'welcome_section' ;
			}
			?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=bp-toolkit&tab=welcome_section" class="nav-tab <?php echo $active_tab == 'welcome_section' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Welcome', 'bp-toolkit' ) ?></a>
			</h2>

			<!-- Contain in a DIV for future use -->
			<div class="bptk-admin-container">

				<h4><?php _e( 'Thank you for installing Block, Suspend, Report for BuddyPress', 'bp-toolkit' ) ?></h4>
				<p><?php _e( 'For support, or to request a feature, please open a ticket here - ', 'bp-toolkit' ) ?><a href="https://wordpress.org/support/plugin/bp-toolkit/"><?php _e( 'Block, Suspend, Report for BuddyPress Support page', 'bp-toolkit' ) ?></a>.</p>
				<p><?php _e( "Block, Suspend, Report for BuddyPress doesn't require any special setup. Once activated, the Block, Suspend and Report modules begin working behind the scenes. Each module is explained below.", 'bp-toolkit' ) ?></p>

				<h4><?php _e( "Block", 'bp-toolkit' ) ?></h4>
				<p><?php _e( "The Block module provides a button on a member's profile and also in the member list. When one of your community presses the block button on someone's profile, they can no longer send or receive private messages to or from that member. They will also no longer be able to access each other's profile pages.", 'bp-toolkit' ) ?></p>

				<h4><?php _e( "Suspend", 'bp-toolkit' ) ?></h4>
				<p><?php _e( "The Suspend module also provides a button for each member. However, this button is only for administrators, as it provides a means to log a member out of the website and prevent them from signing in again. It is a great tool for immediately removing a member who has been breaching your terms of use. As soon as you are ready, click the button again to unsuspend them.", 'bp-toolkit' ) ?></p>

				<h4><?php _e( "Report", 'bp-toolkit' ) ?></h4>
				<p><?php _e( "The Report module provides a way for a member to report another, to the site administrator. Once the report button is clicked, the administrator receives an email. You can then liaise directly with the member to decide a course of action.", 'bp-toolkit' ) ?></p>

				<div class="advert">
					<h3><?php _e( "Block, Suspend, Report for BuddyPress Pro Edition", 'bp-toolkit' ) ?></h3>
					<p><?php _e( "The Professional version adds the following features:", 'bp-toolkit' ) ?></p>
					<ul>
						<li><?php _e( "Premium email support", 'bp-toolkit' ) ?></li>
						<li><?php _e( "Ability to add custom CSS to match your theme's styling perfectly", 'bp-toolkit' ) ?></li>
						<li><?php _e( "Automatically receive new features as they are built", 'bp-toolkit' ) ?></li>
					</ul>

					<h4><?php _e( "Plus, extra features for eacht module:", 'bp-toolkit' ) ?></h4>

						<h4><?php _e( "Block", 'bp-toolkit' ) ?></h4>
						<ul>
							<li><?php _e( "Users will be prevented from viewing any activity from members they have blocked, not just prevent sending and receiving of private messages. This addition turns Block, Suspend, Report for BuddyPress into a complete blocking system for your community.", 'bp-toolkit' ) ?></li>
						</ul>

						<h4><?php _e( "Suspend", 'bp-toolkit' ) ?></h4>
						<ul>
							<li><?php _e( "Send a fully customisable email to a suspended user. Handy to give an explanation about why they were suspended and what they can do to rejoin your community.", 'bp-toolkit' ) ?></li>
						</ul>

						<h4><?php _e( "Report", 'bp-toolkit' ) ?></h4>
						<ul>
							<li><?php _e( "Specify an email address (or more than one) to send the report to. Rather than the site administrator, you can now designate another member of staff or moderator to deal with reports from the community.", 'bp-toolkit' ) ?></li>
							<li><?php _e( "When a user clicks on the report button, they see a popup screen allowing them to give their reasons for the report.", 'bp-toolkit' ) ?></li>
						</ul>


					</div>
			</div>
		</div>
	<?php }

	/**
	* Add rating links to the admin dashboard
	*
	* @param string		$footer_text The existing footer text
	*
	* @return 	string
	* @since  	1.0.3
	* @global 	string $pagenow
	*
	*/
	public function admin_rate_us( $footer_text ) {
		global $pagenow;

		if ( ('options-general.php' === $pagenow) && ('bp-toolkit' === $_GET['page']) ) {
			$rate_text = sprintf(
				/* translators: %s: Link to 5 star rating */
				__( 'If you like <strong>Block, Suspend, Report for BuddyPress</strong> please leave us a %s rating. It takes a minute and helps a lot. Thanks in advance!', 'bp-toolkit' ),
				'<a href="https://wordpress.org/support/view/plugin-reviews/bp-toolkit?filter=5#postform" target="_blank" class="bp-toolkit-rating-link" style="text-decoration:none;" data-rated="' . esc_attr__( 'Thanks :)', 'bp-toolkit' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);

			return $rate_text;
		} else {
			return $footer_text;
		}
	}

}
