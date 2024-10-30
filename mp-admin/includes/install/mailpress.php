<?php

/* MailPress install */

global $wpdb, $wp_version;

$m = array();

if ( version_compare( $wp_version, $min_ver_wp , '<' ) )	$m[] = sprintf( __( 'Your %1$s version is \'%2$s\', at least version \'%3$s\' required.', 'MailPress' ), __( 'WordPress' ), $wp_version , $min_ver_wp );
if ( !extension_loaded( 'simplexml' ) )				$m[] = sprintf( __( 'Default php extension \'%1$s\' not loaded.', 'MailPress'), 'simplexml' );
if ( !extension_loaded( 'intl' ) )				$m[] = sprintf( __( 'Default php extension \'%1$s\' not loaded.', 'MailPress'), 'intl' );
if ( !function_exists( 'proc_open' ) )				$m[] =          __( '"proc_open" php function is not available, ask your webhost technical support.', 'MailPress' );

 
//////////////////////////////////
////            7.0           ////     Since MailPress 7.0 content of tmp and mp-content/advanced is moved/copied to uploads mailpress directory
//////////////////////////////////

if ( !is_dir( MP_UPL_ABSPATH ) && !mkdir( MP_UPL_ABSPATH ) ) 		$m[] = sprintf( __( 'The directory \'%1$s\' cannot be created.', 'MailPress' ), MP_UPL_ABSPATH );
if (  is_dir( MP_UPL_ABSPATH ) && !is_writable( MP_UPL_ABSPATH ) ) 	$m[] = sprintf( __( 'The directory \'%1$s\' is not writable.', 'MailPress' ), MP_UPL_ABSPATH );

$mp_tmp_renames = array ( 'log' => 'MP_Log_' . $wpdb->blogid . '_*.txt', 'ip' => '*.spc', 'oembed' => '*.png', );

foreach( $mp_tmp_renames as $dir => $pattern )
{
	$dir = MP_UPL_ABSPATH . $dir;
	if ( !is_dir( $dir ) && !mkdir( $dir ) ) 					$m[] = sprintf( __( 'The directory \'%1$s\' cannot be created.', 'MailPress' ), $dir );
	if ( is_dir( $dir )  && !is_writable( $dir ) )				$m[] = sprintf( __( 'The directory \'%1$s\' is not writable.', 'MailPress' ), $dir );
}

//

if ( !empty( $m ) )
{
	$plugin = filter_input( INPUT_GET, 'plugin' );
	$err  = sprintf( __( '<b>Sorry, but you can\'t run this plugin : %1$s. </b>', 'MailPress' ), $plugin );
	$err .= '<ol><li>' . implode( '</li><li>', $m ) . '</li></ol>';

	if ( isset( $plugin ) ) deactivate_plugins( $plugin );	
	trigger_error( $err, E_USER_ERROR );
	return false;
}


//////////////////////////////////
////            7.0           ////     Since MailPress 7.0 content of tmp and mp-content/advanced is moved/copied to uploads mailpress directory
//////////////////////////////////
//// TMP ////

if ( is_dir( MP_ABSPATH . 'tmp' ) )
{

// log / ip / oembed 

	foreach( $mp_tmp_renames as $dir => $pattern )
	{
		$files = glob( MP_ABSPATH . 'tmp/' . $pattern );
		if ( $files )
		{
			$log = ( ( 'log' == $dir ) && ( $wpdb->blogid != get_current_blog_id() ) );
			foreach( $files as $file )
			{
				$dfile = ( $log ) ? str_replace( 'MP_Log_' . $wpdb->blogid . '_', 'MP_Log_' . get_current_blog_id() . '_', basename( $file ) ) : basename( $file );
				rename( $file, MP_UPL_ABSPATH . $dir . '/' . $dfile );
			}
			if( !file_exists( MP_UPL_ABSPATH . $dir . '/index.php' )) copy( MP_CONTENT_DIR . 'index.php', MP_UPL_ABSPATH . $dir . '/index.php' );
		}
	}

// spool ... nothing done here ! expecting all spooled mails are sent and directories are empty.

	$spool_path = 'spool/' . get_current_blog_id() . '/';

	if ( !is_dir( MP_UPL_ABSPATH . $spool_path ) )
	{
		$batch_spool_send = get_option( 'MailPress_batch_spool_send' );
		if ( $batch_spool_send )
		{
			if ( isset( $batch_spool_send['path'] ) && is_dir( $batch_spool_send['path'] ) )
			{
				//rename( $batch_spool_send['path'], MP_UPL_ABSPATH . $spool_path );
				unset( $batch_spool_send['path'] );
				update_option( 'MailPress_batch_spool_send', $batch_spool_send );
			}
		}
	}
}

//// end of TMP ////

//// ADVANCED ////

$advanced_path = 'advanced/' . get_current_blog_id() . '/';

