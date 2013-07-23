<?php
namespace TimeKeywords;

use TimeKeywords\NowTimeKeyword;
use TimeKeywords\TodayTimeKeyword;
use TimeKeywords\TodayAtTimeKeyword;
use TimeKeywords\TomorrowTimeKeyword;

require_once 'source/TimeKeywords/NowTimeKeyword.php';
require_once 'source/TimeKeywords/TodayTimeKeyword.php';
require_once 'source/TimeKeywords/TodayAtTimeKeyword.php';
require_once 'source/TimeKeywords/TomorrowTimeKeyword.php';

/**
 * Manages all @link ITimeKeyword (you don't say ...).
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class TimeKeywordManager
{
	/**
	 * Filled with @link ITimeKeyword after first usage.
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
			array_push( self::$keywords, new NowTimeKeyword() );
			array_push( self::$keywords, new TomorrowTimeKeyword() );
			array_push( self::$keywords, new TodayTimeKeyword() );
			array_push( self::$keywords, new TodayAtTimeKeyword() );
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
}