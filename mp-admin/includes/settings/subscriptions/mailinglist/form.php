<!-- subscriptions > mailinglist -->
<?php

// default mailinglist

$default_mailinglist	= get_option( self::option_name_default );

$dropdown_options = array(	'hide_empty' 	=> 0, 
					'hierarchical' 	=> true,
					'show_count' 	=> 0,
					'orderby' 		=> 'name',
					'selected' 	=> $default_mailinglist,
					'htmlname' 	=> 'default_mailinglist'
);

// opened to public

global $mp_general, $mp_subscriptions;
if ( !isset( $subscriptions ) ) $subscriptions = $mp_subscriptions;

$mls = array();
$mailinglists = apply_filters( 'MailPress_mailinglists', array() );

if ( empty( $mailinglists ) )
{
	_e( 'You need to create at least one mailinglist.', 'MailPress' );
}
else
{
	foreach ( $mailinglists as $k => $v ) 
	{
		$x = str_replace( 'MailPress_mailinglist~', '', $k, $count );
		if ( 0 == $count ) 	continue;	
		if ( empty( $x ) ) 	continue;
		$mls[$x] = $v;
	}

	$mls_o = '';
	foreach ( $mls as $k => $v )
	{
		$mls_id = 'subscriptions_display_mailinglists_' . $k;
		$mls_checked = ( isset( $mp_subscriptions['display_mailinglists'][$k] ) ) ? checked( 'on', $mp_subscriptions['display_mailinglists'][$k], false ) : '';
		$mls_o .= '<label for="' . $mls_id . '"><input type="checkbox" name="subscriptions[display_mailinglists][' . $k . ']" id="' . $mls_id . '" ' . $mls_checked . ' />&#160;&#160;' . $v . '</label><br />' . "\r\n";
	}
}
?>

			<tr class="mp_sep">
				<th class="thtitle"><?php _e( 'Mailing lists', 'MailPress' ); ?></th>
				<td colspan="4"></td>
			</tr>
			<tr>
				<th><?php _e( 'Default', 'MailPress' ); ?></th>
				<td class="nopad" colspan="4">
					<?php	 MP_Mailinglist::dropdown( $dropdown_options ); ?>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Opened To Public', 'MailPress' ); ?></th>
				<td colspan="4">
	
					<input type="hidden"   name="mailinglist[on]" value="on" />
	
					<table id="mailinglists" class="general">
						<tr>
							<td><?php echo $mls_o; ?></td>
						</tr>
					</table>
				</td>
			</tr>