<?php // batch_spool_send

if ( !( function_exists( 'current_user_can' ) && current_user_can( MP_AdminPage::capability ) ) ) die( 'Access denied' );

$spool_path = 'spool/' . get_current_blog_id() . '/';

$content .= '<table>';

$content .= '<tr><th>';
$content .= __( 'Sending from spool', 'MailPress' );
$content .= '</th><td>';
$content .= __('All mails are generated in one pass and stored in a spool folder.', 'MailPress' );
$content .= ' ' . __('Then your mailing will be sent in the background by several batches.', 'MailPress' );
$content .= '</td></tr>';

// Spool Path
$content .= '<tr><th><span>';
$content .= __( 'Spool Path', 'MailPress' );
$content .= '</span></th><td>';
$content .= sprintf( __( 'Since MailPress 7.0, spool path is %s ', 'MailPress' ), '"<code>' . MP_UPL_PATH . $spool_path . '</code>"'  ); 
$content .= '</td></tr>';

// Max mails sent per batch
$content .= '<tr><th><span>';
$content .= __( 'Max mails...', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'For each batch, MailPress will try to send the generated mails for each x recipient(s).', 'MailPress' );
$content .= '</td></tr>';

// Time limit in seconds
$content .= '<tr><th><span>';
$content .= __( 'Time limit...', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'For each batch, MailPress will be limited in time.', 'MailPress' );
$content .= '</td></tr>';

// Max retries
$content .= '<tr><th><span>';
$content .= __( 'Max retries', 'MailPress' );
$content .= '</span></th><td>';
$content .= __( 'Once all recipients have been processed, retry to send x times if some failures (1 recommended).', 'MailPress' );
$content .= '</td></tr>';

$content .= '</table>';