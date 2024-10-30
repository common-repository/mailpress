<?php
class MP_import_excel extends MP_import_importer_
{
	var $id = 'excel';

	function dispatch() 
	{
		$step = $this->get_step();

		$this->header();
		switch ( $step ) 
		{
			case 0 :
				$this->greet();
			break;
			case 1 :
				$this->start_trace( $step );
				if ( $this->handle_upload() )
				{
					$this->message_report( " ANALYSIS   !" );
					$sniff = $this->sniff( $step );

					if ( $sniff )
					{
						if ( $sniff == 1 )
						{
							$this->message_report( "** INFO  ** ! Only one sheet in excel spreadsheet " );
							$this->end_trace( true );
							$this->fileform( $this->file_id, 0 );
						}
						else
						{
							$this->message_report( " SHEET FORM ! There are $sniff sheets detected " );
							$this->end_trace( true );			
							$this->sheetform();
						}
					}
					else
					{
						$this->end_trace( true );	
						$this->error( '<p><strong>' . __( 'Unable to determine sheet(s)', 'MailPress' ) . '</strong></p>' );
					}

				}
				else
				{
					$this->message_report( "** ERROR ** ! Could not upload the file" );
					$this->end_trace( false );
				}

			break;
			case 2 :
				$this->fileform( MP_AdminPage::$get_['id'], MP_AdminPage::$get_['sid'] );
			break;
			case 3:
				$this->start_trace( $step );
				$import = $this->import( MP_AdminPage::$get_['id'], MP_AdminPage::$get_['sid'] );
				$this->end_trace( true );
				if ( $import )
					$this->success( '<p>' . sprintf( __( "<b>Number of records</b> : <i>%s</i>", 'MailPress' ), $import ) . '</strong></p>' );
				else 
					$this->error( '<p><strong>' . $this->file . '</strong></p>' );
			break;
		}
		$this->footer();
	}

// step 0

// step 1

	function sniff( $step, $first = true )
	{
		$this->message_report( " sniff $step    ! >>> " . $this->file );

		require_once 'parseexcel/parseexcel.lib.php';
		$this->excel = new Spreadsheet_Excel_Reader( $this->file );

		$this->hasheader = true;

		return ( $first ) ? count( $this->excel->boundsheets ) : true;
	}

	function sheetform() 
	{
		if ( !isset( $this->excel ) )
		{
			$this->file_id = ( int ) $id;
			$this->file    = get_attached_file( $this->file_id );
			if ( !file_exists( $this->file ) ) { $this->message_report( "File not found " . $this->file ); return false; }

			$this->sniff( MP_AdminPage::$get_['step'], false );
		}
?>
	<h3><?php _e( 'File scan', 'MailPress' ); ?></h3>
	<p><?php _e( "Here are the sheets found in the excel file, please select one", 'MailPress' ); ?></p>
	<div>
<?php
		foreach ( $this->excel->boundsheets as $sid => $sheet )
		{
?>
	<form action="<?php echo MailPress_import; ?>&amp;mp_import=excel&amp;step=2&amp;sid=<?php echo $sid; ?>&amp;id=<?php echo $this->file_id; ?>" method="post" style="float:left;">
		<input type="submit" class="button-primary" value="<?php echo attribute_escape( $sheet['name'] ); ?>" />&nbsp;&nbsp;
	</form>
<?php
		}
?>
	</div>
<?php
	}

	function find_email( $sid )
	{
		$this->message_report( " FIND EMAIL !" );

		if ( !isset( $this->excel ) )
		{
			$this->file_id = ( int ) $id;
			$this->file    = get_attached_file( $this->file_id );
			if ( !file_exists( $this->file ) ) { $this->message_report( "File not found " . $this->file ); return false; }

			$this->sniff( MP_AdminPage::$get_['step'], false );
		}

		$email = array();
		$i = 0;

		foreach ( $this->excel->sheets[$sid]['cells'] as $r => $row )
		{
			foreach ( $row as $c => $v )	if ( MailPress::is_email( $v ) ) if ( isset( $email[$c] ) ) $email[$c]++; else $email[$c] = 1;

			$i++;
			if ( $i > 9 ) break;
		}

		if ( 0 == count( $email ) )
		{
			$this->message_report( ' **WARNING* ! Unable to determine email location' );
			return false;
		}

		asort( $email );
		$x = array_flip( $email );
		$this->emailcol = end( $x );
		
		$this->message_report( ' email      ! ' . sprintf( 'Email probably in column %s', $this->emailcol ) );

		return $this->emailcol;
	}
        
// step 2
        
