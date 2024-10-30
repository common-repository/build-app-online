<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://buildapp.online
 * @since      1.0.0
 *
 * @package    Build_App_Online
 * @subpackage Build_App_Online/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Build_App_Online
 * @subpackage Build_App_Online/includes
 * @author     Abdul Hakeem <hakeem.nala@gmail.com>
 */
class Build_App_Online {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Build_App_Online_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BUILD_APP_ONLINE_VERSION' ) ) {
			$this->version = BUILD_APP_ONLINE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'build-app-online';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_payments_hooks();

		//Booking Hooks
		$this->define_booking_hooks();

		//Booking Hooks
		$this->define_blog_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Build_App_Online_Loader. Orchestrates the hooks of the plugin.
	 * - Build_App_Online_i18n. Defines internationalization functionality.
	 * - Build_App_Online_Admin. Defines all hooks for the admin area.
	 * - Build_App_Online_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-build-app-online-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-build-app-online-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-build-app-online-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-public.php';

		/**
		 * The class responsible for defining all actions that occur in the multivendor of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-multivendor.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-payments.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-booking.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-build-app-online-blog-public.php';

		$this->loader = new Build_App_Online_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Build_App_Online_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Build_App_Online_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Build_App_Online_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'add_vendor_type_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'save_vendor_type_fields' );

		//$this->loader->add_action( 'init', $plugin_admin, 'handle_orgin' );

		//$this->loader->add_action( 'wpcf7_mail_sent', $plugin_admin, 'wpcf7_mail_sent', 10, 1 );

		//$this->loader->add_action( 'wcfm_after_enquiry_submit', $plugin_admin, 'wcfm_after_enquiry_submit', 10, 6 );

        $this->loader->add_action( 'woocommerce_new_order', $plugin_admin, 'neworder',  10, 1  );

        $this->loader->add_filter( 'woocommerce_thankyou_order_received_text', $plugin_admin, 'send_admin_and_vendor_push_notification', 199, 2 );

        $this->loader->add_action( 'save_post_product', $plugin_admin, 'save_new_post', 10, 3  );
        $this->loader->add_filter('wcml_load_multi_currency_in_ajax', $plugin_admin, 'loadCurrency', 20, 1);

        $this->loader->add_action('woocommerce_order_status_changed', $plugin_admin, 'order_status_changed', 10, 1  );

        $this->loader->add_filter('woocommerce_rest_product_object_query', $plugin_admin, 'mstoreapp_prepare_product_query', 10, 2);

        $this->loader->add_filter('woocommerce_rest_product_cat_query', $plugin_admin, 'remove_uncategorized_category', 10, 1);

        /* For All Multi Vendor */
        $this->loader->add_action('wp_ajax_build-app-online-upload_image', $plugin_admin, 'uploadimage');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-upload_image', $plugin_admin, 'uploadimage');

        $this->loader->add_action('wp_ajax_build-app-online-upload_images', $plugin_admin, 'uploadimages');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-upload_images', $plugin_admin, 'uploadimages');

        $this->loader->add_action('wp_ajax_build-app-online-new_chat_message', $plugin_admin, 'flutter_new_chat_message');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-new_chat_message', $plugin_admin, 'flutter_new_chat_message');

        $this->loader->add_action('wp_ajax_build-app-online-test', $plugin_admin, 'test');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-test', $plugin_admin, 'test');

        $this->loader->add_action('wp_ajax_build-app-online-site_details', $plugin_admin, 'site_details');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-site_details', $plugin_admin, 'site_details');

        $this->loader->add_action('wp_ajax_build-app-online-product_addons', $plugin_admin, 'get_product_addons');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-product_addons', $plugin_admin, 'get_product_addons');

        $this->loader->add_action('wp_ajax_app_save_options', $plugin_admin, 'app_save_options');
        $this->loader->add_action('wp_ajax_nopriv_app_save_options', $plugin_admin, 'app_save_options');

        $this->loader->add_action('wp_ajax_build-app-online-bao_options', $plugin_admin, 'app_get_options');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-bao_options', $plugin_admin, 'app_get_options');

        $this->loader->add_action('wp_ajax_build-app-online-pages', $plugin_admin, 'get_pages');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-pages', $plugin_admin, 'get_pages');

        $this->loader->add_action('wp_ajax_build-app-online-page', $plugin_admin, 'get_page');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-page', $plugin_admin, 'get_page');

        $this->loader->add_action('wp_ajax_build-app-online-groups', $plugin_admin, 'get_groups');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-groups', $plugin_admin, 'get_groups');

