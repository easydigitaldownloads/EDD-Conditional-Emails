<?php
/**
 * Admin pages
 *
 * @package     EDD\ConditionalEmails\Admin\Pages
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Add admin pages
 *
 * @since       1.0.0
 * @return      void
 */
function edd_conditional_emails_admin_pages() {
	add_submenu_page( null, __( 'Conditional Email', 'edd-conditional-emails' ), __( 'Conditional Email', 'edd-conditional-emails' ), 'manage_shop_settings', 'edd-conditional-email', 'edd_conditional_emails_render_edit' );
}
add_action( 'admin_menu', 'edd_conditional_emails_admin_pages', 10 );
