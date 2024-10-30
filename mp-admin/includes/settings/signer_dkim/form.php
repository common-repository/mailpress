<?php // signer_dkim

if ( !isset( $signer_dkim ) )
{
	$signer_dkim = get_option( Mailpress_signer_dkim::option_name );
}
?>
<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">

<!-- privateKey -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['privateKey'] ) ) echo ' class="form-invalid"'; ?>>
			<th>
				<?php _e( 'Private Key File', 'MailPress' ); ?><br /><small><?php _e('(full path to .pem file)', 'MailPress' ); ?></small>
			</th>
			<td>
				<input type="text" name="signer_dkim[privateKey]" size="100" value="<?php if ( isset( $signer_dkim['privateKey'] ) ) echo $signer_dkim['privateKey']; ?>" />
			</td>
		</tr>
<!-- domainName -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['domainName'] ) ) echo ' class="form-invalid"'; ?>>
			<th>
				<?php _e( 'Domain Name', 'MailPress' ); ?>  
			</th>
			<td>
				<input type="text" name="signer_dkim[domainName]" size="100" value="<?php if ( isset( $signer_dkim['domainName'] ) ) echo $signer_dkim['domainName']; ?>" />
			</td>
		</tr>
<!-- selector -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['selector'] ) ) echo ' class="form-invalid"'; ?>>
			<th>
				<?php _e( 'Selector', 'MailPress' ); ?>   
			</th>
			<td>
				<input type="text" name="signer_dkim[selector]" size="25" value="<?php if ( isset( $signer_dkim['selector'] ) ) echo $signer_dkim['selector']; ?>" />
			</td>
		</tr>

	</table>

<?php MP_AdminPage::save_button(); ?>

</form>