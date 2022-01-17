<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://broadshoppy.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Advanced_Coupons_Discount
 * @subpackage Woocommerce_Advanced_Coupons_Discount/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Advanced_Coupons_Discount
 * @subpackage Woocommerce_Advanced_Coupons_Discount/admin
 * @author     BroadshoppyAuthor <wordpress.divine@gmail.com>
 */
class Woocommerce_Advanced_Coupons_Discount_Admin {

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;


        add_filter('woocommerce_coupon_data_tabs', array($this, 'wacd_coupon_data_tabs'));
        add_action('woocommerce_coupon_data_panels', array($this, 'wacd_coupon_data_panel'));
        add_action('woocommerce_coupon_options_save', array($this, 'wacd_coupon_data_save'));
        add_action('woocommerce_coupon_options', array($this, 'wacd_add_maximum_discount'), 10, 0);
    }

    public function wacd_coupon_data_tabs($tabs = array()) {

        // add auto-coupon tab to list
        $tabs['auto-coupon'] = array(
            'label' => __('Price Range', 'woocommerce'),
            'target' => 'coupon_price_range',
            'class' => 'coupon_price_range',
        );

        return $tabs;
    }

    public function wacd_coupon_data_panel() {
        ?>
        <div id="coupon_price_range" class="panel woocommerce_options_panel">
            <?php
            woocommerce_wp_checkbox(array(
                'id' => 'coupon_price_range_enable',
                'label' => __('Price Range', 'woocommerce'),
                'description' => __('Check this option if you wish this coupon to be applied when all conditions are met.', 'woocommerce')
            ));

            $post_id = $_REQUEST['post'];
            ?>

            <div class="wacd-repeater-title">
                <h4>Price Range Setting</h4>
            </div>
            <div class="wacd-repeater-head">
                <span class="head">Min Price</span>
                <span class="head">Max Price</span>
                <span class="head">Discount Type</span>
                <span class="head">Discount</span>
                <span class="head">Action</span>
            </div>
            <div class="wacd-repeater">
                <div data-repeater-list="coupon_price_range_data" class="coupon_price_range_data">
                    <?php
                    $conditions = get_post_meta($post_id, 'coupon_price_range_data', true);
                    if (!empty($conditions)) {
                        foreach ($conditions as $condition) {
                            ?>
                            <div data-repeater-item class="coupon_price_range_data-repeater">
                                <input placeholder="Min Price" class="wacd-min-price repeater-fields" type="number" name="wacd_min_price" min="1" value="<?php echo $condition['wacd_min_price']; ?>">
                                <input placeholder="Max Price" class="wacd-max-price repeater-fields" type="number" name="wacd_max_price" min="-1" value="<?php echo $condition['wacd_max_price']; ?>">
                                <select class="pricing-type wacd-pricing-type repeater-fields" name="wacd_pricing_type" >
                                    <?php
                                    $type_values = array(
                                        'Percentage discount' => 'percent',
                                        'Price discount' => 'fixed_cart',
                                    );
                                    $selected = $condition['wacd_pricing_type'];
                                    ?>
                                    <?php foreach ($type_values as $key => $value) :
                                        ?>
                                        <option value="<?php echo esc_attr($value); ?>" <?php echo ($selected == $value) ? 'selected' : ''; ?>><?php echo esc_attr($key); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input placeholder="Discount" class="wacd-discount repeater-fields" type="number" name="wacd_discount" min="1" value="<?php echo $condition['wacd_discount']; ?>">
                                
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div data-repeater-item class="coupon_price_range_data-repeater">
                            <input placeholder="Min Price" class="wacd-min-price repeater-fields" type="number" name="wacd_min_price" min="1">
                            <input placeholder="Max Price" class="wacd-max-price repeater-fields" type="number" name="wacd_max_price" min="-1">
                            <select class="pricing-type wacd-pricing-type repeater-fields" name="wacd_pricing_type" >
                                <option selected disabled>Select Type</option>
                                <?php
                                $type_values = array(
                                    'Percentage discount' => 'percent',
                                    'Price discount' => 'fixed_cart',
                                );
                                ?>
                                <?php foreach ($type_values as $key => $value) :
                                    ?>
                                    <option value="<?php echo esc_attr($value); ?>" ><?php echo esc_attr($key); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input placeholder="Discount" class="repeater-fields wacd-discount" type="number" name="wacd_discount" min="1">
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <input data-repeater-create type="button" class="button button-primary button-large add-quantity-range-price-field-btn wacd-add-new" value="ADD NEW" disabled="" />
                <p class="pro-note"><b>Note:</b> If you want to add more range buy pro version.</p>
            </div>
            <hr/>
            <div class="coupon_price_range_mail_body">
                <div class="seperator">
                    <h4>Send Mail : </h4>
                    <input type="checkbox" disabled="" name="price_range_mail_enable" />Check this option to notify below email for this coupon.
                </div>
                <p class="pro-note"><b>Note:</b> This option enable in pro version.</p>
                
            </div>
        </div>
        <?php
    }

    public function wacd_add_maximum_discount() {
        woocommerce_wp_text_input(array(
            'id' => 'maximum_discount',
            'label' => __('Maximum Limit Discount:', 'woocommerce'),
            'placeholder' => __('100', 'woocommerce'),
            'description' => __('This field allows you to set the maximum discount allowed to use the coupon. ( This option enable in pro verion. )', 'woocommerce'),
            'data_type' => 'price',
            'desc_tip' => false,
        ));
        woocommerce_wp_text_input(array(
            'id' => 'max_cart_total',
            'label' => __('Maximum Cart Total', 'woocommerce'),
            'placeholder' => __('100', 'woocommerce'),
            'description' => __('This field allows you to set the maximum cart total allowed to use the coupon.', 'woocommerce'),
            'data_type' => 'price',
            'desc_tip' => true,
        ));
        woocommerce_wp_text_input(array(
            'id' => 'min_cart_total',
            'label' => __('Minimum Cart Total', 'woocommerce'),
            'placeholder' => __('100', 'woocommerce'),
            'description' => __('This field allows you to set the minimum cart total allowed to use the coupon.', 'woocommerce'),
            'data_type' => 'price',
            'desc_tip' => true,
        ));   
        woocommerce_wp_text_input(array(
            'id' => 'max_cart_quantity',
            'label' => __('Maximum Cart Quantity', 'woocommerce'),
            'placeholder' => __('100', 'woocommerce'),
            'description' => __('This field allows you to set the maximum cart quantity allowed to use the coupon.', 'woocommerce'),
            'data_type' => 'price',
            'desc_tip' => true,
        ));
        woocommerce_wp_text_input(array(
            'id' => 'min_cart_quantity',
            'label' => __('Minimum Cart Quantity', 'woocommerce'),
            'placeholder' => __('100', 'woocommerce'),
            'description' => __('This field allows you to set the minimum cart quantity allowed to use the coupon.', 'woocommerce'),
            'data_type' => 'price',
            'desc_tip' => true,
        ));
     
         global $wp_roles;
     	$roles = $wp_roles->get_names();
		woocommerce_wp_select(array(
            'id' => 'wc_user_role',
            'label' => __('User Role', 'woocommerce'),
            'description' => __('This field allows you to set the user role allowed to use the coupon.', 'woocommerce'),
  			'options' => $roles,
            'desc_tip' => true,
        ));
        
    }

    public function wacd_coupon_data_save($post_id = null) {

        $coupon_price_range_enable = isset($_POST['coupon_price_range_enable']) ? 'yes' : 'no';
        update_post_meta($post_id, 'coupon_price_range_enable', $coupon_price_range_enable);

        $maximum_discount = $_POST['maximum_discount'];
        update_post_meta($post_id, 'maximum_discount', $maximum_discount);

        $max_cart_total = $_POST['max_cart_total'];
        update_post_meta($post_id, 'max_cart_total', $max_cart_total);

        $min_cart_total = $_POST['min_cart_total'];
        update_post_meta($post_id, 'min_cart_total', $min_cart_total);

        $max_cart_quantity = $_POST['max_cart_quantity'];
        update_post_meta($post_id, 'max_cart_quantity', $max_cart_quantity);

        $min_cart_quantity = $_POST['min_cart_quantity'];
        update_post_meta($post_id, 'min_cart_quantity', $min_cart_quantity);

        $coupon_price_range_data = $_POST['coupon_price_range_data'];
        update_post_meta($post_id, 'coupon_price_range_data', $coupon_price_range_data);
		
		$wc_user_role = $_POST['wc_user_role'];
        update_post_meta($post_id, 'wc_user_role', $wc_user_role);
        
        
    }

    /**
     * Register the stylesheets for the admin area.
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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woocommerce-advanced-coupons-discount-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
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
        wp_enqueue_script('wacd-repeater-jquery', plugin_dir_url(__FILE__) . 'js/jquery.repeater.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-advanced-coupons-discount-admin.js', array('jquery'), $this->version, true);
    }

}
