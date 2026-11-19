<?php

if (!defined('ABSPATH')) {
    die('No direct access.');
}

if (! class_exists('MetaSlider_Email_Collection')) {
    /**
     * Sends the opt-in email address to MetaSlider's connect service, which
     * forwards it on to our mailing list provider. Replaces the old AppSero
     * based opt-in tracking (see #1869, #2150).
     */
    class MetaSlider_Email_Collection
    {
        /**
         * Shared secret sent to the connect service. This ships inside a public
         * plugin so it is not a real secret - it only keeps casual traffic out.
         * The connect service rate limits on top of this.
         */
        const CONNECT_KEY = '803ee115a6335b218396b35c7e2fc4ca';

        /**
         * Identifies this product to the connect service, which uses it to pick
         * the Mailjet list. Other MetaSlider plugins send their own slug.
         */
        const PLUGIN_SLUG = 'ml-slider';

        /**
         * Helper method for checking whether the site has opted in
         *
         * @return boolean
         */
        public static function site_is_optin()
        {
            $settings = get_option('metaslider_global_settings');
            return isset($settings['optIn']) && filter_var($settings['optIn'], FILTER_VALIDATE_BOOLEAN);
        }

        /**
         * Whether the current user saw an opt-in prompt before 3.114.0 and did not share their email
         *
         * @since 3.114.0
         *
         * @return boolean
         */
        public static function is_existing_user()
        {
            // Legacy flag, no longer written - never reuse or delete it, or the Mailjet segment changes
            $status = get_user_option('metaslider_analytics_onboarding_status');
            return $status && 'yes' !== $status;
        }

        /**
         * Transient name for the pause after a failed send of this address
         *
         * @since 3.114.0
         *
         * @param string $email The opt-in email address
         *
         * @return string
         */
        public static function retry_cooldown_key($email)
        {
            return 'metaslider_optin_retry_cooldown_' . md5($email);
        }

        /**
         * Send the opted-in email address to the connect service, which adds
         * it to the mailing list immediately.
         *
         * Deduplicates on the address rather than on the opt-in flag, so that
         * re-saving settings doesn't send again, but changing the address does.
         * A failed handoff isn't recorded as sent, so a later save retries it -
         * but only after a short cooldown, not on every single call in between.
         *
         * This is called on every save where optIn is true, not just the
         * opt-in modal's own submission - e.g. the general Settings page
         * re-sends optIn on every unrelated toggle. The modal always clears
         * the marker right before calling this, so 'duplicate' is what fires
         * on those other, unrelated saves instead.
         *
         * @return array - a report of what was attempted. 'status' is one of
         *                 'subscribed', 'duplicate', 'invalid' or 'failed'.
         *                 'duplicate' is a success case for the UI: the address
         *                 was already handed off, so nothing was sent again.
         *
         * @since 3.112.0
         * @since 3.114.0 Send a segment for existing users, so they can get their own Mailjet tag.
         */
        public static function optin()
        {
            $email = get_option('metaslider_optin_email');

            // No silent fallback to the current user's own address here. This
            // is called on every save while optIn is true, potentially by a
            // different admin than whoever originally opted in - substituting
            // their address without asking would subscribe someone who never
            // agreed to it.
            if (! is_email($email)) {
                return array(
                    'status'  => 'invalid',
                    'message' => 'No valid email address to send.',
                );
            }

            if (get_option('metaslider_optin_email_sent') === $email) {
                return array(
                    'status'  => 'duplicate',
                    'message' => 'This address was already handed off, so nothing was sent again.',
                    'sent'    => array('plugin' => self::PLUGIN_SLUG, 'email' => $email),
                );
            }

            // This is called on every save while optIn is true, not just the
            // one that's actually trying to subscribe - so a real outage would
            // otherwise mean every future settings save (by anyone, for any
            // unrelated toggle) blocks for the full 10s timeout, retrying the
            // same failed call, until the connect service comes back. Skip
            // the network call entirely while a recent attempt is on cooldown.
            $cooldown_key = self::retry_cooldown_key($email);

            if (get_transient($cooldown_key)) {
                return array(
                    'status'  => 'failed',
                    'message' => 'A previous attempt failed recently - not retrying immediately.',
                );
            }

            $on_failure = function ($report) use ($cooldown_key) {
                set_transient($cooldown_key, 1, 60);
                return $report;
            };

            // The connect service that forwards the data on to Mailjet (#2150).
            // Filterable so it can be pointed elsewhere for testing.
            $endpoint = apply_filters(
                'metaslider_connect_endpoint',
                'https://connect.metaslider.com/wp-json/connect/v1/subscribe'
            );

            $payload = array(
                'plugin' => self::PLUGIN_SLUG,
                'email'  => $email,
            );

            // Lets the connect service tag existing users in Mailjet. New users get no segment.
            if (self::is_existing_user()) {
                $payload['segment'] = 'existing';
            }

            $response = wp_remote_post($endpoint, array(
                // The connect service calls Mailjet's Send API before it
                // answers, so this round trip includes dispatching an email.
                // Three seconds wasn't enough and timed out mid-send, leaving
                // the user with a failure message and an email in their inbox.
                // phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
                'timeout' => 10,
                'headers' => array(
                    'X-MetaSlider-Connect-Key' => apply_filters('metaslider_connect_key', self::CONNECT_KEY),
                ),
                // The plugin slug picks the Mailjet list on the connect service
                'body'    => $payload,
            ));

            // Everything below reports back what happened, so the caller can
            // see the outcome without digging through server logs
            $report = array(
                'status'   => 'failed',
                'endpoint' => $endpoint,
                'sent'     => $payload,
            );

            if (is_wp_error($response)) {
                $report['message'] = 'The connect service could not be reached.';
                $report['error'] = $response->get_error_message();
                return $on_failure($report);
            }

            $report['http_code'] = (int) wp_remote_retrieve_response_code($response);

            // A 200 on its own isn't proof the service got this. A misrouted
            // REST request can return the site's HTML with a 200, so require
            // the JSON success payload before recording the handoff.
            $body = json_decode(wp_remote_retrieve_body($response), true);

            if (!is_array($body)) {
                $report['message'] = 'The connect service did not return JSON. Check the endpoint URL.';
                return $on_failure($report);
            }

            // Whatever the service reported about Mailjet, passed straight through
            $report['connect'] = isset($body['data']) ? $body['data'] : $body;

            if (200 !== $report['http_code'] || empty($body['success'])) {
                // Prefer the service's own explanation - "Too many requests"
                // is a lot more useful than a generic rejection
                $reason = isset($body['data']) && is_string($body['data']) ? $body['data'] : '';

                $report['message'] = $reason
                    ? 'The connect service rejected the request: ' . $reason
                    : 'The connect service rejected the request.';

                return $on_failure($report);
            }

            update_option('metaslider_optin_email_sent', $email, true);

            $report['status'] = 'subscribed';
            $report['message'] = 'Subscribed. The address is on the mailing list now.';

            return $report;
        }
    }
}
