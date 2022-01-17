<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://broadshoppy.com
 * @since             1.0.0
 * @package           Woocommerce_Advanced_Coupons_Discount
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Advanced Coupons Discount
 * Plugin URI:        http://broadshoppy.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            BroadshoppyAuthor
 * Author URI:        http://broadshoppy.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-advanced-coupons-discount
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


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-advanced-coupons-discount-activator.php
 */
function activate_woocommerce_advanced_coupons_discount() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-advanced-coupons-discount-activator.php';
	Woocommerce_Advanced_Coupons_Discount_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-advanced-coupons-discount-deactivator.php
 */
function deactivate_woocommerce_advanced_coupons_discount() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-advanced-coupons-discount-deactivator.php';
	Woocommerce_Advanced_Coupons_Discount_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_advanced_coupons_discount' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_advanced_coupons_discount' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-advanced-coupons-discount.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_advanced_coupons_discount() {

	$plugin = new Woocommerce_Advanced_Coupons_Discount();
	$plugin->run();

}
run_woocommerce_advanced_coupons_discount();
