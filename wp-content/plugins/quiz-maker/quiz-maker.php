<?php
ob_start();
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://ays-pro.com/
 * @since             3.0.0
 * @package           Quiz_Maker
 *
 * @wordpress-plugin
 * Plugin Name:       Quiz Maker
 * Plugin URI:        http://ays-pro.com/index.php/wordpress/quiz-maker
 * Description:       Create powerful and engaging quizzes, tests, and exams in minutes. Build an unlimited number of quizzes and questions.
 * Version:           21.7.7
 * Author:            Quiz Maker team
 * Author URI:        http://ays-pro.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quiz-maker
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AYS_QUIZ_NAME_VERSION', '21.7.7' );
define( 'AYS_QUIZ_VERSION', '21.7.7' );
define( 'AYS_QUIZ_NAME', 'quiz-maker' );

if( ! defined( 'AYS_QUIZ_BASENAME' ) )
    define( 'AYS_QUIZ_BASENAME', plugin_basename( __FILE__ ) );

if( ! defined( 'AYS_QUIZ_DIR' ) )
    define( 'AYS_QUIZ_DIR', plugin_dir_path( __FILE__ ) );

if( ! defined( 'AYS_QUIZ_BASE_URL' ) ) {
    define( 'AYS_QUIZ_BASE_URL', plugin_dir_url(__FILE__ ) );
}
if( ! defined( 'AYS_QUIZ_ADMIN_PATH' ) )
    define( 'AYS_QUIZ_ADMIN_PATH', plugin_dir_path( __FILE__ ) . 'admin' );

if( ! defined( 'AYS_QUIZ_ADMIN_URL' ) )
    define( 'AYS_QUIZ_ADMIN_URL', plugin_dir_url( __FILE__ ) . 'admin' );

if( ! defined( 'AYS_QUIZ_PUBLIC_PATH' ) )
    define( 'AYS_QUIZ_PUBLIC_PATH', plugin_dir_path( __FILE__ ) . 'public' );

if( ! defined( 'AYS_QUIZ_PUBLIC_URL' ) )
    define( 'AYS_QUIZ_PUBLIC_URL', plugin_dir_url( __FILE__ ) . 'public' );


$wp_upload_dir = wp_upload_dir();
$wp_upload_basedir = $wp_upload_dir['basedir'];
$wp_upload_baseurl = $wp_upload_dir['baseurl'];

if( ! defined( 'AYS_QUIZ_CERTIFICATES_SAVE_PATH' ) ){
    define( 'AYS_QUIZ_CERTIFICATES_SAVE_PATH', $wp_upload_basedir . '/' . AYS_QUIZ_NAME . '/certificates' );
}
if( ! defined( 'AYS_QUIZ_CERTIFICATES_SAVE_URL' ) ){
    define( 'AYS_QUIZ_CERTIFICATES_SAVE_URL', $wp_upload_baseurl . '/' . AYS_QUIZ_NAME . '/certificates' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-quiz-maker-activator.php
 */
function activate_quiz_maker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quiz-maker-activator.php';
	Quiz_Maker_Activator::ays_quiz_update_db_check();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-quiz-maker-deactivator.php
 */
function deactivate_quiz_maker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quiz-maker-deactivator.php';
	Quiz_Maker_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_quiz_maker' );
register_deactivation_hook( __FILE__, 'deactivate_quiz_maker' );

add_action( 'plugins_loaded', 'activate_quiz_maker' );

add_option('ays_quiz_integrations', null);

if(get_option('ays_quiz_rate_state') === false){
    add_option( 'ays_quiz_rate_state', 0 );
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-quiz-maker.php';
require plugin_dir_path( __FILE__ ) . 'quiz/quiz-maker-block.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_quiz_maker() {
    // add_action( 'activated_plugin', 'quiz_maker_activation_redirect_method' );
    add_action( 'admin_notices', 'quiz_maker_general_admin_notice' );
	$plugin = new Quiz_Maker();
	$plugin->run();

}

function quiz_maker_activation_redirect_method( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'admin.php?page=' . AYS_QUIZ_NAME ) ) );
    }
}

