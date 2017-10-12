<?php
namespace Dispatcher;

include_once( "src/Initializer.php" );

use Utils\Response;
use Utils\WorkflowUtil;
use Utils\I18N\I18NUtil;

/**
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */

$dictionary = I18NUtil::getDictionary();
$response = new Response();

foreach( array( 2, 1 ) as $class )
{
	$response->add( $class, $class, $dictionary->get( "classselector.title", array( "class" => $class ) ),
		$dictionary->get( "classselector.subtitle", array( "class" => $class ) ),
		WorkflowUtil::getImage( "icon.png" ) );
}

echo $response->export();
