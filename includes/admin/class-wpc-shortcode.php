<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!class_exists('WPC_Primary_Category_Shortcode'))
{
	/**
	 * Class WPC_Primary_Category_Shortcode
	 */
	class WPC_Primary_Category_Shortcode
	{

		/**
		 * Init actions
		 */
		public function init()
		{
			add_shortcode( 'wpc_posts', array( __CLASS__, 'posts_shortcode' ) );
		}

		/**
		 * @param $atts
		 *
		 * @return mixed|string
		 *
		 * Generate shortcode with attributes
		 */
		public function posts_shortcode($atts)
		{
			$atts = shortcode_atts( array(
				'post_type' => '',
				'taxonomy'  => '',
				'primary_taxonomy_id'    => '',
				'per_page'    => '',
			), $atts, 'wpc_posts' );

			$get_saved_settings = WPC_Primary_Category_Admin::get_saved_settings();

			if(!isset($get_saved_settings[$atts['post_type']]) || (!in_array($atts['taxonomy'], $get_saved_settings[$atts['post_type']])))
			{
				return '';
			}

			$atts['per_page'] = $atts['per_page'] ? $atts['per_page'] : 6;
			$args = array(
				'post_type' => $atts['post_type'],
				'posts_per_page' => $atts['per_page'],
				'meta_query' => array(
					array(
						'key'     => 'primary_'.$atts['taxonomy'],
						'value'   => $atts['primary_taxonomy_id'],
						'compare' => '=',
					)
				)
			);

			$query = new WP_Query( $args );
			// todo : Allow theme override
			return include WPC_PATH . 'templates/frontend/posts-list.php';

		}
	}
}
