<?php
class TRP_Step_Switcher implements TRP_Onboarding_Step_Interface {
    protected $settings;
    protected $config;
    protected WP_Error $errors;

    public function __construct( $settings ){
        $this->settings = $settings;
        
        // Get the language switcher tab component to access config
        $trp = TRP_Translate_Press::get_trp_instance();
        $language_switcher_tab = $trp->get_component('language_switcher_tab');
        $this->config = $language_switcher_tab->get_initial_config();
        
        // Ensure config is an array
        if (!is_array($this->config)) {
            $this->config = [];
        }
        
        $this->errors = new WP_Error();
    }
    public function handle($data) {
        // Validation
        $nonce = isset($data['_wpnonce_trp_onboarding_switcher']) ? $data['_wpnonce_trp_onboarding_switcher'] : '';

        if (!wp_verify_nonce($nonce, 'trp_onboarding_switcher')) {
            $this->errors->add('nonce_fail_switcher', __('The link you followed has expired. Please reload the page and try again.', 'translatepress-multilingual'));
            return;
        }

        if ($this->errors->has_errors()) {
            set_transient('trp_onboarding_errors', $this->errors, 30);
            wp_redirect(add_query_arg(['step' => 'switcher']));
            exit;
        }

        // Handle floating switcher enable/disable
        if (isset($data['trp_language_switcher'])) {
            $this->config['floater']['enabled'] = ($data['trp_language_switcher'] === 'yes');
        } else {
            $this->config['floater']['enabled'] = false;
        }

        // Handle switcher location
        if (isset($data['switcher_location']) && !empty($data['switcher_location'])) {
            $location = sanitize_text_field($data['switcher_location']);
            $valid_locations = ['bottom-right', 'bottom-left', 'top-right', 'top-left'];
            if (in_array($location, $valid_locations)) {
                $this->config['floater']['layoutCustomizer']['desktop']['position'] = $location;
                $this->config['floater']['layoutCustomizer']['mobile']['position'] = $location;
            }
        }

        // Handle template selection
        if (isset($data['switcher_template']) && !empty($data['switcher_template'])) {
            $template = sanitize_text_field($data['switcher_template']);
            $this->apply_template($template);
        }

        // Apply position-based border radius after template selection
        if (isset($data['switcher_location']) && !empty($data['switcher_location'])) {
            $position = sanitize_text_field($data['switcher_location']);
            $this->apply_position_based_border_radius($position);
        }

        // Save the updated config
        update_option('trp_language_switcher_settings', $this->config);

        wp_redirect(add_query_arg(['step' => 'autotranslation']));
        exit;
    }

    /**
     * Apply template color settings to the current config
     * 
     * @param string $template Template name (default, dark, border, transparent)
     */
    private function apply_template($template) {
        $templates = $this->get_template_settings();
        
        if (!isset($templates[$template])) {
            return; // Invalid template, skip
        }
        
        $template_settings = $templates[$template];
        
        // Apply floater settings
        if (isset($template_settings['floater'])) {
            foreach ($template_settings['floater'] as $key => $value) {
                $this->config['floater'][$key] = $value;
            }
        }
        
        // Apply shortcode settings
        if (isset($template_settings['shortcode'])) {
            foreach ($template_settings['shortcode'] as $key => $value) {
                $this->config['shortcode'][$key] = $value;
            }
        }
    }

