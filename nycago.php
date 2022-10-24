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

$includes = array( 'posttypes', 'taxonomies' ); // , 'events', 'sermons'

foreach ( $includes as $inc ) {
    $filepath = $plugin_path . 'inc/'.$inc.'.php'; 
    if ( file_exists($filepath) ) { include_once( $filepath ); } else { echo "no $filepath found"; }
}

/* +~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+~+ */

// Enqueue scripts and styles -- WIP
//add_action( 'wp_enqueue_scripts', 'wpnycago_scripts_method' );
function wpnycago_scripts_method() {
    
    global $current_user;
    $current_user = wp_get_current_user();

}


// Add post_type query var to edit_post_link so as to be able to selectively load plugins via plugins-corral MU plugin
add_filter( 'get_edit_post_link', 'add_post_type_query_var', 10, 3 );
function add_post_type_query_var( $url, $post_id, $context ) {

    $post_type = get_post_type( $post_id );
    
    // TODO: consider whether to add query_arg only for certain CPTS?
    if ( $post_type && !empty($post_type) ) { $url = add_query_arg( 'post_type', $post_type, $url ); }
    
    return $url;
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


// Enable shortcodes in sidebar widgets
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );


// ACF
//add_filter('acf/settings/row_index_offset', '__return_zero');
// TODO: update other calls to ACF functions in case this screws them up?


/*** Add Custom Post Status: Archived ***/

add_action( 'init', 'nycago_custom_post_status_creation' );
function nycago_custom_post_status_creation(){
	register_post_status( 'archived', array(
		'label'                     => _x( 'Archived', 'post' ), 
		'label_count'               => _n_noop( 'Archived <span class="count">(%s)</span>', 'Archived <span class="count">(%s)</span>'),
		'public'                    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'post_type'                 => array( 'post', 'nf_sub' ),
	));
}

add_filter( 'display_post_states', 'nycago_display_status_label' );
function nycago_display_status_label( $statuses ) {
	global $post; // we need it to check current post status
	if( get_query_var( 'post_status' ) != 'archived' ){ // not for pages with all posts of this status
		if ( $post && $post->post_status == 'archived' ){ // если статус поста - Архив
			return array('Archived'); // returning our status label
		}
	}
	return $statuses; // returning the array with default statuses
}

// TODO: move script to JS file and enqueue it properly(?)
add_action('admin_footer-edit.php','nycago_status_into_inline_edit');
function nycago_status_into_inline_edit() { // ultra-simple example
	echo "<script>
	jQuery(document).ready( function() {
		jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"archived\">Archived</option>' );
	});
	</script>";
}

add_action( 'post_submitbox_misc_actions', 'nycago_post_submitbox_misc_actions' );
function nycago_post_submitbox_misc_actions(){

    global $post;

    //only when editing a post
    if ( $post->post_type == 'post' || $post->post_type == 'event' ){

        // custom post status: approved
        $complete = '';
        $label = '';   

        if( $post->post_status == 'archived' ){
            $complete = 'selected=\"selected\"';
            $label = '<span id=\"post-status-display\"> Archived</span>';
        }

        echo '<script>'.
                 'jQuery(document).ready(function($){'.
                     '$("select#post_status").append('.
                         '"<option value=\"archived\" '.$complete.'>'.
                             'Archived'.
                         '</option>"'.
                     ');'.
                     '$(".misc-pub-section label").append("'.$label.'");'.
                 '});'.
             '</script>';
    }
}



/*** MISC ***/

// Function to determine default taxonomy for a given post_type, for use with display_posts shortcode, &c.
/*function atc_get_default_taxonomy ( $post_type = null ) {
    switch ($post_type) {
        case "post":
            return "category";
        case "page":
            return "page_tag"; // ??
        case "event":
            return "event-categories";
        case "product":
            return "product_cat";
        case "repertoire":
            return "repertoire_category";
        case "person":
            return "people_category";
        case "sermon":
            return "sermon_topic";
        default:
            return "category"; // default -- applies to type 'post'
    }
}*/

///

function digit_to_word($number){
    switch($number){
        case 0:$word = "zero";break;
        case 1:$word = "one";break;
        case 2:$word = "two";break;
        case 3:$word = "three";break;
        case 4:$word = "four";break;
        case 5:$word = "five";break;
        case 6:$word = "six";break;
        case 7:$word = "seven";break;
        case 8:$word = "eight";break;
        case 9:$word = "nine";break;
    }
    return $word;
}

