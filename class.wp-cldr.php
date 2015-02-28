<?php

/**
* A class for fetching a bucketed key value store of localized data from extracts of Unicode's Common Locale Data Repository.
*
* Examples:
*
* // The default locale is English
* $cldr = new WP_CLDR();
* $territories_in_english = $cldr->territories_by_locale( 'en' );
*
* // You can override the default locale per-call by passing in a language slug in the second parameter.
* $germany_in_arabic = $cldr->_territory( 'DE' , 'ar' );
*
* // use a convenience parameter during instantiation to change the default locale
* $cldr = new WP_CLDR( 'fr' );
* $germany_in_french = $cldr->_territory( 'DE' );
* $us_dollar_in_french = $cldr->_currency( 'USD' );
* $canadian_french_in_french = $cldr->_locale( 'fr-ca' );
* $canadian_french_in_english = $cldr->_locale( 'fr-ca', 'en' );
* $africa_in_french = $cldr->_region( '002' );
*
* // switch locales after the object has been created
* $cldr->set_locale('en')
* $us_dollar_in_english = $cldr->_currency( 'USD' );
*/

class WP_CLDR {
	private $locale = 'en';
	private $localized = array();

	const CACHE_GROUP = 'wp-cldr';

	public function __construct( $locale = 'en' ) {
		$this->set_locale( $locale );
	}

	public function set_locale( $locale ) {
		if ( $locale === $this->locale && isset( $this->localized[ $locale ] ) && ! empty( $this->localized[ $locale ] ) ) {
			// No need to do duplicate work when setting the same locale repeatedly
			return;
		}
		$this->locale = $locale;
		$this->initialize_locale( $locale );
	}

	public function initialize_locale( $locale = 'en', $use_cache = true ) {
		if ( $use_cache ) {
			$cache_key = 'cldr-localized-names-' . $locale;

			$cached_data = wp_cache_get( $cache_key, WP_CLDR::CACHE_GROUP );

			if ( $cached_data ) {
				$this->localized[ $locale ] = $cached_data;
				return true;
			}
		}

		$data_file_name = WP_CLDR::data_file_name( $locale );
		if ( ! file_exists( $data_file_name ) ) {
			return false;
		}

		// Ideally this would just be more API instead of requiring a file
		require $data_file_name;

		$this->localized[ $locale ] = (object) compact(
			'territory_names',
			'currency_names',
			'locale_names',
			'region_names'
		);

		if ( $use_cache ) {
			wp_cache_set( $cache_key, $this->localized[ $locale ], WP_CLDR::CACHE_GROUP );
		}
		return true;
	}


	/**
	* Helper function to get CLDR code for a given WordPress locale code
	*
	* @param string $wp_locale The WordPress locale
	* @return string The CLDR locale code, if different, or if not the original WordPress locale code
	*/
	public function get_CLDR_locale( $wp_locale ) {

		$wp2cldr =  array(
			'pt-br' => 'pt-BR',
			'zh-cn' => 'zh',
			'zh-tw' => 'zh-Hant',
			'pt'	=> 'pt-PT',
			'fr-ca' => 'fr-CA',			
			);
		
		if ( isset( $wp2cldr[$wp_locale] ) ) {
			return $wp2cldr[$wp_locale];
		} else {
			return $wp_locale;
		}
	}

	/**
	* Helper function to get CLDR data for a particular locale and bucket.
	*
	* @param string $locale The locale for the CLDR data request
	* @param string $bucket The bucket for the CLDR data request
	* @return array $bucket_array the CLDR data for the locale and bucket, or English if no match with any CLDR data files
	*/
	public function get_CLDR_data( $locale, $bucket ) {

		$CLDR_locale = $this->get_CLDR_locale($locale);

		$dir = __DIR__;
		$data_file_name = "$dir/cldr/main/" . $CLDR_locale . '/' . $bucket . '.json';

		if ( ! file_exists( $data_file_name ) ) {
			$data_file_name = "$dir/cldr/main/en/" . $bucket . '.json'; 
			a8c_slack('@stuwest', 'bad locale code "' . $locale . '" passed to wp-cldr. "en" used instead.');
			$CLDR_locale = 'en';
		}

		$json_raw = file_get_contents( $data_file_name );
		$json_decoded = json_decode( $json_raw, true );

		switch( $bucket ) {
		    case 'territories':
		    case 'languages':
				$bucket_array = $json_decoded['main'][$CLDR_locale]['localeDisplayNames'][$bucket];
				// sort data according to locale collation rules
				$coll = collator_create( $CLDR_locale );
				collator_asort($coll, $bucket_array, Collator::SORT_STRING ); 
				break;
			case 'currencies':
				$bucket_array = $json_decoded['main'][$CLDR_locale]['numbers'][$bucket];
		}

		return $bucket_array;
	}

