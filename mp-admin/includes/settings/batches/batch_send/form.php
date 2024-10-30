<!-- batch_send -->
<?php

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

if ( !isset( $batch_send ) )
{
	$batch_send = get_option( MailPress_batch_send::option_name );
	if ( !isset( $batch_send['batch_mode'] ) ) $batch_send['batch_mode'] = 'wpcron';
}
?>
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Sending Mails', 'MailPress' ); ?></th>
			<td></td>
		</tr>
		<tr>
			<th><label for="batch_send_per_pass"><?php _e( 'Max Mails Sent Per Batch', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="batch_send[per_pass]" id="batch_send_per_pass">
<?php MP_AdminPage::select_number( 1, 10, $batch_send['per_pass']       ?? false, 1 );?>
<?php MP_AdminPage::select_number( 11, 100, $batch_send['per_pass']     ?? false, 10 );?>
<?php MP_AdminPage::select_number( 101, 1000, $batch_send['per_pass']   ?? false, 100 );?>
<?php MP_AdminPage::select_number( 1001, 10000, $batch_send['per_pass'] ?? false, 1000 );?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="batch_send_max_retry"><?php _e( 'Max Retries', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="batch_send[max_retry]" class="w4e" id="batch_send_max_retry">
<?php MP_AdminPage::select_number( 0, 5, $batch_send['max_retry'] ?? false );?>
				</select>
			</td>
		</tr>
<?php MP_AdminPage::cron_form( $batch_send, 'batch_send', 'MailPress_batch_send' ); ?>