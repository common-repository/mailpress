<?php // general  => one sanitize here, mostly selects and radio buttons + email is checked with email validator (RFC822,2822,5321,5322,6530,6531,6532) those RFC are not supported by Wp

$mp_general		= get_option( MailPress::option_name_general );	

if ( isset( MP_AdminPage::$pst_['general'] ))
{
	$mp_general['tab']= 'general';

	$mp_general	= stripslashes_deep( MP_AdminPage::$pst_['general'] );

	$mp_general['fromname'] = $mp_general['fromname'];

	switch ( true )
	{
		case ( !MailPress::is_email( $mp_general['fromemail'] ) ) :
			MP_AdminPage::$err_mess['fromemail'] = __( 'field should be an email', 'MailPress' );
		break;
		case ( empty( $mp_general['fromname'] ) ) :
			MP_AdminPage::$err_mess['fromname'] = __( 'field should be a name', 'MailPress' );
		break;
		case ( ( 'ajax' != $mp_general['subscription_mngt'] ) && ( !is_numeric( $mp_general['id'] ) ) ) :
			MP_AdminPage::$err_mess['subscription_mngt'] = __( 'field should be numeric', 'MailPress' );
		break;
		default :
			$mp_general['gmapkey']     = trim( $mp_general['gmapkey']     );
			$mp_general['mapboxtoken'] = trim( $mp_general['mapboxtoken'] );
			if ( empty( $mp_general['gmapkey'] ) && empty( $mp_general['mapboxtoken'] ) ) $mp_general['map_provider'] = 'o';

			do_action( 'MailPress_settings_general_update' );

			if ( 'ajax' == $mp_general['subscription_mngt'] ) $mp_general['id'] = '';

			update_option( MailPress::option_name_general, $mp_general );

			$message = __( 'General settings saved', 'MailPress' );
		break;
	}
}