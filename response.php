<?php

/**
 * Result of a request.
 * Generates a Alfred valid XML.
 */
class Response
{
	private $results = array();

	public function add( $uid, $arg, $title, $sub, $icon, $valid='yes', $auto=null, $type=null )
	{
		$result = array(
			'uid' => $uid,
			'arg' => $arg,
			'title' => $title,
			'subtitle' => $sub,
			'icon' => $icon,
			'valid' => $valid,
			'autocomplete' => $auto,
			'type' => $type
		);

		if ( is_null( $type ) )
		{
			unset( $result['type'] );
		}

		array_push( $this->results, $result );

		return $result;
	}

	public function export()
	{
		$items = new SimpleXMLElement("<items></items>");

		foreach( $this->results as $result )
		{
			$item = $items->addChild( 'item' );
			$keys = array_keys( $result );

			foreach( $keys as $key )
			{
				if ( $key == 'uid' )
				{
					$item->addAttribute( 'uid', $result[$key] );
				}
				elseif ( $key == 'arg' )
				{
					$item->addAttribute( 'arg', $result[$key] );
				}
				elseif ( $key == 'type' )
				{
					$item->addAttribute( 'type', $result[$key] );
				}
				elseif ( $key == 'valid' )
				{
					if ( $result[$key] == 'yes' || $result[$key] == 'no' )
					{
						$item->addAttribute( 'valid', $result[$key] );
					}
				}
				elseif ( $key == 'autocomplete' )
				{
					$item->addAttribute( 'autocomplete', $result[$key] );
				}
				elseif ( $key == 'icon' )
				{
					if ( substr( $result[$key], 0, 9 ) == 'fileicon:' )
					{
						$val = substr( $result[$key], 9 );
						$item->$key = $val;
						$item->$key->addAttribute( 'type', 'fileicon' );
					}
					elseif ( substr( $result[$key], 0, 9 ) == 'filetype:' )
					{
						$val = substr( $result[$key], 9 );
						$item->$key = $val;
						$item->$key->addAttribute( 'type', 'filetype' );
					}
					else
					{
						$item->$key = $result[$key];
					}
				}
				else
				{
					$item->$key = $result[$key];
				}
			}
		}

		return $items->asXML();
	}
}