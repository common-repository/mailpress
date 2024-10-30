<?php
if ( defined('MP_Ip_ipstack_access_key') )
{
class MP_Ip_ipstack extends MP_ip_provider_
{
	var $id 	= 'ipstack';
	var $url	= 'http://api.ipstack.com/%1$s?access_key=%2$s';
	var $credit= 'https://ipstack.com/';
	var $type 	= 'json';

	function content( $valid, $content )
	{
		if ( strpos( $content, '"latitude":null' ) ) return false;
		return $valid;
	}

	function url($arg)
	{
		$arg[] = MP_Ip_ipstack_access_key;
		return $arg;
	}

	function data( $content, $ip )
	{
		$keep = array( 'country_code', 'region_code', 'region_name', 'city', 'zip', 'latitude', 'longitude', 'connection' );
		$html = '';

		$json =  json_decode( $content, true );
		foreach ( $json as $k => $v )
		{
			if ( $v == 'NA' ) continue;
			if ( empty( $v ) )   continue;

			if ( ( $k == 'connection' ) && ( isset( $k['connection']['isp'] ) ) ) $k['connection'] = $k['connection']['isp'];

			if ( !in_array( $k, $keep ) ) continue;

			if ( in_array( $k, array( 'country_code', 'region_code', 'latitude', 'longitude' ) ) ) {$$k = $v; continue;}

			$html .= '<p style="margin:3px;"><b>' . $k . '</b> : ' . $v . '</p>';
		}

		 $geo = array( 'lat' => $latitude, 'lng' => $longitude );

		$subcountry = ( 'US' == strtoupper( $country_code ) ) ? $region_code : MP_Ip::no_state;
		return $this->cache_custom( $ip, $geo, strtoupper( $country_code ), strtoupper( $subcountry ), $html );
	}
}
new MP_Ip_ipstack();
}