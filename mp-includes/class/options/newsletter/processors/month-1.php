<?php
class MP_Newsletter_processor_month_1 extends MP_newsletter_processor_
{
	public $id = 'month-1';

	function get_bounds() 
	{
		$y = $this->year;
		$m = $this->month;
		$d = $this->get_day( $y, $m );

		$h = $this->get_hour();
		$i = $this->get_minute();

		if ( $this->day < $d ) $m--;
		if ( !$m ) {$m = 12; $y--;}
		$d = $this->get_day( $y, $m );

		$this->upper_bound = $this->format_timestamp( $y, $m, $d, $h, $i, 0 );

		if ( $this->upper_bound > $this->date )
		{
			$m--; 
			if ( !$m ) {$m = 12; $y--;}
			$d = $this->get_day( $y, $m );

			$this->upper_bound = $this->format_timestamp( $y, $m, $d, $h, $i, 0 );
		}

		$m--; 
		if ( !$m ) {$m = 12; $y--;}
		$d = $this->get_day( $y, $m );

		$this->lower_bound = $this->format_timestamp( $y, $m, $d, $h, $i, 0 );

		switch ( true )
		{
			case ( isset( $this->options['threshold'] ) ) :			// old format
				$y = substr( $this->options['threshold'], 0, 4 );
				$m = substr( $this->options['threshold'], 4, 2 );
				$this->old_lower_bound =  "{$y}-{$m}-01 00:00:00";
			break;
			default :
				$this->get_old_lower_bound();
			break;
		}
	}
}
new MP_Newsletter_processor_month_1( __( 'Previous month', 'MailPress' ) );