/*function nycago_get_posts ( $a = array() ) {
    
    global $wpdb;
    
    // Init vars
    $arr_posts_info = array();
    $info = "";
    $get_by_ids = false;
    $category_link = null;
    
    $info .= "args as passed to nycago_get_posts: <pre>".print_r($a,true)."</pre>";
    
    if ( isset($a['limit']) )       { $num_posts = $a['limit'];     } else { $num_posts = '-1'; }
    if ( isset($a['orderby']) )     { $orderby = $a['orderby'];     } else { $orderby = null; }
    if ( isset($a['order']) )       { $order = $a['order'];         } else { $order = null; }
    if ( isset($a['post_type']) )   { $post_type = $a['post_type']; } else { $post_type = 'post'; }
    //
    if ( isset($a['meta_query']) )  { $meta_query = $a['meta_query']; } else { $meta_query = array(); }
    if ( isset($a['tax_query']) )  	{ $tax_query = $a['tax_query']; } else { $tax_query = array(); }
    
    // Set up basic args
    $args = array(
		'post_type'       => $post_type,
		'post_status'     => 'publish',
		'order'           => $order,
		'posts_per_page'  => $num_posts
	);
    
    // Posts by ID
    // NB: if IDs are specified, ignore most other args
    if ( isset($a['ids']) && !empty($a['ids']) ) {
        
        $info .= "Getting posts by IDs: ".$a['ids'];
        // Turn the list of IDs into a proper array
		$posts_in         = array_map( 'intval', nycago_att_explode( $a['ids'] ) );
		$args['post__in'] = $posts_in;
        $args['orderby'] = 'post__in';
        $get_by_ids = true;
        
	}
    
    if ( !$get_by_ids ) {
        
        // TODO: simplify the setting of default values
        if ( isset($a['taxonomy']) )    { $taxonomy = $a['taxonomy'];   } else { $taxonomy = null; }
        if ( isset($a['tax_terms']) )   { $tax_terms = $a['tax_terms']; } else { $tax_terms = null; }
        if ( isset($a['category']) )    { $category = $a['category'];   } else { $category = null; }
		//
        if ( isset($a['meta_key']) )    { $meta_key = $a['meta_key'];   } else { $meta_key = null; }
        if ( isset($a['meta_value']) )  { $meta_value = $a['meta_value'];   } else { $meta_value = null; }
        
        // For Events & Sermons
        if ( isset($a['series']) )      { $series = $a['series'];       } else { $series = null; }
        
        // Deal w/ taxonomy args
        $tax_field = 'slug'; // init -- in some cases will want to use term_id
        if ( $category && empty($taxonomy) ) {
            $taxonomy = 'category';
            $tax_terms = $category;
        }
        $cat_id = null; // init

        // If not empty tax_terms and empty taxonomy, determine default taxonomy from post type
        if ( empty($taxonomy) && !empty($tax_terms) ) {
            $info .= "Using atc_get_default_taxonomy"; // tft
            $taxonomy = atc_get_default_taxonomy($post_type);
        }

        // Taxonomy operator
        if ( strpos($tax_terms,"NOT-") !== false ) {
            $tax_terms = str_replace("NOT-","",$tax_terms);
            $tax_operator = 'NOT IN';
        } else {
            $tax_operator = 'IN';
        }

        // Post default category, if applicable -- WIP
        if ( $post_type == 'post' && ( empty($taxonomy) || $taxonomy == 'category' ) && empty($tax_terms) ) {
            $category = null; // tft
            //$category = atc_get_default_category();
            if ( !empty($category) ) {
                $tax_terms = $category;
                //$cat_id = get_cat_ID( $category );
                //$tax_terms = $cat_id;
                if ( empty($taxonomy) ) {
                    $taxonomy = 'category';
                }
            } else {
                $tax_terms = null;
            }
        }
        
        // If terms, check to see if array or string; build tax_query accordingly
        //if ( !empty($terms) ) { } // TBD
        
        // Orderby
        if ( isset($a['orderby']) ) {

            $standard_orderby_values = array( 'none', 'ID', 'author', 'title', 'name', 'type', 'date', 'modified', 'parent', 'rand', 'comment_count', 'relevance', 'menu_order', 'meta_value', 'meta_value_num', 'post__in', 'post_name__in', 'post_parent__in' );

            // determine if orderby is actually meta_value or meta_value_num with orderby $a value to be used as meta_key
            if ( !in_array( $a['orderby'], $standard_orderby_values) ) {
                // TODO: determine whether to sort meta values as numbers or as text
                if (strpos($a['orderby'], 'num') !== false) {
                    $args['orderby'] = 'meta_value_num'; // or meta_value?
                } else {
                    $args['orderby'] = 'meta_value';
                }
                //$args['meta_key'] = $a['orderby'];
                
            } else {
                $args['orderby'] = $a['orderby'];
            }

        }
        
		
		if ( !empty($tax_query) ) {
			
			$args['tax_query'] = $tax_query;
			
		} else if ( is_category() ) {

            // Post category archive
            $info .= "is_category (archive)<br />";

            // Get archive cat_id
            if ( is_dev_site() ) {
                $archive_cat_id = '2183'; // dev
            } else {
                $archive_cat_id = '2971'; // live
            }

            $tax_field = 'term_id';

            $args['tax_query'] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'category',
                    'field'    => $tax_field,
                    'terms'    => array( $tax_terms ),
                ),
                array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => array( $archive_cat_id),
                    'operator' => 'NOT IN',
                ),
            );

        } else if ( $taxonomy && $tax_terms ) {

            $info .= "Building tax_query based on taxonomy & tax_terms.<br />";

            $args['tax_query'] = array(
                array(
                    'taxonomy'  => $taxonomy,
                    'field'     => $tax_field,
                    'terms'     => $tax_terms,
                    'operator'  => $tax_operator,
                )
            );

        }
        
        // Meta Query
		if ( empty($meta_query) ) {
			
			$meta_query_components = array();
        
			// Featured Image restrictions?
			// TODO: update this to account for custom_thumb and first_image options? Or is it no longer necessary at all?
			if ( isset($a['has_image']) && $a['has_image'] == true ) {
				$meta_query_components[] = 
					array(
						'key' => '_thumbnail_id',
						'compare' => 'EXISTS'
					);
			}

			// WIP/TODO: check to see if meta_query was set already via query args...
			//if ( !isset($a['meta_query']) )  {

			if ( ( $meta_key && $meta_value ) ) {

				$meta_query_components[] = 
					array(
						'key' => $meta_key,
						'value'   => $meta_value,
						'compare' => '=',
					);
			} else if ( ( $meta_key ) ) {

                // meta_key specified, but no value
				$meta_query_components[] = 
					array(
						'key' => $meta_key,
						//'value' => '' ,
                        'compare' => 'EXISTS',
					);
			}

			if ( count($meta_query_components) > 1 ) {
				$meta_query['relation'] = 'AND';
				foreach ( $meta_query_components AS $component ) {
					$meta_query[] = $component;
				}
			} else {
				$meta_query = $meta_query_components;
			}
			
		}

        if ( !empty($meta_query) ) {
            $args['meta_query'] = $meta_query;
        }
        
        if ( $cat_id && ! is_category() ) { // is_archive()

            // Get the URL of this category
            $category_url = get_category_link( $cat_id );
            $category_link = 'Category Link';
            if ($category_url) { 
                $category_link = '<a href="'.$category_url.'"';
                if ($category === "Latest News") {
                    $category_link .= 'title="Latest News">All Latest News';
                } else {
                    $category_link .= 'title="'.$category.'">All '.$category.' Articles';
                }
                $category_link .= '</a>';
            }

        }
        
    } // END if ( !$get_by_ids )
    
    
    // -------
    // Run the query
    // -------
	$arr_posts = new WP_Query( $args );
    
    $info .= "WP_Query run as follows:";
    $info .= "<pre>args: ".print_r($args, true)."</pre>"; // tft
    //$info .= "<pre>meta_query: ".print_r($meta_query, true)."</pre>"; // tft
	$info .= "<pre>arr_posts: ".print_r($arr_posts, true)."</pre>"; // tft
    $info .= "<pre>".$arr_posts->request."</pre>"; // tft -- wip
    //$info .= "<!-- Last SQL-Query: ".$wpdb->last_query." -->";

    $info = '<div class="troubleshooting">'.$info.'</div>';
    
    $arr_posts_info['arr_posts'] = $arr_posts;
    $arr_posts_info['args'] = $args;
    $arr_posts_info['category_link'] = $category_link;
    $arr_posts_info['info'] = $info;
    
    return $arr_posts_info;
}*/


