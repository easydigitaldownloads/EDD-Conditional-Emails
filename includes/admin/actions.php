<?php
/**
 * Admin actions
 *
 * @package     EDD\ConditionalEmails\Admin\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Save emails 
 *
 * @since       1.0.0
 * @param       array $data
 * @return      void
 */
function edd_edit_conditional_email( $data ) {
    if( ! current_user_can( 'manage_shop_settings' ) ) {
        wp_die( __( 'You do not have permission to add conditional emails', 'edd-conditional-emails' ), __( 'Error', 'edd-conditional-emails' ), array( 'response' => 401 ) );
    }

    if( ! wp_verify_nonce( $data['edd-conditional-emails-nonce'], 'edd_conditional_emails_nonce' ) ) {
        wp_die( __( 'Nonce verification failed', 'edd-conditional-emails' ), __( 'Error', 'edd-conditional-emails' ), array( 'response' => 401 ) );
    }

    $message        = ( ! empty( $data['message'] ) ? wp_kses( $data['message'], wp_kses_allowed_html( 'post' ) ) : false );
    $condition      = isset( $data['condition'] ) ? esc_attr( $data['condition'] ) : 'payment-status';
    $status_from    = isset( $data['status_from'] ) ? esc_attr( $data['status_from'] ) : false;
    $status_to      = isset( $data['status_to'] ) ? esc_attr( $data['status_to'] ) : false;

    if( $condition == 'payment-status' ) {
        $subject = ( ! empty( $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : __( 'The status of your purchase has changed', 'edd-conditional-emails' ) );

        if( empty( $message ) ) {
            $message = 'Hello {name},

The status of your purchase has changed to ' . ucwords( $status_to ) . '.';
        }
    }

    if( $status_from == $status_to ) {
        echo '<div class="error settings-error"><p><strong>' . __( '"Status From" and "Status To" cannot be set to the same value!', 'edd-conditional-emails' ) . '</strong></p></div>';
        return;
    }

    $email_id   = ( ! empty( $data['email-id'] ) ? absint( $data['email-id'] ) : false );
    
    if( ! $email_id ) {
        $email_id = wp_insert_post(
            array(
                'post_title'    => $subject,
                'post_type'     => 'conditional-email',
                'post_status'   => 'publish'
            )
        );
    }

    $meta = array(
        'condition'     => $condition,
        'status_from'   => $status_from,
        'status_to'     => $status_to,
        'subject'       => $subject,
        'message'       => $message
    );

    update_post_meta( $email_id, '_edd_conditional_email', $meta );

    wp_safe_redirect( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=emails' ) );
    exit;
}
add_action( 'edd_edit_conditional_email', 'edd_edit_conditional_email' );


/**
 * Delete email
 *
 * @since       1.0.0
 * @param       array $data
 * @return      void
 */
function edd_delete_conditional_email( $data ) {
    if( ! current_user_can( 'manage_shop_settings' ) ) {
        wp_die( __( 'You do not have permission to delete conditional emails', 'edd-conditional-emails' ), __( 'Error', 'edd-conditional-emails' ), array( 'response' => 401 ) );
    }

    if( ! wp_verify_nonce( $data['_wpnonce'] ) ) {
        wp_die( __( 'Nonce verification failed', 'edd-conditional-emails' ), __( 'Error', 'edd-conditional-emails' ), array( 'response' => 401 ) );
    }
    
    if( empty( $data['email'] ) || ! isset( $data['email'] ) ) {
        wp_die( __( 'No email ID provided', 'edd-conditional-emails' ), __( 'Error', 'edd-conditional-emails' ), array( 'response' => 409 ) );
    }

    wp_delete_post( $data['email'] );

    wp_safe_redirect( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=emails' ) );
    exit;
}
add_action( 'edd_delete_conditional_email', 'edd_delete_conditional_email' );
