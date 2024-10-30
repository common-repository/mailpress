<?php
class MP_Form_field_type_geotag extends MP_form_field_type_
{
	var $file		= __FILE__;

	var $id		= 'geotag';

	var $category 	= 'composite';

	var $order		= 100;

	function get_name( $field )
	{
		$this->field = $field;
		return ( isset( $this->field->settings['options']['is'] ) ) ? $this->prefix . '[' . $this->field->form_id . ']['. $this->field->id . '][' . $this->field->settings['options']['is'] . ']' : $this->prefix . '[' . $this->field->form_id . ']['. $this->field->id . ']';
	}

	function get_id( $field )
	{
		$this->field = $field;
		return ( isset( $this->field->settings['options']['is'] ) ) ? $this->prefix .       $this->field->form_id .  '_'. $this->field->id .  '_' . $this->field->settings['options']['is']       : $this->prefix .       $this->field->form_id .  '_'. $this->field->id;
	}

	function submitted( $field )
	{
		$this->field = $field;

		$options = $this->field->settings['googlemap'];

		$value = $this->get_value();

		$required 	= ( isset( $this->field->settings['controls']['required'] ) && $this->field->settings['controls']['required'] );
		$empty 	= ( empty( $value['lat'] ) || empty( $value['lng'] ) );
		$geotag_ok 	= true;

		if ( $required && $empty )
		{
			$this->field->submitted['on_error'] = 1;
			return $this->field;
		}

		if ( isset( $options['lat_lng'] ) && !isset( $options['lat_lng_disabled'] ) ) if ( ( $value['lat'] != ( string )( float )$value['lat'] ) || ( $value['lng'] != ( string )( float )$value['lng'] ) ) $geotag_ok = false;

		if ( !$geotag_ok )
		{
			$this->field->submitted['on_error'] = 2;
			return $this->field;
		}

		$width  = ( float ) $this->field->settings['googlemap']['width'];
		$height = ( float ) $this->field->settings['googlemap']['height'];
		if ( $width  > 640 ) $width  = 640;
		if ( $height > 640 ) $height = 640;

		$args = array ( 	'lng'			=> $value['lng'],
						'lat'			=> $value['lat'],
					 	'center_lng'	=> $value['center_lng'],
						'center_lat'	=> $value['center_lat'], 
						'zoom'		=> $value['zoomlevel'], 
   						'size'		=> $width . 'x' . $height, 
						'maptype'		=> $value['maptype'], 
		);
		$value['reverse_geocoding'] = MP_Map::get_address( $value['lng'], $value['lat'] );

		$this->field->submitted['value'] = $value;
		$this->field->submitted['text']  = '';
		$this->field->submitted['text']  = 'lat : ' . $value['lat'] . ' lng : ' . $value['lng'] . "<br />\n\r";
		$this->field->submitted['text'] .= __( 'Reverse geocoding :', 'MailPress' ) . ' ' . ( ( !empty( $value['reverse_geocoding'] ) ) ? $value['reverse_geocoding'] : '<small>[<i>' . __( 'empty', 'MailPress' ) . '</i>]</small>' ) . "<br />\n\r";
		if ( isset( $value['geocode'] ) ) $this->field->submitted['text'] .= ( ( !empty( $value['geocode'] ) ) ? 'geocode : ' . $value['geocode'] : '<small>[<i>' . __( 'empty', 'MailPress' ) . '</i>]</small>' ) . "<br />\n\r";

		$this->field->submitted['map']   =  MP_Map::get_staticmap( false, $args );

		return $this->field;
	}

	function attributes_filter( $no_reset )
	{
		$xlatlng = MP_Ip::get_current_latlng();

		$options = $this->field->settings['googlemap'];

		$options['lat'] = $options['center_lat'] = ( $xlatlng ) ? ( float ) trim( $xlatlng['lat'] ) : ( float ) trim( $options['lat'] );
		$options['lng'] = $options['center_lng'] = ( $xlatlng ) ? ( float ) trim( $xlatlng['lng'] ) : ( float ) trim( $options['lng'] );

		if ( isset( $options['geocode'] ) ) $options['geocode_value'] = MP_Map::get_address( $options['lng'], $options['lat'] );
		$this->field->settings['googlemap'] = $options;

		if ( !$no_reset ) return;

		$post_ = $this->get_value();
		$options['lat'] 		= ( float ) $post_['lat'];
		$options['lng'] 		= ( float ) $post_['lng'];
		$options['center_lat'] 	= ( float ) $post_['center_lat'];
		$options['center_lng'] 	= ( float ) $post_['center_lng'];
		$options['zoomlevel'] 	= $post_['zoomlevel'];
		$options['maptype'] 		= $post_['maptype'];

		if ( isset( $options['geocode'] ) ) $options['geocode_value'] =  esc_attr( $post_['geocode'] );
		
		$this->field->settings['googlemap'] = $options;

		$this->attributes_filter_css();
	}

