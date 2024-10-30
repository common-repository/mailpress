<?php // connection_sendmail => nothing to sanitize here, see full explanation here => https://swiftmailer.symfony.com/docs/sending.html#the-sendmail-transport

if ( isset( MP_AdminPage::$pst_['connection_sendmail'] ) )
{
	$connection_sendmail = MP_AdminPage::$pst_['connection_sendmail'];

	update_option( MailPress::option_name_sendmail, $connection_sendmail );
	$message = __( "'SENDMAIL' settings saved", 'MailPress' );
}
