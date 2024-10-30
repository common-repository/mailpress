<!-- subscriptions -->

<?php if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' ); ?>

<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">

<?php do_action( 'MailPress_settings_subscriptions_form' ); ?>

	</table>

<?php MP_AdminPage::save_button(); ?>

</form>