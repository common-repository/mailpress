<?php /* tracking */
if ( class_exists( 'MailPress' ) && class_exists( 'MailPress_tracking' ) )
{
class MP_WP_Privacy_eraser_tracking extends MP_WP_privacy_eraser_
{
	const per_page = 100;

	var $priority = 48;

	function eraser( $email, $page = 1 )
	{
		global $wpdb;

		$mp_user = $this->get_user( $email );
		if ( !$mp_user ) return $this->erase;

		/*  */

		// some clean up
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->mp_tracks WHERE user_id = %d AND mail_id NOT IN ( SELECT id FROM $wpdb->mp_mails ) ;", $mp_user->id ) );

		return $this->erase;
	}
}
new MP_WP_Privacy_eraser_tracking( __( 'MailPress tracks eraser', 'MailPress' ) );
}