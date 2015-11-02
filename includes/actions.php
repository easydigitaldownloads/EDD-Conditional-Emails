<?php
/**
 * Actions
 *
 * @package     EDD\ConditionalEmails\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Process emails on purchase status change
 *
 * @since       1.0.0
 * @param       int $payment_id The ID of this payment
 * @param       string $new_status The new status of this payment
 * @param       string $old_status The old status of this payment
 * @return      void
 */
function edd_conditional_emails_status_change_email( $payment_id, $new_status, $old_status ) {
	$emails = get_posts(
		array(
			'posts_per_page'    => 999999,
			'post_type'         => 'conditional-email',
			'post_status'       => 'publish'
		)
	);

	if( $emails ) {
		foreach( $emails as $key => $email ) {
			$meta = get_post_meta( $email->ID, '_edd_conditional_email', true );

			if( $meta['condition'] == 'payment-status' || $meta['condition'] == 'purchase-status' ) {
				if( $meta['status_from'] == $old_status && $meta['status_to'] == $new_status ) {
					if( $meta['send_to'] == 'user' ) {
						$email_to = esc_attr( edd_get_payment_user_email( $payment_id ) );
					} elseif( $meta['send_to'] == 'admin' ) {
						$email_to = get_option( 'admin_email' );
					} elseif( $meta['send_to'] == 'custom' ) {
						if( ! isset( $meta['custom_email'] ) || $meta['custom_email'] == '' ) {
							$email_to = get_option( 'admin_email' );
						} else {
							$email_to = esc_attr( $meta['custom_email'] );
						}
					}

					$message = edd_do_email_tags( $meta['message'], $payment_id );

					if( class_exists( 'EDD_Emails' ) ) {
						EDD()->emails->send( $email_to, $meta['subject'], $message );
					} else {
						$from_name  = get_bloginfo( 'name' );
						$from_email = get_bloginfo( 'admin_email' );
						$headers    = 'From: ' . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
						$headers   .= 'Reply-To: ' . $from_email . "\r\n";

						wp_mail( $email_to, $meta['subject'], $message, $headers );
					}
				}
			}
		}
	}
}
add_action( 'edd_update_payment_status', 'edd_conditional_emails_status_change_email', 100, 3 );


/**
 * Process emails on minimum purchase amount
 *
 * @since       1.0.3
 * @param       int $payment_id The ID of a given payment
 * @return      void
 */
function edd_conditional_emails_purchase_amount( $payment_id ) {
	$value  = edd_get_cart_total();
	$emails = get_posts(
		array(
			'posts_per_page' => 999999,
			'post_type'      => 'conditional-email',
			'post_status'    => 'publish'
		)
	);

	if( $emails ) {
		foreach( $emails as $key => $email ) {
			$meta = get_post_meta( $email->ID, '_edd_conditional_email', true );

			if( $meta['condition'] == 'purchase-amount' ) {
				if( $value >= (float) $meta['minimum_amount'] ){
					if( $meta['send_to'] == 'user' ) {
						$email_to = esc_attr( edd_get_payment_user_email( $payment_id ) );
					} elseif( $meta['send_to'] == 'admin' ) {
						$email_to = get_option( 'admin_email' );
					} elseif( $meta['send_to'] == 'custom' ) {
						if( ! isset( $meta['custom_email'] ) || $meta['custom_email'] == '' ) {
							$email_to = get_option( 'admin_email' );
						} else {
							$email_to = esc_attr( $meta['custom_email'] );
						}
					}

					$message = edd_do_email_tags( $meta['message'], $payment_id );

					if( class_exists( 'EDD_Emails' ) ) {
						EDD()->emails->send( $email_to, $meta['subject'], $message );
					} else {
						$from_name  = get_bloginfo( 'name' );
						$from_email = get_bloginfo( 'admin_email' );
						$headers    = 'From: ' . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
						$headers   .= 'Reply-To: ' . $from_email . "\r\n";

						wp_mail( $email_to, $meta['subject'], $message, $headers );
					}
				}
			}
		}
	}
}
add_action( 'edd_complete_purchase', 'edd_conditional_emails_purchase_amount', 100, 1 );
