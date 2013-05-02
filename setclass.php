<?php
require_once( "response.php" );
require_once( "workflowutil.php" );

$response = new Response();
$response->add( "second", "2", "2. Klasse", "Auswählen um die 2. Klasse als Standard zu verwenden.", WorkflowUtil::getImage( "icon.png" ) );
$response->add( "first", "1", "1. Klasse", "Auswählen um die 1. Klasse als Standard zu verwenden.", WorkflowUtil::getImage( "icon.png" ) );
echo $response->export();