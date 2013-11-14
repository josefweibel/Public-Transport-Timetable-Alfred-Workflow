<?php
namespace TimeKeywords;

use TimeKeywords\AHourMinuteTimeKeyword;

require_once 'src/TimeKeywords/AHourMinuteTimeKeyword.php';

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
abstract class AAtHourMinuteTimeKeyword extends AHourMinuteTimeKeyword
{
	private static $onKeyword = "am";

	/**
	 * [keyword] [hour]:[minute]
	 * @return string a valid pattern with your keyword.
	 */
	public function getPattern()
	{
		$this->checkKeyword();
		return "/.*( " . self::$onKeyword . ")?( " . $this->keyword . ")( " . $this->atKeyword . ")?" . self::getTimePattern() . "$/i";
	}

	/**
	 * Removes your keyword and the time from the query.
	 * @param string $query
	 * @return string the query without the keyword and the time.
	 */
	public function removeTimeKeyword( $query )
	{
		$this->checkKeyword();
		return preg_replace( "/( " . self::$onKeyword . ")?( " . $this->keyword . ")?" . ( strlen( $this->atKeyword ) > 0 ? "( " . $this->atKeyword .
				")?" : "" ) . self::getTimePattern() . "$/i" , "", $query );
	}
}