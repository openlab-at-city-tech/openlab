<?php

namespace TheLion\OutoftheBox;

class Helpers
{
    /**
     *  Do so magic to make sure that the path is correctly set according to the Dropbox Rules.
     *
     * @param string $path
     *
     * @return string
     */
    public static function clean_folder_path($path)
    {
        $path = html_entity_decode($path);
        $special_chars = ['?', '<', '>', ':', '"', '*', '|'];
        $path = str_replace($special_chars, '', $path);
        $path = trim($path, '/');
        $path = str_replace(['\\', '//'], '/', $path);

        if (!empty($path)) {
            $path = '/'.$path;
        }

        return trim($path);

    }

    public static function get_pathinfo($path)
    {
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $m);
        if (isset($m[1])) {
            $ret['dirname'] = $m[1];
        }
        if (isset($m[2])) {
            $ret['basename'] = $m[2];
        }
        if (isset($m[5])) {
            $ret['extension'] = $m[5];
        }
        if (isset($m[3])) {
            $ret['filename'] = $m[3];
        }

        if ('.' === substr($path, -1)) {
            $ret['basename'] .= '.';
            unset($ret['extension']);
        }

        return $ret;
    }

    public static function filter_filename($filename, $beautify = true)
    {
        // sanitize filename
        $dangerous_characters = ['"', '/', '\\', '?', '#', '*', '|', '<', '>', ':'];
        $filename = str_replace($dangerous_characters, '-', $filename);

        // avoids ".", ".." or ".hiddenFiles"
        $filename = ltrim($filename, '.-');
        // optional beautification
        if ($beautify) {
            $filename = self::beautify_filename($filename);
        }
        // maximise filename length to 255 bytes http://serverfault.com/a/9548/44086
        $pathinfo = self::get_pathinfo($filename);
        $ext = isset($pathinfo['extension']) ? ($pathinfo['extension']) : false;
        $fn = isset($pathinfo['filename']) ? ($pathinfo['filename']) : false;

        if (!extension_loaded('mbstring')) {
            return $fn.($ext ? '.'.$ext : '');
        }

        return mb_strcut($fn, 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($fn)).($ext ? '.'.$ext : '');
    }

    public static function beautify_filename($filename)
    {
        // reduce consecutive characters
        $filename = preg_replace([
            // "file   name.zip" becomes "file-name.zip"
            '/ +/',
            // "file___name.zip" becomes "file-name.zip"
            '/_+/',
            // "file---name.zip" becomes "file-name.zip"
            '/-+/',
        ], '-', $filename);
        $filename = preg_replace([
            // "file--.--.-.--name.zip" becomes "file.name.zip"
            '/-*\.-*/',
            // "file...name..zip" becomes "file.name.zip"
            '/\.{2,}/',
        ], '.', $filename);
        // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
        if (function_exists('mb_strtolower') && function_exists('mb_detect_encoding')) {
            $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        }
        // ".file-name.-" becomes "file-name"
        return trim($filename, '.-');
    }

    /**
     * Checks if a particular user has a role.
     * Returns true if a match was found.
     *
     * @param array $roles_to_check roles array
     * @param /WP_User $user           WP_User object
     *
     * @return bool
     */
    public static function check_user_role($roles_to_check = [], $user = null)
    {
        if (in_array('all', $roles_to_check)) {
            return true;
        }

        if (in_array('none', $roles_to_check)) {
            return false;
        }

        if (in_array('guest', $roles_to_check)) {
            return true;
        }

        if (!is_user_logged_in() && null === $user) {
            return false;
        }

        /* If administrators are locked out, uncomment the following lines
         * See FAQ article: https://florisdeleeuwnl.zendesk.com/hc/en-us/articles/360010544560
        */

        // if (current_user_can('setup_network')) {
        //     return true;
        // }

        if (null === $user) {
            $user = wp_get_current_user();
        }

        if (empty($user) || (!($user instanceof \WP_User))) {
            return false;
        }

        foreach ($user->roles as $role) {
            if (in_array($role, $roles_to_check)) {
                return true;
            }
        }

        foreach ($roles_to_check as $role) {
            if ((string) $user->ID === $role) {
                return true;
            }
        }

        return false;
    }

    public static function get_user_name()
    {
        if (!is_user_logged_in()) {
            return esc_html__('Anonymous user', 'wpcloudplugins');
        }

        $current_user = wp_get_current_user();

        return $current_user->display_name;
    }

    public static function get_user_email()
    {
        if (!is_user_logged_in()) {
            return null;
        }

        $current_user = wp_get_current_user();

        return $current_user->user_email;
    }

    public static function get_user_ip()
    {
        // User IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }

        return apply_filters('wpb_get_ip', $ip);
    }

    public static function get_user_location($ip = null)
    {
        $userip = empty($ip) ? self::get_user_ip() : $ip;

        try {
            $geolocation = (unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$userip)));

            if (false !== $geolocation && 200 === $geolocation['geoplugin_status']) {
                return $geolocation['geoplugin_city'].', '.$geolocation['geoplugin_region'].', '.$geolocation['geoplugin_countryName'];
            }

            return '-location unknown-';
        } catch (\Exception $ex) {
            return '-location unknown-';
        }
    }

    public static function get_all_users_and_roles()
    {
        // Build list
        $list = [];

        // Get User Roles
        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new \WP_Roles();
        }

        $wp_roles_names = $wp_roles->get_names();

        // Add custom roles
        $wp_roles_names['guest'] = esc_html__('Anonymous user', 'wpcloudplugins');
        $wp_roles_names['all'] = esc_html__('Everyone', 'wpcloudplugins');
        $wp_roles_names['none'] = esc_html__('None', 'wpcloudplugins');

        foreach ($wp_roles_names as $wp_role_id => $wp_role_name) {
            $list[] = [
                'value' => (string) $wp_role_id,
                'id' => (string) $wp_role_id,
                'text' => $wp_role_name,
                'type' => 'role',
                'searchBy' => $wp_role_name,
                'img' => get_avatar_url('role', ['size' => '32']),
            ];
        }

        // Get Users
        $users = get_users(['fields' => ['user_login', 'display_name', 'id']]);
        $user_count = count($users);
        $i = 1;

        foreach ($users as $wp_user) {
            if ($i > 5000) {
                // Don't show individual users for very large sites
                break;
            }
            $list[] = [
                'value' => (string) $wp_user->id,
                'id' => (string) $wp_user->id,
                'text' => (empty($wp_user->display_name) ? $wp_user->user_login : $wp_user->display_name),
                'type' => 'user',
                'searchBy' => $wp_user->user_login,
                'img' => ($user_count < 1000) ? get_avatar_url($wp_user->id, ['size' => '32']) : OUTOFTHEBOX_ROOTPATH.'/css/images/usericon.png',
            ];
            ++$i;
        }

        // Sort the list by name ASC. First Roles, than Users
        $sort = [];
        foreach ($list as $k => $v) {
            $sort['is_role'][$k] = ('role' === $v['type']);
            $sort['sort'][$k] = $v['text'];
        }

        @array_multisort($sort['is_role'], SORT_DESC, SORT_REGULAR, $sort['sort'], SORT_ASC, SORT_NATURAL, $list, SORT_ASC);

        return $list;
    }

    public static function get_linked_users($path)
    {
        $linked_users = [];
        $all_parent_folders = array_filter(explode('/', $path));

        // First obtain all users that are manually linked to the entry or its parents
        global $wpdb;

        $meta_query = [
            'relation' => 'OR',
            [
                'key' => $wpdb->prefix.'out_of_the_box_linkedto',
                'value' => '"/"',
                'compare' => 'LIKE',
            ],
        ];

        $path_arr = $all_parent_folders;

        do {
            $meta_query[] = [
                'key' => $wpdb->prefix.'out_of_the_box_linkedto',
                'value' => '"/'.implode('/', $path_arr).'"',
                'compare' => 'LIKE',
            ];
            array_pop($path_arr);
        } while (count($path_arr) > 0);

        $manually_linked_users = (get_users(['meta_query' => $meta_query]));

        foreach ($manually_linked_users as $userdata) {
            $linked_users[$userdata->ID] = $userdata;
        }

        /* Secondly obtain all users that are automatically linked to the entry or its parents
         * The folder has to contain the email address of the user */

        foreach ($all_parent_folders as $parent) {
            $extracted_email = Helpers::extract_email_from_string($parent);

            if (false === $extracted_email) {
                continue;
            }

            $userdata = \WP_User::get_data_by('email', $extracted_email);

            if (!$userdata) {
                continue;
            }

            $linked_users[$userdata->ID] = $userdata;
        }

        return $linked_users;
    }

    public static function return_bytes($size_str)
    {
        if (empty($size_str)) {
            return $size_str;
        }

        $unit = substr($size_str, -1);
        if (('B' === $unit || 'b' === $unit) && (!ctype_digit(substr($size_str, -2)))) {
            $unit = substr($size_str, -2, 1);
        }

        switch ($unit) {
            case 'M': case 'm': return (int) $size_str * 1048576;

            case 'K': case 'k': return (int) $size_str * 1024;

            case 'G': case 'g': return (int) $size_str * 1073741824;

            default: return $size_str;
        }
    }

    public static function bytes_to_size_1024($bytes, $precision = 0)
    {
        if (empty($bytes)) {
            return $bytes;
        }

        // human readable format -- powers of 1024
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];

        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
    }

    public static function remove_element_with_value($array, $key, $value)
    {
        foreach ($array as $subKey => $subArray) {
            if ($subArray[$key] == $value) {
                unset($array[$subKey]);
            }
        }

        return $array;
    }

    public static function find_item_in_array_with_value($array, $key, $search)
    {
        $columns = array_map(function ($e) use ($key) {
            return is_object($e) ? $e->{$key} : $e[$key];
        }, $array);

        return array_search($search, $columns);
    }

    /**
     * Extract the email address from a string.
     *
     * @param string $string
     *
     * @return bool|string
     */
    public static function extract_email_from_string($string)
    {
        $result = preg_match('/\b[^\s]+@[\w|.]+/', $string, $matches);

        if (empty($result)) {
            return false;
        }

        $email = $matches[0];

        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return $email;
    }

    public static function compress_css($minify)
    {
        $minify = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify);
        $minify = str_replace(': ', ':', $minify);

        return str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $minify);
    }

    public static function compress_html($minify)
    {
        $search = [
            '/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s', // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // Remove HTML comments
        ];

        $replace = [
            '>',
            '<',
            '\\1',
            '',
        ];

        return preg_replace($search, $replace, $minify);
    }

    public static function apply_placeholders($value, $context = null, $extra = [])
    {
        // Add User Placeholders for Guest users
        if (!isset($extra['user_data'])) {
            if (is_user_logged_in()) {
                $extra['user_data'] = wp_get_current_user();
            } else {
                $id = uniqid();
                if (!isset($_COOKIE['OftB-ID'])) {
                    $expire = time() + 60 * 60 * 24 * 7;
                    Helpers::set_cookie('OftB-ID', $id, $expire, COOKIEPATH, COOKIE_DOMAIN, false, false, 'strict');
                } else {
                    $id = $_COOKIE['OftB-ID'];
                }

                $extra['user_data'] = new \stdClass();
                $extra['user_data']->user_login = md5($id);
                $extra['user_data']->display_name = esc_html__('Guests', 'wpcloudplugins').' - '.$id;
                $extra['user_data']->ID = $id;
                $extra['user_data']->user_role = esc_html__('Anonymous user', 'wpcloudplugins');
            }
        }

        // User Placeholders
        if (isset($extra['user_data'])) {
            $user_data = $extra['user_data'];
            $value = strtr($value, [
                '%user_login%' => isset($user_data->user_login) ? $user_data->user_login : '',
                '%user_email%' => isset($user_data->user_email) ? $user_data->user_email : '',
                '%user_firstname%' => isset($user_data->user_firstname) ? $user_data->user_firstname : '',
                '%user_lastname%' => isset($user_data->user_lastname) ? $user_data->user_lastname : '',
                '%display_name%' => isset($user_data->display_name) ? $user_data->display_name : '',
                '%ID%' => isset($user_data->ID) ? $user_data->ID : '',
                '%user_role%' => isset($user_data->roles) ? implode(',', $user_data->roles) : '',
                '%user_registered%' => isset($user_data->user_registered) ? date('Y-m-d',strtotime($user_data->user_registered)) : ''
            ]);
        }

        // Custom User Meta Placeholders
        preg_match_all('/%usermeta_(?<key>.+)%/U', $value, $usermeta_requests, PREG_SET_ORDER, 0);

        if (!empty($usermeta_requests)) {
            foreach ($usermeta_requests as $usermeta_request) {
                $usermeta_placeholder = $usermeta_request[0];
                $usermeta_value = get_user_meta($user_data->ID, $usermeta_request['key'], true);
                $value = strtr($value, [
                    $usermeta_placeholder => !empty($usermeta_value) ? $usermeta_value : '',
                ]);
            }
        }

        // Extra Placeholders
        $value = strtr($value, [
            '%yyyy-mm-dd%' => date('Y-m-d'),
            '%jjjj-mm-dd%' => date('Y-m-d'), // Backward compatibility
            '%hh:mm%' => date('Hi'),
            '%ip%' => self::get_user_ip(),
            '%directory_separator%' => '/',
            '%uniqueID%' => get_option('out_of_the_box_uniqueID', 0),
        ]);

        // Location Placeholder
        if (false !== strpos($value, '%location%')) {
            // Geo location only if required
            $value = strtr($value, [
                '%location%' => self::get_user_location(),
            ]);
        }

        // WooCommerce Products Placeholders
        if (isset($extra['wc_product'])) {
            $value = strtr($value, [
                '%wc_product_id%' => $extra['wc_product']->get_id(),
                '%wc_product_sku%' => $extra['wc_product']->get_sku(),
                '%wc_product_name%' => $extra['wc_product']->get_name(),
            ]);

            if (isset($extra['wc_order'])) {
                $product_quantity = 0;
                foreach ($extra['wc_order']->get_items() as $item_id => $item_product) {
                    if ($item_product->get_product_id() == $extra['wc_product']->get_id()) {
                        $product_quantity = $extra['wc_order']->get_item_count($item_product->get_type());
                    }
                }

                $value = strtr($value, [
                    '%wc_product_quantity%' => $product_quantity,
                ]);
            }
        }

        // WooCommerce Order Placeholders
        if (isset($extra['wc_order'])) {
            $value = strtr($value, [
                '%wc_order_id%' => $extra['wc_order']->get_order_number(),
                '%wc_order_quantity%' => $extra['wc_order']->get_item_count(),
                '%wc_order_date_created%' => $extra['wc_order']->get_date_created()->format('Y-m-d'),
            ]);
        }

        return apply_filters('outofthebox_apply_placeholders', $value, $context, $extra);
    }

    public static function get_default_icon($mimetype, $is_dir = false)
    {
        $icon = 'unknown';

        if ($is_dir) {
            $icon = 'folder';
        } elseif (false !== strpos($mimetype, 'word')) {
            $icon = 'application-msword';
        } elseif (false !== strpos($mimetype, 'excel') || false !== strpos($mimetype, 'spreadsheet')) {
            $icon = 'application-vnd.ms-excel';
        } elseif (false !== strpos($mimetype, 'powerpoint') || false !== strpos($mimetype, 'presentation')) {
            $icon = 'application-vnd.ms-powerpoint';
        } elseif (false !== strpos($mimetype, 'access') || false !== strpos($mimetype, 'mdb')) {
            $icon = 'application-vnd.ms-access';
        } elseif (
            false !== strpos($mimetype, 'photoshop')
            || 'application/psd' === $mimetype
            || 'image/psd' === $mimetype
            ) {
            $icon = 'application-x-photoshop';
        } elseif (
            false !== strpos($mimetype, 'illustrator')
            || false !== strpos($mimetype, 'postscript')
            || false !== strpos($mimetype, 'svg')
            ) {
            $icon = 'vector';
        } elseif (false !== strpos($mimetype, 'image')) {
            $icon = 'image-x-generic';
        } elseif (false !== strpos($mimetype, 'audio')) {
            $icon = 'audio-x-generic';
        } elseif (false !== strpos($mimetype, 'video')) {
            $icon = 'video-x-generic';
        } elseif (false !== strpos($mimetype, 'pdf')) {
            $icon = 'application-pdf';
        } elseif (false !== strpos($mimetype, 'zip')
                || false !== strpos($mimetype, 'archive')
                || false !== strpos($mimetype, 'tar')
                || false !== strpos($mimetype, 'compressed')
        ) {
            $icon = 'application-zip';
        } elseif (false !== strpos($mimetype, 'html')
            || false !== strpos($mimetype, 'application/x-httpd-php')
            || false !== strpos($mimetype, 'application/javascript')
            ) {
            $icon = 'text-xml';
        } elseif (false !== strpos($mimetype, 'application/exe')
                || false !== strpos($mimetype, 'application/x-msdownload')
                || false !== strpos($mimetype, 'application/x-exe')
                || false !== strpos($mimetype, 'application/x-winexe')
                || false !== strpos($mimetype, 'application/msdos-windows')
                || false !== strpos($mimetype, 'application/x-executable')
        ) {
            $icon = 'application-x-executable';
        } elseif (false !== strpos($mimetype, 'url')
        || false !== strpos($mimetype, 'shortcut')
        ) {
            $icon = 'shortcut';
        } elseif (false !== strpos($mimetype, 'text')) {
            $icon = 'text-x-generic';
        }

        return OUTOFTHEBOX_ICON_SET.'32x32/'.$icon.'.png';
    }

    public static function is_close_to_memory_limit()
    {
        $memory_limit = self::return_bytes(ini_get('memory_limit'));
        $memory_usage = memory_get_usage();

        return ($memory_usage + (1000 * 1000)) > $memory_limit;
    }

    public static function get_free_memory_available()
    {
        $memory_limit = self::return_bytes(ini_get('memory_limit'));

        if ($memory_limit < 0) {
            if (defined('WP_MEMORY_LIMIT')) {
                $memory_limit = self::return_bytes(WP_MEMORY_LIMIT);
            } else {
                $memory_limit = 1024 * 1024 * 92; // Return 92MB if we can't get any reading on memory limits
            }
        }

        $memory_usage = memory_get_usage();

        $free_memory = $memory_limit - $memory_usage;

        if ($free_memory < (1024 * 1024 * 10)) {
            // Return a minimum of 10MB available
            return 1024 * 1024 * 10;
        }

        return $free_memory;
    }

    /**
     * Converts milliseconds to formatted time or seconds.
     *
     * @param int [$ms] The length of the media asset in milliseconds
     * @param bool [$seconds] Whether to return only seconds
     *
     * @return mixed The formatted length or total seconds of the media asset
     */
    public static function convert_ms_to_time($ms, $seconds = false)
    {
        if (empty($ms)) {
            return 0;
        }

        $total_seconds = ($ms / 1000);

        if ($seconds) {
            return $total_seconds;
        }
        $time = '';

        $value = [
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
        ];

        if ($total_seconds >= 3600) {
            $value['hours'] = floor($total_seconds / 3600);
            $total_seconds = $total_seconds % 3600;

            $time .= $value['hours'].':';
        }

        if ($total_seconds >= 60) {
            $value['minutes'] = floor($total_seconds / 60);
            $total_seconds = $total_seconds % 60;

            $time .= $value['minutes'].':';
        } else {
            $time .= '0:';
        }

        $value['seconds'] = floor($total_seconds);

        if ($value['seconds'] < 10) {
            $value['seconds'] = '0'.$value['seconds'];
        }

        $time .= $value['seconds'];

        return $time;
    }

    /**
     * Add $dep (script handle) to the array of dependencies for $handle.
     *
     * @param string $handle Script handle for which you want to add a dependency
     * @param string $dep    Script handle - the dependency you wish to add
     */
    public static function append_dependency($handle, $dep)
    {
        global $wp_scripts;

        $script = $wp_scripts->query($handle, 'registered');
        if (!$script) {
            return false;
        }
        if (!in_array($dep, $script->deps)) {
            $script->deps[] = $dep;
        }

        return true;
    }

    public static function is_deprecated($type, $name)
    {
        error_log("[WP Cloud Plugin message]: {$type} {$name} is deprecated.");

        ob_start();
        debug_print_backtrace();
        error_log(ob_get_clean());
    }

    public static function set_cookie($name, $value, $expire, $path, $domain, $secure, $httponly, $samesite = 'None')
    {
        if (PHP_VERSION_ID < 70300) {
            @setcookie($name, $value, $expire, "{$path}; samesite={$samesite}", $domain, $secure, $httponly);
        } else {
            @setcookie($name, $value, [
                'expires' => $expire,
                'path' => $path,
                'domain' => $domain,
                'samesite' => $samesite,
                'secure' => $secure,
                'httponly' => $httponly,
            ]);
        }
    }

    public static function encrypt($data)
    {
        // Fallback for servers without openssl support
        if (false === function_exists('openssl_encrypt') || false === in_array('aes-256-cbc', openssl_get_cipher_methods())) {
            return base64_encode($data);
        }

        $method = 'aes-256-cbc';
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);

        $first_encrypted = openssl_encrypt($data, $method, OUTOFTHEBOX_AUTH_KEY, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv.$first_encrypted);
    }

    public static function decrypt($input)
    {
        $mix = base64_decode($input);

        // Fallback for servers without openssl support
        if (false === function_exists('openssl_encrypt') || false === in_array('aes-256-cbc', openssl_get_cipher_methods())) {
            return $mix;
        }

        $method = 'aes-256-cbc';
        $iv_length = openssl_cipher_iv_length($method);

        $iv = substr($mix, 0, $iv_length);
        $first_encrypted = substr($mix, $iv_length);

        return openssl_decrypt($first_encrypted, $method, OUTOFTHEBOX_AUTH_KEY, OPENSSL_RAW_DATA, $iv);
    }

    public static function get_mimetype($extension = '')
    {
        if (empty($extension)) {
            return 'application/octet-stream';
        }

        $mime_types_map = [
            '123' => 'application/vnd.lotus-1-2-3',
            '3dml' => 'text/vnd.in3d.3dml',
            '3ds' => 'image/x-3ds',
            '3g2' => 'video/3gpp2',
            '3gp' => 'video/3gpp',
            '7z' => 'application/x-7z-compressed',
            'aab' => 'application/x-authorware-bin',
            'aac' => 'audio/x-aac',
            'aam' => 'application/x-authorware-map',
            'aas' => 'application/x-authorware-seg',
            'abw' => 'application/x-abiword',
            'ac' => 'application/pkix-attr-cert',
            'acc' => 'application/vnd.americandynamics.acc',
            'ace' => 'application/x-ace-compressed',
            'acu' => 'application/vnd.acucobol',
            'acutc' => 'application/vnd.acucorp',
            'adp' => 'audio/adpcm',
            'aep' => 'application/vnd.audiograph',
            'afm' => 'application/x-font-type1',
            'afp' => 'application/vnd.ibm.modcap',
            'ahead' => 'application/vnd.ahead.space',
            'ai' => 'application/postscript',
            'aif' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'air' => 'application/vnd.adobe.air-application-installer-package+zip',
            'ait' => 'application/vnd.dvb.ait',
            'ami' => 'application/vnd.amiga.ami',
            'apk' => 'application/vnd.android.package-archive',
            'appcache' => 'text/cache-manifest',
            'application' => 'application/x-ms-application',
            'apr' => 'application/vnd.lotus-approach',
            'arc' => 'application/x-freearc',
            'asc' => 'application/pgp-signature',
            'asf' => 'video/x-ms-asf',
            'asm' => 'text/x-asm',
            'aso' => 'application/vnd.accpac.simply.aso',
            'asx' => 'video/x-ms-asf',
            'atc' => 'application/vnd.acucorp',
            'atom' => 'application/atom+xml',
            'atomcat' => 'application/atomcat+xml',
            'atomsvc' => 'application/atomsvc+xml',
            'atx' => 'application/vnd.antix.game-component',
            'au' => 'audio/basic',
            'avi' => 'video/x-msvideo',
            'aw' => 'application/applixware',
            'azf' => 'application/vnd.airzip.filesecure.azf',
            'azs' => 'application/vnd.airzip.filesecure.azs',
            'azw' => 'application/vnd.amazon.ebook',
            'bat' => 'application/x-msdownload',
            'bcpio' => 'application/x-bcpio',
            'bdf' => 'application/x-font-bdf',
            'bdm' => 'application/vnd.syncml.dm+wbxml',
            'bed' => 'application/vnd.realvnc.bed',
            'bh2' => 'application/vnd.fujitsu.oasysprs',
            'bin' => 'application/octet-stream',
            'blb' => 'application/x-blorb',
            'blorb' => 'application/x-blorb',
            'bmi' => 'application/vnd.bmi',
            'bmp' => 'image/x-ms-bmp',
            'book' => 'application/vnd.framemaker',
            'box' => 'application/vnd.previewsystems.box',
            'boz' => 'application/x-bzip2',
            'bpk' => 'application/octet-stream',
            'btif' => 'image/prs.btif',
            'buffer' => 'application/octet-stream',
            'bz' => 'application/x-bzip',
            'bz2' => 'application/x-bzip2',
            'c' => 'text/x-c',
            'c11amc' => 'application/vnd.cluetrust.cartomobile-config',
            'c11amz' => 'application/vnd.cluetrust.cartomobile-config-pkg',
            'c4d' => 'application/vnd.clonk.c4group',
            'c4f' => 'application/vnd.clonk.c4group',
            'c4g' => 'application/vnd.clonk.c4group',
            'c4p' => 'application/vnd.clonk.c4group',
            'c4u' => 'application/vnd.clonk.c4group',
            'cab' => 'application/vnd.ms-cab-compressed',
            'caf' => 'audio/x-caf',
            'cap' => 'application/vnd.tcpdump.pcap',
            'car' => 'application/vnd.curl.car',
            'cat' => 'application/vnd.ms-pki.seccat',
            'cb7' => 'application/x-cbr',
            'cba' => 'application/x-cbr',
            'cbr' => 'application/x-cbr',
            'cbt' => 'application/x-cbr',
            'cbz' => 'application/x-cbr',
            'cc' => 'text/x-c',
            'cct' => 'application/x-director',
            'ccxml' => 'application/ccxml+xml',
            'cdbcmsg' => 'application/vnd.contact.cmsg',
            'cdf' => 'application/x-netcdf',
            'cdkey' => 'application/vnd.mediastation.cdkey',
            'cdmia' => 'application/cdmi-capability',
            'cdmic' => 'application/cdmi-container',
            'cdmid' => 'application/cdmi-domain',
            'cdmio' => 'application/cdmi-object',
            'cdmiq' => 'application/cdmi-queue',
            'cdx' => 'chemical/x-cdx',
            'cdxml' => 'application/vnd.chemdraw+xml',
            'cdy' => 'application/vnd.cinderella',
            'cer' => 'application/pkix-cert',
            'cfs' => 'application/x-cfs-compressed',
            'cgm' => 'image/cgm',
            'chat' => 'application/x-chat',
            'chm' => 'application/vnd.ms-htmlhelp',
            'chrt' => 'application/vnd.kde.kchart',
            'cif' => 'chemical/x-cif',
            'cii' => 'application/vnd.anser-web-certificate-issue-initiation',
            'cil' => 'application/vnd.ms-artgalry',
            'cla' => 'application/vnd.claymore',
            'class' => 'application/java-vm',
            'clkk' => 'application/vnd.crick.clicker.keyboard',
            'clkp' => 'application/vnd.crick.clicker.palette',
            'clkt' => 'application/vnd.crick.clicker.template',
            'clkw' => 'application/vnd.crick.clicker.wordbank',
            'clkx' => 'application/vnd.crick.clicker',
            'clp' => 'application/x-msclip',
            'cmc' => 'application/vnd.cosmocaller',
            'cmdf' => 'chemical/x-cmdf',
            'cml' => 'chemical/x-cml',
            'cmp' => 'application/vnd.yellowriver-custom-menu',
            'cmx' => 'image/x-cmx',
            'cod' => 'application/vnd.rim.cod',
            'com' => 'application/x-msdownload',
            'conf' => 'text/plain',
            'cpio' => 'application/x-cpio',
            'cpp' => 'text/x-c',
            'cpt' => 'application/mac-compactpro',
            'crd' => 'application/x-mscardfile',
            'crl' => 'application/pkix-crl',
            'crt' => 'application/x-x509-ca-cert',
            'crx' => 'application/x-chrome-extension',
            'cryptonote' => 'application/vnd.rig.cryptonote',
            'csh' => 'application/x-csh',
            'csml' => 'chemical/x-csml',
            'csp' => 'application/vnd.commonspace',
            'css' => 'text/css',
            'cst' => 'application/x-director',
            'csv' => 'text/csv',
            'cu' => 'application/cu-seeme',
            'curl' => 'text/vnd.curl',
            'cww' => 'application/prs.cww',
            'cxt' => 'application/x-director',
            'cxx' => 'text/x-c',
            'dae' => 'model/vnd.collada+xml',
            'daf' => 'application/vnd.mobius.daf',
            'dart' => 'application/vnd.dart',
            'dataless' => 'application/vnd.fdsn.seed',
            'davmount' => 'application/davmount+xml',
            'dbk' => 'application/docbook+xml',
            'dcr' => 'application/x-director',
            'dcurl' => 'text/vnd.curl.dcurl',
            'dd2' => 'application/vnd.oma.dd2+xml',
            'ddd' => 'application/vnd.fujixerox.ddd',
            'deb' => 'application/x-debian-package',
            'def' => 'text/plain',
            'deploy' => 'application/octet-stream',
            'der' => 'application/x-x509-ca-cert',
            'dfac' => 'application/vnd.dreamfactory',
            'dgc' => 'application/x-dgc-compressed',
            'dic' => 'text/x-c',
            'dir' => 'application/x-director',
            'dis' => 'application/vnd.mobius.dis',
            'dist' => 'application/octet-stream',
            'distz' => 'application/octet-stream',
            'djv' => 'image/vnd.djvu',
            'djvu' => 'image/vnd.djvu',
            'dll' => 'application/x-msdownload',
            'dmg' => 'application/x-apple-diskimage',
            'dmp' => 'application/vnd.tcpdump.pcap',
            'dms' => 'application/octet-stream',
            'dna' => 'application/vnd.dna',
            'doc' => 'application/msword',
            'docm' => 'application/vnd.ms-word.document.macroenabled.12',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dot' => 'application/msword',
            'dotm' => 'application/vnd.ms-word.template.macroenabled.12',
            'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'dp' => 'application/vnd.osgi.dp',
            'dpg' => 'application/vnd.dpgraph',
            'dra' => 'audio/vnd.dra',
            'dsc' => 'text/prs.lines.tag',
            'dssc' => 'application/dssc+der',
            'dtb' => 'application/x-dtbook+xml',
            'dtd' => 'application/xml-dtd',
            'dts' => 'audio/vnd.dts',
            'dtshd' => 'audio/vnd.dts.hd',
            'dump' => 'application/octet-stream',
            'dvb' => 'video/vnd.dvb.file',
            'dvi' => 'application/x-dvi',
            'dwf' => 'model/vnd.dwf',
            'dwg' => 'image/vnd.dwg',
            'dxf' => 'image/vnd.dxf',
            'dxp' => 'application/vnd.spotfire.dxp',
            'dxr' => 'application/x-director',
            'ecelp4800' => 'audio/vnd.nuera.ecelp4800',
            'ecelp7470' => 'audio/vnd.nuera.ecelp7470',
            'ecelp9600' => 'audio/vnd.nuera.ecelp9600',
            'ecma' => 'application/ecmascript',
            'edm' => 'application/vnd.novadigm.edm',
            'edx' => 'application/vnd.novadigm.edx',
            'efif' => 'application/vnd.picsel',
            'ei6' => 'application/vnd.pg.osasli',
            'elc' => 'application/octet-stream',
            'emf' => 'application/x-msmetafile',
            'eml' => 'message/rfc822',
            'emma' => 'application/emma+xml',
            'emz' => 'application/x-msmetafile',
            'eol' => 'audio/vnd.digital-winds',
            'eot' => 'application/vnd.ms-fontobject',
            'eps' => 'application/postscript',
            'epub' => 'application/epub+zip',
            'es3' => 'application/vnd.eszigno3+xml',
            'esa' => 'application/vnd.osgi.subsystem',
            'esf' => 'application/vnd.epson.esf',
            'et3' => 'application/vnd.eszigno3+xml',
            'etx' => 'text/x-setext',
            'eva' => 'application/x-eva',
            'event-stream' => 'text/event-stream',
            'evy' => 'application/x-envoy',
            'exe' => 'application/x-msdownload',
            'exi' => 'application/exi',
            'ext' => 'application/vnd.novadigm.ext',
            'ez' => 'application/andrew-inset',
            'ez2' => 'application/vnd.ezpix-album',
            'ez3' => 'application/vnd.ezpix-package',
            'f' => 'text/x-fortran',
            'f4v' => 'video/x-f4v',
            'f77' => 'text/x-fortran',
            'f90' => 'text/x-fortran',
            'fbs' => 'image/vnd.fastbidsheet',
            'fcdt' => 'application/vnd.adobe.formscentral.fcdt',
            'fcs' => 'application/vnd.isac.fcs',
            'fdf' => 'application/vnd.fdf',
            'fe_launch' => 'application/vnd.denovo.fcselayout-link',
            'fg5' => 'application/vnd.fujitsu.oasysgp',
            'fgd' => 'application/x-director',
            'fh' => 'image/x-freehand',
            'fh4' => 'image/x-freehand',
            'fh5' => 'image/x-freehand',
            'fh7' => 'image/x-freehand',
            'fhc' => 'image/x-freehand',
            'fig' => 'application/x-xfig',
            'flac' => 'audio/flac',
            'fli' => 'video/x-fli',
            'flo' => 'application/vnd.micrografx.flo',
            'flv' => 'video/x-flv',
            'flw' => 'application/vnd.kde.kivio',
            'flx' => 'text/vnd.fmi.flexstor',
            'fly' => 'text/vnd.fly',
            'fm' => 'application/vnd.framemaker',
            'fnc' => 'application/vnd.frogans.fnc',
            'for' => 'text/x-fortran',
            'fpx' => 'image/vnd.fpx',
            'frame' => 'application/vnd.framemaker',
            'fsc' => 'application/vnd.fsc.weblaunch',
            'fst' => 'image/vnd.fst',
            'ftc' => 'application/vnd.fluxtime.clip',
            'fti' => 'application/vnd.anser-web-funds-transfer-initiation',
            'fvt' => 'video/vnd.fvt',
            'fxp' => 'application/vnd.adobe.fxp',
            'fxpl' => 'application/vnd.adobe.fxp',
            'fzs' => 'application/vnd.fuzzysheet',
            'g2w' => 'application/vnd.geoplan',
            'g3' => 'image/g3fax',
            'g3w' => 'application/vnd.geospace',
            'gac' => 'application/vnd.groove-account',
            'gam' => 'application/x-tads',
            'gbr' => 'application/rpki-ghostbusters',
            'gca' => 'application/x-gca-compressed',
            'gdl' => 'model/vnd.gdl',
            'geo' => 'application/vnd.dynageo',
            'gex' => 'application/vnd.geometry-explorer',
            'ggb' => 'application/vnd.geogebra.file',
            'ggt' => 'application/vnd.geogebra.tool',
            'ghf' => 'application/vnd.groove-help',
            'gif' => 'image/gif',
            'gim' => 'application/vnd.groove-identity-message',
            'gml' => 'application/gml+xml',
            'gmx' => 'application/vnd.gmx',
            'gnumeric' => 'application/x-gnumeric',
            'gph' => 'application/vnd.flographit',
            'gpx' => 'application/gpx+xml',
            'gqf' => 'application/vnd.grafeq',
            'gqs' => 'application/vnd.grafeq',
            'gram' => 'application/srgs',
            'gramps' => 'application/x-gramps-xml',
            'gre' => 'application/vnd.geometry-explorer',
            'grv' => 'application/vnd.groove-injector',
            'grxml' => 'application/srgs+xml',
            'gsf' => 'application/x-font-ghostscript',
            'gtar' => 'application/x-gtar',
            'gtm' => 'application/vnd.groove-tool-message',
            'gtw' => 'model/vnd.gtw',
            'gv' => 'text/vnd.graphviz',
            'gxf' => 'application/gxf',
            'gxt' => 'application/vnd.geonext',
            'h' => 'text/x-c',
            'h261' => 'video/h261',
            'h263' => 'video/h263',
            'h264' => 'video/h264',
            'hal' => 'application/vnd.hal+xml',
            'hbci' => 'application/vnd.hbci',
            'hdf' => 'application/x-hdf',
            'hh' => 'text/x-c',
            'hlp' => 'application/winhlp',
            'hpgl' => 'application/vnd.hp-hpgl',
            'hpid' => 'application/vnd.hp-hpid',
            'hps' => 'application/vnd.hp-hps',
            'hqx' => 'application/mac-binhex40',
            'htc' => 'text/x-component',
            'htke' => 'application/vnd.kenameaapp',
            'htm' => 'text/html',
            'html' => 'text/html',
            'hvd' => 'application/vnd.yamaha.hv-dic',
            'hvp' => 'application/vnd.yamaha.hv-voice',
            'hvs' => 'application/vnd.yamaha.hv-script',
            'i2g' => 'application/vnd.intergeo',
            'icc' => 'application/vnd.iccprofile',
            'ice' => 'x-conference/x-cooltalk',
            'icm' => 'application/vnd.iccprofile',
            'ico' => 'image/x-icon',
            'ics' => 'text/calendar',
            'ief' => 'image/ief',
            'ifb' => 'text/calendar',
            'ifm' => 'application/vnd.shana.informed.formdata',
            'iges' => 'model/iges',
            'igl' => 'application/vnd.igloader',
            'igm' => 'application/vnd.insors.igm',
            'igs' => 'model/iges',
            'igx' => 'application/vnd.micrografx.igx',
            'iif' => 'application/vnd.shana.informed.interchange',
            'imp' => 'application/vnd.accpac.simply.imp',
            'ims' => 'application/vnd.ms-ims',
            'in' => 'text/plain',
            'ink' => 'application/inkml+xml',
            'inkml' => 'application/inkml+xml',
            'install' => 'application/x-install-instructions',
            'iota' => 'application/vnd.astraea-software.iota',
            'ipfix' => 'application/ipfix',
            'ipk' => 'application/vnd.shana.informed.package',
            'irm' => 'application/vnd.ibm.rights-management',
            'irp' => 'application/vnd.irepository.package+xml',
            'iso' => 'application/x-iso9660-image',
            'itp' => 'application/vnd.shana.informed.formtemplate',
            'ivp' => 'application/vnd.immervision-ivp',
            'ivu' => 'application/vnd.immervision-ivu',
            'jad' => 'text/vnd.sun.j2me.app-descriptor',
            'jam' => 'application/vnd.jam',
            'jar' => 'application/java-archive',
            'java' => 'text/x-java-source',
            'jisp' => 'application/vnd.jisp',
            'jlt' => 'application/vnd.hp-jlyt',
            'jnlp' => 'application/x-java-jnlp-file',
            'joda' => 'application/vnd.joost.joda-archive',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpgm' => 'video/jpm',
            'jpgv' => 'video/jpeg',
            'jpm' => 'video/jpm',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'jsonml' => 'application/jsonml+json',
            'kar' => 'audio/midi',
            'karbon' => 'application/vnd.kde.karbon',
            'kfo' => 'application/vnd.kde.kformula',
            'kia' => 'application/vnd.kidspiration',
            'kml' => 'application/vnd.google-earth.kml+xml',
            'kmz' => 'application/vnd.google-earth.kmz',
            'kne' => 'application/vnd.kinar',
            'knp' => 'application/vnd.kinar',
            'kon' => 'application/vnd.kde.kontour',
            'kpr' => 'application/vnd.kde.kpresenter',
            'kpt' => 'application/vnd.kde.kpresenter',
            'kpxx' => 'application/vnd.ds-keypoint',
            'ksp' => 'application/vnd.kde.kspread',
            'ktr' => 'application/vnd.kahootz',
            'ktx' => 'image/ktx',
            'ktz' => 'application/vnd.kahootz',
            'kwd' => 'application/vnd.kde.kword',
            'kwt' => 'application/vnd.kde.kword',
            'lasxml' => 'application/vnd.las.las+xml',
            'latex' => 'application/x-latex',
            'lbd' => 'application/vnd.llamagraphics.life-balance.desktop',
            'lbe' => 'application/vnd.llamagraphics.life-balance.exchange+xml',
            'les' => 'application/vnd.hhe.lesson-player',
            'lha' => 'application/x-lzh-compressed',
            'link66' => 'application/vnd.route66.link66+xml',
            'list' => 'text/plain',
            'list3820' => 'application/vnd.ibm.modcap',
            'listafp' => 'application/vnd.ibm.modcap',
            'lnk' => 'application/x-ms-shortcut',
            'log' => 'text/plain',
            'lostxml' => 'application/lost+xml',
            'lrf' => 'application/octet-stream',
            'lrm' => 'application/vnd.ms-lrm',
            'ltf' => 'application/vnd.frogans.ltf',
            'lua' => 'text/x-lua',
            'luac' => 'application/x-lua-bytecode',
            'lvp' => 'audio/vnd.lucent.voice',
            'lwp' => 'application/vnd.lotus-wordpro',
            'lzh' => 'application/x-lzh-compressed',
            'm13' => 'application/x-msmediaview',
            'm14' => 'application/x-msmediaview',
            'm1v' => 'video/mpeg',
            'm21' => 'application/mp21',
            'm2a' => 'audio/mpeg',
            'm2v' => 'video/mpeg',
            'm3a' => 'audio/mpeg',
            'm3u' => 'audio/x-mpegurl',
            'm3u8' => 'application/x-mpegURL',
            'm4a' => 'audio/mp4',
            'm4p' => 'application/mp4',
            'm4u' => 'video/vnd.mpegurl',
            'm4v' => 'video/x-m4v',
            'ma' => 'application/mathematica',
            'mads' => 'application/mads+xml',
            'mag' => 'application/vnd.ecowin.chart',
            'maker' => 'application/vnd.framemaker',
            'man' => 'text/troff',
            'manifest' => 'text/cache-manifest',
            'mar' => 'application/octet-stream',
            'markdown' => 'text/x-markdown',
            'mathml' => 'application/mathml+xml',
            'mb' => 'application/mathematica',
            'mbk' => 'application/vnd.mobius.mbk',
            'mbox' => 'application/mbox',
            'mc1' => 'application/vnd.medcalcdata',
            'mcd' => 'application/vnd.mcd',
            'mcurl' => 'text/vnd.curl.mcurl',
            'md' => 'text/x-markdown',
            'mdb' => 'application/x-msaccess',
            'mdi' => 'image/vnd.ms-modi',
            'me' => 'text/troff',
            'mesh' => 'model/mesh',
            'meta4' => 'application/metalink4+xml',
            'metalink' => 'application/metalink+xml',
            'mets' => 'application/mets+xml',
            'mfm' => 'application/vnd.mfmp',
            'mft' => 'application/rpki-manifest',
            'mgp' => 'application/vnd.osgeo.mapguide.package',
            'mgz' => 'application/vnd.proteus.magazine',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mie' => 'application/x-mie',
            'mif' => 'application/vnd.mif',
            'mime' => 'message/rfc822',
            'mj2' => 'video/mj2',
            'mjp2' => 'video/mj2',
            'mk3d' => 'video/x-matroska',
            'mka' => 'audio/x-matroska',
            'mkd' => 'text/x-markdown',
            'mks' => 'video/x-matroska',
            'mkv' => 'video/x-matroska',
            'mlp' => 'application/vnd.dolby.mlp',
            'mmd' => 'application/vnd.chipnuts.karaoke-mmd',
            'mmf' => 'application/vnd.smaf',
            'mmr' => 'image/vnd.fujixerox.edmics-mmr',
            'mng' => 'video/x-mng',
            'mny' => 'application/x-msmoney',
            'mobi' => 'application/x-mobipocket-ebook',
            'mods' => 'application/mods+xml',
            'mov' => 'video/quicktime',
            'movie' => 'video/x-sgi-movie',
            'mp2' => 'audio/mpeg',
            'mp21' => 'application/mp21',
            'mp2a' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            'mp4a' => 'audio/mp4',
            'mp4s' => 'application/mp4',
            'mp4v' => 'video/mp4',
            'mpc' => 'application/vnd.mophun.certificate',
            'mpe' => 'video/mpeg',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpg4' => 'video/mp4',
            'mpga' => 'audio/mpeg',
            'mpkg' => 'application/vnd.apple.installer+xml',
            'mpm' => 'application/vnd.blueice.multipass',
            'mpn' => 'application/vnd.mophun.application',
            'mpp' => 'application/vnd.ms-project',
            'mpt' => 'application/vnd.ms-project',
            'mpy' => 'application/vnd.ibm.minipay',
            'mqy' => 'application/vnd.mobius.mqy',
            'mrc' => 'application/marc',
            'mrcx' => 'application/marcxml+xml',
            'ms' => 'text/troff',
            'mscml' => 'application/mediaservercontrol+xml',
            'mseed' => 'application/vnd.fdsn.mseed',
            'mseq' => 'application/vnd.mseq',
            'msf' => 'application/vnd.epson.msf',
            'msh' => 'model/mesh',
            'msi' => 'application/x-msdownload',
            'msl' => 'application/vnd.mobius.msl',
            'msty' => 'application/vnd.muvee.style',
            'mts' => 'model/vnd.mts',
            'mus' => 'application/vnd.musician',
            'musicxml' => 'application/vnd.recordare.musicxml+xml',
            'mvb' => 'application/x-msmediaview',
            'mwf' => 'application/vnd.mfer',
            'mxf' => 'application/mxf',
            'mxl' => 'application/vnd.recordare.musicxml',
            'mxml' => 'application/xv+xml',
            'mxs' => 'application/vnd.triscape.mxs',
            'mxu' => 'video/vnd.mpegurl',
            'n-gage' => 'application/vnd.nokia.n-gage.symbian.install',
            'n3' => 'text/n3',
            'nb' => 'application/mathematica',
            'nbp' => 'application/vnd.wolfram.player',
            'nc' => 'application/x-netcdf',
            'ncx' => 'application/x-dtbncx+xml',
            'nfo' => 'text/x-nfo',
            'ngdat' => 'application/vnd.nokia.n-gage.data',
            'nitf' => 'application/vnd.nitf',
            'nlu' => 'application/vnd.neurolanguage.nlu',
            'nml' => 'application/vnd.enliven',
            'nnd' => 'application/vnd.noblenet-directory',
            'nns' => 'application/vnd.noblenet-sealer',
            'nnw' => 'application/vnd.noblenet-web',
            'npx' => 'image/vnd.net-fpx',
            'nsc' => 'application/x-conference',
            'nsf' => 'application/vnd.lotus-notes',
            'ntf' => 'application/vnd.nitf',
            'nzb' => 'application/x-nzb',
            'oa2' => 'application/vnd.fujitsu.oasys2',
            'oa3' => 'application/vnd.fujitsu.oasys3',
            'oas' => 'application/vnd.fujitsu.oasys',
            'obd' => 'application/x-msbinder',
            'obj' => 'application/x-tgif',
            'oda' => 'application/oda',
            'odb' => 'application/vnd.oasis.opendocument.database',
            'odc' => 'application/vnd.oasis.opendocument.chart',
            'odf' => 'application/vnd.oasis.opendocument.formula',
            'odft' => 'application/vnd.oasis.opendocument.formula-template',
            'odg' => 'application/vnd.oasis.opendocument.graphics',
            'odi' => 'application/vnd.oasis.opendocument.image',
            'odm' => 'application/vnd.oasis.opendocument.text-master',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'oga' => 'audio/ogg',
            'ogg' => 'audio/ogg',
            'ogv' => 'video/ogg',
            'ogx' => 'application/ogg',
            'omdoc' => 'application/omdoc+xml',
            'onepkg' => 'application/onenote',
            'onetmp' => 'application/onenote',
            'onetoc' => 'application/onenote',
            'onetoc2' => 'application/onenote',
            'opf' => 'application/oebps-package+xml',
            'opml' => 'text/x-opml',
            'oprc' => 'application/vnd.palm',
            'org' => 'application/vnd.lotus-organizer',
            'osf' => 'application/vnd.yamaha.openscoreformat',
            'osfpvg' => 'application/vnd.yamaha.openscoreformat.osfpvg+xml',
            'otc' => 'application/vnd.oasis.opendocument.chart-template',
            'otf' => 'font/opentype',
            'otg' => 'application/vnd.oasis.opendocument.graphics-template',
            'oth' => 'application/vnd.oasis.opendocument.text-web',
            'oti' => 'application/vnd.oasis.opendocument.image-template',
            'otp' => 'application/vnd.oasis.opendocument.presentation-template',
            'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
            'ott' => 'application/vnd.oasis.opendocument.text-template',
            'oxps' => 'application/oxps',
            'oxt' => 'application/vnd.openofficeorg.extension',
            'p' => 'text/x-pascal',
            'p10' => 'application/pkcs10',
            'p12' => 'application/x-pkcs12',
            'p7b' => 'application/x-pkcs7-certificates',
            'p7c' => 'application/pkcs7-mime',
            'p7m' => 'application/pkcs7-mime',
            'p7r' => 'application/x-pkcs7-certreqresp',
            'p7s' => 'application/pkcs7-signature',
            'p8' => 'application/pkcs8',
            'pas' => 'text/x-pascal',
            'paw' => 'application/vnd.pawaafile',
            'pbd' => 'application/vnd.powerbuilder6',
            'pbm' => 'image/x-portable-bitmap',
            'pcap' => 'application/vnd.tcpdump.pcap',
            'pcf' => 'application/x-font-pcf',
            'pcl' => 'application/vnd.hp-pcl',
            'pclxl' => 'application/vnd.hp-pclxl',
            'pct' => 'image/x-pict',
            'pcurl' => 'application/vnd.curl.pcurl',
            'pcx' => 'image/x-pcx',
            'pdb' => 'application/vnd.palm',
            'pdf' => 'application/pdf',
            'pfa' => 'application/x-font-type1',
            'pfb' => 'application/x-font-type1',
            'pfm' => 'application/x-font-type1',
            'pfr' => 'application/font-tdpfr',
            'pfx' => 'application/x-pkcs12',
            'pgm' => 'image/x-portable-graymap',
            'pgn' => 'application/x-chess-pgn',
            'pgp' => 'application/pgp-encrypted',
            'php' => 'application/x-httpd-php',
            'pic' => 'image/x-pict',
            'pkg' => 'application/octet-stream',
            'pki' => 'application/pkixcmp',
            'pkipath' => 'application/pkix-pkipath',
            'plb' => 'application/vnd.3gpp.pic-bw-large',
            'plc' => 'application/vnd.mobius.plc',
            'plf' => 'application/vnd.pocketlearn',
            'pls' => 'application/pls+xml',
            'pml' => 'application/vnd.ctc-posml',
            'png' => 'image/png',
            'pnm' => 'image/x-portable-anymap',
            'portpkg' => 'application/vnd.macports.portpkg',
            'pot' => 'application/vnd.ms-powerpoint',
            'potm' => 'application/vnd.ms-powerpoint.template.macroenabled.12',
            'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
            'ppam' => 'application/vnd.ms-powerpoint.addin.macroenabled.12',
            'ppd' => 'application/vnd.cups-ppd',
            'ppm' => 'image/x-portable-pixmap',
            'pps' => 'application/vnd.ms-powerpoint',
            'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroenabled.12',
            'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptm' => 'application/vnd.ms-powerpoint.presentation.macroenabled.12',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'pqa' => 'application/vnd.palm',
            'prc' => 'application/x-mobipocket-ebook',
            'pre' => 'application/vnd.lotus-freelance',
            'prf' => 'application/pics-rules',
            'ps' => 'application/postscript',
            'psb' => 'application/vnd.3gpp.pic-bw-small',
            'psd' => 'image/vnd.adobe.photoshop',
            'psf' => 'application/x-font-linux-psf',
            'pskcxml' => 'application/pskc+xml',
            'ptid' => 'application/vnd.pvi.ptid1',
            'pub' => 'application/x-mspublisher',
            'pvb' => 'application/vnd.3gpp.pic-bw-var',
            'pwn' => 'application/vnd.3m.post-it-notes',
            'pya' => 'audio/vnd.ms-playready.media.pya',
            'pyv' => 'video/vnd.ms-playready.media.pyv',
            'qam' => 'application/vnd.epson.quickanime',
            'qbo' => 'application/vnd.intu.qbo',
            'qfx' => 'application/vnd.intu.qfx',
            'qps' => 'application/vnd.publishare-delta-tree',
            'qt' => 'video/quicktime',
            'qwd' => 'application/vnd.quark.quarkxpress',
            'qwt' => 'application/vnd.quark.quarkxpress',
            'qxb' => 'application/vnd.quark.quarkxpress',
            'qxd' => 'application/vnd.quark.quarkxpress',
            'qxl' => 'application/vnd.quark.quarkxpress',
            'qxt' => 'application/vnd.quark.quarkxpress',
            'ra' => 'audio/x-pn-realaudio',
            'ram' => 'audio/x-pn-realaudio',
            'rar' => 'application/x-rar-compressed',
            'ras' => 'image/x-cmu-raster',
            'rcprofile' => 'application/vnd.ipunplugged.rcprofile',
            'rdf' => 'application/rdf+xml',
            'rdz' => 'application/vnd.data-vision.rdz',
            'rep' => 'application/vnd.businessobjects',
            'res' => 'application/x-dtbresource+xml',
            'rgb' => 'image/x-rgb',
            'rif' => 'application/reginfo+xml',
            'rip' => 'audio/vnd.rip',
            'ris' => 'application/x-research-info-systems',
            'rl' => 'application/resource-lists+xml',
            'rlc' => 'image/vnd.fujixerox.edmics-rlc',
            'rld' => 'application/resource-lists-diff+xml',
            'rm' => 'application/vnd.rn-realmedia',
            'rmi' => 'audio/midi',
            'rmp' => 'audio/x-pn-realaudio-plugin',
            'rms' => 'application/vnd.jcp.javame.midlet-rms',
            'rmvb' => 'application/vnd.rn-realmedia-vbr',
            'rnc' => 'application/relax-ng-compact-syntax',
            'roa' => 'application/rpki-roa',
            'roff' => 'text/troff',
            'rp9' => 'application/vnd.cloanto.rp9',
            'rpss' => 'application/vnd.nokia.radio-presets',
            'rpst' => 'application/vnd.nokia.radio-preset',
            'rq' => 'application/sparql-query',
            'rs' => 'application/rls-services+xml',
            'rsd' => 'application/rsd+xml',
            'rss' => 'application/rss+xml',
            'rtf' => 'text/rtf',
            'rtx' => 'text/richtext',
            's' => 'text/x-asm',
            's3m' => 'audio/s3m',
            'saf' => 'application/vnd.yamaha.smaf-audio',
            'sbml' => 'application/sbml+xml',
            'sc' => 'application/vnd.ibm.secure-container',
            'scd' => 'application/x-msschedule',
            'scm' => 'application/vnd.lotus-screencam',
            'scq' => 'application/scvp-cv-request',
            'scs' => 'application/scvp-cv-response',
            'scurl' => 'text/vnd.curl.scurl',
            'sda' => 'application/vnd.stardivision.draw',
            'sdc' => 'application/vnd.stardivision.calc',
            'sdd' => 'application/vnd.stardivision.impress',
            'sdkd' => 'application/vnd.solent.sdkm+xml',
            'sdkm' => 'application/vnd.solent.sdkm+xml',
            'sdp' => 'application/sdp',
            'sdw' => 'application/vnd.stardivision.writer',
            'see' => 'application/vnd.seemail',
            'seed' => 'application/vnd.fdsn.seed',
            'sema' => 'application/vnd.sema',
            'semd' => 'application/vnd.semd',
            'semf' => 'application/vnd.semf',
            'ser' => 'application/java-serialized-object',
            'setpay' => 'application/set-payment-initiation',
            'setreg' => 'application/set-registration-initiation',
            'sfd-hdstx' => 'application/vnd.hydrostatix.sof-data',
            'sfs' => 'application/vnd.spotfire.sfs',
            'sfv' => 'text/x-sfv',
            'sgi' => 'image/sgi',
            'sgl' => 'application/vnd.stardivision.writer-global',
            'sgm' => 'text/sgml',
            'sgml' => 'text/sgml',
            'sh' => 'application/x-sh',
            'shar' => 'application/x-shar',
            'shf' => 'application/shf+xml',
            'sid' => 'image/x-mrsid-image',
            'sig' => 'application/pgp-signature',
            'sil' => 'audio/silk',
            'silo' => 'model/mesh',
            'sis' => 'application/vnd.symbian.install',
            'sisx' => 'application/vnd.symbian.install',
            'sit' => 'application/x-stuffit',
            'sitx' => 'application/x-stuffitx',
            'skd' => 'application/vnd.koan',
            'skm' => 'application/vnd.koan',
            'skp' => 'application/vnd.koan',
            'skt' => 'application/vnd.koan',
            'sldm' => 'application/vnd.ms-powerpoint.slide.macroenabled.12',
            'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'slt' => 'application/vnd.epson.salt',
            'sm' => 'application/vnd.stepmania.stepchart',
            'smf' => 'application/vnd.stardivision.math',
            'smi' => 'application/smil+xml',
            'smil' => 'application/smil+xml',
            'smv' => 'video/x-smv',
            'smzip' => 'application/vnd.stepmania.package',
            'snd' => 'audio/basic',
            'snf' => 'application/x-font-snf',
            'so' => 'application/octet-stream',
            'spc' => 'application/x-pkcs7-certificates',
            'spf' => 'application/vnd.yamaha.smaf-phrase',
            'spl' => 'application/x-futuresplash',
            'spot' => 'text/vnd.in3d.spot',
            'spp' => 'application/scvp-vp-response',
            'spq' => 'application/scvp-vp-request',
            'spx' => 'audio/ogg',
            'sql' => 'application/x-sql',
            'src' => 'application/x-wais-source',
            'srt' => 'application/x-subrip',
            'sru' => 'application/sru+xml',
            'srx' => 'application/sparql-results+xml',
            'ssdl' => 'application/ssdl+xml',
            'sse' => 'application/vnd.kodak-descriptor',
            'ssf' => 'application/vnd.epson.ssf',
            'ssml' => 'application/ssml+xml',
            'st' => 'application/vnd.sailingtracker.track',
            'stc' => 'application/vnd.sun.xml.calc.template',
            'std' => 'application/vnd.sun.xml.draw.template',
            'stf' => 'application/vnd.wt.stf',
            'sti' => 'application/vnd.sun.xml.impress.template',
            'stk' => 'application/hyperstudio',
            'stl' => 'application/vnd.ms-pki.stl',
            'str' => 'application/vnd.pg.format',
            'stw' => 'application/vnd.sun.xml.writer.template',
            'sub' => 'text/vnd.dvb.subtitle',
            'sus' => 'application/vnd.sus-calendar',
            'susp' => 'application/vnd.sus-calendar',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc' => 'application/x-sv4crc',
            'svc' => 'application/vnd.dvb.service',
            'svd' => 'application/vnd.svd',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'swa' => 'application/x-director',
            'swf' => 'application/x-shockwave-flash',
            'swi' => 'application/vnd.aristanetworks.swi',
            'sxc' => 'application/vnd.sun.xml.calc',
            'sxd' => 'application/vnd.sun.xml.draw',
            'sxg' => 'application/vnd.sun.xml.writer.global',
            'sxi' => 'application/vnd.sun.xml.impress',
            'sxm' => 'application/vnd.sun.xml.math',
            'sxw' => 'application/vnd.sun.xml.writer',
            't' => 'text/troff',
            't3' => 'application/x-t3vm-image',
            'taglet' => 'application/vnd.mynfc',
            'tao' => 'application/vnd.tao.intent-module-archive',
            'tar' => 'application/x-tar',
            'tcap' => 'application/vnd.3gpp2.tcap',
            'tcl' => 'application/x-tcl',
            'teacher' => 'application/vnd.smart.teacher',
            'tei' => 'application/tei+xml',
            'teicorpus' => 'application/tei+xml',
            'tex' => 'application/x-tex',
            'texi' => 'application/x-texinfo',
            'texinfo' => 'application/x-texinfo',
            'text' => 'text/plain',
            'tfi' => 'application/thraud+xml',
            'tfm' => 'application/x-tex-tfm',
            'tga' => 'image/x-tga',
            'thmx' => 'application/vnd.ms-officetheme',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'tmo' => 'application/vnd.tmobile-livetv',
            'torrent' => 'application/x-bittorrent',
            'tpl' => 'application/vnd.groove-tool-template',
            'tpt' => 'application/vnd.trid.tpt',
            'tr' => 'text/troff',
            'tra' => 'application/vnd.trueapp',
            'trm' => 'application/x-msterminal',
            'ts' => 'video/MP2T',
            'tsd' => 'application/timestamped-data',
            'tsv' => 'text/tab-separated-values',
            'ttc' => 'application/x-font-ttf',
            'ttf' => 'application/x-font-ttf',
            'ttl' => 'text/turtle',
            'twd' => 'application/vnd.simtech-mindmapper',
            'twds' => 'application/vnd.simtech-mindmapper',
            'txd' => 'application/vnd.genomatix.tuxedo',
            'txf' => 'application/vnd.mobius.txf',
            'txt' => 'text/plain',
            'u32' => 'application/x-authorware-bin',
            'udeb' => 'application/x-debian-package',
            'ufd' => 'application/vnd.ufdl',
            'ufdl' => 'application/vnd.ufdl',
            'ulx' => 'application/x-glulx',
            'umj' => 'application/vnd.umajin',
            'unityweb' => 'application/vnd.unity',
            'uoml' => 'application/vnd.uoml+xml',
            'url' => 'text/url',
            'uri' => 'text/uri-list',
            'uris' => 'text/uri-list',
            'urls' => 'text/uri-list',
            'ustar' => 'application/x-ustar',
            'utz' => 'application/vnd.uiq.theme',
            'uu' => 'text/x-uuencode',
            'uva' => 'audio/vnd.dece.audio',
            'uvd' => 'application/vnd.dece.data',
            'uvf' => 'application/vnd.dece.data',
            'uvg' => 'image/vnd.dece.graphic',
            'uvh' => 'video/vnd.dece.hd',
            'uvi' => 'image/vnd.dece.graphic',
            'uvm' => 'video/vnd.dece.mobile',
            'uvp' => 'video/vnd.dece.pd',
            'uvs' => 'video/vnd.dece.sd',
            'uvt' => 'application/vnd.dece.ttml+xml',
            'uvu' => 'video/vnd.uvvu.mp4',
            'uvv' => 'video/vnd.dece.video',
            'uvva' => 'audio/vnd.dece.audio',
            'uvvd' => 'application/vnd.dece.data',
            'uvvf' => 'application/vnd.dece.data',
            'uvvg' => 'image/vnd.dece.graphic',
            'uvvh' => 'video/vnd.dece.hd',
            'uvvi' => 'image/vnd.dece.graphic',
            'uvvm' => 'video/vnd.dece.mobile',
            'uvvp' => 'video/vnd.dece.pd',
            'uvvs' => 'video/vnd.dece.sd',
            'uvvt' => 'application/vnd.dece.ttml+xml',
            'uvvu' => 'video/vnd.uvvu.mp4',
            'uvvv' => 'video/vnd.dece.video',
            'uvvx' => 'application/vnd.dece.unspecified',
            'uvvz' => 'application/vnd.dece.zip',
            'uvx' => 'application/vnd.dece.unspecified',
            'uvz' => 'application/vnd.dece.zip',
            'vcard' => 'text/vcard',
            'vcd' => 'application/x-cdlink',
            'vcf' => 'text/x-vcard',
            'vcg' => 'application/vnd.groove-vcard',
            'vcs' => 'text/x-vcalendar',
            'vcx' => 'application/vnd.vcx',
            'vis' => 'application/vnd.visionary',
            'viv' => 'video/vnd.vivo',
            'vob' => 'video/x-ms-vob',
            'vor' => 'application/vnd.stardivision.writer',
            'vox' => 'application/x-authorware-bin',
            'vrml' => 'model/vrml',
            'vsd' => 'application/vnd.visio',
            'vsf' => 'application/vnd.vsf',
            'vss' => 'application/vnd.visio',
            'vst' => 'application/vnd.visio',
            'vsw' => 'application/vnd.visio',
            'vtt' => 'text/vtt',
            'vtu' => 'model/vnd.vtu',
            'vxml' => 'application/voicexml+xml',
            'w3d' => 'application/x-director',
            'wad' => 'application/x-doom',
            'wav' => 'audio/x-wav',
            'wax' => 'audio/x-ms-wax',
            'wbmp' => 'image/vnd.wap.wbmp',
            'wbs' => 'application/vnd.criticaltools.wbs+xml',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wcm' => 'application/vnd.ms-works',
            'wdb' => 'application/vnd.ms-works',
            'wdp' => 'image/vnd.ms-photo',
            'web' => 'text/url',
            'weba' => 'audio/webm',
            'webapp' => 'application/x-web-app-manifest+json',
            'webm' => 'video/webm',
            'webp' => 'image/webp',
            'wg' => 'application/vnd.pmi.widget',
            'wgt' => 'application/widget',
            'wks' => 'application/vnd.ms-works',
            'wm' => 'video/x-ms-wm',
            'wma' => 'audio/x-ms-wma',
            'wmd' => 'application/x-ms-wmd',
            'wmf' => 'application/x-msmetafile',
            'wml' => 'text/vnd.wap.wml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'wmls' => 'text/vnd.wap.wmlscript',
            'wmlsc' => 'application/vnd.wap.wmlscriptc',
            'wmv' => 'video/x-ms-wmv',
            'wmx' => 'video/x-ms-wmx',
            'wmz' => 'application/x-msmetafile',
            'woff' => 'application/x-font-woff',
            'wpd' => 'application/vnd.wordperfect',
            'wpl' => 'application/vnd.ms-wpl',
            'wps' => 'application/vnd.ms-works',
            'wqd' => 'application/vnd.wqd',
            'wri' => 'application/x-mswrite',
            'wrl' => 'model/vrml',
            'wsdl' => 'application/wsdl+xml',
            'wspolicy' => 'application/wspolicy+xml',
            'wtb' => 'application/vnd.webturbo',
            'wvx' => 'video/x-ms-wvx',
            'x32' => 'application/x-authorware-bin',
            'x3d' => 'model/x3d+xml',
            'x3db' => 'model/x3d+binary',
            'x3dbz' => 'model/x3d+binary',
            'x3dv' => 'model/x3d+vrml',
            'x3dvz' => 'model/x3d+vrml',
            'x3dz' => 'model/x3d+xml',
            'xaml' => 'application/xaml+xml',
            'xap' => 'application/x-silverlight-app',
            'xar' => 'application/vnd.xara',
            'xbap' => 'application/x-ms-xbap',
            'xbd' => 'application/vnd.fujixerox.docuworks.binder',
            'xbm' => 'image/x-xbitmap',
            'xdf' => 'application/xcap-diff+xml',
            'xdm' => 'application/vnd.syncml.dm+xml',
            'xdp' => 'application/vnd.adobe.xdp+xml',
            'xdssc' => 'application/dssc+xml',
            'xdw' => 'application/vnd.fujixerox.docuworks',
            'xenc' => 'application/xenc+xml',
            'xer' => 'application/patch-ops-error+xml',
            'xfdf' => 'application/vnd.adobe.xfdf',
            'xfdl' => 'application/vnd.xfdl',
            'xht' => 'application/xhtml+xml',
            'xhtml' => 'application/xhtml+xml',
            'xhvml' => 'application/xv+xml',
            'xif' => 'image/vnd.xiff',
            'xla' => 'application/vnd.ms-excel',
            'xlam' => 'application/vnd.ms-excel.addin.macroenabled.12',
            'xlc' => 'application/vnd.ms-excel',
            'xlf' => 'application/x-xliff+xml',
            'xlm' => 'application/vnd.ms-excel',
            'xls' => 'application/vnd.ms-excel',
            'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroenabled.12',
            'xlsm' => 'application/vnd.ms-excel.sheet.macroenabled.12',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xlt' => 'application/vnd.ms-excel',
            'xltm' => 'application/vnd.ms-excel.template.macroenabled.12',
            'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'xlw' => 'application/vnd.ms-excel',
            'xm' => 'audio/xm',
            'xml' => 'application/xml',
            'xo' => 'application/vnd.olpc-sugar',
            'xop' => 'application/xop+xml',
            'xpi' => 'application/x-xpinstall',
            'xpl' => 'application/xproc+xml',
            'xpm' => 'image/x-xpixmap',
            'xpr' => 'application/vnd.is-xpr',
            'xps' => 'application/vnd.ms-xpsdocument',
            'xpw' => 'application/vnd.intercon.formnet',
            'xpx' => 'application/vnd.intercon.formnet',
            'xsl' => 'application/xml',
            'xslt' => 'application/xslt+xml',
            'xsm' => 'application/vnd.syncml+xml',
            'xspf' => 'application/xspf+xml',
            'xul' => 'application/vnd.mozilla.xul+xml',
            'xvm' => 'application/xv+xml',
            'xvml' => 'application/xv+xml',
            'xwd' => 'image/x-xwindowdump',
            'xyz' => 'chemical/x-xyz',
            'xz' => 'application/x-xz',
            'yang' => 'application/yang',
            'yin' => 'application/yin+xml',
            'z1' => 'application/x-zmachine',
            'z2' => 'application/x-zmachine',
            'z3' => 'application/x-zmachine',
            'z4' => 'application/x-zmachine',
            'z5' => 'application/x-zmachine',
            'z6' => 'application/x-zmachine',
            'z7' => 'application/x-zmachine',
            'z8' => 'application/x-zmachine',
            'zaz' => 'application/vnd.zzazz.deck+xml',
            'zip' => 'application/zip',
            'zir' => 'application/vnd.zul',
            'zirz' => 'application/vnd.zul',
            'zmm' => 'application/vnd.handheld-entertainment+xml',
        ];

        // Add Google Mimetypes
        $mime_types_map['gdoc'] = 'application/vnd.google-apps.document';
        $mime_types_map['gslides'] = 'application/vnd.google-apps.presentation';
        $mime_types_map['gsheet'] = 'application/vnd.google-apps.spreadsheet';
        $mime_types_map['gdraw'] = 'application/vnd.google-apps.drawing';
        $mime_types_map['gtable'] = 'application/vnd.google-apps.fusiontable';
        $mime_types_map['gform'] = 'application/vnd.google-apps.form';

        if (isset($mime_types_map[$extension])) {
            return $mime_types_map[$extension];
        }

        return 'application/octet-stream';
    }

    /**
     * Purge all caches on the WordPress site
     * Mainly used when updating the plugin to prevent issue with
     * oudated javascript libraries or shortcode data.
     */
    public static function purge_cache_others()
    {
        try {
            // Purge all W3 Total Cache
            if (function_exists('w3tc_flush_all')) {
                \w3tc_flush_all();
            }

            // Purge WP Super Cache
            if (function_exists('wp_cache_clear_cache')) {
                \wp_cache_clear_cache();
            }

            // Purge WP Rocket
            if (function_exists('rocket_clean_domain')) {
                \rocket_clean_domain();
            }

            // Purge Cachify
            if (function_exists('cachify_flush_cache')) {
                \cachify_flush_cache();
            }

            // Purge Comet Cache
            if (class_exists('comet_cache')) {
                \comet_cache::clear();
            }

            // Purge Zen Cache
            if (class_exists('zencache')) {
                \zencache::clear();
            }

            // Purge LiteSpeed Cache
            if (class_exists('LiteSpeed_Cache_Tags')) {
                \LiteSpeed_Cache_Tags::add_purge_tag('*');
            }

            // Purge Hyper Cache
            if (class_exists('HyperCache')) {
                do_action('autoptimize_action_cachepurged');
            }

            // purge cache enabler
            if (has_action('ce_clear_cache')) {
                do_action('ce_clear_cache');
            }

            // purge wpfc
            if (function_exists('wpfc_clear_all_cache')) {
                \wpfc_clear_all_cache(true);
            }

            // add breeze cache purge support
            if (class_exists('Breeze_PurgeCache')) {
                \Breeze_PurgeCache::breeze_cache_flush();
            }

            // swift
            if (class_exists('Swift_Performance_Cache')) {
                \Swift_Performance_Cache::clear_all_cache();
            }

            // Hummingbird
            if (has_action('wphb_clear_page_cache')) {
                do_action('wphb_clear_page_cache');
            }

            // WP-Optimize
            if (has_action('wpo_cache_flush')) {
                do_action('wpo_cache_flush');
            }

            // hosting companies

            // Purge SG Optimizer (Siteground)
            if (function_exists('sg_cachepress_purge_cache')) {
                \sg_cachepress_purge_cache();
            }

            // Purge Godaddy Managed WordPress Hosting (Varnish + APC)
            if (class_exists('WPaaS\Plugin') && method_exists('WPass\Plugin', 'vip')) {
                \fvm_godaddy_request('BAN');
            }

            // Purge WP Engine
            if (class_exists('WpeCommon')) {
                if (method_exists('WpeCommon', 'purge_memcached')) {
                    \WpeCommon::purge_memcached();
                }
                if (method_exists('WpeCommon', 'purge_varnish_cache')) {
                    \WpeCommon::purge_varnish_cache();
                }
            }

            // Purge Kinsta
            global $kinsta_cache;
            if (isset($kinsta_cache) && class_exists('\\Kinsta\\CDN_Enabler')) {
                if (!empty($kinsta_cache->kinsta_cache_purge)) {
                    $kinsta_cache->kinsta_cache_purge->purge_complete_caches();
                }
            }

            // Purge Pagely
            if (class_exists('PagelyCachePurge')) {
                $purge_pagely = new \PagelyCachePurge();
                $purge_pagely->purgeAll();
            }

            // Purge Pantheon Advanced Page Cache plugin
            if (function_exists('pantheon_wp_clear_edge_all')) {
                \pantheon_wp_clear_edge_all();
            }

            // wordpress default cache
            if (function_exists('wp_cache_flush')) {
                \wp_cache_flush();
            }
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.sprintf('Cannot clear cache on line %s: %s', __LINE__, $ex->getMessage()));
        }
    }
}
