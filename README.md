# WordPress Primary Category

Allow publisher to designate primary category for posts and custom post types.

- [Installation](#installation)
- [Settings](#settings)
- [Shortcode](#shortcode)
- [Widgets](#widgets)
- [WP_Query](#wp_query)

## Installation

1. Copy the wordpress-primary-category folder into your wp-content/plugins folder
2. Activate the WordPress Primary Category plugin via the plugin admin page

## Settings

1. Open settings page.
    - Settings -> Primary Category
    - Plugins page -> Settings link in WordPress Primary Category
2. Select the taxonomies where you want to enable primary category feature and save it.

## Shortcode

List out the posts with selected parameters.

#### Parameters

```PHP
$posttype
    string $posttype
    default value: NULL
    required

$taxonomy
    string $taxonomy
    default value: NULL
    required   
    
$primary_taxonomy_id
    int $term_id
    default value: NULL
    required 
    
$per_page
    int $per_page
    default value: 6
    optional 
```

#### How to use

```PHP
[wpc_posts post_type="venue" taxonomy="location" primary_taxonomy_id="10" per_page="10"]
```

```PHP
[wpc_posts post_type="venue" taxonomy="location" primary_taxonomy_id="10"]
```

## Filter

**wpc_posts_html**    

To modify the default output of shorcode.

```PHP
string $html
array $query
```      
        
### How to use

```PHP
add_filter('wpc_posts_html', 'customize_output', 10, 2);
function customize_output( $html, $query ) {
	$html = '';
	if ( $query->have_posts() ) {
		$html .= '<ul>';
		while ( $query->have_posts() ) {
			$query->the_post();
			$html .= '<li><a href="' . get_the_permalink() . '" title="' . get_the_title() . '"> ' . get_the_title() . '</a></li>';
		}
		$html .= '</ul>';
	} else {
		$html .= 'No posts found with that critearea.';
	}

	return $html;
}

```

    
## Widgets

Two types of widgets are available.
  1. Primary Posts List (List the posts with selected parameters.)
  2. Primary Categoires List (List the active categories with selected parameters)
  
## WP_Query

**Get posts with specific taxonomy using meta query.**

```PHP
$args = array(
	'post_type' => 'venue',
	'meta_query' => array(
		array(
			'key'     => 'primary_location',
			'value'   => array( 5, 9 ),
			'compare' => 'IN',
		)
	),
);

$query = new WP_Query( $args );
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		// your code here
	}
}

```
 
