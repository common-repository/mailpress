<?php
class MP_Tracking_metabox_u005 extends MP_tracking_metabox_sysinfo_
{
	var $id	= 'u005';
	var $context= 'side';
	var $file 	= __FILE__;

	var $item_id = 'user_id';
	var $query = false;

	function extended_meta_box( $mp_user )
	{
		global $wpdb;
		$tracks = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT agent, ip FROM $wpdb->mp_tracks WHERE user_id = %d LIMIT 10;", $mp_user->id ) );
		if ( empty( $tracks ) ) return;

		$out = '<table>';
		foreach( $tracks as $track ) 
		{
			$os      = apply_filters( 'MailPress_tracking_useragents_os_get_info',      $track->agent );
			$browser = apply_filters( 'MailPress_tracking_useragents_browser_get_info', $track->agent );
//			$out .= $os . ' ' . $browser . '&#160;&#160;&#160;@&#160;' . $track->ip . '<br />'; 
			$out .= '<tr><td>' . $os . '</td><td>' . $browser . '</td><td class="num">' . $track->ip . '</td></tr>';
		}
		$out .= '</table>';

		echo $out;
	}
}
new MP_Tracking_metabox_u005( __( 'System info', 'MailPress' ) );