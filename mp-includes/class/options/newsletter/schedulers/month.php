<?php
class MP_Newsletter_scheduler_month extends MP_newsletter_scheduler_
{
	public $id = 'month';

	function schedule( $newsletter ) 
	{
		$this->newsletter = $newsletter;

		$y = $this->year;
		$m = $this->month;
		$d = $this->get_day( $y, $m );

		$h = $this->get_hour();
		$i = $this->get_minute();

		if ( $this->day > $d )
		{
			$m++;
			if ( $m > 12 ) { $m = 1; $y++; }
			$d = $this->get_day( $y, $m );
		}

		$timestamp = $this->format_timestamp( $y, $m, $d, $h, $i, 0 );

		return $this->schedule_single_event( $timestamp );
	}
}
new MP_Newsletter_scheduler_month( __( 'Every month', 'MailPress' ) );
