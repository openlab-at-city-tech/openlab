<?php

use RebelCode\Spotlight\Instagram\Plugin;

if (!defined('SL_INSTA_FREE_PLUGIN_BASENAME')) {
    define('SL_INSTA_FREE_PLUGIN_BASENAME', 'spotlight-social-photo-feeds/plugin.php');
}

if (!function_exists('spotlightInsta')) {
    /**
     * Retrieves the plugin instance.
     *
     * @since 0.2
     *
     * @return Plugin
     */
    function spotlightInsta()
    {
        static $instance = null;

        return ($instance === null)
            ? $instance = new Plugin(SL_INSTA_DIR . '/plugin.php')
            : $instance;
    }
}

if (!class_exists('SlInstaRuntime')) {
    /**
     * A simple struct that stores runtime information about active copies of the plugin.
     *
     * @since 0.4
     */
    class SlInstaRuntime
    {
        /**
         * The info for the copy of the plugin that is truly active and running.
         *
         * @var SlInstaPluginInfo
         */
        public $info = null;

        /**
         * Whether or not a free version of the plugin is active.
         *
         * @var bool
         */
        public $isFreeActive = false;

        /**
         * Whether or not a PRO version of the plugin is active.
         *
         * @var bool
         */
        public $isProActive = false;

        /**
         * The version of the free plugin that is active, or null if a free version is not active.
         *
         * @var string|null
         */
        public $freeVersion = null;

        /**
         * The version of the PRO plugin that is active, or null if a PRO version is not active.
         *
         * @var string|null
         */
        public $proVersion = null;
    }
}

if (!class_exists('SlInstaPluginInfo')) {
    /**
     * A simple struct that stores information about a Spotlight plugin.
     *
     * @since 0.4
     */
    class SlInstaPluginInfo
    {
        /**
         * The basename of the plugin - the string by which WordPress identifies plugins.
         *
         * @var string
         */
        public $basename;

        /**
         * The path to the plugin's main file.
         *
         * @var string
         */
        public $file;

        /**
         * The path to the plugin's directory.
         *
         * @var string
         */
        public $dir;

        /**
         * The version of the plugin.
         *
         * @var string
         */
        public $version;

        /**
         * Whether the plugin is a PRO version or not.
         *
         * @var bool
         */
        public $isPro;
    }
}

if (!function_exists('slInstaRunPlugin')) {
    /**
     * Runs the plugin. Maybe. Maybe not.
     *
     * This function checks to see if the plugin is allowed to run. This is determined by whether or not the plugin
     * coexists alongside other copies of the plugin on the same site. If so, only the latest PRO version is allowed
     * to run. If the plugin is not that version, the callback is not invoked.
     *
     * @since 0.4
     *
     * @param string   $mainFile The path to the plugin's main file.
     * @param callable $callback The function that runs the plugin, which will be called if all checks pass.
     *                           An argument of type {@link SlInstaRuntime} is passed to this function to avoid using
     *                           even more globals!
     *
     * @return bool True if the plugin callback was run, false if not.
     */
    function slInstaRunPlugin($mainFile, $callback)
    {
        global $slInsta;

        if (!isset($slInsta)) {
            slInstaLoadWpPluginFunctions();

            $slInsta = new SlInstaRuntime();

            // Get the Spotlight plugin instances that are active
            // We need to tell the function to include the plugin that triggered this script
            // since it may not yet be marked as "active" in the DB option if it was activated in this request
            $thisBaseName = plugin_basename($mainFile);
            $slInstaPlugins = slInstaGetPluginInstances([$thisBaseName]);

            foreach ($slInstaPlugins as $info) {
                $slInsta->isFreeActive = $slInsta->isFreeActive || !$info->isPro;
                $slInsta->isProActive = $slInsta->isProActive || $info->isPro;

                if (!$info->isPro && version_compare($info->version ?? '0.0', $slInsta->freeVersion ?? '0.0', '>')) {
                    $slInsta->freeVersion = $info->version;
                }

                if ($info->isPro && version_compare($info->version ?? '0.0', $slInsta->proVersion ?? '0.0', '>')) {
                    $slInsta->proVersion = $info->version;
                }
            }

            // Sort the instances by whether or not they are PRO first, and by their versions second
            usort($slInstaPlugins, function ($a, $b) {
                $aScore = intval($a->isPro);
                $bScore = intval($b->isPro);

                return $bScore - $aScore;
            });

            // Set the first instance in the sorted list to run
            $slInsta->info = $slInstaPlugins[0];

            // Allows PRO to act as its own free version. This should only be used by developers that have access to the
            // full version of the plugin, before any build processing occurs
            if (defined('SL_INSTA_DEV_ENV') && SL_INSTA_DEV_ENV) {
                $slInsta->freeVersion = $slInsta->proVersion;
                $slInsta->isFreeActive = $slInsta->isProActive;
            }
        }

        // If this plugin is not the elected one, stop here
        if (plugin_basename($mainFile) !== $slInsta->info->basename) {
            return false;
        }

        // Invoke the callback
        $callback($slInsta);

        return true;
    }
}

