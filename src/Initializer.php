<?php

if( !function_exists( "autoloader" ) )
{
	function autoloader( $class )
	{
	    require_once( realpath( dirname( __FILE__ ) ) . '/' . str_replace( '\\', '/', $class ) . '.php' );
	}

	spl_autoload_register( "autoloader" );
}
