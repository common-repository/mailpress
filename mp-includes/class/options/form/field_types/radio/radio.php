<?php
class MP_Form_field_type_radio extends MP_form_field_type_
{
	var $file 		= __FILE__;

	var $id 		= 'radio';

	var $category 	= 'html';

	var $order 	= 40;

	function submitted( $field )
	{
		$this->field = $field;

		$post_ = filter_input_array( INPUT_POST );
		$value = ( isset( $post_[$this->prefix][$this->field->form_id][$this->prefix . $this->field->settings['attributes']['name']] ) ) ? $post_[$this->prefix][$this->field->form_id][$this->prefix . $this->field->settings['attributes']['name']] : false;

		$required 	= ( isset( $this->field->settings['controls']['required'] ) && $this->field->settings['controls']['required'] );
		$empty 	= ( $value === false ) ? true : false;

		if ( $required && $empty )
		{
			$this->field->submitted['on_error'] = 1;
			return $this->field;
		}

		if ( $value === $this->field->settings['attributes']['value'] )
		{
			$this->field->submitted['value'] = $value;
			$this->field->submitted['text']  = sprintf( __( '"%1$s" checked', 'MailPress' ), $value );
			return $this->field;
		}

		return $this->field;
	}

	function attributes_filter( $no_reset )
	{
		if ( !$no_reset ) return;

		$post_ = filter_input_array( INPUT_POST );

		unset( $this->field->setting['attributes']['checked'] );
		if ( $post_[$this->prefix][$this->field->form_id][$this->prefix . $this->field->settings['attributes']['name']] == $this->field->settings['attributes']['value'] ) $this->field->settings['attributes']['checked'] = 'checked';

		$this->attributes_filter_css();
	}
}
new MP_Form_field_type_radio( __( 'Radio Button', 'MailPress' ) );