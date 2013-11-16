<?php
namespace TimeKeywords;

use TimeKeywords\AHourMinuteTimeKeyword;

require_once 'src/TimeKeywords/AHourMinuteTimeKeyword.php';

/**
 * Represents a time for the day after tomorrow.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class DayAfterTomorrowTimeKeyword extends AHourMinuteTimeKeyword
{
	/**
	 * Sets the keyword 'übermorgen'.
	 */
	public function __construct()
	{
		$this->keyword = "übermorgen";
	}

	/**
	 * @param string $query
	 * @return \DateTime the time in the query for the next day.
	 */
	public function getTime( $query )
	{
		return $this->getDateTimeFromQuery( $query, "2 days" );
	}
}

