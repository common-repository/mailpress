<?php /* comment */
if ( class_exists( 'MailPress' ) && class_exists( 'MailPress_comment' ) )
{
class MP_WP_Privacy_exporter_comment extends MP_WP_privacy_exporter_
{
	const per_page = 100;

	var $priority = 41;

	function exporter( $email, $page = 1 )
	{
		$mp_user = $this->get_user( $email );
		if ( !$mp_user ) return $this->export;

		/*  */

		$start = ( $page - 1 ) * self::per_page;

		$items = $this->get_items();

		if ( !$items ) return $this->export;

		$comments = array_slice( $items, $start, self::per_page, true );

		$total = count( $items );
		$current = $start + count( $comments );

		/*  */

		$properties = array(
			'post_id' => __( 'Post id', 'MailPress' ),
			'post_title' => __( 'Post Title', 'MailPress' ),
		);

		foreach ( (array) $comments as $comment )
		{
			$data = array();

			foreach ( $properties as $key => $name )
			{
				$value = '';

				switch ( $key )
				{
					case 'post_id':
					case 'post_title':
						$value = $comment->{$key};
					break;
				};

				if ( empty( $value ) ) continue;

				$data[] = array( 'name' => $name, 'value' => $value, );
			}

			$this->export['data'][] = array(
				'group_id'    => 'mp_comments',
				'group_label' => _n( 'Subscriber > Comment', 'Subscriber > Comments', $total, 'MailPress' ),
				'group_description' => _n( 'Subscriber&#8217;s comment subscription.', 'Subscriber&#8217;s comment subscriptions.', $total, 'MailPress' ),
				'item_id'     => "mp_user-{$mp_user->id}-{$comment->post_id}",
				'data'        => $data,
			);
		}

		$this->export['done'] = ( $total == $current );

		return $this->export;
	}

	function get_items()
	{
		return MailPress_comment::get_subscriptions( $this->mp_user_id );
	}
}
new MP_WP_Privacy_exporter_comment( __( 'MailPress comments', 'MailPress' ) );
}