    /**
     * Get template color configurations based on Vue.js preset settings
     * 
     * @return array Template configurations
     */
    private function get_template_settings() {
        return [
            'default' => [
                'floater' => [
                    'bgColor' => '#ffffff',
                    'bgHoverColor' => '#0000000d', 
                    'textColor' => '#143852',
                    'textHoverColor' => '#1d2327',
                    'borderColor' => '#1438521a'
                ],
                'shortcode' => [
                    'bgColor' => '#ffffff',
                    'bgHoverColor' => '#0000000d',
                    'textColor' => '#143852', 
                    'textHoverColor' => '#1d2327',
                    'borderColor' => '#1438521a'
                ]
            ],
            'dark' => [
                'floater' => [
                    'bgColor' => '#000000',
                    'bgHoverColor' => '#444444',
                    'textColor' => '#ffffff', 
                    'textHoverColor' => '#eeeeee',
                    'borderColor' => 'transparent'
                ],
                'shortcode' => [
                    'bgColor' => '#000000',
                    'bgHoverColor' => '#444444',
                    'textColor' => '#ffffff',
                    'textHoverColor' => '#eeeeee', 
                    'borderColor' => 'transparent'
                ]
            ],
            'border' => [
                'floater' => [
                    'bgColor' => '#FFFFFF',
                    'bgHoverColor' => '#000000',
                    'textColor' => '#143852',
                    'textHoverColor' => '#ffffff',
                    'borderColor' => '#143852'
                ],
                'shortcode' => [
                    'bgColor' => '#FFFFFF', 
                    'bgHoverColor' => '#000000',
                    'textColor' => '#143852',
                    'textHoverColor' => '#ffffff',
                    'borderColor' => '#143852'
                ]
            ],
            'transparent' => [
                'floater' => [
                    'bgColor' => '#FFFFFFB2',
                    'bgHoverColor' => '#FFFFFFB2', 
                    'textColor' => '#000000',
                    'textHoverColor' => '#000000',
                    'borderColor' => 'transparent'
                ],
                'shortcode' => [
                    'bgColor' => '#FFFFFFB2',
                    'bgHoverColor' => '#FFFFFFB2',
                    'textColor' => '#000000', 
                    'textHoverColor' => '#000000',
                    'borderColor' => 'transparent'
                ]
            ]
        ];
    }

    /**
     * Apply position-based border radius to the floater config
     * 
     * @param string $position Position (bottom-right, bottom-left, top-right, top-left)
     */
    private function apply_position_based_border_radius($position) {
        // Get existing border radius or use default
        $existing_radius = isset($this->config['floater']['borderRadius']) ? $this->config['floater']['borderRadius'] : null;
        $default_radius = 8; // Default radius value if none exists
        
        // Use existing radius values if available, otherwise use default
        $radius_value = $default_radius;
        if (is_array($existing_radius) && !empty($existing_radius)) {
            // Use the first non-zero value from existing radius, or default if all are zero
            foreach ($existing_radius as $r) {
                if ($r > 0) {
                    $radius_value = $r;
                    break;
                }
            }
        }
        
        // Calculate border radius based on position
        // borderRadius format: [top-left, top-right, bottom-right, bottom-left]
        switch ($position) {
            case 'bottom-left':
            case 'bottom-right':
                // Bottom positions: top corners have radius, bottom corners are 0
                $border_radius = [$radius_value, $radius_value, 0, 0];
                break;
            case 'top-left':
            case 'top-right':
                // Top positions: bottom corners have radius, top corners are 0
                $border_radius = [0, 0, $radius_value, $radius_value];
                break;
            default:
                // Fallback to existing radius or default
                $border_radius = is_array($existing_radius) ? $existing_radius : [$default_radius, $default_radius, 0, 0];
                break;
        }
        
        // Apply the border radius to floater config
        $this->config['floater']['borderRadius'] = $border_radius;
    }

