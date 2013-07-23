<?php
namespace TimeKeywords;

use BadFunctionCallException;
use TimeKeywords\ITimeKeyword;

require_once 'source/TimeKeywords/ITimeKeyword.php';

/**
 * An abstract which can used for TimeKeywords with the following pattern: [keyword] [hour]:[minute]
 * In your implementation you have to set the $keyword and implement the @link ITimeKeyword#getTime( $query ).
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
abstract class AHourMinuteTimeKeyword implements ITimeKeyword
{
	/**
	 * The keyword which identifies your TimeKeyword. It will be used for the patterns.
	 * You have to initalize this variable first of all. For example in your constructor.
	 * @var string 
	 */
	protected $keyword = null;
	
	/**
	 * [keyword] [hour]:[minute]
	 * @return string a valid pattern with your keyword.
	 */
	public function getPattern()
	{
		if( !$this->keyword )
		{
			throw new BadFunctionCallException( "you have to define the class variable " .
				"'keyword' in your AHourMinuteTimeKeyword implementation." );
		}
		
		return "/.*( " . $this->keyword . ")( )(([01]?[0-9]|2[0-3]):[0-5][0-9])$/i";
	}
	
	/**
	 * Removes your keyword and the time from the query.
	 * @param string $query
	 * @return string the query without the keyword and the time.
	 */
	public function removeTimeKeyword( $query )
	{
		if( !$this->keyword )
		{
			throw new BadFunctionCallException( "you have to define the class variable " .
				"'keyword' in your AHourMinuteTimeKeyword implementation." );
		}
		
		return preg_replace( "/( " . $this->keyword . 
				")?( )(([01]?[0-9]|2[0-3]):[0-5][0-9]|)$/i" , "", $query );
	}
}