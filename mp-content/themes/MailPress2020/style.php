<?php /* MailPress2020 */

// colors

$col1 = '#e9b55f';
$col2 = '#da676c';
$col3 = '#f7e9ce';

$_000  = '#000';
$_fff  = '#fff';
$_333  = '#333';
$_555  = '#555';
$_777  = '#777';
$_999  = '#999';
$_f0   = '#f0f0f0';
$_8c   = '#8c8c8c';
$_de   = '#dedede';

// fonts

$fonta = '\'Helvetica Neue\', Arial, Helvetica, \'Nimbus Sans L\', sans-serif;';
$fontb = 'Georgia, \'Bitstream Charter\', serif;';

$_classes = array( 

'body'		=> "	color:{$_000};
			font-family: {$fonta};
			background:none repeat scroll 0 0 {$_f0};",

'button'	=> "	background-color:{$col1};
			border-color:{$col1};
			color:{$_f0};
			text-shadow:0 -1px 0 rgba( 0, 0, 0, 0.3 );
			font-size:13px;
			line-height:16px;
			margin:1px;
			font-family:{$fonta};

			border-radius: 5px;
			-moz-border-radius: 5px;
			-webkit-border-radius: 5px;
			-khtml-border-radius: 5px;",

'mail_link'	=> "  	margin:0 0 5px;",

'txtleft'	=> "	text-align:left;",

'a'		=> "	color:{$col1};
			text-decoration:underline;",

// header

'wrapper'	=> "	margin:0 auto;
			border:none;
			padding:0;
			width:700px;
			background:none repeat scroll 0 0 {$_fff};",

'globlink'	=> "	color: {$_999};
			font-family: {$fonta};
			font-weight:bold;",

'htable'	=> "	background:{$col1};
			width:100%;",

'htr'		=> "	height:60px;",

'logo'		=> "	border:none;
			padding-top: 4px;",

'htdate'	=> "	width:100%;
			padding:0;
			margin:0;
			height:40px;
			border-bottom:1px solid {$col2};
			background:{$col3};",

'hdate'		=> "	padding:0 10px 0 0;
			margin:0;border:none;
			background:{$col3};
			font-family:{$fonta};
			color:{$_555};
			font-size:16px;
			text-align:right;",

'main'		=> "	margin:0;
			padding:20px 10px;
			border:0;
			text-align:left;
			width:100%",

'content'	=> "	margin:0;
			padding:0;
			border:0;
			width:70%;
			float:left;",
// content

'ctable'	=> "	width:100%;
			padding-right:20px;",

'ctd'		=> "	margin:0;
			padding:0;
			border:none;
			color:{$_333};
			text-align:left;
			font-family:{$fonta}",

'cdiv'		=> "	margin:0pt 0pt 10px;
			padding:0;
			border:none;
			text-align:justify;",

'ch2'		=> "	margin:0;
			padding:0;
			border:none;
			color:{$_333};
			font-size:1.4em;
			font-weight:bold;
			font-family:{$fonta}",

'cdate'		=> "	line-height:2em;
			color:{$_777};
			font-size:0.7em;
			font-family:{$fonta}",

'cp'		=> "	line-height:1.4em;
			font-size:0.85em;",

'clink'		=> "	text-decoration:none;
			color:{$_333};",

// sidebar

'sidediv'	=> "	margin:0;
			padding:0;
			border:0;
			width:29%;
			float:left;",

'sidetable'	=> "	background-color: {$_fff} !important;
			padding:10px;
			color:{$col1};
			border: 2px solid {$col1};
			margin-bottom: 8px;
			width:100%;",

'sidelink'	=> "  	font-family:{$fonta};
			font-size:11px;
			font-weight:bold;
			color:{$col1};
			text-decoration:none;",

'sideul'	=> "  	padding-left:15px;",
// footer

'ftable'	=> "	margin:0;
			padding:10px 20px;
			border-top:1px solid {$_de};
			width:100%;",

'fltd'		=> "	font-family:{$fonta}
			color:{$_de};
			font-size:10px;",

'frtd'		=> "	font-family:{$fonta};
			color:{$_8c};
			font-size:14px;text-align:right;",
 );