<?php
class MP_Map
{
	const provider = 'b';

	public static function print_styles() 
	{
		wp_register_style(  'mp_map', 		'/' . MP_PATH . 'mp-includes/class/options/map/bing/map_bing.css' );

		return 'mp_map';
	}

	public static function print_scripts()  
	{
		add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ), 99);

		global $mp_general;

		$mp_mapL10n = array( 
			'id'			=> MP_AdminPage::$get_['id'],
			'type'		=> MP_AdminPage::map_of,
			'fullscreen' 	=> esc_js( __( 'Full screen', 'MailPress' ) ),
			'center'		=> esc_js( __( 'Center', 'MailPress' ) ),
			'changemap'	=> esc_js( __( 'Change map', 'MailPress' ) ),
			'bmapkey'		=> $mp_general['bmapkey'],
		);

		wp_register_script( 'mp_map', '/' . MP_PATH . 'mp-includes/class/options/map/mp_map_b.js', array( 'schedule' ), false, 1 );
		wp_localize_script( 'mp_map', 'mp_mapL10n', $mp_mapL10n );

		return 'mp_map';
	}

	public static function admin_footer()  
	{
		$h = array();
		$h[] = '';

		$h = array_merge( $h, self::get_bing_scripts() );

		echo implode( "\r\n", $h );
	}

	public static function get_bing_scripts()  
	{
		global $mp_general;

		$callback = 'MicroSoftBing';
		$src = 'https://www.bing.com/api/maps/mapcontrol';

		$args = array();
		$args['callback'] = $callback;
		$args['key'] = $mp_general['bmapkey'];

		$src  = add_query_arg( $args, $src );

		$h = array();

		$h[] = '<script type="text/javascript"> function ' . $callback . '() { for (var i in MAILPRESS_data) new mp_map_bing(MAILPRESS_data[i]); } </script>';
		$h[] = '<script type="text/javascript" src="' . $src . '" async defer></script>';

		return $h;
	}

	public static function form_geotag( $options )
	{
		global $mp_general;

		$h = array();

		$mp_mapL10n = array( 
			'fullscreen' 	=> esc_js( __( 'Full screen', 'MailPress' ) ),
			'center'		=> esc_js( __( 'Center on marker', 'MailPress' ) ), 
			'rgeocode'		=> esc_js( __( 'Find marker address', 'MailPress' ) ), 
			'changemap'	=> esc_js( __( 'Change map', 'MailPress' ) ),
		 );

		$callback = 'MicroSoftBing';
		$src = 'https://www.bing.com/api/maps/mapcontrol';

		$args = array();
		$args['callback'] = $callback;
		$args['key'] = $mp_general['bmapkey'];

		$src  = add_query_arg( $args, $src );

		$h['h'][] = '<link rel="stylesheet" href="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/bing/map_bing.css" type="text/css" media="all" />';

		if ( !isset( $options['jQuery'] ) ) $h['h'][] = '<script type="text/javascript" src="' . site_url() . '/wp-includes/js/jquery/jquery.js"></script>';

		if ( !isset( $options['gmap'] ) )   foreach( self::get_bing_scripts() as $s ) $h['f'][] = $s;
		$h['f'][] = '<script type="text/javascript" src="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/mp_map_b_form_geotag.js"></script>';

		$h['f'][] = '<script type="text/javascript">';
		$h['f'][] = '/* <![CDATA[ */';
		foreach ( array( 'mp_mapL10n' => $mp_mapL10n ) as $var => $val ) $h['f'][] = "var $var = " . MP_::print_scripts_l10n_val( $val );
		$h['f'][] = ';';
		$h['f'][] = '/* ]]> */';
		$h['f'][] = '</script>';

		return $h;
	}

	public static function get_maptype( $maptype )
	{
		$m = array(	'SATELLITE' 	=> 'Aerial',
					'HYBRID'		=> 'AerialWithLabels',
					'TERRAIN'		=> 'CanvasLight',
					'ROADMAP'		=> 'Road',
		);

		return $m[$maptype] ?? $m['ROADMAP'];
	}

	public static function get_staticmap( $ip, $args = '' )
	{
		global $mp_general;

		$x['geo'] = array( 'lng' => 0, 'lat' => 0 );

		if ( $ip )
		{
			$x  = MP_Ip::get_all( $ip );

			if ( !$x['geo']['lat'] && !$x['geo']['lng'] ) return array( 'src' => false, 'addr' => false );
		}

		$defaults = array ( 	'lng'			=> $x['geo']['lng'],
						'lat'			=> $x['geo']['lat'],
					 	'center_lng'	=> $x['geo']['lng'],
						'center_lat'	=> $x['geo']['lat'], 
						'zoom'		=> 4, 
   						'size'		=> '300x300', 
						'maptype'		=> 'ROADMAP',
						'format'		=> 'png',
						'key'			=> $mp_general['bmapkey'],
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$url  = '';
		$url .= 'https://dev.virtualearth.net/REST/V1/Imagery/Map/';
		$url .= self::get_maptype( $maptype ) . '/';
		$url .= $center_lat . ',' . $center_lng  . '/';
		$url .= $zoom;

		$args = array();
		$args['mapSize'] = str_replace( 'x', ',', $size );
		$args['format'] = 'png';
		$args['pushpin'] = $lat . ',' . $lng  ;
		$args['key']     = $mp_general['bmapkey'];

		return add_query_arg( $args, $url );
	}

	public static function get_address( $lng, $lat )
	{
		global $mp_general;

		$HTTP_USER_AGENT = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT' );

		$url = 'http://dev.virtualearth.net/REST/v1/Locations/' . $lat . ',' . $lng;

		$args = array();
		$args['key'] = $mp_general['bmapkey'];

		$url  = add_query_arg( $args, $url );

		$http = wp_remote_retrieve_body( wp_remote_get( $url, array( 'user-agent' => $HTTP_USER_AGENT ) ) );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );

		if ( isset( $json->resourceSets[0]->resources[0] ) ) $json = $json->resourceSets[0]->resources[0];
		else return false;

		return $json->name ?? '';
	}

	public static function get_lnglat( $addr )
	{
		$HTTP_USER_AGENT = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT' );

		$count = 1;
		$addr = trim( $addr );
		$addr = str_replace( array( "\r", "\n", "\t", '  ' ), ' ', $addr );
		while( $count) $addr = str_replace( '  ', ' ', $addr, $count );
		$addr = implode( '+', explode( ' ', $addr ) );

		global $mp_general;

		$args = array();
		$args['q'] = $addr;
		$args['maxResults'] = 1;
		$args['key'] = $mp_general['bmapkey'];

		$url  = add_query_arg( $args, 'http://dev.virtualearth.net/REST/v1/Locations' );

		$http = wp_remote_retrieve_body( wp_remote_get( $url, array( 'user-agent' => $HTTP_USER_AGENT ) ) );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );

		if ( isset( $json->resourceSets[0]->resources[0] ) ) $json = $json->resourceSets[0]->resources[0];
		else return false;

		$a =    $json->name                  ?? '';
		$lat =  $json->point->coordinates[0] ?? 0;
		$lng =  $json->point->coordinates[1] ?? 0;

		if ( ( $lng == 0 ) && ( $lat == 0 ) ) return false;

		return array( 'lng' => (float) $lng, 'lat' => (float) $lat, 'addr' => $a );
	}
}