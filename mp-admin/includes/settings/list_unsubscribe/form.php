<?php // list_unsubscribe

$xlist_unsubscribe = array (
	'c' 	=> __( 'Unsubscribe from sending list',	'MailPress' ),
	'b' 	=> __( 'Unsubscribe from all lists', 	'MailPress' ),
	'a' 	=> __( 'Delete subscriber', 		'MailPress' ),


); 


if ( !isset( $list_unsubscribe ) )
{
	$list_unsubscribe = get_option( MailPress_list_unsubscribe::option_name );
        if ( !isset( $list_unsubscribe['batch_mode'] ) ) $list_unsubscribe['batch_mode'] = 'wpcron';
}
?>
<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">
<!-- Pop3 -->

<?php MP_AdminPage::pop3_form( $list_unsubscribe, 'list_unsubscribe' ); ?>

<!-- mailbox -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Processing', 'MailPress' ); ?></th>
			<td></td>
		</tr>
<!-- unsubscribing mode -->
		<tr>
			<th><label for="list_unsubscribe_mode"><?php _e( 'Unsubscribing ...', 'MailPress' ); ?></label></th>
			<td class="field">
				<select name="list_unsubscribe[mode]" id="list_unsubscribe_mode">
<?php MP_AdminPage::select_option( $xlist_unsubscribe, $list_unsubscribe['mode'] ?? false );?>
				</select>
			</td>
		</tr>
<!-- cron -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Batch', 'MailPress' ); ?></th>
			<td></td>
		</tr>
<?php MP_AdminPage::cron_form( $list_unsubscribe, 'list_unsubscribe', 'MailPress_list_unsubscribe', 'h_d' ); ?>
	</table>

<?php MP_AdminPage::save_button(); ?>

</form>