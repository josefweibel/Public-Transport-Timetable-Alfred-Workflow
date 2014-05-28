<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

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
		return $this->getDateTimeFromQuery( $query, "tomorrow" );
	}
}

