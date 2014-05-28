<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use TimeKeywords\TodayTimeKeyword;

/**
 * The same as @link TodayTimeKeyword but with the keyword 'um'.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class AtTimeKeyword extends TodayTimeKeyword
{
	/**
	 * Sets the keyword 'um'.
	 */
	public function __construct()
	{
		$this->keyword = "um";
		$this->atKeyword = "";
	}
}