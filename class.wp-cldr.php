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
* $us_dollar_in_french = $cldr->_currency_name( 'USD' );
* $canadian_french_in_french = $cldr->_language( 'fr-ca' );
* $canadian_french_in_english = $cldr->_language( 'fr-ca' , 'en' );
* $us_dollar_symbol_in_simplified_chinese = $cldr->_currency_symbol( 'USD', 'zh' );
* $africa_in_french = $cldr->_territory( '002' );
*
* // switch locales after the object has been created
* $cldr->set_locale('en');
* $us_dollar_in_english = $cldr->_currency_name( 'USD' );
*/

class WP_CLDR {
	private $locale = 'en';
	private $localized = array();

	const CACHE_GROUP = 'wp-cldr';

	public function __construct( $locale = 'en' ) {
		$this->set_locale( $locale );
	}

	public function set_locale( $locale ) {
		$this->locale = $locale;
		$this->initialize_locale( $locale );
	}

	/**
	* Helper function to get CLDR code for a given WordPress locale code
	*
	* @param string $wp_locale The WordPress locale
	* @return string The CLDR locale code, if different, or if not the original WordPress locale code
	*/
	public function get_CLDR_locale( $wp_locale ) {

		// this array captures the WordPress locales that are signficiantly different from CLDR locales
		$wp2cldr =  array(
			'zh-cn' => 'zh',
			'zh-tw' => 'zh-Hant',
			'pt'	=> 'pt-PT',
			'mya' => 'my',
			'tir' => 'ti',
			'el-po' => 'grc', // from .org GlotPress locales.php
			'bal' => 'ca', // from .org GlotPress locales.php
			'bel' => 'be', // from .org GlotPress locales.php
			'dzo' => 'dz', // from .org GlotPress locales.php
			'fuc' => 'ff', // from .org GlotPress locales.php
			'ido' => 'io', // from .org GlotPress locales.php
			'ike' => 'iu', // from .org GlotPress locales.php
			'haw-us' => 'haw', // from .org GlotPress locales.php
			'kin' => 'rw', // from .org GlotPress locales.php
			'lin' => 'ln', // from .org GlotPress locales.php
			'me-me' => 'sr-Latn-ME', // from .org GlotPress locales.php
			'mhr' => 'chm', // from .org GlotPress locales.php
			'mri' => 'mi', // from .org GlotPress locales.php
			'ory' => 'or', // from .org GlotPress locales.php
			'roh' => 'rm', // from .org GlotPress locales.php
			'srd' => 'sc', // from .org GlotPress locales.php
			'tuk' => 'tk', // from .org GlotPress locales.php
			'zh-hk' => 'zh-Hant', // from .org GlotPress locales.php
			'zh-sg' => 'zh', // from .org GlotPress locales.php
		);

		// convert underscores to dashes and everything to lowercase
		$cleaned_up_wp_locale = str_replace( '_', '-', $wp_locale );
		$cleaned_up_wp_locale = strtolower( $cleaned_up_wp_locale );

		// check for an exact match in exceptions array
		if ( isset ( $wp2cldr[$cleaned_up_wp_locale] ) ) {
			return $wp2cldr[$cleaned_up_wp_locale];
		}

		// capitalize country code to match capitalization of CLDR JSON paths
 		$locale_components = explode("-", $cleaned_up_wp_locale);
		if ( 2 == strlen( $locale_components[1] ) ) {
			$locale_components[1] = strtoupper( $locale_components[1] );
			$cleaned_up_wp_locale = implode( '-', $locale_components );
		}

		return $cleaned_up_wp_locale;
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
			$CLDR_locale = 'en';
		}

		$json_raw = file_get_contents( $data_file_name );
		$json_decoded = json_decode( $json_raw, true );

		switch( $bucket ) {
		    case 'territories':
		    case 'languages':
				$bucket_array = $json_decoded['main'][$CLDR_locale]['localeDisplayNames'][$bucket];
				if ( function_exists( 'collator_create' ) ) {
					// sort data according to locale collation rules
					$coll = collator_create( $CLDR_locale );
					collator_asort( $coll, $bucket_array, Collator::SORT_STRING );
				} else {
					asort( $bucket_array );
				}
				break;
			case 'currencies':
				$bucket_array = $json_decoded['main'][$CLDR_locale]['numbers'][$bucket];
		}

