<?php
namespace Utils;

include( "src/Initializer.php" );

use DateTime;
use DateTimeZone;
use Utils\Response;
use Utils\WorkflowUtil;
use Utils\I18N\I18NUtil;

/**
 * Has methods to get data from the Transport API and saves the results readable as Alfred results to an response.
 * All methods have to be static.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
abstract class TransportUtil
{
	/**
	 * @var string translation key for the take me home keyword.
	 */
	const KEYWORD_HOME = "keywords.home";

	/**
	 * @var string url to get locations.
	 */
	const URL_LOCATIONS = "http://transport.opendata.ch/v1/locations";

	/**
	 * @var string url to get connections.
	 */
	const URL_CONNECTIONS = "http://transport.opendata.ch/v1/connections";

	/**
	 * @var array of transport shortcuts which are complicated to understand.
	 * complicated shortcut => nice shortcut translation key (prefix it with 'badnames.')
	 */
	public static $badnames = array( "T" => "tram", "B" => "bus", "NFB" => "nfb", "NFT" => "nft" );

	/**
	 * @var array of service categories in which is it helpful to show the linenumber too.
	 */
	public static $categoriesWithNumber = array( "S", "SN", "T", "B", "NFB", "NFT" );

	/**
	 * Adds locations from the Transport API to the given response.
	 * @param Response $response
	 * @param String $query search text
	 * @param String $exclude (optional)
	 * @param String $subtitle placeholder for the station is 'station'
	 * @param array $subtitleParams additional params for getting the translation from the dictionary
	 * @param boolean $isOk
	 * @param String $okText (optional) dictionary key with placeholder {station}.
	 * @param int $max (optional, default = 10)
	 */
	public static function addLocations( &$response, $query, $exclude, $subtitle, 
			$subtitleParams = null, $isOk = false, $okText = null, $okParams = array(), $max = 10 )
	{
		$dictionary = I18NUtil::getDictionary();

		// if the query is equal to the excluded name, add a comma to
		// get stations of the city in the query.
		if( strtolower( $query ) == strtolower( $exclude ) )
		{
			$query = $query . ", ";
		}

		$params = array( "query" => $query );

		// get the results
		$json = WorkflowUtil::request( self::URL_LOCATIONS . "?" . http_build_query( $params ) );
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
					if( !$subtitleParams )
					{
						$subtitleParams = array();
					}

					$subtitleParams = array_merge( $subtitleParams, array( "station" => $station->name ) );

					if( $okText )
					{
						$okText = $dictionary->get( $okText, array_merge( $okParams, array( "station" => $station->name ) ) );
					}
					else
					{
						$okText = $station->name;
					}
					
					// add station to the response
					$response->add( $station->id, $station->name, $station->name, 
							$dictionary->get( $subtitle, $subtitleParams ), WorkflowUtil::getImage( "station.png" ), 
							!$isOk ? "yes" : "no", $okText );
				}
				elseif ( count( $stations ) === 1 )
				{
					// the single station is excluded -> message
					$response->add( "nothing", "nothing", $dictionary->get( "errors.alreadyatstart-title" ),
							$dictionary->get( "errors.alreadyatstart-subtitle", array( "station" => $station->name ) ),
							WorkflowUtil::getImage( "icon.png" ) );
				}
			}
		}
		else
		{
			// no results found (or server could be down) -> message
			$response->add( "nothing", "nothing", $dictionary->get( "errors.notplacefound-title" ),
					$dictionary->get( "errors.noplacefound-subtitle" ), WorkflowUtil::getImage( "icon.png" ) );
		}
	}

	/**
	 * Adds the home location if it matches with the query to the response.
	 * @param Response $response
	 * @param String $query
	 * @param boolean $isOk
	 * @param String $okText (optional) dictionary key with placeholder {station}.
	 */
	public static function addHomeLocation( &$response, $query, $isOk, $okText = null, $okParams = array() )
	{
		$dictionary = I18NUtil::getDictionary();
		$keyword = $dictionary->get( self::KEYWORD_HOME );

		if( strpos( $keyword, $query ) !== false )
		{
			if( $okText )
			{
				$okText = $dictionary->get( $okText, array_merge( $okParams, array( "station" => $keyword ) ) );
			}
			else
			{
				$okText = $keyword;
			}
			
			$response->add( $keyword, $keyword, $keyword, $dictionary->get( self::KEYWORD_HOME . "subtitle" ),
					WorkflowUtil::getImage( "station.png" ), !$isOk ? "yes" : "no", $okText );
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
		$dictionary = I18NUtil::getDictionary();

		if( strtolower( $from ) != strtolower( $to ) )
		{
			$onlyDate = $date->format( "Y-m-d" );
			$onlyTime = $date->format( "H:i" );

			$params = array( "limit" => $max, "from" => $from, "to" => $to,
				"date" => $onlyDate, "time" => $onlyTime );

			// get the results
			$json = WorkflowUtil::request( self::URL_CONNECTIONS . "?" . http_build_query( $params ) );
			$result = json_decode( $json );
			$connections = $result->connections;

			if( $connections )
			{
				$class = self::getClass();

				$timezone = new DateTimeZone( "Europe/Zurich" ); // TODO: use right timezone.
				$now = new DateTime( null, $timezone );

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

					// TODO: create helper function for this
					$departureText = DateUtil::formatRelativeTime( $secondsToDeparture, $departure );

					if( !empty( $connection->from->prognosis->departure ) )
					{
						$newDeparture = new DateTime( $connection->from->prognosis->departure, $timezone );
						array_push( $subtitles, $dictionary->get( "connection-titles.departures-delay", 
							array( "station" => WorkflowUtil::formatTimeDiff( $newDeparture->diff( $departure ), false ) ) ) );
					}

					if( $capacity )
					{
						$departureText .= "   ";
						for( $i = 0; $i < 3; $i++ )
						{
							$departureText .= $capacity > $i ? "●" : "○";
						}
					}

					// TODO: create SubtitlePart-Objects
					// subtitles
					$subtitles = array();
					if( $secondsToDeparture <= 60 * 60 * 2 && $secondsToDeparture >= -60 * 60 * 2 )
					{
						array_push( $subtitles, $dictionary->get( "connection-subtitles.departure-time", array( "time" => $departure->format( "H:i" ) ) ) );
					}
					else if( $now->format( "Y-m-d" ) != $departure->format( "Y-m-d" ) )
					{
						array_push( $subtitles, $dictionary->get( "connection-subtitles.departure-date", array( "date" => $departure->format( "d.m.Y" ) ) ) );
					}

					if( $connection->from->platform )
					{
						if( empty( $connection->from->prognosis->platform ) )
						{
							array_push( $subtitles, $dictionary->get( "connection-subtitles.departure-track", 
								array( "track" => $connection->from->platform ) ) );
						}
						else
						{
							array_push( $subtitles, $dictionary->get( "connection-subtitles.departure-track-changed", 
								array( "oldtrack" => $connection->from->platform, "track" => $connection->from->prognosis->platform ) ) );
						}
					}

					if( empty( $connection->to->prognosis->arrival ) )
					{
						array_push( $subtitles, $dictionary->get( "connection-subtitles.arrival-time", 
								array( "time" => $arrival->format( "H:i" ) ) ) );
					}
					else
					{
						$newArrival = new DateTime( $connection->to->prognosis->arrival, $timezone );
						array_push( $subtitles, $dictionary->get( "connection-subtitles.arrival-time-delay", 
								array( "time" => $arrival->format( "H:i" ), "delay" => WorkflowUtil::formatTimeDiff( 
										$newArrival->diff( $arrival ), true ) ) ) );
					}

					array_push( $subtitles, $dictionary->get( "connection-subtitles.duration", 
							array( "duration" => WorkflowUtil::formatTimeDiff( $duration ) ) ) );
					
					if( $withFromInSubtext )
					{
						array_push( $subtitles, $dictionary->get( "connection-subtitles.departure-station", 
							array( "station" => $connection->from->station->name ) ) );
					}
					
					if( $withToInSubtext )
					{
						array_push( $subtitles, $dictionary->get( "connection-subtitles.arrival-station", 
							array( "station" => $connection->to->station->name ) ) );
					}

					$sections = array();
					foreach( $connection->sections as $section )
					{
						if( $section->journey && $section->journey->category )
						{
							$sectionText = "";

							if( array_key_exists( $section->journey->category, self::$badnames ) )
							{
								$sectionText = $dictionary->get( "badnames." . self::$badnames[ $section->journey->category ] );
							}
							else
							{
								$sectionText = $section->journey->category;
							}

							if( in_array( $section->journey->category, self::$categoriesWithNumber ) )
							{
								$parts = explode( " ", $section->journey->name );
								if( count( $parts ) >= 2 )
								{
									$sectionText .= $parts[1];
								}
							}

							array_push( $sections, $sectionText );
						}
					}
					
					array_push( $subtitles, $dictionary->get( "connection-subtitles.transporttypes", array( "types" => implode( ", ", $sections ) ) ) );

					if( count( $subtitles ) )
					{
						$subtitle = implode( ", ", $subtitles ) . ".";
					}

					// if there are connections with the same departure time, increment the amount per site.
					if( $lastDepartureTime == $connection->from->departure )
					{
						$perSite++;
					}

					// create a more a less unique identifier for this connection.
					$id = $connection->from->station->id . "-" . $connection->to->station->id . "-" .
							$connection->from->departure;

					$url = self::getConnectionURL( $connection->to->station->name, $connection->from->station->name, 
							$date, floor( $connectionIndex / $perSite ),  ( $connectionIndex % $perSite ) + 1 );

					$response->add( $id, $url, $departureText, $subtitle, WorkflowUtil::getImage( "arrow.png" ) );

					$connectionIndex++;
					$lastDepartureTime = $connection->from->departure;
				}
			}
			else
			{
				$response->add( "nothing", "nothing", $dictionary->get( "errors.noconnectionsfound-title" ),
						$dictionary->get( "errors.noconnectionsfound-subtitle" ), WorkflowUtil::getImage( "icon.png" ) );
			}
		}
		else
		{
			$response->add( "nothing", "nothing", $dictionary->get( "errors.samestartandendplace-title" ),
					$dictionary->get( "errors.samestartandendplace-subtitle" ), WorkflowUtil::getImage( "icon.png" ) );
		}
	}
	
	public static function getConnectionURL( $departureStation, $arrivalStation, $departureDate, $page, $index )
	{
		return sprintf( "/to/%s/from/%s/at/%s?page=%s&c=%s", urlencode( $departureStation ), 
				urlencode( $arrivalStation ), urlencode( $departureDate->format( "Y-m-d\TH:i" ) ), $page, $index );
	}

	/**
	 * Returns the class which has the user setted. If no class was set it will return 2.
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

		$dictionary = I18NUtil::getDictionary();
		$keyword = $dictionary->get( self::KEYWORD_HOME );

		if( preg_match( "/" . $keyword . "$/i", trim( $query ) ) && $home )
		{
			return $home;
		}

		return $query;
	}
}
