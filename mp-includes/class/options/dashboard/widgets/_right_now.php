<?php
class MP_Dashboard__right_now extends MP_WP_Dashboard_widget_
{
	var $id = 'mp__right_now';

	function widget()
	{
		global $wpdb, $wp_locale;

		$countm = $wpdb->get_var( "SELECT sum( scount ) FROM $wpdb->mp_stats WHERE stype = 't';" );
		$counts = $wpdb->get_var( "SELECT count( * )    FROM $wpdb->mp_users WHERE status = 'active';" );
		if ( !$countm ) $countm = 0;
		if ( !$counts ) $counts = 0;

		$plugin_data = get_plugin_data( MP_ABSPATH . 'MailPress.php' );
		$plugin_version = $plugin_data['Version'];

		$th = new MP_Themes();
		$themes = $th->themes; 
		$ct = $th->current_theme_info(); 
?>
<div id="dashboard_right_now">
<div class="inside">
	<div class="table table_content">
		<table>
			<tr class="first">
				<td class="first b b-posts">
<?php 	if ( current_user_can( 'MailPress_edit_mails' ) ) : ?>
					<a href="<?php echo MailPress_mails; ?>"><?php echo $countm; ?></a>
<?php 	else : ?>
					<?php echo $countm; ?>
<?php 	endif; ?>
				</td>
				<td class="t posts"><?php echo( _n( __( 'Mail sent', 'MailPress' ), __( 'Mails sent', 'MailPress' ), $countm ) ); ?></td>
			</tr>
		</table>
	</div>
	<div class="table table_discussion">
		<table>
			<tr class="first">
				<td class="b b-comments">
<?php 	if ( current_user_can( 'MailPress_edit_users' ) ) : ?>
					<a href="<?php echo MailPress_users; ?>"><?php echo $counts; ?></a>
<?php 	else : ?>
					<?php echo $counts; ?>
<?php 	endif; ?>
				</td>
				<td class="last t approved"><?php echo( _n( __( 'Active subscriber', 'MailPress' ), __( 'Active subscribers', 'MailPress' ), $counts ) ); ?></td>
			</tr>
		</table>
	</div>
	<div class="versions">
		<p>
<?php 	
		$theme_title = ( current_user_can( 'MailPress_switch_themes' ) ) ? '<a href="' . MailPress_themes . '">' . $ct->title . '</a>' : $ct->title;
		printf( __( 'Theme %s', 'MailPress' ), '<span class="b">' . $theme_title . '</span>' );
?>
		</p>
	</div>
	<div style="float:right;">
		<table>
			<tr>
				<td>
					<span id="mp_paypal" style="float:right;padding:0;margin:0;">
						<a href="https://paypal.me/arenaut" target="_blank"><img title="<?php echo esc_attr( __( 'Thank you :-) !', 'MailPress' ) ); ?>"  alt="<?php echo esc_attr( __( 'Thank you :-) !', 'MailPress' ) ); ?>" src="<?php echo site_url() . '/' . MP_PATH; ?>mp-includes/images/PP_M.png" /></a>
					</span>
				</td>
			</tr>
		</table>
	</div>
	<div>
		<table>
			<tr>
				<td>
					<?php printf( __( 'You are using <strong>MailPress %s</strong>.', 'MailPress' ), $plugin_version ); ?>
				</td>
			</tr>
		</table>
	</div>
<?php $this->add_ons(); ?>
</div>
</div>
<?php
	}

	function add_ons()
	{
		$addons = MP_Addons::get_all();
		$out = array();
		foreach( $addons as $addon )
		{
			if ( !$addon['active'] ) continue;
		
			$haystack = $addon['Name'];
			$needle   = 'MailPress_';
			if ( strpos( $haystack, $needle ) === 0 ) 
			{
				$haystack = substr( $haystack, strlen( $needle ) );
				$haystack = ucfirst( $haystack );
				$out[] = $haystack;
			}
		}
		if ( !empty( $out ) )
		{
			echo '<div id="add-ons"><br /><hr />' . __( 'With following add-ons :', 'MailPress' ) . '<br />' . implode( ', ', $out ) . '</div>';
		}
	}
}
new MP_Dashboard__right_now( __( "MailPress - 'Right Now'", 'MailPress' ) );