if ( !is_dir( MP_UPL_ABSPATH . $advanced_path ) )
{
	if ( is_dir( MP_CONTENT_DIR . 'advanced' ) )
	{
		// mirroring the files for this blog
		$source = untrailingslashit( MP_CONTENT_DIR . 'advanced' );
		$dest   = untrailingslashit( MP_UPL_ABSPATH . $advanced_path );
		mkdir( $dest, 0777, true );
		foreach( $iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST ) as $item )
		{
			if ($item->isDir()) 	mkdir( $dest . '/' . $iterator->getSubPathName(), 0777, true );
			else 				copy( $item, $dest . '/' . $iterator->getSubPathName() );
		}
	}
}

//// end of ADVANCED ////

//////////////////////////////////
//// Install                  ////
//////////////////////////////////

// theme init
if ( !get_option( 'MailPress_current_theme' ) )
{
	add_option ( 'MailPress_template',	'twentyten' );
	add_option ( 'MailPress_stylesheet',	'twentyten' );
	add_option ( 'MailPress_current_theme',	'MailPress Twenty Ten' );
}

$charset_collate = $wpdb->get_charset_collate();

$mp_tables = "CREATE TABLE $wpdb->mp_mails (
 id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 status enum('draft','unsent','sending','sent','archived','','paused') NOT NULL,
 theme varchar(255) NOT NULL DEFAULT '',
 themedir varchar(255) NOT NULL DEFAULT '',
 template varchar(255) NOT NULL DEFAULT '',
 fromemail varchar(255) NOT NULL DEFAULT '',
 fromname varchar(255) NOT NULL DEFAULT '',
 toname varchar(255) NOT NULL DEFAULT '',
 charset varchar(255) NOT NULL DEFAULT '',
 parent bigint(20) unsigned NOT NULL DEFAULT 0,
 child bigint(20) NOT NULL DEFAULT 0,
 subject varchar(255) NOT NULL DEFAULT '',
 created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 created_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
 sent timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 sent_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
 toemail longtext NOT NULL,
 plaintext longtext NOT NULL,
 html longtext NOT NULL,
 PRIMARY KEY (id),
 KEY status (status)
) $charset_collate;
CREATE TABLE $wpdb->mp_mailmeta (
 meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 mp_mail_id bigint(20) unsigned NOT NULL DEFAULT 0,
 meta_key varchar(255) NOT NULL DEFAULT '',
 meta_value longtext,
 PRIMARY KEY (meta_id),
 KEY mp_mail_id (mp_mail_id),
 KEY meta_key (meta_key)
) $charset_collate;
CREATE TABLE $wpdb->mp_users (
 id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 email varchar(100) NOT NULL,
 name varchar(100) NOT NULL,
 status enum('waiting','active','bounced','unsubscribed') NOT NULL,
 confkey varchar(100) NOT NULL,
 created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 created_IP varchar(100) NOT NULL DEFAULT '',
 created_agent text NOT NULL,
 created_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
 created_country char(2) NOT NULL DEFAULT 'ZZ',
 created_US_state char(2) NOT NULL DEFAULT 'ZZ',
 laststatus timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 laststatus_IP varchar(100) NOT NULL DEFAULT '',
 laststatus_agent text NOT NULL,
 laststatus_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
 PRIMARY KEY (id),
 KEY status (status)
) $charset_collate;
CREATE TABLE $wpdb->mp_usermeta (
 meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 mp_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
 meta_key varchar(255) NOT NULL DEFAULT '',
 meta_value longtext,
 PRIMARY KEY (meta_id),
 KEY mp_user_id (mp_user_id),
 KEY meta_key (meta_key)
) $charset_collate;
CREATE TABLE $wpdb->mp_stats (
 sdate date NOT NULL,
 stype char(1) NOT NULL,
 slib varchar(45) NOT NULL,
 scount bigint(20) NOT NULL,
 PRIMARY KEY (stype,sdate,slib)
) $charset_collate;\n";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $mp_tables );

// some clean up
$wpdb->query( "DELETE FROM $wpdb->mp_mails    WHERE status = '' AND theme <> '';" );
$wpdb->query( "DELETE FROM $wpdb->mp_mailmeta WHERE mp_mail_id NOT IN ( SELECT id FROM $wpdb->mp_mails );" );
$wpdb->query( "DELETE FROM $wpdb->mp_usermeta WHERE mp_user_id NOT IN ( SELECT id FROM $wpdb->mp_users );" );
$wpdb->query( "DELETE FROM $wpdb->mp_usermeta WHERE meta_value NOT IN ( SELECT id FROM $wpdb->mp_mails ) AND meta_key = '_MailPress_mail_sent' ;" );
$wpdb->query( "DELETE FROM $wpdb->mp_stats    WHERE scount = 0 ;" );

$wpdb->query( "UPDATE $wpdb->mp_mailmeta SET meta_key = '_MailPress_attached_file' WHERE meta_key = '_mp_attached_file';" );