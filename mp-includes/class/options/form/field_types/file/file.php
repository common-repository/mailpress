<?php
class MP_Form_field_type_file extends MP_form_field_type_
{
	var $file 		= __FILE__;

	var $id		= 'file';

	var $category 	= 'html';

	var $order		= 60;

	function get_name( $field )
	{
		$this->field = $field;

		return $this->prefix . $this->field->form_id . '_' . $this->field->id;
	}

// have file loading ?
	function have_file ( $have_file )			
	{
		return true;
	}

	function submitted( $field )
	{
		$this->field = $field;

		$name		= $this->get_name( $this->field );

		$required 	= ( isset( $this->field->settings['controls']['required'] ) && $this->field->settings['controls']['required'] );
		$empty 	= ( !isset( $_FILES[$name] ) || empty( $_FILES[$name]['name'] ) );

		if ( $empty )
		{
			if ( $required )
			{
				$this->field->submitted['on_error'] = true;
				return $this->field;
			}
			$this->field->submitted['value'] = false;
			$this->field->submitted['text']  = __( 'no file', 'MailPress' );
			return $this->field;
		}
		$this->field->submitted['file'] = $name;

		$i = 0;
		$this->field->submitted['text']  = '';
		$attributes = array( 'name', 'type', 'tmp_name', 'error', 'size' );

		foreach( $attributes as $attribute ) if ( isset( $_FILES[$name][$attribute] ) ) $this->field->submitted['value'][$attribute] = $_FILES[$name][$attribute];
		foreach( $this->field->submitted['value'] as $attribute => $v )
		{
			$i++;
			if ( $i == 1 ) 	$this->field->submitted['text'] .= "$attribute : " . ( ( !empty( $v ) ) ? "$v " : '<small>[<i>' . __( 'empty', 'MailPress' ) . '</i>]</small>' ) . ( ( count( $this->field->submitted['value'] ) > 1 )   ? ', ' : '' );
			else			$this->field->submitted['text'] .= "$attribute : " . ( ( !empty( $v ) ) ? "$v " : '<small>[<i>' . __( 'empty', 'MailPress' ) . '</i>]</small>' ) . ( ( count( $this->field->submitted['value'] ) != $i ) ? ', ' : '' );
		}
		return $this->field;
	}

	function attributes_filter( $no_reset )
	{
		if ( !$no_reset ) return;

		$this->attributes_filter_css();
	}
}
new MP_Form_field_type_file( __( 'File select', 'MailPress' ) );