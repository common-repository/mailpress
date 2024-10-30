<?php
class MP_Tracking_metabox_m007 extends MP_tracking_metabox_
{
	var $id	= 'm007';
	var $context= 'normal';
	var $file 	= __FILE__;

	function meta_box( $mail )
	{
		global $wpdb;
		$tracks = $wpdb->get_results( $wpdb->prepare( "SELECT DATE( tmstp ) as tmstp, track, count( * ) as count FROM $wpdb->mp_tracks WHERE mail_id = %d GROUP BY 1, 2 ORDER BY 1 DESC, 2 DESC ;", $mail->id ) );
		if ( $tracks )
		{
			$x = array();
			foreach( $tracks as $track )
			{
				$time = $track->tmstp;
				if ( MailPress_tracking_openedmail == $track->track )
				{
					if ( isset( $x[$track->tmstp]['o'] ) ) 	$x[$track->tmstp]['o'] += $track->count;
					else						$x[$track->tmstp]['o']  = $track->count;
				}
				else
				{
					if ( isset( $x[$track->tmstp]['c'] ) ) 	$x[$track->tmstp]['c'] += $track->count;
					else						$x[$track->tmstp]['c']  = $track->count;
				}
			}

			$out = '<table id="tracking_mp_m007">	<thead><tr><th></th><th>' . __( 'Opened', 'MailPress' ) . '</th><th>' . __( 'Clicks', 'MailPress' ) . '</th></tr></thead><tbody>';

			foreach( $x as $k => $v )
			{
				$out .= '<tr><td>' . $k . '</td>';
				$out .= ( isset( $v['o'] ) ) ? '<td class="number">' . $v['o'] . '</td>' : '<td class="number"></td>';
				$out .= ( isset( $v['c'] ) ) ? '<td class="number">' . $v['c'] . '</td>' : '<td class="number"></td>';
				$out .= '</tr>';
			}

			$out .= '</tbody></table>';

			echo $out;
		}
	}
}
new MP_Tracking_metabox_m007( __( 'Opened, Clicks/day', 'MailPress' ) );