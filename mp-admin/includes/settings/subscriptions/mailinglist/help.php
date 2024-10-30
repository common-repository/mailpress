<?php // mailinglist

$content .= '<table>';

$content .= '<tr><th>';
$content .= __( 'Mailing lists', 'MailPress' );
$content .= '</th><td>';
$content .= __( 'to send a mailing for each mailpress user subscribed to a mailing list.', 'MailPress' );
$content .= '</td></tr>';

// default mailinglist
$content .= '<tr><th><span>';
$content .= __( 'Default', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'Select the default mailing list for each new mailpress user.', 'MailPress' );
$content .= '</td></tr>';

// opened to public
$content .= '<tr><th><span>';
$content .= __( 'Public', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'Select the mailing list(s) opened for public subscription (other mailing lists for internal use).', 'MailPress' );
$content .= '<br />';
$content .= __( 'Different from default mailing list to be selected on General settings tab.', 'MailPress' );
$content .= '</td></tr>';

$content .= '</table>';

$content .= '<hr />';