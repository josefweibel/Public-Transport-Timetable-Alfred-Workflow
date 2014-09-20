<?php
namespace Dispatcher;

include( "src/Initializer.php" );

use Utils\Response;
use TimeKeywords\TimeKeywordManager;
use Utils\TransportUtil;
use Utils\WorkflowUtil;
use Utils\I18N\I18NUtil;

/**
 * Handles the from-to-action. This script will give suggestions for the stations and if there are three points " ..." at the end of the query, it will return the requested connections.
 * Expample: php -f fromto.php "Bern nach HB"
 * @param [start station] ["nach"] [destination station] [" ..."].
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */

$response = new Response();
$dictionary = I18NUtil::getDictionary();
$query = WorkflowUtil::normalize( $argv[1] );

$fromEnd = strripos( $query, " nach " );
if( $fromEnd === false )
{
	$from = trim( $query );
	if( empty( $from ) )
	{
		$response->add( "nothing", "nothing", $dictionary->get( "fromto.nofrom-title" ),
					$dictionary->get( "fromto.nofrom-subtitle" ), WorkflowUtil::getImage( "icon.png" ), 'no' );
	}
	else
	{
		TransportUtil::addLocations( $response, $from, "", "fromto.departure-subtitle", null, true, "", " nach " );
	}
}
else
{
	$realquery = $query;

	$timeKeyword = TimeKeywordManager::getTimeKeyword( $query );
	if( $timeKeyword )
	{
		$query = $timeKeyword->removeTimeKeyword( $query );
	}

	$fromHuman = trim( substr( $argv[1], 0, strripos( $argv[1], " nach " ) ) );
	$from = trim( substr( $query, 0, $fromEnd ) );
	$to = trim( substr( $query, $fromEnd + 6 ) );

	if( !$timeKeyword )
	{
		if( empty( $to ) )
		{
			$response->add( "nothing", "nothing", $dictionary->get( "fromto.noto-title" ),
					$dictionary->get( "fromto.noto-subtitle" ), WorkflowUtil::getImage( "icon.png" ), 'no' );
		}
		else
		{
			TransportUtil::addHomeLocation( $response, $to, true, $fromHuman . " nach ",
					" " . TimeKeywordManager::getDefaultTimeKeyword() );
			TransportUtil::addLocations( $response, $to, $from,
					"fromto.arrival-subtitle", array( "start" => $from ), true,
					$fromHuman . " nach ", " " . TimeKeywordManager::getDefaultTimeKeyword() );
		}
	}
	else
	{
		$to = TransportUtil::getStationForHome( $to );
		TransportUtil::addConnections( $response, $from, $to,
				$timeKeyword->getTime( $realquery ), false, false );
	}
}

echo $response->export();