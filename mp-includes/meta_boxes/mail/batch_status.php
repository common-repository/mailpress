<?php

$out = '';
$out .= '<table class="batch_send">' . "\r\n";
$out .= '<tr><td colspan="4" class="batch_sendc">' . sprintf( __( 'Batch status : %1$s', 'MailPress' ), $mail->status ) . '</td></tr>' . "\r\n";

if ( is_array( $mailmeta ) ) 
{
	$ths = array( __( 'Total recipients', 'MailPress' ), __( 'Sent', 'MailPress' ), __( 'Try/Pass', 'MailPress' ), __( 'Processed', 'MailPress' ), );
	$tds = array( $mailmeta['count'], $mailmeta['sent'], $mailmeta['try'] . '/' . $mailmeta['pass'], $mailmeta['processed'] );

	$out .= '<tr><td colspan="4">&#160;</td></tr>' . "\r\n";
	$out .= '<tr>';
	foreach( $ths as $th ) $out .= '<th>' . $th . '</th>';
	$out .= '</tr>' . "\r\n";
	$out .= '<tr>';
	foreach( $tds as $td ) $out .= '<td class="batch_send">' . $td . '</td>';
	$out .= '</tr>' . "\r\n";
	if ( !empty( $mailmeta['failed'] ) )
	{
		$out .= '<tr><td colspan="4">&#160;</td></tr>' . "\r\n";
		$out .= '<tr>';
		$out .= '<td>' . sprintf( __( 'Pending (%1$s)', 'MailPress' ), count( $mailmeta['failed'] ) ) . '</td>';
		$out .= '<td colspan="3"><select>' . MP_AdminPage::select_option( array_keys( $mailmeta['failed'] ), '', false ) . '</select></td>';
		$out .= '</tr>' . "\r\n";	
	}
}

$out .= '</table>' . "\r\n";

echo $out;