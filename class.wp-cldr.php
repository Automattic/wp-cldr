<?php

/**
* A class for fetching localization data from Unicode's Common Locale Data Repository.
*
* Examples:
*
* // The default locale is English
* $cldr = new WP_CLDR();
* $territories_in_english = $cldr->territories_by_locale( 'en' );
*
* // You can override the default locale per-call by passing in a language slug in the second parameter
* $germany_in_arabic = $cldr->territory_name( 'DE' , 'ar_AR' );
*
* // Use a convenience parameter during instantiation to change the default locale
* $cldr = new WP_CLDR( 'fr' );
* $germany_in_french = $cldr->territory_name( 'DE' );
* $us_dollar_in_french = $cldr->currency_name( 'USD' );
* $canadian_french_in_french = $cldr->language_name( 'fr-ca' );
* $canadian_french_in_english = $cldr->language_name( 'fr-ca' , 'en' );
* $german_in_german = $cldr->language_name( 'de_DE' , 'de-DE' );
* $bengali_in_japanese = $cldr->language_name( 'bn_BD' , 'ja_JP' );
* $us_dollar_symbol_in_simplified_chinese = $cldr->currency_symbol( 'USD', 'zh' );
* $africa_in_french = $cldr->territory_name( '002' );
*
* // Switch locales after the object has been created
* $cldr->set_locale( 'en' );
* $us_dollar_in_english = $cldr->currency_name( 'USD' );
*
* @autounit wp-cldr
*/

class WP_CLDR {
	private $locale = 'en';
	private $localized = array();

	const CACHE_GROUP = 'wp-cldr';
	const CLDR_VERSION = '27';

	public function __construct( $locale = 'en', $use_cache = true ) {
		$this->use_cache = $use_cache;
		$this->set_locale( $locale );
	}

	public function set_locale( $locale ) {
		$this->locale = $locale;
		$this->initialize_locale_bucket( $locale );
	}

	/**
	* Helper function to get CLDR code for a given WordPress locale code
	*
	* @param string $wp_locale The WordPress locale
	* @return string The CLDR locale code, if different, or if not the original WordPress locale code
	*/
	public function get_cldr_locale( $wp_locale ) {

		// this array captures the WordPress locales that are signficiantly different from CLDR locales
		$wp2cldr =	array(
			'zh-cn' => 'zh-Hans',
			'zh-tw' => 'zh-Hant',
			'zh' => 'zh-Hans',
			'als' => 'gsw',
			'pt'	=> 'pt-PT',
			'pt-br'	=> 'pt-BR',
			'el-po'	=> '',
			'me' => '',
			'tl' => 'fil',
			'mya' => 'my',
			'tir' => 'ti',
			'bal' => 'ca', // from .org GlotPress locales.php
			'bel' => 'be', // from .org GlotPress locales.php
			'dzo' => 'dz', // from .org GlotPress locales.php
			'fuc' => 'ff', // from .org GlotPress locales.php
			'ido' => 'io', // from .org GlotPress locales.php
			'ike' => 'iu', // from .org GlotPress locales.php
			'haw-us' => 'haw', // from .org GlotPress locales.php
			'kin' => 'rw', // from .org GlotPress locales.php
			'lin' => 'ln', // from .org GlotPress locales.php
			'me-me' => '', // from .org GlotPress locales.php
			'mhr' => 'chm', // from .org GlotPress locales.php
			'mri' => 'mi', // from .org GlotPress locales.php
			'ory' => 'or', // from .org GlotPress locales.php
			'ph' => 'fil', // from .org GlotPress locales.php
			'roh' => 'rm', // from .org GlotPress locales.php
			'srd' => 'sc', // from .org GlotPress locales.php
			'tuk' => 'tk', // from .org GlotPress locales.php
			'zh-hk' => 'zh-Hant', // from .org GlotPress locales.php
			'zh-sg' => 'zh-Hans', // from .org GlotPress locales.php
		);

		// convert underscores to dashes and everything to lowercase
		$cleaned_up_wp_locale = str_replace( '_', '-', $wp_locale );
		$cleaned_up_wp_locale = strtolower( $cleaned_up_wp_locale );

		// check for an exact match in exceptions array
		if ( isset ( $wp2cldr[$cleaned_up_wp_locale] ) ) {
			return $wp2cldr[$cleaned_up_wp_locale];
		}

		// capitalize country code and initial letter of script code to match CLDR JSON filenames
 		$locale_components = explode("-", $cleaned_up_wp_locale);
		if ( isset( $locale_components[1]) ) {
			if ( 2 == strlen( $locale_components[1] ) ) {
				$locale_components[1] = strtoupper( $locale_components[1] );
				$cleaned_up_wp_locale = implode( '-', $locale_components );
			} else if ( 2 < strlen( $locale_components[1] ) ) {
				$locale_components[1] = ucfirst( $locale_components[1] );
				$cleaned_up_wp_locale = implode( '-', $locale_components );
				}
		}

		return $cleaned_up_wp_locale;
	}

