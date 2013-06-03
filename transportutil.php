<?php
class TransportUtil
{
	public static function getLocations( $query, $not = "", $subtextPrefix, $subtextSuffix, $autoPrefix, $autoSuffix, &$response, $max = 10 )
	{
		if( strtolower( $not ) == strtolower( $query ) )
		{
			$query = $query . ", ";
		}

		$json = WorkflowUtil::request( "http://transport.opendata.ch/v1/locations?query=". urlencode( $query ) );
		$result = json_decode( $json, true );
		$stations = $result[ "stations" ];

		if( $stations && count( $stations ) > 0 )
		{
			for( $i = 0; $i < count( $stations ) && $i < $max; $i++ )
			{
				$station = $stations[ $i ];
				if( strtolower( $not ) != strtolower( $station[ "name" ] ) )
				{
					$response->add( $station[ "id" ], $station[ "name" ], $station[ "name" ], $subtextPrefix.$station[ "name" ].$subtextSuffix, WorkflowUtil::getImage( "station.png" ), "no", $autoPrefix.$station[ "name" ].$autoSuffix );
				}
				else if( count( $stations ) == 1 )
				{
					$response->add( "nothing", $orig, "Du befindest dich schon hier", $station[ "name" ] . " ist dein Startbahnhof. ", WorkflowUtil::getImage( "icon.png" ) );
				}
			}
		}
		else
		{
			$response->add( "nothing", $orig, "Kein Ort gefunden", "Es wurde kein passender Ort gefunden.", WorkflowUtil::getImage( "icon.png" ) );
		}

		return $response;
	}

	public static function getConnections( $from, $to, $withToInSubtext, $withFromInSubtext, $samePlace, &$response )
	{
		$badNames = array( "T" => "Tram", "B" => "Bus", "NFB" => "NF Bus", "NFT" => "NF Tram" );
		$class = trim( fgets( fopen( "class.txt", "r" ) ) );

		$conResponse = WorkflowUtil::request( "http://transport.opendata.ch/v1/connections?limit=6&from=".urlencode( $from )."&to=".urlencode( $to ) );
		$connections = json_decode( $conResponse, true );

		if( strtolower( $from ) != strtolower( $to ) )
		{
			if( $connections[ "connections" ] && count( $connections[ "connections" ] ) > 0 )
			{
				$timezone = new DateTimeZone( "Europe/Zurich" );
				$now = new DateTime( null, $timezone );
				$nowFormatted = urlencode( $now->format( "Y-m-d\TH:i" ) );

				$connectionId = 1;
				$lastDepartureTime = null;
				foreach( $connections[ "connections" ] as $connection )
				{
					$departure = new DateTime( $connection[ "from" ][ "departure" ], $timezone );
					$arrival = new DateTime( $connection[ "to" ][ "arrival" ], $timezone );

					$relDeparture = $now->diff( $departure );
					$duration = $departure->diff( $arrival );

					$minDeparture = intval( $relDeparture->format( "%i" ) );
					if( intval( $relDeparture->format( "%h" ) ) > 0 )
					{
						$departureText = "in ".$relDeparture->format( "%h:%I" )." Stunden";
					}
					else if( $minDeparture === 0 )
					{
						$departureText = "jetzt";
					}
					else if( $minDeparture === 1 )
					{
						$departureText = "in einer Minute";
					}
					else
					{
						$departureText = "in ".$minDeparture." Minuten";
					}

					if( !empty( $connection[ "from" ][ "prognosis" ][ "departure" ] ) )
					{
						$newDeparture = new DateTime( $connection[ "from" ][ "prognosis" ][ "departure" ], $timezone );
						$departureText .= " (+" . WorkflowUtil::formatTimeDiff( $newDeparture->diff( $departure ), " h", " min" ) . ")";
					}

					$estimation = $class === 2 ? $connection[ "capacity2nd" ] : $connection[ "capacity1st" ];
					if( $estimation && $estimation > 0 )
					{
						$departureText .= "   ";
						for( $i = 0; $i < 3; $i++ )
						{
							$departureText .= $connection[ "capacity2nd" ] > $i ? "●" : "○";
						}
					}

					$subtext = "um " . $departure->format( "H:i" ) . " Uhr";

					if( $connection[ "from" ][ "platform" ] != null )
					{
						$subtext .= " ab Gleis ";

						if( !empty( $connection[ "from" ][ "prognosis" ][ "platform" ] ) )
						{
							$subtext .= $connection[ "from" ][ "prognosis" ][ "platform" ] . " statt ";
						}

						$subtext .= $connection[ "from" ][ "platform" ];

					}

					$subtext .= ", an " . $arrival->format( "H:i" )." Uhr";
					if( !empty( $connection[ "to" ][ "prognosis" ][ "arrival" ] ) )
					{
						$newArrival = new DateTime( $connection[ "to" ][ "prognosis" ][ "arrival" ], $timezone );
						$subtext .= " (+" . WorkflowUtil::formatTimeDiff( $newArrival->diff( $arrival ), " h", " min" ) . ")";
					}

					$subtext .= ", dauert " . WorkflowUtil::formatTimeDiff( $duration );

					$subtext .= ( $withFromInSubtext ? " von " . $connection[ "from" ][ "station" ][ "name" ] : "" )
							. ( $withToInSubtext ? " nach " . $connection[ "to" ][ "station" ][ "name" ] : "" );

					if( count( $connection[ "sections" ] ) > 0 )
					{
						$subtext .= ", mit";
						$i = 0;
						$total = count( $connection[ "sections" ] );
						foreach( $connection[ "sections" ] as $section )
						{
							if( $section[ "journey" ][ "category" ] )
							{
								if( $i > 0 )
								{
									$subtext .= $i < $total - 1 ? ", " : " und ";
								}
								else
								{
									$subtext .= " ";
								}

								if( array_key_exists( $section[ "journey" ][ "category" ], $badNames ) )
								{
									$subtext .= $badNames[ $section[ "journey" ][ "category" ] ] . " " . $section[ "journey" ][ "number" ];
								}
								else
								{
									$subtext .= $section[ "journey" ][ "category" ];
								}

								$i++;
							}
							else
							{
								$total--;
							}
						}
					}

					$subtext .= ".";

					$id = $connection[ "from" ][ "station" ][ "id" ] . "-" . $connection[ "to" ][ "station" ][ "id" ] . "-" .$connection[ "from" ][ "departure" ];
					// $id = time() . rand();
					$url = "/to/" . urlencode( $connection[ "to" ][ "station" ][ "name" ] ) . "/from/" . urlencode( $connection[ "from" ][ "station" ][ "name" ] ) . "/at/" . $nowFormatted . "?page=" . floor( $connectionId / 4 ) . "&c=" . ( $connectionId % 4 );
					$response->add( $id, $url, $departureText, $subtext, WorkflowUtil::getImage( "arrow.png" ) );

					// if there are more than 4 connections per page on trnsprt.ch, count these with the same departure time as one.
					if( $lastDepartureTime != $connection[ "from" ][ "departure" ] )
					{
						$connectionId++;
					}
					$lastDepartureTime = $connection[ "from" ][ "departure" ];
				}
			}
			else
			{
				$response->add( "nothing", $orig, "Keine Verbindungen", "Leider konnten keine Verbindungen gefunden werden.", WorkflowUtil::getImage( "icon.png" ) );
			}
		}
		else
		{
			$response->add( "nothing", $orig, "Du befindest dich schon hier.", $from . " ist bereits dein Startbahnhof. ".$samePlace, WorkflowUtil::getImage( "icon.png" ) );
		}
	}
}