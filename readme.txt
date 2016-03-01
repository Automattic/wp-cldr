=== wp-cldr ===
Contributors: stuwest, jblz, automattic
Tags: i18n, internationalization, L10n, localization, unicode, CLDR
Requires at least: 4.4
Tested up to: 4.4.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use CLDR localization data in WordPress.

== Description ==

This plugin provides WordPress developers with easy access to localized country/region names, language names, currency names/symbols/usage, and other localization info from the [Unicode Common Locale Data Repository (CLDR)] (http://cldr.unicode.org/).

With the plugin active, WordPress developers will have access across more than 100 WordPress locales to localized data items including:
- localized names for territories including ISO 3166 country codes and UN M.49 region codes
- localized currency names and symbols for ISO 4317 currency codes
- information on currency usage in different countries
- localized language names for ISO 639 language codes
- localized calendar information including the first day of the week in different countries
- telephone codes for different countries

The plugin includes support for high volume applications. It includes two layers of caching (in-memory arrays and the WordPress object cache). It is currently used on WordPress.com.

CLDR is a library of localization data managed by Unicode. It emphasizes [common, everyday usage] (http://cldr.unicode.org/translation/country-names) and is available in over 700 language-region locales. It is [updated every six months] (http://cldr.unicode.org/index/downloads) and used by [all major software systems] (http://cldr.unicode.org/#TOC-Who-uses-CLDR-). CLDR data is licensed under [Unicode's data files and software license] (http://unicode.org/copyright.html#Exhibit1) which is on [the list of approved GPLv2 compatible licenses] (https://www.gnu.org/philosophy/license-list.html#Unicode).

The plugin currently includes CLDR data for WordPress.org locales including `ary`, `ar`, `az`, `bg_BG`, `bn_BD`, `bs_BA`, `ca`, `cy`, `da_DK`, `de_CH`, `de_DE`, `de_DE_formal`, `el`, `en_NZ`, `en_ZA`, `en_AU`, `en_GB`, `en_CA`, `eo`, `es_MX`, `es_VE`, `es_CL`, `es_ES`, `es_AR`, `es_PE`, `es_CO`, `et`, `eu`, `fa_IR`, `fi`, `fr_CA`, `fr_BE`, `fr_FR`, `gd`, `gl_ES`, `haz`, `he_IL`, `hi_IN`, `hr`, `hu_HU`, `hy`, `id_ID`, `is_IS`, `it_IT`, `ja`, `ko_KR`, `lt_LT`, `ms_MY`, `my_MM`, `nb_NO`, `nl_NL`, `nn_NO`, `oci`, `pl_PL`, `ps`, `pt_PT`, `pt_BR`, `ro_RO`, `ru_RU`, `sk_SK`, `sl_SI`, `sq`, `sr_RS`, `sv_SE`, `th`, `tl`, `tr_TR`, `ug_CN`, `uk`, `vi`, `zh_TW`, `zh_CN`,

More information in [detailed API documentation] (https://automattic.github.io/wp-cldr/class-WP_CLDR.html).

== Testing ==
The class included a set of PHPUnit tests. To run them, call `phpunit` from the plugin directory.

== Installation ==

1. Upload the folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Where can I report issues? =

Open up a new issue on Github at https://github.com/Automattic/wp-cldr/issues.

== Changelog ==

= 1.0 (Mar __, 2016) =

* initial release into plugin repo
