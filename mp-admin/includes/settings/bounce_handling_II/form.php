<?php // bounce_handling_II

$xmailboxstatus = array( 	0	=>	__( 'no changes', 'MailPress' ),
				1	=>	__( 'mark as read', 'MailPress' ),
				2	=>	__( 'delete', 'MailPress' ) );

if ( !isset( $bounce_handling_II ) )
{
	$bounce_handling_II = get_option( MailPress_bounce_handling_II::option_name );
	if ( !isset( $bounce_handling_II['batch_mode'] ) ) $bounce_handling_II['batch_mode'] = 'wpcron';
}
if ( !isset( $bounce_handling_II['pop3'] ) )
{
	$bounce_handling_II['pop3'] = get_option( 'MailPress_connection_pop3' );
}
?>
<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">
<!-- Pop3 -->

<?php MP_AdminPage::pop3_form( $bounce_handling_II, 'bounce_handling_II' ); ?>

<!-- bounces -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Handling Bounces', 'MailPress' ); ?> II</th>
			<td></td>
		</tr>
<?php if ( !class_exists( 'MailPress_bounce_handling' ) ) : ?>
		<tr>
			<th><label for="bounce_handling_II_Return_Path"><?php _e( 'Return-Path', 'MailPress' ); ?></label></th>
			<td class="field<?php if ( isset( MP_AdminPage::$err_mess['Return-Path'] ) ) echo ' form-invalid'; ?>">
				<input type="text" name="bounce_handling_II[Return-Path]" value="<?php if ( isset( $bounce_handling_II['Return-Path'] ) ) echo esc_attr( $bounce_handling_II['Return-Path'] ); ?>" class="regular-text" id="bounce_handling_II_Return_Path" />
				<br /><?php _e( 'optional', 'MailPress' ); ?>
			</td>
		</tr>
<?php endif; ?>
		<tr>
			<th><label for="bounce_handling_II_max_bounces"><?php _e( 'Max Bounces Per User', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="bounce_handling_II[max_bounces]" class="w4e" id="bounce_handling_II_max_bounces">
<?php MP_AdminPage::select_number( 0, 5, $bounce_handling_II['max_bounces'] ?? 1 );?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="bounce_handling_II_mailbox_status"><?php _e( 'Bounce In Mailbox', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="bounce_handling_II[mailbox_status]" id="bounce_handling_II_mailbox_status">
<?php MP_AdminPage::select_option( $xmailboxstatus, ( ( isset( $bounce_handling_II['mailbox_status'] ) ) ? $bounce_handling_II['mailbox_status'] : 2 ) );?>
				</select>
			</td>
		</tr>
<!-- cron -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Batch', 'MailPress' ); ?></th>
			<td></td>
		</tr>
<?php MP_AdminPage::cron_form( $bounce_handling_II, 'bounce_handling_II', 'MailPress_bounce_handling_II' ); ?>
	</table>

<?php MP_AdminPage::save_button(); ?>

</form>