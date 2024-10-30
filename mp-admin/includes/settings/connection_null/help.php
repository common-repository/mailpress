<?php // connection_null

$content .= '<table>';
// null

$content .= '<tr><th>';
$content .= 'NULL';
$content .= '</th><td></td></tr>';

// Connect
$content .= '<tr><th><span>';

$content .= '</span></th><td>';
$content .= __( 'No parameters required for the NULL swiftmailer transport', 'MailPress' );
$content .= '<br />';
$content .= __( 'Only used for test and debug.', 'MailPress' );
$content .= '<br />';
$content .= '<br />';
$content .= __( '*** NO MESSAGE SENT ***', 'MailPress' );
$content .= '</td></tr>';

$content .= '<tr><td colspan="2">';
$content .= '<hr />';
$content .= '</td></tr>';
$content .= '</table>';