<?php
class MP_Newsletter_processor_week_1 extends MP_newsletter_processor_
{
	public $id = 'week-1';

	function get_bounds() 
	{
		$time = $this->time;

		$format = 'Y-m-d ' . zeroise( $this->get_hour(), 2 ) . ':' . zeroise( $this->get_minute(), 2 ) . ':00';

		$first_wday = $this->get_wday();

		while ( wp_date( 'w', $time ) != $first_wday ) $time -= DAY_IN_SECONDS;

		$this->upper_bound = wp_date( $format, $time );

		if ( $this->upper_bound > $this->date )
		{
			$time -= WEEK_IN_SECONDS;
			$this->upper_bound = wp_date( $format, $time );
		}

		$this->lower_bound = wp_date( $format, $time - WEEK_IN_SECONDS );

		switch ( true )
		{
			case ( isset( $this->options['threshold'] ) ) :			// old old format
				$y = substr( $this->options['threshold'], 0, 4 );
				$w = substr( $this->options['threshold'], 4, 2 );
				$this->old_lower_bound = date( 'Y-m-d 00:00:00', strtotime( "{$y}W{$w}1" ) );
			break;
			case ( isset( $this->options['end_of_week'] ) ) : 		// old format
				$this->old_lower_bound = date( 'Y-m-d 00:00:00', $this->options['end_of_week'] + DAY_IN_SECONDS );
			break;
			default :
				$this->get_old_lower_bound();
			break;
		}
	}
}
new MP_Newsletter_processor_week_1( __( 'Previous week', 'MailPress' ) );