<?php // list_unsubscribe

$content .= '<table>';

$content .= '<tr><th>List-Unsubscribe</th><td> ';
$content .= __('to process List-Unsubscribe.', 'MailPress' );
$content .= '</td></tr>';

$content .= '<tr><td colspan="2"><hr /></td></tr>';

$content .= MP_AdminPage::pop3_help();

$content .= '<tr><td colspan="2"><hr /></td></tr>';

// Subscriber and subscriptions
$content .= '<tr><th><span>';
$content .= __( 'List-Unsubscribe processing', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'a) delete subscriber,', 'MailPress' );
$content .= '<br />';
$content .= __( 'b) delete all subscriptions for that subscriber,', 'MailPress' );
$content .= '<br />';
$content .= __( 'c) delete specific subscription from the sending list (for that specific mail) : i.e. comment, mailinglist, newsletter', 'MailPress' );
$content .= '</td></tr>';

$content .= '<tr><td colspan="2"><hr /></td></tr>';

$content .= MP_AdminPage::cron_help();

$content .= '</table>';