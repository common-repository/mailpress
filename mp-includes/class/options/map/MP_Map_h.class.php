<?php
class MP_Map
{
	const provider = 'h';

	public static function print_styles() 
	{
		wp_register_style(  'here', 		'https://js.api.here.com/v3/3.0/mapsjs-ui.css' );
		wp_register_style(  'mp_map', 		'/' . MP_PATH . 'mp-includes/class/options/map/here/map_here.css', array( 'here' ) );

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
			'app_id'  		=> $mp_general['here_id'],
			'app_code'		=> $mp_general['here_code'],
		);

		wp_register_script( 'here_core', 'https://js.api.here.com/v3/3.0/mapsjs-core.js', false, null, 1 );
		wp_register_script( 'here_serv', 'https://js.api.here.com/v3/3.0/mapsjs-service.js', array( 'here_core' ), null, 1 );
		wp_register_script( 'here_ui',   'https://js.api.here.com/v3/3.0/mapsjs-ui.js', array( 'here_serv' ), null, 1 );
		wp_register_script( 'here_evts',  'https://js.api.here.com/v3/3.0/mapsjs-mapevents.js', array( 'here_ui' ), null, 1 );
		wp_register_script( 'here_c',    'https://js.api.here.com/v3/3.0/mapsjs-clustering.js', array( 'here_evts' ), null, 1 );

		wp_register_script( 'mp_map', 		'/' . MP_PATH . 'mp-includes/class/options/map/mp_map_h.js', array( 'here_c', 'schedule' ), null, 1 );
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
			'app_id'  		=> $mp_general['here_id'],
			'app_code'		=> $mp_general['here_code'],
		 );

		$h['h'][] = '<link rel="stylesheet" href="https://js.api.here.com/v3/3.0/mapsjs-ui.css"          type="text/css" media="all" />';
		$h['h'][] = '<link rel="stylesheet" href="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/here/map_here.css" type="text/css" media="all" />';

		if ( !isset( $options['jQuery'] ) ) $h['h'][] = '<script type="text/javascript" src="' . site_url() . '/wp-includes/js/jquery/jquery.js"></script>';

		if ( !isset( $options['gmap'] ) ) 
		{
			$h['f'][] = '<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-core.js"></script>';
			$h['f'][] = '<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-service.js"></script>';
			$h['f'][] = '<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-ui.js"></script>';
			$h['f'][] = '<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-mapevents.js"></script>';
		}

		$h['f'][] = '<script type="text/javascript" src="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/mp_map_h_form_geotag.js"></script>';

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
		$m = array(	'SATELLITE' 	=> 1,
					'HYBRID'		=> 3,
					'TERRAIN'		=> 2,
					'ROADMAP'		=> 0,
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
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$wh = explode( 'x', $size);
		$w = $wh[0];
		$h = $wh[1];

		$m = $lat . ',' . $lng;
		$c = $center_lat . ',' . $center_lng;

		$url  = 'https://image.maps.api.here.com/mia/1.6/mapview';

		$args = array();
		$args['c'] = $c;
		$args['z'] = $zoom;
		$args['w'] = $w;
		$args['h'] = $h;
		$args['t'] = self::get_maptype( $maptype );
		$args['poi'] = $m;
		$args['poithm'] = 0;
		$args['app_id']  = $mp_general['here_id'];
		$args['app_code']= $mp_general['here_code'];

		return add_query_arg( $args, $url );
	}

	public static function get_address( $lng, $lat )
	{
		global $mp_general;

		$HTTP_USER_AGENT = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT' );

		$args = array();
		$args['prox'] = $lat . ',' . $lng . ',0';
		$args['mode'] = 'retrieveAddresses';
		$args['maxresults'] = 1;
		$args['app_id']  = $mp_general['here_id'];
		$args['app_code']= $mp_general['here_code'];

		$url  = add_query_arg( $args, 'https://reverse.geocoder.api.here.com/6.2/reversegeocode.json' );

		$http = wp_remote_retrieve_body( wp_remote_get( $url, array( 'user-agent' => $HTTP_USER_AGENT ) ) );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );

		if ( isset( $json->Response->View[0]->Result[0]->Location ) ) $json = $json->Response->View[0]->Result[0]->Location;
		else return false;

		return $json->Address->Label ?? '';
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
		$args['searchtext'] = $addr;
		$args['maxresults'] = 1;
		$args['app_id']  = $mp_general['here_id'];
		$args['app_code']= $mp_general['here_code'];

		$url  = add_query_arg( $args, 'https://geocoder.api.here.com/6.2/geocode.json' );

		$http = wp_remote_retrieve_body( wp_remote_get( $url, array( 'user-agent' => $HTTP_USER_AGENT ) ) );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );

		if ( isset( $json->Response->View[0]->Result[0]->Location ) ) $json = $json->Response->View[0]->Result[0]->Location;
		else return false;

		$a =    $json->Address->Label             ?? '';
		$lat =  $json->DisplayPosition->Latitude  ?? 0;
		$lng =  $json->DisplayPosition->Longitude ?? 0;

		if ( ( $lng == 0 ) && ( $lat == 0 ) ) return false;

		return array( 'lng' => (float) $lng, 'lat' => (float) $lat, 'addr' => $a );
	}
}