// Function for display of posts in various formats -- links, grid, &c.
// This shortcode is in use on numerous Pages, as well as via the archive.php page template
//add_shortcode('display_posts', 'nycago_display_posts');
/*function nycago_display_posts ( $atts = [] ) {

    global $wpdb;
	$info = "";

	$a = shortcode_atts( array(
        
        'post_type' => 'post',
        'limit' => 5,
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_key' => null,
        'meta_value' => null,
        //
        'ids' => null,
        'name' => null,
        //
        'category' => null, // for posts, pages only
        'taxonomy'  => null,
        'tax_terms'  => null,
        //
        'return_format' => 'links', // or: 'excerpt' for single excerpt 'archive' for linked list as in search results/archives; OR 'grid' for "flex-grid"
        'cols' => 4,
        'spacing' => 'spaced',
        'header' => false,
        'overlay' => false,
        'has_image' => false, // set to true to ONLY return posts with features images
        'class' => null, // for additional styling
        
        // For post_type 'event'
        'scope' => 'upcoming',
        
        // For Events or Sermons
        'series' => false,
        
        // For table return_format
        'fields'  => null,
        'headers'  => null,
        
    ), $atts );
    
    //if ( $a ) { $info .= '<div class="troubleshooting"><pre>'.print_r($a, true).'</pre></div>'; } // tft
    
    $post_type = $a['post_type'];
    $return_format = $a['return_format'];
    $class = $a['class'];
    
    // For grid format:
    $num_cols = $a['cols'];
    $spacing = $a['spacing'];
    $header = $a['header'];
    $overlay = $a['overlay'];
    
    // For table format:
    $fields = $a['fields'];
    $headers = $a['headers'];
    
    // Clean up the array
    if ( $post_type !== "event" ) { unset($a["scope"]); }
    if ( $post_type !== "event" && $post_type !== "sermon" ) { unset($a["series"]); }
    if ( $return_format != "grid" ) { unset($a["cols"]); unset($a["spacing"]); unset($a["overlay"]); }
    
    // Make sure the return_format is valid
    if ( $return_format != "links" && $return_format != "grid" && $return_format != "table" ) {
        $return_format = "links"; // default
    }
    // TODO: revive/fix "archive" option -- deal w/ get_template_part issue...
    // TODO: add "table" format option??
    //
    
    // Retrieve an array of posts matching the args supplied    
    if ( $post_type == 'event' ) {
        // TODO: deal w/ taxonomy parameters -- how to translate these properly for EM?
        $posts = EM_Events::get( $a ); // Retrieves an array of EM_Event Objects
    } else {
        $posts_info = nycago_get_posts( $a );
        $posts = $posts_info['arr_posts']->posts; // Retrieves an array of WP_Post Objects
        $info .= $posts_info['info'];
    }
    
    //$info .= "<!-- TEST -->"; // tft
    
    if ( $posts ) {
        $info .= '<div class="troubleshooting"><pre>'.print_r($posts, true).'</pre></div>'; // tft
    }
    
	if ( $posts ) {
        
		//if ($a['header'] == 'true') { $info .= '<h3>Latest '.$category.' Articles:</h3>'; } // TODO: fix this!
		
        if ( $return_format == "links" ) {
            $info .= '<ul>';
        } else if ( $return_format == "archive" ) {
            $info .= '<div class="posts_archive">';
        } else if ( $return_format == "table" ) {
            
            $info .= '<table class="posts_archive">'; //$info .= '<table class="posts_archive '.$class.'">';
            // Make header row from field names
            if ( !empty($fields) ) {
                
                $info .= "<tr>"; // prep the header row
                
                // make array from fields string
                $arr_fields = explode(",",$fields);
                //$info .= "<td>".$fields."</td>";
                //$info .= "<td><pre>".print_r($arr_fields, true)."</pre></td>"; // tft
                
                if ( !empty($headers) ) {
                    $arr_headers = explode(",",$headers);
                    
                    foreach ( $arr_headers as $header ) {
                        $header = trim($header);
                        if ( $header == "-" ) { $header = ""; }
                        $info .= "<th>".$header."</th>";
                    }
                    
                } else {
                    
                    // If no headers were submitted, make do with the field names
                    foreach ( $arr_fields as $field_name ) {
                        $field_name = ucfirst(trim($field_name));
                        $info .= "<th>".$field_name."</th>";
                    }
                    
                }
                
                $info .= "</tr>"; // close out the header row
            }
            
        } else if ( $return_format == "grid" ) {
            $colclass = digit_to_word($num_cols)."col";
            if ( $class ) { $colclass .= " ".$class; }
            $info .= '<div class="flex-container '.$colclass.'">';
        }
        
        foreach ( $posts as $post ) {
            
            //$info .= '<pre>'.print_r($post, true).'</pre>'; // tft
            //$info .= '<div class="troubleshooting">post: <pre>'.print_r($post, true).'</pre></div>'; // tft
            
            if ( $post_type == 'event' ) {
                $post_id = $post->post_id;
                $info .= '<!-- Event post_id: '.$post_id." -->"; // tft
            } else {
                $post_id = $post->ID;
                $info .= '<!-- Post post_id: '.$post_id." -->"; // tft
            }
            
            // If a short_title is set, use it. If not, use the post_title
            $short_title = get_post_meta( $post_id, 'short_title', true );
            if ( $short_title ) { $post_title = $short_title; } else { $post_title = get_the_title($post_id); }
            
            if ( $return_format == "links" ) {
                
                $info .= '<li>';
                $info .= '<a href="'.get_the_permalink( $post_id ).'" rel="bookmark">'.$post_title.'</a>';
                $info .= '</li>';
                
            } else if ( $return_format == "archive" ) {
                
                // TODO: bring this more in alignment with theme template display? e.g. content-excerpt, content-sermon, content-event...
                $info .= '<!-- wpt/adapted: content-excerpt -->';
                $info .= '<article id="post-'.$post_id.'">'; // post_class()
                $info .= '<header class="entry-header">';
                $info .= '<h2 class="entry-title"><a href="'.get_the_permalink( $post_id ).'" rel="bookmark">'.$post_title.'</a></h2>';
                $info .= '</header><!-- .entry-header -->';
                $info .= '<div class="entry-content">';
                if ( is_dev_site() ) {
                    $info .= nycago_post_thumbnail($post_id);
                }
                $info .= get_the_excerpt( $post_id );
                $info .= '</div><!-- .entry-content -->';
                $info .= '<footer class="entry-footer">';
                $info .= twentysixteen_entry_meta( $post_id );
                $info .= '</footer><!-- .entry-footer -->';
                $info .= '</article><!-- #post-'.$post_id.' -->';

                //$info .= get_template_part( 'template-parts/content', 'excerpt', array('post_id' => $post_id ) ); // 
                //$post_type_for_template = atc_get_type_for_template();
                //get_template_part( 'template-parts/content', $post_type_for_template );
                //$info .= get_template_part( 'template-parts/content', $post_type );
                
            } else if ( $return_format == "table" ) {
                
                $info .= '<tr>';
                
                if ( !empty($arr_fields) ) { 
                    
                    foreach ( $arr_fields as $field_name ) {
                        $field_name = trim($field_name);
                        if ( !empty($field_name) ) {
                            
                            $info .= '<td>';
                            if ( $field_name == "title" ) {
                                $field_value = '<a href="'.get_the_permalink( $post_id ).'" rel="bookmark">'.$post_title.'</a>';
                            } else {
                                $field_value = get_post_meta( $post_id, $field_name, true );
                                //$info .= "[".$field_name."] "; // tft
                            }
                            
                            if ( is_array($field_value) ) {
                                
                                if ( count($field_value) == 1 ) { // If t
                                    if ( is_numeric($field_value[0]) ) {
                                        // Get post_title
                                        $field_value = get_the_title($field_value[0]);
                                        $info .= $field_value;
                                    } else {
                                        $info .= "Not is_numeric: ".$field_value[0];
                                    }
                                    
                                } else {
                                    $info .= count($field_value).": <pre>".print_r($field_value, true)."</pre>";
                                }
                                
                            } else {
                                $info .= $field_value;
                            }
                            
                            $info .= '</td>';
                        }
                    }
                    
                }
                
                $info .= '</tr>';
                
            } else if ( $return_format == "grid" ) {
                
                $post_info = "";
                $grid_img = "";
                $featured_img_url = "/wp-content/uploads/woocommerce-placeholder-250x250.png"; // Default/placeholder
                
                // Get a featured image for display in the grid
                
                // First, check to see if the post has a Custom Thumbnail
                $custom_thumb_id = get_post_meta( $post_id, 'custom_thumb', true );
                
                if ( $custom_thumb_id ) {
                    
                    $featured_img_url = wp_get_attachment_image_url( $custom_thumb_id, 'medium' ); 
                    //$grid_img = wp_get_attachment_image( $custom_thumb_id, 'medium', false, array( "class" => "custom_thumb" ) );
                    //$post_info .= "custom_thumb_id: $custom_thumb_id<br />"; // tft
                    
                } else {
                    
                    // No custom_thumb? Then retrieve the url for the full size featured image, if any
                    if ( has_post_thumbnail( $post_id ) ) {
                        
                        $featured_img_url = get_the_post_thumbnail_url( $post_id, 'medium');
                        
                    } else { 
                        
                        // If there's no featured image, look for an image in the post content
                        
                        $first_image = get_first_image_from_post_content( $post_id );
                        if ( $first_image && !empty($first_image['id']) ) {
                            
                            $first_img_src = wp_get_attachment_image_src( $first_image['id'], 'full' );
                            
                            // If the image found is large enough, display it in the grid
                            if ( $first_img_src[1] > 300 && $first_img_src[2] > 300 ) {
                                $featured_img_url = wp_get_attachment_image_url( $first_image['id'], 'medium' );
                            }
                        }
                        
                    }
                    
                }
                
                $grid_img = '<img src="'.$featured_img_url.'" alt="'.get_the_title($post_id).'" width="100%" height="100%" />';
                
                $post_info .= '<a href="'.get_the_permalink($post_id).'" rel="bookmark">';
                $post_info .= '<span class="post_title">'.$post_title.'</span>';
                // For events, also display the date/time
                if ( $post_type == 'event' ) { 
                    $event_start_datetime = get_post_meta( $post_id, '_event_start_local', true );
                    //$event_start_time = get_post_meta( $post_id, '_event_start_date', true );
                    if ( $event_start_datetime ) {
                        //$post_info .= "[".$event_start_datetime."]"; // tft
                        $date_str = date_i18n( "l, F d, Y \@ g:i a", strtotime($event_start_datetime) );
                        $post_info .= "<br />".$date_str;
                    }
                }
                //
                $post_info .= '</a>';
                
                $info .= '<div class="flex-box '.$spacing.'">';
                //$info .= 'test: '.$featured_img_url; // tft
                $info .= '<div class="flex-img">';
                $info .= '<a href="'.get_the_permalink($post_id).'" rel="bookmark">';
                $info .= $grid_img;
                $info .= '</a>';
                $info .= '</div>';
                if ( $overlay == true ) {
                    $info .= '<div class="overlay">'.$post_info.'</div>';
                } else {
                    $info .= '<div class="post_info">'.$post_info.'</div>';
                }
                $info .= '</div>';
                
            } else {
                
                $the_content = apply_filters('the_content', get_the_content($post_id));
                $info .= $the_content;
                //$info .= the_content();
                
            }
            
        }
        
        if ( $return_format == "links" ) {
            //if ( ! is_archive() && ! is_category() ) { $info .= '<li>'.$category_link.'</li>'; }
            $info .= '</ul>';
        } else if ( $return_format == "archive" ) {
            $info .= '</div>';
        } else if ( $return_format == "table" ) {
            $info .= '</table>';
        } else if ( $return_format == "grid" ) {
            $info .= '</div>';
        }
		
        wp_reset_postdata();
    
    } // END if posts
    
    return $info;
    
}
*/
/**
 * Explode list using "," and ", ".
 *
 * @param string $string String to split up.
 * @return array Array of string parts.
 */