        $this->loader->add_action('wp_ajax_build-app-online-group', $plugin_admin, 'get_group');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-group', $plugin_admin, 'get_group');

        $this->loader->add_action('wp_ajax_build-app-online-save_options', $plugin_admin, 'app_save_options');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-save_options', $plugin_admin, 'app_save_options');

        $this->loader->add_action('wp_ajax_build-app-online-jwt_token', $plugin_admin, 'firebase_jwt_token');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-jwt_token', $plugin_admin, 'firebase_jwt_token');

        $this->loader->add_action('wp_ajax_build-app-online-update_user_metavalue', $plugin_admin, 'update_user_meta_value');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-update_user_metavalue', $plugin_admin, 'update_user_meta_value');

        $this->loader->add_action('wp_ajax_build-app-online-order_statuses', $plugin_admin, 'get_order_statuses');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-order_statuses', $plugin_admin, 'get_order_statuses');

        /* This is for WC Marketplace only */
        $this->loader->add_filter('wcmp_rest_prepare_dc_vendor_object', $plugin_admin, 'mstoreapp_prepare_vendors_query', 10, 3);

        /* For Dokan and WCFM Only */
        $this->loader->add_action('wp_ajax_build-app-online-update-vendor-product', $plugin_admin, 'update_vendor_product');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-update-vendor-product', $plugin_admin, 'update_vendor_product');

        /* For Dokan Only */
        $this->loader->add_filter('woocommerce_rest_prepare_product_object', $plugin_admin, 'mstoreapp_prepare_product', 10, 3);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Build_App_Online_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_filter( 'query_vars', $plugin_public, 'add_query_vars' );

		//$this->loader->add_filter( 'woocommerce_product_query_tax_query', $plugin_public, 'update_product_query_tax_query', 10, 2 );

		$this->loader->add_action('wp_ajax_build-app-online-add_all_products_cart', $plugin_public, 'add_all_products_cart');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-add_all_products_cart', $plugin_public, 'add_all_products_cart');

        $this->loader->add_action('wp_ajax_build-app-online-dotapp', $plugin_public, 'dotapp');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-dotapp', $plugin_public, 'dotapp');

        $this->loader->add_action('wp_ajax_build-app-online-keys', $plugin_public, 'keys');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-keys', $plugin_public, 'keys');

        $this->loader->add_action('wp_ajax_build-app-online-login', $plugin_public, 'login');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-login', $plugin_public, 'login');

        $this->loader->add_action('wp_ajax_build-app-online-cart', $plugin_public, 'cart');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-cart', $plugin_public, 'cart');

        $this->loader->add_action('wp_ajax_build-app-online-apply_coupon', $plugin_public, 'apply_coupon');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-apply_coupon', $plugin_public, 'apply_coupon');

        $this->loader->add_action('wp_ajax_build-app-online-test', $plugin_public, 'test');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-test', $plugin_public, 'test');

        $this->loader->add_action('wp_ajax_build-app-online-remove_coupon', $plugin_public, 'remove_coupon');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-remove_coupon', $plugin_public, 'remove_coupon');

        $this->loader->add_action('wp_ajax_build-app-online-update_shipping_method', $plugin_public, 'update_shipping_method');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-update_shipping_method', $plugin_public, 'update_shipping_method');

        $this->loader->add_action('wp_ajax_build-app-online-remove_cart_item', $plugin_public, 'remove_cart_item');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-remove_cart_item', $plugin_public, 'remove_cart_item');

        $this->loader->add_action('wp_ajax_build-app-online-get_checkout_form', $plugin_public, 'get_checkout_form');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-get_checkout_form', $plugin_public, 'get_checkout_form');

        $this->loader->add_action('wp_ajax_build-app-online-update_order_review', $plugin_public, 'update_order_review');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-update_order_review', $plugin_public, 'update_order_review');

        $this->loader->add_action('wp_ajax_build-app-online-add_to_cart', $plugin_public, 'add_to_cart');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-add_to_cart', $plugin_public, 'add_to_cart');

        $this->loader->add_action('wp_ajax_build-app-online-add_product_to_cart', $plugin_public, 'add_product_to_cart');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-add_product_to_cart', $plugin_public, 'add_product_to_cart');

        $this->loader->add_action('wp_ajax_build-app-online-payment', $plugin_public, 'payment');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-payment', $plugin_public, 'payment');

        $this->loader->add_action('wp_ajax_build-app-online-userdata', $plugin_public, 'userdata');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-userdata', $plugin_public, 'userdata');

        $this->loader->add_action('wp_ajax_build-app-online-json_search_products', $plugin_public, 'json_search_products');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-json_search_products', $plugin_public, 'json_search_products');

