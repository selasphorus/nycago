<?php
/**
 * @package NYCAGO
 */

/*
Plugin Name: NYC-AGO Custom Functionality
Plugin URI: 
Description: 
Version: 0.1
Author: atc
Author URI: 
License: 
Text Domain: nycago
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

$plugin_path = plugin_dir_path( __FILE__ );

/* +~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+ */

// Include sub-files
// TODO: make them required? Otherwise dependencies may be an issue.
// TODO: maybe: convert to classes/methods approach??
/* -- SEE SDG
$includes = array( 'posttypes', 'taxonomies' );

foreach ( $includes as $inc ) {
    $filepath = $plugin_path . 'inc/'.$inc.'.php'; 
    if ( file_exists($filepath) ) { include_once( $filepath ); } else { echo "no $filepath found"; }
}
*/
/* +~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+ */

// Enqueue scripts and styles -- WIP
//add_action( 'wp_enqueue_scripts', 'wpnycago_scripts_method' );
function wpnycago_scripts_method() {
    
    global $current_user;
    $current_user = wp_get_current_user();

}

// WIP
//add_action('wp_head', 'nycago_meta_tags');
function nycago_meta_tags() { 
    
    // Set defaults
    $og_url = "https://www.nycago.org/";
    $og_type = "website";
    //$og_title = "NYC-AGO";
    //$og_image = "https://www.nycago.org/wp-content/uploads/2022/XX/logo.png";
    $og_description = "NYC-AGO website";
    
    if ( is_page() || is_single() || is_singular() ) {
        
        $og_type = "article";
        $post_id = get_queried_object_id();
        $og_url = get_the_permalink( $post_id );
        $og_title = get_the_title( $post_id );
        
        // Get the featured image URL, if there is one
        if ( get_the_post_thumbnail_url( $post_id ) ) { $og_image = get_the_post_thumbnail_url( $post_id ); }
        
        // Get and clean up the excerpt for use in the description meta tag
        $excerpt = get_the_excerpt( $post_id );
        $excerpt = str_replace('&nbsp;Read more...','...',$excerpt); // Remove the "read more" tag from auto-excerpts
        $og_description = wp_strip_all_tags( $excerpt, true );
        
    }

    echo '<meta property="og:url" content="'.$og_url.'" />';
    echo '<meta property="og:type" content="'.$og_type.'" />';
    echo '<meta property="og:title" content="'.$og_title.'" />';
    echo '<meta property="og:image" content="'.$og_image.'" />';
    echo '<meta property="og:description" content="'.$og_description.'" />';
    //fb:app_id
    
}

// Function to extract HTML content from file saved to Media Library
function extract_html_local ( $attachment_id ) {

	$html_content = "";
	
	/*
	// @var array|WP_Error $response
	$response = wp_remote_get( 'http://www.example.com/index.html' );

	if ( is_array( $response ) && ! is_wp_error( $response ) ) {
		$headers = $response['headers']; // array of http header lines
		$body    = $response['body']; // use the content
	}
	*/
	
	return $html_content;
	
}

// Function to extract HTML content from url
add_shortcode( 'show_file_content', 'extract_html' );
function extract_html ( $atts = [] ) {

	$args = shortcode_atts( 
        array(
            'url'   => null
        ), $atts );
	$url = $args['url'];
	
	$html_content = "";
	
	if ( ! empty($url) ) { 
	
		// TODO: check to make sure it's an AGO url before proceeding
		
		// @var array|WP_Error $response
		$response = wp_remote_get( $url);

		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$headers = $response['headers']; // array of http header lines
			$body = $response['body']; // use the content
			
			$html_content .= "<pre>";
			$html_content .= print_r($headers, true);
			$html_content .= "</pre>";
			$html_content .= $body;
			
		}
	}
	
	return $html_content;
	
}

