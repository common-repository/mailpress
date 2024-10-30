<?php // signer_dkim

$content .= '<table>';
// dkim

$content .= '<tr><th>';
$content .= sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://en.wikipedia.org/wiki/DomainKeys_Identified_Mail', __( 'DKIM', 'MailPress' ) );
$content .= '</th><td></td></tr>';

// PrivateKey
$content .= '<tr><th><span>';
$content .= __( 'PrivateKey', 'MailPress' );
$content .= '</span></th><td>';
$content .= __('(full path to .pem file)', 'MailPress' );
$content .= '</td></tr>';

// DomainName
$content .= '<tr><th><span>';
$content .= __( 'Domain Name', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'domain name sending the mails.', 'MailPress' );
$content .= '</td></tr>';

// Selector
$content .= '<tr><th><span>';
$content .= __( 'Selector', 'MailPress' );
$content .= '</span></th><td>';
$content .= sprintf( __( 'Name of the %1$s Record in DNS', 'MailPress' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://en.wikipedia.org/wiki/TXT_record', 'TXT' ) );
$content .= '</td></tr>';


// Ressources
$content .= '<tr><th><span>';
$content .= __( 'Ressources', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'Many ressources available on the web, just to cite a few :', 'MailPress' );
$content .= '<ul><li><code><a href="https://www.openssl.org/" target="_blank">OpenSSL</a></code></li>';
$content .= '<li><code><a href="http://www.dkim.org/" target="_blank">dkim.org</a></code></li>';
$content .= '<li><code><a href="https://dkimcore.org/" target="_blank">dkimcore</a></code></li>';

$content .= '<li><code><a href="https://www.dataenter.com/doc/general_domainkeys.htm" target="_blank">DKIM Quick Start (OpenSSL commands only)</a></code></li></ul>';
$content .= '</td></tr>';

$content .= '</table>';