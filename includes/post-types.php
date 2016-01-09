<?php
/**
 * Post type functions
 *
 * @package     EDD\ConditionalEmails
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register the email post type
 *
 * @since       1.0.0
 * @return      void
 */
function edd_conditional_emails_register_cpt() {
	$labels = array(
		'name'               => _x( 'Emails', 'post type general name', 'edd-conditional-emails' ),
		'singular_name'      => _x( 'Email', 'post type singular name', 'edd-conditional-emails' ),
		'add_new'            => __( 'Add New', 'edd-conditional-emails' ),
		'add_new_item'       => __( 'Add New Email', 'edd-conditional-emails' ),
		'edit_item'          => __( 'Edit Email', 'edd-conditional-emails' ),
		'new_item'           => __( 'New Email', 'edd-conditional-emails' ),
		'all_items'          => __( 'All Emails', 'edd-conditional-emails' ),
		'view_item'          => __( 'View Email', 'edd-conditional-emails' ),
		'search_items'       => __( 'Search Emails', 'edd-conditional-emails' ),
		'not_found'          => __( 'No Emails found', 'edd-conditional-emails' ),
		'not_found_in_trash' => __( 'No Emails found in Trash', 'edd-conditional-emails' ),
		'menu_name'          => __( 'Conditional Emails', 'edd-conditional-emails' )
	);

	$args = array(
		'labels'       => apply_filters( 'edd_conditional_emails_labels', $labels ),
		'public'       => false,
		'show_in_menu' => false,
		'query_var'    => false,
		'hierarchical' => false,
		'supports'     => apply_filters( 'edd_conditional_emails_supports', array( 'title', 'editor' ) ),
		'can_export'   => true
	);

	register_post_type( 'conditional-email', $args );
}
add_action( 'init', 'edd_conditional_emails_register_cpt', 1 );
