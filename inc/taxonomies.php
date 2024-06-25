<?php

defined( 'ABSPATH' ) or die( 'Nope!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

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

// Custom Taxonomy: Organ Tag
function nycago_register_taxonomy_organ_tag() {
    $cap = 'organ';
    $labels = array(
        'name'              => _x( 'Organ Tags', 'taxonomy general name' ),
        'singular_name'     => _x( 'Organ Tag', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Organ Tags' ),
        'all_items'         => __( 'All Organ Tags' ),
        'parent_item'       => __( 'Parent Organ Tag' ),
        'parent_item_colon' => __( 'Parent Organ Tag:' ),
        'edit_item'         => __( 'Edit Organ Tag' ),
        'update_item'       => __( 'Update Organ Tag' ),
        'add_new_item'      => __( 'Add New Organ Tag' ),
        'new_item_name'     => __( 'New Organ Tag Name' ),
        'menu_name'         => __( 'Organ Tags' ),
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
        'rewrite'           => [ 'slug' => 'organ_tag' ],
    );
    register_taxonomy( 'action_type', [ 'organ' ], $args );
}
add_action( 'init', 'nycago_register_taxonomy_organ_tag' );


?>