<?php // delete_inactive_users

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

$content .= '<hr />';

$content .= '<table>';

$content .= '<tr><th>';
$content .= __( 'Deleting Users', 'MailPress' );
$content .= '</th><td>';
$content .= __('to do some clean up on inactive, bounced, unsubscribed mailpress users !', 'MailPress' );
$content .= '</td></tr>';

// Return-Path
$content .= '<tr><th><span>';
$content .= __( 'Keep inactive...', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'All other "inactive" users will be deleted.', 'MailPress' );
$content .= '</td></tr>';

$content .= '</table>';