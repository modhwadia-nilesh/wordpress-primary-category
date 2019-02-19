<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Register Widgets
 */
function wpc_load_primary_posts_lists()
{
	register_widget( 'wpc_primary_posts_lists' );
	register_widget( 'wpc_primary_categories_list' );
}
add_action( 'widgets_init', 'wpc_load_primary_posts_lists' );


/**
 * Class wpc_primary_posts_lists
 */
class wpc_primary_posts_lists extends WP_Widget
{

	/**
	 * wpc_primary_posts_lists constructor.
	 */
	function __construct()
	{
		parent::__construct(
			'wpc_primary_posts_lists',
			__('Primary Posts List'),
			array( 'description' => __( 'List out all posts with active primary category' ), )
		);
	}


	/**
	 * @param array $args
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function widget( $args, $instance )
	{
		$title = apply_filters( 'widget_title', $instance['title'] );

		if(empty($instance['post_type']) || empty($instance['taxonomy']) || empty($instance['taxonomy_id']))
        {
            return '';
        }

		$get_saved_settings = WPC_Primary_Category_Admin::get_saved_settings();

		if(!isset($get_saved_settings[$instance['post_type']]) || (!in_array($instance['taxonomy'], $get_saved_settings[$instance['post_type']])))
		{
			return '';
		}

		echo $args['before_widget'];

		if ( ! empty( $title ) )
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo do_shortcode('[wpc_posts post_type="'.$instance['post_type'].'" taxonomy="'.$instance['taxonomy'].'" primary_taxonomy_id="'.$instance['taxonomy_id'].'"]');

		echo $args['after_widget'];

	}


	/**
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance )
	{

		$title = $instance[ 'title' ];
		$post_type_instance = $instance[ 'post_type' ];
		$taxonomy_instance = $instance[ 'taxonomy' ];
		$taxonomy_id_instance = $instance[ 'taxonomy_id' ];

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>


		<?php
		/**
		 *  Post types select box
		 */
		$saved_settings_data = WPC_Primary_Category_Admin::get_saved_settings();
		if ( $saved_settings_data && is_array( $saved_settings_data ) )
		{
			echo '<p>';
			echo '<label for="'.$this->get_field_id( 'post_type' ).'">'. _e( 'Post Type:' ).'</label>';
			echo '<select class="widefat widget-posttypes" id="' . $this->get_field_name( 'post_type' ) . '" name="' . $this->get_field_name( 'post_type' ) . '">';
		    echo '<option>Select Post Type</option>';
			foreach ( $saved_settings_data as $post_type => $taxonomies )
			{
				$selected = '';
				$post_type_obj = get_post_type_object( $post_type );
				if($post_type_obj->name == $post_type_instance)
				{
					$selected = 'selected';
				}

				echo '<option value="' . $post_type_obj->name . '" '.$selected.'>' . $post_type_obj->label . '</option>';
			}
			echo '</select>';
			echo '</p>';
		}

		/**
		 * Taxonomies select box
		 */
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'taxonomy' ).'">'. _e( 'Taxonomy:' ).'</label>';
		echo '<select class="widefat widget-taxonomies" id="' . $this->get_field_name( 'taxonomy' ) . '" name="' . $this->get_field_name( 'taxonomy' ) . '">';

		if ( $saved_settings_data && is_array( $saved_settings_data ) )
		{
			foreach ($saved_settings_data[$post_type_instance] as $data)
			{
				$taxonomy_obj = get_taxonomy($data);
				$selected = '';
				if($taxonomy_obj->name == $taxonomy_instance)
				{
					$selected = 'selected';
				}
				echo '<option value="' . $taxonomy_obj->name . '" '.$selected.'>' . $taxonomy_obj->label . '</option>';
			}
		}

		echo '</select>';
		echo '</p>';

		/**
		 *  Taxonomy terms select box
		 */
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'taxonomy_id' ).'">'. _e( 'Taxonomy Term:' ).'</label>';
		echo '<select class="widefat widget-taxonomies-ids" id="' . $this->get_field_name( 'taxonomy_id' ) . '" name="' . $this->get_field_name( 'taxonomy_id' ) . '">';

		$get_meta_values = array_unique(WPC_Primary_Category_Admin::get_meta_values( 'primary_'.$taxonomy_instance ));

        foreach ($get_meta_values as $get_meta_value)
        {
            $taxonomy_detail_obj = get_term_by('id', $get_meta_value, $taxonomy_instance);

            $selected = '';
            if($taxonomy_id_instance == $taxonomy_detail_obj->term_id)
            {
                $selected = 'selected';
            }

            echo '<option value="' . $taxonomy_detail_obj->term_id . '" '.$selected.'>' . $taxonomy_detail_obj->name . '</option>';
        }

		echo '</select>';
		echo '</p>';

	}


	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['post_type'] = ( ! empty( $new_instance['post_type'] ) ) ? strip_tags( $new_instance['post_type'] ) : '';
		$instance['taxonomy'] = ( ! empty( $new_instance['taxonomy'] ) ) ? strip_tags( $new_instance['taxonomy'] ) : '';
		$instance['taxonomy_id'] = ( ! empty( $new_instance['taxonomy_id'] ) ) ? strip_tags( $new_instance['taxonomy_id'] ) : '';
		return $instance;
	}

}

