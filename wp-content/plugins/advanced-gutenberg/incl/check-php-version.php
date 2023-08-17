<?php
/**
 * @return bool
 *
 * @author      PublishPress
 * @copyright   Copyright (c) 2023, PublishPress
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @package     PublishPress Blocks
 */

namespace PublishPressBlocksPhpCheck {

    use function __;
    use function add_action;
    use function esc_html;
    use function load_plugin_textdomain;

    use const PHP_VERSION;

    $data = [
        'plugin_name' => 'PublishPress Blocks',
        'plugin_slug' => 'advanced-gutenberg',
        'plugin_file' => 'advanced-gutenberg/advanced-gutenberg.php',
        'min_php_version' => '7.2.5',
        'message_format' => __(
            '%s requires PHP %s or later. Please upgrade PHP to a compatible version. Your current version is %s.',
            'advanced-gutenberg'
        ),
    ];

    $isValidVersion = version_compare(PHP_VERSION, $data['min_php_version'], '>=');

    if (! $isValidVersion) {
        load_plugin_textdomain($data['plugin_slug'], false, __DIR__ . '/../languages/');

        add_action('after_plugin_row_' . $data['plugin_file'], function ($pluginFile) use ($data) {
            ?>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3" class="colspanchange">
                    <div class="notice inline notice-warning notice-alt">
                        <p>
                            <span class="dashicons dashicons-warning" style="margin-right: 6px; color: #d63638;"></span>
                            <?php
                            echo esc_html(
                                sprintf(
                                    $data['message_format'],
                                    $data['plugin_name'],
                                    $data['min_php_version'],
                                    PHP_VERSION
                                )
                            );
                            ?>
                        </p>
                    </div>
                </td>
            </tr>
            <?php
        });
    }

    return $isValidVersion;
}
