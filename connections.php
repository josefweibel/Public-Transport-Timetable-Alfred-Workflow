<?php

require_once( "workflowutil.php" );
require_once( "response.php" );
require_once( "transportutil.php" );
$response = new Response();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );
$start = WorkflowUtil::getValue( "config", "home" );
$normStart = WorkflowUtil::normalize( $start );

if( empty( $start ) )
{
	$response->add( "nothing", $orig, "Du hast noch keine Heimstation festgelegt.", "Ändere das, indem du 'fahrplan set' in Alfred tippst. Alternativ kannst du auch mit 'von' eine Suche mit Startstation machen.", WorkflowUtil::getImage( "icon.png" ) );
}
else if( substr( $query, -4 ) === " ..." )
{
	$query = substr( $query, 0, strlen( $query ) - 4 );
	TransportUtil::getConnections( $normStart, $query, false, true, "", $response );
}
else
{
	TransportUtil::getLocations( $query, $normStart, "Verbindungen von " . $start . " nach ", " anzeigen.", "", " ...", $response );
}

echo $response->export();