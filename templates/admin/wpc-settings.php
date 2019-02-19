<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$not_allowed_post_types  = array( 'attachment' => 'attachment', 'page' => 'page' ); // Not allowed post types
$not_allowed_taxonomies = array( 'post_tag', 'post_format' ); // Not allowed taxonomies
$post_types = array_diff_key( get_post_types( array('public' => true), 'object', 'and' ), $not_allowed_post_types );
$get_saved_settings = WPC_Primary_Category_Admin::get_saved_settings(); // Get saved taxonomies data

?>
<form method="post" action="<?php echo esc_url( admin_url( 'options-general.php?page=wpc-options' ) ); ?>" id="wpc_form">
    <h1>WordPress Primary Category</h1>
    <p>To enable primary category feature, Select from below listed taxonomies for each post types.</p>

	<?php
	wp_nonce_field( 'wpc-settings-options', 'wpc_settings_nonce' );
	if ( $post_types && is_array( $post_types ) )
	{
		?>
        <table class="form-table">
            <tbody>
			<?php
			foreach ( $post_types as $post_type )
			{
				$post_taxonomies = get_object_taxonomies( $post_type->name, 'object' ); // Taxonomies object for post type

				if ( $post_taxonomies && is_array( $post_taxonomies ) )
				{

					echo '<h2>' . $post_type->label . '</h2>';

					foreach ( $post_taxonomies as $post_taxonomy )
					{

						$checked = '';
						if ( $get_saved_settings && array_key_exists( $post_type->name, $get_saved_settings ) )
						{
							if ( in_array( $post_taxonomy->name, $get_saved_settings[ $post_type->name ] ) )
							{
								$checked = 'checked';
							}
						}

						if ( in_array( $post_taxonomy->name, $not_allowed_taxonomies ) )
						{
							continue;
						}

						$taxonomy_title = $post_taxonomy->label ? $post_taxonomy->label : $post_taxonomy->name;

						$unique_id = 'wpc-primary-categories-' . $post_type->name . '-' . $post_taxonomy->name;

						echo '<div class="inside"><input type="checkbox" id="'. $unique_id .'" name="wpc_primary_categories[' . $post_type->name . '][]" value="' . $post_taxonomy->name . '" ' . $checked . '><label for="'.$unique_id.'">'.$taxonomy_title.'</label></div>';
					}

				}
			}
			?>
            </tbody>
        </table>

		<?php
	}
	?>
    <p class="submit"><input class="button-primary" type="submit" value="Save"></p>
</form>
