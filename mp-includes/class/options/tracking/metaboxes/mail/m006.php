<?php
class MP_Tracking_metabox_m006 extends MP_tracking_metabox_
{
	const prefix = 'tracking_m006';

	var $id	= 'm006';
	var $context= 'side';
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

	function meta_box( $mail )
	{
	// m006
		global $wpdb;
		$t = array();
		$t['t006']['settings'] = null;

	// t006 markers
		$tracks = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT ip, user_id FROM $wpdb->mp_tracks WHERE mail_id = %d ", $mail->id ) );

		if ( $tracks )
		{
			foreach( $tracks as $track )
			{
				$x = MP_Ip::get_latlng( $track->ip );

				if ( !$x ) continue;

				if ( !isset( $def_lat ) && isset( $x['lat'] ) ) $def_lat = $x['lat'];
				if ( !isset( $def_lng ) && isset( $x['lng'] ) ) $def_lng = $x['lng'];
				$x['ip'] = $track->ip;

				$user = MP_User::get( $track->user_id );
				if ( get_option( 'show_avatars' ) ) $x['info'] = get_avatar( $user->email, 32 );
				$flag   = ( ( 'ZZ' == $user->created_country ) || empty( $user->created_country ) ) ? '' : '<div class="mp_flag mp_flag_' . strtolower( $user->created_country ) . '" title="' . esc_attr( strtolower( $user->created_country ) ) . '"></div>';
				$x['info'] = '<table><tr><td style="text-align:center;">' . ( ( get_option( 'show_avatars' ) ) ? str_replace( "'", '"', get_avatar( $user->email, 32 ) ) : '' ) . '<br style="line-height:0;" /><br style="line-height:3px;" />' . $flag . '</td><td style="text-align:center;padding-left:5px;">' . $user->email . '<br />' . $user->name . '<br />' . $track->ip . '</td></tr></table>';

				$t['t006']['markers'][] = $x;
			}
		}

	// t006 settings
		$t['t006']['settings'] = MP_Mail_meta::get( $mail->id, '_MailPress_' . self::prefix );
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
new MP_Tracking_metabox_m006( __( 'Geoip', 'MailPress' ) );