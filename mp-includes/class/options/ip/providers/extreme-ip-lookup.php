<?php
class MP_Ip_extreme_ip_lookup extends MP_ip_provider_
{
	var $id 	= 'extreme-ip-lookup';
	var $url	= 'https://extreme-ip-lookup.com/json/%1$s';
	var $credit	= 'https://extreme-ip-lookup.com/';
	var $type 	= 'json';

	function content( $valid, $content )
	{
		if ( strpos( $content, '"lat" : ""' ) ) return false;
		return $valid;
	}

	function data( $content, $ip )
	{
		$UsStates = array(
			'Alabama' => 'AL', 'Alaska' => 'AK', 'Arizona' => 'AZ', 'Arkansas' => 'AR', 
			'California' => 'CA', 'Colorado' => 'CO', 'Connecticut' => 'CT',
			'Delaware' => 'DE',
			'Floride' => 'FL',
			'Georgia' => 'GA',
			'Hawaii' => 'HI',
			'Idaho' => 'ID', 'Illinois' => 'IL', 'Indiana' => 'IN', 'Iowa' => 'IA',
			'Kansas' => 'KS', 'Kentucky' => 'KY',
			'Louisiana' => 'LA',
			'Maine' => 'ME', 'Maryland' => 'MD', 'Massachusetts' => 'MA', 'Michigan' => 'MI', 	'Minnesota' => 'MN', 	'Mississippi' => 'MS', 'Missouri' => 'MO', 'Montana' => 'MT',
			'Nebraska' => 'NE', 'Nevada' => 'NV', 'New Hampshire' => 'NH', 'New Jersey' => 'NJ', 'New Mexico' => 'NM', 'New York' => 'NY', 'North Carolina' => 'NC', 'North Dakota' => 'ND',
			'Ohio' => 'OH', 'Oklahoma' => 'OK', 'Oregon' => 'OR',
			'Pennsylvania' => 'PA',
			'Rhode Island' => 'RI',
			'South Carolina' => 'SC', 'South Dakota' => 'SD',
			'Tennessee' => 'TN', 'Texas' => 'TX',
			'Utah' => 'UT',
			'Vermont' => 'VT', 'Virginia' => 'VA',
			'Washington' => 'WA', 'West Virginia' => 'WV', 'Wisconsin' => 'WI', 'Wyoming' => 'WY',
		);
			
		$skip = array( 'continent', 'ipName', 'ipType', 'isp', 'query', 'status' );
		$html = '';

		$json =  json_decode( $content, true );

		foreach ( $json as $k => $v )
		{
			if ( $v == 'n/a' ) continue;
			if ( empty( $v ) ) continue;

			if ( in_array( $k, $skip ) ) continue;

			if ( in_array( $k, array( 'countryCode', 'region', 'lat', 'lon' ) ) ) {$$k = $v; continue;}

			$html .= '<p style="margin:3px;"><b>' . $k . '</b> : ' . $v . '</p>';
		}

		$geo = array( 'lat' => $lat, 'lng' => $lon );

		if ( 'US' == strtoupper( $countryCode ) )
		{
			$subcountry = $UsStates[$region] ?? MP_Ip::get_USstate( $ip );
		}
		else $subcountry = MP_Ip::no_state;
		return $this->cache_custom( $ip, $geo, strtoupper( $countryCode ), strtoupper( $subcountry ), $html );
	}
}
new MP_Ip_extreme_ip_lookup();