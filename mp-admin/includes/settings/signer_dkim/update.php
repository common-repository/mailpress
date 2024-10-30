<?php // signer_dkim => no sanitize here, very sensitive information (paths to certificates)

if ( isset( MP_AdminPage::$pst_['signer_dkim'] ) )
{
	$signer_dkim = MP_AdminPage::$pst_['signer_dkim'];
	$signer_dkim['privateKey'] = stripslashes( $signer_dkim['privateKey'] );

	switch ( true )
	{
		case ( empty( $signer_dkim['privateKey'] ) || !is_file( $signer_dkim['privateKey'] ) ) :
			MP_AdminPage::$err_mess['privateKey'] = __( 'field should be a valid file.', 'MailPress' );
		break;
		case ( empty( $signer_dkim['domainName'] ) ) :
			MP_AdminPage::$err_mess['domainName'] =  __( 'field should not be empty', 'MailPress' );
		break;
		case ( empty( $signer_dkim['selector'] ) ) :
			MP_AdminPage::$err_mess['selector'] =  __( 'field should not be empty', 'MailPress' );
		break;
		default :
			update_option( MailPress_signer_dkim::option_name, $signer_dkim );
			$message = __( "'DKIM' settings saved", 'MailPress' );
		break;
	}
}
