<?php
class MP_Tracking_metabox_m002all extends MP_tracking_metabox_
{
	var $id	= 'm002all';
	var $context= 'normal';
	var $file 	= __FILE__;

	function meta_box( $mail )
	{
		global $wpdb;
		$tracks = $wpdb->get_results( $wpdb->prepare( "SELECT user_id, MAX( tmstp ) as tmstp FROM $wpdb->mp_tracks WHERE mail_id = %d GROUP BY user_id ORDER BY tmstp DESC;", $mail->id ) );

		if ( $tracks ) 
		{
			$out = '<div class="mp_scroll"><table>';
			foreach( $tracks as $track )
			{
				$tracking_url = esc_url( MP_::url( MailPress_tracking_u, array( 'id' => $track->user_id ) ) );
				$action = '<a href="' . $tracking_url . '" target="_blank" title="' . __( 'See tracking results', 'MailPress' ) . '">' . MP_User::get_email( $track->user_id ) . '</a>';
				$out .= '<tr><td><abbr title="' . $track->tmstp . '">' . substr( $track->tmstp, 0, 10 ) . '</abbr></td><td>&#160;' . $action . '</td></tr>';
			}
			$out .= '</table></div>';

			echo $out;
		}
	}
}
new MP_Tracking_metabox_m002all( __( 'All users', 'MailPress' ) );