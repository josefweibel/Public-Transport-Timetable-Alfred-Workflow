<?php

/**
 * Handles the from-to-action. This script will give suggestions for the stations and if there are three points " ..." at the end of the query, it will return the requested connections.
 * Expample: php -f fromto.php "Bern nach HB"
 * @param [start station] ["nach"] [destination station] [" ..."].
 */

require_once( "workflowutil.php" );
require_once( "response.php" );
require_once( "transportutil.php" );

$response = new Response();
$query = WorkflowUtil::normalize( $argv[1] );

$toEnd = false;
$fromEnd = strripos( $query, " nach " );

if( $fromEnd === false )
{
	$from = trim( $query );
	if( empty( $from ) )
	{
		$response->add( "nothing", $orig, "Wo bist du?", "Einfach mit tippen beginnen ...", WorkflowUtil::getImage( "icon.png" ) );
	}
	else
	{
		TransportUtil::getLocations( $from, "", "", " als Abfahrtsort verwenden.", "", " nach ", $response );
	}
}
else
{
	$fromHuman = trim( substr( $argv[1], 0, strripos( $argv[1], " nach " ) ) );
	$from = trim( substr( $query, 0, $fromEnd ) );
	$toEnd = strripos( $query, "..." );

	if( $toEnd === false )
	{
		$to = trim( substr( $query, $fromEnd + 6 ) );
		if( empty( $to ) )
		{
			$response->add( "nothing", $orig, "Wo willst du hin?", "Du bist nicht mehr weit von deinen Verbindungen entfernt.", WorkflowUtil::getImage( "icon.png" ) );
		}
		else
		{
			TransportUtil::getLocations( $to, $from, "Verbindungen von " . $from . " nach ", " anzeigen.", $fromHuman . " nach ", " ...", $response );
		}
	}
	else
	{
		$to = trim( substr( $query, $fromEnd + 6, strlen( $query ) - $fromEnd - 9 ) );
		TransportUtil::getConnections( $from, $to, false, false, "", $response );
	}
}

echo $response->export();