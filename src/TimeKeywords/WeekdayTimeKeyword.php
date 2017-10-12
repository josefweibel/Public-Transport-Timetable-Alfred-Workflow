<?php
namespace TimeKeywords;

include_once( "src/Initializer.php" );

use TimeKeywords\AAtHourMinuteTimeKeyword;
use Utils\I18N\I18NUtil;

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class WeekdayTimeKeyword extends AAtHourMinuteTimeKeyword
{
	public static $weekdays = array( "monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday" );

	/**
	 * translated weekday.
	 */
	public $keyword = null;
	
	/**
	 * weekday in english.
	 */
	private $weekday = null;

	public function __construct( $weekday )
	{
        parent::__construct();
		
		$dictionary = I18NUtil::getDictionary();
		$this->weekday = $weekday;
		$this->keyword = $dictionary->get( "weekdays." . $weekday );
	}

	public function getTime( $query )
	{
		return $this->getDateTimeFromQuery( $query, $this->weekday );
	}
}