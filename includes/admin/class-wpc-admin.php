<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if(!class_exists('WPC_Primary_Category_Admin'))
{
	/**
	 * Class WPC_Primary_Category_Admin
	 */
	class WPC_Primary_Category_Admin
	{
		/**
		 *  Init actions
		 */
		public function init()
		{
			add_action( 'add_meta_boxes', array( __CLASS__, 'wpc_meta_box' ) );
			add_action( 'save_post', array( __CLASS__, 'wpc_save_data' ) );
		}

		/**
		 *  Add meta box admin side screen
		 */
		public function wpc_meta_box()
		{
			$saved_settings_data = self::get_saved_settings();

			if($saved_settings_data)
			{
				foreach ($saved_settings_data as $post_type => $taxonomies)
				{
					foreach ($taxonomies as $taxonomy)
					{
						$taxonomy_object = get_taxonomy( $taxonomy );

						$taxonomy_label = $taxonomy_object->labels->singular_name ? $taxonomy_object->labels->singular_name :
							($taxonomy_object->label ? $taxonomy_object->label : $taxonomy_object->name);

						add_meta_box (
							'primary_'.$taxonomy,
							'Primary '.$taxonomy_label,
							array( __CLASS__, 'wc_meta_box_content' ),
							$post_type,
							'side',
							'high',
							array('taxonomy' => $taxonomy)
						);
					}
				}
			}
		}

		/**
		 * @param $post
		 * @param $taxonomy
		 *
		 * Metabox select box to admin side screen
		 */
		public function wc_meta_box_content( $post, $taxonomy)
		{
			$taxonomy =  $taxonomy['args']['taxonomy'];
			$html = '';

			$post_terms = get_the_terms($post->ID, $taxonomy);

			$primary_category_id = get_post_meta($post->ID, 'primary_'.$taxonomy, true);

			if($post_terms)
			{
				$html .= '<select name="primary_'.$taxonomy.'" id="primary_'.$taxonomy.'"">';
				foreach( $post_terms as $term )
				{
					$selected = '';
					if($primary_category_id == $term->term_id)
					{
						$selected = 'selected';
					}
					$html .= '<option value="' . $term->term_id . '" '.$selected.'>' . $term->name . '</option>';
				}
				$html .= '</select>';
			}
			else
			{
				$html .= 'Please refresh page after saving/updating ' . $taxonomy . ' to set any one as primary';
			}
			echo $html;
		}

		/**
		 * Save the meta data
		 */
		public function wpc_save_data()
		{
			global $post;

			$saved_settings_data = self::get_saved_settings();



			foreach ($saved_settings_data as $key => $val)
			{
				foreach ( $val as $taxonomy )
				{
					$primary_category = sanitize_text_field( $_POST[ 'primary_'.$taxonomy ] );
					if ( isset( $_POST[ 'primary_'.$taxonomy ] ) && has_term($primary_category, $taxonomy, $post->ID))
					{
						update_post_meta( $post->ID, 'primary_'.$taxonomy , $primary_category );
					}
					else
					{
						delete_post_meta($post->ID, 'primary_'.$taxonomy);
					}
				}
			}
		}

		/**
		 * @return mixed
		 */
		public static function get_saved_settings()
		{
			return get_option( 'wpc_primary_categories' );
		}


		/**
		 * @param string $key
		 * @param string $status
		 *
		 * @return array
		 */
		public static function get_meta_values( $key = '', $status = 'publish' )
		{
			global $wpdb;
			$taxonomy_term_list = $wpdb->get_col( $wpdb->prepare( "
	            SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	            LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	            WHERE pm.meta_key = '%s' 
	            AND p.post_status = '%s' 
	        ", $key, $status ) );
			return $taxonomy_term_list;
		}
	}
}