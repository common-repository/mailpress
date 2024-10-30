<?php
class MP_Ip_ipapi extends MP_ip_provider_
{
	var $id 	= 'ipapi';
	var $url	= 'https://ipapi.co/%1$s/xml/';
	var $credit = 'https://ipapi.co';
	var $type 	= 'xml';

	function content( $valid, $content )
	{
		if ( strpos( $content, '<error>True</error>' ) == true ) return false;
		return $valid;
	}

	function data( $content, $ip )
	{
		$skip = array( 'ip', 'continent_code', 'in_eu', 'timezone', 'utc_offset', 'country_calling_code' );
		$html = '';

		$xml = $this->xml2array( $content );
		foreach ( $xml as $k => $v )
		{
			if ( empty( $v ) )   continue;
			if ( $v == 'NA' ) continue;

			if ( in_array( $k, $skip ) ) continue;

			if ( in_array( $k, array( 'country', 'region_code', 'latitude', 'longitude' ) ) ) {$$k = $v; continue;}

			$html .= '<p style="margin:3px;"><b>' . $k . '</b> : ' . $v . '</p>';
		}
		$geo = ( isset( $latitude ) && isset( $longitude ) ) ? array( 'lat' => $latitude, 'lng' => $longitude ) : array();
		$country = ( isset( $country ) ) ? strtoupper( substr( $country, 0, 2 ) ) : '';
		if ( 'US' == $country )
		{
			$region_code = ( isset( $region_code ) ) ? $region_code : MP_Ip::get_USstate( $ip );
		}
		else $region_code = MP_Ip::no_state;
		$region =  $region_code ?? '';
		return $this->cache_custom( $ip, $geo, $country, $region_code, $html );
	}
}
new MP_Ip_ipapi();