// Function to process newsletters to add content from old html
add_shortcode( 'process_newsletters', 'process_newsletters' );
function process_newsletters ( $atts = [] ) {

	$current_user = wp_get_current_user();
    if ( $current_user->user_login != 'birdhive@gmail.com' ) {
    	return "You are not authorized to run this operation.<br />";    
    }
    
	$info = "";
    $indent = "&nbsp;&nbsp;&nbsp;&nbsp;";

	$a = shortcode_atts( array(
        'testing' => true,
        'verbose' => false,
        'id' => null,
        'year' => date('Y'),
        'num_posts' => 10,
        'admin_tag_slug' => 'programmatically-updated',
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_key' => null
    ), $atts );
	
    $testing = $a['testing'];
    $verbose = $a['verbose'];
    $num_posts = (int) $a['num_posts'];
    $year = get_query_var( 'y' );
    if ( $year == "" ) { $year = $a['year']; } //$year = get_query_var( 'year' ) ? get_query_var( 'year' ) : $a['year'];
    $orderby = $a['orderby'];
    $order = $a['order'];
    $meta_key = $a['meta_key'];
    $admin_tag_slug = $a['admin_tag_slug'];
    
    // Set up the WP query args
	$args = array(
		'post_type' => 'newsletter',
		'post_status' => 'publish',
        'posts_per_page' => $num_posts,
        'meta_query' => array(
            //'relation' => 'AND',
            array(
                'key'   => "old_site_url", 
                'compare' => 'LIKE',
                'value' => 'html',
            ),
            /*array(
                'key'   => "date_calculation",
                'compare' => 'EXISTS'
            )*/
        ),
        'orderby'	=> $orderby,
        'order'	=> $order,
	);
    
    //if ( $a['id'] !== null ) { $args['p'] = $a['id']; }
    if ( $a['id'] !== null ) { $args['post__in'] = explode(', ', $a['id']); }
    if ( $a['meta_key'] !== null ) { $args['meta_key'] = $meta_key; }
	
	// Run the query
	$arr_posts = new WP_Query( $args );
    $posts = $arr_posts->posts;
    
    $info .= ">>> process_newsletters <<<<br />";
    $info .= "testing: $testing; verbose: $verbose; orderby: $orderby; order: $order; meta_key: $meta_key; ";
    //$info .= "year: $year<br />";
    $info .= "[num posts: ".count($posts)."]<br />";
    //$info .= "args: <pre>".print_r( $args, true )."</pre>";
    $info .= "<!-- args: <pre>".print_r( $args, true )."</pre> -->";
    //$info .= "Last SQL-Query: <pre>{$arr_posts->request}</pre><br />"; // tft
    $info .= "<br />";
    
    foreach ( $posts AS $post ) {
        
        setup_postdata( $post );
        $post_id = $post->ID;
        $post_title = $post->post_title;
        $slug = $post->post_name;
        $info .= '<span class="label">['.$post_id.'] "'.$post_title.'"</span><br />';
    	
        // init
        $calc_info = "";
        
        $changes_made = false;
        
        // Extract html 
        $html_content = "";
		$url = get_post_meta( $post_id, 'old_site_url', true );
		
		if ( ! empty($url) ) { 
	
			$info .= "url: ".$url."<br />";
			$ext = pathinfo($url,PATHINFO_EXTENSION);
			$info .= "ext: ".$ext."<br />";
			
			// TODO: check to make sure it's an AGO url before proceeding
			
			// Proceed only if this is an html url, not a link to a PDF file
			if ( $ext == "html" ) { 
			
				// @var array|WP_Error $response
				$response = wp_remote_get( $url);

				if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			
					$headers = $response['headers']; // array of http header lines
					$body = $response['body']; // use the content
			
					$html_content .= "<pre>";
					$html_content .= print_r($headers, true);
					$html_content .= "</pre>";
					//$html_content .= $body;
					
					// WIP/TODO: deal w/ relative URLs; import images into Media Library...
					
					// stylesheets
					// urls
					# Does the line contain any hyperlinks? If so, extract the filename or link(s).
            		//if ( $line =~ /<a href=\"([^>\"]*)\"/ || $line =~ /<a name=\"([A-Za-z0-9]+)\"/) {} //pl
            		
					// image tags
					# Does the line contain any images? If so, extract and store the file info, alt tag...
            		//if ( $line =~ /(<img[^>]*src=[^>]*>)/ ) {} // pl
            		// Find all the image tags in the post content
    				preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $body, $images);
    				$info .= "Images:<br />";
    				foreach ( $images as $image ) {
    					$info .= "<pre>".print_r($image, true)."</pre>";
    				}
			
				}			
			}
		}
		
		$info .= $html_content;
        
        // Save content (only if previously empty)
	
        
        //$info .= $calc_info;
        $info .= "<br />";
             
    } // END foreach post

	
	return $info;
	
}


?>