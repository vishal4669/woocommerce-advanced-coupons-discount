<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://broadshoppy.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Advanced_Coupons_Discount
 * @subpackage Woocommerce_Advanced_Coupons_Discount/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Advanced_Coupons_Discount
 * @subpackage Woocommerce_Advanced_Coupons_Discount/public
 * @author     BroadshoppyAuthor <wordpress.divine@gmail.com>
 */
class Woocommerce_Advanced_Coupons_Discount_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_filter('woocommerce_coupon_get_discount_amount', array($this, 'wacd_filter_woocommerce_coupon_get_discount_amount'), 99, 5);
    }

    public function wacd_filter_woocommerce_coupon_get_discount_amount($discount, $discounting_amount, $cart_item, $single, $instance) {

        $coupon_id = $instance->get_id();
        if ($coupon_id > 0) {

            global $woocommerce;
            $coupon = new WC_Coupon($coupon_id);
            $get_cart_d = $this->get_wc_cart_data();
            $subtotal = $get_cart_d['subtotal'];
            $last_key = $get_cart_d['last_key'];
            $quantity_total = $get_cart_d['quantity_total'];
            $enable = get_post_meta($coupon_id, 'coupon_price_range_enable', true);
            $coupon_amount = get_post_meta($coupon_id, 'coupon_amount', true);
            $main_discount = ( $subtotal * $coupon_amount ) / 100;

            $max_cart_total = get_post_meta($coupon_id, 'max_cart_total', true);
            $min_cart_total = get_post_meta($coupon_id, 'min_cart_total', true);
            $max_cart_quantity = get_post_meta($coupon_id, 'max_cart_quantity', true);
            $min_cart_quantity = get_post_meta($coupon_id, 'min_cart_quantity', true);
            $wc_role = get_post_meta($coupon_id, 'wc_user_role', true);


            $item_total = $cart_item['line_subtotal'];
            $items = $woocommerce->cart->get_cart();
            if ($enable == 'yes' && $coupon->is_type('percent')) {            	
                $conditions = get_post_meta($coupon_id, 'coupon_price_range_data', true);
                if (!empty($conditions)) {
                    foreach ($conditions as $condition) {
                        $min = $condition['wacd_min_price'];
                        $max = $condition['wacd_max_price'];
                        $type = $condition['wacd_pricing_type'];
                        $wacd_discount = $condition['wacd_discount'];
                        if ($subtotal >= $min && ($max == '-1' || $subtotal <= $max )) {
                            if ($type == 'percent') {
                                $main_discount = ($subtotal * $wacd_discount) / 100;
                            	$discount = ( $item_total * $wacd_discount ) / 100;
                            	if ( $cart_item['key'] == $last_key  ) {
				            		$total_real_discount = 0;
							        foreach($items as $item => $values) {
							        	$item_price_total = $values['quantity'] * $values['data']->get_price();		
							        	$total_real_discount = round( $total_real_discount, 2 ) + round( ( $item_price_total * $wacd_discount ) / 100, 2 );
							        }
							        echo  $total_real_discount;
							        if( ( $wacd_discount - $total_real_discount ) > 0 ) {
							        	$discount = round( $discount, 2 ) + round( $main_discount - $total_real_discount, 2 );
							        }
				            	}      
                            } else {
                            	$discount_per = ( 100 * $wacd_discount ) / $subtotal;
                            	$main_discount = ($subtotal * $discount_per) / 100;
                            	$discount = ( $item_total * $discount_per ) / 100;
                            	if ( $cart_item['key'] == $last_key  ) {
				            		$total_real_discount = 0;
							        foreach($items as $item => $values) {
							        	$item_price_total = $values['quantity'] * $values['data']->get_price();		
							        	$total_real_discount = round( $total_real_discount, 2 ) + round( ( $item_price_total * $discount_per ) / 100, 2 );
							        }
							        echo  $total_real_discount;
							        if( ( $wacd_discount - $total_real_discount ) > 0 ) {
							        	$discount = round( $discount, 2 ) + round( $wacd_discount - $total_real_discount, 2 );
							        }
				            	}
                            }
                        }
                    }
                }
            }
            
        }
        if ( ! empty( $max_cart_total ) && $max_cart_total < $subtotal ) {
            WC()->cart->remove_coupons();
        }
        if ( ! empty( $min_cart_total ) && $min_cart_total > $subtotal ) {
           WC()->cart->remove_coupons();
        } 
        if ( ! empty( $max_cart_quantity ) && $max_cart_quantity < $quantity_total ) {
            WC()->cart->remove_coupons();
        }
        if ( ! empty( $min_cart_quantity ) && $min_cart_quantity > $quantity_total ) {
            WC()->cart->remove_coupons();
        }
        if ( $wc_role ) {
            $roles = $this->webby_get_current_user_role();
            if ( ! $roles || $roles != $wc_role ) {
                WC()->cart->remove_coupons();
            }
        }

        return $discount;
    }

    public function webby_get_current_user_role() {
        if( is_user_logged_in() ) {
            $user = wp_get_current_user();
            $role = ( array ) $user->roles;
            return $role[0];
        } else {
            return false;
        }
     }

    public function get_wc_cart_data() {
    	global $woocommerce;
    	$items = $woocommerce->cart->get_cart();
    	$price_total = 0;
        $quantity_total = 0;
    	$last_key = 0;
        foreach($items as $item => $values) { 
        	$last_key = $values['key'];
            $item_price_total = $values['quantity'] * $values['data']->get_price();
            $price_total +=$item_price_total;
            $quantity_total += $values['quantity'];
        }
        return array( 'subtotal' => $price_total, 'last_key' => $last_key, 'quantity_total' => $quantity_total );
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woocommerce_Advanced_Coupons_Discount_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woocommerce_Advanced_Coupons_Discount_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woocommerce-advanced-coupons-discount-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woocommerce_Advanced_Coupons_Discount_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woocommerce_Advanced_Coupons_Discount_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-advanced-coupons-discount-public.js', array('jquery'), $this->version, false);
    }

}
