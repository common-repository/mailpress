<?php
class MP_Newsletter_scheduler_week extends MP_newsletter_scheduler_
{
	public $id = 'week';

	function schedule( $newsletter ) 
	{
		$this->newsletter = $newsletter;

		$time = $this->time;

		$format = 'Y-m-d ' . zeroise( $this->get_hour(), 2 ) . ':' . zeroise( $this->get_minute(), 2 ) . ':00';

		$wdiff  = $this->get_wday() - $this->wday;
		if ( $wdiff < 0 ) $wdiff += 7;
		$time += $wdiff * DAY_IN_SECONDS; 

		$timestamp = wp_date( $format, $time );

		return $this->schedule_single_event( $timestamp );
	}
}
new MP_Newsletter_scheduler_week( __( 'Every week', 'MailPress' ) );