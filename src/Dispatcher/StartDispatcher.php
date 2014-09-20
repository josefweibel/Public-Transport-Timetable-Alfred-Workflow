<?php
namespace Dispatcher;

include( "src/Initializer.php" );

use Utils\Response;
use Utils\TransportUtil;
use Utils\WorkflowUtil;

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */

$response = new Response();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );
TransportUtil::addLocations( $response, $query, "", "startselector.subtitle" );

echo $response->export();