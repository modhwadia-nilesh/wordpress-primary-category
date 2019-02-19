<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!class_exists('WPC_Primary_Category_Admin'))
{
	class WPC_Primary_Category_Settings
	{

		/**
		 * Init category settings
		 */
		public function init()
		{
			add_action( 'admin_menu', array(__CLASS__, 'register_admin_panel') );
		}

		/**
		 * Add settings page
		 */
		public function register_admin_panel()
		{
			add_options_page( 'Primary Category', 'Primary Category', 'manage_options', 'wpc-options', array(__CLASS__, 'admin_panel_options') );
		}

		/**
		 * WPC settings page
		 */
		function admin_panel_options()
		{
			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}
			self::update_posted_data();
			require_once WPC_PATH . 'templates/admin/wpc-settings.php';
		}

		/**
		 *  Save WPC settings.
		 */
		public function update_posted_data()
		{
			if (isset( $_POST['wpc_settings_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['wpc_settings_nonce'] ), 'wpc-settings-options' ))
			{
				$posted_data = wp_unslash( $_POST['wpc_primary_categories'] );
				update_option( 'wpc_primary_categories', $posted_data, 'yes' );

				echo '<p>Settings Updated.</p>';
			}
		}

	}
}