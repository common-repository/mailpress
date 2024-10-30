<?php
class MP_Form_field_type_email extends MP_form_field_type_
{
	var $file		= __FILE__;

	var $id		= 'email';

	var $category 	= 'html';

	var $order		= 80;

	function submitted( $field )
	{
		$this->field = $field;

		$value	= $this->get_value();
		$value	= trim( $value );

		$required 	= ( isset( $this->field->settings['controls']['required'] ) && $this->field->settings['controls']['required'] );
		$empty 	= empty( $value );
		$is_email 	= ( MailPress::is_email( $value ) );
		if ( $required )
		{
			if ( $empty )
			{
				$this->field->submitted['on_error'] = 1;
				return $this->field;
			}
			if ( !$is_email )
			{
				$this->field->submitted['on_error'] = 2;
				return $this->field;
			}
		}
		if ( !$empty && !$is_email )
		{
			$this->field->submitted['on_error'] = 3;
			return $this->field;
		}
		return parent::submitted( $this->field );
	}

	function attributes_filter( $no_reset )
	{
		$visitor_email = ( isset( $this->field->settings['options']['visitor_email'] ) && $this->field->settings['options']['visitor_email'] );
		if ( $visitor_email )
		{
			global $user_ID; switch ( true ) { case ( $user_ID != 0 && is_numeric( $user_ID ) ) : $user  = get_userdata( $user_ID ); $email = $user->user_email; break; default : $email = ( isset( $_COOKIE['comment_author_email_' . COOKIEHASH] ) ) ? $_COOKIE['comment_author_email_' . COOKIEHASH] : ''; break; }
			if ( !empty( $email ) ) $this->field->settings['attributes']['value'] = $email;
		}

		if ( !$no_reset ) return;

		parent::attributes_filter( $no_reset );
		$this->attributes_filter_css();
	}
}
new MP_Form_field_type_email( __( 'Email', 'MailPress' ) );