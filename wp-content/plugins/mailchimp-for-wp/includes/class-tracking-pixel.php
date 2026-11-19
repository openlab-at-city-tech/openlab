<?php

defined('ABSPATH') || exit;


/**
 * Outputs the Mailchimp Site Tracking Pixel SDK script
 * and identifies subscribers after successful form submissions.
 *
 * @since 4.13.0
 * @access private
 * @ignore
 */
class MC4WP_Tracking_Pixel
{
    /**
     * @var string The foreign_id / site_id used to identify the connected site in Mailchimp.
     */
    private $site_id;

    /**
     * @var string Email address of a subscriber to identify, set during form processing.
     */
    private $identify_email = '';

    /**
     * @param string $site_id The connected site ID stored in the plugin options.
     */
    public function __construct(string $site_id)
    {
        $this->site_id = $site_id;
    }

    /**
     * Register hooks for outputting the tracking pixel.
     *
     * @return void
     */
    public function add_hooks(): void
    {
        add_action('wp_head', [$this, 'output_tracking_script']);
        add_action('mc4wp_form_subscribed', [$this, 'capture_subscriber_email'], 10, 2);
        add_action('wp_footer', [$this, 'output_identify_script'], 99);
    }

    /**
     * Output the Mailchimp Site Tracking Pixel SDK script tag.
     *
     * @return void
     */
    public function output_tracking_script(): void
    {
        if (empty($this->site_id)) {
            return;
        }

        $opts = mc4wp_get_options();
        if (empty($opts['tracking_pixel_script_url'])) {
            return;
        }

        wp_enqueue_script('mc4wp-mailchimp-site-tracking-pixel', $opts['tracking_pixel_script_url'], [], MC4WP_VERSION, [
            'strategy' => 'defer',
        ]);
    }

    /**
     * Capture the subscriber email after a successful form subscription.
     *
     * Fires during the mc4wp_form_subscribed action hook.
     *
     * @since 4.13.0
     *
     * @param MC4WP_Form $form  The submitted form instance.
     * @param string     $email The subscriber's email address.
     * @return void
     */
    public function capture_subscriber_email($form, string $email): void
    {
        $this->identify_email = $email;
    }

    /**
     * Output inline script to identify the subscriber via the Mailchimp pixel SDK.
     *
     * Only outputs when a subscriber email was captured during this request.
     *
     * @since 4.13.0
     *
     * @return void
     */
    public function output_identify_script(): void
    {
        if (empty($this->identify_email) || empty($this->site_id)) {
            return;
        }

        echo '<script>';
        echo 'if(window.$mcSite&&window.$mcSite.pixel&&window.$mcSite.pixel.api){';
        echo 'window.$mcSite.pixel.api.identify({type:"EMAIL",value:"' . esc_js($this->identify_email) . '"});';
        echo '}';
        echo '</script>' . "\n";
    }

    /**
     * Auto-fetch an existing Mailchimp Connected Site that matches the current domain,
     * or create a new one if none is found.
     *
     * @since 4.13.0
     *
     * @param string $api_key  API key to use. Defaults to the configured one, pass this explicitly when
     *                         the key is about to change since the API service holds the stored key.
     *
     * @return array{site_id: string, script_url: string}|false  Array with site data on success, false on failure.
     */
    public static function fetch_or_create_connected_site(string $api_key = '')
    {
        try {
            /** @var MC4WP_API_V3 $api */
            $api          = $api_key === '' ? mc4wp_get_service('api') : new MC4WP_API_V3($api_key);
            $foreign_id   = self::get_foreign_id();
            $domain       = self::get_site_domain();
            $matched_site = null;

            // First, look the site up by its foreign_id directly. The e-commerce add-on registers a
            // connected site using the store ID, so this normally hits on the first try.
            try {
                $site = $api->get_connected_site($foreign_id);

                if (isset($site->domain) && self::normalize_domain($site->domain) === $domain) {
                    $matched_site = $site;
                } else {
                    // The ID belongs to a different domain, so using its script would track the wrong
                    // site. Make the ID unique to this domain in case we end up registering below.
                    $foreign_id .= '-' . substr(md5($domain), 0, 8);
                }
            } catch (MC4WP_API_Resource_Not_Found_Exception $e) {
                // No connected site registered under this ID yet.
            }

            // Otherwise, look for an existing site registered under the same domain.
            if (null === $matched_site) {
                foreach ($api->get_connected_sites() as $site) {
                    $site_domain = isset($site->domain) ? self::normalize_domain($site->domain) : '';
                    if ('' !== $site_domain && $site_domain === $domain) {
                        $matched_site = $site;
                        break;
                    }
                }
            }

            // Still nothing, so register a new connected site. Mailchimp rejects a second site for a
            // domain that is already connected, which is why both lookups above run first.
            if (null === $matched_site) {
                $matched_site = $api->add_connected_site([
                    'foreign_id' => $foreign_id,
                    'domain'     => $domain,
                ]);
            }

            return [
                'site_id'    => sanitize_text_field($matched_site->foreign_id ?? $matched_site->id ?? ''),
                'script_url' => esc_url_raw($matched_site->site_script->url ?? ''),
            ];
        } catch (Exception $e) {
            // Cast the exception to string so Mailchimp's own error detail ends up in the log,
            // instead of just the HTTP status message.
            mc4wp_get_service('log')->error(sprintf('Tracking Pixel: error fetching/creating connected site. %s', (string) $e));
            return false;
        }
    }

