<?php

namespace TimeKeywords;

use \PHPUnit_Framework_TestCase;
use \DateTime;
use \DateTimeZone;

require_once 'src/TimeKeywords/NowTimeKeyword.php';

class NowTimeKeywordTest extends PHPUnit_Framework_TestCase
{
	protected $keyword;
	protected $timezone;
 
    protected function setUp()
    {
        $this->keyword = new NowTimeKeyword();
        $this->timezone = new DateTimeZone( "Europe/Zurich" );
    }
    
	public function testMatches()
	{
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Chiasso nach Visp jetzt" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach St. Gallen jetzt" ) );

		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Lausanne nach Bieljetzt" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Münchenjetzt" ) );
	}
	
	public function testTimeGetter()
	{
		$this->assertEquals( new DateTime( null, $this->timezone ), $this->keyword->getTime( "von Chiasso nach Visp jetzt" ) );
		$this->assertEquals( new DateTime( null, $this->timezone ), $this->keyword->getTime( "nach St. Gallen jetzt" ) );
	}
	
	public function testRemoveTimeKeyword()
	{
		$this->assertEquals( "von Chiasso nach Visp", 
				$this->keyword->removeTimeKeyword( "von Chiasso nach Visp jetzt" ) );
		$this->assertEquals( "nach St. Gallen", 
				$this->keyword->removeTimeKeyword( "nach St. Gallen jetzt" ) );
		
		$this->assertEquals( "von Lausanne nach Bieljetzt", 
				$this->keyword->removeTimeKeyword( "von Lausanne nach Bieljetzt" ) );
		$this->assertEquals( "nach Münchenjetzt", 
				$this->keyword->removeTimeKeyword( "nach Münchenjetzt" ) );
	}
}