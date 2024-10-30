<?php // delete_inactive_users => no need to sanitize, only select, radio buttons

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

if ( isset( MP_AdminPage::$pst_['batch_delete_inactive_users'] ) ) 
{
	$batch_delete_inactive_users = MP_AdminPage::$pst_['batch_delete_inactive_users'];

	$old_delete_inactive_users = get_option( MailPress_delete_inactive_users::option_name );

	update_option( MailPress_delete_inactive_users::option_name, $batch_delete_inactive_users );

	if ( !isset( $old_delete_inactive_users['batch_mode'] ) )
	{
		$old_delete_inactive_users['batch_mode'] = '';
	}

	if ( $old_delete_inactive_users['batch_mode'] != $batch_delete_inactive_users['batch_mode'] )
	{
		if ( 'wpcron' != $batch_delete_inactive_users['batch_mode'] )
		{
			wp_clear_scheduled_hook( MailPress_delete_inactive_users::process_name );
		}
		else
		{
			MailPress_delete_inactive_users::schedule();
		}
	}
}