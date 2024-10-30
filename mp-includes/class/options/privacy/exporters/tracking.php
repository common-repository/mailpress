<?php /* tracking */
if ( class_exists( 'MailPress' ) && class_exists( 'MailPress_tracking' ) )
{
class MP_WP_Privacy_exporter_tracking extends MP_WP_privacy_exporter_
{
	const per_page = 100;

	var $priority = 44;

	function exporter( $email, $page = 1 )
	{
		global $wpdb;

		$mp_user = $this->get_user( $email );
		if ( !$mp_user ) return $this->export;

		/*  */

		$start = ( $page - 1 ) * self::per_page;

		$tracks = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->mp_tracks WHERE user_id = %d ORDER BY tmstp ASC LIMIT $start, " . self::per_page . ";", $mp_user->id ) );

		if ( !$tracks ) return $this->export;

		$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );
		$current = $start + count( $tracks );

		/*  */

		$x = new MP_Mail();

		foreach ( (array) $tracks as $k => $track )
		{
			if ( MailPress::is_bot( $track->agent ) )
			{
				unset( $tracks[$k] );
				continue;
			}

			$subject = $wpdb->get_var( $wpdb->prepare( "SELECT subject FROM $wpdb->mp_mails WHERE id = %d ;", $track->mail_id ) );
			$tracks[$k]->subject = $x->viewsubject( $subject, $track->mail_id, $track->mail_id, $mp_user->id );

			switch ( $track->track )
			{
				case '{{viewhtml}}' :
					$tracks[$k]->track = sprintf( '<a href="%1$s" title="%2$s" target="_blank">%3$s</a>',
											MP_User::get_view_url( $mp_user->confkey, $track->mail_id ),
											esc_attr( $track->track ),
											MailPress_tracking::translate_track( $track->track, $track->mail_id )
					);
				break;
				case '{{subscribe}}' :
					//$url = MP_User::get_subscribe_url( $get_['us'] );	break;
				case '{{unsubscribe}}' :
					//$url = MP_User::get_unsubscribe_url( $get_['us'] ); break;
				default :
					$tracks[$k]->track = MailPress_tracking::translate_track( $track->track, $track->mail_id );
				break;
			}
		}

		/*  */

		$properties = array(
			'tmstp' 	=> __( 'Collect Time', 'MailPress' ),
			'id' 		=> __( 'Collect Id', 'MailPress' ),
			'mail_id' 	=> __( 'Mail Id', 'MailPress' ),
			'subject'	=> __( 'Mail Subject', 'MailPress' ),
			'track' 	=> __( 'Collected Url', 'MailPress' ),
			'context' 	=> __( 'Collected Context', 'MailPress' ),
			'ip' 		=> __( 'Collected Ip', 'MailPress' ),
			'agent' 	=> __( 'Collected UserAgent', 'MailPress' ),
			'referrer'	=> __( 'Collected Referrer', 'MailPress' ),
		);

		foreach ( (array) $tracks as $track )
		{
			$data = array();

			foreach ( $properties as $key => $name )
			{
				$value = '';

				switch ( $key )
				{
					case 'id':
						$value = $track->{$key};
					break;
					case 'mail_id':
					case 'subject':
					case 'tmstp':
					case 'context':
					case 'ip':
					case 'agent':
					case 'track':
					case 'referrer':
						$value = $track->{$key};
					break;
				};

				if ( empty( $value ) ) continue;

				$data[] = array( 'name' => $name, 'value' => $value, );
			}

			$this->export['data'][] = array(
				'group_id'    => 'mp_tracks',
				'group_label' => __( 'Subscriber > Collected Data', 'MailPress' ),
				'group_description' => __( 'Subscriber&#8217;s mail activity.', 'MailPress' ),
				'item_id'     => "mp_user-{$mp_user->id}-{$track->id}",
				'data'        => $data,
			);
		}

		$this->export['done'] = ( $total == $current );

		return $this->export;
	}
}
new MP_WP_Privacy_exporter_tracking( __( 'MailPress tracks', 'MailPress' ) );
}