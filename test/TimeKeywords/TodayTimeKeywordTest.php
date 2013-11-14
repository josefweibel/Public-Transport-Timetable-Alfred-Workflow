<?php

namespace TimeKeywords;

use \PHPUnit_Framework_TestCase;
use \DateTime;
use \DateTimeZone;

require_once 'src/TimeKeywords/TodayTimeKeyword.php';

class TodayTimeKeywordTest extends PHPUnit_Framework_TestCase
{
	protected $keyword;
	protected $timezone;
 
    protected function setUp()
    {
        $this->keyword = new TodayTimeKeyword();
        $this->timezone = new DateTimeZone( "Europe/Zurich" );
    }
    
	public function testMatches()
	{
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern heute 12:53" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Basel heute 3:45" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Dietikon nach Genf heute um 00:54" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Berlin heute um 23:59" ) );

		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Wien nach Paris 1:30" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Berlin 8:45" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Wien nach Paris um 10:00" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Brig um 23:59" ) );

		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern heute 14:62" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Basel heute 25:00" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern heute um 12:60" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Basel heute um 24:00" ) );
	}
	
	public function testTimeGetter()
	{
		$this->assertEquals( new DateTime( "12:53:00", $this->timezone ), 
				$this->keyword->getTime( "von Zürich HB nach Bern heute 12:53" ) );
		$this->assertEquals( new DateTime( "3:45:00", $this->timezone ), 
				$this->keyword->getTime( "nach Basel heute 3:45" ) );
		$this->assertEquals( new DateTime( "0:54:00", $this->timezone ), 
				$this->keyword->getTime( "von Dietikon nach Genf heute um 00:54" ) );
		$this->assertEquals( new DateTime( "23:59:00", $this->timezone ), 
				$this->keyword->getTime( "nach Berlin heute um 23:59" ) );
	}
	
	public function testRemoveTimeKeyword()
	{
		$this->assertEquals( "von Zürich HB nach Bern", 
				$this->keyword->removeTimeKeyword( "von Zürich HB nach Bern heute 12:53" ) );
		$this->assertEquals( "nach Basel", 
				$this->keyword->removeTimeKeyword( "nach Basel heute 3:45" ) );
		$this->assertEquals( "von Dietikon nach Genf", 
				$this->keyword->removeTimeKeyword( "von Dietikon nach Genf heute um 00:54" ) );
		$this->assertEquals( "nach Berlin", 
				$this->keyword->removeTimeKeyword( "nach Berlin heute um 23:59" ) );
		
		$this->assertEquals( "von Zürich HB nach Bern heute 14:62", 
				$this->keyword->removeTimeKeyword( "von Zürich HB nach Bern heute 14:62" ) );
		$this->assertEquals( "nach Basel heute 25:00", 
				$this->keyword->removeTimeKeyword( "nach Basel heute 25:00" ) );
	}
}