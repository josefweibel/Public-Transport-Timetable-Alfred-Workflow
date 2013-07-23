<?php
namespace TimeKeywords;

use DateTime;
use DateTimeZone;
use TimeKeywords\AHourMinuteTimeKeyword;

/**
 * Represents a time for the next day.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class TomorrowTimeKeyword extends AHourMinuteTimeKeyword
{
	/**
	 * Sets the keyword 'morgen'.
	 */
	public function __construct()
	{
		$this->keyword = "morgen";
	}
	
	/**
	 * @param string $query
	 * @return \DateTime the time in the query for the next day.
	 */
	public function getTime( $query )
	{
		$timezone = new DateTimeZone( "Europe/Zurich" );
		
		$results = array();
		preg_match( "/ ([01]?[0-9]|2[0-3]):([0-5][0-9])$/i", $query, $results );

		$date = new DateTime( "tomorrow", $timezone );
		$date->setTime( intval( $results[1] ), intval( $results[2] ), 0 );
		return $date;
	}
}

