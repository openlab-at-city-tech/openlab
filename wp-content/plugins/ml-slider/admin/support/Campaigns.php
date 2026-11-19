<?php

if (!defined('ABSPATH')) {
    die('No direct access.');
}

if (! class_exists('MetaSlider_Campaigns')) {
    /**
     * Shows promotional campaigns that ship with the plugin (#2605).
     *
     * Campaigns are written in admin/support/campaign-list.php, so their text
     * can be translated, and go out with each release, so the plugin never
     * contacts a server for them (WordPress.org guideline 7). Images go in
     * admin/assets/campaigns/ (create it for the first one) and are referenced
     * as {campaign_assets}/<file>.
     * This class reads that list, checks each campaign's dates and how long the
     * site has used MetaSlider, and remembers the site's answer. Campaigns are
     * for free users only: none shows while MetaSlider Pro is installed,
     * active or not, but a site that deleted Pro gets them again.
     *
     * One campaign in campaign-list.php:
     *   array(
     *     'slug' => 'pro-75-off-2026',         // unique, never reused: answers are saved per slug
     *     'starts_at' => '2026-11-20T00:00:00+00:00',  // or null
     *     'ends_at' => '2026-12-01T00:00:00+00:00',    // or null
     *     'snooze_days' => 84,                 // how long "Maybe later" hides it
     *     'rules' => array('min_usage_days' => 90),
     *     'content' => array(
     *       'headline' => __('Thanks for using MetaSlider!', 'ml-slider'),
     *       'badge' => __('Limited time offer', 'ml-slider'),  // optional, a red tag in the corner
     *       'intro' => __('Get Slideshow Pro for', 'ml-slider'),  // optional, shown above the price; "\n" starts a new line
     *       'price' => '$29.50',                // optional: intro, price and features show as a price box
     *       'old_price' => '$79',               // optional, crossed out
     *       'features' => array(__('More themes', 'ml-slider')),  // optional, up to 8
     *       'body' => '<img src="{campaign_assets}/offer.png" alt=""> ' . __('Any other offer text', 'ml-slider'),  // optional, below the price box
     *       'cta_label' => __('Claim my discount', 'ml-slider'),
     *       'cta_url' => 'https://www.metaslider.com/',
     *       'later_label' => __('Maybe later', 'ml-slider'),  // optional, this is the default
     *     ),
     *   )
     *
     * The headline and offer allow simple formatting only (see allowed_html()),
     * and the offer allows images from the campaigns folder. Links must be https.
     *
     * @since 3.114.1
     */
    class MetaSlider_Campaigns
    {
        const STATE_OPTION = 'metaslider_campaign_state';
        const LIST_FILE = 'admin/support/campaign-list.php';
        const ASSETS_PLACEHOLDER = '{campaign_assets}';
        const MAX_CAMPAIGNS = 10;
        const MAX_STATE_ENTRIES = 50;
        const CAPABILITY = 'activate_plugins';
        const PRO_CACHE = 'metaslider_campaign_has_pro';

        /**
         * Character limits for each text. The headline and offer count their HTML too.
         */
        const TEXT_LIMITS = array(
            'headline' => 300,
            'badge' => 40,
            'intro' => 100,
            'price' => 20,
            'old_price' => 20,
            'body' => 2000,
            'cta_label' => 40,
            'later_label' => 40,
        );

        /**
         * At most this many features, each up to FEATURE_LIMIT characters.
         */
        const MAX_FEATURES = 8;
        const FEATURE_LIMIT = 60;

        /**
         * The texts that keep formatting HTML.
         */
        const RICH_TEXTS = array('headline', 'body');

        /**
         * Answers the popup can record.
         */
        const RESPONSES = array('claimed', 'later', 'dismissed');

        /**
         * Oldest slideshow time, per request.
         *
         * @var int|false|null
         */
        private static $oldest_slideshow = null;

        /**
         * The parsed campaigns, per request.
         *
         * @var array|null
         */
        private static $bundle = null;

        /**
         * The campaigns shipped with this release, validated.
         *
         * @return array
         */
        public static function campaigns()
        {
            if (null !== self::$bundle) {
                return self::$bundle;
            }

            self::$bundle = self::parse(include METASLIDER_PATH . self::LIST_FILE);

            return self::$bundle;
        }

        /**
         * Validates the campaign list.
         *
         * @param mixed $items - the campaigns from campaign-list.php
         * @return array - the valid campaigns
         */
        public static function parse($items)
        {
            if (! is_array($items)) {
                return array();
            }

            $campaigns = array();
            $slugs = array();
            foreach ($items as $item) {
                $campaign = self::sanitize_campaign($item);

                // Answers are saved per slug, so a repeated one would share another campaign's answer
                if ($campaign && ! isset($slugs[$campaign['slug']])) {
                    $slugs[$campaign['slug']] = true;
                    $campaigns[] = $campaign;
                }

                if (count($campaigns) >= self::MAX_CAMPAIGNS) {
                    break;
                }
            }

            return $campaigns;
        }

        /**
         * Validates one campaign. Unknown fields are dropped and any invalid required field rejects it.
         *
         * @param mixed $item - one campaign
         * @return array|null
         */
        public static function sanitize_campaign($item)
        {
            if (! is_array($item) || ! isset($item['slug']) || ! self::is_valid_slug($item['slug'])) {
                return null;
            }

            $content = isset($item['content']) && is_array($item['content']) ? $item['content'] : array();
            $texts = array();
            foreach (self::TEXT_LIMITS as $key => $max) {
                $value = isset($content[$key]) && is_string($content[$key]) ? $content[$key] : '';

                if (mb_strlen($value) > $max) {
                    // Cutting HTML can split a tag, so formatted text rejects the campaign instead
                    if (in_array($key, self::RICH_TEXTS, true)) {
                        return null;
                    }

                    // A long translation of plain text is cut rather than hiding the campaign in that language
                    $value = mb_substr($value, 0, $max);
                }

                if (in_array($key, self::RICH_TEXTS, true)) {
                    $texts[$key] = self::clean_html($value, $key);
                } else {
                    // The intro keeps its line breaks, so a translation can break the line where it reads best
                    $texts[$key] = 'intro' === $key ? sanitize_textarea_field($value) : sanitize_text_field($value);
                }
            }

            // A headline that is only tags shows nothing, so it counts as missing
            if ('' === trim(wp_strip_all_tags($texts['headline']))) {
                $texts['headline'] = '';
            }

            $features = self::sanitize_features($content);
            if (null === $features) {
                return null;
            }
            $texts['features'] = $features;

            if ('' === $texts['later_label']) {
                $texts['later_label'] = __('Maybe later', 'ml-slider');
            }

            $url = isset($content['cta_url']) && is_string($content['cta_url']) ? self::https_url($content['cta_url']) : '';

            if ('' === $texts['headline'] || '' === $texts['cta_label'] || '' === $url) {
                return null;
            }

            $texts['cta_url'] = $url;

            $starts_at = self::parse_time($item, 'starts_at');
            $ends_at = self::parse_time($item, 'ends_at');

            if (false === $starts_at || false === $ends_at) {
                return null;
            }

            $rules = isset($item['rules']) && is_array($item['rules']) ? $item['rules'] : array();

            return array(
                'slug' => $item['slug'],
                'starts_at' => $starts_at,
                'ends_at' => $ends_at,
                'snooze_days' => self::int_in_range($item, 'snooze_days', 1, 365, 84),
                'rules' => array(
                    'min_usage_days' => self::int_in_range($rules, 'min_usage_days', 0, 3650, 0),
                ),
                'content' => $texts,
            );
        }

        /**
         * The campaign to show on this page load, if any.
         *
         * @param int|null $now - the current time, for tests
         * @return array|null
         */
        public static function current($now = null)
        {
            $now = null === $now ? time() : (int) $now;

            // Force it on to re-test the popup, e.g. add_filter('metaslider_always_show_campaign', '__return_true');
            $always = apply_filters('metaslider_always_show_campaign', false);

            foreach (self::campaigns() as $campaign) {
                if ($always || self::is_eligible($campaign, $now)) {
                    return $campaign;
                }
            }

            return null;
        }

        /**
         * What the slideshow editor needs to show the current campaign.
         *
         * @return array|false
         */
        public static function for_editor()
        {
            // The answer counts for the whole site, so only someone who could turn on the upgrade sees it; unlike install_plugins, DISALLOW_FILE_MODS doesn't remove this
            if (! current_user_can(self::CAPABILITY)) {
                return false;
            }

            $campaign = self::current();

            if (! $campaign) {
                return false;
            }

            return array(
                'slug' => $campaign['slug'],
                'content' => $campaign['content'],
            );
        }

        /**
         * The campaign for one slideshow editor page load: none when the email sign-up popup shows first or there is no slideshow yet.
         *
         * @param int $slider_id - the slideshow being edited, 0 for none
         * @param bool $show_optin - whether the email sign-up popup shows on this page load
         * @return array|false
         */
        public static function for_editor_page($slider_id, $show_optin)
        {
            // The editor shows one popup per page load and needs a slideshow, so the campaign is only worked out when it can show
            if ($show_optin || ! $slider_id) {
                return false;
            }

            return self::for_editor();
        }

        /**
         * Whether a campaign is inside its dates, passes the local rules, hasn't been answered and the site is free-only.
         *
         * @param array $campaign - a validated campaign
         * @param int $now - the current time
         * @return bool
         */
        public static function is_eligible($campaign, $now)
        {
            if ($campaign['starts_at'] && $campaign['starts_at'] > $now) {
                return false;
            }

            if ($campaign['ends_at'] && $campaign['ends_at'] <= $now) {
                return false;
            }

            if (! self::is_open($campaign['slug'], $now) || self::has_pro()) {
                return false;
            }

            return self::used_for_days($campaign['rules']['min_usage_days'], $now);
        }

        /**
         * Whether the site still has to answer a campaign.
         *
         * @param string $slug - the campaign slug
         * @param int $now - the current time
         * @return bool
         */
        public static function is_open($slug, $now)
        {
            $state = self::state();

            if (! isset($state[$slug]['status'])) {
                return true;
            }

            // "Maybe later" brings it back after the snooze; the other answers are final
            if ('later' === $state[$slug]['status']) {
                return isset($state[$slug]['until']) && $now >= (int) $state[$slug]['until'];
            }

            return false;
        }

        /**
         * Records the site's answer. Only campaigns in this release are accepted.
         *
         * @param string $slug - the campaign slug
         * @param string $response - one of RESPONSES
         * @param int|null $now - the current time, for tests
         * @return bool
         */
        public static function respond($slug, $response, $now = null)
        {
            $now = null === $now ? time() : (int) $now;

            if (! self::is_valid_slug($slug) || ! in_array($response, self::RESPONSES, true)) {
                return false;
            }

            $campaign = self::find($slug);
            if (! $campaign) {
                return false;
            }

            $entry = array('status' => $response, 'at' => $now);
            if ('later' === $response) {
                $entry['until'] = $now + $campaign['snooze_days'] * DAY_IN_SECONDS;
            }

            $state = self::state();

            // A final answer stays final, e.g. "Maybe later" in a second tab can't undo "claimed"
            if (isset($state[$slug]['status']) && in_array($state[$slug]['status'], array('claimed', 'dismissed'), true)) {
                return true;
            }

            unset($state[$slug]);
            $state[$slug] = $entry;

            $state = self::prune($state);

            update_option(self::STATE_OPTION, $state, false);

            return true;
        }

        /**
         * @return array - [campaign slug => ['status' => string, 'at' => int, 'until' => int]]
         */
        public static function state()
        {
            $state = get_option(self::STATE_OPTION, array());

            return is_array($state) ? $state : array();
        }

        /**
         * Keeps the saved answers under MAX_STATE_ENTRIES, dropping campaigns that no longer ship first,
         * then the oldest snoozes. Final answers for shipped campaigns are never dropped.
         *
         * @param array $state - the answers
         * @return array
         */
        private static function prune($state)
        {
            if (count($state) <= self::MAX_STATE_ENTRIES) {
                return $state;
            }

            $shipped = array_column(self::campaigns(), 'slug');
            foreach (array_keys($state) as $slug) {
                if (count($state) <= self::MAX_STATE_ENTRIES) {
                    return $state;
                }

                if (! in_array((string) $slug, $shipped, true)) {
                    unset($state[$slug]);
                }
            }

            foreach ($state as $slug => $entry) {
                if (count($state) <= self::MAX_STATE_ENTRIES) {
                    break;
                }

                if (! isset($entry['status']) || ! in_array($entry['status'], array('claimed', 'dismissed'), true)) {
                    unset($state[$slug]);
                }
            }

            return $state;
        }

        /**
         * Whether Pro is running or installed. A deleted copy doesn't count, so the version option Pro leaves behind is ignored.
         *
         * @return bool
         */
        public static function has_pro()
        {
            if (defined('METASLIDERPRO_VERSION')) {
                return true;
            }

            // Looking for an inactive copy can read every plugin's header, so the answer is kept for a day
            $cached = get_transient(self::PRO_CACHE);
            if (false === $cached) {
                $cached = metaslider_pro_is_installed() ? 'yes' : 'no';
                set_transient(self::PRO_CACHE, $cached, DAY_IN_SECONDS);
            }

            return 'yes' === $cached;
        }

        /**
         * Whether the site's oldest slideshow is at least this many days old.
         *
         * @param int $days - the minimum age
         * @param int $now - the current time
         * @return bool
         */
        public static function used_for_days($days, $now)
        {
            if ($days <= 0) {
                return true;
            }

            if (null === self::$oldest_slideshow) {
                $oldest = wp_cache_get('oldest_slideshow', 'metaslider');

                if (false === $oldest) {
                    global $wpdb;

                    // A direct query, because language plugins filter get_posts() and could hide the oldest slideshow
                    $oldest = (int) $wpdb->get_var($wpdb->prepare(
                        "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status NOT IN ('trash', 'auto-draft') ORDER BY post_date ASC LIMIT 1",
                        'ml-slider'
                    ));
                    wp_cache_set('oldest_slideshow', $oldest, 'metaslider', HOUR_IN_SECONDS);
                }

                self::$oldest_slideshow = $oldest ? get_post_timestamp($oldest) : false;
            }

            return self::$oldest_slideshow && self::$oldest_slideshow <= $now - $days * DAY_IN_SECONDS;
        }

        /**
         * Clears the per-request caches, for tests. Campaigns passed in are used instead of the list file.
         *
         * @param array|null $items - campaigns as written in campaign-list.php
         * @return void
         */
        public static function reset($items = null)
        {
            self::$oldest_slideshow = null;
            self::$bundle = null === $items ? null : self::parse($items);
        }

        /**
         * The URL of the bundled campaign images.
         *
         * @return string
         */
        public static function assets_url()
        {
            return METASLIDER_ADMIN_ASSETS_URL . 'campaigns';
        }

        /**
         * The only HTML the headline and offer may contain.
         *
         * @param string $field - 'headline' or 'body'
         * @return array - in wp_kses() format
         */
        public static function allowed_html($field = 'headline')
        {
            $allowed = array(
                'strong' => array(),
                'b' => array(),
                'em' => array(),
                'i' => array(),
                'u' => array(),
                's' => array(),
                'del' => array(),
                'br' => array(),
                'span' => array('style' => true),
                'a' => array('href' => true),
            );

            if ('body' === $field) {
                $allowed['img'] = array('src' => true, 'alt' => true, 'width' => true, 'height' => true);
            }

            return $allowed;
        }

        /**
         * Whether a value is a valid campaign slug: lowercase letters, numbers and dashes.
         *
         * @param mixed $slug - the value to check
         * @return bool
         */
        public static function is_valid_slug($slug)
        {
            return is_string($slug) && 1 === preg_match('/^[a-z0-9][a-z0-9-]{0,63}$/', $slug);
        }

        /**
         * Rebuilds a link or image tag from its parsed attributes, so text inside one attribute can't pass for another.
         *
         * @param string $tag - 'a' or 'img'
         * @param string $attributes - the raw attribute string
         * @return string - the safe tag, or '' to drop an image
         */
        public static function rebuild_tag($tag, $attributes)
        {
            $parsed = array();
            foreach (wp_kses_hair($attributes, array('http', 'https')) as $name => $attribute) {
                $parsed[strtolower($name)] = $attribute['value'];
            }

            if ('a' === $tag) {
                $href = isset($parsed['href']) ? self::https_url($parsed['href']) : '';
                return '<a' . ('' !== $href ? ' href="' . esc_url($href) . '"' : '') . ' target="_blank" rel="noopener noreferrer">';
            }

            // Only a plain image file in the campaigns folder, so the popup never loads anything from elsewhere
            $src = isset($parsed['src']) ? $parsed['src'] : '';
            $prefix = self::assets_url() . '/';
            $file = 0 === strpos($src, $prefix) ? substr($src, strlen($prefix)) : '';
            if (1 !== preg_match('/^[a-z0-9][a-z0-9_-]*\.(png|jpe?g|gif|webp)$/i', $file)) {
                return '';
            }

            $html = '<img src="' . esc_url($src) . '" alt="' . esc_attr(isset($parsed['alt']) ? $parsed['alt'] : '') . '"';
            foreach (array('width', 'height') as $size) {
                if (isset($parsed[$size]) && ctype_digit((string) $parsed[$size])) {
                    $html .= ' ' . $size . '="' . absint($parsed[$size]) . '"';
                }
            }

            return $html . ' />';
        }

        /**
         * A campaign from this release.
         *
         * @param string $slug - the campaign slug
         * @return array|null
         */
        private static function find($slug)
        {
            foreach (self::campaigns() as $campaign) {
                if ($campaign['slug'] === $slug) {
                    return $campaign;
                }
            }

            return null;
        }

        /**
         * Formatting HTML with anything not allowed removed, links opening in a new tab and only bundled images kept.
         *
         * @param string $html - the HTML
         * @param string $field - 'headline' or 'body'
         * @return string
         */
        private static function clean_html($html, $field)
        {
            $html = str_replace(self::ASSETS_PLACEHOLDER, self::assets_url(), $html);

            // Only plain colors in style, so a background image can't load from anywhere
            $only_color = function () {
                return array('color');
            };
            add_filter('safe_style_css', $only_color, PHP_INT_MAX);
            $html = wp_kses(force_balance_tags($html), self::allowed_html($field), array('http', 'https'));
            remove_filter('safe_style_css', $only_color, PHP_INT_MAX);

            return trim(preg_replace_callback('/<(a|img)\b([^>]*)>/i', function ($match) {
                return MetaSlider_Campaigns::rebuild_tag(strtolower($match[1]), $match[2]);
            }, $html));
        }

        /**
         * The feature list as plain texts, or null when it isn't a list or is too long. Long features are cut, like other plain text.
         *
         * @param array $content - the campaign content
         * @return array|null
         */
        private static function sanitize_features($content)
        {
            if (! isset($content['features'])) {
                return array();
            }

            if (! is_array($content['features']) || count($content['features']) > self::MAX_FEATURES) {
                return null;
            }

            $features = array();
            foreach ($content['features'] as $feature) {
                if (! is_string($feature)) {
                    return null;
                }

                $feature = sanitize_text_field(mb_substr($feature, 0, self::FEATURE_LIMIT));
                if ('' !== $feature) {
                    $features[] = $feature;
                }
            }

            return $features;
        }

        /**
         * Keeps a link only when it is an absolute https URL.
         *
         * @param string $url - the link
         * @return string - the clean URL, or ''
         */
        private static function https_url($url)
        {
            $url = esc_url_raw($url, array('https'));

            if ('' === $url || strlen($url) > 2048) {
                return '';
            }

            $parts = wp_parse_url($url);

            return is_array($parts) && ! empty($parts['host']) && isset($parts['scheme']) && 'https' === strtolower($parts['scheme']) ? $url : '';
        }

        /**
         * A date from the list: null means none (0), anything unreadable is false.
         *
         * @param array $item - the campaign
         * @param string $key - the field
         * @return int|false
         */
        private static function parse_time($item, $key)
        {
            if (! isset($item[$key]) || null === $item[$key]) {
                return 0;
            }

            if (! is_string($item[$key])) {
                return false;
            }

            $time = strtotime($item[$key]);

            // Before 1970 is a typo, and 0 would mean "no limit", so it rejects the campaign instead
            return false === $time || $time <= 0 ? false : $time;
        }

        /**
         * @param array $data - the array to read
         * @param string $key - the field
         * @param int $min - the lowest allowed value
         * @param int $max - the highest allowed value
         * @param int $default - used when the field is missing or not a number
         * @return int
         */
        private static function int_in_range($data, $key, $min, $max, $default)
        {
            if (! isset($data[$key]) || ! is_numeric($data[$key])) {
                return $default;
            }

            return min($max, max($min, (int) $data[$key]));
        }
    }
}
