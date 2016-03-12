# wp-cldr

Gives WordPress developers easy access to localized country, region, language, currency, time zone, and calendar info.

== Description ==

This plugin provides WordPress developers with easy access to localized country, language, currency, region, and calendar info from the [Unicode Common Locale Data Repository](http://cldr.unicode.org/).

With the plugin active, WordPress developers can access the following for over 100 WordPress locales:

* Names for countries (and ISO 3166 country codes).
* Names for regions (and UN M.49 region codes, plus countries included in each region).
* Names and symbols for currencies (and ISO 4317 currency codes).
* Names for languages (and ISO 639 language codes).
* Names and symbols for currencies (and ISO 4317 currency codes).
* Names for time zone example cities.
* Calendar information including the first day of the week in different countries.
* Country information including telephone codes, most spoken languages, currency, and population.

More information in the [detailed API documentation](https://automattic.github.io/wp-cldr/class-WP_CLDR.html).

CLDR is a library of localization data coordinated by Unicode. It emphasizes [common, everyday usage](http://cldr.unicode.org/translation/country-names) and is available in over 700 language-region locales. It is [updated every six months](http://cldr.unicode.org/index/downloads) and used by [all major software systems](http://cldr.unicode.org/#TOC-Who-uses-CLDR-). CLDR data is licensed under [Unicode's data files and software license](http://unicode.org/copyright.html#Exhibit1) which is on [the list of approved GPLv2 compatible licenses](https://www.gnu.org/philosophy/license-list.html#Unicode).

== Installation ==

1. Upload the folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. See the plugin in action via its settings page.
1. Build CLDR data into your site by using [functions in the API documentation](https://automattic.github.io/wp-cldr/class-WP_CLDR.html)

== Frequently Asked Questions ==

= What locales are included? =

The plugin ships with JSON files for over 100 WordPress locales including `ar`, `ary`, `az`, `bg_BG`, `bn_BD`, `bs_BA`, `ca`, `cy`, `da_DK`, `de_CH`, `de_DE`, `de_DE_formal`, `el`, `en_US`, `en_AU`, `en_CA`, `en_GB`, `en_NZ`, `en_ZA`, `eo`, `es_AR`, `es_CL`, `es_CO`, `es_ES`, `es_GT`, `es_MX`, `es_PE`, `es_VE`, `et`, `eu`, `fa_IR`, `fi`, `fr_BE`, `fr_CA`, `fr_FR`, `gd`, `gl_ES`, `he_IL`, `hi_IN`, `hr`, `hu_HU`, `hy`, `id_ID`, `is_IS`, `it_IT`, `ja`, `ka_GE`, `ko_KR`, `lt_LT`, `ms_MY`, `my_MM`, `nb_NO`, `nl_NL`, `nl_NL_formal`, `nn_NO`, `pl_PL`, `ps`, `pt_BR`, `pt_PT`, `ro_RO`, `ru_RU`, `sk_SK`, `sl_SI`, `sq`, `sr_RS`, `sv_SE`, `th`, `tl`, `tr_TR`, `ug_CN`, `uk`, `vi`, `zh_CN`, `zh_TW`.

= Is there testing? =

Yes! The class includes a suite of PHPUnit tests. To run them, call `phpunit` from the plugin directory.

= Can the plugin handle high volume? =

The plugin includes two layers of caching (in-memory arrays and the WordPress object cache) and is designed for high volume use. It is currently used on WordPress.com.

= Where do the JSON files come from? =

The scripts used to collect the JSON files are included in the repo. A bash script `bash get-cldr-files.sh` uses wget to collect the files from [Unicode's reference distribution of CLDR JSON on GitHub](http://cldr.unicode.org/index/cldr-spec/json); a command-line PHP script `php prune-cldr-files.php` removes unneeded locales and locale files from that download. Both should be run from within the `wp-cldr` directory.

= Where can I report issues? =

Open up a new issue on GitHub at https://github.com/Automattic/wp-cldr/issues. We love pull requests!

## Examples:
### The default locale is English
```
$cldr = new WP_CLDR();
$territories_in_english = $cldr->get_territories();
```

### You can override the default locale per-call by passing in a language slug in the second parameter
```
$germany_in_arabic = $cldr->get_territory_name( 'DE' , 'ar' );
```

### Use a convenience parameter during instantiation to change the default locale
```
$cldr = new WP_CLDR( 'fr' );
$germany_in_french = $cldr->get_territory_name( 'DE' );
$us_dollar_in_french = $cldr->get_currency_name( 'USD' );
$canadian_french_in_french = $cldr->get_language_name( 'fr-ca' );
$canadian_french_in_english = $cldr->get_language_name( 'fr-ca' , 'en' );
$german_in_german = $cldr->get_language_name( 'de_DE' , 'de-DE' );
$bengali_in_japanese = $cldr->get_language_name( 'bn_BD' , 'ja_JP' );
$us_dollar_symbol_in_simplified_chinese = $cldr->get_currency_symbol( 'USD', 'zh' );
$africa_in_french = $cldr->get_territory_name( '002' );
```

### Switch locales after the object has been created
```
$cldr->set_locale( 'en' );
$us_dollar_in_english = $cldr->get_currency_name( 'USD' );
```

### Get CLDR's supplemental data
```
$telephone_code_in_france = $cldr->get_telephone_code( 'FR' );
```

Tags: i18n, internationalization, L10n, localization, unicode, CLDR
