<?php
/*
 *  Widget dependent dropdowns
 */
add_action( 'wp_ajax_fetch_taxonomies', 'fetch_taxonomies' );
function fetch_taxonomies()
{
	$post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';
	$saved_settings_data = WPC_Primary_Category_Admin::get_saved_settings();
	$html = '';
	foreach ($saved_settings_data[$post_type] as $data)
	{
		$taxonomy = get_taxonomy($data);
		$get_meta_values = array_unique(WPC_Primary_Category_Admin::get_meta_values( 'primary_'.$taxonomy->name ));
		if($get_meta_values){
			$html .= '<option value="'.$taxonomy->name.'">'.$taxonomy->label.'</option>';
		}
	}
	echo $html;
	die();
}

add_action( 'wp_ajax_fetch_taxonomies_ids', 'fetch_taxonomies_ids' );
function fetch_taxonomies_ids()
{
	$taxonomy = isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
	$get_meta_values = array_unique(WPC_Primary_Category_Admin::get_meta_values( 'primary_'.$taxonomy ));
	$html = '';
	foreach ($get_meta_values as $data)
	{
		$taxnomy_detail_obj = get_term_by('id', $data, $taxonomy);

		$html .= '<option value="'.$taxnomy_detail_obj->term_id.'">'.$taxnomy_detail_obj->name.'</option>';
	}
	echo $html;
	die();

}