        $this->loader->add_action('wp_ajax_build-app-online-nonce', $plugin_public, 'nonce');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-nonce', $plugin_public, 'nonce');

        $this->loader->add_action('wp_ajax_build-app-online-passwordreset', $plugin_public, 'passwordreset');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-passwordreset', $plugin_public, 'passwordreset');

        $this->loader->add_action('wp_ajax_build-app-online-get_country', $plugin_public, 'get_country');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-get_country', $plugin_public, 'get_country');

        $this->loader->add_action('wp_ajax_build-app-online-get_wishlist', $plugin_public, 'get_wishlist');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-get_wishlist', $plugin_public, 'get_wishlist');

        $this->loader->add_action('wp_ajax_build-app-online-wishlistids', $plugin_public, 'get_wishlistids');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-wishlistids', $plugin_public, 'get_wishlistids');

        $this->loader->add_action('wp_ajax_build-app-online-update_wishlist', $plugin_public, 'update_wishlist');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-update_wishlist', $plugin_public, 'update_wishlist');

        //Delete
        $this->loader->add_action('wp_ajax_dotapp_get_wishlist', $plugin_public, 'fetch_wishlist');
        $this->loader->add_action('wp_ajax_nopriv_dotapp_get_wishlist', $plugin_public, 'fetch_wishlist');

        //Delete
        $this->loader->add_action('wp_ajax_build-app-online-remove_wishlist', $plugin_public, 'remove_wishlist');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-remove_wishlist', $plugin_public, 'remove_wishlist');

        $this->loader->add_action('wp_ajax_build-app-online-page_content', $plugin_public, 'pagecontent');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-page_content', $plugin_public, 'pagecontent');

        $this->loader->add_action('wp_ajax_build-app-online-set_fulfill_status', $plugin_public, 'set_fulfill_status');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-set_fulfill_status', $plugin_public, 'set_fulfill_status');

	    $this->loader->add_action('wp_ajax_build-app-online-logout', $plugin_public, 'logout');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-logout', $plugin_public, 'logout');

	    $this->loader->add_action('wp_ajax_build-app-online-emptyCart', $plugin_public, 'emptyCart');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-emptyCart', $plugin_public, 'emptyCart');

	    $this->loader->add_action('wp_ajax_build-app-online-update_user_notification', $plugin_public, 'update_user_notification');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-update_user_notification', $plugin_public, 'update_user_notification');

	    $this->loader->add_action('wp_ajax_build-app-online-email-otp', $plugin_public, 'email_otp');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-email-otp', $plugin_public, 'email_otp');

        $this->loader->add_action('wp_ajax_build-app-online-reset-user-password', $plugin_public, 'reset_user_password');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-reset-user-password', $plugin_public, 'reset_user_password');

        $this->loader->add_action('wp_ajax_build-app-online-create-user', $plugin_public, 'create_user');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-create-user', $plugin_public, 'create_user');

        $this->loader->add_action('wp_ajax_build-app-online-update-address', $plugin_public, 'update_address');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-update-address', $plugin_public, 'update_address');

        $this->loader->add_action('wp_ajax_build-app-online-get-states', $plugin_public, 'get_states');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-get-states', $plugin_public, 'get_states');

        $this->loader->add_action('wp_ajax_build-app-online-product-attributes', $plugin_public, 'product_attributes');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-product-attributes', $plugin_public, 'product_attributes');

        //$this->loader->add_action('wp_ajax_build-app-online-locations', $plugin_public, 'locations');
        //$this->loader->add_action('wp_ajax_nopriv_build-app-online-locations', $plugin_public, 'locations');

        $this->loader->add_action('wp_ajax_build-app-online-wallet', $plugin_public, 'get_wallet');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-wallet', $plugin_public, 'get_wallet');

        $this->loader->add_action('wp_ajax_build-app-online-woo_refund_key', $plugin_public, 'woo_refund_key');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-woo_refund_key', $plugin_public, 'woo_refund_key');

        $this->loader->add_action('wp_ajax_build-app-online-categories', $plugin_public, 'get_categories');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-categories', $plugin_public, 'get_categories');

        $this->loader->add_action('wp_ajax_build-app-online-products', $plugin_public, 'getProducts');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-products', $plugin_public, 'getProducts');

        $this->loader->add_action('wp_ajax_build-app-online-product', $plugin_public, 'getProduct');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-product', $plugin_public, 'getProduct');

        $this->loader->add_action('wp_ajax_build-app-online-orders', $plugin_public, 'getOrders');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-orders', $plugin_public, 'getOrders');

