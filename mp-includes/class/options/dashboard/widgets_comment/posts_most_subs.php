<?php
class MP_Dashboard_posts_most_subs extends MP_WP_Dashboard_widget_
{
	var $id = 'mp_posts_most_subs';

	function widget()
	{
		global $wpdb, $wp_locale;

		$wgt_post = false;
		$posts = $wpdb->get_results( $wpdb->prepare( "SELECT count( * ) as count, id, post_title, guid, post_modified FROM $wpdb->posts a, $wpdb->postmeta b WHERE meta_key = %s AND id = post_id AND post_status = 'publish' GROUP BY id, post_title, guid ORDER BY 1;", MailPress_comment::meta_key ) );
		foreach( $posts as $post )
		{
			$wgt_post .= '<li>';
			$wgt_post .= '( ' . $post->count . ' ) <a class="rsswidget" title="" href="' . $post->guid . '">' . $post->post_title . '</a>';
			$wgt_post .= '<span class="rss-date">' . mysql2date( get_option( 'date_format' ), $post->post_modified ) . '</span>';
			$wgt_post .= '</li>' . "\r\n";
		}

		if ( $wgt_post )
		{

			echo '<div><ul>' . "\r\n" . $wgt_post . '</ul></div>' . "\r\n";
		}
	}
}
new MP_Dashboard_posts_most_subs( __( 'MailPress - Most subscribed', 'MailPress' ) );