<?php

defined( 'ABSPATH' ) or die( 'Nope!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/*** GENERAL/ADMIN ***/

// Admin Note
function nycago_register_post_type_admin_note() {

	$labels = array(
		'name' => __( 'Admin Notes', 'nycago' ),
		'singular_name' => __( 'Admin Note', 'nycago' ),
		'add_new' => __( 'New Admin Note', 'nycago' ),
		'add_new_item' => __( 'Add New Admin Note', 'nycago' ),
		'edit_item' => __( 'Edit Admin Note', 'nycago' ),
		'new_item' => __( 'New Admin Note', 'nycago' ),
		'view_item' => __( 'View Admin Notes', 'nycago' ),
		'search_items' => __( 'Search Admin Notes', 'nycago' ),
		'not_found' =>  __( 'No Admin Notes Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Admin Notes found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'admin_note' ),
        'capability_type' => array('admin_note', 'admin_notes'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	'menu_icon'          => 'dashicons-info-outline',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'adminnote_category', 'admin_tag', 'data_table', 'query_tag', 'admin_tag' ),
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'admin_note', $args );
	
}
add_action( 'init', 'nycago_register_post_type_admin_note' );


/*** PEOPLE ***/

// Person
function nycago_register_post_type_person() {

	$labels = array(
		'name' => __( 'People', 'nycago' ),
		'singular_name' => __( 'Person', 'nycago' ),
		'add_new' => __( 'New Person', 'nycago' ),
		'add_new_item' => __( 'Add New Person', 'nycago' ),
		'edit_item' => __( 'Edit Person', 'nycago' ),
		'new_item' => __( 'New Person', 'nycago' ),
		'view_item' => __( 'View Person', 'nycago' ),
		'search_items' => __( 'Search People', 'nycago' ),
		'not_found' =>  __( 'No People Found', 'nycago' ),
		'not_found_in_trash' => __( 'No People found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'person' ),
        'capability_type' => array('person', 'people'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	'menu_icon'          => 'dashicons-groups',
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'people_category', 'person_role', 'admin_tag' ),
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'person', $args );
	
}
add_action( 'init', 'nycago_register_post_type_person' );

/*** VENUES ***/

// Venue
function nycago_register_post_type_venue() {

	$labels = array(
		'name' => __( 'Venues', 'nycago' ),
		'singular_name' => __( 'Venue', 'nycago' ),
		'add_new' => __( 'New Venue', 'nycago' ),
		'add_new_item' => __( 'Add New Venue', 'nycago' ),
		'edit_item' => __( 'Edit Venue', 'nycago' ),
		'new_item' => __( 'New Venue', 'nycago' ),
		'view_item' => __( 'View Venues', 'nycago' ),
		'search_items' => __( 'Search Venues', 'nycago' ),
		'not_found' =>  __( 'No Venues Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Venues found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'venue' ),
        'capability_type' => array('venue', 'venues'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	'menu_icon'          => 'dashicons-admin-multisite',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'admin_tag', 'venue_category' ), //'venue_category', 'people_tag', 
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'venue', $args );
	
}
add_action( 'init', 'nycago_register_post_type_venue' );

// Address
function nycago_register_post_type_address() {

	$labels = array(
		'name' => __( 'Addresses', 'nycago' ),
		'singular_name' => __( 'Address', 'nycago' ),
		'add_new' => __( 'New Address', 'nycago' ),
		'add_new_item' => __( 'Add New Address', 'nycago' ),
		'edit_item' => __( 'Edit Address', 'nycago' ),
		'new_item' => __( 'New Address', 'nycago' ),
		'view_item' => __( 'View Addresses', 'nycago' ),
		'search_items' => __( 'Search Addresses', 'nycago' ),
		'not_found' =>  __( 'No Addresses Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Addresses found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=venue',
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'address' ),
        'capability_type' => array('venue', 'venues'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	//'menu_icon'          => 'dashicons-welcome-write-blog',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'admin_tag' ), //'people_category', 'people_tag', 
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'address', $args );
	
}
//add_action( 'init', 'nycago_register_post_type_address' ); // disabled as redundant w/ EM locations 08/20/22

/*** ORGANS ***/

// Organ
function nycago_register_post_type_organ() {

	$labels = array(
		'name' => __( 'Organs', 'nycago' ),
		'singular_name' => __( 'Organ', 'nycago' ),
		'add_new' => __( 'New Organ', 'nycago' ),
		'add_new_item' => __( 'Add New Organ', 'nycago' ),
		'edit_item' => __( 'Edit Organ', 'nycago' ),
		'new_item' => __( 'New Organ', 'nycago' ),
		'view_item' => __( 'View Organs', 'nycago' ),
		'search_items' => __( 'Search Organs', 'nycago' ),
		'not_found' =>  __( 'No Organs Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Organs found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'organ' ),
        'capability_type' => array('organ', 'organs'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	'menu_icon'          => 'dashicons-playlist-audio',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'admin_tag' ), //'people_category', 'people_tag', 
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'organ', $args );
	
}
add_action( 'init', 'nycago_register_post_type_organ' );

// Organ Builder
function nycago_register_post_type_builder() {

	$labels = array(
		'name' => __( 'Builders', 'nycago' ),
		'singular_name' => __( 'Builder', 'nycago' ),
		'add_new' => __( 'New Builder', 'nycago' ),
		'add_new_item' => __( 'Add New Builder', 'nycago' ),
		'edit_item' => __( 'Edit Builder', 'nycago' ),
		'new_item' => __( 'New Builder', 'nycago' ),
		'view_item' => __( 'View Builders', 'nycago' ),
		'search_items' => __( 'Search Builders', 'nycago' ),
		'not_found' =>  __( 'No Builders Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Builders found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=organ',
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'builder' ),
        'capability_type' => array('builder', 'builders'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	//'menu_icon'          => 'dashicons-welcome-write-blog',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'admin_tag' ), //'people_category', 'people_tag', 
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'builder', $args );
	
}
add_action( 'init', 'nycago_register_post_type_builder' );

// Division
function nycago_register_post_type_division() {

	$labels = array(
		'name' => __( 'Divisions', 'nycago' ),
		'singular_name' => __( 'Division', 'nycago' ),
		'add_new' => __( 'New Division', 'nycago' ),
		'add_new_item' => __( 'Add New Division', 'nycago' ),
		'edit_item' => __( 'Edit Division', 'nycago' ),
		'new_item' => __( 'New Division', 'nycago' ),
		'view_item' => __( 'View Divisions', 'nycago' ),
		'search_items' => __( 'Search Divisions', 'nycago' ),
		'not_found' =>  __( 'No Divisions Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Divisions found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=organ',
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'division' ),
        'capability_type' => array('organ', 'organs'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	//'menu_icon'          => 'dashicons-welcome-write-blog',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'admin_tag' ), //'people_category', 'people_tag', 
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'division', $args );
	
}
add_action( 'init', 'nycago_register_post_type_division' );

// Manual
function nycago_register_post_type_manual() {

	$labels = array(
		'name' => __( 'Manuals', 'nycago' ),
		'singular_name' => __( 'Manual', 'nycago' ),
		'add_new' => __( 'New Manual', 'nycago' ),
		'add_new_item' => __( 'Add New Manual', 'nycago' ),
		'edit_item' => __( 'Edit Manual', 'nycago' ),
		'new_item' => __( 'New Manual', 'nycago' ),
		'view_item' => __( 'View Manuals', 'nycago' ),
		'search_items' => __( 'Search Manuals', 'nycago' ),
		'not_found' =>  __( 'No Manuals Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Manuals found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=organ',
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'manual' ),
        'capability_type' => array('organ', 'organs'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	//'menu_icon'          => 'dashicons-welcome-write-blog',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'admin_tag' ), //'people_category', 'people_tag', 
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'manual', $args );
	
}
add_action( 'init', 'nycago_register_post_type_manual' );

// Stop
function nycago_register_post_type_stop() {

	$labels = array(
		'name' => __( 'Stops', 'nycago' ),
		'singular_name' => __( 'Stop', 'nycago' ),
		'add_new' => __( 'New Stop', 'nycago' ),
		'add_new_item' => __( 'Add New Stop', 'nycago' ),
		'edit_item' => __( 'Edit Stop', 'nycago' ),
		'new_item' => __( 'New Stop', 'nycago' ),
		'view_item' => __( 'View Stops', 'nycago' ),
		'search_items' => __( 'Search Stops', 'nycago' ),
		'not_found' =>  __( 'No Stops Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Stops found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=organ',
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'stop' ),
        'capability_type' => array('organ', 'organs'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	//'menu_icon'          => 'dashicons-welcome-write-blog',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'admin_tag' ), //'people_category', 'people_tag', 
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'stop', $args );
	
}
add_action( 'init', 'nycago_register_post_type_stop' );

/*** SOURCES ***/

// Source
function nycago_register_post_type_source() {

	$labels = array(
		'name' => __( 'Sources', 'nycago' ),
		'singular_name' => __( 'Source', 'nycago' ),
		'add_new' => __( 'New Source', 'nycago' ),
		'add_new_item' => __( 'Add New Source', 'nycago' ),
		'edit_item' => __( 'Edit Source', 'nycago' ),
		'new_item' => __( 'New Source', 'nycago' ),
		'view_item' => __( 'View Sources', 'nycago' ),
		'search_items' => __( 'Search Sources', 'nycago' ),
		'not_found' =>  __( 'No Sources Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Sources found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'source' ),
        'capability_type' => array('organ', 'organs'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	//'menu_icon'          => 'dashicons-welcome-write-blog',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
		'taxonomies' => array( 'admin_tag' ), //'people_category', 'people_tag', 
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'source', $args );
	
}
add_action( 'init', 'nycago_register_post_type_source' );

/*** MUSIC LIBRARY ***/

// Repertoire, aka Musical Work
function nycago_register_post_type_repertoire() {

	$labels = array(
		'name' => __( 'Musical Works', 'nycago' ),
		'singular_name' => __( 'Musical Work', 'nycago' ),
		'add_new' => __( 'New Musical Work', 'nycago' ),
		'add_new_item' => __( 'Add New Musical Work', 'nycago' ),
		'edit_item' => __( 'Edit Musical Work', 'nycago' ),
		'new_item' => __( 'New Musical Work', 'nycago' ),
		'view_item' => __( 'View Musical Works', 'nycago' ),
		'search_items' => __( 'Search Musical Works', 'nycago' ),
		'not_found' =>  __( 'No Musical Works Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Musical Works found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'repertoire' ),
        //'capability_type' => array('musicwork', 'repertoire'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	'menu_icon'          => 'dashicons-book',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //'editor', 
		'taxonomies' => array( 'repertoire_category', 'occasion', 'season', 'post_tag', 'admin_tag' ),
		'show_in_rest' => false, // i.e. false = use classic, not block editor
	);

	register_post_type( 'repertoire', $args );
	
}
add_action( 'init', 'nycago_register_post_type_repertoire' );

/*** SERMONS ***/

function nycago_register_post_type_sermon() {

	$labels = array(
		'name' => __( 'Sermons', 'nycago' ),
		'singular_name' => __( 'Sermon', 'nycago' ),
		'add_new' => __( 'New Sermon', 'nycago' ),
		'add_new_item' => __( 'Add New Sermon', 'nycago' ),
		'edit_item' => __( 'Edit Sermon', 'nycago' ),
		'new_item' => __( 'New Sermon', 'nycago' ),
		'view_item' => __( 'View Sermons', 'nycago' ),
		'search_items' => __( 'Search Sermons', 'nycago' ),
		'not_found' =>  __( 'No Sermons Found', 'nycago' ),
		'not_found_in_trash' => __( 'No Sermons found in Trash', 'nycago' ),
	);
	
	$args = array(
		'labels' => $labels,
	 	'public' => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'sermon' ),
        'capability_type' => array('sermon', 'sermons'),
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'hierarchical'       => false,
	 	'menu_icon'          => 'dashicons-welcome-write-blog',
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //'editor', 
		'taxonomies' => array( 'admin_tag' ), //'people_category', 'people_tag', 
		'show_in_rest' => true,    
	);

	register_post_type( 'sermon', $args );
	
}
//add_action( 'init', 'nycago_register_post_type_sermon' );



?>