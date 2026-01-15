<?php
namespace PublishPress\Blocks;

use DeviceDetector\DeviceDetector;

/*
 * Block controls logic
 */
if (!class_exists('\\PublishPress\\Blocks\\Controls')) {
    class Controls
    {
        /**
         * Check if block is using controls and decide to display or not in frontend
         *
         * @param string $block_content Block HTML output
         * @param array $block Block attributes
         *
         * @return string                   $block_content or an empty string when block is hidden
         * @since 2.14.0
         *
         * @since 3.1.0 function renamed and migrated from AdvancedGutenbergMain
         */
        public static function checkBlockControls($block_content, $block)
        {
            if (
                Utilities::settingIsEnabled('block_controls')
                && isset($block['attrs']['advgbBlockControls'])
                && $block['blockName']
            ) {
                $controls = $block['attrs']['advgbBlockControls'];

                $show_block = true;

                // Cache device type detection for performance
                static $device_type = null;
                if ($device_type === null) {
                    $device_type = self::getDeviceType();
                }

                foreach ($controls as $key => $item) {
                    if (
                        isset($item['control'])
                        && self::getControlValue($item['control'], 1) === true // Is this control enabled? @TODO Dynamic way to define default value depending the control ; not all will be active (1) by default
                        && self::isBlockEnabled($block['blockName']) // Controls are enabled for this block?
                        && isset($item['enabled'])
                        && (bool) $item['enabled'] === true
                    ) {
                        // skip some control
                        if (in_array($item['control'], ['meta'])) {
                            continue;
                        }

                        // apply preset control
                        if ($item['control'] === 'presets') {
                            if (!self::checkPresetControls($block_content, $block, $item, $key)) {
                                $show_block = false;
                            }
                        } elseif (self::displayBlock($block, $item['control'], $key, $device_type) === false) {
                                // Stop iteration; we reached a control that decides block shouln't be displayed
                                $show_block = false;
                        }

                    }
                }
                if (!$show_block) {
                    return '';
                }
                // Generate a unique identifier for the block if it doesn't have one
                $block_identifier = self::getBlockIdentifier($block);
                // Add our control class to the block
                $block_content = self::addBlockControlClass($block_content, $block_identifier);
                // Add CSS rules as backup
                $block_content = self::outputControlCSS($block_content, $block, $block_identifier);
            }

            return $block_content;
        }

        private static function getPresetItemConfig($preset_id, $presets = false) {
            $presets = ! is_array($presets) ? self::getPresets(true) : $presets;
            $preset_config = false;

            if (isset($presets[$preset_id])) {
                $preset_config = $presets[$preset_id];
            }

            return $preset_config;
        }

        private static function checkPresetControls($block_content, $block, $preset_config, $key)
        {
            $selected_presets = $preset_config['selected'] ?? [];
            $logic = $preset_config['logic'] ?? 'any';

            $preset_matches = 0;
            foreach ($selected_presets as $preset_id) {
                $preset_rule = self::getPresetItemConfig($preset_id);
                if ($preset_rule) {
                    if (self::evaluatePreset($preset_rule, $block, $key)) {
                        $preset_matches++;
                    }
                }
            }

            $show_block = false;
            if ($logic === 'any') {
                $show_block = $preset_matches > 0;
            } else {
                $show_block = $preset_matches === count($selected_presets);
            }

            return $show_block ? $block_content : '';
        }

        private static function evaluatePreset($preset, $block, $key)
        {
            $control_sets = $preset['controlSets'] ?? [];
            // OR logic between control sets
            foreach ($control_sets as $control_set) {
                if (self::evaluateControlSet($control_set, $block, $key)) {
                    return true; // If any control set matches, preset matches
                }
            }

            return false;
        }

        private static function evaluateControlSet($control_set, $block, $key)
        {
            $rules = $control_set['rules'] ?? [];

            // AND logic within control set
            foreach ($rules as $rule) {
                if (!self::evaluateRule($rule, $block, $key)) {
                    return false; // If any rule fails, control set fails
                }
            }

            return true; // All rules passed
        }

        private static function evaluateRule($rule, $block, $key)
        {
            $rule_type = $rule['type'] ?? '';

            // Replace preset id with data
            $block['attrs']['advgbBlockControls'][$key] = array_merge([
                'control' => $rule_type,
                'enabled' => true
            ], $rule);

            return self::displayBlock($block, $rule_type, $key);
        }

        /**
         * Check if block in widgets area is using controls and decide to display or not in frontend,
         * including its widget HTML wrapper.
         *
         * @param array $instance Widget instance
         *
         * @return bool false means block and its widget HTML wrapper is hidden
         * @since 3.1.2
         *
         */
        public static function checkBlockControlsWidget($instance)
        {
            // Exclude REST API
            if (
                strpos(wp_get_raw_referer(), '/wp-admin/widgets.php')
                && isset($_SERVER['REQUEST_URI'])
                && false !== strpos(
                    filter_var(wp_unslash($_SERVER['REQUEST_URI']), FILTER_SANITIZE_URL),
                    '/wp-json/'
                )
            ) {
                return $instance;
            }

            if (
                Utilities::settingIsEnabled('block_controls')
                && !empty($instance['content'])
                && has_blocks($instance['content'])
            ) {
                $blocks = parse_blocks($instance['content']);

                if (isset($blocks[0]['attrs']['advgbBlockControls']) && $blocks[0]['blockName']) {
                    $controls = $blocks[0]['attrs']['advgbBlockControls'];

                    foreach ($controls as $key => $item) {
                        if (
                            isset($item['control'])
                            && self::getControlValue(
                                $item['control'],
                                1
                            ) === true // Is this control enabled? @TODO Dynamic way to define default value depending the control ; not all will be active (1) by default
                            && self::isBlockEnabled($blocks[0]['blockName']) // Controls are enabled for this block?
                            && isset($item['enabled'])
                            && (bool) $item['enabled'] === true
                        ) {
                            if (self::displayBlock($blocks[0], $item['control'], $key) === false) {
                                return false; // This block is hidden
                            }
                        }
                    }
                }
            }

            return $instance;
        }

        /**
         * Check a single control against a block
         *
         * @param array $block Block object
         * @param string $control Control to validate against a block. e.g. 'schedule'
         * @param int $key Array position for $control
         * @param mixed $device_type The current user devce type
         *
         * @return bool             True to display block, false to hide
         * @since 3.1.0
         *
         */
        private static function displayBlock($block, $control, $key, $device_type = null)
        {
            switch ($control) {
                // Schedule control
                default:
                case 'schedule':
                    $bControl = $block['attrs']['advgbBlockControls'][$key];

                    // Backward compatibility - check if we have the old single schedule format
                    if (isset($bControl['schedules'])) {
                        // New format with multiple schedules
                        $schedules = $bControl['schedules'];
                    } else {
                        // Legacy format - convert to array
                        $schedules = [[
                            'dateFrom' => $bControl['dateFrom'] ?? null,
                            'dateTo' => $bControl['dateTo'] ?? null,
                            'recurring' => $bControl['recurring'] ?? false,
                            'days' => $bControl['days'] ?? [],
                            'timeFrom' => $bControl['timeFrom'] ?? null,
                            'timeTo' => $bControl['timeTo'] ?? null,
                            'timezone' => $bControl['timezone'] ?? null
                        ]];
                    }

                    $shouldShow = false;

                    foreach ($schedules as $schedule) {
                        // Skip if schedule is empty
                        if (
                            empty($schedule['dateFrom']) && empty($schedule['dateTo']) &&
                            empty($schedule['timeFrom']) && empty($schedule['timeTo']) &&
                            empty($schedule['days'])
                        ) {
                            continue;
                        }

                        $dateFrom = $dateTo = $recurring = $timeFrom = $timeTo = null;
                        $days = isset($schedule['days']) && is_array($schedule['days']) && count($schedule['days'])
                            ? $schedule['days'] : [];
                        if (count($days)) {
                            // Convert JavaScript days (0=Sun) to PHP 'N' format (7=Sun)
                            $days = array_map(function ($day) {
                                $day = intval($day);
                                return $day === 0 ? 7 : $day;
                            }, $days);
                        }

                        // Timezone handling
                        if (
                            defined('ADVANCED_GUTENBERG_PRO_LOADED')
                            && isset($schedule['timezone'])
                            && !empty($schedule['timezone'])
                            && method_exists('PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_set_timezone')
                        ) {
                            $timezone = \PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_set_timezone(
                                esc_html($schedule['timezone'])
                            );
                        } else {
                            $timezone = wp_timezone();
                        }

                        // Start showing
                        if (!empty($schedule['dateFrom'])) {
                            $dateFrom = \DateTime::createFromFormat('Y-m-d\TH:i:s', $schedule['dateFrom'], $timezone);
                            // Reset seconds to zero to enable proper comparison
                            $dateFrom->setTime($dateFrom->format('H'), $dateFrom->format('i'), 0);
                        }

                        // Stop showing
                        if (!empty($schedule['dateTo'])) {
                            $dateTo = \DateTime::createFromFormat('Y-m-d\TH:i:s', $schedule['dateTo'], $timezone);
                            // Reset seconds to zero to enable proper comparison
                            $dateTo->setTime($dateTo->format('H'), $dateTo->format('i'), 0);

                            if ($dateFrom) {
                                // Recurring is only relevant when both dateFrom and dateTo are defined
                                $recurring = isset($schedule['recurring']) ? $schedule['recurring'] : false;
                            }
                        }

                        /**
                         * Time handling
                         * Time from and Time to exists and are valid.
                         * Valid times: "02:00:00", "19:35:00"
                         * Invalid times: "-06:00:00", "25:00:00"
                         *
                         */
                        if (
                            !empty($schedule['timeFrom'])
                            && !empty($schedule['timeTo'])
                            && strtotime($schedule['timeFrom']) !== false
                            && strtotime($schedule['timeTo']) !== false
                        ) {
                            // Get current datetime with timezone
                            $timeNow = new \DateTime('now', $timezone);
                            $timeNow->format('Y-m-d\TH:i:s');
                            $timeFrom = clone $timeNow;
                            $timeTo = clone $timeNow;

                            // Replace with our time attributes in previously generated datetime
                            $timeFrom->modify($schedule['timeFrom']);
                            $timeTo->modify($schedule['timeTo']);
                        }

                        if ($dateFrom || $dateTo || $days || ($timeFrom && $timeTo)) {
                            // Fetch current time keeping in mind the timezone
                            $now = new \DateTime('now', $timezone);
                            $now->format('Y-m-d\TH:i:s');

                            /* Reset seconds to zero to enable proper comparison
                            * as the from and to dates have those as 0
                            * but do this only for the from comparison
                            * as we need the block to stop showing at the right time and not 1 minute extra
                            */
                            $nowFrom = clone $now;
                            $nowFrom->setTime($now->format('H'), $now->format('i'), 0);

                            // Decide if block is displayed or not
                            if ($recurring) {
                                // Make the year same as today's
                                $dateFrom->setDate(
                                    $nowFrom->format('Y'),
                                    $dateFrom->format('m'),
                                    $dateFrom->format('j')
                                );
                                $dateTo->setDate($nowFrom->format('Y'), $dateTo->format('m'), $dateTo->format('j'));
                            }

                            if (
                                (!$schedule['dateFrom'] || $dateFrom->getTimestamp() <= $nowFrom->getTimestamp()) // No "Start showing", or "Start showing" <= Now
                                && (!$schedule['dateTo'] || $now->getTimestamp() < $dateTo->getTimestamp()) // No "Stop showing", or now < "Stop showing" &&
                                && (!count($days) || in_array($nowFrom->format('N'), $days)) // "These days"
                                && (!$schedule['timeFrom'] || $timeFrom->getTimestamp() <= $nowFrom->getTimestamp()) // No "Time from", or "Time
                                && (!$schedule['timeTo'] || $now->getTimestamp() < $timeTo->getTimestamp()) // No "Time to", or now
                            ) {
                                // If any schedule matches, show the block
                                $shouldShow = true;
                                return $shouldShow;
                            }
                        }
                    }

                    return $shouldShow;
                    break;

                // User role control
                case 'user_role':
                    $bControl = $block['attrs']['advgbBlockControls'][$key];
                    $selected_roles = is_array($bControl['roles']) && count($bControl['roles'])
                        ? $bControl['roles'] : [];

                    if (count($selected_roles)) {
                        // Check if user role exists to avoid non-valid roles
                        foreach ($selected_roles as $key => $role) {
                            if (!$GLOBALS['wp_roles']->is_role($role)) {
                                unset($selected_roles[$key]);
                            }
                        }
                    }

                    // Check current user role visit
                    $user = wp_get_current_user();
                    $approach = isset($bControl['approach']) && !empty(sanitize_text_field($bControl['approach']))
                        ? $bControl['approach'] : 'public';

                    switch ($approach) {
                        default:
                        case 'public':
                            return true;
                            break;

                        case 'hidden':
                            return false;
                            break;

                        case 'login':
                            return is_user_logged_in() ? true : false;
                            break;

                        case 'logout':
                            return !is_user_logged_in() ? true : false;
                            break;

                        case 'include':
                            return array_intersect($selected_roles, $user->roles) ? true : false;
                            break;

                        case 'exclude':
                            return !array_intersect($selected_roles, $user->roles) ? true : false;
                            break;
                    }

                    break;

                case 'device_type':
                    $bControl = $block['attrs']['advgbBlockControls'][$key];
                    $selected_devices = isset($bControl['devices']) ? $bControl['devices'] : [];

                    // If no devices selected, show on all
                    if (empty($selected_devices)) {
                        return true;
                    }

                    if ($device_type === null) {
                        $device_type = self::getDeviceType();
                    }

                    // Use the pre-detected device type
                    return in_array($device_type, $selected_devices);
                    break;

                case 'device_width':
                    $bControl = $block['attrs']['advgbBlockControls'][$key];
                    $min_width = isset($bControl['min_width']) ? intval($bControl['min_width']) : 0;
                    $max_width = isset($bControl['max_width']) ? intval($bControl['max_width']) : 0;

                    // Get current screen width
                    $screen_width = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ?
                        (isset($_SERVER['HTTP_X_SCREEN_WIDTH']) ? intval($_SERVER['HTTP_X_SCREEN_WIDTH']) : 0) :
                        (isset($_SERVER['HTTP_X_CLIENT_WIDTH']) ? intval($_SERVER['HTTP_X_CLIENT_WIDTH']) : 0);
                    // If we couldn't detect screen width, return true
                    if ($screen_width === 0) {
                        return true;
                    }

                    // Check min/max width
                    $min_ok = ($min_width === 0) || ($screen_width >= $min_width);
                    $max_ok = ($max_width === 0) || ($screen_width <= $max_width);

                    return $min_ok && $max_ok;
                    break;

                case 'browser_device':
                    return self::checkBrowserDevice($block, $block['attrs']['advgbBlockControls'][$key]);
                    break;

                case 'operating_system':
                    return self::checkOperatingSystem($block, $block['attrs']['advgbBlockControls'][$key]);
                    break;

                case 'cookie':
                    return self::checkCookie($block, $block['attrs']['advgbBlockControls'][$key]);
                    break;

                case 'user_meta':
                    return self::checkUserMeta($block, $block['attrs']['advgbBlockControls'][$key]);
                    break;

                case 'post_meta':
                    return self::checkPostMeta($block, $block['attrs']['advgbBlockControls'][$key]);
                    break;

                case 'query_string':
                    return self::checkQueryString($block, $block['attrs']['advgbBlockControls'][$key]);
                    break;

                case 'capabilities':
                    return self::checkCapabilities($block, $block['attrs']['advgbBlockControls'][$key]);
                    break;

                // Archive control
                case 'archive':
                    $bControl = $block['attrs']['advgbBlockControls'][$key];
                    $taxonomies = is_array($bControl['taxonomies']) ? $bControl['taxonomies'] : [];
                    $taxQuery = get_queried_object();

                    if (!isset($taxQuery->taxonomy)) {
                        return true;
                    }

                    $merged_tax = []; // To store selected taxonomies. e.g. [ 'category', 'post_tag' ]
                    $merged_terms = []; // To store selected terms from all taxonomies. e.g. [99,72,51]

                    // Create taxonomies array
                    if (isset($taxonomies) && count($taxonomies)) {
                        foreach ($taxonomies as $item) {
                            $merged_tax[] = sanitize_text_field($item['tax']);

                            // Create terms array
                            if (isset($item['terms']) && count($item['terms'])) {
                                foreach ($item['terms'] as $term) {
                                    $merged_terms[] = intval($term);
                                }
                            }
                        }
                    }

                    if (count($merged_tax)) {
                        $approach = isset($bControl['approach']) && !empty(sanitize_text_field($bControl['approach']))
                            ? $bControl['approach'] : 'exclude';

                        switch ($approach) {
                            case 'include':
                                return self::checkTaxonomies($taxonomies, $merged_terms, $taxQuery) ? true : false;
                                break;

                            case 'exclude':
                                return self::checkTaxonomies($taxonomies, $merged_terms, $taxQuery) ? false : true;
                                break;
                        }
                    }
                    break;

                // Pages control
                case 'page':
                    $bControl = $block['attrs']['advgbBlockControls'][$key];
                    $selected = is_array($bControl['pages']) ? $bControl['pages'] : [];

                    if (count($selected)) {
                        $selected = array_map('sanitize_text_field', $selected);
                        $approach = isset($bControl['approach']) && !empty(sanitize_text_field($bControl['approach']))
                            ? $bControl['approach'] : 'public';

                        switch ($approach) {
                            default:
                            case 'public':
                                return true;
                                break;

                            case 'include':
                                return self::checkPages($selected) ? true : false;
                                break;

                            case 'exclude':
                                return self::checkPages($selected) ? false : true;
                                break;
                        }
                    }
                    break;
            }

            return true;
        }

        public static function getBrowserType()
        {
            static $browser_type = null;
            if ($browser_type === null) {
                $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                $dd = new DeviceDetector($user_agent);
                $dd->parse();
                $browser_type = strtolower($dd->getClient('name'));
            }
            return $browser_type;
        }

        public static function getOperatingSystem()
        {
            static $os_type = null;
            if ($os_type === null) {
                $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                $dd = new DeviceDetector($user_agent);
                $dd->parse();
                $os_type = strtolower($dd->getOs('name'));
            }
            return $os_type;
        }

        /**
         * Get device type
         *
         * @return string
         */
        public static function getDeviceType()
        {
            static $device_type = null;
            if ($device_type === null) {
                $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                $dd = new DeviceDetector($user_agent);
                $dd->parse();

                if ($dd->isBot()) {
                    $device_type = 'robot';
                } elseif ($dd->isMobile()) {
                    $device_type = 'mobile';
                } elseif ($dd->isTablet()) {
                    $device_type = 'tablet';
                } else {
                    $device_type = 'desktop';
                }
            }
            return $device_type;
        }

        public static function checkBrowserDevice($block, $control_data)
        {
            $browser = self::getBrowserType();
            $browsers = isset($control_data['browsers']) ? $control_data['browsers'] : [];
            $approach = isset($control_data['approach']) ? $control_data['approach'] : 'include';

            $match = in_array($browser, $browsers);
            return ($approach === 'include') ? $match : !$match;
        }

        public static function checkOperatingSystem($block, $control_data)
        {
            $os = self::getOperatingSystem();
            $systems = isset($control_data['systems']) ? $control_data['systems'] : [];
            $approach = isset($control_data['approach']) ? $control_data['approach'] : 'include';

            $match = in_array($os, $systems);
            return ($approach === 'include') ? $match : !$match;
        }

        public static function checkCookie($block, $control_data)
        {
            $cookie_name = isset($control_data['name']) ? $control_data['name'] : '';
            $condition = isset($control_data['condition']) ? $control_data['condition'] : '=';
            $value = isset($control_data['value']) ? $control_data['value'] : '';
            $approach = isset($control_data['approach']) ? $control_data['approach'] : 'include';

            if (empty($cookie_name)) return false;

            $cookie_value = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : null;
            $match = self::evaluateCondition($cookie_value, $condition, $value);

            return ($approach === 'include') ? $match : !$match;
        }

        public static function checkUserMeta($block, $control_data)
        {
            if (!is_user_logged_in()) return false;

            $meta_key = isset($control_data['key']) ? $control_data['key'] : '';
            $condition = isset($control_data['condition']) ? $control_data['condition'] : '=';
            $value = isset($control_data['value']) ? $control_data['value'] : '';
            $approach = isset($control_data['approach']) ? $control_data['approach'] : 'include';

            if (empty($meta_key)) return false;

            $meta_value = get_user_meta(get_current_user_id(), $meta_key, true);
            $match = self::evaluateCondition($meta_value, $condition, $value);

            return ($approach === 'include') ? $match : !$match;
        }

        public static function checkPostMeta($block, $control_data)
        {
            global $post;
            if (!$post) return false;

            $meta_key = isset($control_data['key']) ? $control_data['key'] : '';
            $condition = isset($control_data['condition']) ? $control_data['condition'] : '=';
            $value = isset($control_data['value']) ? $control_data['value'] : '';
            $approach = isset($control_data['approach']) ? $control_data['approach'] : 'include';

            if (empty($meta_key)) return false;

            $meta_value = get_post_meta($post->ID, $meta_key, true);
            $match = self::evaluateCondition($meta_value, $condition, $value);

            return ($approach === 'include') ? $match : !$match;
        }

        public static function checkQueryString($block, $control_data)
        {
            $query_strings = isset($control_data['queries']) ? $control_data['queries'] : [];
            if (!is_array($query_strings)) {
                $query_strings = array_filter(
                    array_map( 'trim', preg_split( '/\r\n|\r|\n/', $query_strings ) )
                );
            }
            $logic = isset($control_data['logic']) ? $control_data['logic'] : 'all';
            $approach = isset($control_data['approach']) ? $control_data['approach'] : 'include';

            if (empty($query_strings)) return false;

            $matches = 0;
            foreach ($query_strings as $query) {
                if (isset($_GET[$query])) {
                    $matches++;
                }
            }

            $result = false;
            if ($logic === 'all') {
                $result = $matches === count($query_strings);
            } else { // 'any'
                $result = $matches > 0;
            }

            return ($approach === 'include') ? $result : !$result;
        }

        public static function checkCapabilities($block, $control_data)
        {
            if (! is_user_logged_in()) {
                 return false;
            }

            $capabilities = isset($control_data['capabilities']) ? $control_data['capabilities'] : [];
            $approach = isset($control_data['approach']) ? $control_data['approach'] : 'include';

            if (empty($capabilities)) {
                return false;
            }

            $has_capability = false;
            foreach ($capabilities as $cap) {
                if (current_user_can($cap)) {
                    $has_capability = true;
                    break;
                }
            }

            return ($approach === 'include') ? $has_capability : !$has_capability;
        }


        private static function evaluateCondition($actual, $condition, $expected)
        {
            switch ($condition) {
                case '=':
                    return $actual == $expected;
                case '!=':
                    return $actual != $expected;
                case '<':
                    return $actual < $expected;
                case '>':
                    return $actual > $expected;
                case '<=':
                    return $actual <= $expected;
                case '>=':
                    return $actual >= $expected;
                case 'contains':
                    return strpos($actual, $expected) !== false;
                case 'beginsWith':
                    return strpos($actual, $expected) === 0;
                case 'endsWith':
                    return substr($actual, -strlen($expected)) === $expected;
                case 'doesNotContain':
                    return strpos($actual, $expected) === false;
                case 'doesNotBeginWith':
                    return strpos($actual, $expected) !== 0;
                case 'doesNotEndWith':
                    return substr($actual, -strlen($expected)) !== $expected;
                case 'null':
                    return is_null($actual) || $actual === '';
                case 'notNull':
                    return !is_null($actual) && $actual !== '';
                case 'in':
                    $values = explode(',', $expected);
                    return in_array($actual, array_map('trim', $values));
                case 'notIn':
                    $values = explode(',', $expected);
                    return !in_array($actual, array_map('trim', $values));
                case 'between':
                    $values = explode(',', $expected);
                    if (count($values) === 2) {
                        return $actual >= trim($values[0]) && $actual <= trim($values[1]);
                    }
                    return false;
                case 'notBetween':
                    $values = explode(',', $expected);
                    if (count($values) === 2) {
                        return !($actual >= trim($values[0]) && $actual <= trim($values[1]));
                    }
                    return true;
                default:
                    return false;
            }
        }

        /**
         * Generate a unique identifier for the block
         *
         * @param mixed $block
         * @return string
         */
        private static function getBlockIdentifier($block)
        {
            // Use block ID if available
            if (isset($block['attrs']['block_id'])) {
                return 'advgb-block-' . $block['attrs']['block_id'];
            }

            // Fallback to hash of block content
            $content_hash = substr(md5($block['innerHTML']), 0, 8);

            return 'advgb-dyn-' . $content_hash;
        }

        /**
         * Add control class to block
         *
         * @param mixed $block_content
         * @param mixed $identifier
         *
         * @return mixed
         */
        private static function addBlockControlClass($block_content, $identifier)
        {
            // Skip if already processed
            if (strpos($block_content, $identifier) !== false) {
                return $block_content;
            }

            // Handle blocks with existing class attribute
            if (strpos($block_content, 'class="') !== false) {
                return preg_replace(
                    '/class="([^"]*)"/',
                    'class="$1 ' . esc_attr($identifier) . '"',
                    $block_content,
                    1
                );
            }

            // Handle blocks without class attribute
            return preg_replace(
                '/<([a-zA-Z0-9_-]+)([^>]*)>/',
                '<$1$2 class="' . esc_attr($identifier) . '">',
                $block_content,
                1
            );
        }

        /**
         * Additional css backup for device type and device width
         * @param mixed $block_content
         * @param mixed $block
         * @param string $identifier
         *
         */
        public static function outputControlCSS($block_content, $block, $identifier)
        {
            if (
                Utilities::settingIsEnabled('block_controls')
                && isset($block['attrs']['advgbBlockControls'])
                && $block['blockName']
            ) {
                $controls = $block['attrs']['advgbBlockControls'];
                $css_rules = [];

                foreach ($controls as $key => $item) {
                    if (isset($item['control']) && isset($item['enabled']) && (bool) $item['enabled'] === true) {
                        $control_items = [];
                        if ($item['control'] === 'presets') {
                            $selected_presets = $item['selected'] ?? [];
                            foreach ($selected_presets as $preset_id) {
                                $preset_item_config = self::getPresetItemConfig($preset_id);
                                $control_sets = $preset_item_config['controlSets'] ?? [];
                                foreach ($control_sets as $control_set) {
                                    $control_set_rules = $control_set['rules'] ?? [];
                                    foreach ($control_set_rules as $control_set_rule) {
                                        /**
                                         * CSS Operator cannot respect AND/OR rules in presets.
                                         * So, we should 100% relied on PHP logic for 'device_type'
                                         * except 'device_width' that's solely css.
                                         */
                                        if (in_array($control_set_rule['type'], ['device_width'])) {
                                            $control_set_rule['control'] = $control_set_rule['type'];
                                            $control_items[] = $control_set_rule;
                                        }
                                    }
                                }
                            }
                        } else {
                            $control_items[] = $item;
                        }
                        foreach ($control_items as $control_item) {
                            switch ($control_item['control']) {
                                case 'device_type':
                                    $devices = isset($control_item['devices']) ? $control_item['devices'] : [];
                                    if (!empty($devices)) {
                                        $hidden_devices = array_diff(['desktop', 'tablet', 'mobile'], $devices);

                                        foreach ($hidden_devices as $device) {
                                            switch ($device) {
                                                case 'mobile':
                                                    $css_rules[] = "@media (max-width: 767px) { .{$identifier} { display: none !important; } }";
                                                    break;
                                                case 'tablet':
                                                    $css_rules[] = "@media (min-width: 768px) and (max-width: 1024px) { .{$identifier} { display: none !important; } }";
                                                    break;
                                                case 'desktop':
                                                    $css_rules[] = "@media (min-width: 1025px) { .{$identifier} { display: none !important; } }";
                                                    break;
                                            }
                                        }
                                    }
                                    break;
                                case 'device_width':
                                    $min_width = isset($control_item['min_width']) ? intval($control_item['min_width']) : 0;
                                    $max_width = isset($control_item['max_width']) ? intval($control_item['max_width']) : 0;

                                    if ($min_width > 0) {
                                        $css_rules[] = "@media (max-width: " . ($min_width - 1) . "px) { .{$identifier} { display: none !important; } }";
                                    }

                                    if ($max_width > 0) {
                                        $css_rules[] = "@media (min-width: " . ($max_width + 1) . "px) { .{$identifier} { display: none !important; } }";
                                    }
                                    break;
                            }
                        }
                    }
                }

                if (!empty($css_rules)) {
                    $block_content = '<style>' . implode(' ', $css_rules) . '</style>' . $block_content;
                }
            }

            return $block_content;
        }


        /**
         * Check taxonomies in frontend
         *
         * @param array $taxonomies Array of taxonomies setup e.g. [ ['tax'=>'category', 'terms' => [172,99,3], 'all' => false], ['tax'=>'post_tag', 'terms' => [], 'all' => true] ]
         * @param array $terms Array of term ids e.g. [172,99,3]
         * @param object $taxQuery WP_Term
         *
         * @return bool
         * @since 3.1.2
         *
         */
        public static function checkTaxonomies($taxonomies, $terms, $taxQuery)
        {
            foreach ($taxonomies as $item) {
                if ((string) $item['tax'] === $taxQuery->taxonomy && (bool) $item['all']) {
                    // Taxonomy found & all terms
                    return true;
                } elseif (in_array($taxQuery->term_id, $terms)) {
                    // Term found
                    return true;
                } else {
                    // Nothing to do here
                }
            }

            return false;
        }

        /**
         * Check pages in frontend
         *
         * @param array $selected Array of pages e.g. ['home', 'search']
         *
         * @return bool
         * @since 3.1.1
         *
         */
        public static function checkPages($selected)
        {
            if (
                in_array('home', $selected)
                && ((is_home() && is_front_page())
                    || is_front_page()
                )
            ) {
                return true;
            } elseif (in_array('blog', $selected) && is_home()) {
                return true;
            } elseif (in_array('archive', $selected) && is_archive()) {
                return true;
            } elseif (in_array('search', $selected) && is_search()) {
                return true;
            } elseif (in_array('page404', $selected) && is_404()) {
                return true;
            }

            return false;
        }

        /**
         * Add attributes to ServerSideRender blocks to fix "Invalid parameter(s): attributes" error.
         * As example: 'core/latest-comments'
         * Related Gutenberg issue: https://github.com/WordPress/gutenberg/issues/16850
         *
         * @since 3.1.0 function renamed and migrated from AdvancedGutenbergMain
         * @since 2.14.0
         */
        public static function addAttributes()
        {
            $registered_blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
            foreach ($registered_blocks as $block) {
                $block->attributes['advgbBlockControls'] = [
                    'type' => 'array',
                    'default' => [],
                ];
                // Add custom style attributes to fix https://github.com/publishpress/publishpress-blocks/issues/1652
                $block->attributes['customStyle'] = [
                    'type' => 'string',
                ];
                $block->attributes['identifyColor'] = [
                    'type' => 'string',
                ];
            }
        }

        /**
         * Make sure ServerSideRender blocks are rendererd correctly in editor.
         * As example: 'core/latest-comments'
         * https://github.com/brainstormforce/ultimate-addons-for-gutenberg/blob/master/classes/class-uagb-loader.php#L136-L194
         *
         * @since 3.1.0 function renamed and migrated from AdvancedGutenbergMain
         * @since 2.14.0
         */
        public static function removeAttributes($result, $server, $request)
        {
            if (strpos($request->get_route(), '/wp/v2/block-renderer') !== false) {
                if (isset($request['attributes'])) {
                    $attributes = $request['attributes'];

                    if (isset($attributes['advgbBlockControls'])) {
                        unset($attributes['advgbBlockControls']);
                    }

                    if (isset($attributes['customStyle'])) {
                        unset($attributes['customStyle']);
                    }
                    if (isset($attributes['identifyColor'])) {
                        unset($attributes['identifyColor']);
                    }

                    $request['attributes'] = $attributes;
                }
            }

            return $result;
        }

        /**
         * Get a Block control value from database option
         *
         * @param string $name Setting name - e.g. 'schedule' from advgb_block_controls > controls
         * @param bool $default Default value when $setting doesn't exist in $option
         *
         * @return bool
         * @since 3.1.0
         *
         */
        public static function getControlValue($name, $default)
        {
            $settings = get_option('advgb_block_controls');

            $value = isset($settings['controls'][$name])
                ? (bool) $settings['controls'][$name]
                : (bool) $default;

            return $value;
        }

        /**
         * Check if Block controls are enabled for a particular block
         *
         * @param string $block Block name. e.g. 'core/paragraph'
         *
         * @return bool
         * @since 3.1.0
         *
         */
        public static function isBlockEnabled($block)
        {
            $settings = get_option('advgb_block_controls');

            if (
                $settings
                && isset($settings['inactive_blocks'])
                && is_array($settings['inactive_blocks'])
                && count($settings['inactive_blocks']) > 0
                && in_array($block, $settings['inactive_blocks'])
            ) {
                return false;
            }

            return true;
        }

        /**
         * Save block controls page data
         *
         * @return boolean true on success, false on failure
         * @since 3.1.0
         */
        public static function save()
        {
            if (!current_user_can('activate_plugins')) {
                return false;
            }

            // Controls
            if (isset($_POST['save_controls'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- we check nonce below
                if (
                    !wp_verify_nonce(
                        sanitize_key($_POST['advgb_controls_settings_nonce_field']),
                        'advgb_controls_settings_nonce'
                    )
                ) {
                    return false;
                }

                $advgb_block_controls = get_option('advgb_block_controls');
                $advgb_block_controls['controls']['schedule'] = isset($_POST['schedule_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['user_role'] = isset($_POST['user_role_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['device_type'] = isset($_POST['device_type_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['device_width'] = isset($_POST['device_width_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['archive'] = isset($_POST['archive_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['page'] = isset($_POST['page_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['browser_device'] = isset($_POST['browser_device_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['operating_system'] = isset($_POST['operating_system_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['cookie'] = isset($_POST['cookie_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['user_meta'] = isset($_POST['user_meta_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['post_meta'] = isset($_POST['post_meta_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['query_string'] = isset($_POST['query_string_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['capabilities'] = isset($_POST['capabilities_control']) ? (bool) 1 : (bool) 0;
                $advgb_block_controls['controls']['presets'] = isset($_POST['presets_control']) ? (bool) 1 : (bool) 0;

                update_option('advgb_block_controls', $advgb_block_controls, false);

                wp_safe_redirect(
                    add_query_arg(
                        [
                            'save' => 'success'
                        ],
                        str_replace(
                            '/wp-admin/',
                            '',
                            sanitize_url($_POST['_wp_http_referer']) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                        )
                    )
                );
            } // Blocks
            elseif (isset($_POST['save_blocks'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- we check nonce below
                if (
                    !wp_verify_nonce(
                        sanitize_key($_POST['advgb_controls_block_nonce_field']),
                        'advgb_controls_block_nonce'
                    )
                ) {
                    return false;
                }

                if (
                    isset($_POST['blocks_list'])
                    && isset($_POST['active_blocks'])
                    && is_array($_POST['active_blocks'])
                ) {
                    $blocks_list = array_map(
                        'sanitize_text_field',
                        json_decode(stripslashes($_POST['blocks_list'])) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    );
                    $active_blocks = array_map('sanitize_text_field', $_POST['active_blocks']);
                    $inactive_blocks = array_values(array_diff($blocks_list, $active_blocks));

                    // Save block controls
                    $block_controls = get_option('advgb_block_controls');
                    $block_controls['active_blocks'] = isset($active_blocks) ? $active_blocks : '';
                    $block_controls['inactive_blocks'] = isset($inactive_blocks) ? $inactive_blocks : '';

                    update_option('advgb_block_controls', $block_controls, false);

                    // Redirect with success message
                    wp_safe_redirect(
                        add_query_arg(
                            [
                                'tab' => 'blocks',
                                'save' => 'success'
                            ],
                            str_replace(
                                '/wp-admin/',
                                '',
                                sanitize_url($_POST['_wp_http_referer']) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                            )
                        )
                    );
                } else {
                    // Redirect with error message / Nothing was saved
                    wp_safe_redirect(
                        add_query_arg(
                            [
                                'save' => 'error'
                            ],
                            str_replace(
                                '/wp-admin/',
                                '',
                                sanitize_url($_POST['_wp_http_referer']) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                            )
                        )
                    );
                }
            } else {
                // Nothing to do here
            }

            return false;
        }

        /**
         * Get controls status (enabled/disabled)
         *
         * @return array    e.g.['schedule' => true, 'user_role' => true]
         * @since 3.1.1
         */
        private static function getControlsArray()
        {
            $block_controls = get_option('advgb_block_controls');
            $result = [];
            $controls = [
                'schedule',
                'user_role',
                'device_type',
                'device_width',
                'archive',
                'page',
                'browser_device',
                'operating_system',
                'cookie',
                'user_meta',
                'post_meta',
                'query_string',
                'capabilities',
                'presets'
            ];

            if ($block_controls) {
                foreach ($controls as $item) {
                    $result[$item] = isset($block_controls['controls'][$item])
                        ? (bool) $block_controls['controls'][$item]
                        : (bool) 1;
                }
            } else {
                foreach ($controls as $item) {
                    $result[$item] = (bool) 1;
                }
            }

            return $result;
        }

        public static function getPresets($retain_key = false)
        {
            $presets = (array) get_option('advgb_block_control_presets', []);
            if ($retain_key) {
                return $presets;
            }
            $formatted_presets = [];
            foreach ($presets as $id => $preset) {
                $formatted_presets[] = array_merge($preset, ['id' => $id]);
            }

            return $formatted_presets;
        }

        public static function savePreset($preset_data)
        {
            $presets = self::getPresets();
            $preset_id = $preset_data['id'] ?? uniqid('preset_');
            $presets[$preset_id] = $preset_data;
            update_option('advgb_block_control_presets', $presets);
            return $preset_id;
        }

        public static function deletePreset($preset_id)
        {
            $presets = self::getPresets();
            unset($presets[$preset_id]);
            update_option('advgb_block_control_presets', $presets);
        }

        /**
         * Javascript objects with controls configuration to load in Admin.
         * 'advgb_block_controls_vars' object with controls configuration.
         * 'advgb_blocks_list' object with all the saved blocks in 'advgb_blocks_list' option.
         *
         * @return void
         * @since 3.1.0
         */
        public static function adminData()
        {
            // Build blocks form and add filters functions
            wp_add_inline_script(
                'advgb_main_js',
                "window.addEventListener('load', function () {
                    advgbGetBlockControls(
                        advgb_block_controls_vars.inactive_blocks,
                        '#advgb_block_controls_nonce_field',
                        'advgb_block_controls',
                        " . wp_json_encode(self::defaultExcludedBlocks()) . "
                    );
                });"
            );
            do_action('enqueue_block_editor_assets');

            // Block categories
            $blockCategories = array();
            if (function_exists('get_block_categories')) {
                $blockCategories = get_block_categories(get_post());
            } elseif (function_exists('gutenberg_get_block_categories')) {
                $blockCategories = gutenberg_get_block_categories(get_post());
            }
            wp_add_inline_script(
                'wp-blocks',
                sprintf('wp.blocks.setCategories( %s );', wp_json_encode($blockCategories)),
                'after'
            );

            // Block types
            $block_type_registry = \WP_Block_Type_Registry::get_instance();
            foreach ($block_type_registry->get_all_registered() as $block_name => $block_type) {
                if (!empty($block_type->editor_script)) {
                    wp_enqueue_script($block_type->editor_script);
                }
            }

            /* Get blocks saved in advgb_blocks_list option to include the ones that are missing
             * as result of javascript method wp.blocks.getBlockTypes()
             * e.g. blocks registered only via PHP
             */
            $advgb_blocks_list = get_option('advgb_blocks_list');
            if ($advgb_blocks_list && is_array($advgb_blocks_list)) {
                $saved_blocks = $advgb_blocks_list;
            } else {
                $saved_blocks = [];
            }
            wp_localize_script(
                'advgb_main_js',
                'advgb_blocks_list',
                $saved_blocks
            );

            // Active and inactive blocks
            $block_controls = get_option('advgb_block_controls');
            if (
                $block_controls
                && isset($block_controls['active_blocks'])
                && isset($block_controls['inactive_blocks'])
                && is_array($block_controls['active_blocks'])
                && is_array($block_controls['inactive_blocks'])
            ) {
                wp_localize_script(
                    'wp-blocks',
                    'advgb_block_controls_vars',
                    [
                        'controls' => self::getControlsArray(),
                        'active_blocks' => $block_controls['active_blocks'],
                        'inactive_blocks' => $block_controls['inactive_blocks'],

                        'user_roles' => self::getUserRoles(),
                        'taxonomies' => self::getTaxonomies(),
                        'page' => self::getPages(),
                        'capabilities' => self::getAllCapabilities(),
                        'presets' => self::getPresets()
                    ]
                );
            } else {
                // Nothing saved in database for current user role. Set empty (access to all blocks)
                wp_localize_script(
                    'wp-blocks',
                    'advgb_block_controls_vars',
                    [
                        'controls' => self::getControlsArray(),
                        'active_blocks' => [],
                        'inactive_blocks' => [],

                        'user_roles' => self::getUserRoles(),
                        'taxonomies' => self::getTaxonomies(),
                        'page' => self::getPages(),
                        'capabilities' => self::getAllCapabilities(),
                        'presets' => self::getPresets()
                    ]
                );
            }
        }

        /**
         * Javascript objects with controls configuration to load in Editor.
         * 'advgb_block_controls_vars' object with controls configuration.
         *
         * @return void
         * @since 3.1.0
         */
        public static function editorData()
        {
            $advgb_block_controls = get_option('advgb_block_controls');

            if (
                $advgb_block_controls
                && isset($advgb_block_controls['inactive_blocks'])
                && is_array($advgb_block_controls['inactive_blocks'])
                && count($advgb_block_controls['inactive_blocks']) > 0
            ) {
                // Merge non supported saved and manually defined blocks
                $non_supported = array_merge(
                    $advgb_block_controls['inactive_blocks'],
                    self::defaultExcludedBlocks()
                );
                $non_supported = array_unique($non_supported);
            } else {
                // Non supported manually defined blocks
                $non_supported = self::defaultExcludedBlocks();
            }

            // Output js variable
            wp_localize_script(
                'wp-blocks',
                'advgb_block_controls_vars',
                [
                    'non_supported' => $non_supported,
                    'controls' => self::getControlsArray(),
                    'user_roles' => self::getUserRoles(),
                    'taxonomies' => self::getTaxonomies(),
                    'page' => self::getPages(),
                    'capabilities' => self::getAllCapabilities(),
                    'presets' => self::getPresets()
                ]
            );
        }

        public static function getAllCapabilities()
        {
            global $wp_roles;

            if ( empty( $wp_roles->roles ) || ! is_array( $wp_roles->roles ) ) {
                return [];
            }

            $capability_arrays = array_map(
                function ($role) {
                    return ! empty( $role['capabilities'] ) && is_array( $role['capabilities'] )
                        ? array_keys( $role['capabilities'] )
                        : [];
                },
                $wp_roles->roles
            );

            $capability_arrays = array_values( array_filter( $capability_arrays ) );

            if ( empty( $capability_arrays ) ) {
                return [];
            }

            $merged = call_user_func_array( 'array_merge', $capability_arrays );

            return array_values( array_unique( $merged ) );
        }



        /**
         * Block controls support for these blocks is not available
         *
         * @return void
         * @since 3.1.0
         */
        public static function defaultExcludedBlocks()
        {
            return [
                'core/freeform',
                'core/legacy-widget',
                'core/widget-area',
                'core/column',
                'advgb/tab',
                'advgb/column',
                'advgb/accordion' // @TODO - Deprecated block. Remove later.
            ];
        }

        /**
         * Enqueue assets for editor
         *
         * @param $wp_editor_dep Block editor dependency based on current screen. e.g. 'wp-editor'
         *
         * @return void
         * @since 3.1.0
         *
         */
        public static function editorAssets($wp_editor_dep)
        {
            if (Utilities::settingIsEnabled('block_controls')) {

                wp_enqueue_style('wp-components');

                Utilities::enqueueToolTipsAssets();

                wp_enqueue_style(
                    'advgb-preset-manager',
                    ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/css/block-controls-preset-manager.css',
                    [],
                    ADVANCED_GUTENBERG_VERSION
                );

                wp_enqueue_script(
                    'advgb_preset_data_manager',
                    ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/blocks/preset-data-manager.js',
                    [
                        'wp-blocks',
                        'wp-i18n',
                        'wp-element',
                        'wp-data',
                        'wp-plugins',
                        'wp-compose'
                    ],
                    ADVANCED_GUTENBERG_VERSION,
                    false
                );

                wp_enqueue_script(
                    'advgb_block_controls',
                    ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/blocks/block-controls.js',
                    [
                        'advgb_preset_data_manager',
                        'ppb-tooltips-js',
                        'wp-blocks',
                        'wp-i18n',
                        'wp-element',
                        'wp-data',
                        $wp_editor_dep,
                        'wp-plugins',
                        'wp-compose'
                    ],
                    ADVANCED_GUTENBERG_VERSION,
                    true
                );

                wp_enqueue_script(
                    'advgb-preset-manager',
                    ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/blocks/preset-manager.js',
                    ['advgb_preset_data_manager', 'wp-components', 'wp-element', 'wp-i18n', 'wp-api-fetch'],
                    ADVANCED_GUTENBERG_VERSION,
                    true
                );

                wp_set_script_translations( 'advgb_block_controls', 'advanced-gutenberg' );
            }
        }

        /**
         * Retrieve User roles
         *
         * @return array
         * @since 3.1.0
         *
         */
        public static function getUserRoles()
        {
            global $wp_roles;
            $result = [];
            $roles_list = $wp_roles->get_names();
            foreach ($roles_list as $roles => $role_name) {
                $result[] = [
                    'slug' => $roles,
                    'title' => esc_attr(translate_user_role($role_name))
                ];
            }

            return $result;
        }

        /**
         * Retrieve Taxonomies
         *
         * @return array
         * @since 3.1.1
         *
         */
        public static function getTaxonomies()
        {
            $taxonomies = get_taxonomies();
            $result = [];
            $exclude = [
                'nav_menu',
                'link_category',
                'post_format',
                'wp_theme',
                'wp_template_part_area'
            ];

            foreach ($taxonomies as $item) {
                $tax = get_taxonomy($item);

                if (!in_array($item, $exclude)) {
                    $result[] = [
                        'slug' => $item,
                        'title' => $tax->labels->singular_name
                    ];
                }
            }

            return $result;
        }

        /**
         * Retrieve Taxonomies selected in a block
         *
         * @param array $selected Selected taxonomies in the block
         *
         * @return array
         * @since 3.1.1
         *
         */
        public static function getBlockTaxonomies($selected)
        {
            if (!is_array($selected) || !count($selected)) {
                return [];
            }

            global $wp_taxonomies;

            $result = [];
            $taxonomies = $selected;

            foreach ($wp_taxonomies as $key => $value) {
                if (in_array($key, $taxonomies)) {
                    $result[] = [
                        'slug' => $key,
                        'name' => $value->labels->singular_name
                    ];
                }
            }

            return $result;
        }

        /**
         * Retrieve Terms
         *
         * @param array $data Taxonomy slugs and term ids or search word
         *
         * @return array
         * @since 3.1.1
         *
         */
        public static function getTerms($data)
        {
            if (
                isset($data['taxonomies'])
                && is_array($data['taxonomies'])
                && count($data['taxonomies'])
            ) {
                $taxonomies = array_map('sanitize_text_field', $data['taxonomies']);
                $args['taxonomy'] = $taxonomies;

                // Note: can't use search and include in the same request
                if (isset($data['search']) && !empty($data['search'])) {
                    $args['search'] = sanitize_text_field($data['search']);
                    $args['number'] = 10;
                }

                if (isset($data['ids']) && is_array($data['ids']) && count($data['ids'])) {
                    $args['include'] = array_map('intval', $data['ids']);
                }

                $result = [];

                /*/ Include "All <taxonomy> terms" options
                            global $wp_taxonomies;
                            foreach( $taxonomies as $tax ) {
                                $result[] = [
                                    'slug' => "all__{$tax}",
                                    'title' => sprintf(
                                            __( 'All %s terms', 'advanced-gutenberg' ),
                                            $wp_taxonomies[$tax]->labels->singular_name
                                        ),
                                    'tax' => $tax,
                                ];
                            }*/

                $term_query = new \WP_Term_Query($args);

                if (!empty($term_query->terms)) {
                    foreach ($term_query->terms as $term) {
                        $taxLabel = $term->taxonomy;

                        // Get human readable taxonomy name
                        $blockTaxonomies = self::getBlockTaxonomies($taxonomies);
                        if (count($blockTaxonomies)) {
                            foreach ($blockTaxonomies as $tax) {
                                if ($tax['slug'] === $term->taxonomy) {
                                    $taxLabel = $tax['name'];
                                    break;
                                }
                            }
                        }

                        $result[] = [
                            'slug' => $term->term_id,
                            'title' => $term->name . ' (' . $taxLabel . ')',
                            'tax' => $term->taxonomy,
                        ];
                    }
                }

                return $result;
            }

            return [];
        }

        /**
         * Retrieve pages
         *
         * @return array
         * @since 3.1.1
         *
         */
        public static function getPages()
        {
            return [
                [
                    'slug' => 'home',
                    'title' => __('Home', 'advanced-gutenberg')
                ],
                [
                    'slug' => 'blog',
                    'title' => __('Blog', 'advanced-gutenberg')
                ],
                [
                    'slug' => 'archive',
                    'title' => __('Archive', 'advanced-gutenberg')
                ],
                [
                    'slug' => 'search',
                    'title' => __('Search', 'advanced-gutenberg')
                ],
                [
                    'slug' => 'page404',
                    'title' => __('404', 'advanced-gutenberg')
                ]
            ];
        }

        /**
         * Register custom REST API routes
         *
         * @return array
         * @since 3.1.1
         *
         */
        public static function registerCustomRoutes()
        {
            // Fetch searched terms from all selected taxonomies
            register_rest_route(
                'advgb/v1',
                '/terms',
                [
                    'methods' => 'GET',
                    'callback' => ['PublishPress\Blocks\Controls', 'getTerms'],
                    'args' => [
                        'search' => [
                            'validate_callback' => ['PublishPress\Blocks\Controls', 'validateString'],
                            'sanitize_callback' => 'sanitize_text_field',
                            'required' => false,
                            'type' => 'string'
                        ],
                        'ids' => [
                            'validate_callback' => ['PublishPress\Blocks\Controls', 'validateArray'],
                            'sanitize_callback' => ['PublishPress\Blocks\Controls', 'sanitizeNumbersArray'],
                            'required' => false,
                            'type' => 'array'
                        ],
                        'taxonomies' => [
                            'validate_callback' => ['PublishPress\Blocks\Controls', 'validateArray'],
                            'sanitize_callback' => ['PublishPress\Blocks\Controls', 'sanitizeStringsArray'],
                            'required' => true,
                            'type' => 'array'
                        ],
                    ],
                    'permission_callback' => function () {
                        return current_user_can('edit_others_posts');
                    }
                ]
            );
        }

        /**
         * Check if value is a string
         *
         * @param $value Value to check
         *
         * @return array
         * @since 3.1.1
         *
         */
        public static function validateString($value)
        {
            return is_string($value);
        }

        /**
         * Check if value is an array
         *
         * @param $value Value to check
         *
         * @return array
         * @since 3.1.1
         *
         */
        public static function validateArray($value)
        {
            return is_array($value) && count($value);
        }

        /**
         * Sanitize an array of strings
         *
         * @param $value Value to check
         *
         * @return array
         * @since 3.1.1
         *
         */
        public static function sanitizeStringsArray($value)
        {
            return array_map('sanitize_key', $value);
        }

        /**
         * Sanitize an array of numbers
         *
         * @param $value Value to check
         *
         * @return array
         * @since 3.1.1
         *
         */
        public static function sanitizeNumbersArray($value)
        {
            return array_map('intval', $value);
        }
    }
}
