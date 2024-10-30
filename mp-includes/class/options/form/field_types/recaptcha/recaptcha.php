<?php

class MP_Form_field_type_recaptcha extends MP_form_field_type_
{
	var $file	= __FILE__;

	var $id 	= 'recaptcha';

	var $category = 'composite';
	var $order	= 110;

	function submitted( $field )
	{
		$this->field = $field;

		require_once( 'captcha/recaptchalib.php' );

		$post_ = filter_input_array( INPUT_POST );

		$resp = recaptcha_check_answer ( $this->field->settings['keys']['privatekey'], filter_input( INPUT_SERVER, 'REMOTE_ADDR' ), $post_["recaptcha_challenge_field"], $post_["recaptcha_response_field"] );

		if ( !$resp->is_valid ) 
		{
			// set the error code so that we can display it
			// $error = $resp->error;
			$this->field->submitted['on_error'] = $resp->error;
			return $this->field;
		}

		$this->field->submitted['value'] = 1;
		$this->field->submitted['text']  = __( 'ok', 'MailPress' );

		return $this->field;
	}

	function attributes_filter( $no_reset )
	{
		if ( !$no_reset ) return;
		
		$this->attributes_filter_css();
	}

	function build_tag()
	{
		require_once( 'captcha/recaptchalib.php' );

		$tag = recaptcha_get_html( $this->field->settings['keys']['publickey'], ( isset( $this->field->submitted['on_error'] ) ) ? $this->field->submitted['on_error'] : null );
		$id  = $this->get_id( $this->field );

		$form_format =  '{{img}}';

		$form_formats = $this->get_formats( $form_formats );

		$search[] = '{{img}}';		$replace[] = '%1$s';
		$search[] = '{{id}}'; 		$replace[] = '%2$s';

		$html = str_replace( $search, $replace,  $form_format );

		return sprintf( $html, $tag, $id );
	}
}
new MP_Form_field_type_recaptcha( __( 'ReCaptcha', 'MailPress' ) );