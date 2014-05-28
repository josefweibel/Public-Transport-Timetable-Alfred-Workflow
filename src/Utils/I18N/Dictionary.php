<?php
namespace Utils\I18N;

/**
 * Contains translations.
 *
 * Usage:
 * 	$dictionary.get( "timekeywords.today" ); // returns the translation "today" in the group "timekeywords"
 *	$dictionary.get( "hello", array( "placeholder", "World" ) ); // replaces the "{placeholder}" in the file with "World"
 *
 * @author Josef Weibel <a href="http://www.josefweibel.ch">www.josefweibel.ch</a>
 */
class Dictionary
{
	/**
	 * Separator to access translations in groups.
	 */
	const GROUP_SEPARATOR = ".";

	/**
	 * The placeholders should start with this string.
	 */
	const PARAM_START = "{";

	/**
	 * The placeholders should end with this string.
	 */
	const PARAM_END = "}";

	/**
	 * Multidimensional array with key-value-pairs.
	 */
	private $translations;

	/**
	 * Creates a new instance of a dictionary with the given translations.
	 * @param $translations may not be null.
	 */
	public function __construct( $translations )
	{
		$this->translations = $translations;
	}

	/**
	 * Gets the translation for the key and replaces placeholders with the given values.
	 * @param $key the translation which should be returned. Can be dot-separated if you use groups in your dictionary.
	 * @param $params (optional) key-value-pairs to replace placeholders in the dictionary. Placeholders are wrapped with '{' and '}'.
	 * @return the translation or null if it wasn't found.
	 */
	public function get( $key, $params )
	{
		$keys = explode( self::GROUP_SEPARATOR, $key );

		$translation = $this->translations;
		foreach( $keys as $k )
		{
			$translation = $translation[ $k ];
		}

		if( $translation && $params )
		{
			$replace_pairs = array();

			// wrap placeholders with '{' and '}'.
			foreach( $params as $search => $replace )
			{
				$replace_pairs[ self::PARAM_START . $search . self::PARAM_END ] = $replace;
			}

			$translation = strtr( $translation, $replace_pairs );
		}

		return $translation;
	}
}