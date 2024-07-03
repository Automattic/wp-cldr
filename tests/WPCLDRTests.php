<?php
/**
 * Unit tests related to WP_CLDR class for fetching localization data from Unicode's Common Locale Data Repository
 *
 * The wp-cldr plugin is comprised of the WP_CLDR class, a subset of the reference JSON files from Unicode, and unit tests.
 *
 * @link https://github.com/Automattic/wp-cldr
 *
 * @package wp-cldr
 */

declare( strict_types=1 );

require 'class-wp-cldr.php';
use PHPUnit\Framework\TestCase;

/**
 * Performs unit tests against the wp-cldr plugin.
 */
final class WPCLDRTests extends TestCase {
	protected $cldr;
	protected function setup() : void {
		// The second parameter, false, tells the class to not use caching which means we can avoid loading WordPress for these tests.
		$this->cldr = new WP_CLDR( 'en', false );
	}

	public function test_get_territory_name() {

		// Test country names.
		$this->assertEquals( 'Allemagne', $this->cldr->get_territory_name( 'DE', 'fr_FR' ) );
		$this->assertEquals( 'ألمانيا', $this->cldr->get_territory_name( 'DE', 'ar_AR' ) );

		// Test region names.
		$this->assertEquals( 'Afrique', $this->cldr->get_territory_name( '002', 'fr_FR' ) );
		$this->assertEquals( '亚洲', $this->cldr->get_territory_name( '142', 'zh-cn' ) );

		// Test some bad slugs.
		$this->assertEquals( '', $this->cldr->get_territory_name( 'bad-slug', 'fr_FR' ) );
		$this->assertEquals( 'Africa', $this->cldr->get_territory_name( '002', 'badlocalecode' ) );
		$this->assertEquals( 'Africa', $this->cldr->get_territory_name( '002', 'bad-locale-code' ) );
		$this->assertEquals( 'Africa', $this->cldr->get_territory_name( '002', '' ) );
	}

	public function test_get_currency_name() {

		$this->assertEquals( 'dollar des États-Unis', $this->cldr->get_currency_name( 'USD', 'fr' ) );
		$this->assertEquals( 'US Dollar', $this->cldr->get_currency_name( 'USD', 'en' ) );

		// Test some bad slugs.
		$this->assertEquals( '', $this->cldr->get_currency_name( 'bad-slug', 'en' ) );
		$this->assertEquals( '', $this->cldr->get_currency_name( '' ) );
		$this->assertEquals( 'US Dollar', $this->cldr->get_currency_name( 'USD', '' ) );
	}

	public function test_get_currency_symbol() {

		$this->assertEquals( 'US$', $this->cldr->get_currency_symbol( 'USD', 'zh' ) );
		$this->assertEquals( '$', $this->cldr->get_currency_symbol( 'USD', 'en' ) );

		// Test some bad slugs.
		$this->assertEquals( '', $this->cldr->get_currency_symbol( 'bad-slug' ) );
		$this->assertEquals( '', $this->cldr->get_currency_symbol( '' ) );
		$this->assertEquals( '$', $this->cldr->get_currency_symbol( 'USD', '' ) );
	}

	public function test_get_language_name() {

		$this->assertEquals( 'français canadien', $this->cldr->get_language_name( 'fr-ca', 'fr' ) );
		$this->assertEquals( 'Canadian French', $this->cldr->get_language_name( 'fr-ca', 'en' ) );
		$this->assertEquals( 'Deutsch', $this->cldr->get_language_name( 'de_DE', 'de-DE' ) );
		$this->assertEquals( 'ベンガル語', $this->cldr->get_language_name( 'bn_BD', 'ja_JP' ) );

		// Test some bad slugs.
		$this->assertEquals( '', $this->cldr->get_language_name( 'bad-slug' ) );
		$this->assertEquals( '', $this->cldr->get_language_name( '' ) );
		$this->assertEquals( 'Canadian French', $this->cldr->get_language_name( 'fr-ca', '' ) );
	}

	public function test_get_territories() {

		$territories_in_english = $this->cldr->get_territories();
		$this->assertArrayHasKey( 'US', $territories_in_english );
		$this->assertEquals( 'United States', $territories_in_english['US'] );

		// Test some bad slugs.
		$all_territories = $this->cldr->get_territories( 'bad-slug' );
		$this->assertEquals( 'United States', $all_territories['US'] );
	}

