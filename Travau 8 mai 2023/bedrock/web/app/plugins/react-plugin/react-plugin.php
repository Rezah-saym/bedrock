<?php
/**
 * @package   Plugin react Wordpress
 * @author    Rezah Web Agency
 * @license   GPL-3.0
 * @link      https://www.linkedin.com/in/rezah-randrianarivony-945003177/
 *
 * Plugin Name: React Plugin [Custom]
 * Plugin URI: https://www.linkedin.com/in/rezah-randrianarivony-945003177/
 * Description: pluging v1
 * Author: Esokia
 * Version: 1.0.0
 * Author URI: https://www.linkedin.com/in/rezah-randrianarivony-945003177/
 * Text Domain: rzh_inovation
 * Domain Path: /languages.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define('RZH_PLUGIN_PATH', __DIR__);
define('RZH_PLUGIN_VERSION', '1.0.p64');
define('RZH_UPLOADS_DIR', wp_upload_dir()['basedir']);
define('RZH_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RZH_PLUGIN_ASSETS_URL', RZH_PLUGIN_URL.'/assets');
define('RZH_PLUGIN_IMAGES_URL', RZH_PLUGIN_ASSETS_URL.'/images');
define('RZH_PLUGIN_CSS_URL', RZH_PLUGIN_ASSETS_URL.'/css');
define('RZH_PLUGIN_JS_URL', RZH_PLUGIN_ASSETS_URL.'/js');

define('TWO_MONTHS', 60 * 60 * 24 * 30 * 2 );

define('PLUGIN_LOG_DIR', RZH_UPLOADS_DIR.'/plugin_log' );

require_once __dir__.'/vendor/autoload.php';

use Rezah\Bootstrap;
$boot = new Bootstrap();
register_activation_hook( __FILE__, [$boot, 'rzhActivation'] );
register_deactivation_hook( __FILE__, [$boot, 'rzhDesactivation'] );