	/**
	* Helper function to get CLDR extract filename for a particular locale.
	*
	* @param string $locale The locale for the filename
	* @return string $input the appropriate CLDR extract filename or English if no match with $locale
	*/
	private static function data_file_name( $locale ) {
		$dir = __DIR__;
		$input = "$dir/cldr/cldr-" . $locale . '.php';
		if ( ! file_exists( $input ) ) {
			$input = "$dir/cldr/cldr-en.php";  // may add some logging here to catch errors in language codes 
		}
		return $input;
	}

	public function flush_wp_cache_for_locale( $locale ) {
		$cache_key = 'cldr-localized-names-' . $locale;
		return wp_cache_delete( $cache_key, WP_CLDR::CACHE_GROUP );
	}

	/**
	* Run this to force clear caches for all locales we know about
	*/
	public function flush_all_wp_caches() {
		$this->localized = array();

		// Initialize without the cache
		$this->initialize_locale( 'en', false );

		$locales = $this->locales_by_locale( 'en' );
		foreach( array_keys( $locales ) as $locale ) {
			$this->flush_wp_cache_for_locale( $locale );
		}
	}

	/**
	* Return all the data for a given locale 
	* @param  string $locale (optional) Which locale's strings to return.
	*                           Defaults to the current locale (which defaults to English).
	* @return object            Values for keys initialized for a particular locale
	*/
	public function get_localized_names( $locale = null ) {
		if ( ! $locale ) {
			$locale = $this->locale;
		}

		if ( isset( $this->localized[ $locale ] ) ) {
			return (object) $this->localized[ $locale ];
		}

		// Maybe that locale hasn't been initialized yet, let's try again:
		$this->initialize_locale( $locale );

		if ( isset( $this->localized[ $locale ] ) ) {
			return (object) $this->localized[ $locale ];
		}

		// Really not found
		return new StdClass();
	}

	/**
	* Get the localized value for a particular key in a particular bucket
	* @param  string $key       The individual item's id / stub.
	*                           For example: US, FR, DE
	* @param  string $bucket    (optional) In which group of data to look for the key
	*                           Defaults to 'territory_names'
	* @param  string $locale (optional)
	* @return string            The localized string
	*/
	public function __( $key, $bucket = 'territory_names', $locale = null ) {
		if ( ! is_string( $key ) || ! strlen( $key ) ) {
			return '';
		}

		$names = $this->get_localized_names( $locale );
		$bucket = $names->{$bucket};

		if ( isset( $bucket[ $key ] ) ) {
			return (string) $bucket[ $key ];
		}
	}

	/**
	* Helpers to more easily access by bucket
	*/
	public function _territory( $cldr_territory_code, $locale = null ) {
		return $this->__( $cldr_territory_code, 'territory_names', $locale );
	}

	public function _region( $cldr_region_code, $locale = null ) {
		return $this->__( $cldr_region_code, 'region_names', $locale );
	}

	public function _currency( $cldr_currency_code, $locale = null ) {
		return $this->__( $cldr_currency_code, 'currency_names', $locale );
	}

	public function _locale( $cldr_locale_code, $locale = null ) {
		return $this->__( $cldr_locale_code, 'locale_names', $locale );
	}

	/**
	* Get territory names localized for a particular locale.
	*
	* @param string $locale The locale to return the list in
	* @return array an associative array of ISO 3166-1 alpha-2 territory codes and localized territory names from CLDR
	*/
	public function territories_by_locale( $locale = null ) {
		$names = $this->get_localized_names( $locale );
		return $names->territory_names;
	}

	/**
	* Get region names localized for a particular locale.
	*
	* @param string $locale The locale to return the list in
	* @return array an associative array of UN M.49 region codes and localized region names from CLDR
	*/
	public function regions_by_locale( $locale = null ) {
		$names = $this->get_localized_names( $locale );
		return $names->region_names;
	}

	/**
	* Get locale names localized for a particular locale.
	*
	* @param string $locale The locale to return the list in
	* @return array an associative array of ISO 639 locale codes and localized locale names from CLDR
	*/
	public function locales_by_locale( $locale = null ) {
		$names = $this->get_localized_names( $locale );
		return $names->locale_names;
	}

	/**
	* Get currency names localized for a particular locale.
	*
	* @param string $locale The locale to return the list in
	* @return array an associative array of ISO 4217 alpha codes and localized currency names from CLDR
	*/
	public function currencies_by_locale( $locale = null ) {
		$names = $this->get_localized_names( $locale );
		return $names->currency_names;
	}
}