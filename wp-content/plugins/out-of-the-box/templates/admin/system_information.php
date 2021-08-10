<?php
defined('ABSPATH') || exit;

if (!function_exists('wpcp_help_tip')) {
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
}

?>
<table class="wpcp_systeminfo_table widefat" cellspacing="0" >
  <thead>
    <tr>
      <th colspan="3"><h2><?php esc_html_e('WordPress environment', 'wpcloudplugins'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php esc_html_e('WordPress address (URL)', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The root URL of your site.', 'wpcloudplugins')); ?></td>
      <td><?php echo esc_html($environment['site_url']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Site address (URL)', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The homepage URL of your site.', 'wpcloudplugins')); ?></td>
      <td><?php echo esc_html($environment['home_url']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Plugin version', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The version of this plugin installed on your site.', 'wpcloudplugins')); ?></td>
      <td><?php echo esc_html($environment['version']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress version', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The version of WordPress installed on your site.', 'wpcloudplugins')); ?></td>
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
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('%1$s - There is a newer version of WordPress available (%2$s)', 'wpcloudplugins'), esc_html($environment['wp_version']), esc_html($latest_version)).'</mark>';
        } else {
            echo '<mark class="yes">'.esc_html($environment['wp_version']).'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress multisite', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Whether or not you have WordPress Multisite enabled.', 'wpcloudplugins')); ?></td>
      <td><?php echo ($environment['wp_multisite']) ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress memory limit', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The maximum amount of memory (RAM) that your site can use at one time.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if ($environment['wp_memory_limit'] < 268435456) {
            // Translators: %1$s: Memory limit, %2$s: Docs link.
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('%1$s - We recommend setting memory to at least 256MB. See: %2$s', 'wpcloudplugins'), esc_html(size_format($environment['wp_memory_limit'])), '<a href="https://wordpress.org/support/article/editing-wp-config-php/#increasing-memory-allocated-to-php" target="_blank">'.esc_html__('Increasing memory allocated to PHP', 'wpcloudplugins').'</a>').'</mark>';
        } else {
            echo '<mark class="yes">'.esc_html(size_format($environment['wp_memory_limit'])).'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress debug mode', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Displays whether or not WordPress is in Debug Mode.', 'wpcloudplugins')); ?></td>
      <td>
        <?php if ($environment['wp_debug_mode']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="no">&ndash;</mark>
        <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('WordPress cron', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Displays whether or not WP Cron Jobs are enabled.', 'wpcloudplugins')); ?></td>
      <td>
        <?php if ($environment['wp_cron']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="no">&ndash;</mark>
        <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('Language', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The current language used by WordPress. Default = English', 'wpcloudplugins')); ?></td>
      <td><?php echo esc_html($environment['language']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('External object cache', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Displays whether or not WordPress is using an external object cache.', 'wpcloudplugins')); ?></td>
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
      <th colspan="3"><h2><?php esc_html_e('Server environment', 'wpcloudplugins'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php esc_html_e('Server info', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Information about the web server that is currently hosting your site.', 'wpcloudplugins')); ?></td>
      <td><?php echo esc_html($environment['server_info']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('PHP version', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The version of PHP installed on your hosting server.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if (version_compare($environment['php_version'], '7.2', '>=')) {
            echo '<mark class="yes">'.esc_html($environment['php_version']).'</mark>';
        } else {
            $update_link = ' <a href="https://wordpress.org/support/update-php/" target="_blank">'.esc_html__('How to update your PHP version', 'wpcloudplugins').'</a>';
            $class = 'error';

            if (version_compare($environment['php_version'], '7.2', '<')) {
                $notice = '<span class="dashicons dashicons-warning"></span> '.esc_html__('We recommend using PHP version 7.2 or above for greater performance and security.', 'wpcloudplugins').$update_link;
                $class = 'error';
            }

            echo '<mark class="'.esc_attr($class).'">'.esc_html($environment['php_version']).' - '.wp_kses_post($notice).'</mark>';
        }
        ?>
      </td>
    </tr>
    <?php if (function_exists('ini_get')) { ?>
        <tr>
          <td>PHP post max size:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The largest filesize that can be contained in one post.', 'wpcloudplugins')); ?></td>
          <td><?php echo esc_html(size_format($environment['php_post_max_size'])); ?></td>
        </tr>
        <tr>
          <td>PHP time limit:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'wpcloudplugins')); ?></td>
          <td><?php echo esc_html($environment['php_max_execution_time']); ?></td>
        </tr>
        <tr>
          <td>PHP max input vars:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The maximum number of variables your server can use for a single function to avoid overloads.', 'wpcloudplugins')); ?></td>
          <td><?php echo esc_html($environment['php_max_input_vars']); ?></td>
        </tr>
        <tr>
          <td>cURL version:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The version of cURL installed on your server.', 'wpcloudplugins')); ?></td>
          <td><?php echo esc_html($environment['curl_version']); ?></td>
        </tr>
    <?php } ?>

    <tr>
      <td><?php esc_html_e('Max upload size', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The largest filesize that can be uploaded to your WordPress installation.', 'wpcloudplugins')); ?></td>
      <td><?php echo esc_html(size_format($environment['max_upload_size'])); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Default timezone is UTC', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The default timezone for your server.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if ('UTC' !== $environment['default_timezone']) {
            // Translators: %s: default timezone..
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('Default timezone is %s - it should be UTC', 'wpcloudplugins'), esc_html($environment['default_timezone'])).'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>cURL:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('We use cURL and fopen to communicate with API services.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if ($environment['curl_enabled']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.esc_html__('Your server does not have cURL enabled - The plugin cannot communicate with the API service. Contact your hosting provider.', 'wpcloudplugins').'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>GZip:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('GZip (zlib) is used to compress the AJAX responses of the plugin.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if ($environment['gzip_compression_enabled']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            // Translators: %s: classname and link.
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.sprintf(esc_html__('Your server does not support the %s function - this is required to compress the output of the plugin.', 'wpcloudplugins'), '<a href="https://php.net/manual/en/zlib.installation.php">zlib</a>').'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Multibyte string:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Multibyte String (mbstring) is used to convert character encoding, like for emails or converting characters to lowercase.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if ($environment['mbstring_enabled']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            // Translators: %s: classname and link.
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('Your server does not support the %s functions - this is required for better character encoding. Some fallbacks will be used instead for it.', 'wpcloudplugins'), '<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>').'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Flock:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Portable advisory file locking (flock) is used to for the caching mechanism.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if ($environment['flock']) {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
        } else {
            // Translators: %s: classname and link.
            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> '.sprintf(esc_html__('Your server does not support the %s function - this is required for the caching mechanisms. Please enable this function to prevent caching problems.', 'wpcloudplugins'), '<a href="https://www.php.net/manual/en/function.flock.php">flock</a>').'</mark>';
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
      <th colspan="3"><h2><?php esc_html_e('Security', 'wpcloudplugins'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php esc_html_e('Secure connection (HTTPS)', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Is the connection to your store secure?', 'wpcloudplugins')); ?></td>
      <td>
        <?php if ($environment['secure_connection']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="error"><span class="dashicons dashicons-warning"></span>
              <?php
              // Translators: %s: docs link.
              echo wp_kses_post(sprintf(__('Your site is not using HTTPS. <a href="%s" target="_blank">Learn more about HTTPS and SSL Certificates</a>.', 'wpcloudplugins'), 'https://www.wpbeginner.com/wp-tutorials/how-to-add-ssl-and-https-in-wordpress/'));
              ?>
            </mark>
        <?php } ?>
      </td>
    </tr>
    <tr>
      <td><?php esc_html_e('OpenSSL support', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Can the plugin use OpenSSL for secure connections and encryption?', 'wpcloudplugins')); ?></td>
      <td>
        <?php if ($environment['openssl_encrypt']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="error"><span class="dashicons dashicons-warning"></span></mark>
        <?php } ?>
      </td>
    </tr>    
    <tr>
      <td><?php esc_html_e('Hide errors from visitors', 'wpcloudplugins'); ?></td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Error messages can contain sensitive information about your store environment. These should be hidden from untrusted visitors.', 'wpcloudplugins')); ?></td>
      <td>
        <?php if ($environment['hide_errors']) { ?>
            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
        <?php } else { ?>
            <mark class="error"><span class="dashicons dashicons-warning"></span><?php esc_html_e('Error messages should not be shown to visitors.', 'wpcloudplugins'); ?></mark>
        <?php } ?>
      </td>
    </tr>
  </tbody>
</table>


<table class="wpcp_systeminfo_table widefat" cellspacing="0">
  <thead>
    <tr>
      <th colspan="3"><h2><?php esc_html_e('Integrations', 'wpcloudplugins'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>WooCommerce</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in WooCommerce for your Digital Products.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if (!$environment['woocommerce']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'wpcloudplugins').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.\WC()->version.'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>WooCommerce -> Product Documents</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in the Product Documents extension of WooCommerce.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if (!$environment['woocommerce_product_documents']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'wpcloudplugins').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> </mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Elementor</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in Elementor.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if (!$environment['elementor']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'wpcloudplugins').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.ELEMENTOR_VERSION.'</mark>';
        }
        ?>
      </td>
    </tr>     
    <tr>
      <td>Fluent Forms</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in Fluent Forms.', 'wpcloudplugins')); ?></td>

      <td>
        <?php
        if (!$environment['fluentforms']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'wpcloudplugins').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.FLUENTFORM_VERSION.'</mark>';
        }
        ?>
      </td>
    </tr>      
    <tr>
      <td>Formidable Forms</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in Formidable Forms.', 'wpcloudplugins')); ?></td>

      <td>
        <?php
        if (!$environment['formidableforms']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'wpcloudplugins').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.\FrmAppHelper::plugin_version().'</mark>';
        }
        ?>
      </td>
    </tr>    
    <tr>
      <td>WPForms</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in WPForms.', 'wpcloudplugins')); ?></td>

      <td>
        <?php
        if (!$environment['wpforms']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'wpcloudplugins').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.WPFORMS_VERSION.'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Gravity Forms</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in Gravity Forms.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if (!$environment['gravity_forms']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'wpcloudplugins').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.\GFCommon::$version.'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Gravity PDF</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The Gravity PDF integrations allow you to store your Gravity Forms submissions directly in the cloud.', 'wpcloudplugins')); ?></td>
      <td>
        <?php
        if (!$environment['gravity_pdf']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'wpcloudplugins').'</mark>';
        } else {
            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> '.PDF_EXTENDED_VERSION.'</mark>';
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>Contact Form 7</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('You can use the plugin in Contact Form 7.', 'wpcloudplugins')); ?></td>

      <td>
        <?php
        if (!$environment['contact_form_7']) {
            echo '<mark class="no"><span class="dashicons dashicons-no-alt"></span> '.esc_html__('Not active', 'wpcloudplugins').'</mark>';
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
      <th colspan="3"><h2><?php esc_html_e('Theme', 'wpcloudplugins'); ?></h2></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php esc_html_e('Name', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The name of the current active theme.', 'wpcloudplugins')); ?></td>
      <td><?php echo esc_html($theme['name']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Version', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The installed version of the current active theme.', 'wpcloudplugins')); ?></td>
      <td><?php echo esc_html($theme['version']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Author URL', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('The theme developers URL.', 'wpcloudplugins')); ?></td>
      <td><?php echo esc_html($theme['author_url']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Child theme', 'wpcloudplugins'); ?>:</td>
      <td class="help"><?php echo wpcp_help_tip(esc_html__('Displays whether or not the current theme is a child theme.', 'wpcloudplugins')); ?></td>
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
          <td><?php esc_html_e('Parent theme name', 'wpcloudplugins'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The name of the parent theme.', 'wpcloudplugins')); ?></td>
          <td><?php echo esc_html($theme['parent_name']); ?></td>
        </tr>
        <tr>
          <td><?php esc_html_e('Parent theme version', 'wpcloudplugins'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The installed version of the parent theme.', 'wpcloudplugins')); ?></td>
          <td><?php echo esc_html($theme['parent_version']); ?></td>
        </tr>
        <tr>
          <td><?php esc_html_e('Parent theme author URL', 'wpcloudplugins'); ?>:</td>
          <td class="help"><?php echo wpcp_help_tip(esc_html__('The parent theme developers URL.', 'wpcloudplugins')); ?></td>
          <td><?php echo esc_html($theme['parent_author_url']); ?></td>
        </tr>
    <?php } ?>
  </tbody>
</table>


<table class="wpcp_systeminfo_table widefat" cellspacing="0">
  <thead>
    <tr>
      <th colspan="3"><h2><?php esc_html_e('Active plugins', 'wpcloudplugins'); ?> (<?php echo count($active_plugins_data); ?>)</h2></th>
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
                $plugin_name = '<a href="'.esc_url($plugin['url']).'" aria-label="'.esc_attr__('Visit plugin homepage', 'wpcloudplugins').'" target="_blank">'.$plugin_name.'</a>';
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
                printf(esc_html__('by %s', 'wpcloudplugins'), esc_html($plugin['author_name']));
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