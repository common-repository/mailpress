<?php
class MP_Tracking_metabox_u006 extends MP_tracking_metabox_
{
	const prefix = 'tracking_u006';

	var $id	= 'u006';
	var $context= 'normal';
	var $file 	= __FILE__;

	function __construct( $title )
	{
		add_filter( 'MailPress_scripts', array( $this, 'scripts' ), 8, 2 );
		parent::__construct( $title );
	}
	
	function scripts( $scripts )
	{
		if ( !isset( MP_AdminPage::$get_['id'] ) ) return $scripts;

		$scripts[] = MP_Map::print_scripts();

		return $scripts;
	}

	function meta_box( $mp_user )
	{
	// u006
		global $wpdb;
		$t = array();
		$t['t006']['settings'] = null;

	// t006 markers
		$tracks = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT ip, user_id FROM $wpdb->mp_tracks WHERE user_id = %d ;", $mp_user->id ) );

		if ( $tracks )
		{
			foreach( $tracks as $track )
			{
				$y = MP_Ip::get_all( $track->ip );

				if ( !isset( $y['geo'] ) ) continue;

				$x = $y['geo'];
				if ( !isset( $def_lat ) && isset( $x['lat'] ) ) $def_lat = $x['lat'];
				if ( !isset( $def_lng ) && isset( $x['lng'] ) ) $def_lng = $x['lng'];
				$x['ip'] = $track->ip;

				if ( isset( $y['html'] ) )     $x['info']  = str_replace( '"', '&quote;', $y['html'] );
				if ( isset( $y['provider'] ) ) $x['info'] .= str_replace( '"', '&quote;', '<div><p style=\'margin:3px;\'><i><small>' . sprintf( __( 'ip data provided by %1$s', 'MailPress' ), $y['provider']['credit'] ) . '</small></i></p></div>' );

				$t['t006']['markers'][] = $x;
			}
		}

	// t006 settings
		$t['t006']['settings'] = MP_User_meta::get( $mp_user->id, '_MailPress_' . self::prefix );
		if ( !$t['t006']['settings'] ) $t['t006']['settings'] = get_user_option( '_MailPress_' . self::prefix );
		if ( !isset( $def_lat ) ) $def_lat = 48.8352;
		if ( !isset( $def_lng ) ) $def_lng = 2.4718;
		if ( !$t['t006']['settings'] ) $t['t006']['settings'] = array( 'center_lat' => $def_lat, 'center_lng' => $def_lng, 'zoomlevel' => 3, 'maptype' => 'NORMAL' );
		$t['t006']['settings']['prefix'] = self::prefix;
		$t['t006']['settings']['count'] = ( isset( $t['t006']['markers'] ) ) ? count( $t['t006']['markers'] ) : 0;

		$out = '';

		$out .= '<script type="text/javascript">' . "\r\n";
		$out .= '/* <![CDATA[ */' . "\r\n";
		foreach ( $t as $var => $val )
		{
			$out .= 'var ' . $var . ' = ' . MP_AdminPage::print_scripts_l10n_val( $val );
		}
		$out .= ';' . "\r\n";
		$out .= '/* ]]> */' . "\r\n";
		$out .= '</script>' . "\r\n";

		$out .= '<div id="' . self::prefix . '_map" style="overflow:hidden;height:500px;width:auto;padding:0;margin:0;"></div>';

		foreach( $t['t006']['settings'] as $k => $v ) 
		{
                if ( 'prefix' == $k ) continue;
			$out .= '<input type="hidden" id="' . self::prefix . '_' . $k . '" value="' . esc_attr( $v ) . '" />';
		}

		echo $out;
	}
}
new MP_Tracking_metabox_u006( __( 'Geoip', 'MailPress' ) );