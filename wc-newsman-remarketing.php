<?php
/**
 * Plugin Name: NewsmanApp Remarketing
 * Plugin URI: https://github.com/Newsman/WP-Plugin-NewsmanApp
 * Description: Allows Newsman Remarketing code to be inserted into WooCommerce store pages.
 * Author: Newsman
 * Author URI: https://newsman.com
 * Version: 1.8.1
 * WC requires at least: 2.1
 * WC tested up to: 4.1
 * License: GPLv2 or later
 * Text Domain: newsman-remarketing
 * Domain Path: languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Newsman_Remarketing' ) ) {

	/**
	 * Newsman Remarketing Integration main class.
	 */

	class WC_Newsman_Remarketing {

		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		const VERSION = '1.4.6';

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin.
		 */
		public function __construct() {
			//Allow non ecommerce pages
			if ( ! class_exists( 'WooCommerce' ) ) {
	
				return;
				
			}

			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'init', array( $this, 'show_ga_pro_notices' ) );

			// Checks with WooCommerce is installed.
			//if ( class_exists( 'WC_Integration' ) && defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '2.1-beta-1', '>=' ) ) {
			include_once 'includes/class-wc-newsman-remarketing.php';

			// Register the integration.
			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
			//	} else {
			//		add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			//	}

			//add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );
		}

		public function plugin_links( $links ) {
			$settings_url = add_query_arg(
				array(
					'page' => 'wc-settings',
					'tab' => 'Integration',
				),
				admin_url( 'admin.php' )
			);

			$plugin_links = array(
				'<a href="' . esc_url( $settings_url ) . '">' . __( 'Settings', 'wc-newsman-remarketing' ) . '</a>',
				'<a href="https://github.com/Newsman/WP-Plugin-NewsmanApp">' . __( 'Support', 'wc-newsman-remarketing' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @return void
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wc-newsman-remarketing' );

			load_textdomain( 'wc-newsman-remarketing', trailingslashit( WP_LANG_DIR ) . 'wc-newsman-remarketing/wc-newsman-remarketing-' . $locale . '.mo' );
			load_plugin_textdomain( 'wc-newsman-remarketing', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * WooCommerce fallback notice.
		 *
		 * @return string
		 */
		public function woocommerce_missing_notice() {
			echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Newsman Remarketing depends on the last version of %s to work!', 'wc-newsman-remarketing' ), '<a href="https://newsman.com/" target="_blank">' . __( 'WooCommerce', 'wc-newsman-remarketing' ) . '</a>' ) . '</p></div>';
		}

		/**
		 * Add a new integration to WooCommerce.
		 *
		 * @param  array $integrations WooCommerce integrations.
		 *
		 * @return array               Newsman Remarketing integration.
		 */
		public function add_integration( $integrations ) {
			$integrations[] = 'WC_Class_Newsman_Remarketing';

			return $integrations;
		}

		/**
		 * Logic for Google Analytics Pro notices.
		 */
		public function show_ga_pro_notices() {
			// Notice was already shown
		/*	if ( get_option( 'woocommerce_google_analytics_pro_notice_shown', false ) ) {
				return;
			} */

			$completed_orders = wc_orders_count( 'completed' );

			// Only show the notice if there are 10 <= completed orders <= 100.
			if ( ! ( 10 <= $completed_orders && $completed_orders <= 100 ) ) {
				return;
			}
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Newsman_Remarketing', 'get_instance' ), 0 );

}