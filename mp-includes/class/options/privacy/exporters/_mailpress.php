<?php /* mailpress */
class MP_WP_Privacy_exporter_mailpress extends MP_WP_privacy_exporter_
{
	var $priority = 40;

	function exporter( $email, $page = 1 )
	{
		$mp_user = $this->get_user( $email );
		if ( !$mp_user ) return $this->export;

		$properties = array(
			'id' 				=> __( 'Id', 'MailPress' ),
			'email' 			=> __( 'Email', 'MailPress' ),
			'name' 			=> __( 'Name', 'MailPress' ),
			'confkey' 			=> __( 'Internal Key', 'MailPress' ),
			'created' 			=> __( 'Registration Date', 'MailPress' ),
			'created_IP' 		=> __( 'Registration Ip', 'MailPress' ),
			'created_agent' 	=> __( 'Registration UserAgent', 'MailPress' ),
			'created_user_id' 	=> __( 'Registration WP ID', 'MailPress' ),
			'laststatus' 		=> __( 'Last Status Date', 'MailPress' ),
			'laststatus_IP' 	=> __( 'Last Status Ip', 'MailPress' ),
			'laststatus_agent' 	=> __( 'Last Status UserAgent', 'MailPress' ),
			'laststatus_user_id' 	=> __( 'Last Status WP ID', 'MailPress' ),
		);

		$data = array();

		foreach ( $properties as $key => $name )
		{
			$value = '';

			switch ( $key )
			{
				case 'id':
				case 'email':
				case 'name':
				case 'confkey':
				case 'created':
				case 'created_IP':
				case 'created_user_id':
				case 'laststatus':
				case 'laststatus_IP':
				case 'laststatus_agent':
				case 'laststatus_user_id':
					$value = $mp_user->{$key};
				break;
			}

			if ( empty( $value ) ) continue;

			$data[] = array( 'name'  => $name, 'value' => $value, );
		}

		$this->export['data'][] = array(
			'group_id'    => 'mp_user',
			'group_label' => __( 'Subscriber', 'MailPress' ),
			'item_id'     => "mp_user-{$mp_user->id}",
			'data'        => $data,
		);

		if ( class_exists( 'MailPress_sync_wordpress_user' ) )
		{
			$wp_users = MailPress_sync_wordpress_user::get_wp_users_by_mp_user_id( $mp_user->id );
			if ( $wp_users )
			{
				$extra_info = array(
					'ID'			=> __( 'WP User ID synch',  'MailPress' ),
					'user_email'	=> __( 'WP User Email (reminder)', 'MailPress' ),
					'display_name'	=> __( 'WP User Name  (reminder)', 'MailPress' ),
				);

				foreach ( (array) $wp_users as $wp_user )
				{
					$data = array();

					foreach ( $extra_info as $key => $name )
					{
						$value = '';

						switch ( $key )
						{
							case 'ID':
							case 'user_email':
							case 'display_name':
								$value = $wp_user->{$key};
							break;
						}

						if ( empty( $value ) ) continue;

						$data[] = array( 'name'  => $name, 'value' => $value, );
					}

					$this->export['data'][] = array(
						'group_id'    => 'mp_user',
						'group_label' => __( 'Subscriber', 'MailPress' ),
						'group_description' => __( 'Subscriber&#8217;s informations.', 'MailPress' ),
						'item_id'     => "mp_user-{$mp_user->id}-{$wp_user->ID}",
						'data'        => $data,
					);
				}
			}
		}

		return $this->export;	
	}
}
new MP_WP_Privacy_exporter_mailpress( __( 'MailPress core', 'MailPress' ) );