/**
 * Class wpc_primary_categories_lists
 */
class wpc_primary_categories_list extends WP_Widget
{
	/**
	 * wpc_primary_categories_list constructor.
	 */
	function __construct()
	{
		parent::__construct(
			'wpc_primary_categories_list',
			__('Primary Categories List'),
			array( 'description' => __( 'List out all active primary categories' ), )
		);
	}


	/**
	 * @param array $args
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function widget( $args, $instance )
	{
		$title = apply_filters( 'widget_title', $instance['title'] );

		$get_saved_settings = WPC_Primary_Category_Admin::get_saved_settings();

		if(!isset($get_saved_settings[$instance[ 'post_type' ]]) || (!in_array($instance[ 'taxonomy' ], $get_saved_settings[$instance[ 'post_type' ]])))
		{
			return '';
		}

		echo $args['before_widget'];

		if ( ! empty( $title ) )
        {
	        echo $args['before_title'] . $title . $args['after_title'];
        }

        echo '<ul>';

        $get_meta_values = array_unique(WPC_Primary_Category_Admin::get_meta_values( 'primary_'.$instance[ 'taxonomy' ] ));

        foreach ($get_meta_values as $get_meta_value)
        {
            $taxonomy_detail_obj = get_term_by('id', $get_meta_value, $instance[ 'taxonomy' ]);

            echo '<li><a href="'.get_term_link($taxonomy_detail_obj->term_id).'"> ' . $taxonomy_detail_obj->name . '</a></li>';
        }

        echo '</ul>';

		echo $args['after_widget'];

	}

	/**
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance )
	{
		$title = $instance[ 'title' ];
		$post_type_instance = $instance[ 'post_type' ];
		$taxonomy_instance = $instance[ 'taxonomy' ];

		?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>


		<?php

		/**
		 * Post types select box
		 */
		$saved_settings_data = WPC_Primary_Category_Admin::get_saved_settings();
		if ( $saved_settings_data && is_array( $saved_settings_data ) )
		{
			echo '<p>';
			echo '<label for="'.$this->get_field_id( 'post_type' ).'">'. _e( 'Post Type:' ).'</label>';
			echo '<select class="widefat widget-posttypes" id="' . $this->get_field_name( 'post_type' ) . '" name="' . $this->get_field_name( 'post_type' ) . '">';
			echo '<option>Select Post Type</option>';

			foreach ( $saved_settings_data as $post_type => $taxonomies )
			{
				$selected = '';
				$post_type_obj = get_post_type_object( $post_type );
				if($post_type_obj->name == $post_type_instance)
				{
					$selected = 'selected';
				}

				echo '<option value="' . $post_type_obj->name . '" '.$selected.'>' . $post_type_obj->label . '</option>';
			}

			echo '</select>';
			echo '</p>';
		}

		/**
		 * Taxonomies select box
		 */
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'taxonomy' ).'">'. _e( 'Taxonomy:' ).'</label>';
		echo '<select class="widefat widget-taxonomies" id="' . $this->get_field_name( 'taxonomy' ) . '" name="' . $this->get_field_name( 'taxonomy' ) . '">';

        foreach ($saved_settings_data[$post_type_instance] as $data)
        {
            $taxonomy_obj = get_taxonomy($data);
            $selected = '';
            if($taxonomy_obj->name == $taxonomy_instance)
            {
                $selected = 'selected';
            }
            echo '<option value="' . $taxonomy_obj->name . '" '.$selected.'>' . $taxonomy_obj->label . '</option>';
        }

		echo '</select>';
		echo '</p>';

	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['post_type'] = ( ! empty( $new_instance['post_type'] ) ) ? strip_tags( $new_instance['post_type'] ) : '';
		$instance['taxonomy'] = ( ! empty( $new_instance['taxonomy'] ) ) ? strip_tags( $new_instance['taxonomy'] ) : '';
		return $instance;
	}
}
