# wp-cldr-plugin

WordPress plugin that leverages the Unicode Common Locale Data Repository to provide localized territory, currency, language, etc. names.

## 

## Examples:
### The default locale is English
```
$cldr = new WP_CLDR();
$territories_in_english = $cldr->territories_by_locale( 'en' );
```

### You can override the default locale per-call by passing in a language slug in the second parameter.
```$germany_in_arabic = $cldr->_territory( 'DE' , 'ar' );```

### use a convenience parameter during instantiation to change the default locale
```
$cldr = new WP_CLDR( 'fr' );
$germany_in_french = $cldr->_territory( 'DE' );
$us_dollar_in_french = $cldr->_currency( 'USD' );
$canadian_french_in_french = $cldr->_locale( 'fr-ca' );
$canadian_french_in_english = $cldr->_locale( 'fr-ca', 'en' );
$africa_in_french = $cldr->_region( '002' );
```

### switch locales after the object has been created
```
$cldr->set_locale('en')
$us_dollar_in_english = $cldr->_currency( 'USD' );
```

## Links:
* http://cldr.unicode.org/
* https://github.com/wikimedia/mediawiki-extensions-cldr
