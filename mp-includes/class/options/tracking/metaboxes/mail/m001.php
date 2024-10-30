<?php
class MP_Tracking_metabox_m001 extends MP_tracking_metabox_
{
	var $id	= 'm001';
	var $context= 'normal';
	var $file 	= __FILE__;

	function meta_box( $mail )
	{
		global $wpdb;
		$tracks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->mp_tracks WHERE mail_id = %d ORDER BY tmstp DESC LIMIT 10;", $mail->id ) );
		if ( $tracks ) 
		{
			$out = '<table>';
			foreach( $tracks as $track ) 
			{
				$tracking_url = esc_url( MP_::url( MailPress_tracking_u, array( 'id' => $track->user_id ) ) );
				$action = '<a href="' . $tracking_url . '" target="_blank" title="' . esc_attr( __( 'See tracking results', 'MailPress' ) ) . '">' . MP_User::get_email( $track->user_id ) . '</a>';
				$out .= '<tr><td><abbr title="' . esc_attr( $track->tmstp ) . '">' . substr( $track->tmstp, 0, 10 ) . '</abbr></td><td>&#160;' . $action . '</td><td>&#160;' . MailPress_tracking::translate_track( $track->track, $track->mail_id ) . '</td></tr>';
			}
			$out .= '</table>';

			echo $out;
		}
	}
}
new MP_Tracking_metabox_m001( __( 'Last 10 actions', 'MailPress' ) );
