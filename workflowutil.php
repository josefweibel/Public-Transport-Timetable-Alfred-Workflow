<?php

class WorkflowUtil
{
	/**
	 * Bundle ID which was set in the workflow configuration of Alfred.
	 * Used to determinate the application support folder.
	 */
	private static $bundle = "ch.josefweibel.alfred.publictransport";

	/**
	 * Makes a simple http(s) request with curl.
	 * @param url the request url.
	 * @param options some options for the request. http://php.net/manual/en/function.curl-setopt.php
	 * @return the response of the request as string.
	 */
	public static function request( $url, $options = null )
	{
		$defaults = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL => $url,
			CURLOPT_FRESH_CONNECT => true
		);

		if ( is_array( $options ) )
		{
			$defaults = array_merge( $defaults, $options );
		}

		$ch  = curl_init();
		curl_setopt_array( $ch, $defaults );
		$out = curl_exec( $ch );
		curl_close( $ch );

		return $out;
	}

	/**
	 * Normalizes the given string. It removes the special characters like '^' or '¨' which Alfred gives specially.
	 * @param someString string which should normalized.
	 * @return normalized string.
	 */
	public static function normalize( $someString )
	{
		return preg_replace( "/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s", "", $someString );
	}

	/**
	 * @return the correct path to the given img file name.
	 */
	public static function getImage( $img )
	{
		return "img/" . $img;
	}

	/**
	 * Returns a human readable time string for the given time difference.
	 * Supports only minutes and hours.
	 * @param diff DateInterval the differece which should be beautified.
	 * @param name for 1 hour. default is " Stunde". Please include the first space if neccessary.
	 * @param name for multiple hours. default is " Stunden". Please include the first space if neccessary.
	 * @param name for minutes. default is " Minuten". Please include the first space if neccessary.
	 * @return a beautiful time string.
	 */
	public static function formatTimeDiff( $diff, $hour = " Stunde", $hours = " Stunden", $min = " Minuten" )
	{
		if( intval( $diff->format( "%h" ) ) > 1 )
		{
			return $diff->format( "%h:%I" ).$hours;
		}
		else if( intval( $diff->format( "%h" ) ) === 1 )
		{
			return $diff->format( "%h:%I" ).$hour;
		}
		else
		{
			return $diff->format( "%i" ).$min;
		}
	}

	public static function getFromCacheOrHttp( $filename, $ttl, $url )
	{
		if( file_exists( $filename ) && time() - filemtime( $filename ) < $ttl )
		{
			return file_get_contents( $filename );
		}
		else
		{
			$content = self::request( $url );
			file_put_contents( $filename, $content );
			return $content;
		}
	}

	private static function getFilename( $filename )
	{
		$file = exec( 'printf $HOME' ) . "/Library/Application Support/Alfred 2/Workflow Data/" . self::$bundle . "/";

		if ( !file_exists( $file ) )
		{
			exec( "mkdir '" . $file . "'" );
		}

		return $file . $filename . ".json";
	}

	private static function getContentOfFile( $filename )
	{
		$file = self::getFilename( $filename );
		if( file_exists( $file ) )
		{
			$content = json_decode( file_get_contents( $file ), true );
		}

		if( !is_array( $content ) )
		{
			$content = array();
		}

		return $content;
	}

	public static function setValue( $filename, $values, $override = false )
	{
		$file = self::getFilename( $filename );

		if( !$override )
		{
			$values = array_merge( self::getContentOfFile( $filename ), $values );
		}

		file_put_contents( $file, json_encode( $values) );
	}

	public static function getValue( $filename, $key )
	{
		$content = self::getContentOfFile( $filename );
		return $content[ $key ];
	}
}