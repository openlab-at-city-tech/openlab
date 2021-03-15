<?php

class Cloudsponge_Integration {
	var $enabled;
	var $key;

	/**
	 * PHP 5 Constructor
	 *
	 * @package Invite Anyone
	 * @since 0.8
	 */
	function __construct() {

		if ( empty( $options ) )
			$options = get_option( 'invite_anyone' );

		$this->enabled = !empty( $options['cloudsponge_enabled'] ) ? $options['cloudsponge_enabled'] : false;
		$this->domain_key = !empty( $options['cloudsponge_key'] ) ? $options['cloudsponge_key'] : false;
		$this->account_key = !empty( $options['cloudsponge_account_key'] ) ? $options['cloudsponge_account_key'] : false;
		$this->sources = !empty( $options['cloudsponge_sources'] ) ? explode(",", $options['cloudsponge_sources']) : false;
		$this->deep_links = !empty( $options['cloudsponge_deep_links'] ) ? $options['cloudsponge_deep_links'] : false;

		if ( $this->enabled && ( $this->domain_key || $this->account_key ) ) {
			define( 'INVITE_ANYONE_CS_ENABLED', true );
			add_action( 'invite_anyone_after_addresses', array( $this, 'import_markup' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
		}
	}

	/**
	 * Registers and loads CS JS.
	 *
	 * @package Invite Anyone
	 * @since 0.8.8
	 */
	function enqueue_script() {

		// Values available in the JavaScript side
		$strings = array();

		if ($this->domain_key) {
			wp_register_script( 'ia_cloudsponge_address_books', 'https://api.cloudsponge.com/address_books.js', array(), false, true );
			wp_register_script( 'ia_cloudsponge', plugins_url() . '/invite-anyone/by-email/cloudsponge-js.js', array( 'ia_cloudsponge_address_books' ), false, true );
			$strings['domain_key'] = $this->domain_key;
			$strings['account_key'] = false;
		} else {
			wp_register_script( 'ia_cloudsponge', plugins_url() . '/invite-anyone/by-email/cloudsponge-js.js', array(), false, true );
			$strings['account_key'] = $this->account_key;
			$strings['domain_key'] = false;
		}

		if ( $locale = apply_filters( 'ia_cloudsponge_locale', '' ) ) {
			$strings['locale'] = $locale;
		}

		if ( $stylesheet = apply_filters( 'ia_cloudsponge_stylesheet', '' ) ) {
			$strings['stylesheet'] = $stylesheet;
		}

		$strings['sources'] = $this->sources;

		wp_localize_script( 'ia_cloudsponge', 'ia_cloudsponge', $strings );
	}

	/**
	 * Inserts the Cloudsponge markup into the Send Invites front end page.
	 *
	 * Also responsible for enqueuing the necessary assets.
	 *
	 * @package Invite Anyone
	 * @since 0.8
	 *
	 * @param array $options Invite Anyone settings. Check em so we can bail if necessary
	 */
	function import_markup( $options = false ) {
		wp_enqueue_script( 'ia_cloudsponge' );

		?>

		<input type="hidden" id="cloudsponge-emails" name="cloudsponge-emails" value="" />

		<?php if ( ! $this->deep_links ) : ?>
			<a class="cs_import"><?php esc_html_e( 'You can also add email addresses from your Address Book.', 'invite-anyone' ); ?></a>
		<?php else : ?>
			<?php $sourcesList = self::sources_list(); ?>

			<?php esc_html_e( 'You can also add email addresses from one of the following address books:', 'invite-anyone' ); ?>

			<?php
			$sourcesDisplay = [];
			foreach ( $this->sources as $source ) {
				if ( ! isset( $sourcesList[ $source ] ) ) {
					continue;
				}

				$sourcesDisplay[] = '<a class="cloudsponge-launch" data-cloudsponge-source="' . esc_attr( $source ) . '">' . esc_html( $sourcesList[ $source ]['name'] ) . '</a>';
			}

			echo implode( ', ', $sourcesDisplay );
			?>
		<?php endif;
	}

	public static function sources_list() {
		$sources = get_transient( 'cloudsponge-services' );
		if ( false === $sources ) {
			$sources = json_decode( file_get_contents( 'https://api.cloudsponge.com/services.json' ), true );

			if ( $sources ) {
				set_transient( 'cloudsponge-services', $sources, HOUR_IN_SECONDS );
			} else {
				$sources = [];
			}
		}

		return $sources;
	}
}
$cloudsponge_integration = new Cloudsponge_Integration;