	/**
	* Helper function to load a CLDR JSON data file
	*
	* @param string $cldr_locale The CLDR locale
	* @param string $bucket The CLDR data item
	* @return array $json_decoded an array with the CLDR data from the file, or null if no match with any CLDR data files
	*/
	public function get_cldr_json_file( $cldr_locale, $bucket ) {
		$base_path = __DIR__ . '/json-files/v' . WP_CLDR::CLDR_VERSION;

		switch ( $bucket ) {
			case 'supplemental':
				$relative_path = "cldr-core/supplemental";
				break;

			case 'currencies':
				$relative_path = "cldr-numbers-modern/main/$cldr_locale";
				break;

			default:
				$relative_path = "cldr-localenames-modern/main/$cldr_locale";
				break;
		}

		$data_file_name = "$base_path/$relative_path/$bucket.json";

		if ( ! file_exists( $data_file_name ) ) {
			return null;
		}

		$json_raw = file_get_contents( $data_file_name );
		$json_decoded = json_decode( $json_raw, true );

		return $json_decoded;
	}

	public function initialize_locale_bucket( $locale = 'en', $bucket = 'territories', $use_cache = true ) {

		if ( $this->use_cache ) {
			$cache_key = "cldr-$locale-$bucket";
			$cached_data = wp_cache_get( $cache_key, WP_CLDR::CACHE_GROUP );
			if ( $cached_data ) {
				$this->localized[ $locale ][ $bucket ] = $cached_data;
				return true;
			}
		}

		$cldr_locale = $this->get_cldr_locale($locale);

		$cldr_locale_file = $this->get_cldr_json_file( $cldr_locale, $bucket );

		// if no language-country locale CLDR file, fall back to a language-only CLDR file
		if ( is_null( $cldr_locale_file ) ) {
			$cldr_locale = strtok( $cldr_locale, '-_' );
			$cldr_locale_file = $this->get_cldr_json_file( $cldr_locale, $bucket );
		}

		// if no language CLDR file, fall back to English CLDR file
		if ( is_null( $cldr_locale_file ) ) {
			$cldr_locale = 'en';
			$cldr_locale_file = $this->get_cldr_json_file( $cldr_locale, $bucket );
		}

		// for performance, pre-process a few items before putting into the cache
		switch( $bucket ) {
				case 'territories':
				case 'languages':
				$bucket_array = $cldr_locale_file['main'][$cldr_locale]['localeDisplayNames'][$bucket];
				if ( function_exists( 'collator_create' ) ) {
					// sort data according to locale collation rules
					$coll = collator_create( $cldr_locale );
					collator_asort( $coll, $bucket_array, Collator::SORT_STRING );
				} else {
					asort( $bucket_array );
				}
				break;
			case 'currencies':
				$bucket_array = $cldr_locale_file['main'][$cldr_locale]['numbers'][$bucket];
				break;
			default: // covers supplemental files
				$bucket_array = $cldr_locale_file;
		}

		$this->localized[ $locale ][ $bucket ] = $bucket_array;

		if ( $this->use_cache ) {
			wp_cache_set( $cache_key, $this->localized[ $locale ][ $bucket ], WP_CLDR::CACHE_GROUP );
		}
		return true;
	}

	public function flush_wp_cache_for_locale_bucket( $locale, $bucket ) {
		$cache_key = "cldr-$locale-$bucket";
		return wp_cache_delete( $cache_key, WP_CLDR::CACHE_GROUP );
	}

	/**
	* Run this to force clear caches for all locales we know about
	*/
	public function flush_all_wp_caches() {
		$this->localized = array();

		$locales = $this->languages_by_locale( 'en' );
		$supported_buckets = array( 'countries' , 'languages' , 'territories', 'supplemental' );
		foreach( array_keys( $locales ) as $locale ) {
			foreach( $supported_buckets as $bucket ) {
				$this->flush_wp_cache_for_locale_bucket( $locale, $bucket );
			}
		}
	}

