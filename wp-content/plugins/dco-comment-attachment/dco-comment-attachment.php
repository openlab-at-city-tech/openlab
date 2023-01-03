<?php
/**
 * DCO Comment Attachment
 *
 * @package DCO_Comment_Attachment
 * @author Denis Yanchevskiy
 * @copyright 2019
 * @license GPLv2+
 *
 * @since 1.0.0
 *
 * Plugin Name: DCO Comment Attachment
 * Plugin URI: https://denisco.pro/dco-comment-attachment/
 * Description: Allows your visitors to attach files with their comments
 * Version: 2.4.0
 * Author: Denis Yanchevskiy
 * Author URI: https://denisco.pro
 * License: GPLv2 or later
 * Text Domain: dco-comment-attachment
 */

defined( 'ABSPATH' ) || die;

define( 'DCO_CA_URL', plugin_dir_url( __FILE__ ) );
define( 'DCO_CA_PATH', plugin_dir_path( __FILE__ ) );
define( 'DCO_CA_BASENAME', plugin_basename( __FILE__ ) );
define( 'DCO_CA_VERSION', '2.4.0' );

require_once DCO_CA_PATH . 'includes/functions.php';

require_once DCO_CA_PATH . 'includes/back-compat.php';

require_once DCO_CA_PATH . 'includes/class-dco-ca-base.php';
require_once DCO_CA_PATH . 'includes/class-dco-ca.php';

require_once DCO_CA_PATH . 'includes/class-dco-ca-admin.php';
require_once DCO_CA_PATH . 'includes/class-dco-ca-settings.php';

$GLOBALS['dco_ca']          = new DCO_CA();
$GLOBALS['dco_ca_admin']    = new DCO_CA_Admin();
$GLOBALS['dco_ca_settings'] = new DCO_CA_Settings();
