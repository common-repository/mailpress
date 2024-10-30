<?php
class MP_Form_field_type_textarea extends MP_form_field_type_
{
	var $file			= __FILE__;

	var $id			= 'textarea';
	var $field_not_input 	= true;

	var $category 		= 'html';
	var $order 		= 20;

	function submitted( $field )
	{
		$this->field = $field;

		$value	= sanitize_textarea_field( $this->get_value() );
		$value	= trim( $value );

		$required 	= ( isset( $this->field->settings['controls']['required'] ) && $this->field->settings['controls']['required'] );
		$empty 	= empty( $value );

		if ( $required && $empty )
		{
			$this->field->submitted['on_error'] = 1;
			return $this->field;
		}
		$this->field->submitted['value'] = $value;
		$this->field->submitted['text']  = apply_filters( 'MailPress_the_content', stripslashes( $value ) );
		return $this->field;
	}

	function attributes_filter( $no_reset )
	{
		$this->field->settings['attributes']['tag_content'] = base64_decode( $this->field->settings['attributes']['tag_content'] );

		if ( !$no_reset ) return;

		$value = stripslashes( $this->get_value() );
		$this->field->settings['attributes']['tag_content'] = trim( $value );
		$this->attributes_filter_css();
	}
}
new MP_Form_field_type_textarea( __( 'Multi-line Input', 'MailPress' ) );