/*function nycago_att_explode( $string = '' ) {
	$string = str_replace( ', ', ',', $string );
	return explode( ',', $string );
}*/

///

// Get a linked list of Terms
add_shortcode('list_terms', 'atc_list_terms');
function atc_list_terms ($atts = [], $content = null, $tag = '') {

	$info = "";
	
	$a = shortcode_atts( array(
      	'child_of'		=> 0,
		'cat'			=> 0,
		//'depth'			=> 0,
		'exclude'       => array(),
      	'hierarchical'	=> true,
		'include'       => array(),
		//'meta_key'	=> 'key_name',
     	'orderby'		=> 'name', // 'id', 'meta_value'
      	'show_count'	=> 0,
		'tax'			=> 'category',
		'title'        	=> '',
    ), $atts );
	
	$all_items_url = ""; // tft
	$all_items_link = ""; // tft
	$exclusions_per_taxonomy = array(); // init
	
	if ( $a['tax'] == "category" ) {
		$exclusions_per_taxonomy = array(1389, 1674, 1731);
		$all_items_url = "/news/";
	} else if ( $a['tax'] == "event-categories" ) {
		$exclusions_per_taxonomy = array(1675, 1690);
		$all_items_url = "/events/";
	}
	// Turn exclusion/inclusion attribute from comma-sep list into array as prep for merge/ for use w/ atc_get_terms_orderby
	if ( !empty($a['exclude']) ) { $a['exclude'] = array_map('intval', explode(',', $a['exclude']) ); } //$integerIDs = array_map('intval', explode(',', $string));
	if ( !empty($a['include']) ) { $a['include'] = array_map('intval', explode(',', $a['include']) ); }
	$exclusions = array_merge($a['exclude'], $exclusions_per_taxonomy);
	$inclusions = $a['include'];
	$term_names_to_skip = array('Featured Posts', 'Featured Posts (2)', 'Featured Events', 'Featured Events (2)');
	
	// List terms in a given taxonomy using wp_list_categories (also useful as a widget if using a PHP Code plugin)
    $args = array(
        'child_of' => $a['child_of'],
		//'depth' => $a['depth'],
		'exclude' => $exclusions,
		'include' => $inclusions,
        //'current_category'    => $a['cat'],
        'taxonomy'     => $a['tax'],
        'orderby'      => $a['orderby'],
        //'show_count'   => $a['show_count'],
        //'hierarchical' => $a['hierarchical'],
        //'title_li'     => $a['title']
    );
	$info .= "<!-- ".print_r($args, true)." -->"; // tft
	
	$terms = get_terms($args);
	
	/*
	'meta_query' => array(
        [
            'key' => 'meta_key_slug_1',
            'value' => 'desired value to look for'
        ]
    ),
    'meta_key' => 'meta_key_slug_2',
    'orderby' => 'meta_key_slug_2'
	*/
	
    if ($all_items_url) { 
        $all_items_link = '<a href="'.$all_items_url.'"';
        if ( $a['tax'] === "event-categories" ) {
            $all_items_link .= ' title="All Events">All Events';
        } else {
            $all_items_link .= ' title="All Articles">All Articles';
        }
        $all_items_link .= '</a>';
    }
	
	
	if ( !empty( $terms ) && !is_wp_error( $terms ) ){
		$info .= "<ul>";
		$info .= '<li>'.$all_items_link.'</li>';
		foreach ( $terms as $term ) {
			if ( !in_array($term->name, $term_names_to_skip) ) {
			//if ($term->name != 'Featured Events' AND $term->name != 'Featured Events (2)') {
				if ( $a['tax'] === "event-categories" ) {
                    $term_link = "/events/?category=".$term->slug;
                } else {
                    $term_link = get_term_link( $term );
                }
                $term_name = $term->name;
				//if ($term_name === "Worship Services") { $term_name = "All Worship Services"; }
				$info .= '<li>';
				$info .= '<a href="'.$term_link.'" rel="bookmark">'.$term_name.'</a>';
				$info .= '</li>';
			}		
		}
		$info .= "</ul>";
	} else {
		$info .= "No terms.";
	}
	return $info;
}