	function fileform( $id, $sid ) 
	{
		$mailinglist_ok = ( class_exists( 'MailPress_mailinglist' ) && current_user_can( 'MailPress_manage_mailinglists' ) );
		$newsletter_ok  = ( class_exists( 'MailPress_newsletter' ) );

		$this->start_trace( MP_AdminPage::$get_['step'] );
		$this->message_report( " ANALYSIS   ! for sheet #{$sid}" );

		if ( !isset( $this->excel ) )
		{
			$this->file_id = ( int ) $id;
			$this->file    = get_attached_file( $this->file_id );
			if ( !file_exists( $this->file ) ) { $this->message_report( "File not found " . $this->file ); return false; }

			$this->sniff( MP_AdminPage::$get_['step'], false );
		}

		$this->emailcol = MP_AdminPage::$get_['col'] ?? $this->find_email( $sid );
		$this->end_trace( true );

		if( !empty( $this->excel ) )
		{
			if ( $mailinglist_ok ) add_action( 'admin_print_footer_scripts', array( __CLASS__, 'footer_scripts' ), 1 );

			$columns = array_shift( $this->excel->sheets[$sid]['cells'] );


?>
	<form id="mp_import" action="<?php echo MailPress_import; ?>&amp;mp_import=excel&amp;step=3&amp;id=<?php echo $this->file_id; ?>&amp;sid=<?php echo $sid; ?>" method="post">
<?php 		if ( $mailinglist_ok ) : ?>
		<h3><?php _e( 'Mailing list', 'MailPress' ); ?></h3>
		<p><?php _e( 'Optional, you can import the MailPress users in a specific mailing list ...', 'MailPress' ); ?></p>
<?php			MP_Mailinglist::dropdown( array( 'htmlname' => 'mailinglist', 'htmlid' => 'mailinglist', 'selected' => get_option( MailPress_mailinglist::option_name_default ), 'hierarchical' => true, 'orderby' => 'name', 'hide_empty' => '0', 'show_option_none' => __( 'Choose mailinglist', 'MailPress' ) ) ); ?><?php endif; ?>
<?php 		if ( $newsletter_ok ) : ?>
		<h3><?php _e( 'Newsletter', 'MailPress' ); ?></h3>
		<p>
			<input type="checkbox" name="no_newsletter" id="no_newsletter" />
			<?php _e( '<b>Delete</b> all subscriptions.', 'MailPress' ); ?>
		</p>
		<p>
			<input type="checkbox" name="newsletter" id="newsletter" /> 
			<?php _e( '<b>Add</b> default subscriptions.', 'MailPress' ); ?>
		</p>
<?php 		endif; ?>
		<h3><?php _e( 'File scan', 'MailPress' ); ?></h3>
		<p>
<?php 
			printf( __( "On the first records (see hereunder), the file scan found that the email is in column '<strong>%s</strong>'.", 'MailPress' ), $columns[$this->emailcol] );
			echo '&nbsp;';
			_e( 'However, you can select another column.<br /> Invalid emails will not be inserted.', 'MailPress' ); 
?>
		</p>
		<table class="wp-list-table widefat fixed striped zyxw">
			<thead>
				<tr>
					<td style="width:auto;"><?php _e( 'Choose email column', 'MailPress' ); ?></td>
<?php
			$i = 1;
			foreach ( $columns as $k => $v )
			{
				while ( $i < $k ) { echo '<td></td>'; $i++; }
?>
					<td><input type="radio" name="is_email" value="<?php echo $k; ?>"<?php checked( $k, $this->emailcol ); ?> /><span><?php echo $v; ?></span></td>
<?php
				$i++;
			}
?>
				</tr>
				<tr>
					<td><?php _e( 'Choose name column', 'MailPress' ); ?></td>
<?php
			$i = 1;
			foreach ( $columns as $k => $v )
			{
				while ( $i < $k ) { echo '<td></td>'; $i++; }
?>
					<td><input type="radio" name="is_name" value="<?php echo $k; ?>" /><span><?php echo $v; ?></span></td>
<?php
				$i++;
			}
?>
				</tr>
			</thead>
			<tbody>
<?php

			$i = 0;
			foreach ( $this->excel->sheets[$sid]['cells'] as $r => $row )
			{
?>
				<tr>
					<td></td>
<?php
				$j = 1;
				foreach ( $row as $k => $v )
				{
					while ( $j < $k ) { echo '<td></td>'; $j++; }
?>
					<td><span <?php if ( $k == $this->emailcol ) if ( !MailPress::is_email( $v ) ) echo 'style="background-color:#fdd;"'; else echo 'style="background-color:#dfd;"';?>><?php echo $v; ?></span></td>
<?php
					$j++;
				}
?>
				</tr>
<?php
				$i++;
				if ( $i > 9 ) break;
			}
?>
			</tbody>
		</table>
		<p class="submit">
			<input class="button-primary" type="submit" value="<?php echo attribute_escape( __( 'Submit' ) ); ?>" />
		</p>
	</form>
<?php
		}
	}

