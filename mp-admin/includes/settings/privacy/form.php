<?php // privacy

if ( !isset( $privacy ) )
{
	$privacy = get_option( MailPress_privacy::option_name );
        if ( !isset( $privacy['batch_mode'] ) ) $privacy['batch_mode'] = 'wpcron';
}
?>
<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">
<!-- Pop3 -->

<?php MP_AdminPage::pop3_form( $privacy, 'privacy' ); ?>

<!-- mailbox -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Mailbox', 'MailPress' ); ?></th>
			<td></td>
		</tr>
<!-- subjects words -->
		<tr>
			<th><label for="privacy_export_word"><?php _e( 'Mail Subjects', 'MailPress' ); ?></label></th>
			<td class="field">
				<input type="text" name="privacy[export_word]" value="<?php echo ( isset( $privacy['export_word'] ) ) ? esc_attr( $privacy['export_word'] ) : 'export'; ?>" class="regular-text ltr<?php if ( isset( MP_AdminPage::$err_mess['privacy_export_word'] ) ) echo ' form-invalid'; ?>" id="privacy_export_word" />
				<span class="italic"><?php _e( 'one word for export data request', 'MailPress'); ?></span>
				<br />
				<input type="text" name="privacy[erase_word]" value="<?php echo ( isset( $privacy['erase_word'] ) )  ? esc_attr( $privacy['erase_word'] )  : 'erase';  ?>"  class="regular-text ltr<?php if ( isset( MP_AdminPage::$err_mess['privacy_erase_word'] ) )  echo ' form-invalid'; ?>" />
				<span class="italic"><?php _e( 'one word for erase data request', 'MailPress'); ?></span>
			</td>
		</tr>
<!-- cron -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Batch', 'MailPress' ); ?></th>
			<td></td>
		</tr>
<?php MP_AdminPage::cron_form( $privacy, 'privacy', 'MailPress_privacy', 'h_d' ); ?>
	</table>

<?php MP_AdminPage::save_button(); ?>

</form>