// Function to facilitate custom order when calling get_terms
/**
 * Modifies the get_terms_orderby argument if orderby == include
 *
 * @param  string $orderby Default orderby SQL string.
 * @param  array  $args    get_terms( $taxonomy, $args ) arg.
 * @return string $orderby Modified orderby SQL string.
 */
add_filter( 'get_terms_orderby', 'atc_get_terms_orderby', 10, 2 );
function atc_get_terms_orderby( $orderby, $args ) {
  	//if ( isset( $args['orderby'] ) && 'include' == $args['orderby'] ) {
	if ( isset( $args['orderby'] ) ) {
		if ($args['orderby'] === 'include') {
          $ids = implode(',', array_map( 'absint', $args['include'] ));
          $orderby = "FIELD( t.term_id, $ids )";
		} /*else if ($args['orderby'] === 'post_types') {
          $ids = implode(',', array_map( 'absint', $args['post_types'] ));
          $orderby = "FIELD( t.term_id, $ids )";
		}*/
	} 
	return $orderby;
}


function nycago_add_post_term( $post_id = null, $arr_term_slugs = array(), $taxonomy = "", $return_info = false ) {
//function nycago_add_post_terms( $post_id = null, $arr_term_slugs = array(), $taxonomy = "", $return_info = false ) {
    
    $term_ids = array();
    $info = "";
    $result = null;
    
    // If a string was passed instead of an array, then explode it.
    if ( !is_array($arr_term_slugs) ) { $arr_term_slugs = explode(',', $arr_term_slugs); }
    
    // Add 'programmatically-updated' to all posts updated via this function
    if ( is_dev_site() == true ) { $term_ids[] = 1963; } else { $term_ids[] = 2204; }
    //$arr_term_slugs[] = 'programmatically-updated';
    
    // NB: Hierarchical taxonomies must always pass IDs rather than names -- so, get the IDs
    foreach ( $arr_term_slugs as $term_slug ) {
        
        if ( $term_slug == 'cleanup-required' ) {
            if ( is_dev_site() ) { $term_ids[] = 1668; } else { $term_ids[] = 1668; }
        } else if ( $term_slug == 'program-personnel-placeholders' ) {
            if ( is_dev_site() ) { $term_ids[] = 2177; } else { $term_ids[] = 2548; }
        } else if ( $term_slug == 'program-item-placeholders' ) {
            if ( is_dev_site() ) { $term_ids[] = 2178; } else { $term_ids[] = 2549; }
        } else if ( $term_slug == 'program-placeholders' ) {
            if ( is_dev_site() ) { $term_ids[] = 2176; } else { $term_ids[] = 2547; }
        } else if ( $term_slug == 'slug-updated' ) {
            if ( is_dev_site() ) { $term_ids[] = 1960; } else { $term_ids[] = 2203; }
        } else if ( $term_slug == 't4m-updated' ) {
            if ( is_dev_site() ) { $term_ids[] = 2174; } else { $term_ids[] = 2532; }
        } else if ( $term_slug == 'field-conversion-ok' ) {
            if ( is_dev_site() ) { $term_ids[] = 3091; } else { $term_ids[] = null; }
        }/*else if ( $term_slug == 'programmatically-updated' ) {
            if ( is_dev_site() ) { $term_ids[] = 1963; } else { $term_ids[] = 2204; }
        }*/
        
        if ( has_term( $term_slug, $taxonomy ) ) {
            return "<!-- [nycago_add_post_term] post $post_id already has $taxonomy: $term_slug. No changes made. -->";
        } else {
            $result = wp_set_post_terms( $post_id, $term_ids, $taxonomy, true ); // wp_set_post_terms( int $post_id, string|array $tags = '', string $taxonomy = 'post_tag', bool $append = false )
        }
        
    }
    
    if ( $return_info == true ) {
        
        $info .= "<!-- [nycago_add_post_term] -- ";
        //$info .= "<!-- wp_set_post_terms -- ";
        $info .= "$taxonomy: $term_slug";
        //$info .= implode(", ",$arr_term_slugs)." -- ";
        if ( $result ) { 
			$info .= " success!"; 
		} else { 
			$info .= " FAILED!";
			$info .= print_r($term_ids, true);
		}
        $info .= " -->";
        return $info;
        
    } else {
        
        return $result;
    }
    
}

