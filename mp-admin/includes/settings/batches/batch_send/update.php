<?php // batch_send => no sanitize here, selects and radio buttons 

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

if ( isset( MP_AdminPage::$pst_['batch_send'] ) ) 
{
	$batch_send = MP_AdminPage::$pst_['batch_send'];

	$old_batch_send = get_option( MailPress_batch_send::option_name );

	update_option( MailPress_batch_send::option_name, $batch_send );

	if ( !isset( $old_batch_send['batch_mode'] ) )
	{
		$old_batch_send['batch_mode'] = '';
	}

	if ( $old_batch_send['batch_mode'] != $batch_send['batch_mode'] )
	{
		if ( 'wpcron' != $batch_send['batch_mode'] )
		{
			wp_clear_scheduled_hook( MailPress_batch_send::process_name );
		}
		else
		{
			MailPress_batch_send::schedule();
		}
	}
}