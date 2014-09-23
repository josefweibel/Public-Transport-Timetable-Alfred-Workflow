<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use TimeKeywords\AHourMinuteTimeKeyword;
use Utils\I18N\I18NUtil;

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
        parent::__construct();
        $dictionary = I18NUtil::getDictionary();
        $this->keyword = $dictionary->get( "timekeywords.today" );
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