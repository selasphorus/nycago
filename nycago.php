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
if ( ! is_admin() ) {
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	require_once(ABSPATH . 'wp-admin/includes/post.php' );
}
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
        'do_images' => false,
        'do_links' => false,
        'do_content' => false,
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
    //
    $do_images = $a['do_images'];
    $do_links = $a['do_links'];
    $do_content = $a['do_content'];
    //
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
    $info .= "testing: $testing; verbose: $verbose; orderby: $orderby; order: $order; meta_key: $meta_key;<br />";
    $info .= "do_images: $do_images; do_links: $do_links; do_content: $do_content;<br />";
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
        $info .= '<h2>['.$post_id.'] "'.$post_title.'"</h2>';
        //$info .= '<span class="label">['.$post_id.'] "'.$post_title.'"</span><br />';
    	
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
			
					$headers = $response['headers']; // array of http header lines -- protected object
					$body = $response['body']; // use the content
					$html_content = $body; // Make a copy -- this one we'll clean up, fix links &c.
					
					//$info .= "<pre>".print_r($headers, true)."</pre>";
					
					$html_last_modified = $headers['last-modified'];
					$info .= "html_last_modified: ".$html_last_modified."<br />";
					//$content_length = $headers['content-length'];
					
					if ( $do_links || $do_content ) { //
						// Process all hyperlinks found in the post content
						preg_match_all('/<.+href=[\'"]([^\'"]+)[\'"][^>]+>/i', $html_content, $links);
						$info .= "<h3>Links:</h3>";
						foreach ( $links[1] as $link ) {
					
							$info .= "link: ".$link."<br />";
						
							preg_match('/http/', $link, $tmp);
							if ( count($tmp) > 0 ) { 
								$new_link = null;
								//$info .= "http found via preg_match (not a relative link)<br />";
							} else {
								$new_link = "http://www.nycago.org".$link;
								$info .= ">> new_link: ".$new_link."<br />";
								// Replace old link w/ new in body
								$html_content = str_replace($link,$new_link,$html_content);
							}
							$info .= "---<br />";
						}
					}
            		
            		if ( $do_images || $do_content ) { //
            			// Process all image tags found in the post content
						preg_match_all('/<img.+src=[\'"][^\'"]+[\'"][^>]+>/i', $html_content, $images);
						$info .= "<h3>Images:</h3>";
						foreach ( $images[0] as $img ) {
						
							$img_info = "";
					
							$info .= '<div class="smaller">';
						
							// TODO: deal w/ width and height? probably not necessary. Any other attributes of concern?
						
							// Get img src
							if ( preg_match('/src=[\'"]([^\'"]+)[\'"]/', $img, $src) ) {
							
								$src = $src[1];
								//$info .= "src: ".$src."<br />";
							
								// Get file info
								$pathinfo = pathinfo($src);
								$dirname = $pathinfo['dirname'];
								$filename = $pathinfo['filename'];
								$basename = $pathinfo['basename'];
							
								//$info .= "dirname: ".$dirname."<br />";
								//$info .= "filename: ".$filename."<br />";
								//$info .= "basename: ".$basename."<br />";
							
								// Check to see if file is in Newsletter subfolder -- e.g. /Newsletter/1611_files/article_one_1.jpg
								// If so, make a new more specific filename -- e.g. NL1611-article_one_1.jpg
								if ( preg_match('/([0-9]+)_files/', $dirname, $nlid) ) {
								//if ( preg_match('/\/Newsletter\/([0-9]+)_files/', $dirname, $nlid) ) {
									$nlid = $nlid[1];
									//$info .= "nlid: ".$nlid."<br />";
									$new_name = "NL".$nlid."-".$filename;
									//$info .= "new_name: ".$new_name."<br />";
								} else {
									$new_name = $filename;
								}
							
								// Get img alt, if any
								// TODO: figure out how to add this to the attachment record
								/*if ( preg_match('/alt=[\'"]([^\'"]+)[\'"]/', $img, $alt) ) {
									$alt = trim($alt[1]);
									//$info .= "alt: ".$alt."<br />";
								}*/
								$title = $new_name;
							
								// Check if attachment already exists
								if ( $ml_img = post_exists( $title,'','','attachment') ) {
								
									$img_info .= "<strong>'".$title."' is already in the media library.</strong>";
									if ( $new_name ) { $img_info .= " ($filename/$new_name)"; }
									$img_info .= "<br />";
								
								} else {
								
									$img_info .= "<strong>'".$title."' is not yet in the media library.</strong>";
								
									// Turn the path into an absolute URL and attempt to add remote image to Media Library
									if ( !stripos($src,"http") ) {
										
										$img_url = "http://www.nycago.org";
										// Does the current source start with a slash? If not, add one
										if (!str_starts_with($src, '/')) {
											$img_url .= "/";
											if (!str_starts_with($src, 'Newsletter')) {
												$img_url .= "Newsletter/";
											}
										}
										$img_url .= $src;
										//$info .= "img_url: ".$img_url."<br />";
										$img_info .= " [$img_url]<br />";
						
										// Add image to media library
										$ml_img = media_sideload_image( $img_url, $post_id, $title, 'id' );
										if ( is_wp_error( $ml_img ) ) {
											$img_info .= '<span class="error">';
											$img_info .= "media_sideload_image error: ".$ml_img->get_error_message();
											$img_info .= '</span>';
											$img_info .= "<br />";
											$ml_img = null;
										} else {
											$info .= "Image added to Media Library. New attachment ID: ".$ml_img."<br />";
											$file = get_attached_file($ml_img);
											$path = pathinfo($file);
										
											// If we've got a new_name, update the new attachment accordingly
											if ( $new_name && $new_name != $filename ) {
												$newfile = $path['dirname']."/".$new_name.".".$path['extension'];
												$img_info .= "file: $file/newfile: $newfile<br />";
												rename($file, $newfile);    
												update_attached_file( $ml_img, $newfile );
											}
										
										}					
									}
								}
							
								if ( !empty($ml_img) && $do_content ) {
									$ml_src = wp_get_attachment_image_url($ml_img, 'full');
									// make it a relative link
									$ml_src = str_replace("https://samb71.sg-host.com","",$ml_src);
									$img_info .= "ml_src: ".$ml_src."<br />";
									// Replace old relative url with link to newly-uploaded image
									$html_content = str_replace($src,$ml_src,$html_content);
								}			
							
							}
							
							if ( $do_images ) { $info .= $img_info; }
							$info .= '</div>';
							//$info .= "+++<br />";
						}
            		}
            		
    				if ( $do_content ) {
    				
						// WIP -- Deal w/ anything that's not actually content/copy -- e.g. stylesheets
						// <title
						preg_match('/<title>(.*)<\/title>/i', $html_content, $title);
						$html_title = $title[1];
						preg_match_all('/<meta([^>]+)>/i', $html_content, $meta);
						preg_match_all('/<link.+href=[\'"]([^\'"]+)[\'"][^>]+>/i', $html_content, $header_links);
						preg_match('/<style[^>]+>(.*?)<\/style>/is', $html_content, $css);
						$html_css = $css[1];
						// <font
						// etc???
						
						$info .= "+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+<br />";
						// TODO: figure out how to display HTML as code, not have tags act as tags
						//$info .= "title: <code>".print_r($title,true)."</code><br />";
						$info .= "<h4>title:</h4>";
						foreach ( $title as $i => $tmp ) {
							$info .= "[$i] <code>".htmlspecialchars($tmp)."</code><br />";
						}
						
						//$info .= "meta: ".print_r($meta,true)."<br />";
						$info .= "<h4>meta[0]:</h4>";
						foreach ( $meta[0] as $i => $tmp ) {
							$info .= "[$i] <code>".htmlspecialchars($tmp)."</code><br />";
						}
						$info .= "<h4>meta[1]:</h4>";
						$html_meta = "";
						foreach ( $meta[1] as $i => $tmp ) {
							$info .= "[$i] <code>".htmlspecialchars($tmp)."</code><br />";
							$html_meta .= $tmp."\n";
						}
						
						$info .= "<h4>header_links[0]:</h4>";
						//$info .= "links: ".print_r($links,true)."<br />";
						foreach ( $header_links[0] as $i => $tmp ) {
							$info .= "[$i] <code>".htmlspecialchars($tmp)."</code><br />";
						}
						$info .= "<h4>header_links[1]:</h4>";
						$html_stylesheet = "";
						foreach ( $header_links[1] as $i => $tmp ) {
							$info .= "[$i] <code>".htmlspecialchars($tmp)."</code><br />";
							$html_stylesheet .= $tmp."\n";
						}
						/*foreach ( $links as $i => $tmp ) {
							if (!empty($tmp)) { 
								if (!is_array($tmp)) { 
									$info .= "[$i] <code>".htmlspecialchars($tmp)."</code><br />";
								} else {
									foreach ( $tmp as $k => $v ) {
										$info .= "[$i/$k] <code>".htmlspecialchars($v)."</code><br />";
									}
								}
							}
						}*/
						$info .= "<h4>css:</h4>";
						//$info .= "css: ".print_r($css,true)."<br />";
						foreach ( $css as $i => $tmp ) {
							if (!empty($tmp)) { $info .= "[$i] <code>".htmlspecialchars($tmp)."</code><br />"; }
						}
						//$info .= $html_content;
						$info .= "+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+<br /><br />";
						
						// Run the post_meta updates
						//if ( update_post_meta( $post_id, 'html_last_modified', wp_slash( $html_last_modified ) ) ) {
						if ( update_post_meta( $post_id, 'html_last_modified', $html_last_modified ) ) {
							$info .= "Update OK for html_last_modified postmeta<br />";
						} else {
							//$info .= "ERROR updating html_last_modified postmeta<br />";
						}
						if ( update_post_meta( $post_id, 'html_title', wp_slash( $html_title ) ) ) {
							$info .= "Update OK for html_title postmeta<br />";
						} else {
							//$info .= "ERROR updating html_title postmeta<br />";
						}
						if ( update_post_meta( $post_id, 'html_meta', wp_slash( $html_meta ) ) ) {
							$info .= "Update OK for html_meta postmeta<br />";
						} else {
							//$info .= "ERROR updating html_meta postmeta<br />";
						}
						if ( update_post_meta( $post_id, 'html_stylesheet', wp_slash( $html_stylesheet ) ) ) {
							$info .= "Update OK for html_stylesheet postmeta<br />";
						} else {
							//$info .= "ERROR updating html_stylesheet postmeta<br />";
						}
						if ( update_post_meta( $post_id, 'html_css', wp_slash( $html_css ) ) ) {
							$info .= "Update OK for html_css postmeta<br />";
						} else {
							//$info .= "ERROR updating html_css postmeta<br />";
						}
						if ( update_post_meta( $post_id, 'html_content', wp_slash( $html_content ) ) ) {
							$info .= "Update OK for html_content postmeta<br />";
						} else {
							//$info .= "ERROR updating html_content postmeta<br />";
						}					
						if ( update_post_meta( $post_id, 'html_bk', wp_slash( $body ) ) ) {
							$info .= "Update OK for html_bk postmeta<br />";
						} else {
							//$info .= "ERROR updating html_bk postmeta<br />";
						}						
						
					}
			
				}			
			}
		}
		
		//$info .= $html_content;
        
        // Save content (only if previously empty)
		/*
		$data = array(
			'ID' => $post_id,
			'post_content' => $content,
			'meta_input' => array(
			'meta_key' => $meta_value,
			'another_meta_key' => $another_meta_value
		)
		);

		wp_update_post( $data, true );
		if (is_wp_error($post_id)) { // ?? if (is_wp_error($data)) {
			$errors = $post_id->get_error_messages();
			foreach ($errors as $error) {
				$info .= $error;
			}
		}
        */
        
        
        //$info .= $calc_info;
        $info .= "<br />";
             
    } // END foreach post

	
	return $info;
	
}


?>