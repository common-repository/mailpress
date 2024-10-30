<!-- batch_spool_send -->
<?php 

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

$xevery =  MP_AdminPage::cron_every(); 

if ( !isset( $batch_spool_send ) ) 
{
	$batch_spool_send = get_option( MailPress_batch_spool_send::option_name );
	if ( !isset( $batch_spool_send['batch_mode'] ) ) $batch_spool_send['batch_mode'] = 'wpcron';
}

$spool_path = 'spool/' . get_current_blog_id() . '/';

?>
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Sending Mails From Spool', 'MailPress' ); ?></th>
			<td></td>
		</tr>
		<tr>
			<th><?php _e( 'Spool Path', 'MailPress' ); ?></th>
			<td class="field">
				<br /><?php printf( __( 'Since MailPress 7.0, spool path is %s ', 'MailPress' ), '<code>' .  MP_UPL_PATH . $spool_path . '</code>'  ); ?>
			</td>
		</tr>

		<tr>
			<th><label for="batch_spool_send_time_limit"><?php _e( 'Time Limit In Seconds', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="batch_spool_send[time_limit]" id="batch_spool_send_time_limit">
<?php MP_AdminPage::select_option( $xevery, $batch_spool_send['time_limit'] ?? false );?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="batch_spool_send_per_pass"><?php _e( 'Max Mails Sent Per Batch', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="batch_spool_send[per_pass]" id="batch_spool_send_per_pass">
<?php MP_AdminPage::select_number( 1, 10, $batch_spool_send['per_pass']       ?? false, 1 );?>
<?php MP_AdminPage::select_number( 11, 100, $batch_spool_send['per_pass']     ?? false, 10 );?>
<?php MP_AdminPage::select_number( 101, 1000, $batch_spool_send['per_pass']   ?? false, 100 );?>
<?php MP_AdminPage::select_number( 1001, 10000, $batch_spool_send['per_pass'] ?? false, 1000 );?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="batch_spool_send_max_retry"><?php _e( 'Max Retries', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="batch_spool_send[max_retry]" class="w4e" id="batch_spool_send_max_retry">
<?php MP_AdminPage::select_number( 0, 5, $batch_spool_send['max_retry'] ?? false );?>
				</select>
			</td>
		</tr>
<?php MP_AdminPage::cron_form( $batch_spool_send, 'batch_spool_send', 'MailPress_batch_spool_send' ); ?>