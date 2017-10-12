<?php
namespace Dispatcher;

require_once( "src/Initializer.php" );

use Utils\Response;
use TimeKeywords\TimeKeywordManager;
use Utils\TransportUtil;
use Utils\WorkflowUtil;
use Utils\I18N\I18NUtil;

/**
 * Handles the take-me-home- and the to-action.
 * Expample: php -f to.php "Bern" 1
 * @param String start- or destination name. If there are three points " ..." at the end, the script will return the connections, otherwise suggestions for the station.
 * @param int 1 if the given station is the destination station, 0 if it is the start station.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */

$response = new Response();
$dictionary = I18NUtil::getDictionary();

$query = WorkflowUtil::normalize( trim( $argv[1] ) );
$isTo = $argv[2];

$home = WorkflowUtil::getValue( "config", "home" );
$normHome = WorkflowUtil::normalize( $home );

$timeKeyword = TimeKeywordManager::getTimeKeyword( $query );

if( empty( $home ) )
{
	$response->add( "nothing", "nothing", $dictionary->get( "errors.nohomestation-title" ),
			$dictionary->get( "errors.nohomestation-subtitle" ), WorkflowUtil::getImage( "icon.png" ) );
}
else if( $timeKeyword )
{
	$station = $timeKeyword->removeTimeKeyword( $query );
	TransportUtil::addConnections( $response, $isTo ? $normHome : $station,
			$isTo ? $station : $normHome, $timeKeyword->getTime( $query ),
			!$isTo, $isTo );
}
else
{
	TransportUtil::addLocations( $response, $query, $normHome,
			$isTo ? "to.to-subtitle" : "to.from-subtitle", array( "home" => $home ), true, "to.fullquery",
			array( "timekeyword" => TimeKeywordManager::getDefaultTimeKeyword() ) );
}

echo $response->export();