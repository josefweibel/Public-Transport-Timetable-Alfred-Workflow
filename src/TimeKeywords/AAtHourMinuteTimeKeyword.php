<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use TimeKeywords\AHourMinuteTimeKeyword;
use Utils\I18N\I18NUtil;

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
abstract class AAtHourMinuteTimeKeyword extends AHourMinuteTimeKeyword
{
	private static $onKeyword = null;

    public function __construct()
    {
        parent::__construct();
        $dictionary = I18NUtil::getDictionary();
        self::$onKeyword = $dictionary->get( "timekeywords.onday" );
    }

	/**
	 * [keyword] [hour]:[minute]
	 * @return string a valid pattern with your keyword.
	 */
	public function getPattern()
	{
		$this->checkKeyword();
		return "/.*( " . self::$onKeyword . ")?( " . $this->keyword . ")( " . $this->atKeyword . ")?" . self::getTimePattern() . "$/i";
	}

	/**
	 * Removes your keyword and the time from the query.
	 * @param string $query
	 * @return string the query without the keyword and the time.
	 */
	public function removeTimeKeyword( $query )
	{
		$this->checkKeyword();
		return preg_replace( "/( " . self::$onKeyword . ")?( " . $this->keyword . ")?" . ( strlen( $this->atKeyword ) > 0 ? "( " . $this->atKeyword .
				")?" : "" ) . self::getTimePattern() . "$/i" , "", $query );
	}
}