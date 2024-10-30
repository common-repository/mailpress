<?php // batch_spool_send => no sanitize here, selects and radio buttons 

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

if ( isset( MP_AdminPage::$pst_['batch_spool_send'] ) ) 
{
	$batch_spool_send = MP_AdminPage::$pst_['batch_spool_send'];

	$old_batch_spool_send = get_option( MailPress_batch_spool_send::option_name );

	update_option( MailPress_batch_spool_send::option_name, $batch_spool_send );

	if ( !isset( $old_batch_spool_send['batch_mode'] ) )
	{
		$old_batch_spool_send['batch_mode'] = '';
	}

	if ( $old_batch_spool_send['batch_mode'] != $batch_spool_send['batch_mode'] )
	{
		if ( 'wpcron' != $batch_spool_send['batch_mode'] )
		{
			wp_clear_scheduled_hook( MailPress_batch_spool_send::process_name );
		}
		else
		{
			MailPress_batch_spool_send::schedule();
		}
	}
}