        $this->loader->add_action('wp_ajax_build-app-online-order', $plugin_public, 'getOrder');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-order', $plugin_public, 'getOrder');

        $this->loader->add_action('wp_ajax_build-app-online-customer', $plugin_public, 'getCustomerDetail');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-customer', $plugin_public, 'getCustomerDetail');

        $this->loader->add_action('wp_ajax_build-app-online-product_details', $plugin_public, 'getProductDetail');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-product_details', $plugin_public, 'getProductDetail');

        $this->loader->add_action('wp_ajax_build-app-online-product_reviews', $plugin_public, 'getProductReviews');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-product_reviews', $plugin_public, 'getProductReviews');

        $this->loader->add_action('wp_ajax_build-app-online-cancel_order', $plugin_public, 'cancel_order');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-cancel_order', $plugin_public, 'cancel_order');

        $this->loader->add_action('wp_ajax_build-app-online-update-cart-item-qty', $plugin_public, 'updateCartQty');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-update-cart-item-qty', $plugin_public, 'updateCartQty');

        $this->loader->add_action('wp_ajax_build-app-online-apply-vendor', $plugin_public, 'dokan_apply_for_vendor');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-apply-vendor', $plugin_public, 'dokan_apply_for_vendor');

        $this->loader->add_action('wp_ajax_build-app-online-checkout_form', $plugin_public, 'checkout_form');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-checkout_form', $plugin_public, 'checkout_form');

        $this->loader->add_filter( 'woocommerce_product_data_store_cpt_get_products_query', $plugin_public, 'handling_custom_meta_query_keys', 10, 3 );

        $this->loader->add_action('wp_ajax_build-app-online-downloads', $plugin_public, 'get_downloads');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-downloads', $plugin_public, 'get_downloads');

        $this->loader->add_action('wp_ajax_fcm_details', $plugin_public, 'fcm_details');
        $this->loader->add_action('wp_ajax_nopriv_fcm_details', $plugin_public, 'fcm_details');

        $this->loader->add_action('wp_ajax_build-app-online-store_details', $plugin_public, 'store_details');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-store_details', $plugin_public, 'store_details');

        $this->loader->add_action('wp_ajax_build-app-online-design_app_details', $plugin_public, 'design_app_details');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-design_app_details', $plugin_public, 'design_app_details');

        $this->loader->add_action('wp_ajax_build-app-online-taxonomy', $plugin_public, 'blocks_taxonomy');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-taxonomy', $plugin_public, 'blocks_taxonomy');

        $this->loader->add_action('wp_ajax_build-app-online-myblocks', $plugin_public, 'bao_my_blocks');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-myblocks', $plugin_public, 'bao_my_blocks');

        $this->loader->add_action('wp_ajax_build-app-online-block', $plugin_public, 'get_blocks');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-block', $plugin_public, 'get_blocks');

        $this->loader->add_action('wp_ajax_build-app-online-delete_my_account', $plugin_public, 'delete_my_account');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-delete_my_account', $plugin_public, 'delete_my_account');

        //$this->loader->add_filter('woocommerce_login_redirect', $plugin_public, 'wc_custom_user_redirect', 101, 3);

        //---REWARD POINTS--------/
        $this->loader->add_action('wp_ajax_build-app-online-ajax_maybe_apply_discount', $plugin_public, 'ajax_maybe_apply_discount');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-ajax_maybe_apply_discount', $plugin_public, 'ajax_maybe_apply_discount');
		
		$this->loader->add_action('wp_ajax_build-app-online-getPointsHistory', $plugin_public, 'getPointsHistory');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-getPointsHistory', $plugin_public, 'getPointsHistory');

        $plugin_multivendor = new Build_App_Online_Multivendor( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_filter( 'posts_clauses', $plugin_multivendor, 'mstoreapp_location_filter', 99, 2);
        
        $this->loader->add_action( 'pre_get_users', $plugin_multivendor, 'pre_get_users', 99, 1  );

        $this->loader->add_action('wp_ajax_build-app-online-vendor_details', $plugin_multivendor, 'get_vendor_details');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-vendor_details', $plugin_multivendor, 'get_vendor_details');

	    $this->loader->add_action('wp_ajax_build-app-online-vendors', $plugin_multivendor, 'get_vendors');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-vendors', $plugin_multivendor, 'get_vendors');

        $this->loader->add_action('wp_ajax_build-app-online-contact_vendor', $plugin_multivendor, 'contact_vendor');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-contact_vendor', $plugin_multivendor, 'contact_vendor');

        $this->loader->add_action('wp_ajax_build-app-online-add_vendor_review', $plugin_multivendor, 'add_vendor_review');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-add_vendor_review', $plugin_multivendor, 'add_vendor_review');

        $this->loader->add_action('wp_ajax_build-app-online-vendor_categories', $plugin_multivendor, 'getVendorCategories');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-vendor_categories', $plugin_multivendor, 'getVendorCategories');

        $this->loader->add_action('wp_ajax_build-app-online-store_info', $plugin_multivendor, 'store_info');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-store_info', $plugin_multivendor, 'store_info');

        $this->loader->add_action('wp_ajax_build-app-online-subscriptions', $plugin_public, 'my_subscriptions');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-subscriptions', $plugin_public, 'my_subscriptions');

	}