	/**
	* Return all the data for a given locale and bucket
	* @param	string $locale	which locale's strings to return.
	* @param string $bucket		The bucket for the CLDR data request
	* @return array						 Values for keys initialized for a particular locale
	*/
	public function get_locale_bucket( $locale , $bucket ) {
		if ( ! $locale ) {
			$locale = $this->locale;
		}

		if ( isset( $this->localized[ $locale ][ $bucket ] ) ) {
			return $this->localized[ $locale ][ $bucket ];
		}

		// Maybe that bucket hasn't been initialized on this locale, let's try again:
		$this->initialize_locale_bucket( $locale , $bucket );

		if ( isset( $this->localized[ $locale ][ $bucket ] ) ) {
			return $this->localized[ $locale ][ $bucket ];
		}

		// Really not found
		return null;
	}

	/**
	* Get the localized value for a particular key in a particular bucket
	* @param	string $key			 The individual item's id / stub.
	* @param	string $locale 	The locale
	* @param	string $bucket In which group of data to look for the key
	* @return string						The localized string
	*/
	public function get_cldr_item( $key, $locale, $bucket ) {
		if ( ! is_string( $key ) || ! strlen( $key ) ) {
			return '';
		}

		$bucket_array = $this->get_locale_bucket( $locale, $bucket );

		if ( isset( $bucket_array[ $key ] ) ) {
			return $bucket_array[ $key ];
		}
	}

	/**
	* Helpers to more easily access an item in a bucket
	*/
	public function territory_name( $territory_code, $locale = null ) {
		return $this->get_cldr_item( $territory_code, $locale, 'territories' );
	}

	public function currency_symbol( $currency_code, $locale = null ) {
		$currencies_array = $this->get_locale_bucket( $locale, 'currencies' );
		if ( isset( $currencies_array[$currency_code]['symbol'] ) ) {
			return $currencies_array[$currency_code]['symbol'];
		}
	}

	public function currency_name( $currency_code, $locale = null ) {
		$currencies_array = $this->get_locale_bucket( $locale, 'currencies' );
		if ( isset( $currencies_array[$currency_code]['displayName'] ) ) {
			return $currencies_array[$currency_code]['displayName'];
		}
	}

	public function language_name( $language_code, $locale = null ) {
		$cldr_matched_language_code = $this->get_cldr_locale( $language_code );

		$language_name = $this->get_cldr_item( $cldr_matched_language_code, $locale, 'languages' );

		// if no match for locale (language-COUNTRY), try falling back to CLDR-matched language code only
		if ( is_null( $language_name ) ) {
			$language_name = $this->get_cldr_item( strtok($language_code, '-_' ), $locale, 'languages' );
		}

		return $language_name;
	}

	/**
	* Get territory names localized for a particular locale
	*
	* @param string $locale The locale to return the list in
	* @return array an associative array of ISO 3166-1 alpha-2 country codes and UN M.49 region codes, along with localized names, from CLDR
	*/
	public function territories_by_locale( $locale = null ) {
		return $this->get_locale_bucket( $locale, 'territories' );
	}

	/**
	* Get language names localized for a particular locale
	*
	* @param string $locale The locale to return the list in
	* @return array an associative array of ISO 639 codes and localized language names from CLDR
	*/
	public function languages_by_locale( $locale = null ) {
		return $this->get_locale_bucket( $locale, 'languages' );
	}

	/**
	* Get telephone code for a country. See http://unicode.org/reports/tr35/tr35-info.html#Telephone_Code_Data
	*
	* @param string $locale The two-letter ISO-3166 country code
	* @return string	The telephone code for the country
	*/
	public function telephone_code( $country ) {
		$json_file = $this->get_locale_bucket( 'supplemental', 'telephoneCodeData' );
		if ( isset( $json_file['supplemental']['telephoneCodeData'][$country][0]['telephoneCountryCode'] ) ) {
			return $json_file['supplemental']['telephoneCodeData'][$country][0]['telephoneCountryCode'];
		}
	}

	/**
	* Get the day which typically starts a calendar week in a country. See http://unicode.org/reports/tr35/tr35-dates.html
	*
	* @param string $locale The two-letter ISO-3166 country code
	* @return string	a three-character beginning of the English name for the day considered to be the start of the week
	*/
	public function first_day_of_week( $country ) {
		$json_file = $this->get_locale_bucket( 'supplemental', 'weekData' );
		if ( isset( $json_file['supplemental']['weekData']['firstDay'][$country] ) ) {
			return $json_file['supplemental']['weekData']['firstDay'][$country];
		}
	}

}
