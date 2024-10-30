<?php
class MP_Form_field_type_form extends MP_form_field_type_
{
	var $file			= __FILE__;

	var $id			= 'form';
	var $field_not_input 	= true;

	var $category 		= 'html';
	var $order 		= 00;
}
new MP_Form_field_type_form( __( 'Form', 'MailPress' ) );