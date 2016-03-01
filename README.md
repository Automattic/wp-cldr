# wp-cldr

This plugin provides WordPress developers with easy access to localized country/region names, language names, currency names/symbols/usage, and other localization info from the [Unicode Common Locale Data Repository (CLDR)] (http://cldr.unicode.org/).

With the plugin active, WordPress developers will have access across more than 100 WordPress locales to localized data items including:
- localized names for territories including ISO 3166 country codes and UN M.49 region codes
- localized currency names and symbols for ISO 4317 currency codes
- information on currency usage in different countries
- localized language names for ISO 639 language codes
- localized calendar information including the first day of the week in different countries
- telephone codes for different countries

The plugin includes support for high volume application. It includes two layers of caching (in-memory arrays and the WordPress object cache). It is currently used on WordPress.com.

CLDR is a library of localization data managed by Unicode. It emphasizes [common, everyday usage] (http://cldr.unicode.org/translation/country-names) and is available in over 700 language-region locales. It is [updated every six months] (http://cldr.unicode.org/index/downloads) and used by [all major software systems] (http://cldr.unicode.org/#TOC-Who-uses-CLDR-). CLDR data is licensed under [Unicode's data files and software license] (http://unicode.org/copyright.html#Exhibit1) which is on [the list of approved GPLv2 compatible licenses] (https://www.gnu.org/philosophy/license-list.html#Unicode).

The plugin currently includes CLDR data for WordPress.org locales including `ary`, `ar`, `az`, `bg_BG`, `bn_BD`, `bs_BA`, `ca`, `cy`, `da_DK`, `de_CH`, `de_DE`, `de_DE_formal`, `el`, `en_NZ`, `en_ZA`, `en_AU`, `en_GB`, `en_CA`, `eo`, `es_MX`, `es_VE`, `es_CL`, `es_ES`, `es_AR`, `es_PE`, `es_CO`, `et`, `eu`, `fa_IR`, `fi`, `fr_CA`, `fr_BE`, `fr_FR`, `gd`, `gl_ES`, `haz`, `he_IL`, `hi_IN`, `hr`, `hu_HU`, `hy`, `id_ID`, `is_IS`, `it_IT`, `ja`, `ko_KR`, `lt_LT`, `ms_MY`, `my_MM`, `nb_NO`, `nl_NL`, `nn_NO`, `oci`, `pl_PL`, `ps`, `pt_PT`, `pt_BR`, `ro_RO`, `ru_RU`, `sk_SK`, `sl_SI`, `sq`, `sr_RS`, `sv_SE`, `th`, `tl`, `tr_TR`, `ug_CN`, `uk`, `vi`, `zh_TW`, `zh_CN`,

More information in [detailed API documentation] (https://automattic.github.io/wp-cldr/class-WP_CLDR.html).

Testing
The class included a set of PHPUnit tests. To run them, call `phpunit` from the plugin directory.

##

## Examples:
### The default locale is English
```
$cldr = new WP_CLDR();
$territories_in_english = $cldr->territories_by_locale();
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
$canadian_french_in_french = $cldr->language_name( 'fr-ca' );
$canadian_french_in_english = $cldr->language_name( 'fr-ca' , 'en' );
$german_in_german = $cldr->language_name( 'de_DE' , 'de-DE' );
$bengali_in_japanese = $cldr->language_name( 'bn_BD' , 'ja_JP' );
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
$telephone_code_in_france = $cldr->telephone_code( 'FR' );
```

## Links:
* http://cldr.unicode.org/
* http://cldr.unicode.org/index/cldr-spec/json
