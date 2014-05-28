<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use BadFunctionCallException;
use DateTime;
use DateTimeZone;
use TimeKeywords\ITimeKeyword;

/**
 * An abstract class which can used for TimeKeywords with the following pattern: [keyword] [hour]:[minute]
 * In your implementation you have to set the $keyword and implement the @link ITimeKeyword#getTime( $query ).
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
abstract class AHourMinuteTimeKeyword implements ITimeKeyword
{
	/**
	 * The keyword which identifies your TimeKeyword. It will be used for the patterns.
	 * You have to initalize this variable first of all. For example in your constructor.
	 * @var string
	 */
	protected $keyword = null;

	protected $atKeyword = "um";

	protected static function getTimePattern()
	{
		return " ([01]?[0-9]|2[0-3]):([0-5][0-9])";
	}

	/**
	 * [keyword] [hour]:[minute]
	 * @return string a valid pattern with your keyword.
	 */
	public function getPattern()
	{
		$this->checkKeyword();
		return "/.*( " . $this->keyword . ")( " . $this->atKeyword . ")?" . self::getTimePattern() . "$/i";
	}

	/**
	 * Removes your keyword and the time from the query.
	 * @param string $query
	 * @return string the query without the keyword and the time.
	 */
	public function removeTimeKeyword( $query )
	{
		$this->checkKeyword();
		return preg_replace( "/( " . $this->keyword . ")?" . ( strlen( $this->atKeyword ) > 0 ? "( " . $this->atKeyword .
				")?" : "" ) . self::getTimePattern() . "$/i" , "", $query );
	}

	protected function getDateTimeFromQuery( $query, $date )
	{
		$datetime = new DateTime( $date, new DateTimeZone( "Europe/Zurich" ) );

		$results = array();
		preg_match( "/" . self::getTimePattern() . "$/i", $query, $results );

		$datetime->setTime( intval( $results[1] ), intval( $results[2] ), 0 );
		return $datetime;
	}

	protected function checkKeyword()
	{
		if( !$this->keyword )
		{
			throw new BadFunctionCallException( "You have to define the class variable " .
				"'keyword' in your AHourMinuteTimeKeyword implementation." );
		}
	}
}