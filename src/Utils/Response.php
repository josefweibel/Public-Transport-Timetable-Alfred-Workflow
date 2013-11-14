<?php
namespace Utils;

use \SimpleXMLElement;

/**
 * Result of a request. Generates a Alfred valid XML.
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class Response
{
	/**
	 * The array with all results. One result is one subarray which contains key-value-pairs.
	 * @var array with arrays 
	 */
	private $results = array();

	/**
	 * Adds a new result to the response.
	 * @param string $uid an unique id for this result. 
	 *		  Alfred sorts the result by amount of selection.
	 * @param string $arg the value which will transmitted to the next 
	 *		  item in this workflow if the user selects this result.
	 * @param string $title the great title
	 * @param string $sub the little subtitle.
	 * @param string $icon the filename of an icon. If empty, the default workflow icon will used.
	 *		  You can also set a filetype.
	 * @param string $valid yes or no. Default yes. If 'no' you have to set the $auto param.
	 *		  If the user selects result the $auto content will written into the Alfredbox.
	 * @param string $auto default null. See $valid param.
	 * @param string $type default null. If you set 'file' than the result represents a file
	 *		  and Alfred will show the user actions for the file.
	 * @return array the added result.
	 * @see http://www.alfredforum.com/topic/5-generating-feedback-in-workflows/
	 */
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

	/**
	 * Casts the $results to an Alfred-valid xml.
	 * @return string a valid xml.
	 */
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