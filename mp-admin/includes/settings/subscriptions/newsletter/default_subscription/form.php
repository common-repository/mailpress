<!-- subscriptions > newsletter_default_subscription -->
<?php //newsletter_default_subscription

global $mp_general, $mp_subscriptions, $mp_registered_newsletters;

if ( !isset( $subscriptions ) ) $subscriptions = $mp_subscriptions;
if ( !isset( $subscriptions['default_newsletters'] ) ) $subscriptions['default_newsletters'] = array();

$settings = get_option( MailPress_comment_newsletter_subscription::option_name );

$args = array( 	'htmlname'	=> 'comment_newsletter_subscription[default]',
			'htmlid'	=> 'comment_newsletter_subscription_default', 
			'admin'	=> true, 
			'type'	=> 'select',
			'selected'	=> $settings['default'] ?? '',
 );
/*
$out = '';
foreach ( $mp_registered_newsletters as $mp_registered_newsletter )
{
	$style = ( isset( $subscriptions['newsletters'][$mp_registered_newsletter['id']] ) ) ? '' : ' class="hidden"';
	$out .= '<option id="option_default_newsletter_' . $mp_registered_newsletter['id'] . '" value="' . $mp_registered_newsletter['id'] . '"' . selected( ( isset( $settings['default'] ) ) ? $settings['default'] : '', $mp_registered_newsletter['id'], false ) . $style . '>' . $mp_registered_newsletter['descriptions']['admin'] . '</option>';
}
$out = '<select name="comment_newsletter_subscription[default]">' . $out . '</select>';
*/
?>
		<tr>
			<th><?php _e( 'Default Subscription', 'MailPress' ); ?><br /><?php _e( 'On Comment Form', 'MailPress' ); ?></th>
			<td class="nopad" colspan="4">
				<table>
					<tr>
						<td class="nopad">
							<?php //echo $out; ?>
							<?php echo MailPress_newsletter::get_checklist( false, $args ); ?>
						</td>
						<td class="nopad">
							&#160;<?php _e( 'checked by default', 'MailPress' ); ?>&#160;
							<input type="checkbox" name="comment_newsletter_subscription[checked]"<?php checked( ( isset( $settings['checked'] ) ) ); ?> />
						</td>
					</tr>
				</table>
			</td>
		</tr>
