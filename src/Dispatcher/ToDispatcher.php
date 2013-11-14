<?php
namespace Dispatcher;

use Utils\Response;
use TimeKeywords\TimeKeywordManager;
use Utils\TransportUtil;
use Utils\WorkflowUtil;

require_once 'src/Utils/Response.php';
require_once 'src/TimeKeywords/TimeKeywordManager.php';
require_once 'src/Utils/TransportUtil.php';
require_once 'src/Utils/WorkflowUtil.php';


/**
 * Handles the take-me-home- and the to-action.
 * Expample: php -f to.php "Bern" 1
 * @param String start- or destination name. If there are three points " ..." at the end, the script will return the connections, otherwise suggestions for the station.
 * @param int 1 if the given station is the destination station, 0 if it is the start station.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */

$response = new Response();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );
$isTo = $argv[2];

$home = WorkflowUtil::getValue( "config", "home" );
$normHome = WorkflowUtil::normalize( $home );

$timeKeyword = TimeKeywordManager::getTimeKeyword( $query );

if( empty( $home ) )
{
	$response->add( "nothing", "nothing", "Du hast noch keine Heimstation festgelegt.",
			"Ändere das, indem du 'fahrplan set' in Alfred tippst. " .
			"Alternativ kannst du auch mit 'von' eine Suche mit Startstation machen.",
			WorkflowUtil::getImage( "icon.png" ) );
}
else if( $timeKeyword )
{
	$station = $timeKeyword->removeTimeKeyword( $query );
	TransportUtil::addConnections( $response, $isTo ? $normHome : $station,
			$isTo ? $station : $normHome, $timeKeyword->getTime( $query ),
			!$isTo, $isTo );
}
else if( $isTo )
{
	TransportUtil::addLocations( $response, $query, $normHome,
			"Verbindungen von " . $home . " nach ", " anzeigen.",
			true, "", " " . TransportUtil::KEYWORD_NOW );
}
else
{
	TransportUtil::addLocations( $response, $query, $normHome,
			"Verbindungen von ", " nach " . $home . " anzeigen.",
			true, "", " " . TransportUtil::KEYWORD_NOW );
}

echo $response->export();