	public function test_get_languages() {

		$languages_in_english = $this->cldr->get_languages();
		$this->assertArrayHasKey( 'en', $languages_in_english );
		$this->assertEquals( 'German', $languages_in_english['de'] );

		// Test some bad slugs.
		$all_languages = $this->cldr->get_languages( 'bad-slug' );
		$this->assertEquals( 'English', $all_languages['en'] );
	}

	public function test_set_locale() {

		$this->cldr->set_locale( 'fr' );
		$this->assertEquals( 'Allemagne', $this->cldr->get_territory_name( 'DE' ) );

		// Test some bad slugs.
		$this->cldr->set_locale( 'bad-slug' );
		$this->assertEquals( 'Germany', $this->cldr->get_territory_name( 'DE' ) );
	}

	public function test_wpcom_homepage_locales() {

		// Test the wpcom homepage locales as of Feb 2016.
		$this->assertEquals( 'ألمانيا', $this->cldr->get_territory_name( 'DE', 'ar' ) );
		$this->assertEquals( 'Almaniya', $this->cldr->get_territory_name( 'DE', 'az' ) );
		$this->assertEquals( 'Deutschland', $this->cldr->get_territory_name( 'DE', 'de' ) );
		$this->assertEquals( 'Γερμανία', $this->cldr->get_territory_name( 'DE', 'el' ) );
		$this->assertEquals( 'Germany', $this->cldr->get_territory_name( 'DE', 'en' ) );
		$this->assertEquals( 'Alemania', $this->cldr->get_territory_name( 'DE', 'es' ) );
		$this->assertEquals( 'آلمان', $this->cldr->get_territory_name( 'DE', 'fa' ) );
		$this->assertEquals( 'Saksa', $this->cldr->get_territory_name( 'DE', 'fi' ) );
		$this->assertEquals( 'Allemagne', $this->cldr->get_territory_name( 'DE', 'fr' ) );
		$this->assertEquals( 'Allemagne', $this->cldr->get_territory_name( 'DE', 'fr-ca' ) );
		$this->assertEquals( 'Saksa', $this->cldr->get_territory_name( 'DE', 'fi' ) );
		$this->assertEquals( 'גרמניה', $this->cldr->get_territory_name( 'DE', 'he' ) );
		$this->assertEquals( 'Jerman', $this->cldr->get_territory_name( 'DE', 'id' ) );
		$this->assertEquals( 'Germania', $this->cldr->get_territory_name( 'DE', 'it' ) );
		$this->assertEquals( 'ドイツ', $this->cldr->get_territory_name( 'DE', 'ja' ) );
		$this->assertEquals( '독일', $this->cldr->get_territory_name( 'DE', 'ko' ) );
		$this->assertEquals( 'Duitsland', $this->cldr->get_territory_name( 'DE', 'nl' ) );
		$this->assertEquals( 'Niemcy', $this->cldr->get_territory_name( 'DE', 'pl' ) );
		$this->assertEquals( 'Alemanha', $this->cldr->get_territory_name( 'DE', 'pt-br' ) );
		$this->assertEquals( 'Germania', $this->cldr->get_territory_name( 'DE', 'ro' ) );
		$this->assertEquals( 'Германия', $this->cldr->get_territory_name( 'DE', 'ru' ) );
		$this->assertEquals( 'Tyskland', $this->cldr->get_territory_name( 'DE', 'sv' ) );
		$this->assertEquals( 'เยอรมนี', $this->cldr->get_territory_name( 'DE', 'th' ) );
		$this->assertEquals( 'Almanya', $this->cldr->get_territory_name( 'DE', 'tr' ) );
		$this->assertEquals( 'Німеччина', $this->cldr->get_territory_name( 'DE', 'uk' ) );
		$this->assertEquals( '德国', $this->cldr->get_territory_name( 'DE', 'zh-cn' ) );
		$this->assertEquals( '德國', $this->cldr->get_territory_name( 'DE', 'zh-tw' ) );
	}

	public function test_partial_locale_code() {

		$this->assertEquals( 'Afrique', $this->cldr->get_territory_name( '002', 'fr' ) );
	}

