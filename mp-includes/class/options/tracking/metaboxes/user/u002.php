<?php
class MP_Tracking_metabox_u002 extends MP_tracking_metabox_
{
	var $id	= 'u002';
	var $context= 'side';
	var $file 	= __FILE__;

	function __construct( $title )
	{
		add_filter( 'MailPress_scripts', array( $this, 'scripts' ), 8, 2 );
		add_filter( 'MailPress_styles',  array( $this, 'styles' ),  8, 2 );
		parent::__construct( $title );
	}

	function styles( $styles ) 
	{
		$styles[] = 'thickbox';
		return $styles;
	}

	function scripts( $scripts )
	{
		wp_register_script( 'mp-thickbox', 		'/' . MP_PATH . 'mp-includes/js/mp_thickbox.js', array( 'thickbox' ), false, 1 );
		$scripts[] = 'mp-thickbox';
		return $scripts;
	}

	function meta_box( $mp_user )
	{
		global $wpdb;
		$x = new MP_Mail();

		$tracks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->mp_usermeta WHERE mp_user_id = %d AND meta_key = %s ORDER BY meta_id DESC LIMIT 10;", $mp_user->id, '_MailPress_mail_sent' ) );
		if ( $tracks )
		{
			$out = '<table>';
			foreach( $tracks as $track )
			{
				$mail = $wpdb->get_results( $wpdb->prepare( "SELECT subject, created, sent FROM $wpdb->mp_mails WHERE id = %d ;", $track->meta_value ) );
				foreach( $mail as $mail ) 
				{
					$subject = $mail->subject;
					$date = ( '0000-00-00 00:00:00' == $mail->sent ) ? $mail->created : $mail->sent;
				}

				if ( isset( $subject ) )
				{
					$subject 	= $x->viewsubject( $subject, $track->meta_value, $track->meta_value, $mp_user->id );

					$args = array( 'id' => $track->meta_value, 'mp_user_id' => $mp_user->id, 'key' => $mp_user->confkey , 'action' => 'mp_ajax', 'mp_action' => 'iview', 'TB_iframe' => 'true' );
					$view_url = esc_url( add_query_arg( $args, admin_url( 'admin-ajax.php' ) ) );    

					$track->meta_value = '<a href="' . $view_url . '" class="thickbox thickbox-preview" title="' . esc_attr( sprintf( __( 'View &#8220;%1$s&#8221;', 'MailPress' ) , $subject ) ) . '">' . $track->meta_value . '</a>';
				}
				else
				{
					$date = '';
					$subject = __( '(deleted)', 'MailPress' );
				}
				$out .= '<tr><td><abbr title="' . $date . '">' . substr( $date, 0, 10 ) . '</abbr></td><td class="tracking_tac">' . $track->meta_value . '</td><td>' . ( ( strlen( $subject ) > 45 ) ? substr( $subject, 0, 45 ) . '...' : $subject ) . '</td></tr>';
			}
			$out .= '</table>';

			echo $out;
		}
	}
}
new MP_Tracking_metabox_u002( __( 'Last 10 mails',  'MailPress' ) );