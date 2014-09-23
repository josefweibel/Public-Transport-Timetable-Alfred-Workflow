<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use \DateTime;
use \DateTimeZone;
use TimeKeywords\AAtHourMinuteTimeKeyword;
use Utils\I18N\I18NUtil;

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class AtDayTimeKeyword extends AAtHourMinuteTimeKeyword
{
	protected $months = null;

	public function __construct()
	{
        parent::__construct();

        $dictionary = I18NUtil::getDictionary();
        $this->months = array(
             1 => $dictionary->get( "months.january" ),
             2 => $dictionary->get( "months.february" ),
             3 => $dictionary->get( "months.march" ),
             4 => $dictionary->get( "months.april" ),
             5 => $dictionary->get( "months.may" ),
             6 => $dictionary->get( "months.june" ),
             7 => $dictionary->get( "months.july" ),
             8 => $dictionary->get( "months.august" ),
             9 => $dictionary->get( "months.september" ),
            10 => $dictionary->get( "months.october" ),
            11 => $dictionary->get( "months.november" ),
            12 => $dictionary->get( "months.december" ) );

		$this->keyword = "(0?[1-9]|[12][0-9]|3[01])\.(0?[1-9]\.|1[012]\.| " . implode( " ?| ", $this->months ) . " ?)(((19|20)?[0-9]{2}))?";
	}

	public function getTime( $query )
	{
		$currentDate = new DateTime( null, new DateTimeZone( "Europe/Zurich" ) ); // TODO use right timezone

		$matches = array();
		preg_match( $this->getPattern(), $query, $matches );

		$date  = ( $matches[5] ? ( strlen( $matches[5] ) == 2 ? "20" . $matches[5] : $matches[5] ) : $currentDate->format( "Y" ) ) . "-";
		$date .= str_pad( trim( str_replace( ".", "", str_ireplace( $this->months, array_keys( $this->months ), $matches[4] ) ) ), 2, "0", STR_PAD_LEFT ) . "-";
		$date .= str_pad( $matches[3], 2, "0", STR_PAD_LEFT );

		return $this->getDateTimeFromQuery( $query, $date );
	}
}