    public function render() {
        $tp_switcher_default = TRP_PLUGIN_URL . 'assets/images/onboarding-switcher-default.svg';
        $tp_switcher_dark = TRP_PLUGIN_URL . 'assets/images/onboarding-switcher-dark.svg';
        $tp_switcher_border = TRP_PLUGIN_URL . 'assets/images/onboarding-switcher-border.svg';
        $tp_switcher_transparent = TRP_PLUGIN_URL . 'assets/images/onboarding-switcher-transparent.svg';
        
        // Get current values for form defaults
        $floater_enabled = isset($this->config['floater']['enabled']) && !empty($this->config['floater']['enabled']);
        $current_position = isset($this->config['floater']['layoutCustomizer']['desktop']['position']) ? $this->config['floater']['layoutCustomizer']['desktop']['position'] : 'bottom-right';

        ?>
        <?php
        foreach ($this->errors->get_error_messages() as $message) {
            echo '<div class="ob-notice ob-notice-error">' . esc_html($message) . '</div>';
        }
        ?>

        <h1><?php esc_html_e('Set up Language Switcher', 'translatepress-multilingual'); ?></h1>
        <h3><?php esc_html_e('Select the style of the language switcher. You will find more ways to display it, in plugin settings.', 'translatepress-multilingual'); ?></h3>
        
        <form method="post" class="trp-switcher-wrap">
            <?php wp_nonce_field('trp_onboarding_switcher', '_wpnonce_trp_onboarding_switcher'); ?>
            
            <!-- Enable Floating Switcher -->
            <div class="trp-settings-options-item">
                <label for="trp-machine-translation-enabled">Enable Floating Switcher</label>
                <div class="trp-switch">
                    <input type="checkbox" id="trp-language-switcher-enabled" class="trp-switch-input" name="trp_language_switcher" value="yes" <?php checked($floater_enabled); ?>>
                    <label for="trp-language-switcher-enabled" class="trp-switch-label"></label>
                </div>

            </div>
            <p class="trp-onboarding-description"><?php esc_html_e('Displays a small language drop-down across your website, in a corner of your choosing.', 'translatepress-multilingual'); ?></p>

            <!-- Switcher Location -->
            <div class="trp-settings-options-item">
                <label for="trp-switcher-location"><?php esc_html_e('Switcher Location', 'translatepress-multilingual'); ?></label>
                <select id="trp-switcher-location" name="switcher_location">
                    <option value="bottom-right" <?php selected($current_position, 'bottom-right'); ?>><?php esc_html_e('Bottom Right', 'translatepress-multilingual'); ?></option>
                    <option value="bottom-left" <?php selected($current_position, 'bottom-left'); ?>><?php esc_html_e('Bottom Left', 'translatepress-multilingual'); ?></option>
                    <option value="top-right" <?php selected($current_position, 'top-right'); ?>><?php esc_html_e('Top Right', 'translatepress-multilingual'); ?></option>
                    <option value="top-left" <?php selected($current_position, 'top-left'); ?>><?php esc_html_e('Top Left', 'translatepress-multilingual'); ?></option>
                </select>
            </div>

            <!-- Switcher Template -->
            <div>
                <label for=""><?php esc_html_e('Apply a Template', 'translatepress-multilingual'); ?></label>
                <p class="trp-onboarding-description"><?php esc_html_e('You can customize the design later', 'translatepress-multilingual'); ?></p>
                <div class="trp-switcher-templates">
                    <div class="trp-template-row">
                        <!-- Default Template -->
                        <div class="trp-template-preview">
                            <div class="trp-switcher-img">
                                <img src="<?php echo esc_url( $tp_switcher_default ); ?>" alt="<?php esc_attr_e('Default Template', 'translatepress-multilingual'); ?>" />
                            </div>
                            <label class="trp-template-option">
                                <input type="radio" name="switcher_template" value="default">
                                <?php esc_html_e('Default', 'translatepress-multilingual'); ?>
                            </label>
                        </div>

                        <!-- Dark Template -->
                        <div class="trp-template-preview">
                            <div class="trp-switcher-img">
                                <img src="<?php echo esc_url( $tp_switcher_dark ); ?>" alt="<?php esc_attr_e('Dark Template', 'translatepress-multilingual'); ?>" />
                            </div>
                            <label class="trp-template-option">
                                <input type="radio" name="switcher_template" value="dark">
                                <?php esc_html_e('Dark', 'translatepress-multilingual'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="trp-template-row">
                        <!-- Border Template -->
                        <div class="trp-template-preview">
                            <div class="trp-switcher-img">
                                <img src="<?php echo esc_url( $tp_switcher_border ); ?>" alt="<?php esc_attr_e('Border Template', 'translatepress-multilingual'); ?>" />
                            </div>
                            <label class="trp-template-option">
                                <input type="radio" name="switcher_template" value="border">
                                <?php esc_html_e('Border', 'translatepress-multilingual'); ?>
                            </label>
                        </div>

                        <!-- Transparent Template -->
                        <div class="trp-template-preview">
                            <div class="trp-switcher-img">
                                <img src="<?php echo esc_url( $tp_switcher_transparent ); ?>" alt="<?php esc_attr_e('Transparent Template', 'translatepress-multilingual'); ?>" />
                            </div>
                            <label class="trp-template-option">
                                <input type="radio" name="switcher_template" value="transparent">
                                <?php esc_html_e('Transparent', 'translatepress-multilingual'); ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="trp-continue-onboarding">
                <button type="submit" class="trp-submit-btn" style="min-width: calc(50% - 0.5rem) !important; "><?php esc_html_e('Continue', 'translatepress-multilingual'); ?></button>
            </div>
        </form>
        <?php
    }
}