	private function define_payments_hooks() {

		$plugin_payments = new Build_App_Online_Payments( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('wp_ajax_dotapp_verify_payment', $plugin_payments, 'verify_payment');
        $this->loader->add_action('wp_ajax_nopriv_dotapp_verify_payment', $plugin_payments, 'verify_payment');

        $this->loader->add_action('wp_ajax_build-app-online_razorpay_order_id', $plugin_payments, 'get_razorpay_order_id');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online_razorpay_order_id', $plugin_payments, 'get_razorpay_order_id');

	}

	private function define_booking_hooks() {

		$plugin_booking = new Build_App_Online_Booking( $this->get_plugin_name(), $this->get_version() );

        //UnComment Only for Booking
        $this->loader->add_filter('woocommerce_rest_prepare_shop_order_object', $plugin_booking, 'mstoreapp_prepare_order', 10, 3);

	    $this->loader->add_action('wp_ajax_build-app-online-get_booking', $plugin_booking, 'get_booking');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-get_booking', $plugin_booking, 'get_booking');

        $this->loader->add_action('wp_ajax_build-app-online-create_booking', $plugin_booking, 'create_booking');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-create_booking', $plugin_booking, 'create_booking');

        $this->loader->add_action('wp_ajax_build-app-online-user_booking', $plugin_booking, 'get_user_booking');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-user_booking', $plugin_booking, 'get_user_booking');

	}

	private function define_blog_hooks() {

		$plugin_blog = new Build_App_Online_Blog_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('wp_ajax_build-app-online-blog-posts', $plugin_blog, 'get_posts');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-posts', $plugin_blog, 'get_posts');

        $this->loader->add_action('wp_ajax_build-app-online-blog-design_app_details', $plugin_blog, 'design_app_details');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-design_app_details', $plugin_blog, 'design_app_details');

		$this->loader->add_action('wp_ajax_build-app-online-blog-details', $plugin_blog, 'app_details');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-details', $plugin_blog, 'app_details');

        $this->loader->add_action('wp_ajax_build-app-online-blog-login', $plugin_blog, 'login');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-login', $plugin_blog, 'login');

        $this->loader->add_action( 'save_post', $plugin_blog, 'save_new_post', 10, 3  );

        $this->loader->add_action('wp_ajax_build-app-online-blog-site_details', $plugin_blog, 'site_details');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-site_details', $plugin_blog, 'site_details');

        $this->loader->add_action('wp_ajax_build-app-online-blog-save_options', $plugin_blog, 'app_save_options');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-save_options', $plugin_blog, 'app_save_options');

		$this->loader->add_action('wp_ajax_build-app-online-blog-fcm_details', $plugin_blog, 'fcm_details');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-fcm_details', $plugin_blog, 'fcm_details');

        $this->loader->add_action('wp_ajax_build-app-online-blog-get_bookmark', $plugin_blog, 'get_bookmark');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-get_bookmark', $plugin_blog, 'get_bookmark');

        $this->loader->add_action('wp_ajax_build-app-online-blog-bookmarkids', $plugin_blog, 'get_bookmarkids');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-bookmarkids', $plugin_blog, 'get_bookmarkids');

        $this->loader->add_action('wp_ajax_build-app-online-blog-update_bookmark', $plugin_blog, 'update_bookmark');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-update_bookmark', $plugin_blog, 'update_bookmark');

        $this->loader->add_action('wp_ajax_build-app-online-blog-myblocks', $plugin_blog, 'bao_my_blocks');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-myblocks', $plugin_blog, 'bao_my_blocks');

        $this->loader->add_action('wp_ajax_build-app-online-blog-block', $plugin_blog, 'get_blocks');
        $this->loader->add_action('wp_ajax_nopriv_build-app-online-blog-block', $plugin_blog, 'get_blocks');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Build_App_Online_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
