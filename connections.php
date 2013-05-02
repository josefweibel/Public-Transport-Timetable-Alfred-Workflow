<?php

require_once( "workflowutil.php" );
require_once( "response.php" );
require_once( "transportutil.php" );
$response = new Response();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );
$startStation = trim( fgets( fopen( "start.txt", "r" ) ) );
$normStart = WorkflowUtil::normalize( $startStation );

if( substr( $query, -3 ) === "..." )
{
	$query = substr( $query, 0, strlen( $query ) - 3 );
	if( strtolower( $normStart ) != strtolower( $query ) )
	{
		TransportUtil::getConnections( $normStart, $query, false, true, "", $response );
	}
	else
	{
		$response->add( "nothing", $orig, "Du befindest dich schon hier", $startStation . " ist dein Startbahnhof. Du kannst ihn aber jederzeit mit 'fahrplan set' ändern.", WorkflowUtil::getImage( "icon.png" ) );
	}
}
else
{
	TransportUtil::getLocations( $query, $normStart, "Auswählen um die nächsten Verbindungen für ", " zu sehen.", "", " ...", $response );
}

echo $response->export();