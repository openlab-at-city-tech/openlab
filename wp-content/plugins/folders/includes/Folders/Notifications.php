<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Sends the test notification email from the Notifications settings tab.
 */
class Notifications {

    /**
     * Constructor. No hooks are registered; the class is used statically.
     */
    public function __construct() {

    }

    /**
     * Send a test email to check that notifications can be delivered.
     *
     * @param array $params Request parameters with `nonce` (for
     *                      `folders-email-test-notification`) and `email_id`
     *                      (recipient address).
     * @return array|\WP_Error Result array with `success` and `message`, or WP_Error
     *                         on invalid nonce or email address.
     */
    public static function send_test_notifications($params) {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $email_id = isset( $params['email_id'] ) ? sanitize_text_field( $params['email_id'] ) : '';

        if (empty($nonce) || empty($email_id) || ! wp_verify_nonce( $nonce, 'folders-email-test-notification' ) ) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        if (!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
            return new \WP_Error( 'error', esc_html__('Email address is not valid', 'folders'), array( 'status' => 403 ) );
        }

        $subject = esc_html__("Test Email", 'folders');
        $message = esc_html__("This is test email from Folders", 'folders');
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $status = wp_mail($email_id, $subject, $message, $headers);
        if ($status) {
            return array(
                'success'       => true,
                'message'       => esc_html__('Message sent successfully', 'folders')
            );
        }

        return array(
            'success'       => false,
            'message'       => esc_html__('Could not send email', 'folders')
        );
    }
}
