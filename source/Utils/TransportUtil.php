<?php
namespace Utils;

use DateTime;
use DateTimeZone;
use Utils\Response;
use Utils\WorkflowUtil;

/**
 * Has methods to get data from the Transport API and saves the results readable as Alfred results to an response.
 * All methods have to be static.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
abstract class TransportUtil
{
	/**
	 * @var string default TimeKeyword 
	 */
	public static $nowKeyword = "jetzt";
	
	/**
	 * @var string keyword for take me home. 
	 */
	public static $homeKeyword = "Hause";
	
	/**
	 * @var string url to get locations. 
	 */
	public static $urlLocations = "http://transport.opendata.ch/v1/locations?";
	
	/**
	 * @var string url to get connections. 
	 */
	public static $urlConnections = "http://transport.opendata.ch/v1/connections?";
	
	/**
	 * @var array of transport shortcuts which are complicated to understand.
	 * complicated shortcut => nice shortcut
	 */
	public static $badNames = array( "T" => "Tram", "B" => "Bus", "NFB" => "NF Bus", "NFT" => "NF Tram" );

	/**
	 * Adds locations from the Transport API to the given response.
	 * @param Response $response
	 * @param String $query search text
	 * @param String $exclude (optional)
	 * @param String $titlePrefix
	 * @param String $titleSuffix
	 * @param boolean $isOk
	 * @param String $okPrefix (optional)
	 * @param String $okSuffix (optional)
	 * @param int $max (optional, default = 10)
	 */
	public static function addLocations( &$response, $query, $exclude, $titlePrefix, 
			$titleSuffix, $isOk = false, $okPrefix = null, $okSuffix = null, $max = 10 )
	{
		// if the query equals the excluded name, add an comma to 
		// get stations for the city in the query.
		if( strtolower( $query ) == strtolower( $exclude ) )
		{
			$query = $query . ", ";
		}
		
		$params = array( "query" => $query );
		
		// get the results
		$json = WorkflowUtil::request( self::$urlLocations . http_build_query( $params ) );
		$result = json_decode( $json );
		$stations = $result->stations;
		
		if( $stations )
		{
			$stationIndex = 0;
			foreach ( $stations as $station )
			{
				if( $stationIndex++ > $max )
				{
					break;
				}
				
				if( strtolower( $station->name ) != strtolower( $exclude ) )
				{
					// add station to the response
					$response->add( $station->id, $station->name, $station->name,
							$titlePrefix.$station->name.$titleSuffix, 
							WorkflowUtil::getImage( "station.png" ), !$isOk ? "yes" : "no", 
							$okPrefix.$station->name.$okSuffix );
				}
				elseif ( count( $stations ) === 1 )
				{
					// the single station is excluded -> message
					$response->add( "nothing", "nothing", "Du befindest dich bereits hier", 
							$station->name . " ist dein Startbahnhof. ", 
							WorkflowUtil::getImage( "icon.png" ) );
				}
			}
		}
		else
		{
			// no results found (or server could be down) -> message
			$response->add( "nothing", "nothing", "Hier ist nichts ...", 
					"Es konnte leider kein passender Ort gefunden werden.", 
					WorkflowUtil::getImage( "icon.png" ) );
		}
	}
	
	/**
	 * Adds the home location if it matches with the query to the response.
	 * @param Response $response
	 * @param String $query
	 * @param boolean $isOk
	 * @param String $okPrefix
	 * @param String $okSuffix
	 */
	public static function addHomeLocation( &$response, $query, $isOk, $okPrefix, $okSuffix )
	{
		if( strpos( self::$homeKeyword, $query ) !== false )
		{
			$response->add( self::$homeKeyword, self::$homeKeyword, self::$homeKeyword, 
					"Die nächsten Verbindungen zu dir nach Hause anzeigen.", 
					WorkflowUtil::getImage( "station.png" ), !$isOk ? "yes" : "no", 
					$okPrefix.self::$homeKeyword.$okSuffix );
		}
	}
	
	/**
	 * Adds connections from the Transport API to the given response.
	 * @param Response $response
	 * @param string $from
	 * @param string $to
	 * @param DateTime $date
	 * @param string $withFromInSubtext
	 * @param string $withToInSubtext
	 * @param int $max (optional, default = 6, max = 6)
	 */
	public static function addConnections( &$response, $from, $to, $date, 
			$withFromInSubtext, $withToInSubtext, $max = 6 )
	{	
		if( strtolower( $from ) != strtolower( $to ) )
		{
			$onlyDate = $date->format( "Y-m-d" );
			$onlyTime = $date->format( "H:i" );
			
			$params = array( "limit" => $max, "from" => $from, "to" => $to, 
				"date" => $onlyDate, "time" => $onlyTime );
		
			// get the results
			$json = WorkflowUtil::request( self::$urlConnections . http_build_query( $params ) );
			$result = json_decode( $json );
			$connections = $result->connections;
			
			if( $connections )
			{
				$class = self::getClass();
				
				$timezone = new DateTimeZone( "Europe/Zurich" );
				$now = new DateTime( null, $timezone );
				$nowFormatted = urlencode( $now->format( "Y-m-d\TH:i" ) );
				
				// used to determinate index of connection on trnsprt.ch
				$connectionIndex = 0;
				$perSite = 4;
				$lastDepartureTime = null;
				
				foreach ( $connections as $connection )
				{
					$departure = new DateTime( $connection->from->departure, $timezone );
					$arrival = new DateTime( $connection->to->arrival, $timezone );
					
					$relativeDeparture = $now->diff( $departure );
					$duration = $departure->diff( $arrival );
					$secondsToDeparture = $departure->getTimestamp() - $now->getTimestamp();
					
					$capacity = $class === 2 ? $connection->capacity2nd : $connection->capacity1st;
					
					$departureText = "";
					if( $secondsToDeparture <= -60 * 60 * 2 ) // more than 2 hours ago
					{
						$departureText .= "um " . $departure->format( "H:i" ) . " Uhr";
					}
					else if( $secondsToDeparture <= -60 * 62 ) // between 2 minutes and 2 hours ago
					{
						$departureText = $relativeDeparture->format( "vor einer Stunde und %i Minuten" );
					}
					else if( $secondsToDeparture <= -60 * 61 ) // an 1 hour and one minute ago
					{
						$departureText = "vor einer Stunde und einer Minute";
					}
					else if( $secondsToDeparture <= -60 * 60 ) // an 1 hour ago
					{
						$departureText = "vor einer Stunde";
					}
					else if( $secondsToDeparture <= -120 ) // less than 2 minutes ago
					{
						$departureText = $relativeDeparture->format( "vor %i Minuten" );
					}
					else if( $secondsToDeparture <= -60 ) // less than a minute ago
					{
						$departureText = "vor einer Minute";
					}
					else if( $secondsToDeparture <= 60 ) // in less than 1 minute
					{
						$departureText = "jetzt";
					}
					else if( $secondsToDeparture < 120 ) // in less than 2 minutes
					{
						$departureText = "in einer Minute";
					}
					else if( $secondsToDeparture < 60 * 60 ) // in less than 1 hour
					{
						$departureText = $relativeDeparture->format( "in %i Minuten" );
					}
					else if( $secondsToDeparture < 60 * 61 ) // in 1 hour
					{
						$departureText = "in einer Stunde";
					}
					else if( $secondsToDeparture < 60 * 62 ) // in 1 hour and one minute
					{
						$departureText = "in einer Stunde und einer Minute";
					}
					else if( $secondsToDeparture <= 60 * 60 * 2 ) // in less than 2 hours
					{
						$departureText = $relativeDeparture->format( "in einer Stunde und %i Minuten" );
					}
					else // in more than 2 hours
					{	
						$departureText .= "um " . $departure->format( "H:i" ) . " Uhr";
					}

					if( !empty( $connection->from->prognosis->departure ) )
					{
						$newDeparture = new DateTime( $connection->from->prognosis->departure, $timezone );
						$departureText .= " (+" . WorkflowUtil::formatTimeDiff( 
								$newDeparture->diff( $departure ), " h", " min" ) . ")";
					}
					
					if( $capacity )
					{
						$departureText .= "   ";
						for( $i = 0; $i < 3; $i++ )
						{
							$departureText .= $capacity > $i ? "●" : "○";
						}
					}

					// subtitle
					$subtitle = "";
					if( $secondsToDeparture <= 60 * 60 * 2 && $secondsToDeparture >= -60 * 60 * 2 )
					{
						$subtitle = "um " . $departure->format( "H:i" ) . " Uhr";
					}
					else if( $now->format( "Y-m-d" ) != $departure->format( "Y-m-d" ) )
					{
						$subtitle = "am " . $departure->format( "d.m.Y" );
					}
					
					if( $connection->from->platform != null )
					{
						if( strlen( $subtitle ) > 0 )
						{
							$subtitle .= " ";
						}
						
						$subtitle .= "ab Gleis ";

						if( !empty( $connection->from->prognosis->platform ) )
						{
							$subtitle .= $connection->from->prognosis->platform . " statt ";
						}

						$subtitle .= $connection->from->platform;

					}

					if( strlen( $subtitle ) > 0 )
					{
						$subtitle .= ", ";
					}
					
					$subtitle .= "an " . $arrival->format( "H:i" )." Uhr";
					if( !empty( $connection->to->prognosis->arrival ) )
					{
						$newArrival = new DateTime( $connection->to->prognosis->arrival, $timezone );
						$subtitle .= " (+" . WorkflowUtil::formatTimeDiff( 
								$newArrival->diff( $arrival ), " h", " min" ) . ")";
					}
					
					$subtitle .= ", dauert " . WorkflowUtil::formatTimeDiff( $duration );

					$subtitle .= ( $withFromInSubtext ? " von " . $connection->from->station->name : "" )
							. ( $withToInSubtext ? " nach " . $connection->to->station->name : "" );

					$sections = $connection->sections;
					if( $sections )
					{
						$subtitle .= ", mit";
						$sectionIndex = 0;
						$total = count( $sections ) - 1;
						
						foreach( $sections as $section )
						{
							if( $section->journey->category )
							{
								if( $sectionIndex > 0 )
								{
									$subtitle .= $sectionIndex < $total ? ", " : " und ";
								}
								else
								{
									$subtitle .= " ";
								}

								if( array_key_exists( $section->journey->category, self::$badNames ) )
								{
									$subtitle .= self::$badNames[ $section->journey->category ] 
											. " " . $section->journey->number;
								}
								else
								{
									$subtitle .= $section->journey->category;
								}

								$sectionIndex++;
							}
							else
							{
								$total--;
							}
						}
					}

					if( strlen( $subtitle ) > 0 )
					{
						$subtitle .= ".";
					}
						
					// if there are connections with the same departure time, increment the amount per site.
					if( $lastDepartureTime == $connection->from->departure )
					{
						$perSite++;
					}

					$id = $connection->from->station->id . "-" . $connection->to->station->id . "-" . 
							$connection->from->departure;
					
					$url = "/to/" . urlencode( $connection->to->station->name ) . 
							"/from/" . urlencode( $connection->from->station->name ) . 
							"/at/" . $nowFormatted . 
							"?page=" . floor( $connectionIndex / $perSite ) . 
							"&c=" . ( ( $connectionIndex  % $perSite ) + 1 );
					
					$response->add( $id, $url, $departureText, $subtitle, 
							WorkflowUtil::getImage( "arrow.png" ) );

					$connectionIndex++;
					$lastDepartureTime = $connection->from->departure;
				}
			}
			else
			{
				$response->add( "nothing", "nothing", "Nichts gefunden ...", 
						"Leider konnten keine Verbindungen gefunden werden.", 
						WorkflowUtil::getImage( "icon.png" ) );
			}
		}
		else
		{
			$response->add( "nothing", "nothing", "Für das braucht es keinen öffentlichen Verkehr ...", 
					"Du kannst nicht denselben Ort für den Start und das Zeil verwenden.", 
					WorkflowUtil::getImage( "icon.png" ) );
		}
	}
	
	/**
	 * Returns the class which has the user setted. If no class was setted it will return 2.
	 * @return int the class. 1 or 2.
	 */
	public static function getClass()
	{
		$class = WorkflowUtil::getValue( "config", "class" );

		if( !$class )
		{
			$class = 2;
		}
		
		return $class;
	}
	
	/**
	 * @return string home station of the user from the config file or null if not setted.
	 */
	public static function getHome()
	{
		return WorkflowUtil::getValue( "config", "home" );
	}
	
	/**
	 * @param string $query one station to check.
	 * @return string the home station if the $query is equal to the 
	 * self::$homeKeyword otherwise the given $query.
	 */
	public static function getStationForHome( $query )
	{
		$home = self::getHome();
		
		if( preg_match( "/" . self::$homeKeyword . "$/i", trim( $query ) ) && $home )
		{
			return $home;
		}
		
		return $query;
	}
}
