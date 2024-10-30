<?php
class MP_Tracking_metabox_m003 extends MP_tracking_metabox_
{
	var $id 	= 'm003';
	var $context= 'normal';
	var $file 	= __FILE__;

	function meta_box( $mail )
	{
		global $wpdb;
		$tracks = $wpdb->get_results( $wpdb->prepare( "SELECT DATE( tmstp ) as tmstp, count( * ) as count FROM $wpdb->mp_tracks WHERE mail_id = %d AND track = %s GROUP BY 1 ORDER BY 1 DESC ;", $mail->id, MailPress_tracking_openedmail ) );
		if ( $tracks )
		{
			$out = '';
			foreach( $tracks as $track )
			{
				$out .= $track->tmstp . ' <b>' . $track->count . '</b><br />';
			}

			echo $out;
		}
	}
}
new MP_Tracking_metabox_m003( __( 'Opened/day', 'MailPress' ) );