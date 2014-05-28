<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use TimeKeywords\AAtHourMinuteTimeKeyword;

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class WeekdayTimeKeyword extends AAtHourMinuteTimeKeyword
{
	public static $weekdays = array( "Montag" => "Monday",
									 "Dienstag" => "Tuesday",
									 "Mittwoch" => "Wednesday",
									 "Donnerstag" => "Thursday",
									 "Freitag" => "Friday",
									 "Samstag" => "Saturday",
									 "Sonntag" => "Sunday" );

	public $keyword = null;

	public function __construct( $weekday )
	{
		$this->keyword = $weekday;
	}

	public function getTime( $query )
	{
		return $this->getDateTimeFromQuery( $query, self::$weekdays[$this->keyword] );
	}
}