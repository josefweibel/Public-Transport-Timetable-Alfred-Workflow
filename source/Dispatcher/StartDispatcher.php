<?php

use Utils\Response;
use Utils\TransportUtil;
use Utils\WorkflowUtil;

require_once 'source/Utils/Response.php';
require_once 'source/Utils/TransportUtil.php';
require_once 'source/Utils/WorkflowUtil.php';

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */

$response = new Response();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );
TransportUtil::addLocations( $response, $query, "", "", " als Heimort festlegen." );

echo $response->export();