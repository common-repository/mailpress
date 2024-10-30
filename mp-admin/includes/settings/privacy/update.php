<?php // privacy => no sanitize here, selects and radio buttons + email is checked with email validator (RFC822,2822,5321,5322,6530,6531,6532) those RFC are not supported by Wp

if ( isset( MP_AdminPage::$pst_['privacy'] ) ) 
{
	$privacy	= MP_AdminPage::$pst_['privacy'];

	$word_export = MailPress_privacy::one_word( $privacy['export_word'] );
	$word_erase  = MailPress_privacy::one_word( $privacy['erase_word']  );

	switch ( true )
	{
		case ( empty( $privacy['pop3']['server'] ) ) :
			MP_AdminPage::$err_mess['privacy_pop3_server'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( empty( $privacy['pop3']['port'] ) ) :
			MP_AdminPage::$err_mess['privacy_pop3_port'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( !is_numeric( $privacy['pop3']['port'] ) ) : 
			MP_AdminPage::$err_mess['privacy_pop3_port'] = __( 'field should be a number', 'MailPress' );
		break;
		case ( empty( $privacy['pop3']['username'] ) && !empty( $privacy['pop3']['password'] ) ) :
			MP_AdminPage::$err_mess['privacy_pop3_username'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( empty( $privacy['export_word'] ) ) :
			MP_AdminPage::$err_mess['privacy_export_word'] = __( 'field should not be empty', 'MailPress' );
		break;
                case ( !$word_export ) :
			MP_AdminPage::$err_mess['privacy_export_word'] = __( 'only one word please !', 'MailPress' );
		break;
		case ( empty( $privacy['erase_word'] ) ) :
			MP_AdminPage::$err_mess['privacy_erase_word'] = __( 'field should not be empty', 'MailPress' );
		break;
                case ( !$word_erase ) :
			MP_AdminPage::$err_mess['privacy_erase_word'] = __( 'only one word please !', 'MailPress' );
		break;

		default :
			$old_privacy = get_option( MailPress_privacy::option_name );

			$privacy['export_word'] = $word_export;
			$privacy['erase_word']  = $word_erase;

			update_option( MailPress_privacy::option_name, $privacy );
			$message = __( "'Privacy' settings saved", 'MailPress' );

			if ( !isset( $old_privacy['batch_mode'] ) ) 
			{
				$old_privacy['batch_mode'] = '';
			}

			if ( $old_privacy['batch_mode'] != $privacy['batch_mode'] )
			{
				if ( 'wpcron' != $privacy['batch_mode'] )
				{
					wp_clear_scheduled_hook( MailPress_privacy::process_name );
				}
				else
				{
					MailPress_privacy::schedule();
				}
			}
		break;
	}
}