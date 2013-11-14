<?php

namespace TimeKeywords;

use \PHPUnit_Framework_TestCase;
use \DateTime;
use \DateTimeZone;

require_once 'src/TimeKeywords/AtTimeKeyword.php';

class AtTimeKeywordTest extends PHPUnit_Framework_TestCase
{
	protected $keyword;
	protected $timezone;

    protected function setUp()
    {
        $this->keyword = new AtTimeKeyword();
        $this->timezone = new DateTimeZone( "Europe/Zurich" );
    }

	public function testMatches()
	{
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Aarau nach Sursee um 19:00" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Baden um 18:32" ) );

		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Solothurn nach Grenchen um 24:00" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Sissach um 9:61" ) );
	}

	public function testTimeGetter()
	{
		$this->assertEquals( new DateTime( "19:00", $this->timezone ), $this->keyword->getTime( "von Aarau nach Sursee um 19:00" ) );
		$this->assertEquals( new DateTime( "18:32", $this->timezone ), $this->keyword->getTime( "nach Baden um 18:32" ) );
	}

	public function testRemoveTimeKeyword()
	{
		$this->assertEquals( "von Aarau nach Sursee",
				$this->keyword->removeTimeKeyword( "von Aarau nach Sursee um 19:00" ) );
		$this->assertEquals( "nach Baden",
				$this->keyword->removeTimeKeyword( "nach Baden um 18:32" ) );
		$this->assertEquals( "nach Baden um",
				$this->keyword->removeTimeKeyword( "nach Baden um um 18:32" ) );
	}
}