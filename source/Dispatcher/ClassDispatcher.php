<?php

use Utils\Response;
use Utils\WorkflowUtil;

require_once 'source/Utils/Response.php';
require_once 'source/Utils/WorkflowUtil.php';

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */

$response = new Response();
$response->add( "second", "2", "2. Klasse", 
		"Auswählen um die 2. Klasse als Standard zu verwenden.", 
		WorkflowUtil::getImage( "icon.png" ) );

$response->add( "first", "1", "1. Klasse", 
		"Auswählen um die 1. Klasse als Standard zu verwenden.", 
		WorkflowUtil::getImage( "icon.png" ) );

echo $response->export();