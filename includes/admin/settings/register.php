<?php
/**
 * Settings
 *
 * @package     EDD\ConditionalEmails\Admin\Settings
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Add settings section
 *
 * @since       1.0.1
 * @param       array $sections The existing extensions sections
 * @return      array The modified extensions settings
 */
function edd_conditional_emails_add_settings_section( $sections ) {
	$sections['conditional-emails'] = __( 'Conditional Emails', 'edd-conditional-emails' );

	return $sections;
}
add_filter( 'edd_settings_sections_emails', 'edd_conditional_emails_add_settings_section' );


/**
 * Register new settings in Emails
 *
 * @since       1.0.0
 * @param       array $settings The existing settings
 * @return      array The new settings
 */
function edd_conditional_emails_settings( $settings ) {
	$new_settings = array(
		'conditional-emails' => apply_filters( 'edd_conditional_emails_settings', array(
			array(
				'id'   => 'edd_conditional_emails_header',
				'name' => '<strong>' . __( 'Conditional Emails', 'edd-conditional-emails' ) . '</strong>',
				'desc' => '',
				'type' => 'header'
			),
			array(
				'id'   => 'conditional_emails_table',
				'name' => __( 'Emails', 'edd-conditional-emails' ),
				'desc' => __( 'Configure your emails', 'edd-conditional-emails' ),
				'type' => 'hook'
			)
		) )
	);

	return array_merge( $settings, $new_settings );
}
add_filter( 'edd_settings_emails', 'edd_conditional_emails_settings' );


/**
 * Display the email table
 *
 * @since       1.0.0
 * @return      void
 */
function edd_conditional_emails_table() {
	ob_start(); ?>
	<table id="edd-conditional-emails-table" class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th style="width: 350%; padding-left: 10px;" scope="col"><?php _e( 'Subject', 'edd-conditional-emails' ); ?></th>
				<th style="width: 200%; padding-left: 10px;" scope="col"><?php _e( 'Condition', 'edd-conditional-emails' ); ?></th>
				<th style="width: 200%; padding-left: 10px;" scope="col"><?php _e( 'Send To', 'edd-conditional-emails' ); ?></th>
				<th scope="col" style="padding-left: 10px;"><?php _e( 'Actions', 'edd-conditional-emails' ); ?></th>
			</tr>
		</thead>
		<?php
		$emails = get_posts(
			array(
				'posts_per_page' => 99999,
				'post_type'      => 'conditional-email',
				'post_status'    => 'publish'
			)
		);

		if( ! empty( $emails ) ) {
			$i = 1;
			foreach( $emails as $key => $email ) {
				$meta    = get_post_meta( $email->ID, '_edd_conditional_email', true );
				$status  = edd_conditional_emails_get_status( $meta );
				$send_to = edd_conditional_emails_get_email_type( $meta );

				echo '<tr' . ( $i % 2 == 0 ? ' class="alternate"' : '' ) . '>';
				echo '<td>' . esc_html( $meta['subject'] ) . '</td>';
				echo '<td>' . $status . '</td>';
				echo '<td>' . $send_to . '</td>';
				echo '<td>';
				echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=download&page=edd-conditional-email&edd-ca-action=edit-conditional-email&email=' . $email->ID ) ) . '" class="edd-edit-conditional-email" data-key="' . esc_attr( $email->ID ) . '">' . __( 'Edit', 'edd-conditional-emails' ) . '</a>&nbsp;|';
				echo '<a href="' . wp_nonce_url( admin_url( 'edit.php?post_type=download&page=edd-conditional-email&edd_action=delete_conditional_email&email=' . $email->ID ) ) . '" class="edd-delete">' . __( 'Delete', 'edd-conditional-emails' ) . '</a>';
				echo '</td>';
				echo '</tr>';

				$i++;
			}
		}
		?>
	</table>
	<p>
		<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=edd-conditional-email&edd-ca-action=add-conditional-email' ) ); ?>" class="button-secondary" id="edd-add-conditional-email"><?php _e( 'Add Email', 'edd-conditional-emails' ); ?></a>
	</p>
	<?php
	echo ob_get_clean();
}
add_action( 'edd_conditional_emails_table', 'edd_conditional_emails_table' );


/**
 * Render the add/edit screen
 *
 * @since       1.0.0
 * @return      void
 */