function quiz_maker_general_admin_notice(){
    global $wpdb;
    if ( isset($_GET['page']) && strpos($_GET['page'], AYS_QUIZ_NAME) !== false ) {
        ?>
         <div class="ays-notice-banner">
            <div class="navigation-bar">
                <div id="navigation-container">
                    <div class="logo-container">
                        <a href="https://ays-pro.com/wordpress/quiz-maker" target="_blank" style="display: inline-block;box-shadow: none;">
                            <img  class="quiz-logo" src="<?php echo plugin_dir_url( __FILE__ ) . 'admin/images/icons/quiz-maker-logo.png'; ?>" alt="<?php echo __( "Quiz Maker", AYS_QUIZ_NAME ); ?>" title="<?php echo __( "Quiz Maker", AYS_QUIZ_NAME ); ?>"/>
                        </a>
                    </div>
                    <ul id="menu">
                        <li class="modile-ddmenu-lg"><a class="ays-btn" href="https://quiz-plugin.com/wordpress-quiz-plugin-pro-demo/" target="_blank"><?php echo __( "Demo", AYS_QUIZ_NAME ); ?></a></li>
                        <li><a class="ays-btn" href="https://wordpress.org/support/plugin/quiz-maker/" target="_blank"><?php echo __( "Support Forum", AYS_QUIZ_NAME ); ?></a></li>
<!--                        <li class="modile-ddmenu-xs take_survay"><a class="ays-btn" href="http://bit.ly/2tLbooO" target="_blank">Take a survey</a></li>-->
                        <li class="modile-ddmenu-lg"><a class="ays-btn" href="https://quiz-plugin.com/upcoming-quiz-maker-features" target="_blank"><?php echo __( "Upcoming Features", AYS_QUIZ_NAME ); ?></a></li>
                        <li class="modile-ddmenu-xs take_survay"><a class="ays-btn" href="https://ays-demo.com/questionnaire-about-quiz-maker-plugin/" target="_blank"><?php echo __( "Grab your GIFT", AYS_QUIZ_NAME ); ?></a></li>
                        <li class="modile-ddmenu-lg"><a class="ays-btn" href="https://ays-pro.com/contact" target="_blank"><?php echo __( "Contact us", AYS_QUIZ_NAME ); ?></a></li>
                        <li class="modile-ddmenu-md">
                            <a class="toggle_ddmenu" href="javascript:void(0);"><i class="ays_fa ays_fa_ellipsis_h"></i></a>
                            <ul class="ddmenu" data-expanded="false">
                                <li><a class="ays-btn" href="https://quiz-plugin.com/wordpress-quiz-plugin-pro-demo/" target="_blank"><?php echo __( "Demo", AYS_QUIZ_NAME ); ?></a></li>
                                 <li><a class="ays-btn" href="https://quiz-plugin.com/upcoming-quiz-maker-features" target="_blank"><?php echo __( "Upcoming Features", AYS_QUIZ_NAME ); ?></a></li>
                                <li><a class="ays-btn" href="https://ays-pro.com/contact" target="_blank"><?php echo __( "Contact us", AYS_QUIZ_NAME ); ?></a></li>
                            </ul>
                        </li>
                        <li class="modile-ddmenu-sm">
                            <a class="toggle_ddmenu" href="javascript:void(0);"><i class="ays_fa ays_fa_ellipsis_h"></i></a>
                            <ul class="ddmenu" data-expanded="false">
                                <li><a class="ays-btn" href="https://quiz-plugin.com/wordpress-quiz-plugin-pro-demo/" target="_blank"><?php echo __( "Demo", AYS_QUIZ_NAME ); ?></a></li>
<!--                                <li class="take_survay"><a class="ays-btn" href="http://bit.ly/2tLbooO" target="_blank">Take a survey</a></li>-->
                                <li><a class="ays-btn" href="https://quiz-plugin.com/upcoming-quiz-maker-features" target="_blank"><?php echo __( "Upcoming Features", AYS_QUIZ_NAME ); ?></a></li>
                                <li class="take_survay"><a class="ays-btn" href="https://ays-demo.com/questionnaire-about-quiz-maker-plugin/" target="_blank"><?php echo __( "Grab your GIFT", AYS_QUIZ_NAME ); ?></a></li>
                                <li><a class="ays-btn" href="https://ays-pro.com/contact" target="_blank"><?php echo __( "Contact us", AYS_QUIZ_NAME ); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
         </div>
         <?php
            $ays_quiz_rate = intval(get_option('ays_quiz_rate_state'));
            $sql = "SELECT COUNT(*) AS res_count FROM {$wpdb->prefix}aysquiz_reports";
            $results = $wpdb->get_row($sql, 'ARRAY_A');
            if (!is_null($results) && !empty($results)) {
                if(($results['res_count'] >= 5000) && ($ays_quiz_rate < 4)){
                    update_option('ays_quiz_rate_state', 4);
                    ays_quiz_rate_message(5000);
                }elseif(($results['res_count'] >= 1000) && ($ays_quiz_rate < 3)){                
                    update_option('ays_quiz_rate_state', 3);
                    ays_quiz_rate_message(1000);
                }elseif(($results['res_count'] >= 500) && ($ays_quiz_rate < 2)){                
                    update_option('ays_quiz_rate_state', 2);
                    ays_quiz_rate_message(500);
                }elseif(($results['res_count'] >= 100) && ($ays_quiz_rate < 1)){                
                    update_option('ays_quiz_rate_state', 1);
                    ays_quiz_rate_message(100);
                }
            }
    }
}
    
