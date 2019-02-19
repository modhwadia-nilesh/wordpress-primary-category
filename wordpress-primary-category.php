<?php
/*
Plugin Name: WordPress Primary Category
Description: Allow publisher to designate primary category/taxonomy for posts and custom post types.
Version: 1.0
Author: Nilesh Modhwadia
Author URI: https://www.linkedin.com/in/nilesh-modhwadia-78153769/
Text Domain: wordpress-primary-category
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* === DEFINE === */
define( 'WPC_VERSION', '1.0' );
define( 'WPC_SLUG', 'wordpress-primary-category' );
define( 'WPC_FILE', __FILE__ );
define( 'WPC_PATH', plugin_dir_path(__FILE__) );
define( 'WPC_URL', plugins_url('/', __FILE__ ) );

if(!function_exists('WPC_Primary_Category'))
{
	function WPC_Primary_Category()
	{
		require_once WPC_PATH . 'includes/class-wpc.php';
		WPC_Primary_Category::instance();
	}
}

/*
 * Plugin loaded
 */
if(!function_exists('wpc_install'))
{
	function wpc_install(){
		WPC_Primary_Category();
	}
}
add_action('plugins_loaded', 'wpc_install');