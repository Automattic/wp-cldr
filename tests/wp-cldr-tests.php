<?php

require_lib( 'wp-cldr');

/**
 * Performs unit tests against the wp-cldr plugin.
 *
 */
class WP_CLDR_Tests extends PHPUnit_Framework_TestCase {

	// test basic data queries

	public function setup() {
		$this->cldr = new WP_CLDR( 'en', false );
	}

	public function test_territory_name() {

		// test country names
		$this->assertEquals( "Allemagne", $this->cldr->territory_name( 'DE' , 'fr_FR' ) );
		$this->assertEquals( "ألمانيا", $this->cldr->territory_name( 'DE' , 'ar_AR' ) );

		// test region names
		$this->assertEquals( "Afrique", $this->cldr->territory_name( '002', 'fr_FR' ) );
		$this->assertEquals( "亚洲", $this->cldr->territory_name( '142', 'zh-cn' ) );
	}

	public function test_currency_name() {

		$this->assertEquals( "dollar des États-Unis", $this->cldr->currency_name( 'USD', 'fr' ) );
		$this->assertEquals( "US Dollar", $this->cldr->currency_name( 'USD', 'en' ) );
	}

	public function test_currency_symbol() {

		$this->assertEquals( "US$", $this->cldr->currency_symbol( 'USD', 'zh' ) );
		$this->assertEquals( "$", $this->cldr->currency_symbol( 'USD', 'en' ) );
	}

	public function test_language_name() {

		$this->assertEquals( "français canadien", $this->cldr->language_name( 'fr-ca', 'fr' ) );
		$this->assertEquals( "Canadian French", $this->cldr->language_name( 'fr-ca' , 'en' ) );
		$this->assertEquals( "Deutsch", $this->cldr->language_name( 'de_DE' , 'de-DE' ) );
		$this->assertEquals( "ベンガル語", $this->cldr->language_name( 'bn_BD' , 'ja_JP' ) );
	}

	public function test_territories_by_locale() {

		$territories_in_english = $this->cldr->territories_by_locale( 'en' );
		$this->assertArrayHasKey('US', $territories_in_english );
		$this->assertEquals( "United States", $territories_in_english['US'] );
	}

	public function test_languages_by_locale() {

		$languages_in_english = $this->cldr->languages_by_locale( 'en' );
		$this->assertArrayHasKey( 'en', $languages_in_english );
		$this->assertEquals( "German", $languages_in_english[ 'de' ] );
	}

	public function test_set_locale() {

		$this->cldr->set_locale( 'fr' );
		$this->assertEquals( "Allemagne", $this->cldr->territory_name( 'DE' ) );
	}

//	need to add this functionality
/*	public function test_short_variant_of_country_names() {

		$this->assertEquals( "Hong Kong", $this->cldr->territory_name( 'HK' ) );
		$this->assertEquals( "Macau", $this->cldr->territory_name( 'MO' ) );
		$this->assertEquals( "Palestine", $this->cldr->territory_name( 'PS' ) );
	} */

	public function test_static_homepage_locales() {

		// test the static home page locales from WPCom_Languages::get_static_homepage_locales()
		// as of Feb 2016
		$this->assertEquals( "ألمانيا", $this->cldr->territory_name( 'DE', 'ar' ) );
		$this->assertEquals( "Almaniya", $this->cldr->territory_name( 'DE', 'az' ) );
		$this->assertEquals( "Deutschland", $this->cldr->territory_name( 'DE', 'de' ) );
		$this->assertEquals( "Γερμανία", $this->cldr->territory_name( 'DE', 'el' ) );
		$this->assertEquals( "Germany", $this->cldr->territory_name( 'DE', 'en' ) );
		$this->assertEquals( "Alemania", $this->cldr->territory_name( 'DE', 'es' ) );
		$this->assertEquals( "آلمان", $this->cldr->territory_name( 'DE', 'fa' ) );
		$this->assertEquals( "Saksa", $this->cldr->territory_name( 'DE', 'fi' ) );
		$this->assertEquals( "Allemagne", $this->cldr->territory_name( 'DE', 'fr' ) );
		$this->assertEquals( "Allemagne", $this->cldr->territory_name( 'DE', 'fr-ca' ) );
		$this->assertEquals( "Saksa", $this->cldr->territory_name( 'DE', 'fi' ) );
		$this->assertEquals( "גרמניה", $this->cldr->territory_name( 'DE', 'he' ) );
		$this->assertEquals( "Jerman", $this->cldr->territory_name( 'DE', 'id' ) );
		$this->assertEquals( "Germania", $this->cldr->territory_name( 'DE', 'it' ) );
		$this->assertEquals( "ドイツ", $this->cldr->territory_name( 'DE', 'ja' ) );
		$this->assertEquals( "독일", $this->cldr->territory_name( 'DE', 'ko' ) );
		$this->assertEquals( "Duitsland", $this->cldr->territory_name( 'DE', 'nl' ) );
		$this->assertEquals( "Niemcy", $this->cldr->territory_name( 'DE', 'pl' ) );
		$this->assertEquals( "Alemanha", $this->cldr->territory_name( 'DE', 'pt-br' ) );
		$this->assertEquals( "Germania", $this->cldr->territory_name( 'DE', 'ro' ) );
		$this->assertEquals( "Германия", $this->cldr->territory_name( 'DE', 'ru' ) );
		$this->assertEquals( "Tyskland", $this->cldr->territory_name( 'DE', 'sv' ) );
		$this->assertEquals( "เยอรมนี", $this->cldr->territory_name( 'DE', 'th' ) );
		$this->assertEquals( "Almanya", $this->cldr->territory_name( 'DE', 'tr' ) );
//		this needs fixing
//		$this->assertEquals( "Німеччина", $this->cldr->territory_name( 'DE', 'uk' ) );
		$this->assertEquals( "德国", $this->cldr->territory_name( 'DE', 'zh-cn' ) );
		$this->assertEquals( "德國", $this->cldr->territory_name( 'DE', 'zh-tw' ) );
	}

	public function test_flush_cache() {

		$this->cldr->flush_wp_cache_for_locale_bucket( 'de', 'territories' );
		$this->assertEquals( "Tschechische Republik", $this->cldr->territory_name( 'CZ', 'de' ) );
	}

	public function test_partial_locale_code() {

		$this->assertEquals( "Afrique", $this->cldr->territory_name( '002', 'fr' ) );
	}

	public function test_full_locale_code() {

		$this->assertEquals( "Afrique", $this->cldr->territory_name( '002', 'fr_FR' ) );
	}

	public function test_br_locale_code() {

		$this->assertEquals( "África", $this->cldr->territory_name( '002', 'pt-br' ) );
	}
}
