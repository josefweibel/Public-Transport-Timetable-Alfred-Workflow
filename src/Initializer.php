<?php

if( !function_exists( "autoloader" ) )
{
	function autoloader( $class )
	{
		$path = realpath( dirname( __FILE__ ) ) . '/' . str_replace( '\\', '/', $class ) . '.php';
		if( file_exists( $path ) )
		{
	    	require_once( $path );
		}
	}

	spl_autoload_register( "autoloader" );
}
