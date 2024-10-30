<?php
abstract class MP_theme_html_template_
{
	public static function who_is( $ip )
	{
		$x = ( $ip ) ? MP_Ip::get_latlng( $ip ) : false;

		if ( !$x ) return array( 'src' => false, 'addr' => false );

		return array( 'src' => MP_Map::get_staticmap( $ip ), 'addr' => MP_Map::get_address( $x['lng'], $x['lat'] ) );
	}
}