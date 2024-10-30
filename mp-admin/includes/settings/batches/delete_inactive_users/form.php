<!-- delete_inactive_users -->
<?php

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

$xevery = MP_AdminPage::cron_every( 'd_y' );

if ( !isset( $batch_delete_inactive_users ) ) 
{
	$batch_delete_inactive_users = get_option( MailPress_delete_inactive_users::option_name );
	if ( !isset( $batch_delete_inactive_users['batch_mode'] ) ) $batch_delete_inactive_users['batch_mode'] = 'wpcron';
}
?>
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Deleting Users', 'MailPress' ); ?></th>
			<td></td>
		</tr>
		<tr>
			<th><label for="batch_delete_inactive_users_days"><?php _e( 'Keep Inactive Users Since', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="batch_delete_inactive_users[days]" id="batch_delete_inactive_users_days">
<?php MP_AdminPage::select_option( $xevery, $batch_delete_inactive_users['days'] ?? false );?>
				</select>
			</td>
		</tr>
<?php MP_AdminPage::cron_form( $batch_delete_inactive_users, 'batch_delete_inactive_users', 'MailPress_delete_inactive_users', 'd_y' ); ?>