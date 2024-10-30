<?php // connection_smtp

$xssl = array ( ''	=> __( 'No', 'MailPress' ),
			'ssl'	=> 'SSL' ,
			'tls'	=> 'TLS' 
 ); 
$xport = array ( 	'25'		=> __( 'Default SMTP Port', 'MailPress' ),
			'465'		=> __( 'Use for SSL/TLS/GMAIL', 'MailPress' ),
			'custom'	=> __( 'Custom Port: (Use Box)', 'MailPress' )
 ); 

if ( !isset( $connection_smtp ) ) $connection_smtp = get_option( MailPress::option_name_smtp );

$connection_smtp['customport'] = '';
if ( isset( $connection_smtp['port'] ) && !in_array( $connection_smtp['port'], array( 25, 465 ) ) ) 
{
	$connection_smtp['customport'] = $connection_smtp['port']; 
	$connection_smtp['port'] = 'custom';
}

if ( isset( $pophostclass ) ) $connection_smtp['smtp-auth'] = '@PopB4Smtp';

$popb4smtp_class = '';
$popb4smtp_class .= ( isset( $connection_smtp['smtp-auth'] ) && ( '@PopB4Smtp' == $connection_smtp['smtp-auth'] ) ) ? '' : 'hidden ';
$popb4smtp_class .= ( !isset( $pophostclass ) ) ? '' : 'form-invalid'; 

?>
<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">

<!-- smtp server -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['server'] ) ) echo ' class="form-invalid"'; ?>>
			<th><label for="connection_smtp_server"><?php _e( 'SMTP Server', 'MailPress' ); ?></label></th>
			<td colspan="2">
				<input type="text" name="connection_smtp[server]" value="<?php echo ( isset( $connection_smtp['server'] ) ) ? esc_attr( $connection_smtp['server'] ) : ''; ?>" class="regular-text code" id="connection_smtp_server" />
			</td>
		</tr>
<!-- smtp username -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['username'] ) ) echo ' class="form-invalid"'; ?>>
			<th><label for="connection_smtp_username"><?php _e( 'Login Name', 'MailPress' ); ?></label></th>
			<td colspan="2">
				<input type="text" name="connection_smtp[username]" value="<?php echo ( isset( $connection_smtp['username'] ) ) ? esc_attr( $connection_smtp['username'] ) : ''; ?>" class="regular-text" id="connection_smtp_username" />
			</td>
		</tr>
<!-- smtp pw -->
		<tr>
			<th><label for="connection_smtp_password"><?php _e( 'Password', 'MailPress' ); ?></label></th>
			<td colspan="2">
				<input type="password" name="connection_smtp[password]" value="<?php echo ( isset( $connection_smtp['password'] ) ) ? esc_attr( $connection_smtp['password'] ) : ''; ?>" class="regular-text ltr" id="connection_smtp_password" />
			</td>
		</tr>
<!-- smtp ssl/tls -->
		<tr>
			<th><label for="connection_smtp_ssl"><?php _e( 'Use SSL Or TLS ?', 'MailPress' ); ?></label></th>
			<td colspan="2">
				<select name="connection_smtp[ssl]" id="connection_smtp_ssl">
<?php MP_AdminPage::select_option( $xssl, $connection_smtp['ssl'] ?? 'No' );?>
				</select>
				&#160;
<i><?php printf( __( 'Site registered socket transports are : <b>%1$s</b>', 'MailPress' ), ( array() == stream_get_transports() ) ? __( 'none', 'MailPress' ) : implode( '</b>, <b>',stream_get_transports() ) ); ?></i>
			</td>
		</tr>
<!-- smtp port -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['customport'] ) ) echo ' class="form-invalid"'; ?>>
			<th><label for="connection_smtp_port"><?php _e( 'Port', 'MailPress' ); ?></label></th>
			<td colspan="2">
				<select name="connection_smtp[port]" id="connection_smtp_port">
<?php MP_AdminPage::select_option( $xport, $connection_smtp['port'] ?? '25' );?>
				</select>
				&#160;
				<input type="text" name="connection_smtp[customport]" value="<?php echo $connection_smtp['customport']; ?>" class="small-text" />
			</td>
		</tr>
<!-- smtp popB4smtp -->
		<tr<?php if ( isset( MP_AdminPage::$err_mess['smtp-auth'] ) ) echo ' class="form-invalid"'; ?>>
			<th>
				<label for="connection_smtp_auth"><?php _e( 'Pop Before Smtp', 'MailPress' ); ?></label>
			</th>
			<td> 
				<input type="checkbox" value="@PopB4Smtp" name="connection_smtp[smtp-auth]"<?php if ( isset( $connection_smtp['smtp-auth'] ) ) checked( '@PopB4Smtp', $connection_smtp['smtp-auth'] ); ?> id="connection_smtp_auth" />
			</td>
			<td id="POP3" class="<?php echo $popb4smtp_class; ?>"> 
				<?php _e( "POP3 hostname", 'MailPress' ); ?>
				<input type="text" name="connection_smtp[pophost]" value="<?php if ( isset( $connection_smtp['pophost'] ) ) echo esc_attr( $connection_smtp['pophost'] ); ?>" class="regular-text code ltr" />
				<?php _e( "port", 'MailPress' ); ?>
				<input type="text" name="connection_smtp[popport]" value="<?php if ( isset( $connection_smtp['popport'] ) ) echo $connection_smtp['popport']; ?>" class="small-text" />
			</td>
		</tr>

	</table>

<?php MP_AdminPage::save_button(); ?>

</form>