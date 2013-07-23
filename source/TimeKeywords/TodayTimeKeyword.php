<?php
namespace TimeKeywords;

use DateTime;
use DateTimeZone;
use TimeKeywords\AHourMinuteTimeKeyword;

require_once 'source/TimeKeywords/AHourMinuteTimeKeyword.php';

/**
 * Represents a time for the current day.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class TodayTimeKeyword extends AHourMinuteTimeKeyword
{
	/**
	 * Sets the keyword 'heute'.
	 */
	public function __construct()
	{
		$this->keyword = "heute";
	}

	/**
	 * @param string $query
	 * @return \DateTime with the time in the query for today.
	 */
	public function getTime( $query )
	{
		$timezone = new DateTimeZone( "Europe/Zurich" );
		
		$results = array();
		preg_match( "/ ([01]?[0-9]|2[0-3]):([0-5][0-9])$/i", $query, $results );

		$date = new DateTime( "now", $timezone );
		$date->setTime( intval( $results[1] ), intval( $results[2] ), 0 );
		return $date;
	}
}