<?php // batches

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

$content .= '<table>';
$content .= '<tr><th>';
$content .= __( 'Batches', 'MailPress' );
$content .= '</th><td>';
$content .= '</td></tr>';
$content .= '<tr><td colspan="2">';
$content .= __( 'Depending on which Add-ons you have activated, you will have to set specific settings for each type of batch.', 'MailPress' );
$content .= '</td></tr>';
$content .= '</table>';

$content .= '<hr />';

$content = apply_filters( 'MailPress_settings_batches_help', $content );

$content .= '<hr />';

$content .= '<table>';

// 
$content .= '<tr><td colspan="2">';

$content .= __( 'But for every batch, you will have to set how they are scheduled.', 'MailPress' );

$content .= MP_AdminPage::cron_help();

$content .= '</table>';