function ays_quiz_rate_message($count){
     ?>
     <div class="quiz_toast__container">
        <div class="quiz_toast__cell">
            <div class="quiz_toast quiz_toast--red">
                <div class="quiz_toast__main">
                    <div class="quiz_toast__icon">
                        <svg version="1.1" class="quiz_toast__svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 301.691 301.691" style="enable-background:new 0 0 301.691 301.691;" xml:space="preserve">
                            <g>
                                <polygon points="119.151,0 129.6,218.406 172.06,218.406 182.54,0  "></polygon>
                                <rect x="130.563" y="261.168" width="40.525" height="40.523"></rect>
                            </g>
                        </svg>
                    </div>
                    <div class="quiz_toast__content">
                        <p class="quiz_toast__type">
                            <?php 
                                echo __('Wow!!! Excellent job!! Your quizzes was passed by more than '.$count.' people!!', AYS_QUIZ_NAME);
                            ?>
                        </p>
                        <p class="quiz_toast__message">
                            <?php echo sprintf( '<span>%s</span> <a class="quiz_toast__rate_button" href="https://wordpress.org/support/plugin/quiz-maker/reviews/" target="_blank">%s</a>', 'Satisfied with our Quiz Maker plugin? It brings a lot of user to your website? Then it\'s time to rate us!! ', __('Rate Us', AYS_QUIZ_NAME)); ?>
                        </p>
                    </div>
                </div>
                <div class="quiz_toast__close">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.642 15.642" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 15.642 15.642">
                        <path fill-rule="evenodd" d="M8.882,7.821l6.541-6.541c0.293-0.293,0.293-0.768,0-1.061  c-0.293-0.293-0.768-0.293-1.061,0L7.821,6.76L1.28,0.22c-0.293-0.293-0.768-0.293-1.061,0c-0.293,0.293-0.293,0.768,0,1.061  l6.541,6.541L0.22,14.362c-0.293,0.293-0.293,0.768,0,1.061c0.147,0.146,0.338,0.22,0.53,0.22s0.384-0.073,0.53-0.22l6.541-6.541  l6.541,6.541c0.147,0.146,0.338,0.22,0.53,0.22c0.192,0,0.384-0.073,0.53-0.22c0.293-0.293,0.293-0.768,0-1.061L8.882,7.821z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <?php
}

run_quiz_maker();
