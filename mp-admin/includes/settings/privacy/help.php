<?php // privacy

$content .= '<table>';

$content .= '<tr><th>';
$content .= __( 'Privacy', 'MailPress' );
$content .= '</th><td> ';
$content .= __('to create privacy requests from mail sent to a dedicated mailbox with a specific subject.', 'MailPress' );
$content .= '</td></tr>';

$content .= '<tr><td colspan="2"><hr /></td></tr>';

$content .= MP_AdminPage::pop3_help();

$content .= '<tr><td colspan="2"><hr /></td></tr>';

// Dedicated mail subjects
$content .= '<tr><th><span>';
$content .= __( 'Mail subjects', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'Should be an explicit word for each specific request (export/erase).', 'MailPress' );
$content .= '</td></tr>';

$content .= '<tr><td colspan="2"><hr /></td></tr>';

$content .= MP_AdminPage::cron_help();

$content .= '</table>';