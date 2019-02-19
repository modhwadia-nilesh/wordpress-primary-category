<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 *  Posts list based on shortcode.
 */
$html = '';

if($query->have_posts())
{

	$html .= '<ul>';
	while ($query->have_posts())
	{
		$query->the_post();
		$html .= '<li><a href="'.get_the_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></li>';
	}
	$html .= '</ul>';
}
else
{
	$html .= 'No posts found with that criteria.';
}

/**
 * Posts html filter hook
 */
$html = apply_filters('wpc_posts_html', $html, $query);

return $html;