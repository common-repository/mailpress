<?php // connection_smtp => no sanitize here : ports are controlled by is_numeric()

if ( isset( MP_AdminPage::$pst_['connection_smtp'] ) ) 
{
	$connection_smtp	= stripslashes_deep( MP_AdminPage::$pst_['connection_smtp'] );

	if ( 'custom' == $connection_smtp['port'] ) $connection_smtp ['port'] = $connection_smtp['customport'];
	unset( $connection_smtp['customport'] );

	switch ( true )
	{
		case ( empty( $connection_smtp['server'] ) ) :
			MP_AdminPage::$err_mess['server'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( empty( $connection_smtp['username'] ) && !empty( $connection_smtp['password'] ) ) :
			MP_AdminPage::$err_mess['username'] = __( 'field should not be empty', 'MailPress' );
		break;
                case ( isset( $connection_smtp['customport'] ) && !empty( $connection_smtp['customport'] ) && !is_numeric( $connection_smtp[customport] ) ) : 
			MP_AdminPage::$err_mess['customport'] = __( 'field should be a number', 'MailPress' );
		break;
		case ( ( isset( $connection_smtp['smtp-auth'] ) && ( '@PopB4Smtp' == $connection_smtp['smtp-auth'] ) ) && ( empty( $connection_smtp['pophost'] ) ) ) : 
			MP_AdminPage::$err_mess['smtp-auth'] = __( 'field should not be empty', 'MailPress' );
		break;
		case ( ( isset( $connection_smtp['smtp-auth'] ) && ( '@PopB4Smtp' == $connection_smtp['smtp-auth'] ) ) && ( !is_numeric( $connection_smtp['popport'] ) ) ) : 
			MP_AdminPage::$err_mess['smtp-auth'] = __( 'field should be a number', 'MailPress' );
		break;
		default :
			update_option( MailPress::option_name_smtp, $connection_smtp );
			$message = __( 'SMTP settings saved, Test it !!', 'MailPress' );
		break;
	}
}