<?php
if ( !extension_loaded( 'gd' ) ) return;

class MP_Form_field_type_captcha_gd2 extends MP_form_field_type_
{
	var $file 		= __FILE__;

	var $id 		= 'captcha_gd2';

	var $category 	= 'composite';
	var $order		= 121;

	function submitted( $field )
	{
		$this->field = $field;

		$value = $this->get_value();
		$value = trim( $value );

		$required 	= true;
		$empty = empty( $value );

		if ( $empty )
		{
			$this->field->submitted['on_error'] = 1;
			return $this->field;
		}
		else
		{
			@session_start();

			if ( ( !$_SESSION['mp_googlelike'] ) || ( strtolower( $_SESSION['mp_googlelike'] ) != strtolower( $value ) ) )
			{
				$this->field->submitted['on_error'] = 1;
				return $this->field;
			}
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
		$id_input 	= $this->get_id( $this->field );
		$tag_input 	= parent::build_tag();

		$id_img 	= $id_input . '_img';
		$args		= array( 'id' => $this->field->id, 'action' => 'mp_ajax', 'mp_action' => '2ahctpac' );
		$tag_img 	= '<img id="' . $id_img . '" src="' . esc_url( add_query_arg( $args, admin_url( 'admin-ajax.php' ) ) ) . '" alt="" />';

		$this->field->type = $this->id;

		$form_format =  '{{img}}<br />{{input}}';

		$form_format = $this->get_formats( $form_format );

		$search[] = '{{img}}';		$replace[] = '%1$s';
		$search[] = '{{id_img}}'; 	$replace[] = '%2$s';
		$search[] = '{{input}}'; 	$replace[] = '%3$s';
		$search[] = '{{id_input}}';	$replace[] = '%4$s';

		$html = str_replace( $search, $replace,  $form_format );

		return sprintf( $html, $tag_img, $id_img, $tag_input, $id_input );
	}
}
new MP_Form_field_type_captcha_gd2( __( 'Captcha 2', 'MailPress' ) );