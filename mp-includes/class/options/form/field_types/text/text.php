<?php
class MP_Form_field_type_text extends MP_form_field_type_
{
	var $file	= __FILE__;

	var $id	= 'text';

	var $category = 'html';
	var $order	= 10;

	function submitted( $field )
	{
		$this->field = $field;

		$value	= sanitize_text_field( $this->get_value() );
		$value	= trim( $value );

		$required 	= ( isset( $this->field->settings['controls']['required'] ) && $this->field->settings['controls']['required'] );
		$numeric 	= ( isset( $this->field->settings['controls']['numeric'] )  && $this->field->settings['controls']['numeric'] );
		$empty 	= empty( $value );
		$is_numeric	= ( $value == ( string )( float )$value );

		if ( $required )
		{
			if ( $empty )
			{
				$this->field->submitted['on_error'] = 1;
				return $this->field;
			}
			if ( $numeric && !$is_numeric )
			{
				$this->field->submitted['on_error'] = 2;
				return $this->field;
			}
		}
		if ( !$empty && $numeric && !$is_numeric )
		{
			$this->field->submitted['on_error'] = 3;
			return $this->field;
		}
		return parent::submitted( $this->field );
	}

	function attributes_filter( $no_reset )
	{
		$visitor_name = ( isset( $this->field->settings['options']['visitor_name'] ) && $this->field->settings['options']['visitor_name'] );
		if ( $visitor_name )
		{
			global $user_ID; switch ( true ) { case ( $user_ID != 0 && is_numeric( $user_ID ) ) : $user  = get_userdata( $user_ID ); $name  = $user->display_name; break; default : $name   = ( isset( $_COOKIE['comment_author_' . COOKIEHASH] ) ) ? $_COOKIE['comment_author_' . COOKIEHASH] : ''; break; }
			if ( !empty( $name ) ) $this->field->settings['attributes']['value'] = $name;
		}

		if ( !$no_reset ) return;

		parent::attributes_filter( $no_reset );
		$this->attributes_filter_css();
	}
}
new MP_Form_field_type_text( __( 'Text Input', 'MailPress' ) );