function nycago_remove_post_term( $post_id = null, $term_slug = null, $taxonomy = "", $return_info = false ) {
    
    $term_ids = array();
    $info = "";
    
    // TODO -- Cleanup: remove t4m-updated from all events -- it doesn't apply because events don't have a title_for_matching field -- they have title_uid instead
    
    $result = wp_remove_object_terms( $post_id, $term_slug, $taxonomy ); // wp_remove_object_terms( int $object_id, string|int|array $terms, array|string $taxonomy )
    
    if ( $return_info == true ) {
        $info .= "<!-- wp_remove_object_terms -- ";
        $info .= $term_slug;
        if ( $result ) { $info .= "success!"; } else { $info .= "FAILED!"; }
        $info .= " -->";
        return $info;
    } else {
        return $result;
    }
}


/*** Custom Post Types Content ***/

// Umbrella function to get CPT content
// TODO: phase this out? It makes fine-tuning content ordering a bit tricky...
function atc_custom_post_content() {
	
	$info = "";
	$post_type = get_post_type( get_the_ID() );
	
	if ($post_type === "ensemble") {
		$info .= get_cpt_ensemble_content();
	} else if ($post_type === "liturgical_date") {
		$info .= get_cpt_liturgical_date_content();
	} else if ($post_type === "person") {
		$info .= get_cpt_person_content();
	} else if ($post_type === "repertoire") {
		$info .= get_cpt_repertoire_content();
	} else if ($post_type === "edition") {
		$info .= get_cpt_edition_content();
	} else if ($post_type === "reading") {
		$info .= get_cpt_reading_content();
	} else if ($post_type === "sermon") {
		//$info .= get_cpt_sermon_content(); // Disabled because the function doesn't currently add any actual custom content.
	} else {
		//$info .= "<p>[post] content (default)-- coming soon</p>";
		//return false;
		//return;
	}
	
	return $info;
}

// Modify the display order of CPT archives
add_action('pre_get_posts', 'atc_pre_get_posts'); //mind_pre_get_posts
//add_filter( 'posts_orderby' , 'custom_cpt_order' );
function atc_pre_get_posts( $query ) {
  
    if ( is_admin() ) {
        return $query; 
    }
	
	if ( is_archive() && $query->is_main_query() ) {
		
		// In paginated posts loop, show ONLY "Website Archives" category posts 
		// (current/recent posts will be shown separately at the top of the page -- see archive.php)
		//if ( ! is_category( 'website-archives' ) && !is_post_type_archive('repertoire') ) {
		/*if ( is_category() ) { // is_post_type_archive('post')
			if ( is_dev_site() ) {
				$archives_cat_id = '2183'; // dev
			} else {
				$archives_cat_id = '2971'; // live
			}
			//$query->set( 'cat', $archives_cat_id ); // Problem with this approach is that it sets the category for the page as a whole. Need a more fine-tuned approach. 
            // For now, alternate approach is to exclude non-archives posts from display in main loop via archive.php
		}*/
		
		// Custom CPT ORDER
        if ( isset($query->query_vars['post_type']) ) {
            $post_type = $query->query_vars['post_type'];
            if ($post_type === 'bible_book') {
                $query->set('orderby', 'meta_value');
                $query->set('meta_key', 'sort_num');
                $query->set('order', 'ASC');
            } else if ($post_type === 'sermon') {
                $query->set('orderby', 'meta_value');
                $query->set('meta_key', 'sermon_date');
                $query->set('order', 'DESC');
            } else if ($post_type === 'person') {
                $query->set('orderby', 'meta_value');
                $query->set('meta_key', 'last_name');
                $query->set('order', 'ASC');
            } /*else if ($post_type === 'liturgical_date') { // atcwip
                $query->set('orderby', 'meta_value');
                $query->set('meta_key', 'date_time');
                $query->set('order', 'DESC');
            }*/
        }
        
	}
                                                               
  	return $query;
}


