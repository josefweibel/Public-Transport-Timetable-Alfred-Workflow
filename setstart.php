<?php
require_once( "workflowutil.php" );
require_once( "response.php" );
require_once( "transportutil.php" );
$response = new Response();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );
TransportUtil::getLocations( $query, "", "", " als Heimort festlegen.", null, null, $response );

echo $response->export();