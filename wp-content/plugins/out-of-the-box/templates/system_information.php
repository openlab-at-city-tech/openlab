<?php
defined('ABSPATH') || exit;

function wpcp_help_tip($tip)
{
    $tip = htmlspecialchars(
        wp_kses(
            html_entity_decode($tip),
            [
                'br' => [],
                'em' => [],
                'strong' => [],
                'small' => [],
                'span' => [],
                'ul' => [],
                'li' => [],
                'ol' => [],
                'p' => [],
            ]
        )
    );

    return '<span class="outofthebox-help-tip" title="'.$tip.'"></span>';
}
?>
<table class="wpcp_systeminfo_table widefat" cellspacing="0" >
  <thead>
    <tr>
      <th colspan="3"><h2><?php esc_html_e('WordPress environment', 'outofthebox'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php esc_html_e('WordPress address (URL)', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The root URL of your site.', 'outofthebox')); ?></td>
      <td><?php echo esc_html($environment['site_url']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Site address (URL)', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The homepage URL of your site.', 'outofthebox')); ?></td>
      <td><?php echo esc_html($environment['home_url']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Out-of-the-Box version', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The version of Out-of-the-Box installed on your site.', 'outofthebox')); ?></td>
      <td><?php echo esc_html($environment['version']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress version', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The version of WordPress installed on your site.', 'outofthebox')); ?></td>
      <td>
        <?php
        $latest_version = get_transient('wpcp_system_status_wp_version_check');

        if (false === $latest_version) {
            $version_check = wp_remote_get('https://api.wordpress.org/core/version-check/1.7/');
            $api_response = json_decode(wp_remote_retrieve_body($version_check), true);

            if ($api_response && isset($api_response['offers'], $api_response['offers'][0], $api_response['offers'][0]['version'])) {
                $latest_version = $api_response['offers'][0]['version'];
            } else {
                $latest_version = $environment['wp_version'];
            }
            set_transient('wpcp_system_status_wp_version_check', $latest_version, DAY_IN_SECONDS);
        }

        if (version_compare($environment['wp_version'], $latest_version, '<')) {
            // Translators: %1$s: Current version, %2$s: New version
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('%1$s - There is a newer version of WordPress available (%2$s)', 'outofthebox'), esc_html($environment['wp_version']), esc_html($latest_version)).'</mark>';
        } else {
            echo '<mark class="yes">'.esc_html($environment['wp_version']).'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress multisite', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Whether or not you have WordPress Multisite enabled.', 'outofthebox')); ?></td>
      <td><?php echo ($environment['wp_multisite']) ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress memory limit', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The maximum amount of memory (RAM) that your site can use at one time.', 'outofthebox')); ?></td>
      <td>
        <?php
        if ($environment['wp_memory_limit'] < 268435456) {
            // Translators: %1$s: Memory limit, %2$s: Docs link.
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('%1$s - We recommend setting memory to at least 256MB. See: %2$s', 'outofthebox'), esc_html(size_format($environment['wp_memory_limit'])), '<a href="https://wordpress.org/support/article/editing-wp-config-php/#increasing-memory-allocated-to-php" target="_blank">'.esc_html__('Increasing memory allocated to PHP', 'outofthebox').'</a>').'</mark>';
        } else {
            echo '<mark class="yes">'.esc_html(size_format($environment['wp_memory_limit'])).'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress debug mode', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Displays whether or not WordPress is in Debug Mode.', 'outofthebox')); ?></td>
      <td>
        <?php if ($environment['wp_debug_mode']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="no">&ndash;</mark>
        <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress cron', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Displays whether or not WP Cron Jobs are enabled.', 'outofthebox')); ?></td>
      <td>
        <?php if ($environment['wp_cron']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="no">&ndash;</mark>
        <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('Language', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The current language used by WordPress. Default = English', 'outofthebox')); ?></td>
      <td><?php echo esc_html($environment['language']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('External object cache', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Displays whether or not WordPress is using an external object cache.', 'outofthebox')); ?></td>
      <td>
        <?php if ($environment['external_object_cache']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="no">&ndash;</mark>
        <?php } ?>
      </td>
    </tr>
  </tbody>
</table>



<table class="wpcp_systeminfo_table widefat" cellspacing="0">
  <thead>
    <tr>
      <th colspan="3"><h2><?php esc_html_e('Server environment', 'outofthebox'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php esc_html_e('Server info', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Information about the web server that is currently hosting your site.', 'outofthebox')); ?></td>
      <td><?php echo esc_html($environment['server_info']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('PHP version', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The version of PHP installed on your hosting server.', 'outofthebox')); ?></td>
      <td>
        <?php
        if (version_compare($environment['php_version'], '7.0', '>=')) {
            echo '<mark class="yes">'.esc_html($environment['php_version']).'</mark>';
        } else {
            $update_link = ' <a href="https://wordpress.org/support/update-php/" target="_blank">'.esc_html__('How to update your PHP version', 'outofthebox').'</a>';
            $class = 'error';

            if (version_compare($environment['php_version'], '5.6', '<')) {
                $notice = '<span class="dashicons dashicons-warning"></span> '.__('Out-of-the-Box will run under this version of PHP, however, it has reached end of life. We recommend using PHP version 7.2 or above for greater performance and security.', 'outofthebox').$update_link;
            } elseif (version_compare($environment['php_version'], '7.2', '<')) {
                $notice = '<span class="dashicons dashicons-warning"></span> '.__('We recommend using PHP version 7.2 or above for greater performance and security.', 'outofthebox').$update_link;
                $class = 'recommendation';
            }

            echo '<mark class="'.esc_attr($class).'">'.esc_html($environment['php_version']).' - '.wp_kses_post($notice).'</mark>';
        }
        ?>
      </td>
    </tr>
    <?php if (function_exists('ini_get')) { ?>
        <tr>
          <td><?php esc_html_e('PHP post max size', 'outofthebox'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The largest filesize that can be contained in one post.', 'outofthebox')); ?></td>
          <td><?php echo esc_html(size_format($environment['php_post_max_size'])); ?></td>
        </tr>
        <tr>
          <td><?php esc_html_e('PHP time limit', 'outofthebox'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'outofthebox')); ?></td>
          <td><?php echo esc_html($environment['php_max_execution_time']); ?></td>
        </tr>
        <tr>
          <td><?php esc_html_e('PHP max input vars', 'outofthebox'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The maximum number of variables your server can use for a single function to avoid overloads.', 'outofthebox')); ?></td>
          <td><?php echo esc_html($environment['php_max_input_vars']); ?></td>
        </tr>
        <tr>
          <td><?php esc_html_e('cURL version', 'outofthebox'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The version of cURL installed on your server.', 'outofthebox')); ?></td>
          <td><?php echo esc_html($environment['curl_version']); ?></td>
        </tr>
    <?php } ?>

    <tr>
      <td><?php esc_html_e('Max upload size', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The largest filesize that can be uploaded to your WordPress installation.', 'outofthebox')); ?></td>
      <td><?php echo esc_html(size_format($environment['max_upload_size'])); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Default timezone is UTC', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The default timezone for your server.', 'outofthebox')); ?></td>
      <td>
        <?php
        if ('UTC' !== $environment['default_timezone']) {
            // Translators: %s: default timezone..
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('Default timezone is %s - it should be UTC', 'outofthebox'), esc_html($environment['default_timezone'])).'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('cURL', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('We use cURL and fopen to communicate with API services.', 'outofthebox')); ?></td>
      <td>
        <?php
        if ($environment['curl_enabled']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.esc_html__('Your server does not have cURL enabled - The plugin cannot communicate with the API service. Contact your hosting provider.', 'outofthebox').'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>GZip:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('GZip (zlib) is used to compress the output of the plugin.', 'outofthebox')); ?></td>
      <td>
        <?php
        if ($environment['gzip_compression_enabled']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            // Translators: %s: classname and link.
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.sprintf(esc_html__('Your server does not support the %s function - this is required to compress the output of the plugin.', 'outofthebox'), '<a href="https://php.net/manual/en/zlib.installation.php">zlib</a>').'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('Multibyte string', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Multibyte String (mbstring) is used to convert character encoding, like for emails or converting characters to lowercase.', 'outofthebox')); ?></td>
      <td>
        <?php
        if ($environment['mbstring_enabled']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            // Translators: %s: classname and link.
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('Your server does not support the %s functions - this is required for better character encoding. Some fallbacks will be used instead for it.', 'outofthebox'), '<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>').'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Flock:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Portable advisory file locking (flock) is used to for the caching mechanism.', 'outofthebox')); ?></td>
      <td>
        <?php
        if ($environment['flock']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            // Translators: %s: classname and link.
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('Your server does not support the %s function - this is required for the caching mechanisms. Please enable this function to prevent caching problems.', 'outofthebox'), '<a href="https://www.php.net/manual/en/function.flock.php">flock</a>').'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>ZipArchive:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('This extension (ZipArchive) is used when creating zip files while downloading multiple files at once.', 'outofthebox')); ?></td>
      <td>
        <?php
        if ($environment['zip_archive']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            // Translators: %s: classname and link.
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.sprintf(esc_html__('Your server does not support the %s function - this is required for creating zip files.', 'outofthebox'), '<a href="https://www.php.net/manual/en/zip.setup.php">ZipArchive</a>').'</mark>';
        }
        ?>
      </td>
    </tr>
    <?php
    $rows = apply_filters('outofthebox_system_status_environment_rows', []);
    foreach ($rows as $row) {
        if (!empty($row['success'])) {
            $css_class = 'yes';
            $icon = '<span class="dashicons dashicons-yes"></span>';
        } else {
            $css_class = 'error';
            $icon = '<span class="dashicons dashicons-no-alt"></span>';
        } ?>
        <tr>
          <td><?php echo esc_html($row['name']); ?>:</td>
          <td class="help"><?php echo esc_html(isset($row['help']) ? $row['help'] : ''); ?></td>
          <td>
            <mark class="<?php echo esc_attr($css_class); ?>">
              <?php echo wp_kses_post($icon); ?> <?php echo wp_kses_data(!empty($row['note']) ? $row['note'] : ''); ?>
            </mark>
          </td>
        </tr>
        <?php
    }
    ?>
  </tbody>
</table>


<table class="wpcp_systeminfo_table widefat" cellspacing="0">
  <thead>
    <tr>
      <th colspan="3"><h2><?php esc_html_e('Security', 'outofthebox'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php esc_html_e('Secure connection (HTTPS)', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Is the connection to your store secure?', 'outofthebox')); ?></td>
      <td>
        <?php if ($environment['secure_connection']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="error"><span class="dashicons dashicons-warning"></span>
              <?php
              // Translators: %s: docs link.
              echo wp_kses_post(sprintf(__('Your store is not using HTTPS. <a href="%s" target="_blank">Learn more about HTTPS and SSL Certificates</a>.', 'outofthebox'), 'https://www.wpbeginner.com/wp-tutorials/how-to-add-ssl-and-https-in-wordpress/'));
              ?>
            </mark>
        <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('Hide errors from visitors', 'outofthebox'); ?></td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Error messages can contain sensitive information about your store environment. These should be hidden from untrusted visitors.', 'outofthebox')); ?></td>
      <td>
        <?php if ($environment['hide_errors']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="error"><span class="dashicons dashicons-warning"></span><?php esc_html_e('Error messages should not be shown to visitors.', 'outofthebox'); ?></mark>
        <?php } ?>
      </td>
    </tr>
  </tbody>
</table>


<table class="wpcp_systeminfo_table widefat" cellspacing="0">
  <thead>
    <tr>
      <th colspan="3"><h2><?php esc_html_e('Integrations', 'outofthebox'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>WooCommerce</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in WooCommerce for your Digital Products.', 'outofthebox')); ?></td>
      <td>
        <?php
        if (!$environment['woocommerce']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'outofthebox').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.\WC()->version.'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>WooCommerce -> Product Documents</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in the Product Documents extension of WooCommerce.', 'outofthebox')); ?></td>
      <td>
        <?php
        if (!$environment['woocommerce_product_documents']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'outofthebox').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> </mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Gravity Forms</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in Gravity Forms.', 'outofthebox')); ?></td>
      <td>
        <?php
        if (!$environment['gravity_forms']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'outofthebox').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.\GFCommon::$version.'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Gravity PDF</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The Gravity PDF integrations allow you to store your Gravity Forms submissions directly in the cloud.', 'outofthebox')); ?></td>
      <td>
        <?php
        if (!$environment['gravity_pdf']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'outofthebox').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.PDF_EXTENDED_VERSION.'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Contact Form 7</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in Contact Form 7.', 'outofthebox')); ?></td>

      <td>
        <?php
        if (!$environment['contact_form_7']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'outofthebox').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.WPCF7_VERSION.'</mark>';
        }
        ?>
      </td>
    </tr>
  </tbody>
</table>



<table class="wpcp_systeminfo_table widefat" cellspacing="0">
  <thead>
    <tr>
      <th colspan="3"><h2><?php esc_html_e('Theme', 'outofthebox'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php esc_html_e('Name', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The name of the current active theme.', 'outofthebox')); ?></td>
      <td><?php echo esc_html($theme['name']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Version', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The installed version of the current active theme.', 'outofthebox')); ?></td>
      <td><?php echo esc_html($theme['version']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Author URL', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The theme developers URL.', 'outofthebox')); ?></td>
      <td><?php echo esc_html($theme['author_url']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Child theme', 'outofthebox'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Displays whether or not the current theme is a child theme.', 'outofthebox')); ?></td>
      <td>
        <?php
        if ($theme['is_child_theme']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            // Translators: %s docs link.
            echo '<span class="dashicons dashicons-no-alt"></span> &ndash;';
        }
        ?>
      </td>
    </tr>
    <?php if ($theme['is_child_theme']) { ?>
        <tr>
          <td><?php esc_html_e('Parent theme name', 'outofthebox'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The name of the parent theme.', 'outofthebox')); ?></td>
          <td><?php echo esc_html($theme['parent_name']); ?></td>
        </tr>
        <tr>
          <td><?php esc_html_e('Parent theme version', 'outofthebox'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The installed version of the parent theme.', 'outofthebox')); ?></td>
          <td><?php echo esc_html($theme['parent_version']); ?></td>
        </tr>
        <tr>
          <td><?php esc_html_e('Parent theme author URL', 'outofthebox'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The parent theme developers URL.', 'outofthebox')); ?></td>
          <td><?php echo esc_html($theme['parent_author_url']); ?></td>
        </tr>
    <?php } ?>
  </tbody>
</table>


<table class="wpcp_systeminfo_table widefat" cellspacing="0">
  <thead>
    <tr>
      <th colspan="3"><h2><?php esc_html_e('Active plugins', 'outofthebox'); ?> (<?php echo count($active_plugins_data); ?>)</h2></th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($active_plugins_data as $plugin) {
        if (!empty($plugin['name'])) {
            $dirname = dirname($plugin['plugin']);

            // Link the plugin name to the plugin url if available.
            $plugin_name = esc_html($plugin['name']);
            if (!empty($plugin['url'])) {
                $plugin_name = '<a href="'.esc_url($plugin['url']).'" aria-label="'.esc_attr__('Visit plugin homepage', 'outofthebox').'" target="_blank">'.$plugin_name.'</a>';
            }

            $network_string = '';
            if (false !== $plugin['network_activated']) {
                $network_string = ' &ndash; <strong style="color:black;">'.esc_html__('Network enabled', 'woocommerce').'</strong>';
            } ?>
            <tr>
              <td><?php echo wp_kses_post($plugin_name); ?></td>
              <td class="help">&nbsp;</td>
              <td>
                <?php
                // translators: %s: plugin author
                printf(esc_html__('by %s', 'outofthebox'), esc_html($plugin['author_name']));
            echo ' &ndash; '.esc_html($plugin['version']).$network_string; // WPCS: XSS ok.
                ?>
              </td>
            </tr>
            <?php
        }
    }
    ?>
  </tbody>
</table>