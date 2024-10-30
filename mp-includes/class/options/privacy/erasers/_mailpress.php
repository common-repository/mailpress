<?php /* mailpress */

class MP_WP_Privacy_eraser_mailpress extends MP_WP_privacy_eraser_
{
	var $priority = 49;

	function eraser( $email, $page = 1 )
	{
		global $wpdb;

		$mp_user = $this->get_user( $email );
		if ( !$mp_user ) return $this->erase;

		/*  */

		$deleted = ( $this->delete( $mp_user ) );
                $this->erase['items_removed']  = ($deleted) ? 1 : 0;
		$this->erase['items_retained'] = ($deleted) ? 0 : 1;

		if ( $this->erase['items_removed']  ) $this->erase['messages'][] = sprintf( __( 'MailPress - Subscriber (%1$s) erased from the database.',   'MailPress' ), $email );
		if ( $this->erase['items_retained'] ) $this->erase['messages'][] = sprintf( __( 'MailPress - A problem occured while trying to erase subscriber (%1$s) from the database (%2$s)', 'MailPress' ), $email, $wpdb->$last_error );

		return $this->erase;
	}

	function delete( $mp_user )
	{
		return MP_User::delete( $mp_user->id );
	}
}
new MP_WP_Privacy_eraser_mailpress( __( 'Subscriber Eraser', 'MailPress' ) );