/************** CUSTOM POST TYPES CONTENT ***************/


/*********** CPT: PERSON ***********/

function get_cpt_person_content( $post_id = null ) {
	
    $info = ""; // init
    if ($post_id === null) { $post_id = get_the_ID(); }
    $pod = pods( 'person', $post_id );
    
    if ( empty($pod) ) {
        return false;
    }
    
    // Person name (post title)
    //$info .= get_the_title($post_id);
    
    $dates = get_person_dates( $post_id, true );
    if ( $dates && $dates != "" && $dates != "(-)" ) { 
        //$info .= get_the_title($post_id);
        $info .= $dates; 
    }
    
    $info .= get_the_content($post_id);
    
    // TODO: consider eliminating check for has_term, in case someone forgot to apply the appropriate category
    if ( has_term( 'composers', 'people_category', $post_id ) ) {
        // Get compositions
        $arr_obj_compositions = get_related_podposts( $post_id, 'composer', 'repertoire' ); // function get_related_podposts( $post_id = null, $related_field_name = null, $related_post_type = null, $return = 'single'  ) {
        //if ( is_dev_site() ) {
        if ( $arr_obj_compositions ) {
            
            $info .= "<h3>Compositions:</h3>";
            
            //$info .= "<p>arr_compositions (".count($arr_compositions)."): <pre>".print_r($arr_compositions, true)."</pre></p>";
            foreach ( $arr_obj_compositions as $composition ) {
                //$info .= $composition->post_title."<br />";
                $rep_info = get_rep_info( $composition->ID, 'display', false, true ); // ( $post_id = null, $format = 'display', $show_authorship = true, $show_title = true )
                $info .= make_link( get_permalink($composition->ID), $rep_info )."<br />"; // make_link( $url, $linktext, $class = null, $target = null)
            }
        }
        //}
    }
    
    // TODO: arranger, transcriber, translator, librettist
    
    // Find and display any associated Editions, Publications, Sermons, and/or Events
    
    if ( is_dev_site() ) {
        
        // Editions
        $arr_obj_editions = get_related_podposts( $post_id, 'editor', 'edition' );
        //$arr_obj_editions = get_related_podposts( $post_id, 'composer', 'publication' );  // authors, editors, translators
        if ( $arr_obj_editions ) {

            $info .= '<div class="publications">';
            $info .= "<h3>Publications:</h3>";

            //$info .= "<p>arr_obj_editions (".count($arr_obj_editionss)."): <pre>".print_r($arr_obj_editions, true)."</pre></p>";
            foreach ( $arr_obj_editions as $edition ) {
                //$info .= $edition->post_title."<br />";
                $info .= make_link( get_permalink($edition->ID), $edition->post_title )."<br />"; // make_link( $url, $linktext, $class = null, $target = null)
            }

            $info .= '</div>';
        }
    }
    
    // Sermons
    $arr_obj_sermons = get_related_podposts( $post_id, 'sermon_author', 'sermon' );
    if ( $arr_obj_sermons ) {
        
        $info .= '<div class="dev-view sermons">';
        $info .= "<h3>Sermons:</h3>";

        foreach ( $arr_obj_sermons as $sermon ) {
            //$info .= $sermon->post_title."<br />";
            $info .= make_link( get_permalink($sermon->ID), $sermon->post_title )."<br />"; // make_link( $url, $linktext, $class = null, $target = null)
        }
        
        $info .= '</div>';
    }
    
    if ( is_dev_site() ) {
        
        /*
        // Get Related Events
        $args = array(
            'posts_per_page'=> -1,
            'post_type'		=> 'event',
            'meta_query'	=> array(
                array(
                    'key'		=> "personnel_XYZ_person", // name of custom field, with XYZ as a wildcard placeholder (must do this to avoid hashing)
                    'compare' 	=> 'LIKE',
                    'value' 	=> '"' . $post_id . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
                )
            ),
            'orderby'	=> 'meta_value',
            'order'     => 'DESC',
            'meta_key' 	=> '_event_start_date',
        );

        $query = new WP_Query( $args );
        $event_posts = $query->posts;
        $info .= "<!-- args: <pre>".print_r($args,true)."</pre> -->";
        $info .= "<!-- Last SQL-Query: {$query->request} -->";

        if ( $event_posts ) { 
            global $post;
            $info .= '<div class="dev-view em_events">';
            $info .= '<h3>Events at Saint Thomas Church:</h3>';
            foreach($event_posts as $post) { 
                setup_postdata($post);
                // TODO: modify to show title & event date as link text
                $event_title = get_the_title();
                $date_str = get_post_meta( get_the_ID(), '_event_start_date', true );
                if ( $date_str ) { $event_title .= ", ".$date_str; }
                $info .= make_link( get_the_permalink(), $event_title ) . "<br />";	
            }
            $info .= '</div>';
        } else {
            $info .= "<!-- No related events found for post_id: $post_id -->";
        }
        */
        
        $term_obj_list = get_the_terms( $post_id, 'people_category' );
        if ( $term_obj_list ) {
            $terms_string = join(', ', wp_list_pluck($term_obj_list, 'name'));
            $info .= '<div class="dev-view categories">';
            if ( $terms_string ) {
                $info .= "<p>Categories: ".$terms_string."</p>";
            }
            $info .= '</div>';
        }
        
        wp_reset_query();
    }
    
    return $info;
    
}

