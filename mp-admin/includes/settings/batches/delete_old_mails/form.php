<!-- delete_old_mails -->
<?php

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

$xevery = MP_AdminPage::cron_every( 'd_y' );

if ( !isset( $batch_delete_old_mails ) )
{
	$batch_delete_old_mails = get_option( MailPress_delete_old_mails::option_name );
	if ( !isset( $batch_delete_old_mails['batch_mode'] ) ) $batch_delete_old_mails['batch_mode'] = 'wpcron';
}
?>
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Deleting Old Mails', 'MailPress' ); ?></th>
			<td></td>
		</tr>
		<tr>
			<th><label for="batch_delete_old_mails_days"><?php _e( 'Keep Sent Mails Since', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="batch_delete_old_mails[days]" id="batch_delete_old_mails_days">
<?php MP_AdminPage::select_option( $xevery, $batch_delete_old_mails['days'] ?? false );?>
				</select>
			</td>
		</tr>
<?php MP_AdminPage::cron_form( $batch_delete_old_mails, 'batch_delete_old_mails', 'MailPress_delete_old_mails', 'd_y' ); ?>
