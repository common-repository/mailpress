<?php

/* Tracking install */

global $wpdb;

//////////////////////////////////
//// Install                  ////
//////////////////////////////////

$charset_collate = $wpdb->get_charset_collate();

$mp_tables = "CREATE TABLE $wpdb->mp_tracks (
 id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
 user_id bigint(20) NOT NULL default '0',
 mail_id bigint(20) NOT NULL default '0',
 tmstp timestamp NOT NULL default '0000-00-00 00:00:00',
 mmeta_id bigint(20) NOT NULL default '0',
 context varchar(40) NOT NULL default 'html',
 ip varchar(100) NOT NULL default '',
 agent varchar(255) NOT NULL default '',
 track longtext,
 referrer longtext,
 PRIMARY KEY (id),
 UNIQUE KEY id (id),
 KEY user_id  (user_id),
 KEY mail_id  (mail_id),
 KEY mmeta_id (mmeta_id)
) $charset_collate;\n";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $mp_tables );


// some clean up
$wpdb->query( "DELETE FROM $wpdb->mp_tracks WHERE mail_id NOT IN ( SELECT id FROM $wpdb->mp_mails ) AND user_id NOT IN ( SELECT id FROM $wpdb->mp_users );" );