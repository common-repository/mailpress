<?php //sync_wordpress_user => no sanitize here, only checkbox

if ( isset( MP_AdminPage::$pst_['sync_wordpress_user'] ) )
	update_option( MailPress_sync_wordpress_user::option_name, MP_AdminPage::$pst_['sync_wordpress_user'] );
else
	delete_option( MailPress_sync_wordpress_user::option_name );

