<?php
class MP_Dashboard_users_map extends MP_WP_Dashboard_widget_
{
	const option_name = 'MailPress_dashboard_mp_map';

	var $id = 'mp_users_map';

	function widget()
	{
		global $wpdb, $wp_locale;

		if ( !$options = get_option( self::option_name ) )
		{
			$options['code'] = 'world';
			$options['title'] = __( 'Subscribers - World', 'MailPress' );
		}

		$chd = $chld = array();

		$where = 'status="active"';
		$where .= ( 'usa' == $options['code'] ) ? " AND created_country = 'US' AND created_US_state <> 'ZZ'" : " AND created_country <> 'ZZ'";

		$countalls = $wpdb->get_var( "SELECT count( * ) FROM $wpdb->mp_users WHERE $where ;" );
		$users = $wpdb->get_results( "SELECT created_US_state as toto, count( * ) as count FROM $wpdb->mp_users WHERE $where GROUP BY created_US_state;" );

		foreach( $users as $user )
		{
			$chld[] = ( 'UK' == $user->toto ) ? 'GB' : $user->toto;
			$chd[]  = round( 100 * $user->count/$countalls );
		}

		$args = array();
		$args['cht']  = 't';
		$args['chs']  = $this->widget_size( '440x200' );
		$args['chtm'] = $options['code'];
		$args['chf']  = 'bg,s,EAF7FE';
		if ( !empty( $chld ) ) $args['chld'] = join( '', $chld );
		$args['chco'] = 'ffffff,B5F8C2,294D30';
		$args['chd']  = ( empty( $chd ) ) ? 's:_' : 't:' . join( ',', $chd );
		$url = esc_url( add_query_arg( $args, $this->url ) );

		echo '<div style="text-align:center;"><img style="width:100%;" src="' . $url . '" alt="' . esc_attr( $options['title'] ) . '" /></div>' . "\r\n";
	}

	function control()
	{
		$c= array ( 	'africa' 		=> __( 'Africa', 'MailPress' ),
				'asia'		=> __( 'Asia', 'MailPress' ),
				'europe'		=> __( 'Europe', 'MailPress' ),
				'middle_east'	=> __( 'Middle East', 'MailPress' ),
				'south_america'	=> __( 'South America', 'MailPress' ),
				'usa'			=> __( 'USA', 'MailPress' ),
				'world'		=> __( 'World', 'MailPress' ) );

		if ( !$options = get_option( self::option_name ) )
		{
			$options['code'] = 'world';
			$options['title'] = $c[$options['code']];
		}
		if ( $post_ = filter_input_array( INPUT_POST ) && isset( $post_['code'] ) ) 
		{	
			update_option( self::option_name, array( 'code' => $post_['code'] , 'title' => $c[$post_['code']] ) );
			return;
		}

		echo '<select name="code" id="code">' . MP_::select_option( $c, $options['code'] ?? false, false ) . '</select>' . "\r\n";
	}
}
new MP_Dashboard_users_map( __( 'MailPress - Subscribers Map', 'MailPress' ) );