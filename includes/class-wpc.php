<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!class_exists('WPC_Primary_Category'))
{
	class WPC_Primary_Category
	{

		/**
		 * @var null
		 */
		protected static $_instance = null;


		/**
		 * @return WPC_Primary_Category|null
		 */
		public static function instance()
		{
			if(is_null(self::$_instance))
			{
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * WPC_Primary_Category constructor.
		 */
		public function __construct()
		{
			$this->includes();
			$this->hooks();
		}

		/**
		 * Plugin action hooks
		 */
		public function hooks()
		{
			add_action( 'wp_loaded', array( 'WPC_Primary_Category_Settings', 'init' ) );
			add_action( 'admin_init', array( 'WPC_Primary_Category_Admin', 'init' ) );
			add_action( 'init', array( 'WPC_Primary_Category_Shortcode', 'init' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );
			add_filter('plugin_action_links_wordpress-primary-category/wordpress-primary-category.php', array(__CLASS__, 'wpc_add_settings_link'));
		}

		/**
		 * Include required files
		 */
		public function includes()
		{
			require_once WPC_PATH . 'includes/admin/class-wpc-settings.php';
			require_once WPC_PATH . 'includes/admin/class-wpc-admin.php';
			require_once WPC_PATH . 'includes/admin/class-wpc-shortcode.php';
			require_once WPC_PATH . 'includes/admin/class-wpc-widget.php';
			require_once WPC_PATH . 'includes/admin/ajax-calls.php';
		}

		/**
		 * Plugin scripts
		 */
		public function register_scripts()
		{
			wp_enqueue_script( 'wpc_widgets', WPC_URL . 'assets/js/widgets.js' );
			wp_localize_script( 'wpc_widgets', 'WPC', array(
				'saved_settings_data' => (array) WPC_Primary_Category_Admin::get_saved_settings(),
				'ajax_url' => admin_url( 'admin-ajax.php' )
			) );
		}

		/**
		 * @param $links
		 *
		 * @return array
		 */
		function wpc_add_settings_link( $links )
		{
			$links[] = '<a href="' .admin_url( 'options-general.php?page=wpc-options' ) .'">Settings</a>';
			return $links;
		}

	}
}