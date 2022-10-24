<?php

defined( 'ABSPATH' ) or die( 'Nope!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/*** Taxonomies for GENERAL & ADMIN USE ***/

// Custom Taxonomy: Admin Tag
function nycago_register_taxonomy_admin_tag() {
    //$cap = 'event_program';
    $labels = array(
        'name'              => _x( 'Admin Tags', 'taxonomy general name' ),
        'singular_name'     => _x( 'Admin Tag', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Admin Tags' ),
        'all_items'         => __( 'All Admin Tags' ),
        'parent_item'       => __( 'Parent Admin Tag' ),
        'parent_item_colon' => __( 'Parent Admin Tag:' ),
        'edit_item'         => __( 'Edit Admin Tag' ),
        'update_item'       => __( 'Update Admin Tag' ),
        'add_new_item'      => __( 'Add New Admin Tag' ),
        'new_item_name'     => __( 'New Admin Tag Name' ),
        'menu_name'         => __( 'Admin Tags' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        /*'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),*/
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'admin_tag' ],
    );
    register_taxonomy( 'admin_tag', [ 'admin_note', 'bible_book', 'collect', 'data_table', 'edition', 'ensemble', 'event', 'event-recurring', 'event_series', 'lectionary', 'liturgical_date', 'liturgical_date_calc', 'location', 'music_list', 'page', 'person', 'post', 'product', 'psalms_of_the_day', 'publication', 'publisher', 'reading', 'repertoire', 'sermon', 'sermon_series' ], $args );
}
add_action( 'init', 'nycago_register_taxonomy_admin_tag' );

// Custom Taxonomy: Admin Notes Category
function nycago_register_taxonomy_adminnote_category() {
    //$cap = 'XXX';
    $labels = array(
        'name'              => _x( 'Admin Note Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'Admin Note Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Admin Note Categories' ),
        'all_items'         => __( 'All Admin Note Categories' ),
        'parent_item'       => __( 'Parent Admin Note Category' ),
        'parent_item_colon' => __( 'Parent Admin Note Category:' ),
        'edit_item'         => __( 'Edit Admin Note Category' ),
        'update_item'       => __( 'Update Admin Note Category' ),
        'add_new_item'      => __( 'Add New Admin Note Category' ),
        'new_item_name'     => __( 'New Admin Note Category Name' ),
        'menu_name'         => __( 'Admin Note Categories' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        /*'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),*/
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'adminnote_category' ],
    );
    register_taxonomy( 'adminnote_category', [ 'admin_note' ], $args );
}
add_action( 'init', 'nycago_register_taxonomy_adminnote_category' );

// Custom Taxonomy: Data Table
function nycago_register_taxonomy_data_table() {
    //$cap = 'XXX';
    $labels = array(
        'name'              => _x( 'Data Tables', 'taxonomy general name' ),
        'singular_name'     => _x( 'Data Table', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Data Tables' ),
        'all_items'         => __( 'All Data Tables' ),
        'parent_item'       => __( 'Parent Data Table' ),
        'parent_item_colon' => __( 'Parent Data Table:' ),
        'edit_item'         => __( 'Edit Data Table' ),
        'update_item'       => __( 'Update Data Table' ),
        'add_new_item'      => __( 'Add New Data Table' ),
        'new_item_name'     => __( 'New Data Table Name' ),
        'menu_name'         => __( 'Data Tables' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        /*'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),*/
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'data_table' ],
    );
    register_taxonomy( 'data_table', [ 'admin_note' ], $args );
}
add_action( 'init', 'nycago_register_taxonomy_data_table' );

// Custom Taxonomy: Query Tag
function nycago_register_taxonomy_query_tag() {
    //$cap = 'XXX';
    $labels = array(
        'name'              => _x( 'Query Tags', 'taxonomy general name' ),
        'singular_name'     => _x( 'Query Tag', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Query Tags' ),
        'all_items'         => __( 'All Query Tags' ),
        'parent_item'       => __( 'Parent Query Tag' ),
        'parent_item_colon' => __( 'Parent Query Tag:' ),
        'edit_item'         => __( 'Edit Query Tag' ),
        'update_item'       => __( 'Update Query Tag' ),
        'add_new_item'      => __( 'Add New Query Tag' ),
        'new_item_name'     => __( 'New Query Tag Name' ),
        'menu_name'         => __( 'Query Tags' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        /*'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),*/
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'query_tag' ],
    );
    register_taxonomy( 'query_tag', [ 'admin_note' ], $args );
}
add_action( 'init', 'nycago_register_taxonomy_query_tag' );

// Custom Taxonomy: Season
function nycago_register_taxonomy_season() {
    //$cap = 'lectionary';
    $labels = array(
        'name'              => _x( 'Seasons', 'taxonomy general name' ),
        'singular_name'     => _x( 'Season', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Seasons' ),
        'all_items'         => __( 'All Seasons' ),
        'parent_item'       => __( 'Parent Season' ),
        'parent_item_colon' => __( 'Parent Season:' ),
        'edit_item'         => __( 'Edit Season' ),
        'update_item'       => __( 'Update Season' ),
        'add_new_item'      => __( 'Add New Season' ),
        'new_item_name'     => __( 'New Season Name' ),
        'menu_name'         => __( 'Seasons NEW' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        /*'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),*/
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'season' ],
    );
    register_taxonomy( 'season', [ 'collect', 'liturgical_date', 'repertoire' ], $args );
}
//add_action( 'init', 'nycago_register_taxonomy_season' );

/*** Taxonomies for PEOPLE ***/

// Custom Taxonomy: People Category
function nycago_register_taxonomy_people_category() {
    //$cap = 'person';
    $labels = array(
        'name'              => _x( 'People Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'People Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search People Categories' ),
        'all_items'         => __( 'All People Categories' ),
        'parent_item'       => __( 'Parent People Category' ),
        'parent_item_colon' => __( 'Parent People Category:' ),
        'edit_item'         => __( 'Edit People Category' ),
        'update_item'       => __( 'Update People Category' ),
        'add_new_item'      => __( 'Add New People Category' ),
        'new_item_name'     => __( 'New People Category Name' ),
        'menu_name'         => __( 'People Categories' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        /*'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),*/
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'people_category' ],
    );
    register_taxonomy( 'people_category', [ 'person' ], $args );
}
add_action( 'init', 'nycago_register_taxonomy_people_category' );

/*** Taxonomies for VENUES ***/

// Custom Taxonomy: Venue Category
function nycago_register_taxonomy_venue_category() {
    $cap = 'venue';
    $labels = array(
        'name'              => _x( 'Venue Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'Venue Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Venue Categories' ),
        'all_items'         => __( 'All Venue Categories' ),
        'parent_item'       => __( 'Parent Venue Category' ),
        'parent_item_colon' => __( 'Parent Venue Category:' ),
        'edit_item'         => __( 'Edit Venue Category' ),
        'update_item'       => __( 'Update Venue Category' ),
        'add_new_item'      => __( 'Add New Venue Category' ),
        'new_item_name'     => __( 'New Venue Category Name' ),
        'menu_name'         => __( 'Venue Categories' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'venue_category' ],
    );
    register_taxonomy( 'venue_category', [ 'venue' ], $args );
}
add_action( 'init', 'nycago_register_taxonomy_venue_category' );

/*** Taxonomies for ORGANS ***/

// Custom Taxonomy: Action Type
function nycago_register_taxonomy_action_type() {
    $cap = 'organ';
    $labels = array(
        'name'              => _x( 'Action Types', 'taxonomy general name' ),
        'singular_name'     => _x( 'Action Type', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Action Types' ),
        'all_items'         => __( 'All Action Types' ),
        'parent_item'       => __( 'Parent Action Type' ),
        'parent_item_colon' => __( 'Parent Action Type:' ),
        'edit_item'         => __( 'Edit Action Type' ),
        'update_item'       => __( 'Update Action Type' ),
        'add_new_item'      => __( 'Add New Action Type' ),
        'new_item_name'     => __( 'New Action Type Name' ),
        'menu_name'         => __( 'Action Types' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'action_type' ],
    );
    register_taxonomy( 'action_type', [ 'organ' ], $args );
}
add_action( 'init', 'nycago_register_taxonomy_action_type' );


/*** Taxonomies for EVENT PROGRAMS ***/

// Custom Taxonomy: Person Role
function nycago_register_taxonomy_person_role() {
    $cap = 'event_program';
    $labels = array(
        'name'              => _x( 'Personnel Roles', 'taxonomy general name' ),
        'singular_name'     => _x( 'Personnel Role', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Personnel Roles' ),
        'all_items'         => __( 'All Personnel Roles' ),
        'parent_item'       => __( 'Parent Personnel Role' ),
        'parent_item_colon' => __( 'Parent Personnel Role:' ),
        'edit_item'         => __( 'Edit Personnel Role' ),
        'update_item'       => __( 'Update Personnel Role' ),
        'add_new_item'      => __( 'Add New Personnel Role' ),
        'new_item_name'     => __( 'New Personnel Role Name' ),
        'menu_name'         => __( 'Personnel Roles' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'show_admin_column' => true,
        'meta_box_cb'       => false,
        /*'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),*/
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'person_role' ],
    );
    register_taxonomy( 'person_role', [ 'event', 'event_program' ], $args ); // 'person', 
}
add_action( 'init', 'nycago_register_taxonomy_person_role' );

// Custom Taxonomy: Program Label
function nycago_register_taxonomy_program_label() {
    $cap = 'event_program';
    $labels = array(
        'name'              => _x( 'Program Labels', 'taxonomy general name' ),
        'singular_name'     => _x( 'Program Label', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Program Labels' ),
        'all_items'         => __( 'All Program Labels' ),
        'parent_item'       => __( 'Parent Program Label' ),
        'parent_item_colon' => __( 'Parent Program Label:' ),
        'edit_item'         => __( 'Edit Program Label' ),
        'update_item'       => __( 'Update Program Label' ),
        'add_new_item'      => __( 'Add New Program Label' ),
        'new_item_name'     => __( 'New Program Label Name' ),
        'menu_name'         => __( 'Program Labels' ),
    );
    $args = array(
        'labels'            => $labels,
        'description'          => '',
        'public'               => true,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'show_admin_column' => true,
        'meta_box_cb'       => false,
        /*'capabilities'         => array(
            'manage_terms'  =>   'manage_'.$cap.'_terms',
            'edit_terms'    =>   'edit_'.$cap.'_terms',
            'delete_terms'  =>   'delete_'.$cap.'_terms',
            'assign_terms'  =>   'assign_'.$cap.'_terms',
        ),*/
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'program_label' ],
    );
    register_taxonomy( 'program_label', [ 'event', 'event_program' ], $args );
}
add_action( 'init', 'nycago_register_taxonomy_program_label' );


?>