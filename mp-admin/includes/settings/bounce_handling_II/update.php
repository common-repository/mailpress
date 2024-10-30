<?php // bounce_handling_II => no sanitize here, selects and radio buttons + email is checked with email validator (RFC822,2822,5321,5322,6530,6531,6532) those RFC are not supported by Wp

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

if ( isset( MP_AdminPage::$pst_['bounce_handling_II'] ) ) 
{
	$bounce_handling_II	= MP_AdminPage::$pst_['bounce_handling_II'];

	switch ( true )
	{
		case ( empty( $bounce_handling_II['pop3']['server'] ) ) :
			MP_AdminPage::$err_mess['bounce_handling_II_pop3_server'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( empty( $bounce_handling_II['pop3']['port'] ) ) :
			MP_AdminPage::$err_mess['bounce_handling_II_pop3_port'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( !is_numeric( $bounce_handling_II['pop3']['port'] ) ) : 
			MP_AdminPage::$err_mess['bounce_handling_II_pop3_port'] = __( 'field should be a number', 'MailPress' );
		break;
		case ( empty( $bounce_handling_II['pop3']['username'] ) && !empty( $bounce_handling_II['pop3']['password'] ) ) :
			MP_AdminPage::$err_mess['bounce_handling_II_pop3_username'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( !empty( $bounce_handling_II['Return-Path'] ) && !MailPress::is_email( $bounce_handling_II['Return-Path'] ) ) :
			MP_AdminPage::$err_mess['Return-Path'] = __( 'field should be an email', 'MailPress' );
		break;
		default :
			$old_bounce_handling_II = get_option( MailPress_bounce_handling_II::option_name );

			update_option( MailPress_bounce_handling_II::option_name, $bounce_handling_II );
			$message = __( "'Bounce_II' settings saved", 'MailPress' );

			if ( !isset( $old_bounce_handling_II['batch_mode'] ) ) 
			{
				$old_bounce_handling_II['batch_mode'] = '';
			}

			if ( $old_bounce_handling_II['batch_mode'] != $bounce_handling_II['batch_mode'] )
			{
				if ( 'wpcron' != $bounce_handling_II['batch_mode'] )
				{
					wp_clear_scheduled_hook( MailPress_bounce_handling_II::process_name );
				}
				else
				{
					MailPress_bounce_handling_II::schedule();
				}
			}
		break;
	}
}