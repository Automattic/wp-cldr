=== WP CLDR ===
Contributors: stuwest, jblz, automattic
Tags: i18n, internationalization, L10n, localization, unicode, CLDR
Requires at least: 4.4
Tested up to: 4.4.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP CLDR makes it easier to localize a website, store, or blog by giving developers convenient access to the Common Locale Data Repository.

== Description ==

This plugin provides WordPress developers with easy access to localized country/region names, language names, currency names/symbols/usage, and other localization info from the [Unicode Common Locale Data Repository (CLDR)](http://cldr.unicode.org/).

With the plugin active, WordPress developers will have access across more than 100 WordPress locales to localized data items including:

* Localized names for territories including ISO 3166 country codes and UN M.49 region codes.
* Localized currency names and symbols for ISO 4317 currency codes.
* Information on currency usage in different countries.
* Localized language names for ISO 639 language codes.
* Localized calendar information including the first day of the week in different countries.
* Telephone codes for different countries.
* See various functions of the class in action on the plugin's settings page.

CLDR is a library of localization data coordinated by Unicode. It emphasizes [common, everyday usage](http://cldr.unicode.org/translation/country-names) and is available in over 700 language-region locales. It is [updated every six months](http://cldr.unicode.org/index/downloads) and used by [all major software systems](http://cldr.unicode.org/#TOC-Who-uses-CLDR-). CLDR data is licensed under [Unicode's data files and software license](http://unicode.org/copyright.html#Exhibit1) which is on [the list of approved GPLv2 compatible licenses](https://www.gnu.org/philosophy/license-list.html#Unicode).

More information in the [detailed API documentation](https://automattic.github.io/wp-cldr/class-WP_CLDR.html).

== Installation ==

1. Upload the folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. See the plugin in action via its settings page.
1. Build CLDR data into your site by using [functions in the API documentation](https://automattic.github.io/wp-cldr/class-WP_CLDR.html)

== Frequently Asked Questions ==

= What locales are included? =

The plugin ships with JSON files for over 100 WordPress locales including `ary`, `ar`, `az`, `bg_BG`, `bn_BD`, `bs_BA`, `ca`, `cy`, `da_DK`, `de_CH`, `de_DE`, `de_DE_formal`, `el`, `en_NZ`, `en_ZA`, `en_AU`, `en_GB`, `en_CA`, `eo`, `es_MX`, `es_VE`, `es_CL`, `es_ES`, `es_AR`, `es_PE`, `es_CO`, `et`, `eu`, `fa_IR`, `fi`, `fr_CA`, `fr_BE`, `fr_FR`, `gd`, `gl_ES`, `he_IL`, `hi_IN`, `hr`, `hu_HU`, `hy`, `id_ID`, `is_IS`, `it_IT`, `ja`, `ko_KR`, `lt_LT`, `ms_MY`, `my_MM`, `nl_NL`, `nn_NO`, `pl_PL`, `ps`, `pt_PT`, `pt_BR`, `ro_RO`, `ru_RU`, `sk_SK`, `sl_SI`, `sq`, `sr_RS`, `sv_SE`, `th`, `tl`, `tr_TR`, `ug_CN`, `uk`, `vi`, `zh_TW`, `zh_CN`.

= Is there testing? =

Yes! The class includes a suite of PHPUnit tests. To run them, call `phpunit` from the plugin directory.

= Can the plugin handle high volume? =

The plugin includes two layers of caching (in-memory arrays and the WordPress object cache) and is designed for high volume use. It is currently used on WordPress.com.

= Where do the JSON files come from? =

The scripts used to collect the JSON files are included in the repo. A bash script `get-cldr-files.sh` uses wget to collect the files from [Unicode's reference distribution of CLDR JSON on Github](http://cldr.unicode.org/index/cldr-spec/json); a command-line PHP script `prune-cldr-files.php` removes unneeded locales and locale files from that download.

= Where can I report issues? =

Open up a new issue on Github at https://github.com/Automattic/wp-cldr/issues. We love pull requests!

== Screenshots ==

1. The plugin's settings page shows the supported locales and some of the available data items.

== Changelog ==

= 1.0 (Mar __, 2016) =

* initial release into plugin repo
