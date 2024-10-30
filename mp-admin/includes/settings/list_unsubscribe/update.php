<?php // list_unsubscribe => no sanitize here, selects and radio buttons + email is checked with email validator (RFC822,2822,5321,5322,6530,6531,6532) those RFC are not supported by Wp

if ( isset( MP_AdminPage::$pst_['list_unsubscribe'] ) ) 
{
	$list_unsubscribe	= MP_AdminPage::$pst_['list_unsubscribe'];

	switch ( true )
	{
		case ( empty( $list_unsubscribe['pop3']['server'] ) ) :
			MP_AdminPage::$err_mess['list_unsubscribe_pop3_server'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( empty( $list_unsubscribe['pop3']['port'] ) ) :
			MP_AdminPage::$err_mess['list_unsubscribe_pop3_port'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( !is_numeric( $list_unsubscribe['pop3']['port'] ) ) : 
			MP_AdminPage::$err_mess['list_unsubscribe_pop3_port'] = __( 'field should be a number', 'MailPress' );
		break;
		case ( empty( $list_unsubscribe['pop3']['username'] ) && !empty( $list_unsubscribe['pop3']['password'] ) ) :
			MP_AdminPage::$err_mess['list_unsubscribe_pop3_username'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( !in_array( $list_unsubscribe['mode'], array( 'a', 'b', 'c', ) ) ) :
			MP_AdminPage::$err_mess['list_unsubscribe_mode'] = __( 'field should not be empty', 'MailPress' );
		break;

		default :
			$old_list_unsubscribe = get_option( MailPress_list_unsubscribe::option_name );

			update_option( MailPress_list_unsubscribe::option_name, $list_unsubscribe );
			$message = __( "'List-Unsubscribe' settings saved", 'MailPress' );

			if ( !isset( $old_list_unsubscribe['batch_mode'] ) ) 
			{
				$old_list_unsubscribe['batch_mode'] = '';
			}

			if ( $old_list_unsubscribe['batch_mode'] != $list_unsubscribe['batch_mode'] )
			{
				if ( 'wpcron' != $list_unsubscribe['batch_mode'] )
				{
					wp_clear_scheduled_hook( MailPress_list_unsubscribe::process_name );
				}
				else
				{
					MailPress_list_unsubscribe::schedule();
				}
			}
		break;
	}
}