    /**
     * Returns the domain to register this site under in Mailchimp.
     *
     * @return string
     */
    public static function get_site_domain(): string
    {
        $home_url = get_home_url();
        $domain   = self::normalize_domain($home_url);
        $path     = trim((string) wp_parse_url($home_url, PHP_URL_PATH), '/');

        // Mailchimp strips the subdirectory part off a domain, which would make every site in a
        // subdirectory network resolve to the same domain. Turn each path segment into a hostname
        // label instead, deepest first, so /shop becomes shop.example.com like the e-commerce add-on
        // registers it and a nested /network/shop becomes shop.network.example.com.
        if ($path !== '' && is_multisite()) {
            $labels = array_map([ __CLASS__, 'to_hostname_label' ], array_reverse(explode('/', $path)));
            $labels = array_filter($labels);

            if (! empty($labels)) {
                $domain = implode('.', $labels) . '.' . $domain;
            }
        }

        return $domain;
    }

    /**
     * Turns a single URL path segment into something usable as a hostname label.
     *
     * @param string $segment
     * @return string
     */
    private static function to_hostname_label(string $segment): string
    {
        $label = strtolower(rawurldecode($segment));
        $label = (string) preg_replace('/[^a-z0-9-]+/', '-', $label);
        $label = trim((string) preg_replace('/-+/', '-', $label), '-');
        return substr($label, 0, 63);
    }

    /**
     * Reduces a URL or domain to the bare hostname Mailchimp stores it as, so that two spellings of
     * the same site compare equal. Strips the protocol, port, path and the www. prefix.
     *
     * @param string $url
     * @return string
     */
    private static function normalize_domain(string $url): string
    {
        $url = trim($url);

        // Add a scheme-relative prefix so the host is parsed as such for bare domains too.
        if (strpos($url, '//') === false) {
            $url = '//' . ltrim($url, '/');
        }

        $host = strtolower((string) wp_parse_url($url, PHP_URL_HOST));

        if (strpos($host, 'www.') === 0) {
            $host = substr($host, 4);
        }

        return $host;
    }

    /**
     * Returns a foreign_id to use when registering a new connected site.
     * Reuses the e-commerce store ID when available so Mailchimp can link them.
     *
     * @return string
     */
    private static function get_foreign_id(): string
    {
        // Read through the add-on's own getter when available, because it fills in a default store ID
        // at runtime that is not always written back to the option.
        if (function_exists('mc4wp_ecommerce_get_settings')) {
            $ecommerce_settings = mc4wp_ecommerce_get_settings();
        } else {
            $ecommerce_settings = get_option('mc4wp_ecommerce', []);
        }

        if (is_array($ecommerce_settings) && ! empty($ecommerce_settings['store_id'])) {
            return (string) $ecommerce_settings['store_id'];
        }

        // sanitize_title() percent-encodes names without any latin characters, so strip anything
        // that is not safe to use as an identifier.
        $slug = substr((string) preg_replace('/[^a-z0-9-]/', '', sanitize_title(get_bloginfo('name'))), 0, 32);
        if ($slug === '') {
            $slug = 'site';
        }

        return 'mc4wp-' . $slug . '-' . get_current_blog_id();
    }
}
