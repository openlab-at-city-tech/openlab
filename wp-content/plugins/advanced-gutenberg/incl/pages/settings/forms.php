<?php
defined( 'ABSPATH' ) || die;

$website_title                  = get_option( 'blogname' );
$admin_email                    = get_option( 'admin_email' );

$contact_form_sender_name       = $this->getOptionSetting( 'advgb_email_sender', 'contact_form_sender_name', 'text', $website_title );
$contact_form_sender_email      = $this->getOptionSetting( 'advgb_email_sender', 'contact_form_sender_email', 'text', $admin_email );
$contact_form_email_title       = $this->getOptionSetting(
                                    'advgb_email_sender',
                                    'contact_form_email_title',
                                    'text',
                                    __( 'Website Contact', 'advanced-gutenberg' )
                                );
$contact_form_email_receiver    = $this->getOptionSetting(
                                    'advgb_email_sender',
                                    'contact_form_email_receiver',
                                    'text',
                                    $admin_email
                                );
?>
<form method="post">
    <?php wp_nonce_field( 'advgb_email_config_nonce', 'advgb_email_config_nonce_field' ) ?>
    <p>
        <?php
        _e(
            'These email settings apply to messages sent through PublishPress Contact form block.',
            'advanced-gutenberg'
        )
        ?>
    </p>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php _e( 'Sender name', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="text"
                           name="contact_form_sender_name"
                           id="contact_form_sender_name"
                           class="regular-text"
                           value="<?php esc_attr_e( $contact_form_sender_name ) ?>"
                    />
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Sender email', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="email"
                           name="contact_form_sender_email"
                           id="contact_form_sender_email"
                           class="regular-text"
                           value="<?php echo esc_attr( $contact_form_sender_email ) ?>"
                    />
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Email title', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="text"
                           name="contact_form_email_title"
                           id="contact_form_email_title"
                           class="regular-text"
                           value="<?php esc_attr_e( $contact_form_email_title ) ?>"
                    />
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Email receiver', 'advanced-gutenberg' ) ?>
            </th>
            <td>
                <label>
                    <input type="text"
                           name="contact_form_email_receiver"
                           id="contact_form_email_receiver"
                           class="regular-text"
                           value="<?php esc_attr_e( $contact_form_email_receiver ) ?>"
                    />
                </label>
            </td>
        </tr>
    </table>

    <div class="advgb-form-buttons-bottom">
        <button type="submit"
                class="button button-primary"
                id="save_email_config"
                name="save_email_config"
        >
            <?php esc_html_e( 'Save Form Settings', 'advanced-gutenberg' ) ?>
        </button>
    </div>
</form>
