<?php // bounce_handling

$content .= '<table>';

$content .= '<tr><th>';
$content .= sprintf( '<a href="https://en.wikipedia.org/wiki/Bounce_message" target="_blank">%s</a>', __( 'Handling Bounces', 'MailPress' ) );
$content .= '</th><td>';
$content .= __('to discard failing recipients from future mailing.', 'MailPress' );
$content .= ' ';
$content .= sprintf( __( 'based on %s.', 'MailPress' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'http://en.wikipedia.org/wiki/VERP', 'VERP' ) );
$content .= '</td></tr>';

$content .= '<tr><td colspan="2"><hr /></td></tr>';

$content .= MP_AdminPage::pop3_help();

$content .= '<tr><td colspan="2"><hr /></td></tr>';

// Return-Path
$content .= '<tr><th><span>';
$content .= sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://en.wikipedia.org/wiki/Bounce_address', 'Return-Path' );
$content .= '</span></th><td>';
$content .= __( 'have a specific mailbox like <code>bounces@mydomain.tld</code>.', 'MailPress' );
$content .= '</td></tr>';

// Max bounces per user
$content .= '<tr><th><span>';
$content .= __( 'Max bounces...', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'When the amount of bounces is reached for a recipient, it is set as &#8220;bounced&#8221;.', 'MailPress' );
$content .= '<br />';
$content .= sprintf( __( 'Bounced recipients can be quickly identified in %1$s with a little @ icon : %2$s', 'MailPress' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', MailPress_users, __( 'MailPress Users list', 'MailPress' ) ) , '<span class="mp_icon mp_icon_bounced" title="' . __('Bounced', 'MailPress' ) . '" ></span>' );
$content .= '</td></tr>';

// Bounce in mailbox
$content .= '<tr><th><span>';
$content .= __( 'in mailbox', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'for each bounce detected, the bounce will be ... .', 'MailPress' );
$content .= ' ';
$content .= __( 'Make sure to clean up frequently your inbox.', 'MailPress' );
$content .= '</td></tr>';

$content .= '<tr><td colspan="2"><hr /></td></tr>';

$content .= MP_AdminPage::cron_help();

$content .= '</table>';