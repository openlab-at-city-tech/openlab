<?php

defined( 'ABSPATH' ) || die;

$contactform_saved = get_option( 'advgb_contacts_saved' );
$contacts_count    = $contactform_saved ? count( $contactform_saved ) : 0;
$newsletter_saved  = get_option( 'advgb_newsletter_saved' );
$newsletter_count  = $newsletter_saved ? count( $newsletter_saved ) : 0;
?>
<form method="POST" id="export-block-data-form">
	<?php
	wp_nonce_field( 'advgb_export_data_nonce', 'advgb_export_data_nonce_field' ) ?>
    <table class="form-table">
        <tr>
            <th scope="row" style="width: 300px;">
                <label>
					<?php
					_e( 'Download Contacts Form data', 'advanced-gutenberg' ) ?>
                    (<?php
					echo esc_html( $contacts_count ) ?>)
                </label>
                <p class="description">
					<?php
					_e( 'Data stored through Contact form blocks.', 'advanced-gutenberg' ) ?>
                </p>
            </th>
            <td>
                <label>
                    <button type="submit"
                            class="button advgb-export-download"
                            name="block_data_export"
                            value="contact_form.csv"
						<?php
						echo ( esc_html( (int) $contacts_count ) == 0 ) ? ' disabled' : '' ?>
                    >
                        <span class="dashicons dashicons-download"></span>
						<?php
						esc_html_e( 'CSV', 'advanced-gutenberg' ); ?>
                    </button>
                    <button type="submit"
                            class="button advgb-export-download"
                            name="block_data_export"
                            value="contact_form.json"
						<?php
						echo ( esc_html( (int) $contacts_count ) == 0 ) ? ' disabled' : '' ?>
                    >
                        <span class="dashicons dashicons-download"></span>
						<?php
						esc_html_e( 'JSON', 'advanced-gutenberg' ); ?>
                    </button>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row" style="width: 300px;">
                <label>
					<?php
					_e( 'Download Newsletter Form data', 'advanced-gutenberg' ) ?>
                    (<?php
					echo esc_html( $newsletter_count ) ?>)
                </label>
                <p class="description">
					<?php
					_e( 'Data stored through Newsletter form blocks.', 'advanced-gutenberg' ) ?>
                </p>
            </th>
            <td>
                <label>
                    <button type="submit"
                            class="button advgb-export-download"
                            name="block_data_export"
                            value="newsletter.csv"
						<?php
						echo ( esc_html( (int) $newsletter_count ) == 0 ) ? ' disabled' : '' ?>
                    >
                        <span class="dashicons dashicons-download"></span>
						<?php
						esc_html_e( 'CSV', 'advanced-gutenberg' ); ?>
                    </button>
                    <button type="submit"
                            class="button advgb-export-download"
                            name="block_data_export"
                            value="newsletter.json"
						<?php
						echo ( esc_html( (int) $newsletter_count ) == 0 ) ? ' disabled' : '' ?>
                    >
                        <span class="dashicons dashicons-download"></span>
						<?php
						esc_html_e( 'JSON', 'advanced-gutenberg' ); ?>
                    </button>
                </label>
            </td>
        </tr>
    </table>
</form>
