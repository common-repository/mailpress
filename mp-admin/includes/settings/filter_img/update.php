<?php // filter_img

if ( isset( MP_AdminPage::$pst_['filter_img'] ) )
{
	$filter_img	= stripslashes_deep( MP_AdminPage::$pst_['filter_img'] );

	update_option( MailPress_filter_img::option_name, $filter_img );

	$message = __( "'Image filter' settings saved", 'MailPress' );
}