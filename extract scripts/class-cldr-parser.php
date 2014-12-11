<?php

class CLDR_Parser {

	/** This class parses a single CLDR file into the WordPress.com extract format.
	 * @param string $inputFile filename
	 * @param string $outputFile filename
	 *
	 * H/T to https://github.com/wikimedia/mediawiki-extensions-cldr/blob/master/rebuild.php for XML parsing logic
	 */

	function parse( $inputFile, $outputFile, $locale ) {

		$contents = file_get_contents( $inputFile );
		$doc = new SimpleXMLElement( $contents );
		$coll = collator_create( $locale );


		$data = array(
			'territory_names' => array(),
			'locale_names' => array(),
			'currency_names' => array(),
			'currency_symbols' => array(),
			'time_zone_city_names' => array(),
			'time_zone_metazone' => array(),
		);

		foreach ( $doc->xpath( '//territories/territory' ) as $elem ) {

			//	includes alternate versions of names; could also exclude
			if ( (string)$elem['alt'] !== '' ) {
				$data['territory_names'][(string)$elem['type'] . '-alt-' . (string)$elem['alt']] = (string)$elem;
			} else {
				$data['territory_names'][(string)$elem['type']] = (string)$elem;
			}

		}

		// resorts the array based on name (not code), using the locale string sorting order
		collator_asort($coll, $data['territory_names'], Collator::SORT_STRING );
		
		foreach ( $doc->xpath( '//languages/language' ) as $elem ) {
			if ( (string)$elem['alt'] !== '' ) {
				continue;
			}

			if ( (string)$elem['type'] === 'root' ) {
				continue;
			}

			// converts locale code from underscore used by CLDR to dash used by wpcom_to_cldr
			$key = str_replace( '_', '-', strtolower( $elem['type'] ) );

			$data['locale_names'][$key] = (string)$elem;
		}

		// resorts the array based on name (not code), using the locale string sorting order
		collator_asort($coll, $data['locale_names'], Collator::SORT_STRING );
		
		foreach ( $doc->xpath( '//currencies/currency' ) as $elem ) {
			if ( (string)$elem->displayName[0] === '' ) {
				continue;
			}

			$data['currency_names'][(string)$elem['type']] = (string)$elem->displayName[0];
			if ( (string)$elem->symbol[0] !== '' ) {
				$data['currency_symbols'][(string)$elem['type']] = (string)$elem->symbol[0];
			}
		}

		// resorts the array based on name (not code), using the locale string sorting order
		collator_asort($coll, $data['currency_names'], Collator::SORT_STRING );
		
		foreach ( $doc->xpath( '//timeZoneNames/zone' ) as $elem ) {
			$data['time_zone_city_names'][(string)$elem['type']] = (string)$elem->exemplarCity[0];
		}

		foreach ( $doc->xpath( '//timeZoneNames/metazone' ) as $elem ) {
			$data['time_zone_metazone'][(string)$elem['type']] = (string)$elem->long->standard[0];
		}

		$this->save_php( $data, $outputFile, $inputFile );
		echo "$outputFile saved<br>";
	}

	/**
	 * save_php will build and return a string containing nicely formatted php
	 * output of all the vars we've just parsed out of the xml.
	 * @param array $data The variable names and values we want defined in the php output
	 * @param string $location File location to write
	 */
	function save_php( $data, $location, $source_file ) {
		$output = "<?php\n\n//Extracted from Unicode CLDR file " . substr( $source_file , 25 ) . " by @stuwest on " . date('j F Y') . "\n";
		// could have just used var_export but it's so ugly
		foreach ( $data as $varname => $values ) {
			$output .= "\n\$$varname = array(\n";
			foreach ( $values as $key => $value ) {
					// need to escape single quote here because of use in names e.g. Cote d'Ivoire
					$key = addcslashes( $key, "'" );
					$value = addcslashes( $value, "'" );
// may uncomment next 2 lines if prefer to have regions codes be numbers instead of strings	
//					if ( ! is_numeric( $key ) ) {
						$key = "'$key'";
//					}
					$output .= "\t$key => '$value',\n";
			}

			$output .= ");\n";
		}

		$output .= "?>";

		file_put_contents( $location, $output );
	}
}
?>