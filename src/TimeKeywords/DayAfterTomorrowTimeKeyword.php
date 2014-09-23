<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use TimeKeywords\AHourMinuteTimeKeyword;
use Utils\I18N\I18NUtil;

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
        parent::__construct();

        $dictionary = I18NUtil::getDictionary();
        $this->keyword = $dictionary->get( "timekeywords.dayaftertomorrow" );
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

