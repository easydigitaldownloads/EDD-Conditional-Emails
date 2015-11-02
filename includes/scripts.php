<?php
/**
 * Scripts
 *
 * @package     EDD\ConditionalEmails\Scripts
 * @scripts     1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function edd_conditional_emails_admin_scripts( $hook ) {
	if( $hook == 'download_page_edd-conditional-email' ) {
		wp_enqueue_style( 'edd-conditional-emails', EDD_CONDITIONAL_EMAILS_URL . 'assets/css/admin.css', array(), EDD_CONDITIONAL_EMAILS_VER );
		wp_enqueue_script( 'edd-conditional-emails', EDD_CONDITIONAL_EMAILS_URL . 'assets/js/admin.js', array( 'jquery' ), EDD_CONDITIONAL_EMAILS_VER );
	}
}
add_action( 'admin_enqueue_scripts', 'edd_conditional_emails_admin_scripts', 100 );