	public static function footer_scripts() 
	{
		wp_register_script( 'mp-import', '/' . MP_PATH . 'mp-includes/js/mp_mailinglist_dropdown.js', array( 'jquery' ), false, 1 );
		wp_localize_script( 'mp-import', 	'mp_ml_select_L10n', array( 
			'error' => __( 'Please, choose a mailinglist', 'MailPress' ), 
			'select' => 'mailinglist', 
			'form'   => 'mp_import',
			'l10n_print_after' => 'try{convertEntities( mp_ml_select_L10n );}catch( e ){};' 
		 ) );

		wp_enqueue_script( 'mp-import' );
	}

// step 3

	function import( $id, $sid ) 
	{
		$this->message_report( " IMPORT     ! for sheet #{$sid}" );

		if ( !isset( $this->excel ) )
		{
			$this->file_id = ( int ) $id;
			$this->file    = get_attached_file( $this->file_id );
			if ( !file_exists( $this->file ) ) { $this->message_report( "File not found " . $this->file ); return false; }

			$this->sniff( MP_AdminPage::$get_['step'], false );
		}

		$this->emailcol = MP_AdminPage::$pst_['is_email'];
		$this->namecol  = MP_AdminPage::$pst_['is_name'] ?? null;

		$mailinglist_ok = ( class_exists( 'MailPress_mailinglist' ) && ( '-1' != MP_AdminPage::$pst_['mailinglist'] ) );
		if ( $mailinglist_ok )
		{
			$this->mailinglist_ID = MP_AdminPage::$pst_['mailinglist'];
			add_filter( 'MailPress_mailinglist_default', array( $this, 'mailinglist_default' ), 8, 1 );

			$mailinglist_name = MP_Mailinglist::get_name( $this->mailinglist_ID );
		}

		$newsletter_ok 	  = ( class_exists( 'MailPress_newsletter' ) && isset( MP_AdminPage::$pst_['newsletter'] ) );
		$no_newsletter_ok = ( class_exists( 'MailPress_newsletter' ) && isset( MP_AdminPage::$pst_['no_newsletter'] ) );

		$i = 0;
		$columns = array_shift( $this->excel->sheets[$sid]['cells'] );
		foreach ( $this->excel->sheets[$sid]['cells'] as $r => $row )
		{
			if ( !isset( $row[$this->emailcol] ) ) {$this->message_report( "Email column " . $this->emailcol . " not found in file " . $this->file . " for sheet #" . $sid . " in row #" . $r ); continue;}

			$i++;

			$curremail = trim( strtolower( $row[$this->emailcol] ) );
			$currname  = ( isset( $this->namecol ) ) ? trim( $row[$this->namecol] ) : '';
			$mp_user_id = $this->sync_mp_user( $curremail, $currname );

			if ( $mp_user_id )
			{
				if ( $mailinglist_ok )
				{
					$this->sync_mp_user_mailinglist( $mp_user_id, $this->mailinglist_ID, $curremail, $mailinglist_name );
				}
				if ( $no_newsletter_ok )
				{
					$this->sync_mp_user_no_newsletter( $mp_user_id );
				}
				if ( $newsletter_ok )
				{
					$this->sync_mp_user_newsletter( $mp_user_id );
				}

				foreach ( $row as $k => $v )
				{
					if ( $k == $this->emailcol ) continue;
					if ( isset( $this->namecol ) && ( $k == $this->namecol ) ) continue;

					$this->sync_mp_usermeta( $mp_user_id, $columns[$k], $v );
				}
			}
		}
		return $i;
	}

	function mailinglist_default( $default )
	{
		return $this->mailinglist_ID ?? $default;
	}
}
new MP_import_excel( __( 'Import your <strong>excel</strong> file.', 'MailPress' ), __( 'Import Excel file', 'MailPress' ) );