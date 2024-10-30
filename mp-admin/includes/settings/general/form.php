<?php // general

$subscription_mngt = array ( 'ajax' => __( 'Default', 'MailPress' ), 'page_id' => __( 'Page template', 'MailPress' ), 'cat' => __( 'Category template', 'MailPress' ) );

if ( !isset( MP_AdminPage::$pst_['_tab'] ) || ( 'general' != MP_AdminPage::$pst_['_tab'] ) )
{
	$mp_general = get_option( MailPress::option_name_general );
}

if ( !isset( $mp_general['subscription_mngt'] ) )
{
	$mp_general['subscription_mngt'] = 'ajax';
	$mp_general['id'] = '';
}

$map_providers = array( 	'o' => __( 'OpenStreetMap', 'MailPress' ),
					'm' => __( 'Mapbox', 'MailPress' ),
					'g' => __( 'Google Map', 'MailPress' ),
					'b' => __( 'Bing maps', 'MailPress' ),
					'h' => __( 'Here maps', 'MailPress' ),
);

$map_fields    = array(	'b' => array(	0 => array(	'general' => 'bmapkey',
											'id' => 'b_key',
											'label' => __( 'Bing maps key', 'MailPress' ),
											'size' => 90,
									),
					),
					'g' => array(	0 => array(	'general' => 'gmapkey',
											'id' => 'g_key',
											'label' => __( 'Google Map API Key', 'MailPress' ),
											'size' => 90,
									),
					),
					'h' => array(	0 => array(	'general' => 'here_id',
											'id' => 'h_id',
											'label' => __( 'Here app_id', 'MailPress' ),
											'size' => 30,
									),
								1 => array(	'general' => 'here_code',
											'id' => 'h_code',
											'label' => __( 'Here app_code', 'MailPress' ),
											'size' => 30,
									),
					),
					'm' => array(	0 => array(	'general' => 'mapboxtoken',
											'id' => 'm_token',
											'label' => __( 'Mapbox token', 'MailPress' ),
											'size' => 120,
									),
					),
);

if ( !isset( $mp_general['map_provider'] ) ) $mp_general['map_provider'] = 'o';

?>
<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">


<!-- From -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'From', 'MailPress' ); ?></th>
			<td class="nopad"></td>
		</tr>
		<tr>
			<th><label for="general_fromemail"><?php _e( 'All Mails Sent From', 'MailPress' ); ?></label></th>
			<td class="nopad">
				<table class="subscriptions">
					<tr>
						<td class="pr10<?php if ( isset( MP_AdminPage::$err_mess['fromemail'] ) ) echo ' form-invalid'; ?>">
							<?php _e( 'Email : ', 'MailPress' ); ?>
							<input type="text" name="general[fromemail]" value="<?php echo ( isset( $mp_general['fromemail'] ) ) ? $mp_general['fromemail'] : ''; ?>" class="regular-text" id="general_fromemail" />
						</td>
						<td class="pr10<?php if ( isset( MP_AdminPage::$err_mess['fromname'] ) ) echo ' form-invalid'; ?>">
							<?php _e( 'Name : ', 'MailPress' ); ?> 
							<input type="text" name="general[fromname]" value="<?php echo ( isset( $mp_general['fromname'] ) ) ? esc_attr( $mp_general['fromname'] ) : ''; ?>" class="regular-text" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
<!-- Blog -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'On Blog', 'MailPress' ); ?></th>
			<td class="nopad"></td>
		</tr>
		<tr>
			<th><label for="general_fullscreen"><?php _e( 'View Mail In Fullscreen', 'MailPress' ); ?></label></th>
			<td>
				<input type="checkbox" name="general[fullscreen]" id="general_fullscreen"<?php checked( isset( $mp_general['fullscreen'] ) ); ?> />
			</td>
		</tr>
		<tr>
			<th><label for="general_subscription_mngt"><?php _e( ' Manage Subscriptions From', 'MailPress' ); ?></label></th>
			<td class="nopad">
				<table>
					<tr>
						<td>
							<select name="general[subscription_mngt]" class="subscription_mngt" id="general_subscription_mngt">
