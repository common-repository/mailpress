<?php // test  => one sanitize here, selects and checkboxes + email is checked with email validator (RFC822,2822,5321,5322,6530,6531,6532) those RFC are not supported by Wp

if ( isset( MP_AdminPage::$pst_['test'] ) )
{
	$test 	= stripslashes_deep( MP_AdminPage::$pst_['test'] );

	$test['toname'] = sanitize_text_field( $test['toname'] );

	$test['template'] = $test['th'][$test['theme']]['tm'];
	unset( $test['th'] );

	switch ( true )
	{
		case ( !MailPress::is_email( $test['toemail'] ) ) :
			MP_AdminPage::$err_mess['toemail'] = __( 'field should be an email', 'MailPress' );
		break;
		case ( empty( $test['toname'] ) ) :
			MP_AdminPage::$err_mess['toname']  = __( 'field should be a name', 'MailPress' );
		break;
		default :
			update_option( MailPress::option_name_test, $test );
			if ( isset( MP_AdminPage::$pst_['Submit'] ) )
			{
				$message = __( 'Test settings saved', 'MailPress' );
			}
			else
			{
				$url   = home_url();
				$title = get_bloginfo( 'name' );
	
				$mail = new stdClass();
				$mail->Theme = $test['theme'];
				if ( '0' != $test['template'] ) $mail->Template = $test['template'];

				$mail->id		= MP_Mail::get_id( 'settings test' );

			// Set the from name and email
				$mail->fromemail 	= $mp_general['fromemail'];
				$mail->fromname	= $mp_general['fromname'];

			// Set destination address
				$mail->toemail 	= $test['toemail'];
				$mail->toname	= MP_Mail::display_name( $test['toname'] );
				$key = MP_User::get_key_by_email( $mail->toemail );
				if ( $key )
				{
					$mail->viewhtml	 = MP_User::get_view_url( $key, $mail->id );
					$mail->unsubscribe = MP_User::get_unsubscribe_url( $key );
					$mail->subscribe 	 = MP_User::get_subscribe_url( $key );
				}

			// Set mail's subject and body
				$mail->subject	= sprintf( __( 'Connection test : %1$s - Template : %2$s', 'MailPress' ), get_bloginfo( 'name' ), isset( $mail->Template ) ? $mail->Template : __( 'none', 'MailPress' ) );

				$mail->plaintext   =  "\n\n" . __( 'This is a test message of MailPress from', 'MailPress' ) . ' ' . $url . "\n\n";

				$message  = '<div><br /><br />';
				$message .=  sprintf( __( 'This is a <blink>test</blink> message of %1$s from %2$s. <br /><br />', 'MailPress' ), ' <b>MailPress</b> ', '<a href="' .  $url . '">' . $title . '</a>' );
				$message .= '<br /><br /></div>';
				$mail->html       = $message;

				if ( class_exists( 'MailPress_newsletter' ) )
				{
					if ( isset( $mail->Template ) && in_array( $mail->Template, MP_Newsletter::get_templates() ) )
					{
						$posts = $wpdb->get_results( "SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY RAND() LIMIT 1;" );
						if ( $posts )
						{
							$mail->the_title = apply_filters( 'the_title', $posts[0]->post_title );
							$mail->newsletter= true;
							query_posts( 'p='. $posts[0]->ID );
						}
					}
				}

				if (  isset( $test['forcelog'] ) )	$mail->forcelog = '';
				if ( !isset( $test['fakeit'] ) )	$mail->nomail = '';
				if ( !isset( $test['archive'] ) )	$mail->noarchive = '';
				if ( !isset( $test['stats'] ) )		$mail->nostats = '';

				if ( MailPress::mail( $mail ) )
				{
					if ( !isset( $test['fakeit'] ) )
					{
						$message = __( 'Test settings saved, Mail not send as required', 'MailPress' );
					}
					else
					{
						$message = __( 'Test successful, CONGRATULATIONS !', 'MailPress' );
					}
				}
				else
				{
					MP_AdminPage::$err_mess['test'] = __( 'FAILED. Check your logs & settings !', 'MailPress' );
				}
			}
		break;
	}
}