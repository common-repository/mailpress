<?php // batches

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

do_action( 'MailPress_settings_batches_update' );

if ( empty( MP_AdminPage::$err_mess ) ) 
{
	$message = __( "'Batches' settings saved", 'MailPress' );
}

