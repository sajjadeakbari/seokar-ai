<?php
/**
 * Plugin Name:       SeoKar AI
 * Plugin URI:        https://sajjadakbari.ir/plugins/seokar-ai/
 * Description:       Leverage AI for SEO and content creation in WordPress.
 * Version:           0.3.0
 * Author:            Sajjad Akbari
 * Author URI:        https://sajjadakbari.ir/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       seokar-ai
 * Domain Path:       /languages
 * Requires at least: 5.5
 * Tested up to:      6.5
 * Requires PHP:      7.4
 */

defined('ABSPATH') || exit;

// Define core constants
define('SEOKAR_AI_VERSION', '0.3.0');
define('SEOKAR_AI_PLUGIN_FILE', __FILE__);
define('SEOKAR_AI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SEOKAR_AI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SEOKAR_AI_BASENAME', plugin_basename(__FILE__));
define('SEOKAR_AI_MIN_PHP_VERSION', '7.4');
define('SEOKAR_AI_MIN_WP_VERSION', '5.5');

// Autoloader implementation
require_once SEOKAR_AI_PLUGIN_DIR . 'includes/class-seokar-ai-autoloader.php';
SeoKar_AI_Autoloader::register();

// Initialize dependency checker
$dependency_checker = new SeoKar_AI\Includes\Utils\Dependency_Checker(
    SEOKAR_AI_MIN_PHP_VERSION,
    SEOKAR_AI_MIN_WP_VERSION
);

if (!$dependency_checker->check()) {
    add_action('admin_notices', [$dependency_checker, 'display_admin_notice']);
    return;
}

// Register activation/deactivation hooks
register_activation_hook(SEOKAR_AI_PLUGIN_FILE, ['SeoKar_AI\Includes\SeoKar_AI_Core', 'activate']);
register_deactivation_hook(SEOKAR_AI_PLUGIN_FILE, ['SeoKar_AI\Includes\SeoKar_AI_Core', 'deactivate']);

// Initialize the plugin
add_action('plugins_loaded', function() {
    $plugin = SeoKar_AI\Includes\SeoKar_AI_Core::get_instance();
    $plugin->run();
}, 5);
