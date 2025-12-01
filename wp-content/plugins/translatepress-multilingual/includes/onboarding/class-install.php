<?php
class TRP_Step_Install implements TRP_Onboarding_Step_Interface {
    protected array $settings;
    // tp-testing-page needed for testing functionality since pro plugins get disabled by the development version
    private array $pro_slugs = array('tp-testing-page', 'translatepress-personal', 'translatepress-business', 'translatepress-developer');
    protected WP_Error $errors;

    public function __construct( $settings ){
        $this->settings = $settings;
        $this->errors = new WP_Error();

        // Redirect to License if we detect a version other then the free version.
        $trp = TRP_Translate_Press::get_trp_instance();
        if(!in_array( 'TranslatePress', $trp->tp_product_name )){
            wp_redirect(add_query_arg(['step' => 'license']));
        }
    }

    public function handle($data) {
        // Handle individual plugin activation/deactivation
        if (isset($data['plugin_action']) && isset($data['plugin_path'])) {
            $this->handle_plugin_action($data);
            return;
        }

        // Handle plugin upload and installation
        $nonce = isset($data['_wpnonce_trp_onboarding_install']) ? $data['_wpnonce_trp_onboarding_install'] : '';

        if (!wp_verify_nonce($nonce, 'trp_onboarding_install')) {
            $this->errors->add('nonce_fail_languages', __('The link you followed has expired. Please reload the page and try again.', 'translatepress-multilingual'));
            return;
        } elseif (empty($_FILES['plugin_zip_file']) && !is_array($_FILES['plugin_zip_file'])) {
            $this->errors->add('error_plugin_zip_empty', __('Please upload a TranslatePress Pro plugin file.', 'translatepress-multilingual'));
            return;
        }

        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/misc.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        // Upload file to temp location
        $overrides = ['test_form' => false];
        $upload = wp_handle_upload($_FILES['plugin_zip_file'], $overrides);

        if (!empty($upload['error'])) {
            $this->errors->add('error_plugin_zip_file', __('Upload error: ', 'translatepress-multilingual') . esc_html($upload['error']));
            return;
        }

        $zip_path = $upload['file']; // Full server path to ZIP

        // Install plugin from local file
        $skin = new Automatic_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader($skin);
        $result = $upgrader->install($zip_path, array('clear_update_cache' => true, 'overwrite_package'  => true));

        // Delete uploaded temp file
        unlink($zip_path);

        if (is_wp_error($result)) {
            $this->errors->add('error_plugin_zip_file', __('Install failed: ', 'translatepress-multilingual') . esc_html($result->get_error_message()));
            return;
        }

        $plugin_file = $upgrader->plugin_info();

        if (!$plugin_file) {
            $this->errors->add('error_plugin_zip_file', __('Plugin installed, but entry file not found. ', 'translatepress-multilingual'));
            return;
        }

        $activation_result = activate_plugin($plugin_file);

        if (is_wp_error($activation_result)) {
            $this->errors->add('error_plugin_activation', __('Activation error: ', 'translatepress-multilingual') . esc_html($activation_result->get_error_message()));
            return;
        }

        if (!$this->errors->has_errors()) {
            // If no errors, we save our data and redirect to next step
            wp_redirect(add_query_arg(['step' => 'license']));
            exit;
        }
    }

    private function handle_plugin_action($data) {
        $plugin_path = sanitize_text_field($data['plugin_path']);
        $plugin_action = sanitize_text_field($data['plugin_action']);
        
        // Validate plugin path is from our allowed pro slugs
        $slug = explode('/', $plugin_path)[0];

        if (!in_array($slug, $this->pro_slugs)) {
            $this->errors->add('error_invalid_plugin', __('Invalid plugin specified.', 'translatepress-multilingual'));
            return;
        }

        // Verify nonce - use the same pattern as in render()
        $nonce_action = $plugin_action . '-plugin_' . $plugin_path;
        $slug_in_nonce = str_replace('-', '_', $slug);
        $nonce_field = '_wpnonce_' . $slug_in_nonce;
        $nonce = isset($data[$nonce_field]) ? $data[$nonce_field] : '';

        if (!wp_verify_nonce($nonce, $nonce_action)) {
            $this->errors->add('nonce_fail_plugin', __('The link you followed has expired. Please reload the page and try again.', 'translatepress-multilingual'));
            return;
        }

        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        if ($plugin_action === 'activate') {
            $result = activate_plugin($plugin_path);
            if (is_wp_error($result)) {
                $this->errors->add('error_plugin_activation', __('Plugin activation failed: ', 'translatepress-multilingual') . esc_html($result->get_error_message()));
                return;
            }
        } elseif ($plugin_action === 'deactivate') {
            deactivate_plugins($plugin_path);
            if (is_plugin_active($plugin_path)) {
                $this->errors->add('error_plugin_deactivation', __('Plugin deactivation failed.', 'translatepress-multilingual'));
                return;
            }
        } else {
            $this->errors->add('error_invalid_action', __('Invalid action specified.', 'translatepress-multilingual'));
            return;
        }

        // Reload the page to reflect changes
        if (!$this->errors->has_errors() && $plugin_action == 'activate') {
            wp_redirect(add_query_arg(['step' => 'install']));
            exit;
        }
    }

