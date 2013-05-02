<?php
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
		$response->add( "nothing", $orig, "Bitte den Abfahrtsort eingeben.", "", WorkflowUtil::getImage( "icon.png" ) );
	}
	else
	{
		TransportUtil::getLocations( $from, "", "Auswählen um ", " als Abfahrtsort zu verwenden.", "", " nach ", $response );
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
			$response->add( "nothing", $orig, "Bitte den Zielort eingeben.", "", WorkflowUtil::getImage( "icon.png" ) );
		}
		else
		{
			TransportUtil::getLocations( $to, "", "Auswählen um ", " als Zielort zu verwenden.", $fromHuman . " nach ", " ...", $response );
		}
	}
	else
	{
		$to = trim( substr( $query, $fromEnd + 6, strlen( $query ) - $fromEnd - 9 ) );
		TransportUtil::getConnections( $from, $to, false, false, "", $response );
	}
}

echo $response->export();