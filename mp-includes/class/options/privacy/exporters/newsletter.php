<?php /* newsletter */
if ( class_exists( 'MailPress' ) && class_exists( 'MailPress_newsletter' ) )
{
class MP_WP_Privacy_exporter_newsletter extends MP_WP_privacy_exporter_
{
	const per_page = 100;

	var $priority = 43;

	function exporter( $email, $page = 1 )
	{
		$mp_user = $this->get_user( $email );
		if ( !$mp_user ) return $this->export;

		/*  */

		$start = ( $page - 1 ) * self::per_page;

		$items = $this->get_items();

		if ( !$items ) return $this->export;

		$newsletters = array_slice( $items, $start, self::per_page, true );

		$total = count( $items );
		$current = $start + count( $newsletters );

		/*  */

		$properties = array(
			'id' => __( 'Newsletter Id', 'MailPress' ),
			'name' => __( 'Newsletter Name', 'MailPress' ),
		);

		foreach ( (array) $newsletters as $i => $n )
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
				'group_id'    => 'mp_newsletters',
				'group_label' => _n( 'Subscriber > Newsletter', 'Subscriber > Newsletters', $total, 'MailPress' ),
				'group_description' => _n( 'Subscriber&#8217;s newsletter subscription.', 'Subscriber&#8217;s newsletter subscriptions.', $total, 'MailPress' ),
				'item_id'     => "mp_user-{$mp_user->id}-{$i}",
				'data'        => $data,
			);
		}

		$this->export['done'] = ( $total == $current );

		return $this->export;
	}

	function get_items()
	{
		global $mp_subscriptions, $mp_registered_newsletters;
		$n = array();

		$s = MailPress_newsletter::get_subscriptions( $this->mp_user_id );

		foreach( $mp_subscriptions['newsletters'] as $k => $v )
		{
			if ( !isset( $s[$k] ) ) continue;
			$n[$k] = $mp_registered_newsletters[$k]['descriptions']['blog'];
		}

		return $n; 
	}
}
new MP_WP_Privacy_exporter_newsletter( __( 'MailPress newsletters', 'MailPress' ) );
}