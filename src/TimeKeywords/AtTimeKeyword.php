<?php
namespace TimeKeywords;

include( "src/Initializer.php" );

use TimeKeywords\TodayTimeKeyword;
use Utils\I18N\I18NUtil;

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
        parent::__construct();

        $dictionary = I18NUtil::getDictionary();
        $this->keyword = $dictionary->get( "timekeywords.attime" );
        $this->atKeyword = "";
	}
}