<?php  // bounce_handling

$xmailboxstatus = array( 	0	=>	__( 'no changes', 'MailPress' ),
				1	=>	__( 'mark as read', 'MailPress' ),
				2	=>	__( 'delete', 'MailPress' ) );

if ( !isset( $bounce_handling ) )
{
	$bounce_handling = get_option( MailPress_bounce_handling::option_name );
	if ( !isset( $bounce_handling['batch_mode'] ) ) $bounce_handling['batch_mode'] = 'wpcron';
}
if ( !isset( $bounce_handling['pop3'] ) )
{
	$bounce_handling['pop3'] = get_option( 'MailPress_connection_pop3' );
}
?>
<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">

<!-- Pop3 -->

<?php MP_AdminPage::pop3_form( $bounce_handling, 'bounce_handling' ); ?>

<!-- bounces -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Handling Bounces', 'MailPress' ); ?></th>
			<td></td>
		</tr>
		<tr>
			<th><label for="bounce_handling_Return_Path"><?php _e( 'Return-Path', 'MailPress' ); ?></label></th>
			<td class="field<?php if ( isset( MP_AdminPage::$err_mess['Return-Path'] ) ) echo ' form-invalid'; ?>">
				<input type="text" name="bounce_handling[Return-Path]" value="<?php if ( isset( $bounce_handling['Return-Path'] ) ) echo $bounce_handling['Return-Path']; ?>" class="regular-text" id="bounce_handling_Return_Path" />
				<br /><?php printf( __( 'generated Return-Path will be %1$s', 'MailPress' ), ( !isset( $bounce_handling['Return-Path'] ) ) ?  __( 'start_of_email<i>+mail_id</i>+<i>mp_user_id</i>@mydomain.tld', 'MailPress' ) : substr( $bounce_handling['Return-Path'], 0, strpos( $bounce_handling['Return-Path'], '@' ) ) . '<i>+mail_id</i>+<i>mp_user_id</i>@' . substr( $bounce_handling['Return-Path'], strpos( $bounce_handling['Return-Path'], '@' ) + 1 ) ); ?>
			</td>
		</tr>
		<tr>
			<th><label for="bounce_handling_max_bounces"><?php _e( 'Max Bounces Per User', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="bounce_handling[max_bounces]" class="w4e" id="bounce_handling_max_bounces">
<?php MP_AdminPage::select_number( 0, 5, $bounce_handling['max_bounces'] ?? 1 );?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="bounce_handling_mailbox_status"><?php _e( 'Bounce In Mailbox', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="bounce_handling[mailbox_status]" id="bounce_handling_mailbox_status">
<?php MP_AdminPage::select_option( $xmailboxstatus, $bounce_handling['mailbox_status'] ?? 2 );?>
				</select>
			</td>
		</tr>
<!-- cron -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Batch', 'MailPress' ); ?></th>
			<td></td>
		</tr>
<?php MP_AdminPage::cron_form( $bounce_handling, 'bounce_handling', 'MailPress_bounce_handling' ); ?>
	</table>

<?php MP_AdminPage::save_button(); ?>

</form>