<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class TRP_Onboarding
 *
 * Loads required files regarding the TP onboarding, initializes components and hooks methods for the onboarding TP.
 *
 */
class TRP_Onboarding {

    protected $settings;
    protected $steps = [
        'welcome'           => TRP_Step_Welcome::class,
        'install'           => TRP_Step_Install::class,
        'license'           => TRP_Step_License::class,
        'languages'         => TRP_Step_Languages::class,
        'switcher'          => TRP_Step_Switcher::class,
        'autotranslation'   => TRP_Step_AutoTranslation::class,
        'addons'            => TRP_Step_Addons::class,
        'finish'            => TRP_Step_Finish::class,
    ];

    /**
     * The current onboarding step or an error.
     *
     * @var TRP_Onboarding_Step_Interface|WP_Error
     */
    protected $step;

    public function __construct( $settings ){
        $this->settings = $settings;
        add_action( 'admin_init', array( $this, 'run_onboarding_admin' ) );
        // Render both menu & admin page.
        add_action('admin_menu', array($this, 'register_onboarding'));
    }

    public function run_onboarding_admin(){
        if (current_user_can('manage_options') && $this->is_onboarding()) {
            add_action('admin_head', array($this, 'remove_admin_notices'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts_and_styles'));

            // Process form submissions on admin_init to prevent headers already sent issues.
            $this->step = $this->init_step();
            $this->step_handle();
        }


    }

    private function init_step(){
        if(!current_user_can('manage_options') || !$this->is_onboarding()){
            return new WP_Error('not_onboarding', __( 'Not TranslatePress onboarding page.', 'translatepress-multilingual' ));
        }

        if(file_exists(TRP_PLUGIN_DIR . 'includes/onboarding/interface-onboarding-step.php')){
            require_once TRP_PLUGIN_DIR . 'includes/onboarding/interface-onboarding-step.php';
        }

        $step = sanitize_text_field(isset($_GET['step']) ? $_GET['step'] : 'welcome');
        $step_class = (isset($this->steps[$step])) ? $this->steps[$step] : null;
        if($step_class){
            $file = TRP_PLUGIN_DIR . 'includes/onboarding/class-' . $step . '.php';
            if (file_exists($file)) {
                include_once($file);
            }
        }

        if (!$step_class || !class_exists($step_class)) {
            return new WP_Error('invalid_step', sprintf( __( 'Step %s does not exist', 'translatepress-multilingual' ), $step));
        } else {
            return new $step_class($this->settings);
        }
    }

    private function is_onboarding(): bool
    {
        if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            return false;
        }

        if ( empty( $_GET['page'] ) || $_GET['page'] !== 'trp-onboarding' ) {
            return false;
        }

        return true;
    }

    public function register_onboarding(){
        add_submenu_page(
            'translate-press',
            'Setup Wizard',
            'Setup Wizard',
            'manage_options',
            'trp-onboarding',
            array( $this, 'render_template' )
        );
    }

    public function render_template(){
        $full_logo = TRP_PLUGIN_URL . 'assets/images/tp-logo-with-text-dark.svg';
        $small_logo = TRP_PLUGIN_URL . 'assets/images/tp-logo.png';
        ob_start();
        ?>
        <div id="trp-settings-page" class="wrap trp-onboarding">
            <div id="trp-settings-header">
                <div class="trp-settings-logo">
                    <img src="<?php echo esc_url( $full_logo ); ?>"
                         srcset="<?php echo esc_url( $small_logo ); ?> 128w, <?php echo esc_url( $full_logo ); ?> 177w"
                         sizes="(max-width: 520px) 40px, 177px"
                         alt="TranslatePress Logo">
                </div>

                <nav class="trp-onboarding-nav-menu">
                    <ul class="trp-onboarding-nav-list">
                        <li><a href="<?php echo esc_url( admin_url('admin.php?page=trp-onboarding&step=welcome') ); ?>" class="trp-nav-onboarding-dot" aria-label="<?php echo esc_attr__( 'Welcome', 'translatepress-multilingual' ); ?>" title="<?php echo esc_attr__( 'Welcome', 'translatepress-multilingual' ); ?>"></a></li>
                        <li><a href="<?php echo esc_url( admin_url('admin.php?page=trp-onboarding&step=languages') ); ?>" class="trp-nav-onboarding-dot" aria-label="<?php echo esc_attr__( 'Add Languages', 'translatepress-multilingual' ); ?>" title="<?php echo esc_attr__( 'Add Languages', 'translatepress-multilingual' ); ?>"></a></li>
                        <li><a href="<?php echo esc_url( admin_url('admin.php?page=trp-onboarding&step=switcher') ); ?>" class="trp-nav-onboarding-dot" aria-label="<?php echo esc_attr__( 'Language Switcher', 'translatepress-multilingual' ); ?>" title="<?php echo esc_attr__( 'Language Switcher', 'translatepress-multilingual' ); ?>"></a></li>
                        <li><a href="<?php echo esc_url( admin_url('admin.php?page=trp-onboarding&step=autotranslation') ); ?>" class="trp-nav-onboarding-dot" aria-label="<?php echo esc_attr__( 'Automatic Translation', 'translatepress-multilingual' ); ?>" title="<?php echo esc_attr__( 'Automatic Translation', 'translatepress-multilingual' ); ?>"></a></li>
                        <li><a href="<?php echo esc_url( admin_url('admin.php?page=trp-onboarding&step=addons') ); ?>" class="trp-nav-onboarding-dot" aria-label="<?php echo esc_attr__( 'Enable Addons', 'translatepress-multilingual' ); ?>" title="<?php echo esc_attr__( 'Enable Addons', 'translatepress-multilingual' ); ?>"></a></li>
                        <li><a href="<?php echo esc_url( admin_url('admin.php?page=trp-onboarding&step=finish') ); ?>" class="trp-nav-onboarding-dot" aria-label="<?php echo esc_attr__( 'Finalize', 'translatepress-multilingual' ); ?>" title="<?php echo esc_attr__( 'Finalize', 'translatepress-multilingual' ); ?>"></a></li>
                    </ul>
                </nav>

                <div id="trp-header-items-wrapper">
                    <a class="trp-header-link" href="<?php echo esc_url( admin_url( 'options-general.php?page=translate-press' ) ); ?>"><span class="trp-header-item-text trp-primary-text"><?php esc_html_e( 'Exit Setup', 'translatepress-multilingual' ); ?></span></a>
                    <a id="trp-upgrade-now-button" class="trp-header-link" href="https://translatepress.com/pricing/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=header-upsell"><?php esc_html_e( 'Upgrade', 'translatepress-multilingual' ); ?></a>
                </div>
            </div>
            <div class="trp-onboarding-content">
                <?php
                $this->step_render();
                ?>
            </div>
        </div>

        <?php
        echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private function step_handle(){
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // all nonce verification happens inside each step class
            if ($this->step instanceof TRP_Onboarding_Step_Interface){
                $this->step->handle($_POST);
            }
        }
    }

    public function step_render(){
        if ($this->step instanceof TRP_Onboarding_Step_Interface){
            $this->step->render();
        } else {
            esc_html_e('Nothing here', 'translatepress-multilingual');
        }
    }


    public function remove_admin_notices(){
            remove_all_actions( 'admin_notices' );
            remove_all_actions( 'all_admin_notices' );
    }

    public function enqueue_scripts_and_styles(){
        wp_enqueue_style('trp-onboarding-style', TRP_PLUGIN_URL . 'assets/css/trp-onboarding-style.css', array(), TRP_PLUGIN_VERSION);
        wp_enqueue_script( 'trp-select2-lib-js', TRP_PLUGIN_URL . 'assets/lib/select2-lib/dist/js/select2.min.js', array( 'jquery' ), TRP_PLUGIN_VERSION );
        wp_enqueue_style( 'trp-select2-lib-css', TRP_PLUGIN_URL . 'assets/lib/select2-lib/dist/css/select2.min.css', array(), TRP_PLUGIN_VERSION );
        // Register and enqueue your script
        wp_enqueue_script('trp-onboarding-js', TRP_PLUGIN_URL . 'assets/js/trp-onboarding-script.js', array('jquery', 'trp-select2-lib-js'), TRP_PLUGIN_VERSION,true);

        // Localize the script with a variable
        $translation_array = array(
            'trp_secondary_languages' => apply_filters('trp_secondary_languages', 1),
        );
        wp_localize_script('trp-onboarding-js', 'trp_onboarding_vars', $translation_array);
    }
}