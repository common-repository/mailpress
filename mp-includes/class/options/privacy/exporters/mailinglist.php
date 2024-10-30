<?php /* mailinglist */
if ( class_exists( 'MailPress' ) && class_exists( 'MailPress_mailinglist' ) )
{
class MP_WP_Privacy_exporter_mailinglist extends MP_WP_privacy_exporter_
{
	const per_page = 100;

	var $priority = 42;

	function exporter( $email, $page = 1 )
	{
		$mp_user = $this->get_user( $email );
		if ( !$mp_user ) return $this->export;

		/*  */

		$start = ( $page - 1 ) * self::per_page;

		$items = $this->get_items();

		if ( !$items ) return $this->export;

		$mailinglists = array_slice( $items, $start, self::per_page, true );

		$total = count( $items );
		$current = $start + count( $mailinglists );

		/*  */

		$properties = array(
			'id' => __( 'Mailinglist Id', 'MailPress' ),
			'name' => __( 'Mailinglist Name', 'MailPress' ),
		);

		foreach ( (array) $mailinglists as $i => $n )
		{
			$data = array();

			foreach ( $properties as $key => $name )
			{
				$value = '';

				switch ( $key )
				{
					case 'id':
						$value = $i;
					break;
					case 'name':
						$value = $n;
					break;
				};

//				if ( empty( $value ) ) continue;

				$data[] = array( 'name' => $name, 'value' => $value, );
			}

			$this->export['data'][] = array(
				'group_id'    => 'mp_mailinglists',
				'group_label' => _n( 'Subscriber > Mailinglist', 'Subscriber > Mailinglists', $total, 'MailPress' ),
				'group_description' => _n( 'Subscriber&#8217;s mailinglist subscription.', 'Subscriber&#8217;s mailinglist subscriptions.', $total, 'MailPress' ),
				'item_id'     => "mp_user-{$mp_user->id}-{$i}",
				'data'        => $data,
			);
		}

		$this->export['done'] = ( $total == $current );

		return $this->export;
	}

	function get_items()
	{
		return MailPress_mailinglist::get_subscriptions( $this->mp_user_id );
	}
}
new MP_WP_Privacy_exporter_mailinglist( __( 'MailPress mailinglists', 'MailPress' ) );
}