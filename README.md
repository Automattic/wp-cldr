# wp-cldr

WordPress plugin to access localized territory and language names, currency names/symbols, and other localization info. Source is the [JSON distribution] (https://github.com/unicode-cldr/cldr-json) of the [Unicode Common Locale Data Repository (CLDR)] (http://cldr.unicode.org/).

This repository includes [locale display names] (https://github.com/unicode-cldr/cldr-localenames-modern) and [number formatting] (https://github.com/unicode-cldr/cldr-numbers-modern) for the locales currently [used by WordPress.com](https://github.com/Automattic/wp-cldr/blob/master/prune-cldr-files.php#L18) and [used by WordPress.org](https://github.com/Automattic/wp-cldr/blob/master/prune-cldr-files.php#L29), as well as the [core set of supplemental data] (https://github.com/unicode-cldr/cldr-core).

##

## Examples:
### The default locale is English
```
$cldr = new WP_CLDR();
$territories_in_english = $cldr->territories_by_locale();
```

### You can override the default locale per-call by passing in a language slug in the second parameter
```
$germany_in_arabic = $cldr->territory_name( 'DE' , 'ar' );
```

### Use a convenience parameter during instantiation to change the default locale
```
$cldr = new WP_CLDR( 'fr' );
$germany_in_french = $cldr->territory_name( 'DE' );
$us_dollar_in_french = $cldr->currency_name( 'USD' );
$canadian_french_in_french = $cldr->language_name( 'fr-ca' );
$canadian_french_in_english = $cldr->language_name( 'fr-ca' , 'en' );
$german_in_german = $cldr->language_name( 'de_DE' , 'de-DE' );
$bengali_in_japanese = $cldr->language_name( 'bn_BD' , 'ja_JP' );
$us_dollar_symbol_in_simplified_chinese = $cldr->currency_symbol( 'USD', 'zh' );
$africa_in_french = $cldr->territory_name( '002' );
```

### Switch locales after the object has been created
```
$cldr->set_locale( 'en' );
$us_dollar_in_english = $cldr->currency_name( 'USD' );
```

### Get CLDR's supplemental data
```
$telephone_code_in_france = $cldr->telephone_code( 'FR' );
```

## Links:
* http://cldr.unicode.org/
* http://cldr.unicode.org/index/cldr-spec/json
