<?php // signer_smime => no sanitize here, very sensitive information (paths to certificates)

if ( isset( MP_AdminPage::$pst_['signer_smime'] ) )
{
	$signer_smime = MP_AdminPage::$pst_['signer_smime'];

     $signer_smime['Certificate'] = trim( stripslashes( $signer_smime['Certificate'] ) );
     $signer_smime['privateKey']  = trim( stripslashes( $signer_smime['privateKey'] ) );
     $signer_smime['Encryption']  = trim( stripslashes( $signer_smime['Encryption'] ) );

	switch ( true )
	{
		case ( empty( $signer_smime['Certificate'] ) || !is_file( $signer_smime['Certificate'] ) ) :
			MP_AdminPage::$err_mess['Certificate'] = __( 'field should be a valid file.', 'MailPress' );
		break;
		case ( empty( $signer_smime['privateKey'] ) || !is_file( $signer_smime['privateKey'] ) ) :
			MP_AdminPage::$err_mess['privateKey'] = __( 'field should be a valid file.', 'MailPress' );
		break;
		case ( !empty( $signer_smime['Encryption'] ) && !is_file( $signer_smime['Encryption'] ) ) :
			MP_AdminPage::$err_mess['Encryption'] = __( 'field should be a valid file.', 'MailPress' );
		break;
		default :
			update_option( MailPress_signer_smime::option_name, $signer_smime );
			$message = __( "'SMime' settings saved", 'MailPress' );
		break;
	}
}
