<?php
namespace TimeKeywords;

/**
 * Interface for all TimeKeywords which will could used in queries.
 * Please register your TimeKeyword in the TimeKeywordManager.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
interface ITimeKeyword
{
	/**
	 * A regular expression pattern for checking if the query matches with this TimeKeyword.
	 * @return string a valid regular expression
	 */
	public function getPattern();
	
	/**
	 * Removes all data from the query which belongs to the time keyword.
	 * @param string $query the query with the time keyword.
	 * @return string the query without the time keyword.
	 */
	public function removeTimeKeyword( $query );

	/**
	 * If the query matches with the pattern than this method will be called.
	 * It should return a DateTime object with the date and time which was given in the query.
	 * @param string $query the query with the time keyword.
	 * @return DateTime a @link DateTime object with the date and time in the query.
	 */
	public function getTime( $query );
}