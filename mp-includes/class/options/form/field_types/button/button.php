<?php
class MP_Form_field_type_button extends MP_form_field_type_
{
	var $file	= __FILE__;

	var $id 	= 'button';

	var $category = 'html';
	var $order	= 200;

	function submitted( $field )
	{
		$this->field = $field;

		$value = $this->get_value();

		if ( !isset( $value ) )
		{
			$this->field->submitted['value'] = false;
			$this->field->submitted['text']  = __( 'not selected', 'MailPress' );
			return $this->field;
		}
		return parent::submitted( $this->field );
	}
}
new MP_Form_field_type_button( __( 'Button', 'MailPress' ) );