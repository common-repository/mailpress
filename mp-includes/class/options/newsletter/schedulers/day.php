<?php
class MP_Newsletter_scheduler_day extends MP_newsletter_scheduler_
{
	public $id = 'day';

	function schedule( $newsletter ) 
	{ 
		$this->newsletter = $newsletter;

		$y = $this->year;
		$m = $this->month;
		$d = $this->day;

		$this->get_slot_context();  /* 1 slot is 24 hours is the default */

		if ( $this->slot_overnight )
		{
			$d--;
			if ( !$d )
			{
				$m--;
				if ( $m == 0 ) { $y--; $m = 12; }
				$d = $this->get_last_day( $y, $m );			
			}
		}

		return $this->schedule_single_event( $this->format_date( $y, $m, $d ) . ' ' . $this->slots[$this->slot] );
	}
}
new MP_Newsletter_scheduler_day( __( 'Every day', 'MailPress' ) );