<?php MP_AdminPage::select_option( $subscription_mngt, $mp_general['subscription_mngt'] ?? false );?>
							</select>
						</td>
						<td class="mngt_id<?php if ( isset( MP_AdminPage::$err_mess['subscription_mngt'] ) ) echo ' form-invalid'; ?><?php if ( 'ajax' == $mp_general['subscription_mngt'] ) echo ' hidden'; ?>">
							<input type="text" name="general[id]" value="<?php echo $mp_general['id']; ?>" class="small-text" />
							<span class="page_id toggle<?php if ( 'page_id' != $mp_general['subscription_mngt'] ) echo ' hidden'; ?>"><?php _e( "Page id", 'MailPress' ); ?></span>
							<span class="cat     toggle<?php if ( 'cat'     != $mp_general['subscription_mngt'] ) echo ' hidden'; ?>"><?php _e( "Category id", 'MailPress' ); ?></span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<?php do_action( 'MailPress_settings_general_form' ); ?>

<!-- Admin -->
		<tr class="mp_sep">
			<th class="thtitle"><?php _e( 'Admin', 'MailPress' ); ?></th>
			<td class="nopad"></td>
		</tr>
		<tr>
			<th><label for="general_dashboard"><?php _e( 'Dashboard Widgets', 'MailPress' ); ?></label></th>
			<td>
				<input type="checkbox" name="general[dashboard]"<?php checked( isset( $mp_general['dashboard'] ) ); ?> id="general_dashboard" />
			</td>
		</tr>
		<tr>
			<th><label for="general_wp_mail"><?php _e( 'MailPress Version Of wp_mail', 'MailPress' ); ?></label></th>
			<td>
				<input type="checkbox" name="general[wp_mail]"<?php checked( isset( $mp_general['wp_mail'] ) ); ?> id="general_wp_mail" />
			</td>
		</tr>
<?php do_action( 'MailPress_settings_general_form_admin' ); ?>

<!-- Map -->
		<tr>
			<th><label for="general_map_provider"><?php _e( 'Map Provider', 'MailPress' ); ?></label></th>
			<td>
				<table class="map_provider">
					<tr>
						<th>
							<select name="general[map_provider]" id="general_map_provider">
<?php MP_AdminPage::select_option( $map_providers, $mp_general['map_provider'] ?? false );?>
							</select>
						</th>
						<td>
<?php foreach( $map_fields as $k => $v ) { ?>
							<span id="map_provider_<?php echo $k; ?>" class="map_providers<?php if ( $k != $mp_general['map_provider'] ) echo ' hidden'; ?>">
<?php 	foreach( $v as $kk => $vv )
		{
			$z = ( $kk ) ? '_' . $kk : '';
			if ( $kk ) echo "<br />\r\n";
?>
								<label id="<?php echo $k . $z; ?>_prompt_text" class="<?php echo ( isset( $mp_general[$vv['general']] ) && !empty( $mp_general[$vv['general']] ) ) ? 'hidden' : 'hide-if-no-js'; ?>" for="<?php echo $vv['id']; ?>"><?php echo $vv['label']; ?></label>
								<input type="text" size="<?php echo $vv['size']; ?>" name="general[<?php echo $vv['general']; ?>]" id="<?php echo $vv['id']; ?>" value="<?php if ( isset( $mp_general[$vv['general']] ) ) echo $mp_general[$vv['general']]; ?>" />
<?php		} ?>
							</span> 
<?php } ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<!-- Add ons -->
<?php do_action( 'MailPress_settings_general_form_footer' ); ?>

	</table>

<?php if( !$mp_general ) { ?>
	<span class="startmsg"><?php _e( 'You can start to update your mail server configuration, once you have saved your General settings', 'MailPress' ); ?></span>
<?php } ?>

<?php MP_AdminPage::save_button(); ?>

</form>