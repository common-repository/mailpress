<?php // roles_and_capabilities => no sanitize here, only checkboxes

if ( isset( MP_AdminPage::$pst_['cap'] ) )
{
	global $wp_roles;
	foreach( $wp_roles->role_names as $role => $name )
	{
		if ( 'administrator' == $role ) continue;
        
		if ( !isset( MP_AdminPage::$pst_['cap'][$role] ) ) MP_AdminPage::$pst_['cap'][$role] = array();
        
		update_option( 'MailPress_r&c_' . $role, MP_AdminPage::$pst_['cap'][$role] );    
	}

	$message = __( "'Roles and capabilities' settings saved", 'MailPress' );
}