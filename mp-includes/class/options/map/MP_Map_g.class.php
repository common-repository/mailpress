<?php
class MP_Map
{
	const provider = 'g';

	public static function print_styles() 
	{
		wp_register_style(  'mp_map', 		'/' . MP_PATH . 'mp-includes/class/options/map/google/map_google.css' );

		return 'mp_map';
	}

	public static function print_scripts()  
	{
		global $mp_general;

		$mp_mapL10n = array( 
			'id'			=> MP_AdminPage::$get_['id'],
			'type'		=> MP_AdminPage::map_of,
			'fullscreen' 	=> esc_js( __( 'Full screen', 'MailPress' ) ),
			'center'		=> esc_js( __( 'Center', 'MailPress' ) ),
			'changemap'	=> esc_js( __( 'Change map', 'MailPress' ) ),
			'url'			=> site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/google/images/',
		);

		wp_register_script( 'gmap_c', '/' . MP_PATH . 'mp-includes/class/options/map/google/markerclusterer.js', false, false, 1 );
		wp_register_script( 'gmap',   'https://maps.googleapis.com/maps/api/js?key=' . $mp_general['gmapkey'], array( 'gmap_c' ), null, 1 );
		wp_register_script( 'mp_map', '/' . MP_PATH . 'mp-includes/class/options/map/mp_map_g.js', array( 'gmap', 'schedule' ), false, 1 );
		wp_localize_script( 'mp_map', 'mp_mapL10n', $mp_mapL10n );

		return 'mp_map';
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

		$h['h'][] = '<link rel="stylesheet" href="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/google/map_google.css" type="text/css" media="all" />';

		if ( !isset( $options['jQuery'] ) ) $h['h'][] = '<script type="text/javascript" src="' . site_url() . '/wp-includes/js/jquery/jquery.js"></script>';

		if ( !isset( $options['gmap']   ) ) $h['f'][] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=' . $mp_general['gmapkey'] . '"></script>';
		$h['f'][] = '<script type="text/javascript" src="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/mp_map_g_form_geotag.js"></script>';

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
		$m = array(	'SATELLITE' 	=> 'satellite',
					'HYBRID'		=> 'hybrid',
					'TERRAIN'		=> 'terrain',
					'ROADMAP'		=> 'roadmap',
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
						'bearing'		=> 0,
						'pitch'		=> 50, 
   						'size'		=> '300x300', 
						'maptype'		=> 'ROADMAP', 
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$args = array();
		$args['center']  = $center_lat . ',' . $center_lng;
		$args['zoom']    = $zoom;
		$args['size']    = $size;
		$args['maptype'] = self::get_maptype( $maptype );
		$args['markers'] = $lat . ',' . $lng;
		$args['key']     = $mp_general['gmapkey'];

		return add_query_arg( $args, 'https://maps.googleapis.com/maps/api/staticmap' );
	}

	public static function get_address( $lng, $lat )
	{
		global $mp_general;

		$args = array();
		$args['latlng'] = settype( $lat, 'float' ) . ',' . settype( $lng, 'float' );
		$args['key'] = $mp_general['gmapkey'];

		$url  = add_query_arg( $args, 'https://maps.googleapis.com/maps/api/geocode/json' );

		$http = file_get_contents( $url );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );
		if ( !$json ) return false;
		if ( 'OK' != $json->status ) return false;

		if ( isset( $json->results[0] ) ) $json = $json->results[0];
		else return false;

		return $json->formatted_address ?? '';
	}

	public static function get_lnglat( $addr )
	{
		$count = 1;
		$addr = trim( $addr );
		$addr = str_replace( array( "\r", "\n", "\t", '  ' ), ' ', $addr );
		while( $count) $addr = str_replace( '  ', ' ', $addr, $count );
		$addr = implode( '+', explode( ' ', $addr ) );

		global $mp_general;

		$args = array();
		$args['address'] = $addr;
		$args['key'] = $mp_general['gmapkey'];

		$url  = add_query_arg( $args, 'https://maps.googleapis.com/maps/api/geocode/json' );

		$http = file_get_contents( $url );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );
		if ( !$json ) return false;
		if ( 'OK' != $json->status ) return false;

		if ( isset( $json->results[0] ) ) $json = $json->results[0];
		else return false;

		$a =    $json->formatted_address       ?? '';
		$lat =  $json->geometry->location->lat ?? 0;
		$lng =  $json->geometry->location->lng ?? 0;

		if ( ( $lng == 0 ) && ( $lat == 0 ) ) return false;

		return array( 'lng' => (float) $lng, 'lat' => (float) $lat, 'addr' => $a );
	}
}