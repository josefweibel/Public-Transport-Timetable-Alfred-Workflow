<?php
require_once( "workflowutil.php" );
require_once( "response.php" );
$response = new Response();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );

$json = WorkflowUtil::request( "http://transport.opendata.ch/v1/locations?query=". urlencode( $query ) );
$result = json_decode( $json, true );
$stations = $result[ "stations" ];

foreach( $stations as $station )
{
	if( $station[ "type" ] == "station" )
	{
		$icon = "station.png";
		$placename = "Station";
	}
	else if( $station[ "type" ] == "address" )
	{
		$icon = "address.png";
		$placename = "Adresse";
	}
	else
	{
		$icon = "station.png";
		if( $station[ "type" ] == "poi" )
		{
			$icon = "poi.png";
		}

		$placename = "Ort";
	}

	$response->add( $station[ "id" ], $station[ "name" ], $station[ "name" ], "Diesen " . $placename. " als neuen Startpunkt verwenden.",  WorkflowUtil::getImage( $icon ) );
}

if( count( $stations ) == 0 )
{
	$response->add( "nothing", $orig, "Kein Bahnhof gefunden", "Es wurde kein passender Bahnhof gefunden. Versuches es erneut.", WorkflowUtil::getImage( "icon.png" ) );
}

echo $response->export();