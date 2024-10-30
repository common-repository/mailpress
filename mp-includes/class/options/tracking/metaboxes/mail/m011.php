<?php
class MP_Tracking_metabox_m011 extends MP_tracking_metabox_
{
	var $id	= 'm011';
	var $context= 'normal';
	var $file 	= __FILE__;

	function __construct( $title )
	{
		if ( !class_exists( 'MP_Tracking_recipients', false ) ) new MP_Tracking_recipients();
		parent::__construct( $title );
	}

	function meta_box( $mail )
	{
		global $wpdb;

		if ( MailPress::is_email( $mail->toemail ) ) $m[$mail->toemail] = array( '{{_user_id}}' => MP_User::get_id_by_email( $mail->toemail ) );
		else $m = unserialize( $mail->toemail );
		unset( $m['MP_Mail'] );
		$total = count( $m );

		foreach( $m as $email => $v )
		{
			$ug = apply_filters( 'MailPress_tracking_recipients_domain_get', $email );
			$key = $ug->name;
			if ( isset( $x[$key]['count'] ) ) 	$x[$key]['count']++;
			else 						$x[$key]['count'] = 1;
			if ( isset( $ug->icon_path ) && !isset( $x[$key]['img'] ) )
			{
				$x[$key]['img'] = $ug->icon_path;
				$x[$key]['class'] = $ug->classes;
			}

			$opened = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT user_id FROM $wpdb->mp_tracks WHERE mail_id = %d AND user_id = %d AND track = %s ;", $mail->id, $v['{{_user_id}}'], MailPress_tracking_openedmail ) );

			if ( $opened )
			{
				if ( isset( $x[$key]['opened'] ) ) 	$x[$key]['opened']++;
				else 						$x[$key]['opened'] = 1;
			}
		}

		if ( isset( $x[''] ) ) { $w = $x['']; unset( $x[''] ); } else unset( $w );
		uasort( $x, array( 'self', 'sort_domains' ) );
		if ( isset( $w ) ) $x[''] = $w;

		$out  = '<table id ="tracking_mp_011">';
		$out .= '<tr><th>' . __( 'domain', 'MailPress' ) . '</th><th class="num">' . __( 'count', 'MailPress' ) . '</th><th class="num">' . __( '%', 'MailPress' ) . '</th><th class="num">' . __( 'open rate', 'MailPress' ) . '</th></tr>';
		foreach( $x as $k => $v )
		{
			$k = ( empty( $k ) ) ? __( 'others', 'MailPress' ) : $k;
			$out .= '<tr>';
//			$out .= ( isset( $v['img'] ) ) ? '<td><img class="' . $v['class']  . '" src="' . $v['img'] . '" alt="" /> ' . $k . ' </td>' : "<td> $k </td>";
			$out .= ( isset( $v['img'] ) ) ? '<td><div class="' . $v['class']  . '"></div> ' . $k . ' </td>' : "<td> $k </td>";
			$out .= '<td class="num">' . $v['count'] . '</td>';
			$out .= '<td class="num">' . sprintf( "%01.2f %%",100 * $v['count']/$total ) . '</td>';
			$out .= ( isset( $v['opened'] ) ) ? '<td class="num">' . sprintf( "%01.2f %%",100 * $v['opened']/$v['count'] ) . '</td>' : '<td></td>';
			$out .= '</tr>';
		}
		$out .= '</table>';

		echo $out;
	}

	public static function sort_domains( $a, $b ) 
	{
		return $b['count'] - $a['count'];
	}
}
new MP_Tracking_metabox_m011( __( 'Domain recipients', 'MailPress' ) );