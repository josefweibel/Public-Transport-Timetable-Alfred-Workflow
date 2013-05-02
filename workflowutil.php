<?php

class WorkflowUtil
{
	public static function request( $url, $options = null )
	{
		$defaults = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL => $url,
			CURLOPT_FRESH_CONNECT => true
		);

		if ( $options )
		{
			foreach( $options as $key => $value )
			{
				$defaults[ $key ] = $valuev;
			}
		}

		$ch  = curl_init();
		curl_setopt_array( $ch, $defaults );
		$out = curl_exec( $ch );
		curl_close( $ch );

		return $out;
	}

	public static function normalize( $someString )
	{
		return preg_replace( "/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s", "", $someString );
	}

	public static function getImage( $img )
	{
		return "img/" . $img;
	}

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
}