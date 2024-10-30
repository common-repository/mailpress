<?php
class MP_Map
{
	const provider = 'm';

	public static function print_styles() 
	{
		wp_register_style(  'mapbox', 		'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.css' );
		wp_register_style(  'mp_map', 		'/' . MP_PATH . 'mp-includes/class/options/map/mapbox/map_mapbox.css', array( 'mapbox' ) );

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
			'mapboxtoken'	=> $mp_general['mapboxtoken'],
		);

		wp_register_script( 'mapbox', 		'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.js', false, null, 1 );
		wp_register_script( 'mp_map', 		'/' . MP_PATH . 'mp-includes/class/options/map/mp_map_m.js', array( 'mapbox', 'schedule' ), false, 1 );
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
			'mapboxtoken'	=> $mp_general['mapboxtoken'],
		 );

		$h['h'][] = '<link rel="stylesheet" href="https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.css" type="text/css" media="all" />';
		$h['h'][] = '<link rel="stylesheet" href="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/mapbox/map_mapbox.css" type="text/css" media="all" />';

		if ( !isset( $options['jQuery'] ) ) $h['h'][] = '<script type="text/javascript" src="' . site_url() . '/wp-includes/js/jquery/jquery.js"></script>';

		if ( !isset( $options['gmap'] ) ) 
		{
			$h['f'][] = '<script type="text/javascript" src="https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.js"></script>';
			$h['f'][] = '<script type="text/javascript" src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.0.0/mapbox-gl-geocoder.min.js"></script>';
		}

		$h['f'][] = '<script type="text/javascript" src="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/mp_map_m_form_geotag.js"></script>';

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
		$m = array(	'SATELLITE' 	=> 'satellite-v9',
					'HYBRID'		=> 'satellite-streets-v11',
					'TERRAIN'		=> 'outdoors-v11',
					'ROADMAP'		=> 'streets-v11',
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
						'pitch'		=> 0, 
   						'size'		=> '300x300', 
						'maptype'		=> 'ROADMAP', 
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$m = $lng . ',' . $lat;
		$c = $center_lng . ',' . $center_lat;

		$src  = 'https://api.mapbox.com/styles/v1/mapbox/' . self::get_maptype( $maptype ) . '/static/';

		$src .= 'pin-l+39C';				// marker icon + color
		$src .= '(' . $m . ')'; 			// marker pos

		$src .= '/' . $c . ',';			// center
		$src .= $zoom . ',';				// zoom 
		$src .= $bearing . ',';			// bearing (rotation)
		$src .= $pitch;					// pitch   (perspective effect)
		$src .= '/' . $size;				// size

		$src .= '?access_token=' . $mp_general['mapboxtoken'];

		return $src;
	}

	public static function get_address( $lng, $lat )
	{
		global $mp_general;

		$HTTP_USER_AGENT = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT' );

		$args = array();
		$args['access_token'] = $mp_general['mapboxtoken'];

		$url  = add_query_arg( $args, 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . $lng . ',' . $lat .'.json' );

		$http = wp_remote_retrieve_body( wp_remote_get( $url, array( 'user-agent' => $HTTP_USER_AGENT ) ) );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );

		if ( isset( $json->features[0] ) ) $json = $json->features[0];
		else return false;

		return $json->place_name ?? '';
	}

	public static function get_lnglat( $addr )
	{
		$HTTP_USER_AGENT = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT' );

		$count = 1;
		$addr = trim( $addr );
		$addr = str_replace( array( "\r", "\n", "\t", ',', '.', '  ' ), ' ', $addr );
		while( $count) $addr = str_replace( '  ', ' ', $addr, $count );
		$addr = implode( '+', explode( ' ', $addr ) );

		global $mp_general;

		$args = array();
		$args['access_token'] = $mp_general['mapboxtoken'];

		$url = add_query_arg( $args, 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . $addr . '.json' );

		$http = wp_remote_retrieve_body( wp_remote_get( $url, array( 'user-agent' => $HTTP_USER_AGENT ) ) );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );

		if ( isset( $json->features[0] ) ) $json = $json->features[0];
		else return false;

		$a =    $json->place_name               ?? '';
		$lat =  $json->geometry->coordinates[1] ?? 0;
		$lng =  $json->geometry->coordinates[0] ?? 0;

		if ( ( $lng == 0 ) && ( $lat == 0 ) ) return false;

		return array( 'lng' => (float) $lng, 'lat' => (float) $lat, 'addr' => $a );
	}
}