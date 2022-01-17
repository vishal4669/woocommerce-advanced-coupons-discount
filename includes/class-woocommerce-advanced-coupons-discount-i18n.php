<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://broadshoppy.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Advanced_Coupons_Discount
 * @subpackage Woocommerce_Advanced_Coupons_Discount/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Advanced_Coupons_Discount
 * @subpackage Woocommerce_Advanced_Coupons_Discount/includes
 * @author     BroadshoppyAuthor <wordpress.divine@gmail.com>
 */
class Woocommerce_Advanced_Coupons_Discount_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-advanced-coupons-discount',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