	public function test_full_locale_code() {

		$this->assertEquals( 'Afrique', $this->cldr->get_territory_name( '002', 'fr_FR' ) );
	}

	public function test_wpcom_to_cldr_locale_mapping() {

		// Chinese variants.
		$this->assertEquals( 'zh-Hans', $this->cldr->get_cldr_locale( 'zh-cn' ) );
		$this->assertEquals( 'zh-Hant', $this->cldr->get_cldr_locale( 'zh-tw' ) );

		// Test some bad slugs.
		$this->assertEquals( 'bad-Slug', $this->cldr->get_cldr_locale( 'bad-slug' ) );
		$this->assertEquals( 'badslug', $this->cldr->get_cldr_locale( 'badslug' ) );
		$this->assertEquals( '', $this->cldr->get_cldr_locale( '' ) );
	}

	public function test_all_WordPress_locales() {

		// First, create an array of all WordPress locales.
		$wpcom_locales_with_over_1k_translations_at_september_2022 = [ 'az', 'de', 'de-ch', 'ja', 'id', 'pt-br', 'it', 'he', 'es', 'fr', 'nl', 'tr', 'hu', 'ru', 'ko', 'ar', 'sq', 'fr-ca', 'fa', 'zh-tw', 'zh-cn', 'zh-sg', 'sv', 'ga', 'el', 'fi', 'gl', 'bs', 'pt', 'ca', 'sr', 'hr', 'pl', 'nn', 'el-po', 'da', 'gd', 'th', 'cs', 'eo', 'ckb', 'mya', 'cy', 'ro', 'te', 'no', 'bg', 'sk', 'lt', 'ug', 'fr-be', 'vi', 'nb', 'ms', 'km', 'uk', 'eu', 'ky', 'br', 'es-pr', 'es-mx', 'es-cl', 'fr-ch', 'si', 'ta', 'as', 'mk', 'tl', 'su', 'is', 'et', 'lv', 'af', 'bn', 'oc', 'ur', 'kk', 'ne', 'mwl', 'hi', 'en-gb', 'mhr', 'mn', 'zh', 'sl', 'mr', 'ml', 'so', 'sah', 'fo', 'zh-hk', 'ast', 'skr', 'cv', 'kab', 'uz', 'ka', 'pa', 'gu', 'oci', 'bo', 'bel', 'sw', 'hy', 'ps', 'lo', 'kmr', 'bal', 'me', 'kn', 'kir', 'an', 'dv'];
		$wporg_site_languages_menu_at_september_2022 = [ 'en', 'af', 'am', 'arg', 'ar', 'ary', 'as', 'az', 'azb', 'bel', 'bg_BG', 'bn_BD', 'bo', 'bs_BA', 'ca', 'ceb', 'cs_CZ',  'cy', 'da_DK', 'de_CH', 'de_DE', 'de_CH_informal', 'de_DE_formal', 'de_AT', 'dsb', 'dzo', 'el', 'en_AU', 'en_CA', 'en_GB', 'en_NZ', 'en_ZA', 'eo', 'es_AR', 'es_CL', 'es_CR', 'es_CO', 'es_DO', 'es_ES', 'es_EC', 'es_GT', 'es_MX', 'es_PE', 'es_PR', 'es_UY', 'es_VE', 'et', 'eu', 'fa_AF', 'fa_IR', 'fi', 'fr_BE', 'fr_CA', 'fr_FR', 'fur', 'gd', 'gu', 'gl_ES', 'haz', 'he_IL', 'hi_IN', 'hr', 'hsb', 'hu_HU', 'hy', 'id_ID', 'is_IS', 'it_IT', 'ja', 'jv_ID', 'ka_GE', 'kab', 'kk', 'km', 'kn', 'ko_KR', 'ckb', 'lt_LT', 'lv', 'mk_MK', 'ml_IN', 'mn', 'mr', 'ms_MY', 'my_MM', 'nb_NO', 'ne_NP', 'nl_BE', 'nl_NL', 'nl_NL_formal', 'nn_NO', 'oci', 'pa_IN', 'pl_PL', 'ps', 'pt_AO', 'pt_PT_ao90', 'pt_BR', 'pt_PT', 'rhg', 'ro_RO', 'ru_RU', 'sah', 'snd', 'si_LK', 'sk_SK', 'skr', 'sl_SI', 'sq', 'sr_RS', 'sv_SE', 'sw', 'szl', 'ta_IN', 'ta_LK', 'te', 'th', 'tl', 'tr_TR', 'tt_RU', 'tah', 'ug_CN', 'uk', 'ur', 'uz_UZ', 'vi', 'zh_CN', 'zh_TW', 'zh_HK' ];
		$wp_locales = array_unique( array_merge( $wpcom_locales_with_over_1k_translations_at_september_2022, $wporg_site_languages_menu_at_september_2022 ) );

		// Second, check to see if a CLDR JSON file is available for each one, first excluding known missing
		// locales, then checking the WP locale is mapped to a CLDR locale, and then checking a language-only
		// CLDR code. If neither is found, set to `false` so the test fails. Then echo the failed $wp_locale
		// so we can see in the PHPUnit output which one it was.
		$known_missing_locales = [ 'oc', 'mwl', 'mhr', 'azb', 'haz', 'oci', 'skr', 'cv', 'bal', 'me', 'an', 'dv', 'arg', 'rhg', 'szl', 'tah' ];
		foreach ( $wp_locales as $wp_locale ) {
			$found_json_file = true;
			$wp_locale_mapped_to_cldr = $this->cldr->get_cldr_locale( $wp_locale );
			if ( ! in_array( $wp_locale, $known_missing_locales, true ) ) {
				if ( ! $this->cldr->is_cldr_json_available( $wp_locale_mapped_to_cldr, 'territories' ) ) {
					$wp_locale_mapped_to_language_only_cldr = strtok( $wp_locale_mapped_to_cldr, '-_' );
					if ( ! $this->cldr->is_cldr_json_available( $wp_locale_mapped_to_language_only_cldr, 'territories' ) ) {
						$found_json_file = false;
						echo "\n" . $wp_locale . "\n";
					}
				}
			}
			$this->assertTrue( $found_json_file );
		}
	}

