<?php /* erase or anonymize mails */
class MP_WP_Privacy_eraser_mails extends MP_WP_privacy_eraser_
{
	const per_page = 100;

	var $priority = 46;

	function eraser( $email, $page = 1 )
	{
		global $wpdb;

		$mp_user = $this->get_user( $email );

		/*  */

		$mails = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS id, toemail FROM $wpdb->mp_mails WHERE toemail LIKE '%{$email}%' ORDER BY id ASC LIMIT 0, " . self::per_page . ";" );

		if ( !$mails ) return $this->erase;

		$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );

		/*  */

		$anonymized = $deleted = $retained = array();

		foreach ( (array) $mails as $mail )
		{
			if ( $mail->toemail == $email )
				( $this->delete( $mail ) ) ?  $deleted[] = $mail->id : $retained[] = $mail->id;
			else
				( $this->anonymize( $mail, $mp_user, $email ) ) ? $anonymized[] = $mail->id : $retained[] = $mail->id;
		}

		$this->erase['items_removed'] = count( $anonymized ) + count( $deleted );
		$this->erase['items_retained']= count( $retained );

		if ( !empty( $retained )   ) $this->erase['messages'][] = sprintf( __( 'Process "%1$s" aborted for %2$s : (%3$s).', 'MailPress' ), $this->name, $email, implode( ', ', $retained ) );
		if ( !empty( $anonymized ) ) $this->erase['messages'][] = sprintf( __( 'Mails sent to %1$s have been successfully anonymized (%2$s).', 'MailPress' ), $email, implode( ', ', $anonymized ) );
		if ( !empty( $deleted )    ) $this->erase['messages'][] = sprintf( __( 'Mails sent to %1$s have been successfully deleted (%2$s).', 'MailPress' ), $email, implode( ', ', $deleted ) );

		if ( $retained ) return $this->erase; // abort otherwise we enter in a loop
 
		$this->erase['done'] = ( $total <= self::per_page );

		return $this->erase;
	}

	function anonymize( $mail, $mp_user, $email )
	{
		global $wpdb;

		if ( !is_serialized( $mail->toemail ) ) return false;

		$toemails = unserialize( $mail->toemail );

		if ( !isset( $toemails[$email] ) ) return true;

		$a = $toemails[$email];

		$mp_user_id = $a['{{_user_id}}'] ?? $mp_user->id      ?? NULL;
		$confkey    = $a['{{_confkey}}'] ?? $mp_user->confkey ?? NULL;

		unset( $toemails[$email] );

		if ( isset( $mp_user_id ) )
		{
			$toemails[$mp_user_id]['{{_user_id}}'] = $mp_user_id;
			if ( isset( $confkey ) ) 
				$toemails[$mp_user_id]['{{_confkey}}'] = $confkey;
		}

		$data = $format = $where = $where_format = array();

		$data['toemail'] = serialize( $toemails );	$format[] = '%s';

		$where['id'] 	 = ( int ) $mail->id;		$where_format[] = '%d';

		return $wpdb->update( $wpdb->mp_mails, $data, $where, $format, $where_format );
	}

	function delete( $mail )
	{
		return MP_Mail::delete( $mail->id );
	}
}
new MP_WP_Privacy_eraser_mails( __( 'Subscriber > Mail Anonymizer', 'MailPress' ) );