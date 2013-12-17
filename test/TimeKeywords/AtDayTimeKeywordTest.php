<?php

namespace TimeKeywords;

use \PHPUnit_Framework_TestCase;
use \DateTime;
use \DateTimeZone;

require_once 'src/TimeKeywords/AtDayTimeKeyword.php';

class AtDayTimeKeywordTest extends PHPUnit_Framework_TestCase
{
	protected $keyword;
	protected $timezone;

    protected function setUp()
    {
        $this->keyword = new AtDayTimeKeyword();
        $this->timezone = new DateTimeZone( "Europe/Zurich" );
    }

	public function testMatches()
	{
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern 18.12.2013 12:53" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Basel 13.11.2010 3:45" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Dietikon nach Genf 8.10.2000 0:54" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Berlin 1.11.2011 23:59" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Dietikon nach Genf 8.2.1999 8:56" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Berlin 1.1.1911 11:11" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Dietikon nach Genf am 8.2.1999 8:56" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Berlin am 1.1.1911 11:11" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern 18.12.2013 um 12:53" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Basel 13.11.2010 um 3:45" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern am 18.12.2013 um 12:53" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Basel 13.11.2010 am 1.9.2010 um 3:45" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Bellinzona nach Locarno am 24.12. um 20:01" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Bellinzona nach Locarno 24.12. um 20:01" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Bellinzona nach Locarno am 24.12. 20:01" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Bellinzona nach Locarno 24.12. 20:01" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern 18. Dezember 2013 12:53" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Basel 13. November 2010 3:45" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Dietikon nach Genf am 8. Oktober 2000 0:54" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Berlin am 1. November 2011 23:59" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern 18. Dezember 2013 um 12:53" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Basel 13. November 2010 um 3:45" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern am 2. März 13 um 11:10" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Basel am 1. August 10 um 8:45" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Bellinzona nach Locarno am 24. Dezember um 20:01" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Bellinzona nach Locarno 24. Dezember um 20:01" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Bellinzona nach Locarno am 24. Dezember 20:01" ) );
		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Bellinzona nach Locarno 24. Dezember 20:01" ) );

		$this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Bern nach Dietikon am 1. Dezember 19:00" ) );

		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Wien nach Paris 1:30" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Berlin 8:45" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Wien nach Paris um 10:00" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Brig um 23:59" ) );

		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern 24.12. 14:62" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Basel 24.12. 25:00" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern 24.12. um 12:60" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Basel 24.12. um 24:00" ) );

		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Wien nach Paris 24. 10:00" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Brig am 18. 23:59" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Wien nach Paris 24.9.21" ) );
		$this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Brig am 18.1.11" ) );
	}

	public function testTimeGetter()
	{
		$this->assertEquals( new DateTime( "18.12.2013 12:53:00", $this->timezone ),
				$this->keyword->getTime( "von Zürich HB nach Bern am 18.12.2013 um 12:53" ) );

		$this->assertEquals( new DateTime( "11.10.2013 01:32:00", $this->timezone ),
				$this->keyword->getTime( "nach Basel 11. Oktober 1:32" ) );

		$this->assertEquals( new DateTime( "01.01.2014 01:01:00", $this->timezone ),
				$this->keyword->getTime( "von Dietikon nach Genf 1.1.14 um 1:01" ) );

		$this->assertEquals( new DateTime( "02.06.2009 23:59:00", $this->timezone ),
				$this->keyword->getTime( "nach Berlin 2. Juni 2009 um 23:59" ) );

		$this->assertEquals( new DateTime( "15.03.2013 11:20:00", $this->timezone ),
				$this->keyword->getTime( "nach Berlin 15.03. um 11:20" ) );

		$this->assertEquals( new DateTime( "05.01.2013 13:37:00", $this->timezone ),
				$this->keyword->getTime( "nach Berlin am 05.01. um 13:37" ) );

		$this->assertEquals( new DateTime( "06.09.2018 13:37:00", $this->timezone ),
				$this->keyword->getTime( "nach Berlin am 6. September 18 13:37" ) );

		$this->assertEquals( new DateTime( "23.04.2000 09:12:00", $this->timezone ),
				$this->keyword->getTime( "nach Berlin am 23. April 2000 9:12" ) );

		$this->assertEquals( new DateTime( "11.11.2013 11:11:00", $this->timezone ),
				$this->keyword->getTime( "nach Basel 11.11. 11:11" ) );

		$this->assertEquals( new DateTime( "01.07.2013 02:11:00", $this->timezone ),
				$this->keyword->getTime( "von Dietikon nach Genf 1. Juli 13 02:11" ) );

		$this->assertEquals( new DateTime( "01.12.2013 19:00:00", $this->timezone ),
				$this->keyword->getTime( "von Bern nach Dietikon am 1. Dezember um 19:00" ) );
	}

	public function testRemoveTimeKeyword()
	{
		$this->assertEquals( "von Zürich HB nach Bern",
				$this->keyword->removeTimeKeyword( "von Zürich HB nach Bern 18.12.2013 12:53" ) );

		$this->assertEquals( "nach Berlin",
				$this->keyword->removeTimeKeyword( "nach Berlin am 1.1.1911 11:11" ) );

		$this->assertEquals( "von Bellinzona nach Locarno",
				$this->keyword->removeTimeKeyword( "von Bellinzona nach Locarno am 24.12. um 20:01" ) );

		$this->assertEquals( "nach Basel",
				$this->keyword->removeTimeKeyword( "nach Basel am 1.9.2010 um 3:45" ) );


		$this->assertEquals( "von Zürich HB nach Bern",
				$this->keyword->removeTimeKeyword( "von Zürich HB nach Bern 18. Dezember 2013 12:53" ) );

		$this->assertEquals( "von Bellinzona nach Locarno",
				$this->keyword->removeTimeKeyword( "von Bellinzona nach Locarno am 24. Dezember 20:01" ) );

		$this->assertEquals( "nach Basel",
				$this->keyword->removeTimeKeyword( "nach Basel 1. August 10 um 8:45" ) );

		$this->assertEquals( "nach Basel",
				$this->keyword->removeTimeKeyword( "nach Basel am 1. August 10 um 8:45" ) );
	}
}