	public function test_get_first_day_of_week() {

		$this->assertEquals( 'sun', $this->cldr->get_first_day_of_week( 'US' ) );
		$this->assertEquals( 'sat', $this->cldr->get_first_day_of_week( 'QA' ) );

		// Test some bad slugs.
		$this->assertEquals( '', $this->cldr->get_first_day_of_week( 'bad-slug' ) );
		$this->assertEquals( '', $this->cldr->get_first_day_of_week( '' ) );
	}

	public function test_get_currency_for_all_countries() {

		$all_currencies = $this->cldr->get_currency_for_all_countries();
		$this->assertEquals( 'USD', $all_currencies['US'] );
		$this->assertEquals( 'QAR', $all_currencies['QA'] );
		$this->assertEquals( 'EUR', $all_currencies['FR'] );

		// The number of countries is dynamic this range should cover it.
		$this->assertGreaterThan( 245, count( $this->cldr->get_currency_for_all_countries() ) );
		$this->assertLessThan( 275, count( $this->cldr->get_currency_for_all_countries() ) );
	}

	public function test_get_currency_for_country() {

		$this->assertEquals( 'USD', $this->cldr->get_currency_for_country( 'US' ) );
		$this->assertEquals( 'QAR', $this->cldr->get_currency_for_country( 'QA' ) );
		$this->assertEquals( 'EUR', $this->cldr->get_currency_for_country( 'VA' ) );

		// Test a bad slug.
		$this->assertEquals( '', $this->cldr->get_currency_for_country( 'bad-code' ) );
	}

	public function test_get_countries_for_all_currencies() {

		$this->assertArrayHasKey( 'USD', $this->cldr->get_countries_for_all_currencies() );
		$this->assertArrayHasKey( 'EUR', $this->cldr->get_countries_for_all_currencies() );
		$this->assertArrayHasKey( 'QAR', $this->cldr->get_countries_for_all_currencies() );

		// The number of currencies is dynamic this range should cover it.
		$this->assertGreaterThan( 145, count( $this->cldr->get_countries_for_all_currencies() ) );
		$this->assertLessThan( 165, count( $this->cldr->get_countries_for_all_currencies() ) );
	}