if (!function_exists('slInstaGetPluginInstances')) {
    function slInstaGetPluginInstances(array $include = [])
    {
        slInstaLoadWpPluginFunctions();

        $activePlugins = array_merge(get_option('active_plugins'), $include);
        $instances = [];

        foreach ($activePlugins as $basename) {
            $info = slInstaPluginInfo($basename);

            if ($info !== null) {
                $instances[$basename] = $info;
            }
        }

        return $instances;
    }
}

if (!function_exists('slInstaPluginInfo')) {
    /**
     * Retrieves information about a plugin, if it's a copy of Spotlight of any tier or version.
     *
     * @since 0.4
     *
     * @param string $basename The WordPress basename of the plugin.
     *
     * @return SlInstaPluginInfo|null The obtained information about the plugin, or null if the plugin is not a copy
     *                                of Spotlight.
     */
    function slInstaPluginInfo($basename)
    {
        $file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $basename;
        $dir = dirname($file);

        $info = null;
        $result = new SlInstaPluginInfo();
        $result->basename = $basename;
        $result->file = $file;
        $result->dir = $dir;
        $result->version = null;
        $result->isPro = false;

        // Post-v0.4 we get the info from the spotlight.json file and check if its PRO by looking for the pro.php file
        $jsonFile = $dir . '/plugin.json';
        if (file_exists($jsonFile) && is_readable($jsonFile)) {
            $info = @json_decode(file_get_contents($jsonFile));

            if ($info !== null) {
                $result->version = isset($info->version) ? $info->version : null;
                $result->isPro = file_exists($dir . '/includes/pro.php');

                return $result;
            }
        }

        // Pre-v0.4 we have to parse the plugin header data to get the version and look for the PRO modules directory
        if ($info === null && stripos($basename, 'spotlight-social') !== false) {
            slInstaLoadWpPluginFunctions();

            $pluginData = get_plugin_data($file);

            $result->version = $pluginData['Version'];
            $result->isPro = is_dir($dir . '/modules/Pro');

            return $result;
        }

        return null;
    }
}