		return $bucket_array;
	}

	public function initialize_locale( $locale = 'en', $bucket = 'territories', $use_cache = true ) {

		if ( $use_cache ) {
			$cache_key = "cldr-localized-names-$locale-$bucket";
			$cached_data = wp_cache_get( $cache_key, WP_CLDR::CACHE_GROUP );
			if ( $cached_data ) {
				$this->localized[ $locale ][ $bucket ] = $cached_data;
				return true;
			}
		}

		$this->localized[ $locale ][ $bucket ] = $this->get_CLDR_data( $locale, $bucket );

		if ( $use_cache ) {
			wp_cache_set( $cache_key, $this->localized[ $locale ][ $bucket ], WP_CLDR::CACHE_GROUP );
		}
		return true;
	}

	public function flush_wp_cache_for_locale_bucket( $locale, $bucket ) {
		$cache_key = "cldr-localized-names-$locale-$bucket";
		return wp_cache_delete( $cache_key, WP_CLDR::CACHE_GROUP );
	}

	/**
	* Run this to force clear caches for all locales we know about
	*/
	public function flush_all_wp_caches() {
		$this->localized = array();

		// Initialize without the cache
		$this->initialize_locale( 'en', null, false );

		$locales = $this->locales_by_locale( 'en' );
		$supported_buckets = array( 'countries' , 'languages' , 'territories' );
		foreach( array_keys( $locales ) as $locale ) {
			foreach( $supported_buckets as $bucket ) {
				$this->flush_wp_cache_for_locale( $locale , $bucket );
			}
		}
	}

	/**
	* Return all the data for a given locale and bucket
	* @param  string $locale (optional) Which locale's strings to return.
	*                           Defaults to the current locale (which defaults to English).
	* @param string $bucket     The bucket for the CLDR data request
	* @return object            Values for keys initialized for a particular locale
	*/
	public function get_localized_names( $locale = null , $bucket = 'territories' ) {
		if ( ! $locale ) {
			$locale = $this->locale;
		}

		if ( isset( $this->localized[ $locale ][ $bucket ] ) ) {
			return $this->localized[ $locale ][ $bucket ];
		}

		// Maybe that bucket hasn't been initialized on this locale, let's try again:
		$this->initialize_locale( $locale , $bucket );

		if ( isset( $this->localized[ $locale ][ $bucket ] ) ) {
			return $this->localized[ $locale ][ $bucket ];
		}

		// Really not found
		return new StdClass();
	}

	/**
	* Get the localized value for a particular key in a particular bucket
	* @param  string $key       The individual item's id / stub.
	*                           For example: US, FR, DE
	* @param  string $bucket    (optional) In which group of data to look for the key
	*                           Defaults to 'territories'
	* @param  string $locale (optional)
	* @return string            The localized string
	*/
	public function __( $key, $locale = null, $bucket = 'territories' ) {
		if ( ! is_string( $key ) || ! strlen( $key ) ) {
			return '';
		}

		$bucket_array = $this->get_localized_names( $locale, $bucket );

		if ( isset( $bucket_array[ $key ] ) ) {
			return $bucket_array[ $key ];
		}
	}

	/**
	* Helpers to more easily access by bucket
	*/
	public function _territory( $territory_code, $locale = null ) {
		return $this->__( $territory_code, $locale, 'territories' );
	}

	public function _currency_symbol( $currency_code, $locale = null ) {
		$currencies_array = $this->get_localized_names( $locale, 'currencies' );
		if ( isset( $currencies_array[$currency_code]['symbol'] ) ) {
			return $currencies_array[$currency_code]['symbol'];
		}
	}

	public function _currency_name( $currency_code, $locale = null ) {
		$currencies_array = $this->get_localized_names( $locale, 'currencies' );
		if ( isset( $currencies_array[$currency_code]['displayName'] ) ) {
			return $currencies_array[$currency_code]['displayName'];
		}
	}

	public function _language( $language_code, $locale = null ) {
		return $this->__( $this->get_CLDR_locale( $language_code ), $locale, 'languages' );
	}

	/**
	* Get territory names localized for a particular locale.
	*
	* @param string $locale The locale to return the list in
	* @return array an associative array of ISO 3166-1 alpha-2 country codes and UN M.49 region codes, along with localized names, from CLDR
	*/
	public function territories_by_locale( $locale = null ) {
		return $this->get_localized_names( $locale, 'territories' );
	}

	/**
	* Get language names localized for a particular locale.
	*
	* @param string $locale The locale to return the list in
	* @return array an associative array of ISO 639 codes and localized language names from CLDR
	*/
	public function languages_by_locale( $locale = null ) {
		return $this->get_localized_names( $locale, 'languages' );
	}
}
