<?php // signer_smime

$content .= '<table>';
// smime

$content .= '<tr><th>';
$content .= sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://en.wikipedia.org/wiki/S/MIME', __( 'S/Mime', 'MailPress' ) );
$content .= '</th><td></td></tr>';

// Certificate
$content .= '<tr><th><span>';
$content .= __( 'Certificate', 'MailPress' );
$content .= '</span></th><td>';
$content .= __('(full path to .pem file)', 'MailPress' );
$content .= '</td></tr>';

// PrivateKey
$content .= '<tr><th><span>';
$content .= __( 'PrivateKey', 'MailPress' );
$content .= '</span></th><td>';
$content .= __('(full path to .pem file)', 'MailPress' );
$content .= '</td></tr>';

// Passphrase
$content .= '<tr><th><span>';
$content .= __( 'Passphrase', 'MailPress' );
$content .= '</span></th><td>';
$content .= __('sometimes required when generating the .pem files', 'MailPress' );
$content .= '</td></tr>';

// Encryption
$content .= '<tr><th><span>';
$content .=  __( 'Encryption certificate', 'MailPress' );
$content .= '</span></th><td>';
$content .= __('(optionnal full path to .pem file)', 'MailPress' );
$content .= '</td></tr>';


// Ressources
$content .= '<tr><th><span>';
$content .= __( 'Ressources', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'Many ressources available on the web, just to cite a few :', 'MailPress' );
$content .= '<ul><li><code><a href="https://www.openssl.org/" target="_blank">OpenSSL</a></code></li>';
$content .= '<li><code><a href="https://blog.didierstevens.com/2015/03/30/howto-make-your-own-cert-with-openssl-on-windows/" target="_blank">OpenSSL & Windows</a></code></li>';
$content .= '<li><code><a href="https://stackoverflow.com/questions/10175812/how-to-create-a-self-signed-certificate-with-openssl" target="_blank">openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -days 365</a></code></li>';
$content .= '</td></tr>';
$content .= '</table>';