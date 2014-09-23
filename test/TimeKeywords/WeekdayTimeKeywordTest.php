<?php

namespace TimeKeywords;

use \PHPUnit_Framework_TestCase;
use \DateTime;
use \DateTimeZone;
use Utils\I18N\I18NUtil;

require_once 'src/TimeKeywords/WeekdayTimeKeyword.php';

class WeekdayTimeKeywordTest extends PHPUnit_Framework_TestCase
{
	protected $keyword;
	protected $timezone;
 
    protected function setUp()
    {
        $this->timezone = new DateTimeZone( "Europe/Zurich" );
    }
    
	public function testMatches()
	{
		$dictionary = I18NUtil::getDictionary();
        foreach( WeekdayTimeKeyword::$weekdays as $weekday )
        {
            $this->keyword = new WeekdayTimeKeyword( $weekday );
			$german = $dictionary->get( "weekdays." . $weekday );
            
            $this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern " . $german . " 12:53" ) );
            $this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Basel " . $german . " 3:45" ) );
            $this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Dietikon nach Genf " . $german . " um 00:54" ) );
            $this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Berlin " . $german . " um 23:59" ) );
            
            $this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern am " . $german . " 12:53" ) );
            $this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Basel am " . $german . " 3:45" ) );
            $this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "von Dietikon nach Genf am " . $german . " um 00:54" ) );
            $this->assertEquals( 1, preg_match( $this->keyword->getPattern(), "nach Berlin am " . $german . " um 23:59" ) );
    
            $this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Wien nach Paris 1:30" ) );
            $this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Berlin 8:45" ) );
            $this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Wien nach Paris um 10:00" ) );
            $this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Brig um 23:59" ) );
    
            $this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern " . $german . " 14:62" ) );
            $this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Basel " . $german . " 25:00" ) );
            $this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "von Zürich HB nach Bern " . $german . " um 12:60" ) );
            $this->assertEquals( 0, preg_match( $this->keyword->getPattern(), "nach Basel " . $german . " um 24:00" ) );
        }
	}
	
	public function testTimeGetter()
	{
		$dictionary = I18NUtil::getDictionary();
        foreach( WeekdayTimeKeyword::$weekdays as $weekday )
        {
            $this->keyword = new WeekdayTimeKeyword( $weekday );
			$german = $dictionary->get( "weekdays." . $weekday );
            
            $this->assertEquals( new DateTime( $weekday . " 12:53:00", $this->timezone ), 
                    $this->keyword->getTime( "von Zürich HB nach Bern " . $german . " 12:53" ) );
            $this->assertEquals( new DateTime( $weekday . " 3:45:00", $this->timezone ), 
                    $this->keyword->getTime( "nach Basel " . $german . " 3:45" ) );
            $this->assertEquals( new DateTime( $weekday . " 0:54:00", $this->timezone ), 
                    $this->keyword->getTime( "von Dietikon nach Genf " . $german . " um 00:54" ) );
            $this->assertEquals( new DateTime( $weekday . " 23:59:00", $this->timezone ), 
                    $this->keyword->getTime( "nach Berlin " . $german . " um 23:59" ) );
            $this->assertEquals( new DateTime( $weekday . " 0:54:00", $this->timezone ), 
                    $this->keyword->getTime( "von Dietikon nach Genf am " . $german . " um 00:54" ) );
            $this->assertEquals( new DateTime( $weekday . " 23:59:00", $this->timezone ), 
                    $this->keyword->getTime( "nach Berlin am " . $german . " um 23:59" ) );
        }
	}
	
	public function testRemoveTimeKeyword()
	{
		$dictionary = I18NUtil::getDictionary();
        foreach( WeekdayTimeKeyword::$weekdays as $weekday )
        {
            $this->keyword = new WeekdayTimeKeyword( $weekday );
			$german = $dictionary->get( "weekdays." . $weekday );
            
            $this->assertEquals( "von Zürich HB nach Bern", 
                    $this->keyword->removeTimeKeyword( "von Zürich HB nach Bern " . $german . " 12:53" ) );
            $this->assertEquals( "nach Basel", 
                    $this->keyword->removeTimeKeyword( "nach Basel " . $german . " 3:45" ) );
            $this->assertEquals( "von Dietikon nach Genf", 
                    $this->keyword->removeTimeKeyword( "von Dietikon nach Genf " . $german . " um 00:54" ) );
            $this->assertEquals( "nach Berlin", 
                    $this->keyword->removeTimeKeyword( "nach Berlin " . $german . " um 23:59" ) );
            $this->assertEquals( "von Dietikon nach Genf", 
                    $this->keyword->removeTimeKeyword( "von Dietikon nach Genf am " . $german . " um 00:54" ) );
            $this->assertEquals( "nach Berlin", 
                    $this->keyword->removeTimeKeyword( "nach Berlin am " . $german . " um 23:59" ) );
            
            $this->assertEquals( "von Zürich HB nach Bern " . $german . " 14:62", 
                    $this->keyword->removeTimeKeyword( "von Zürich HB nach Bern " . $german . " 14:62" ) );
            $this->assertEquals( "nach Basel " . $german . " 25:00", 
                    $this->keyword->removeTimeKeyword( "nach Basel " . $german . " 25:00" ) );
        }
	}
}