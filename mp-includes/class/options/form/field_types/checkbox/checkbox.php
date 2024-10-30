<?php
class MP_Form_field_type_checkbox extends MP_form_field_type_
{
	var $file		= __FILE__;

	var $id		= 'checkbox';

	var $category 	= 'html';

	var $order		= 30;

	function submitted( $field )
	{
		$this->field = $field;

		$value = $this->get_value();

		if ( !isset( $value ) )
		{
			$this->field->submitted['value'] = false;
			$this->field->submitted['text']  = __( 'not checked', 'MailPress' );
			return $this->field;
		}
		return parent::submitted( $this->field );
	}

	function attributes_filter( $no_reset )
	{
		if ( !$no_reset ) return;

		$value = $this->get_value();

		unset( $this->field->settings['attributes']['checked'] );
		if ( isset( $value ) ) $this->field->settings['attributes']['checked'] = 'checked';

		$this->attributes_filter_css();
	}
}
new MP_Form_field_type_checkbox( __( 'Checkbox', 'MailPress' ) );