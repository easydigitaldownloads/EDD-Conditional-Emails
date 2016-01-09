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
 * Get an array of email conditions
 *
 * @since       1.0.0
 * @return      array $conditions The available conditions
 */
function edd_conditional_emails_conditions() {
	$conditions = array(
		'purchase-status' => __( 'Purchase Status Change', 'edd-conditional-emails' ),
		'abandoned-cart'  => __( 'Abandoned Cart', 'edd-conditional-emails' ),
		'purchase-amount' => __( 'Purchase Amount At Least', 'edd-conditional-emails' ),
		'pending-payment' => __( 'Pending Payment', 'edd-conditional-emails' )
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
		case 'purchase-status' :
		case 'payment-status' :
			$status = sprintf( __( 'Status change (%1$s-%2$s)', 'edd-conditional-emails' ), $meta['status_from'], $meta['status_to'] );
			break;
		case 'abandoned-cart' :
			$status = __( 'Abandoned cart', 'edd-conditional-emails' );
			break;
		case 'purchase-amount' :
			$status = sprintf( __( 'Purchase amount at least (%1$s)', 'edd-conditional-emails' ), $meta['minimum_amount'] );
			break;
		case 'pending-payment' :
			$status = __( 'Pending payment', 'edd-conditional-emails' );
			break;
		default :
			$status = __( 'Condition unknown', 'edd-conditional-emails' );
			break;
	}

	return apply_filters( 'edd_conditional_email_status', $status, $meta );
}


/**
 * Get the email type for a given email
 *
 * @since       1.0.1
 * @param       array $meta The meta data for a given email
 * @return      string $email The requested email data
 */
function edd_conditional_emails_get_email_type( $meta = array() ) {
	if( ! isset( $meta['send_to'] ) || $meta['send_to'] == '' ) {
		$meta['send_to'] = 'user';
	}

	switch( $meta['send_to'] ) {
		case 'user' :
			$email = __( 'User', 'edd-conditional-emails' );
			break;
		case 'admin' :
			$email = sprintf( __( 'Site Admin (%s)', 'edd-conditional-emails' ), get_option( 'admin_email' ) );
			break;
		case 'custom' :
			$email = sprintf( __( 'Custom (%s)', 'edd-conditional-emails' ), ( $meta['custom_email'] ? esc_attr( $meta['custom_email'] ) : get_option( 'admin_email' ) ) );
			break;
		default:
			$email = __( 'User', 'edd-conditional-emails' );
			break;
	}

	return apply_filters( 'edd_conditional_emails_get_email_type', $email, $meta );
}


/**
 * Get the email for a given email
 *
 * @since       1.0.4
 * @param       int $payment_id The ID of the payment to retrieve the email for
 * @param       array $meta The meta data for a given email
 * @return      string $email The requested email address
 */
function edd_conditional_emails_get_email( $payment_id, $meta = array() ) {
	if( ! isset( $meta['send_to'] ) || $meta['send_to'] == '' ) {
		$meta['send_to'] = 'user';
	}

	switch( $meta['send_to'] ) {
		case 'user' :
			$email = esc_attr( edd_get_payment_user_email( $payment_id ) );
			break;
		case 'admin' :
			$email = get_option( 'admin_email' );
			break;
		case 'custom' :
			$email = ( ! isset( $meta['custom_email'] ) || $meta['custom_email'] == '' ) ? get_option( 'admin_email' ) : esc_attr( $meta['custom_email'] );
			break;
		default :
			$email = get_option( 'admin_email' );
			break;
	}

	return $email;
}