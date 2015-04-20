<?php
/**
 * Scripts
 *
 * @package     EDD\ConditionalEmails\Scripts
 * @scripts     1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Get an array of email conditions
 *
 * @since       1.0.0
 * @return      array $conditions The available conditions
 */
function edd_conditional_emails_conditions() {
    $conditions = array(
        'purchase-status'   => __( 'Purchase Status Change', 'edd-conditional-emails' )
    );

    return apply_filters( 'edd_conditional_emails_conditions', $conditions );
}


/**
 * Get the status line for a given email
 *
 * @since       1.0.0
 * @param       array $meta The meta data for a given email
 * @return      string $status The status line
 */
function edd_conditional_emails_get_status( $meta = array() ) {
    switch( $meta['condition'] ) {
        case 'payment-status' :
            $status = sprintf( __( 'Status change (%1$s-%2$s)', 'edd-conditional-emails' ), $meta['status_from'], $meta['status_to'] );
            break;
        default :
            $status = __( 'Condition unknown', 'edd-conditional-emails' );
            break;
    }
    
    return apply_filters( 'edd_conditional_email_status', $status, $meta );
}
