<!-- subscriptions > newsletter -->
<?php // newsletter

global $mp_general, $mp_subscriptions, $mp_registered_newsletters;

if ( !isset( $subscriptions ) ) $subscriptions = $mp_subscriptions;
if ( !isset( $subscriptions['default_newsletters'] ) ) $subscriptions['default_newsletters'] = array();

$col = count( MailPress_newsletter::xml_files );
$item  = 1;
$row = $col * $item;
$i = $j = $td = $tr = $alt = 0;

$out = '';

foreach ( $mp_registered_newsletters as $mp_registered_newsletter )
{
	if ( intval ( $i/$row ) == $i/$row ) 
	{
		$alt++;
		$alternate = ( 1 == $alt ) ? ' class="bkgndc bd1sc"' : ( ( ( $alt/2 ) != intval( $alt/2 ) ) ? ' class="bkgndc"' : '' );
		$tr = true; 
		$td = 0;

		$out .= '<tr' . $alternate . '><th>';
		$out .= apply_filters( 'MailPress_subscriptions_newsletter_th', '** ' . __( 'Post' ) . ' **', $mp_registered_newsletter );
		$out .= '</th>';
	}
	if ( intval ( $j/$item ) == $j/$item )
	{
		$out .= '<td>';
		++$td;
	}

	$default_style = ( isset( $subscriptions['newsletters'][$mp_registered_newsletter['id']] ) ) ? '' : ' class="hidden"' ;
	$out .= '<label for="newsletter_' . $mp_registered_newsletter['id'] . '">';
	$out .= '<input type="checkbox" name="subscriptions[newsletters][' . $mp_registered_newsletter['id'] . ']" id="newsletter_' . $mp_registered_newsletter['id'] . '" class="newsletter"' . checked( isset( $subscriptions['newsletters'][$mp_registered_newsletter['id']] ), true, false ) . ' newsletter="' . $mp_registered_newsletter['id'] . '" newsletter_description="' . esc_attr( $mp_registered_newsletter['descriptions']['admin'] ) . '" />';
	$out .= '&#160;' . $mp_registered_newsletter['descriptions']['admin'];
	$out .= '</label><br />';
	$out .= '<label for="default_newsletter_' . $mp_registered_newsletter['id'] . '">';
	$out .= '<span id="span_default_newsletter_' . $mp_registered_newsletter['id'] . '"' . $default_style . '>';
	$out .= '<input type="checkbox" name="subscriptions[default_newsletters][' . $mp_registered_newsletter['id'] . ']" id="default_newsletter_' . $mp_registered_newsletter['id'] . '"' . checked( isset( $subscriptions['default_newsletters'][$mp_registered_newsletter['id']] ), true, false ) . ' />';
	$out .= '&#160;' . __( 'default', 'MailPress' ) . '</span></label>';

	$j++;
	if ( intval ( $j/$item ) == $j/$item ) $out .= '</td>';
	$i++;
	if ( intval ( $i/$row ) == $i/$row ) {  $out .= '</tr>'; $tr = false; }
}
if ( intval ( $j/$item ) != $j/$item )
{
	$out .= '</td>'; 
	while ( $td < $item ) {  $out .= '<td></td>'; ++$td;}
}
if ( intval ( $i/$row ) != $i/$row )   $out .= '</tr>';
$out .= "\n";
?>
			<tr class="mp_sep">
				<th class="thtitle"><?php _e( 'Newsletters', 'MailPress' ); ?></th>
				<td colspan="4"><input type="hidden" name="newsletter[on]" value="on" /></td>
			</tr>
			<tr>
				<th><?php _e( 'Show At Most', 'MailPress' ); ?></th>
				<td colspan="4" class="nopad">
					<select name="newsletter[post_limits]">
<option value="0">&#160;</option>
<?php MP_AdminPage::select_number( 1, 99, MailPress_newsletter::get_post_limits() ); ?>
					</select>
				&#160;<?php _e( 'posts <i>(blank = WordPress Reading setting)</i>', 'MailPress' ); ?>
				</td>
			</tr>
<?php echo $out; ?>
<?php do_action( 'MailPress_settings_subscriptions_newsletter_form' ); ?>
