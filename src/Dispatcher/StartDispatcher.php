<?php

use Utils\Response;
use Utils\TransportUtil;
use Utils\WorkflowUtil;

require_once 'src/Utils/Response.php';
require_once 'src/Utils/TransportUtil.php';
require_once 'src/Utils/WorkflowUtil.php';

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */

$response = new Response();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );
TransportUtil::addLocations( $response, $query, "", "", " als Heimort festlegen." );

echo $response->export();