    public function render() {
        ?>
        <h1><?php esc_html_e('First, install and activate TranslatePress Pro', 'translatepress-multilingual'); ?></h1>
        <h3>
            <?php esc_html_e('Please upload the TranslatePress PRO zip archive from your', 'translatepress-multilingual'); ?><br/>
            <a href="https://translatepress.com/account/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=install-pro" target="_blank"> <?php esc_html_e('TranslatePress Account', 'translatepress-multilingual'); ?></a>
        </h3>
        <?php
        foreach ($this->errors->get_error_messages() as $message) {
            echo '<div class="ob-notice ob-notice-error">' . esc_html($message) . '</div>';
        }
        ?>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('trp_onboarding_install', '_wpnonce_trp_onboarding_install'); ?>
            <div class="trp-onboarding-install">
                <input type="file" name="plugin_zip_file" accept=".zip" required  /><button class="trp-submit-btn" type="submit"><?php esc_html_e('Install and Activate', 'translatepress-multilingual');?></button>
            </div>
        </form>
        <?php
        $all_plugins = get_plugins();

        if ($this->is_pro_installed()){
            echo '<h3>' . esc_html__('Installed Pro versions', 'translatepress-multilingual') . '</h3>';
            echo '<ul class="trp-plugins">';
            foreach ( $all_plugins as $plugin_path => $plugin_data ) {
                $slug = explode( '/', $plugin_path )[0];
                if ( in_array( $slug, $this->pro_slugs ) ) {
                    $is_active = is_plugin_active( $plugin_path );
                    $action = $is_active ? 'deactivate' : 'activate';
                    $button_label = ucfirst( $action );
                    $nonce_action = $action . '-plugin_' . $plugin_path;
                    $slug_in_nonce = str_replace('-', '_', $slug);
                    ?>
                    <li>
                        <form method="post">
                            <?php wp_nonce_field($nonce_action, '_wpnonce_' . $slug_in_nonce); ?>
                            <label><?php echo esc_html($plugin_data['Name']); ?></label>
                            <input type="hidden" name="plugin_path" value="<?php echo esc_attr( $plugin_path ); ?>"/>
                            <input type="hidden" name="plugin_action" value="<?php echo esc_attr( $action ); ?>"/>
                            <button type="submit" name="submit" class="trp-button-secondary"><?php echo esc_html($button_label); ?></button>
                        </form>
                    </li>
                    <?php
                }
            }
            echo '</ul>';
        }
        ?>
        <div class="trp-ob-center">
            <?php
            // Check transient to determine which step to go back to
            $previous_step = get_transient('trp_onboarding_previous_step');
            $back_step = ($previous_step) ? $previous_step : 'languages';
            ?>
            <a href="<?php echo esc_url(add_query_arg(['step' => $back_step])); ?>"><?php esc_html_e('« Go back', 'translatepress-multilingual'); ?></a>

            <?php if ($this->is_pro_installed()) :?>
            <a href="<?php echo esc_url(add_query_arg(['step' => 'license'])); ?>" style="margin-left: 2rem;"><?php esc_html_e('Activate License »', 'translatepress-multilingual'); ?></a>
            <?php endif; ?>
        </div>
        <?php
    }

    private function is_pro_installed()
    {
        $all_plugins = get_plugins();
        foreach ( $all_plugins as $plugin_path => $plugin_data ) {
            $slug = explode( '/', $plugin_path )[0];
            if ( in_array( $slug, $this->pro_slugs ) ) {
                return true;
            }
        }
        return false;
    }
}