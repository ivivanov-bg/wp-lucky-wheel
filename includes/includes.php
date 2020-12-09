<?php
// no direct access allowed
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
define( 'VI_WP_LUCKY_WHEEL_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "wp-lucky-wheel" . DIRECTORY_SEPARATOR );
define( 'VI_WP_LUCKY_WHEEL_INCLUDES', VI_WP_LUCKY_WHEEL_DIR . "includes" . DIRECTORY_SEPARATOR );
define( 'VI_WP_LUCKY_WHEEL_ADMIN', VI_WP_LUCKY_WHEEL_DIR . "admin" . DIRECTORY_SEPARATOR );
define( 'VI_WP_LUCKY_WHEEL_FRONTEND', VI_WP_LUCKY_WHEEL_DIR . "frontend" . DIRECTORY_SEPARATOR );
$plugin_url = plugins_url( '', __FILE__ );
$plugin_url = str_replace( '/includes', '', $plugin_url );
define( 'VI_WP_LUCKY_WHEEL_CSS', $plugin_url . "/css/" );
define( 'VI_WP_LUCKY_WHEEL_JS', $plugin_url . "/js/" );
define( 'VI_WP_LUCKY_WHEEL_IMAGES', $plugin_url . "/images/" );

require_once VI_WP_LUCKY_WHEEL_INCLUDES . "data.php";
require_once VI_WP_LUCKY_WHEEL_INCLUDES . "functions.php";
require_once VI_WP_LUCKY_WHEEL_INCLUDES . "mobile_detect.php";
require_once VI_WP_LUCKY_WHEEL_INCLUDES . "support.php";
vi_include_folder( VI_WP_LUCKY_WHEEL_ADMIN, 'VI_WP_LUCKY_WHEEL_Admin_' );
vi_include_folder( VI_WP_LUCKY_WHEEL_FRONTEND, 'VI_WP_LUCKY_WHEEL_Frontend_' );