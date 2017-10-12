<?php
namespace Utils;

include_once( "src/Initializer.php" );

use \DateTime;
use \DateInterval;
use \DateTimeZone;
use Utils\I18N\I18NUtil;

/**
 * Contains functions to handle dates and times.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
abstract class DateUtil
{
	/**
	 * Formats the given seconds and returns it in the right language.
	 * If the time is greater than 2 or less than -2 hours, it will return the formatted absolute time.
	 */
	public static function formatRelativeTime( $seconds, $time )
	{
		$dictionary = I18NUtil::getDictionary();
		
		// create DateInterval object with seconds
		$now = new DateTime( null, new DateTimeZone( "Europe/Zurich" ) ); // TODO use correct timezone
		$dummyDate = clone $now;
		$dummyDate->add( DateInterval::createFromDateString( $seconds . " seconds" ) );
		
		$dateInterval = $dummyDate->diff( $now );
		
		if( $seconds <= -60 * 60 * 2 ) // more than 2 hours ago
		{
			return $time->format( $dictionary->get( "relative-time.morethan2hago" ) );
		}
		else if( $seconds <= -60 * 61 ) // between 2 minutes and 2 hours ago
		{
			return $dateInterval->format( $dictionary->get( "relative-time.morethan1hago" ) );
		}
		else if( $seconds <= -60 * 60 ) // an 1 hour ago
		{
			return $dictionary->get( "relative-time.1hago" );
		}
		else if( $seconds <= -120 ) // less than 2 minutes ago
		{
			return $dateInterval->format( $dictionary->get( "relative-time.lessthan1hago" ) );
		}
		else if( $seconds <= -60 ) // less than a minute ago
		{
			return $dictionary->get( "relative-time.1minago" );
		}
		else if( $seconds <= 60 ) // in less than 1 minute
		{
			return $dictionary->get( "relative-time.now" );
		}
		else if( $seconds < 120 ) // in less than 2 minutes
		{
			return $dictionary->get( "relative-time.1min" );
		}
		else if( $seconds < 60 * 60 ) // in less than 1 hour
		{
			return $dateInterval->format( $dictionary->get( "relative-time.lessthan1h" ) );
		}
		else if( $seconds < 60 * 61 ) // in 1 hour
		{
			return $dictionary->get( "relative-time.1h" );
		}
		else if( $seconds <= 60 * 60 * 2 ) // in less than 2 hours
		{
			return $dateInterval->format( $dictionary->get( "relative-time.morethan1h" ) );
		}
		else // in more than 2 hours
		{
			return $time->format( $dictionary->get( "relative-time.morethan2h" ) );
		}
	}
}