	public function test_get_countries_for_currency() {

		$this->assertEquals( [ 'JP' ], $this->cldr->get_countries_for_currency( 'JPY' ) );
		$this->assertEquals( [ 'QA' ], $this->cldr->get_countries_for_currency( 'QAR' ) );
		$this->assertEquals( [ 'GB', 'GG', 'GS', 'IM', 'JE', 'TA' ], $this->cldr->get_countries_for_currency( 'GBP' ) );

		// Test a bad slug.
		$this->assertEquals( [], $this->cldr->get_countries_for_currency( 'bad-code' ) );
	}

	public function test_get_territories_contained() {

		$this->assertEquals( [ 'BM', 'CA', 'GL', 'PM', 'US' ], $this->cldr->get_territories_contained( '021' ) );
		$this->assertEquals( [ 'US' ], $this->cldr->get_territories_contained( 'US' ) );

		// Test some bad slugs.
		$this->assertEquals( [], $this->cldr->get_territories_contained( 'bad-slug' ) );
		$this->assertEquals( [], $this->cldr->get_territories_contained( '' ) );
	}

	public function test_get_languages_spoken() {

		$us_languages = $this->cldr->get_languages_spoken( 'US' );
		$this->assertArrayHasKey( 'en', $us_languages );
		$this->assertArrayHasKey( 'es', $us_languages );

		// Test some bad slugs.
		$this->assertEquals( [], $this->cldr->get_languages_spoken( 'bad-slug' ) );
		$this->assertEquals( [], $this->cldr->get_languages_spoken( '' ) );
	}

	public function test_get_most_spoken_language() {

		$this->assertEquals( 'en', $this->cldr->get_most_spoken_language( 'US' ) );
		$this->assertEquals( 'fr', $this->cldr->get_most_spoken_language( 'FR' ) );
		$this->assertEquals( 'zh', $this->cldr->get_most_spoken_language( 'CN' ) );

		// Test some bad slugs.
		$this->assertEquals( '', $this->cldr->get_most_spoken_language( 'bad-slug' ) );
		$this->assertEquals( '', $this->cldr->get_most_spoken_language( '' ) );
	}

	public function test_get_territory_info() {

		$us_info = $this->cldr->get_territory_info( 'US' );
		$this->assertArrayHasKey( '_gdp', $us_info );
		$this->assertArrayHasKey( 'languagePopulation', $us_info );

		// Test some bad slugs.
		$this->assertEquals( [], $this->cldr->get_territory_info( 'bad-slug' ) );
	}

	public function test_get_time_zone_cities() {

		$time_zone_cities_in_english = $this->cldr->get_time_zone_cities();
		$this->assertArrayHasKey( 'America/Los_Angeles', $time_zone_cities_in_english );
		$this->assertEquals( 'Paris', $time_zone_cities_in_english['Europe/Paris'] );

		// The number of time zone exemplar cities is dynamic this range should cover it.
		$this->assertGreaterThan( 400, count( $this->cldr->get_time_zone_cities() ) );
		$this->assertLessThan( 475, count( $this->cldr->get_time_zone_cities() ) );

		// Test some bad slugs.
		$time_zone_cities = $this->cldr->get_time_zone_cities( 'bad-slug' );
		$this->assertEquals( 'Los Angeles', $time_zone_cities['America/Los_Angeles'] );
	}

	public function test_get_time_zone_city() {

		$this->assertEquals( 'Paris', $this->cldr->get_time_zone_city( 'Europe/Paris' ) );
		$this->assertEquals( 'Los Angeles', $this->cldr->get_time_zone_city( 'America/Los_Angeles' ) );

		$this->assertEquals( 'Londres', $this->cldr->get_time_zone_city( 'Europe/London', 'fr' ) );

		// Test some bad slugs.
		$this->assertEquals( '', $this->cldr->get_time_zone_city( 'bad-slug', 'fr_FR' ) );
		$this->assertEquals( 'Paris', $this->cldr->get_time_zone_city( 'Europe/Paris', 'badlocalecode' ) );
		$this->assertEquals( 'Paris', $this->cldr->get_time_zone_city( 'Europe/Paris', 'bad-locale-code' ) );
		$this->assertEquals( 'Paris', $this->cldr->get_time_zone_city( 'Europe/Paris', '' ) );
	}
}
