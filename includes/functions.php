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

	if( class_exists( 'EDD_Software_Licensing' ) ) {
		$conditions['license-upgrade'] = __( 'License Upgrade', 'edd-conditional-emails' );
		$conditions['license-renewal'] = __( 'License Renewal', 'edd-conditional-emails' );
	}

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
		case 'license-upgrade' :
			$status = __( 'License Upgrade', 'edd-conditional-emails' );
			break;
		case 'license-renewal' :
			$status = __( 'License Renewal', 'edd-conditional-emails' );
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


/**
 * Get a list of available template tags
 *
 * @since       1.0.0
 * @return      string $tags The available template tags
 */
function edd_conditional_emails_get_template_tags() {
	$tags  = '<p class="edd-conditional-email-tags-list">';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount">{download_list} - ' . __( 'A list of download links for each download purchased', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount">{file_urls} - ' . __( 'A plain-text list of download URLs for each download purchased', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '{name} - ' . __( 'The buyer\'s first name', 'edd-conditional-emails' ) . '<br>';
	$tags .= '{fullname} - ' . __( 'The buyer\'s full name, first and last', 'edd-conditional-emails' ) . '<br>';
	$tags .= '{username} - ' . __( 'The buyer\'s user name on the site, if they registered an account', 'edd-conditional-emails' ) . '<br>';
	$tags .= '{user_email} - ' . __( 'The buyer\'s email address', 'edd-conditional-emails' ) . '<br>';
	$tags .= '{billing_address} - ' . __( 'The buyer\'s billing address', 'edd-conditional-emails' ) . '<br>';
	$tags .= '{date} - ' . __( 'The date of the purchase', 'edd-conditional-emails' ) . '<br>';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount show-on-license-upgrade show-on-license-renewal">{subtotal} - ' . __( 'The price of the purchase before taxes', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount show-on-license-upgrade show-on-license-renewal">{tax} - ' . __( 'The taxed amount of the purchase', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount show-on-license-upgrade show-on-license-renewal">{price} - ' . __( 'The total price of the purchase', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount show-on-license-upgrade show-on-license-renewal">{payment_id} - ' . __( 'The unique ID number for this purchase', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount show-on-license-upgrade show-on-license-renewal">{receipt_id} - ' . __( 'The unique ID number for this purchase receipt', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount show-on-license-upgrade show-on-license-renewal">{payment_method} - ' . __( 'The method of payment used for this purchase', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '{sitename} - ' . __( 'Your site name', 'edd-conditional-emails' ) . '<br>';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount show-on-license-upgrade show-on-license-renewal">{receipt_link} - ' . __( 'Adds a link so users can view their receipt directly on your website if they are unable to view it in the browser correctly.', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '<span class="show-on-purchase-status show-on-purchase-amount">{discount_codes} - ' . __( 'Adds a list of any discount codes applied to this purchase', 'edd-conditional-emails' ) . '<br></span>';
	$tags .= '{ip_address} - ' . __( 'The buyer\'s IP Address', 'edd-conditional-emails' ) . '<br>';

	if( class_exists( 'EDD_Software_Licensing' ) ) {
		$tags .= '<span class="show-on-purchase-status show-on-purchase-amount show-on-license-renewal">{license_keys} - ' . __( 'Show all purchased licenses', 'edd-conditional-emails' ) . '<br></span>';
		$tags .= '<span class="show-on-license-upgrade">{license_key} - ' . __( 'Show the license key for this upgrade', 'edd-conditional-emails' ) . '<br></span>';
		$tags .= '<span class="show-on-license-upgrade">{license_product} - ' . __( 'Show the product for this upgrade', 'edd-conditional-emails' ) . '<br></span>';
	}

	if( class_exists( 'CFM_Emails' ) ) {
		$tags .= '<br>' . __( 'CFM fields can be added by entering their meta key name as an email tag.', 'edd-conditional-emails' );
	}

	$tags .= '</p>';

	return $tags;
}