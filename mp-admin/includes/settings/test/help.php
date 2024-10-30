<?php // test

$content = '';
// Test

$content .= '<p>';

$content .= __( 'Do not rush to the &#8220;Test&#8221; button.', 'MailPress' );
$content .= '<br />';
$content .= __( 'Make sure you properly filled all settings on two previous tabs.', 'MailPress' );
$content .= '<br />';
$content .= '<br />';
$content .= sprintf( __( 'If any problem, see log with the following name <code>MP_Log_%1$s_mp_mail_.(date).txt</code>.', 'MailPress' ), get_current_blog_id() );
$content .= '<br />';
$content .= __( 'It can be browsed :', 'MailPress' );

$content .= '</p>';

$content .= '<ul><li>';
if (MP_addons::is_active('MailPress_view_logs'))
{
$content .= sprintf( __('In %1$s.', 'MailPress'), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', MailPress_view_logs, __( 'MailPress Logs admin screen', 'MailPress' ) ) );
}
else
{
$content .= sprintf( __('Activate add-on %1$s, so logs can be seen in %2$s.', 'MailPress'), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', MailPress_addons . '#MailPress_view_logs', __( 'View_logs', 'MailPress' ) ), __( 'MailPress Logs admin screen', 'MailPress' ) );
}
$content .= '</li><li>';
$content .= sprintf( __( 'Otherwise, since MailPress 7.0, in %s ', 'MailPress' ), '"<code>' . MP_UPL_PATH . 'log</code>"'  );
$content .= '</li></ul>';
        