function get_person_dates( $person_id, $styled = false ) {
    
    nycago_log( "divline2" );
    nycago_log( "function called: get_person_dates" );
    
    //nycago_log( "[str_from_persons] arr_persons: ".print_r($arr_persons, true) );
    nycago_log( "[get_person_dates] person_id: ".$person_id );
    //nycago_log( "[get_person_dates] styled: ".$styled );
    
    $info = ""; // init
    $pod = pods( 'person', $person_id );
    
    /*if ( $pod ) { nycago_log( "[get_person_dates] got person pod" ); } else { nycago_log( "[get_person_dates] PROBLEM! No person pod found" ); }*/
    
    if ( $pod->field('birth_year') && $pod->field('birth_year') != "" ) {
        
        //nycago_log( "[get_person_dates] birth_year: ".$pod->display('birth_year') );
        
        if ( $pod->field('death_year') && $pod->field('death_year') != "" ) {
            $info .= "(".$pod->display('birth_year')."-".$pod->display('death_year').")";
            //nycago_log( "[get_person_dates] death_year: ".$pod->display('death_year') );
        } else {
            $info .= "(b. ".$pod->display('birth_year').")";
        }
        
    } else if ( $pod->field('dates') && $pod->field('dates') != "" ) {
        
        $info .= "(".$pod->display('dates').")";
        
    } 
    
    if ( $info != "") {
        if ( $styled == true ) {
            $info = ' <span class="person_dates">'.$info.'</span>';
        } else {
            $info = ' '.$info; // add space before dates str
        }
    }
    
    return $info;
    
}



/*** MISC UTILITY/HELPER FUNCTIONS ***/

// Convert the time string HH:MM:SS to number of seconds (for flowplayer cuepoints &c.)
function xtime_to_seconds($str_time){
	
	/*
	// Method #1
	//$str_time = "23:12:95";
	$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
	sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
	$num_seconds = $hours * 3600 + $minutes * 60 + $seconds;
	
	// Method #2
	//$str_time = "2:50";
	sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
	$num_seconds = isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;
	
	// Method #3
	//$str_time = '21:30:10';
	$num_seconds = strtotime("1970-01-01 $str_time UTC");
	*/
	// Method #4
	//$str_time = '21:30:10';
	$parsed = date_parse($str_time);
	$num_seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
	
	return $num_seconds;
	
}

//
function make_link( $url, $linktext, $class = null, $target = null) {
	
	// TODO: sanitize URL?
	$link = '<a href="'.$url.'"';
	if ($target !== null ) { $link .= ' target="'.$target.'"'; }
    if ($class !== null ) { $link .= ' class="'.$class.'"'; }
	$link .= '>'.$linktext.'</a>';
	//return '<a href="'.$url.'">'.$linktext.'</a>';
	
	return $link;
}


/*** Archive Pages ***/

function nycago_theme_archive_title( $title ) {
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    }
  
    return $title;
}
 
add_filter( 'get_the_archive_title', 'atc_theme_archive_title' );


// Remove menus selectively for plugins which rely on manage_options capability
//add_action( 'admin_menu', 'remove_admin_menu_items', 999 );
function remove_admin_menu_items() {

    $user = wp_get_current_user();
    if ( !in_array( 'administrator', (array) $user->roles ) ) {
        remove_menu_page('members'); // Tadlock Members plugin
        remove_menu_page('mfmmf'); // ManFisher footnotes plugin
        remove_menu_page('google-captcha-pro.php');
        remove_menu_page('wppusher');
        
        if ( !in_array( 'nycago_administrator', (array) $user->roles ) ) {
            remove_menu_page('tools.php'); // Tools
            remove_menu_page('options-general.php'); // Settings
            remove_menu_page('pmxi-admin-home'); // WP All Import
            remove_menu_page('wpdesk-helper');
            remove_menu_page('metaslider'); // no dice -- ??
            remove_submenu_page('index.php','metaslider-settings' ); // no dice -- ??
            //remove_submenu_page('admin.php','metaslider' ); //admin.php?page=metaslider -- no dice -- why?
            //remove_menu_page('admin.php?page=metaslider'); // :-(
            remove_menu_page('WP-Optimize');
            remove_menu_page('seed_csp4'); // admin.php?page=seed_csp4
        }
    }
    
 }

// Function to delete capabilities
// Based on http://chrisburbridge.com/delete-unwanted-wordpress-custom-capabilities/
//add_action( 'admin_init', 'clean_unwanted_caps' ); // tmp disabled -- this function need only ever run once per site per set of caps
function clean_unwanted_caps() {
	global $wp_roles;
	$delete_caps = array(
        
        //'edit_music', 'edit_others_music', 'delete_music', 'publish_music', 'read_music', 'read_private_music',
        //'edit_musics', 'edit_others_musics', 'delete_musics', 'publish_musics', 'read_musics', 'read_private_musics',
        
        //'edit_saurs', 'edit_others_saurs', 'delete_saurs', 'publish_saurs', 'read_private_saurs',
        //'edit_dinosaurs', 'edit_others_dinosaurs', 'delete_dinosaurs', 'publish_dinosaurs', 'read_private_dinosaurs',
        
        //'read_dev_wip', 'read_private_dev_wips', 'edit_dev_wip', 'edit_dev_wips', 'edit_others_dev_wips', 'edit_private_dev_wips', 'edit_published_dev_wips', 'delete_dev_wip', 'delete_dev_wips', 'delete_others_dev_wips', 'delete_private_dev_wips', 'delete_published_dev_wips', 'publish_dev_wips',
        
        //'assign_nycagodev_term', 'assign_nycagodev_terms', 'edit_nycagodev_term', 'edit_nycagodev_terms', 'delete_nycagodev_term', 'delete_nycagodev_terms', 'manage_nycagodev_terms',
        
        //'read_wipdev', 'edit_wipdev', 'read_private_wipdevs', 'edit_wipdevs', 'edit_others_wipdevs', 'edit_private_wipdevs', 'edit_published_wipdevs', 'publish_wipdevs', 'delete_wipdev', 'delete_wipdevs', 'delete_others_wipdevs', 'delete_private_wipdevs', 'delete_published_wipdevs',  
        
        //'smartslider', 'smartslider_config', 'smartslider_delete', 'smartslider_edit',
		
        //'nextend', 'nextend_config', 'nextend_visual_delete', 'nextend_visual_edit'
        
	 );
	foreach ($delete_caps as $cap) {
		foreach (array_keys($wp_roles->roles) as $role) {
			$wp_roles->remove_cap($role, $cap);
		}
	}
}


?>