	function build_tag()
	{
		$options = $this->field->settings['googlemap'];
	//map
		$this->field->settings['options']['is'] = 'map';
		$id_map	= $this->get_id( $this->field );
		$style	= ' style="overflow:hidden;width:' . trim( $options['width'] ) . ';height:' . trim( $options['height'] ) . ';"';
		$tag_map	= '<div id="' . $id_map . '"' . $style . '></div>';
		$tag_map  .= "\r\n";
		//zoomlevel
			$this->field->settings['attributes']['type'] = 'hidden';

			$this->field->settings['attributes']['value'] = $options['zoomlevel'];
			$this->field->settings['options']['is'] = 'zoomlevel';
			$tag_map .= parent::build_tag();
		//maptype
			$this->field->settings['attributes']['value'] = $options['maptype'];
			$this->field->settings['options']['is'] = 'maptype';
			$tag_map .= parent::build_tag();
		//center_lat
			$this->field->settings['attributes']['value'] = $options['center_lat'];
			$this->field->settings['options']['is'] = 'center_lat';
			$tag_map .= parent::build_tag();
		//center_lng
			$this->field->settings['attributes']['value'] = $options['center_lng'];
			$this->field->settings['options']['is'] = 'center_lng';
			$tag_map .= parent::build_tag();

	// lat, lng
		$tag_lat = $tag_lng = $id_lat_d = $id_lng_d = '';
		$this->field->type = 'text';
		if ( isset( $options['lat_lng'] ) )
		{
			if ( !isset( $options['lat_lng_disabled'] ) )
			{
				// lat lng text
				$this->field->settings['attributes']['type'] = 'text';
				$this->field->settings['attributes']['size']  = $options['lat_lng_size'];
				$this->field->settings['attributes']['value'] = $options['lat'];

				$this->field->settings['options']['is'] = 'lat';
				$id_lat	= $this->get_id( $this->field );
				$tag_lat	= parent::build_tag();

				$this->field->settings['attributes']['value'] = $options['lng'];

				$this->field->settings['options']['is'] = 'lng';
				$id_lng	= $this->get_id( $this->field );
				$tag_lng	= parent::build_tag();
			}
			else
			{
				// lat lng text 			id et name differents
				$this->field->settings['attributes']['type'] = 'text';
				$this->field->settings['attributes']['size']  = $options['lat_lng_size'];
				$this->field->settings['attributes']['value'] = $options['lat'];
				$this->field->settings['attributes']['disabled'] = 'disabled';

				$this->field->settings['options']['is'] = 'lat_d';
				$id_lat_d	= $this->get_id( $this->field );
				$tag_lat	= parent::build_tag();

				$this->field->settings['attributes']['value'] = $options['lng'];

				$this->field->settings['options']['is'] = 'lng_d';
				$id_lng_d	= $this->get_id( $this->field );
				$tag_lng	= parent::build_tag();

				// lat lng hidden
				unset( $this->field->settings['attributes']['disabled'], $this->field->settings['attributes']['size'] );

				$this->field->settings['attributes']['type'] = 'hidden';
				$this->field->settings['attributes']['value'] = $options['lat'];

				$this->field->settings['options']['is'] = 'lat';
				$id_lat	= $this->get_id( $this->field );
				$tag_map .= parent::build_tag();

				$this->field->settings['attributes']['value'] = $options['lng'];

				$this->field->settings['options']['is'] = 'lng';
				$id_lng	= $this->get_id( $this->field );
				$tag_map .= parent::build_tag();
			}
		}
		else
		{
			// lat lng hidden
			unset( $this->field->settings['attributes']['disabled'], $this->field->settings['attributes']['size'] );

			$this->field->settings['attributes']['type'] = 'hidden';
			$this->field->settings['attributes']['value'] = $options['lat'];

			$this->field->settings['options']['is'] = 'lat';
			$id_lat	= $this->get_id( $this->field );
			$tag_map .= parent::build_tag();


			$this->field->settings['attributes']['value'] = $options['lng'];

			$this->field->settings['options']['is'] = 'lng';
			$id_lng	= $this->get_id( $this->field );
			$tag_map .= parent::build_tag();
		}

	// geocode
		$id_geocode = $tag_geocode = $id_geocode_button	= $tag_geocode_button = '';
		if ( isset( $options['geocode'] ) ) 
		{
		// input text
			unset( $this->field->settings['attributes']['disabled'] );

			$this->field->settings['attributes']['type']  = 'text';
			$this->field->settings['attributes']['size']  = $options['geocode_size'];
			$this->field->settings['attributes']['value'] = ( isset( $options['geocode_value'] ) ) ? $options['geocode_value'] : '';

			$this->field->settings['options']['is'] = 'geocode';
			$id_geocode		= $this->get_id( $this->field );
			$tag_geocode	= parent::build_tag();

		// button
			$this->field->type = 'button';
			unset( $this->field->settings['attributes']['size'] );

			$this->field->settings['attributes']['type'] = 'button';
			$this->field->settings['attributes']['value'] = $options['geocode_button'];

			$this->field->settings['options']['is'] = 'geocode_button';
			$id_geocode_button	= $this->get_id( $this->field );
			$tag_geocode_button	= parent::build_tag();
		}

	// css & javascript
                $scripts = array();
		if ( !defined( 'MP_FORM_GEOTAG' ) )
		{
			global $mp_general;
			define ( 'MP_FORM_GEOTAG', true );
			$scripts = MP_Map::form_geotag( $options );
		}

		$x = array();
		$x['form'] = $this->field->form_id; $x['field'] = $this->field->id;
		foreach ( array( 'lat', 'lng', 'center_lat', 'center_lng', 'maptype', 'zoomlevel', 'zoom', 'changemap', 'center', 'lat_lng', 'lat_lng_disabled', 'rgeocode' ) as $opt ) $x[$opt] = ( isset( $options[$opt] ) ) ? $options[$opt] : '0';
		$m = array( 'mp_field_type_geotag_' . $this->field->form_id .  '_'. $this->field->id => $x );
		$scripts['f'][] = '<script type="text/javascript">' . "\n" . '/* <![CDATA[ */';
		foreach ( $m as $var => $val ) $scripts['f'][] = "var $var = " . MP_::print_scripts_l10n_val( $val, true );
		$scripts['f'][] = ";\njQuery( document ).ready( function() { var mp_form_" . $this->field->form_id . '_' . $this->field->id  . " = new mp_field_type_geotag( mp_field_type_geotag_" . $this->field->form_id .  '_'. $this->field->id . " ); } );\n/* ]]> */\n</script>";

		foreach( array( 'h' => 'header', 'f' => 'footer' ) as $k => $v )
		{
			if ( isset( $scripts[$k] ) )
			{
				array_unshift( $scripts[$k], "<!-- start $v -->" );
				array_push(    $scripts[$k], "<!-- end $v -->" );
			}
			else
			{
				$scripts[$k][] = "<!-- no $v -->";
			}
			$$v = "\r\n" . implode( "\r\n", $scripts[$k] ) . "\r\n";
		}

	//end
		$this->field->type = $this->id;

		$sf  = '';
		$sf  = ( isset( $options['lat_lng'] ) ) ? 'latlng' : '';
		$sf .= ( isset( $options['geocode'] ) ) ? ( ( empty( $sf ) ) ? 'geocode' : '_geocode' ) : '';
		if ( empty( $sf ) ) $sf = 'alone';

		$form_formats['alone']		=  '{{map}}';
		$form_formats['latlng']		=  '{{map}}lat:{{lat}}&#160;lng:{{lng}}';
		$form_formats['geocode']		=  '{{map}}{{geocode}}&#160;{{geocode_button}}';
		$form_formats['latlng_geocode']	=  '{{map}}lat:{{lat}}&#160;lng:{{lng}}<br />{{geocode}}&#160;{{geocode_button}}';

		$form_formats = $this->get_formats( $form_formats );

		$search[] = '{{map}}';			$replace[] = '%1$s';
		$search[] = '{{id_map}}';			$replace[] = '%2$s';
		$search[] = '{{lat}}'; 			$replace[] = '%3$s';
		$search[] = '{{id_lat}}'; 			$replace[] = '%4$s';
		$search[] = '{{lng}}'; 			$replace[] = '%5$s';
		$search[] = '{{id_lng}}';			$replace[] = '%6$s';
		$search[] = '{{geocode}}';			$replace[] = '%7$s';
		$search[] = '{{id_geocode}}';		$replace[] = '%8$s';
		$search[] = '{{geocode_button}}';	$replace[] = '%9$s';
		$search[] = '{{id_geocode_button}}';	$replace[] = '%10$s';
   		$search[] = '{{id_lat_dis}}';		$replace[] = '%11$s';
		$search[] = '{{id_lng_dis}}';		$replace[] = '%12$s';

		$html = str_replace( $search, $replace, $form_formats[$sf] );
		return $header . sprintf( $html, $tag_map, $id_map, $tag_lat, $id_lat, $tag_lng, $id_lng, $tag_geocode, $id_geocode, $tag_geocode_button, $id_geocode_button, $id_lat_d, $id_lng_d ) . $footer;
	}
}
new MP_Form_field_type_geotag( __( 'Geotag', 'MailPress' ) );