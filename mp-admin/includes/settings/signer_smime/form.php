<?php // signer_smime

if ( !extension_loaded( 'openssl' ) )
{
	$m[] = printf( __( 'Default php extension \'%1$s\' not loaded.', 'MailPress'), 'openssl' );
	return;
}

if ( !isset( $signer_smime ) )
{
	$signer_smime = get_option( Mailpress_signer_smime::option_name );
}
?>
<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">

<!-- Certificate -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['Certificate'] ) ) echo ' class="form-invalid"'; ?>>
			<th>
				<?php _e( 'Certificate', 'MailPress' ); ?><br /><small><?php _e('(full path to .pem file)', 'MailPress' ); ?></small>
			</th>
			<td>
				<input type="text" name="signer_smime[Certificate]" size="100" value="<?php if ( isset( $signer_smime['Certificate'] ) ) echo $signer_smime['Certificate']; ?>" />
			</td>
		</tr>
<!-- privateKey -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['privateKey'] ) ) echo ' class="form-invalid"'; ?>>
			<th>
				<?php _e( 'Private Key File', 'MailPress' ); ?><br /><small><?php _e('(full path to .pem file)', 'MailPress' ); ?></small>
			</th>
			<td>
				<input type="text" name="signer_smime[privateKey]" size="100" value="<?php if ( isset( $signer_smime['privateKey'] ) ) echo $signer_smime['privateKey']; ?>" />
			</td>
		</tr>
<!-- Passphrase -->
		<tr>
			<th>
				<?php _e( 'Passphrase', 'MailPress' ); ?><br /><small><?php _e('(optionnal)', 'MailPress' ); ?></small>
			</th>
			<td>
				<input type="password" name="signer_smime[passphrase]" size="100" value="<?php if ( isset( $signer_smime['passphrase'] ) ) echo $signer_smime['passphrase']; ?>" />
			</td>
		</tr>
<!-- Encryption -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['Encryption'] ) ) echo ' class="form-invalid"'; ?>>
			<th>
				<?php _e( 'Encryption Certificate', 'MailPress' ); ?><br /><small><?php _e('(optionnal full path to .pem file)', 'MailPress' ); ?></small>
			</th>
			<td>
				<input type="text" name="signer_smime[Encryption]" size="100" value="<?php if ( isset( $signer_smime['Encryption'] ) ) echo $signer_smime['Encryption']; ?>" />
			</td>
		</tr>

	</table>

<?php MP_AdminPage::save_button(); ?>

</form>