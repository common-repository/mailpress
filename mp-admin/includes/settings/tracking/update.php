<?php // tracking => no sanitize here, only checkboxes

if ( isset( MP_AdminPage::$pst_['tracking'] ) ) 
{
	$tracking	= MP_AdminPage::$pst_['tracking'];

	update_option( MailPress_tracking::option_name, $tracking );

	$message = __( "'Tracking' settings saved", 'MailPress' );
}