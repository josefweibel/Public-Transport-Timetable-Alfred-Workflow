<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use TimeKeywords\NowTimeKeyword;
use TimeKeywords\TodayTimeKeyword;
use TimeKeywords\AtTimeKeyword;
use TimeKeywords\TomorrowTimeKeyword;
use TimeKeywords\AtDayTimeKeyword;
use TimeKeywords\WeekdayTimeKeyword;
use TimeKeywords\DayAfterTomorrowTimeKeyword;

/**
 * Manages all @link ITimeKeyword (you don't say ...).
 * Dispatchers have to use this class to get the time of their query.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class TimeKeywordManager
{
	/**
	 * Filled with @link ITimeKeyword before first usage.
	 * @var array with @link ITimeKeyword.
	 */
	private static $keywords = null;

	/**
	 * Initializes the $keywords property if not alredy done.
	 * @return array with @link ITimeKeyword.
	 */
	public static function getTimeKeywords()
	{
		if( !self::$keywords )
		{
			self::$keywords = array();
			self::$keywords[] = new NowTimeKeyword();
			self::$keywords[] = new TomorrowTimeKeyword();
			self::$keywords[] = new TodayTimeKeyword();
			self::$keywords[] = new AtTimeKeyword();
			self::$keywords[] = new AtDayTimeKeyword();
			self::$keywords[] = new DayAfterTomorrowTimeKeyword();

			foreach( WeekdayTimeKeyword::$weekdays as $index => $weekday )
			{
				self::$keywords[] = new WeekdayTimeKeyword( $weekday );
			}
		}

		return self::$keywords;
	}

	/**
	 * Searches in all known @link ITimeKeyword and returns the first which matches
	 * with the query. If no @link ITimeKeyword matches than it will return null.
	 * @param String $query with or without a TimeKeyword.
	 * @return @link ITimeKeyword or null.
	 */
	public static function getTimeKeyword( $query )
	{
		$keywords = self::getTimeKeywords();

		foreach( $keywords as $keyword )
		{
			if( preg_match( $keyword->getPattern(), $query ) )
			{
				return $keyword;
			}
		}

		return null;
	}

	/**
	 * @return the keyword which can be appended to the query.
	 */
	public static function getDefaultTimeKeyword()
	{
		$keyword = new NowTimeKeyword();
		return $keyword->getKeyword();
	}
}