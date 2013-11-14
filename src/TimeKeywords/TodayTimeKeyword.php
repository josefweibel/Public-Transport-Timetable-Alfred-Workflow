<?php
namespace TimeKeywords;

use TimeKeywords\AHourMinuteTimeKeyword;

require_once 'src/TimeKeywords/AHourMinuteTimeKeyword.php';

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
		return $this->getDateTimeFromQuery( $query, "now" );
	}
}