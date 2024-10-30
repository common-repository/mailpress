<?php //subscriptions => no sanitize here, checkboxes and selects

$old_subscriptions = get_option( MailPress::option_name_subscriptions );

$subscriptions = MP_AdminPage::$pst_['subscriptions'];

if ( isset( MP_AdminPage::$pst_['newsletter']['on'] ) )
{
	if ( isset( MP_AdminPage::$pst_['newsletter']['post_limits'] ) )
	{
		update_option( MailPress_newsletter::option_post_limits, MP_AdminPage::$pst_['newsletter']['post_limits'] );
	}

	if ( isset( MP_AdminPage::$pst_['comment_newsletter_subscription'] ) )
	{
		update_option ( MailPress_comment_newsletter_subscription::option_name, MP_AdminPage::$pst_['comment_newsletter_subscription'] );
	}

	$diff_default_newsletters = array();
	if ( !isset( $subscriptions['default_newsletters'] ) ) 	 $subscriptions['default_newsletters'] 	= array();
	$old_default_newsletters = $old_subscriptions ['default_newsletters'] ?? MP_Newsletter::get_defaults();

	foreach( $subscriptions['default_newsletters'] as $k => $v ) if ( !isset( $old_default_newsletters[$k] ) )  $diff_default_newsletters[$k] = true;
	foreach( $old_default_newsletters as $k => $v ) if ( !isset( $subscriptions ['default_newsletters'][$k] ) ) $diff_default_newsletters[$k] = true;
	foreach ( $diff_default_newsletters as $k => $v ) MP_Newsletter::reverse_subscriptions( $k );

	if ( $old_subscriptions['default_newsletters'] != $subscriptions['default_newsletters'] || $old_subscriptions['newsletters'] != $subscriptions['newsletters'] ) wp_schedule_single_event( current_time( 'timestamp', 'gmt' ) - 1, 'mp_schedule_newsletters', array( 'args' => array( 'event' => '** Subscriptions updated **' ) ) );
}
else  
{	// so we don't delete settings if addon deactivated !
	if ( isset( $old_subscriptions['newsletters'] ) )		$subscriptions['newsletters']		= $old_subscriptions['newsletters'];
	if ( isset( $old_subscriptions['default_newsletters'] ) )	$subscriptions['default_newsletters']	= $old_subscriptions['default_newsletters'];
}

if ( !isset( MP_AdminPage::$pst_['mailinglist']['on'] ) )
{	// so we don't delete settings if addon deactivated !
	if ( isset( MP_AdminPage::$pst_['default_mailinglist'] ) ) update_option ( MailPress_mailinglist::option_name_default, MP_AdminPage::$pst_['default_mailinglist'] );
	if ( isset( $old_subscriptions['display_mailinglists'] ) ) $subscriptions['display_mailinglists'] 	= $old_subscriptions['display_mailinglists'];
}

if ( isset( MP_AdminPage::$pst_['comment']['on'] ) )
{
	// so we don't delete settings if addon deactivated !
	update_option( MailPress_comment::option, ( isset( $subscriptions['comment_checked'] ) ) );
}

$mp_subscriptions = $subscriptions;
	
update_option( MailPress::option_name_subscriptions, $mp_subscriptions );

$message = __( '"Subscriptions" settings saved', 'MailPress' );