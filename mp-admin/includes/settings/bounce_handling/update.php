<?php // bounce_handling => no sanitize here, selects and radio buttons + email is checked with email validator (RFC822,2822,5321,5322,6530,6531,6532) those RFC are not supported by Wp

if ( isset( MP_AdminPage::$pst_['bounce_handling'] ) ) 
{
	$bounce_handling = MP_AdminPage::$pst_['bounce_handling'];

	switch ( true )
	{
		case ( empty( $bounce_handling['pop3']['server'] ) ) :
			MP_AdminPage::$err_mess['bounce_handling_pop3_server'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( empty( $bounce_handling['pop3']['port'] ) ) :
			MP_AdminPage::$err_mess['bounce_handling_pop3_port'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( !is_numeric( $bounce_handling['pop3']['port'] ) ) : 
			MP_AdminPage::$err_mess['bounce_handling_pop3_port'] = __( 'field should be a number', 'MailPress' );
		break;
		case ( empty( $bounce_handling['pop3']['username'] ) && !empty( $bounce_handling['pop3']['password'] ) ) :
			MP_AdminPage::$err_mess['bounce_handling_pop3_username'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( !MailPress::is_email( $bounce_handling['Return-Path'] ) ) :
			MP_AdminPage::$err_mess['Return-Path'] = __( 'field should be an email', 'MailPress' );
		break;
		default :
			$old_bounce_handling = get_option( MailPress_bounce_handling::option_name );

			update_option( MailPress_bounce_handling::option_name, $bounce_handling );
			$message = __( "'Bounce' settings saved", 'MailPress' );

			if ( !isset( $old_bounce_handling['batch_mode'] ) )
			{
				$old_bounce_handling['batch_mode'] = '';
			}

			if ( $old_bounce_handling['batch_mode'] != $bounce_handling['batch_mode'] )
			{
				if ( 'wpcron' != $bounce_handling['batch_mode'] )
				{
					wp_clear_scheduled_hook( MailPress_bounce_handling::process_name );
				}
				else
				{
					MailPress_bounce_handling::schedule();
				}
			}
		break;
	}
}
