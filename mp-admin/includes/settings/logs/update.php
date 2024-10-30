<?php // logs => no sanitize here, only selects

if ( isset( MP_AdminPage::$pst_['logs'] ) )
{
	$logs = get_option( MailPress::option_name_logs );
	
	foreach ( MP_AdminPage::$pst_['logs'] as $k => $v ) $logs[$k] = $v; // so we don't delete settings if addon deactivated !
	
	update_option( MailPress::option_name_logs, $logs );

	$message = __( 'Logs settings saved', 'MailPress' );
}