if (!function_exists('slInstaDepsSatisfied')) {
    /**
     * Checks whether the plugin dependencies are satisfied.
     *
     * @since 0.4
     *
     * @return bool True if dependencies are satisfied, false if not.
     */
    function slInstaDepsSatisfied()
    {
        // Check PHP version
        if (version_compare(PHP_VERSION, SL_INSTA_MIN_PHP_VERSION, '<')) {
            add_action('admin_notices', function () {
                printf(
                    '<div class="notice notice-error"><p>%s</p></div>',
                    sprintf(
                        _x(
                            '%1$s requires PHP version %2$s or later',
                            '%1$s is the name of the plugin, %2$s is the required PHP version',
                            'sli'
                        ),
                        '<strong>' . SL_INSTA_PLUGIN_NAME . '</strong>',
                        SL_INSTA_MIN_PHP_VERSION
                    )
                );
            });

            return false;
        }

        // Check WordPress version
        global $wp_version;
        if (version_compare($wp_version, SL_INSTA_MIN_WP_VERSION, '<')) {
            add_action('admin_notices', function () {
                printf(
                    '<div class="notice notice-error"><p>%s</p></div>',
                    sprintf(
                        _x(
                            '%1$s requires WordPress version %2$s or later',
                            '%1$s is the name of the plugin, %2$s is the required WP version',
                            'sli'
                        ),
                        '<strong>' . SL_INSTA_PLUGIN_NAME . '</strong>',
                        SL_INSTA_MIN_WP_VERSION
                    )
                );
            });

            return false;
        }

        // Check for extensions
        foreach (['json', 'curl', 'gd|imagick'] as $extStr) {
            $exts = explode('|', $extStr);

            $ok = false;
            foreach ($exts as $ext) {
                if (extension_loaded($ext)) {
                    $ok = true;
                    break;
                }
            }

            if ($ok) {
                continue;
            }

            add_action('admin_notices', function () use ($exts) {
                $names = array_map(function ($ext) {
                    return "<code>$ext</code>";
                }, $exts);

                $names = implode(__(' or ', 'sli'), $names);

                printf(
                    '<div class="notice notice-error"><p>%s</p></div>',
                    sprintf(
                        _x(
                            '%1$s requires the %2$s PHP extension. Kindly install and enable this extension or contact your hosting provider for assistance.',
                            '%1$s is the name of the plugin, %2$s is the name of the extension',
                            'sli'
                        ),
                        '<strong>' . SL_INSTA_PLUGIN_NAME . '</strong>',
                        $names
                    )
                );
            });

            return false;
        }

        return true;
    }
}

if (!function_exists('slInstaRequireFreeNotice')) {
    /**
     * Shows the notice that informs the user that the free version is required.
     *
     * Important: Only hook in this function if the free version of the plugin is not active.
     *
     * @since 0.4
     */
    function slInstaRequireFreeNotice()
    {
        $url = admin_url('admin-ajax.php');
        $nonce = wp_create_nonce('updates');

        ?>
        <div class="notice notice-error">
            <p>
                <b><?= SL_INSTA_NAME ?></b>:
                <?php
                if (slInstaIsFreeInstalled()) {
                    echo strtr(
                        _x(
                            'The free version of the plugin needs to remain activated in order to use the PRO version. {{Click here}} to activate it.',
                            'Put {{ and }} around the text that should activate the plugin',
                            'sl-insta'
                        ),
                        [
                            '{{' => '<a href="' . slInstaGetActivateUrl(SL_INSTA_FREE_PLUGIN_BASENAME) . '">',
                            '}}' => '</a>',
                        ]
                    );
                } else {
                    echo strtr(
                        _x(
                            'You need to install and activate the free version in order to use the PRO version. {{Click here}} to install it.',
                            'Put {{ and }} around the text that should link to the free version download page',
                            'sl-insta'
                        ),
                        [
                            '{{' => '<a href="' . $url . '" id="spotlight-install-free" data-nonce="' . $nonce . '">',
                            '}}' => '</a>',
                        ]
                    );
                }
                ?>
            </p>
            <p id="spotlight-during-install-free" style="display: none;">
                <?= __('Installing ...', 'sl-insta') ?>
            </p>
            <p id="spotlight-error-install-free" style="display: none;">
                <?=
                strtr(
                    _x(
                        'Something went wrong! We couldn\'t install the plugin for you. Kindly download and install the free version from {{this page}}.',
                        'Put {{ and }} around the text that links to the plugin install page',
                        'sl-insta'
                    ),
                    [
                        '{{' => '<a href="' . admin_url('plugin-install.php?s=spotlight&tab=search&type=term') . '">',
                        '}}' => '</a>',
                    ]
                )
                ?>
            </p>
            <p id="spotlight-done-install-free" style="display: none;">
                <?=
                strtr(
                    _x(
                        'Done! Click {{here}} to activate it!',
                        'Put {{ and }} around the text that activates the plugin',
                        'sl-insta'
                    ),
                    [
                        '{{' => '<a id="spotlight-activate-free">',
                        '}}' => '</a>',
                    ]
                )
                ?>
            </p>
        </div>

        <script type="text/javascript">
            jQuery(function ($) {
                function onSuccess(response) {
                    if (!response.success) {
                        return onError();
                    }

                    $('#spotlight-during-install-free').hide();
                    $('#spotlight-error-install-free').hide();
                    $('#spotlight-activate-free').attr('href', response.data.activateUrl);
                    $('#spotlight-done-install-free').show();
                }

                function onError() {
                    $('#spotlight-during-install-free').hide();
                    $('#spotlight-done-install-free').hide();
                    $('#spotlight-error-install-free').show();
                }

                $('#spotlight-install-free').on('click', function (e) {
                    e.preventDefault();

                    const nonce = $(this).data('nonce');

                    $('#spotlight-during-install-free').show();

                    $.ajax({
                        url: $(this).attr('href'),
                        method: "POST",
                        data: {
                            _ajax_nonce: nonce,
                            action: "install-plugin",
                            slug: "spotlight-social-photo-feeds"
                        },
                        dataType: "json",
                        success: onSuccess,
                        error: onError,
                    });
                });
            });

        </script>
        <?php
    }
}

