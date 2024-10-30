<?php
class MP_Newsletter_processor_day_1 extends MP_newsletter_processor_
{
	public $id = 'day-1';

	function get_bounds() 
	{
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
		$this->upper_bound = $this->format_date( $y, $m, $d ) . ' ' . $this->slots[$this->slot];

		if ( $this->slots[$this->slot] <=  $this->slots[$this->slot - 1] )
		{
			$d--;
			if ( !$d )
			{
				$m--;
				if ( $m == 0 ) { $y--; $m = 12; }
				$d = $this->get_last_day( $y, $m );			
			}
		}
		$this->lower_bound = $this->format_date( $y, $m, $d ) . ' ' . $this->slots[$this->slot - 1];



		switch ( true )
		{
			case ( isset( $this->options['threshold'] ) ) :			// old format
				$y = substr( $this->options['threshold'], 0, 4 );
				$m = substr( $this->options['threshold'], 4, 2 );
				$j = substr( $this->options['threshold'], 6, 2 );
				$this->old_lower_bound = "{$y}-{$m}-{$j} 00:00:00";
			break;
			default :
				$this->get_old_lower_bound();
			break;
		}
	}
}
new MP_Newsletter_processor_day_1( __( 'Previous day', 'MailPress' ) );