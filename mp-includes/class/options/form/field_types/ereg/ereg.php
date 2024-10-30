<?php
class MP_Form_field_type_ereg extends MP_form_field_type_
{
	var $file		= __FILE__;

	var $id		= 'ereg';

	var $category 	= 'html';

	var $order		= 90;

	function submitted( $field )
	{
		$this->field = $field;

		$value	= $this->get_value();
		$value	= trim( $value );

		$required 	= ( isset( $this->field->settings['controls']['required'] ) && $this->field->settings['controls']['required'] );
		$empty 	= empty( $value );
		$ereg_ok 	= true;

		if ( $required )
		{
			if ( $empty )
			{
				$this->field->submitted['on_error'] = 1;
				return $this->field;
			}
		}

		$pattern 	= $this->field->settings['options']['pattern'];
		if ( !$empty && !empty( $pattern ) ) $ereg_ok = ( isset( $this->field->settings['options']['ereg'] ) ) ? @preg_match( $pattern, $value ) : @preg_match( $pattern, strtolower( $value ) );

		if ( !$ereg_ok )
		{
			$this->field->submitted['on_error'] = 2;
			return $this->field;
		}
		return parent::submitted( $this->field );
	}
}
new MP_Form_field_type_ereg( __( 'Preg_match Input ', 'MailPress' ) );