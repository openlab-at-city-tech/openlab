<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Sends support and feedback messages to the Premio support team.
 *
 * Messages are posted to Premio's Crisp endpoint together with the site
 * domain, plugin, WordPress and PHP versions.
 */
class FoldersHelp {

    /**
     * Send the deactivation feedback form to Premio support.
     *
     * Retries without SSL verification if the first request fails.
     *
     * @param array $params Request parameters with `nonce` (for `folder_feedback_nonce`),
     *                      `email` and `message`.
     * @return array|\WP_Error Result array (with `field_errors` when validation fails),
     *                         or WP_Error on invalid nonce.
     */
    public static function feedback_form($params) {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';

        if(empty($nonce) || !wp_verify_nonce($nonce, 'folder_feedback_nonce')) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $email = isset( $params['email'] ) ? sanitize_text_field( $params['email'] ) : '';
        $message = isset( $params['message'] ) ? sanitize_text_field( $params['message'] ) : '';

        $errors = [];
        if(empty($email)) {
            $errors[] = [
                'id' => 'email',
                'message' => esc_html__('Email is required', 'folders'),
            ];
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = [
                'id' => 'email',
                'message' => esc_html__('Email address is not valid', 'folders')
            ];
        }

        if(empty($message)) {
            $errors[] = [
                'id' => 'message',
                'message' => esc_html__('Please enter your message', 'folders'),
            ];
        }

        if(!empty($errors)) {
            return array(
                'success'       => false,
                'message'       => esc_html__('Please correct the errors in the form', 'folders'),
                'field_errors'  => $errors,
            );
        }

        $domain       = site_url();
        $current_user = wp_get_current_user();
        $user_name    = $current_user->first_name." ".$current_user->last_name;

        // sending message to Crisp
        $post_message = [];

        $message_data          = [];
        $message_data['key']   = "Plugin";
        $message_data['value'] = "Folders";
        $post_message[]        = $message_data;

        $message_data          = [];
        $message_data['key']   = "Plugin Version";
        $message_data['value'] = FOLDERS_VERSION;
        $post_message[]        = $message_data;

        $message_data          = [];
        $message_data['key']   = "Domain";
        $message_data['value'] = $domain;
        $post_message[]        = $message_data;

        $message_data          = [];
        $message_data['key']   = "Email";
        $message_data['value'] = $email;
        $post_message[]        = $message_data;

        $message_data          = [];
        $message_data['key']   = "WordPress Version";
        $message_data['value'] = esc_attr(get_bloginfo('version'));
        $post_message[]        = $message_data;

        $message_data          = [];
        $message_data['key']   = "PHP Version";
        $message_data['value'] = PHP_VERSION;
        $post_message[]        = $message_data;

        $message_data          = [];
        $message_data['key']   = "Message";
        $message_data['value'] = $message;
        $post_message[]        = $message_data;

        $api_params = [
            'domain'  => $domain,
            'email'   => $email,
            'url'     => site_url(),
            'name'    => $user_name,
            'message' => $post_message,
            'plugin'  => "Folders",
            'type'    => "Uninstall",
        ];

        // Sending message to Crisp API
        $crisp_response = wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", ['body' => $api_params, 'timeout' => 15, 'sslverify' => true]);

        return array(
            'success'       => true,
            'message'       => esc_html__('Your message is sent successfully.', 'folders')
        );
    }

    /**
     * Send a "Need Help" message from the help form to Premio support.
     *
     * Retries without SSL verification if the first request fails.
     *
     * @param array $params Request parameters with `nonce` (for `folders-contact-form`),
     *                      `email` and `message`.
     * @return array|\WP_Error Result array (with `field_errors` when validation fails),
     *                         or WP_Error on invalid nonce.
     */
    public static function send_message($params) {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';


        if(empty($nonce) || !wp_verify_nonce($nonce, 'folders-contact-form')) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $email = isset( $params['email'] ) ? sanitize_text_field( $params['email'] ) : '';
        $message = isset( $params['message'] ) ? sanitize_text_field( $params['message'] ) : '';

        $errors = [];
        if(empty($email)) {
            $errors[] = [
                'id' => 'email',
                'message' => esc_html__('Email is required', 'folders'),
            ];
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = [
                'id' => 'email',
                'message' => esc_html__('Email address is not valid', 'folders')
            ];
        }

        if(empty($message)) {
            $errors[] = [
                'id' => 'message',
                'message' => esc_html__('Please enter your message', 'folders'),
            ];
        }

        if(!empty($errors)) {
            return array(
                'success'       => false,
                'message'       => esc_html__('Please correct the errors in the form', 'folders'),
                'field_errors'  => $errors,
            );
        }

        $request = [
            [
                'key' => 'Plugin',
                'value' => 'Folders (Pro)',
            ],
            [
                'key' => 'Domain',
                'value' => esc_url(site_url()),
            ],
            [
                'key' => 'Email',
                'value' => $email,
            ],
            [
                'key' => 'Message',
                'value' => $message,
            ]
        ];

        global $current_user;
        $user_name = trim($current_user->first_name . " " . $current_user->last_name);
        if(empty($user_name)) {
            $user_name = $email;
        }

        $api_params = [
            'domain' => esc_url(site_url()),
            'email' => $email,
            'url' => site_url(),
            'name' => $user_name,
            'message' => $request,
            'plugin' => "Folders (Pro)",
            'type' => "Need Help",
        ];

        $crisp_response = wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", ['body' => $api_params, 'timeout' => 15, 'sslverify' => true]);

        return array(
            'success'       => true,
            'message'       => esc_html__('Your message is sent successfully.', 'folders')
        );
    }
}