if (!function_exists('slInstaFreeVersionNotice')) {
    /**
     * Shows the notice that informs the user that their FREE version is inadequate.
     *
     * @since 0.4.1
     */
    function slInstaFreeVersionNotice()
    {
        ?>
        <div class="notice notice-error">
            <p>
                <b><?= SL_INSTA_NAME ?></b>:
                <?= __('Your version of the FREE plugin needs to be updated to at least version 0.4.', 'sl-insta') ?>
            </p>
        </div>
        <?php
    }
}

if (!function_exists('slInstaIsFreeInstalled')) {
    /**
     * Checks if the free version of the plugin is installed.
     *
     * @since 0.4
     *
     * @return bool True if the free version of the plugin is installed and inactive, false if it's installed or active.
     */
    function slInstaIsFreeInstalled()
    {
        $plugins = get_plugins();
        $active = array_flip(get_option('active_plugins', []));

        foreach ($plugins as $basename => $plugin) {
            // Check if plugin is in list of active plugins
            if (!array_key_exists($basename, $active)) {
                // If not, get its info
                $info = slInstaPluginInfo($basename);
                // If it's a Spotlight copy and not PRO, return true
                if ($info !== null && !$info->isPro) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('slInstaGetActivateUrl')) {
    /**
     * Creates a URL that can be used to activate a plugin.
     *
     * @since 0.4
     *
     * @param string $plugin The basename of the plugin toe be activated by the URL.
     *
     * @return string The created URL.
     */
    function slInstaGetActivateUrl($plugin)
    {
        return admin_url('plugins.php') . '?' . build_query([
                'action' => 'activate',
                'plugin' => $plugin,
                '_wpnonce' => wp_create_nonce('activate-plugin_spotlight-social-photo-feeds/plugin.php'),
            ]);
    }
}

if (!function_exists('slInstaCheckForConflicts')) {
    function slInstaCheckForConflicts($ignore = null)
    {
        slInstaLoadWpPluginFunctions();

        $conflicts = [];
        foreach (slInstaGetConflictingPlugins() as $plugin => $info) {
            if (is_plugin_active($plugin) && $plugin !== $ignore) {
                // If conflict has a version constraint ...
                if (array_key_exists('version', $info)) {
                    $data = get_plugin_data(WP_CONTENT_DIR . '/plugins/' . $plugin);
                    $version1 = $data['Version'];
                    [$compare, $version2] = $info['version'];

                    // And version constraint is not satisfied, skip conflict
                    if (!version_compare($version1, $version2, $compare)) {
                        continue;
                    }
                }

                $conflicts[] = $plugin;
            }
        }

        if (empty($conflicts)) {
            delete_option('sli_plugin_conflicts');
        } else {
            update_option('sli_plugin_conflicts', $conflicts);
        }
    }
}

if (!function_exists('slInstaShowConflictsNotice')) {
    function slInstaShowConflictsNotice()
    {
        if (filter_input(INPUT_GET, 'sli_ignore_conflicts') === '1') {
            delete_option('sli_plugin_conflicts');
            delete_option('sli_guzzle_conflicts');

            return false;
        }

        $pluginConflicts = get_option('sli_plugin_conflicts', []);
        $guzzleConflicts = get_option('sli_guzzle_conflicts', []);

        $conflicts = array_merge($pluginConflicts, $guzzleConflicts);

        if (!is_array($conflicts) || empty($conflicts)) {
            return false;
        }

        add_action('admin_notices', function () use ($conflicts) {
            $conflictConfig = slInstaGetConflictingPlugins();
            ?>
            <div class="notice notice-error">
                <p>
                    <?php
                    printf(
                        _x(
                            '%s has detected an incompatibility with some of your plugins.',
                            '%s is the name of the plugin',
                            'sl-insta'
                        ),
                        '<b>' . SL_INSTA_NAME . '</b>'
                    )
                    ?>
                </p>
                <ol>
                    <?php foreach ($conflicts as $plugin) : ?>
                        <?php
                        $info = $conflictConfig[$plugin] ?? null;

                        // Default to Guzzle conflict
                        if ($info === null) {
                            $data = get_plugin_data(WP_CONTENT_DIR . '/plugins/' . $plugin);
                            $info = [
                                'name' => $data['Name'] ?? $plugin,
                                'reason' => __(
                                    'Both plugins use the Guzzle HTTP library. This can cause fatal errors if the plugins use different versions of the library.',
                                    'sl-insta'
                                ),
                            ];
                        }
                        ?>
                        <li><b><?= $info['name'] ?></b> &mdash; <i><?= $info['reason'] ?></i></li>
                    <?php endforeach; ?>
                </ol>
                <p>
                    <?=
                    __(
                        'Spotlight has suspended itself to prevent your site from crashing until the conflict is resolved.',
                        'sl-insta'
                    )
                    ?>
                </p>
                <p>
                    <?=
                    __(
                        'To resolve the conflict, disable either Spotlight or the conflicting plugin. Alternatively, you can choose to ignore the conflict(s), but only do so if you know what you\'re doing.',
                        'sl-insta'
                    )
                    ?>
                </p>
                <p>
                    <a class="button button-primary" href="https://spotlightwp.com/support" target="_blank">
                        <?= __('Contact Spotlight support', 'sl-insta') ?>
                    </a>
                    <a
                        class="button button-secondary"
                        href="<?= add_query_arg(['sli_ignore_conflicts' => '1']) ?>"
                        style="margin-right: 5px">
                        <?= __('Ignore conflicts', 'sl-insta') ?>
                    </a>
                </p>
            </div>
            <?php
        });

        return true;
    }
}

if (!function_exists('slInstaGetConflictingPlugins')) {
    function slInstaGetConflictingPlugins()
    {
        return [];
    }
}

if (!function_exists('slInstaLoadWpPluginFunctions')) {
    function slInstaLoadWpPluginFunctions()
    {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
    }
}

if (!function_exists('slInstaCheckDeactivate')) {
    function slInstaCheckDeactivate()
    {
        // Check for error deactivation
        $errorDeactivate = filter_input(INPUT_GET, 'sli_error_deactivate');

        if (!empty($errorDeactivate)) {
            add_action('init', function () use ($errorDeactivate) {
                if (wp_verify_nonce($errorDeactivate, 'sli_error_deactivate') !== false) {
                    slInstaLoadWpPluginFunctions();

                    $instances = slInstaGetPluginInstances();
                    $plugins = array_map(function ($info) {
                        return $info->basename;
                    }, $instances);

                    deactivate_plugins($plugins);
                }
            });

            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('slInstaGetPluginMainFile')) {
    function slInstaGetPluginMainFile($pluginDirName)
    {
        $phpFiles = glob(WP_CONTENT_DIR . '/plugins/' . $pluginDirName . '/*.php');

        foreach ($phpFiles as $filePath) {
            $fileName = basename($filePath);

            if (!is_readable($filePath)) {
                continue;
            }

            // Do not apply markup/translate as it will be cached.
            $data = get_plugin_data($filePath, false, false);

            if (empty($data['Name'])) {
                continue;
            }

            return $fileName;
        }

        return null;
    }
}
