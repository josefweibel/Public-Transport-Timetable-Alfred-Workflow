<?php
namespace TimeKeywords;

use TimeKeywords\TodayTimeKeyword;

require_once 'source/TimeKeywords/TodayTimeKeyword.php';

/**
 * The same as @link TodayTimeKeyword but with the keyword 'um'.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class TodayAtTimeKeyword extends TodayTimeKeyword
{
	/**
	 * Sets the keyword 'um'.
	 */
	public function __construct()
	{
		$this->keyword = "um";
	}
}