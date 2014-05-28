<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use \DateTime;
use \DateTimeZone;
use TimeKeywords\AAtHourMinuteTimeKeyword;

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class AtDayTimeKeyword extends AAtHourMinuteTimeKeyword
{
	protected $months = array(	   1 => "Januar",
								   2 => "Februar",
								   3 => "März",
								   4 => "April",
								   5 => "Mai",
								   6 => "Juni",
								   7 => "Juli",
								   8 => "August",
								   9 => "September",
								  10 => "Oktober",
								  11 => "November",
								  12 => "Dezember" );

	public function __construct()
	{
		$this->keyword = "(0?[1-9]|[12][0-9]|3[01])\.(0?[1-9]\.|1[012]\.| " . implode( " ?| ", $this->months ) . " ?)(((19|20)?[0-9]{2}))?";
	}

	public function getTime( $query )
	{
		$currentDate = new DateTime( null, new DateTimeZone( "Europe/Zurich" ) );

		$matches = array(); //"/" . $this->keyword . "/i"
		preg_match( $this->getPattern(), $query, $matches );

		$date	 = ( $matches[5] ? ( strlen( $matches[5] ) == 2 ? "20" . $matches[5] : $matches[5] ) : $currentDate->format( "Y" ) ) . "-";
		$date .= str_pad( trim( str_replace( ".", "", str_ireplace( $this->months, array_keys( $this->months ), $matches[4] ) ) ), 2, "0", STR_PAD_LEFT ) . "-";
		$date .= str_pad( $matches[3], 2, "0", STR_PAD_LEFT );

		return $this->getDateTimeFromQuery( $query, $date );
	}
}