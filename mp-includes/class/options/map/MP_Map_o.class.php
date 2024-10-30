<?php
class MP_Map
{
	const provider = 'o';

	public static function print_styles() 
	{
		wp_register_style(  'leaflet', 		'/' . MP_PATH . 'mp-includes/class/options/map/leaflet/leaflet.css' );
		wp_register_style(  'leaflet_mc', 	'/' . MP_PATH . 'mp-includes/class/options/map/leaflet/leaflet.markercluster.css' );
		wp_register_style(  'mp_map', 		'/' . MP_PATH . 'mp-includes/class/options/map/leaflet/map_leaflet.css', array( 'leaflet', 'leaflet_mc' ) );

		return 'mp_map';
	}

	public static function print_scripts()  
	{
		$mp_mapL10n = array( 
			'id'			=> MP_AdminPage::$get_['id'],
			'type'		=> MP_AdminPage::map_of,
			'fullscreen' 	=> esc_js( __( 'Full screen', 'MailPress' ) ),
			'center'		=> esc_js( __( 'Center', 'MailPress' ) ),
			'changemap'	=> esc_js( __( 'Change map', 'MailPress' ) ),
		);

		wp_register_script( 'leaflet', 		'/' . MP_PATH . 'mp-includes/class/options/map/leaflet/leaflet.js', false, false, 1 );
		wp_register_script( 'leaflet_c', 	'/' . MP_PATH . 'mp-includes/class/options/map/leaflet/leaflet.markercluster.js', array( 'leaflet' ), false, 1 );
		wp_register_script( 'mp_map', 		'/' . MP_PATH . 'mp-includes/class/options/map/mp_map_o.js', array( 'leaflet_c', 'schedule' ), false, 1 );
		wp_localize_script( 'mp_map', 'mp_mapL10n', $mp_mapL10n );

		return 'mp_map';
	}

	public static function form_geotag( $options )
	{
		$h = array();

		$mp_mapL10n = array( 
			'fullscreen' 	=> esc_js( __( 'Full screen', 'MailPress' ) ),
			'center'		=> esc_js( __( 'Center on marker', 'MailPress' ) ), 
			'rgeocode'		=> esc_js( __( 'Find marker address', 'MailPress' ) ), 
			'changemap'	=> esc_js( __( 'Change map', 'MailPress' ) ),
			'url'			=> admin_url( 'admin-ajax.php' ),
		);

		$h['h'][] = '<link rel="stylesheet" href="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/leaflet/leaflet.css"     type="text/css" media="all" />';
		$h['h'][] = '<link rel="stylesheet" href="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/leaflet/map_leaflet.css" type="text/css" media="all" />';

		if ( !isset( $options['jQuery'] ) ) $h['h'][] = '<script type="text/javascript" src="' . site_url() . '/wp-includes/js/jquery/jquery.js"></script>';

		if ( !isset( $options['gmap']   ) ) $h['f'][] = '<script type="text/javascript" src="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/leaflet/leaflet.js"></script>';
		$h['f'][] = '<script type="text/javascript" src="' . site_url() . '/' . MP_PATH . 'mp-includes/class/options/map/mp_map_o_form_geotag.js"></script>';

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
		$m = array(	'ROADMAP'		=> 'osmarenderer',
		);

		return $m[$maptype] ?? $m['ROADMAP'];
	}

	public static function get_staticmap( $ip, $args = '' )  
	{
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

		$m = $lat . ',' . $lng;
		$c = $center_lat . ',' . $center_lng;

		$args = array();
		$args['center']  = $c;
		$args['zoom']    = $zoom;
		$args['size']    = $size;
		$args['maptype'] = 'osmarenderer';
		$args['markers'] = $m . ',lightblue1';

		return add_query_arg( $args, 'https://staticmap.openstreetmap.de/staticmap.php' );
	}

	public static function get_address( $lng, $lat )
	{
		$HTTP_USER_AGENT = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT' );

		$args = array();
		$args['format']  = 'jsonv2';
		$args['addressdetails'] = 0;
		$args['lat'] = $lat;
		$args['lon'] = $lng;

		$url  = add_query_arg( $args, 'https://nominatim.openstreetmap.org/reverse' );

		$http = wp_remote_retrieve_body( wp_remote_get( $url, array( 'user-agent' => $HTTP_USER_AGENT ) ) );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );
		if ( !$json ) return false;

		return $json->display_name ?? false;
	}

	public static function get_lnglat( $addr )
	{
		$count = 1;
		$addr = trim( $addr );
		$addr = str_replace( array( "\r", "\n", "\t", ',', '.', '  ' ), ' ', $addr );
		while( $count) $addr = str_replace( '  ', ' ', $addr, $count );
		$addr = implode( '+', explode( ' ', $addr ) );

		$HTTP_USER_AGENT = filter_input( INPUT_SERVER, 'HTTP_USER_AGENT' );

		$args = array();
		$args['format'] = 'jsonv2';
		$args['addressdetails'] = 0;
		$args['limit'] = 1;
		$args['q'] = $addr;

		$url  = add_query_arg( $args, 'https://nominatim.openstreetmap.org/search' );

		$http = wp_remote_retrieve_body( wp_remote_get( $url, array( 'user-agent' => $HTTP_USER_AGENT ) ) );
		if ( !$http || empty( $http ) ) return false;

		$json = json_decode( $http );
		if ( !$json ) return false;

		if ( isset( $json[0] ) ) $json = $json[0];
		else return false;
		
		$a =    $json->display_name ?? '';
		$lat =  $json->lat          ?? 0;
		$lng =  $json->lon          ?? 0;

		if ( ( $lng == 0 ) && ( $lat == 0 ) ) return false;

		return array( 'lng' => (float) $lng, 'lat' => (float) $lat, 'addr' => $a );
	}
}