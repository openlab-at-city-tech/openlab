<?php

if ( !class_exists( 'MeowCommon_Classes_Ratings' ) ) {

	class MeowCommon_Classes_Ratings {

		public $mainfile; 	// plugin main file (media-file-renamer.php)
		public $domain; 		// domain used for translation (media-file-renamer)
		public $prefix;			// used for many things (filters, options, etc)

		public function __construct( $prefix, $mainfile, $domain ) {
			$this->mainfile = $mainfile;
			$this->domain = $domain;
			$this->prefix = $prefix;

      register_activation_hook( $mainfile, array( $this, 'show_meowapps_create_rating_date' ) );

      if ( is_admin() ) {
        $rating_date = $this->create_rating_date();
        if ( time() > $rating_date ) {
          add_action( 'admin_notices', array( $this, 'admin_notices_rating' ) );
        }
      }
		}

		function show_meowapps_create_rating_date() {
			delete_option( 'meowapps_hide_meowapps' );
			$this->create_rating_date();
		}

		function create_rating_date() {
			$rating_date = get_option( $this->prefix . '_rating_date' );
			if ( empty( $rating_date ) ) {
				$two_months = strtotime( '+2 months' );
				$six_months = strtotime( '+4 months' );
				$rating_date = mt_rand( $two_months, $six_months );
				update_option( $this->prefix . '_rating_date', $rating_date, false );
			}
			return $rating_date;
		}

		function admin_notices_rating() {
			if ( isset( $_POST[$this->prefix . '_remind_me'] ) ) {
				$two_weeks = strtotime( '+2 weeks' );
				$six_weeks = strtotime( '+6 weeks' );
				$future_date = mt_rand( $two_weeks, $six_weeks );
				update_option( $this->prefix . '_rating_date', $future_date, false );
				return;
			}
			else if ( isset( $_POST[$this->prefix . '_never_remind_me'] ) ) {
				$twenty_years = strtotime( '+5 years' );
				update_option( $this->prefix . '_rating_date', $twenty_years, false );
				return;
			}
			else if ( isset( $_POST[$this->prefix . '_did_it'] ) ) {
				$twenty_years = strtotime( '+10 years' );
				update_option( $this->prefix . '_rating_date', $twenty_years, false );
				return;
			}
			$rating_date = get_option( $this->prefix . '_rating_date' );
			echo '<div class="notice notice-success" data-rating-date="' . date( 'Y-m-d', $rating_date ) . '">';
				echo '<p style="font-size: 100%;">';
				printf(
					// translators: %1$s is a plugin nicename, %2$s is a short url (slug)
					__( 'You have been using <b>%1$s</b> for some time now. Thank you! Could you kindly share your opinion with me, along with, maybe, features you would like to see implemented? Then, please <a style="font-weight: bold; color: #b926ff;" target="_blank" href="https://wordpress.org/support/plugin/%2$s/reviews/?rate=5#new-post">write a little review</a>. That will also bring me joy and motivation! I will get back to you :)', $this->domain ),
					$this->nice_name_from_file( $this->mainfile ),
					$this->nice_short_url_from_file( $this->mainfile )
				);
			echo '<p>
				<form method="post" action="" style="float: right;">
					<input type="hidden" name="' . $this->prefix . '_never_remind_me" value="true">
					<input type="submit" name="submit" id="submit" class="button button-red" value="'
					. __( 'Never remind me!', $this->domain ) . '">
				</form>
				<form method="post" action="" style="float: right; margin-right: 10px;">
					<input type="hidden" name="' . $this->prefix . '_remind_me" value="true">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="'
					. __( 'Remind me in a few weeks...', $this->domain ) . '">
				</form>
				<form method="post" action="" style="float: right; margin-right: 10px;">
					<input type="hidden" name="' . $this->prefix . '_did_it" value="true">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="'
					. __( 'Yes, I did it!', $this->domain ) . '">
				</form>
				<div style="clear: both;"></div>
			</p>
			';
			echo '</div>';
		}

		function nice_short_url_from_file( $file ) {
			$info = pathinfo( $file );
			if ( !empty( $info ) ) {
				$info['filename'] = str_replace( '-pro', '', $info['filename'] );
				return $info['filename'];
			}
			return "";
		}

		function nice_name_from_file( $file ) {
			$info = pathinfo( $file );
			if ( !empty( $info ) ) {
				if ( $info['filename'] == 'wplr-sync' ) {
					return "Photo Engine";
				}
				$info['filename'] = str_replace( '-', ' ', $info['filename'] );
				$file = ucwords( $info['filename'] );
			}
			return $file;
		}
	}
}

?>
