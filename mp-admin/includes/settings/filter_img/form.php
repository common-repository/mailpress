<?php // filter_img

if ( !isset( $filter_img ) )
{
	$filter_img = get_option( MailPress_filter_img::option_name );
}

$filter_img['img'] = str_replace( '<', '&lt;', $filter_img['img'] );
$filter_img['img'] = str_replace( '>', '&gt;', $filter_img['img'] );
if ( !isset( $filter_img['align'] ) ) $filter_img['align'] = 'none';
if ( !isset( $filter_img['extra_style'] ) ) $filter_img['extra_style'] = '';

?>
<form name="<?php echo basename(__DIR__); ?>" method="post" class="mp_settings">
	<input type="hidden" name="_tab" value="<?php echo basename(__DIR__); ?>" />
	<table class="form-table">

<!-- alignment -->
		<tr>
			<th><?php _e( '&lt;img&gt; Defaults', 'MailPress' ); ?></th>
			<td class="field">
				<table>
					<tr>
						<td class="nobd"><?php _e( 'Alignment' ); ?></td>
						<td class="nobd">
							<input type="radio" value="none"   name="filter_img[align]"  id="align-none"<?php checked( 'none', $filter_img['align'] ); ?> />
							<label for="align-none" class="align image-align-none-label"><?php _e( 'None' ); ?></label>
							<input type="radio" value="left"   name="filter_img[align]"  id="align-left"<?php checked( 'left', $filter_img['align'] ); ?> />
							<label for="align-left" class="align image-align-left-label"><?php _e( 'Left' ); ?></label>
							<input type="radio" value="center" name="filter_img[align]"  id="align-center"<?php checked( 'center', $filter_img['align'] ); ?> />
							<label for="align-center" class="align image-align-center-label"><?php _e( 'Center' ); ?></label>
							<input type="radio" value="right"  name="filter_img[align]"  id="align-right"<?php checked( 'right', $filter_img['align'] ); ?> />
							<label for="align-right" class="align image-align-right-label"><?php _e( 'Right' ); ?></label>
						</td>
					</tr>
					<tr>
						<td class="nobd"><?php _e( 'style=', 'MailPress' ); ?></td>
						<td class="nobd">
							<textarea name="filter_img[extra_style]" class="courier" rows="2" cols="61"><?php echo htmlspecialchars( stripslashes( $filter_img['extra_style'] ),ENT_QUOTES );?></textarea>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<!-- sample -->
		<tr>
			<th><label for="filter_img_img"><?php _e( 'Enter Full &lt;img&gt; Html Tag', 'MailPress' ); ?></label></th>
			<td>
				<textarea name="filter_img[img]" class="courier" rows="2" cols="72" id="filter_img_img"><?php echo esc_attr( $filter_img['img'] ); ?></textarea>
				<br /><?php _e( 'this text is not controlled or sanitize, it is just for test purpose', 'MailPress' ); ?>
			</td>
		</tr>
<?php 
if ( !empty( $filter_img['img'] ) )
{
?>
<!-- result -->
		<tr>
			<th><?php _e( 'Filter Result', 'MailPress' ); ?></th>
			<td class="courier">
				<div class="filter-img bkgndc bd1sc">
<?php 
	$x = $filter_img['img'];
	$x = stripslashes( $x );
	$x = htmlspecialchars_decode( $x );
	$x = MailPress_filter_img::img_mail( $x );
	$x = str_ireplace( '<!-- MailPress_filter_img start -->','',$x );
	$x = str_ireplace( '<!-- MailPress_filter_img end -->','',$x );
	$x = htmlspecialchars( $x,ENT_QUOTES );
	echo $x;
?>
				</div>
			</td>
		</tr>
<?php } ?>
<!-- keep url -->
		<tr>
			<th><label for="filter_img_keepurl"><?php _e( 'Keep Url', 'MailPress' ); ?></label></th>
			<td class="field">
				<input type="checkbox" value="on" name="filter_img[keepurl]" id="filter_img_keepurl"<?php if ( isset( $filter_img['keepurl'] ) ) checked( 'on', $filter_img['keepurl'] ); ?> />
				<?php printf( __( 'NO mail attachments with site images when full url (<i>&lt;img src="<b>%1$s/...</b>"</i>) is provided.', 'MailPress' ), site_url() ); ?>
			</td>
		</tr>

	</table>

<?php MP_AdminPage::save_button(); ?>

</form>