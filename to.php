<?php

require_once( "workflowutil.php" );
require_once( "response.php" );
require_once( "transportutil.php" );
$response = new Response();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );
$isTo = $argv[2];

$home = WorkflowUtil::getValue( "config", "home" );
$normHome = WorkflowUtil::normalize( $home );

if( empty( $home ) )
{
	$response->add( "nothing", $orig, "Du hast noch keine Heimstation festgelegt.", "Ändere das, indem du 'fahrplan set' in Alfred tippst. Alternativ kannst du auch mit 'von' eine Suche mit Startstation machen.", WorkflowUtil::getImage( "icon.png" ) );
}
else if( substr( $query, -4 ) === " ..." )
{
	$query = substr( $query, 0, strlen( $query ) - 4 );
	TransportUtil::getConnections( $isTo ? $normHome : $query, $isTo ? $query : $normHome, !$isTo, $isTo, "", $response );
}
else if( $isTo )
{
	TransportUtil::getLocations( $query, $normHome, "Verbindungen von " . $home . " nach ", " anzeigen.", "", " ...", $response );
}
else
{
	TransportUtil::getLocations( $normHome, $query, "Verbindungen von ", " nach " . $home . " anzeigen.", "", " ...", $response );
}

echo $response->export();