function edd_conditional_emails_render_edit() {
	$action   = isset( $_GET['edd-ca-action'] ) ? sanitize_text_field( $_GET['edd-ca-action'] ) : 'add-conditional-email';
	$email_id = ( isset( $_GET['email'] ) ? absint( $_GET['email'] ) : false );

	// Maybe get email
	if( $email_id ) {
		$meta = get_post_meta( $email_id, '_edd_conditional_email', true );
	} else {
		$meta = array();
	}

	$defaults = array(
		'condition'      => 'purchase-status',
		'status_from'    => false,
		'status_to'      => false,
		'minimum_amount' => '',
		'send_to'        => 'user',
		'custom_email'   => '',
		'subject'        => '',
		'message'        => ''
	);
	$meta = array_merge( $defaults, $meta );
	?>
	<div class="wrap">
		<h2><?php ( $action == 'edit-conditional-email' ? _e( 'Edit Email', 'edd-conditional-emails' ) : _e( 'Add Email', 'edd-conditional-emails' ) ); ?> <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=emails&section=conditional-emails' ) ); ?>" class="add-new-h2"><?php _e( 'Go Back', 'edd' ); ?></a></h2>

		<form id="edd-edit-conditional-email" action="" method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-conditional-email-condition"><?php _e( 'Email Condition', 'edd-conditional-emails' ); ?></label>
						</th>
						<td>
							<select name="condition" id="edd-conditional-email-condition">
								<?php
								foreach( edd_conditional_emails_conditions() as $value => $label ) {
									echo '<option value="' . esc_attr( $value ) . '" ' . selected( $value, $meta['condition'], false ) . '>' . esc_attr( $label ) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php _e( 'On what condition should this email be sent?', 'edd-conditional-emails' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-conditional-email-status-from"><?php _e( 'Status - Changed From', 'edd-conditional-emails' ); ?></label>
						</th>
						<td>
							<select name="status_from" id="edd-conditional-email-status-from">
								<?php
								foreach( edd_get_payment_statuses() as $key => $status ) {
									echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $meta['status_from'], false ) . '>' . esc_attr( $status ) . '</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-conditional-email-status-to"><?php _e( 'Status - Changed To', 'edd-conditional-emails' ); ?></label>
						</th>
						<td>
							<select name="status_to" id="edd-conditional-email-status-to">
								<?php
								foreach( edd_get_payment_statuses() as $key => $status ) {
									echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $meta['status_to'], false ) . '>' . esc_attr( $status ) . '</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-conditional-email-minimum-amount"><?php _e( 'Minimum Purchase Amount', 'edd-conditional-emails' ); ?></label>
						</th>
						<td>
							<input name="minimum_amount" id="edd-conditional-email-minimum-amount" type="text" value="<?php echo esc_attr( stripslashes( $meta['minimum_amount'] ) ); ?>" style="width: 100px" />
							<p class="description"><?php _e( 'The minimum amount that will trigger this email.', 'edd-conditional-emails' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-conditional-email-send-to"><?php _e( 'Send To', 'edd-conditional-emails' ); ?></label>
						</th>
						<td>
							<select name="send_to" id="edd-conditional-email-send-to">
								<option value="user"<?php echo selected( 'user', $meta['send_to'], false ); ?>><?php _e( 'User', 'edd-conditional-emails' ); ?></option>
								<option value="admin"<?php echo selected( 'admin', $meta['send_to'], false ); ?>><?php _e( 'Site Admin', 'edd-conditional-emails' ); ?></option>
								<option value="custom"<?php echo selected( 'custom', $meta['send_to'], false ); ?>><?php _e( 'Custom', 'edd-conditional-emails' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-conditional-email-custom-email"><?php _e( 'Email Address', 'edd-conditional-emails' ); ?></label>
						</th>
						<td>
							<input name="custom_email" id="edd-conditional-email-custom-email" type="text" value="<?php echo esc_attr( stripslashes( $meta['custom_email'] ) ); ?>" style="width: 300px;" />
							<p class="description"><?php _e( 'The email address this notice should be sent to.', 'edd-conditional-emails' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-conditional-email-subject"><?php _e( 'Email Subject', 'edd-conditional-emails' ); ?></label>
						</th>
						<td>
							<input name="subject" id="edd-conditional-email-subject" type="text" value="<?php echo esc_attr( stripslashes( $meta['subject'] ) ); ?>" style="width: 300px;" />
							<p class="description"><?php _e( 'The subject of this email.', 'edd-conditional-emails' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="edd-conditional-email-message"><?php _e( 'Email Message', 'edd-conditional-emails' ); ?></label>
						</th>
						<td>
							<?php wp_editor( wp_kses_post( wptexturize( $meta['message'] ) ), 'message', array( 'textarea_name' => 'message' ) ); ?>
							<p class="description"><?php _e( 'The email message to be sent. HTML is accepted. Available template tags:', 'edd-conditional-emails' ); ?></p>
							<p class="edd-conditional-email-tags-list"><?php echo edd_conditional_emails_get_template_tags(); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="hidden" name="email-id" value="<?php echo ( $email_id ? $email_id : 0 ); ?>" />
				<input type="hidden" name="edd-action" value="edit_conditional_email" />
				<input type="hidden" name="edd-conditional-emails-nonce" value="<?php echo wp_create_nonce( 'edd_conditional_emails_nonce' ); ?>" />
				<input type="submit" value="<?php echo ( $action == 'edit-conditional-email' ? __( 'Edit Email', 'edd-conditional-emails' ) : __( 'Add Email', 'edd-conditional-emails' ) ); ?>" class="button-primary" />
			</p>
		</form>
	</div>
	<?php
}
