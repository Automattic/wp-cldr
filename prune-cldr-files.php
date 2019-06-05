<?php
/** Script for pruning unneeded files from https://github.com/unicode-cldr/cldr-json
 *
 * Before executing this script, first do "bash get-cldr-files.sh"
 *
 * @package wp-cldr
 */

require_once 'class-wp-cldr.php';

// The second parameter, false, tell the class to not use caching which means we can avoid loading WordPress.
$cldr = new WP_CLDR( 'en', false );
$cldr_version = WP_CLDR::CLDR_VERSION;

/**
 * Recursively removes a directory and its contents.
 *
 * @param string $directory A directory or file path.
 */
function remove_directory( $directory ) {
	foreach ( glob( "$directory/*" ) as $file ) {
		unlink( $file );
	}
	rmdir( $directory );
}

// WordPress.com locales with over 1k translations as of April 2015.
$wpcom_locales_with_over_1k_translations_at_march_2015 = [ 'az', 'de', 'ja', 'id', 'pt-br', 'it', 'he', 'es', 'fr', 'nl', 'tr', 'hu', 'ru', 'ko', 'ar', 'sq', 'fr-ca', 'fa', 'zh-tw', 'zh-cn', 'sv', 'ga', 'el', 'fi', 'gl', 'bs', 'pt', 'ca', 'sr', 'hr', 'pl', 'nn', 'el-po', 'da', 'gd', 'th', 'cs', 'eo', 'ckb', 'mya', 'cy', 'ro', 'te', 'no', 'bg', 'sk', 'lt', 'ug', 'fr-be', 'vi', 'nb', 'ms', 'km', 'uk', 'eu', 'ky', 'br', 'es-pr', 'fr-ch', 'si', 'ta', 'as', 'mk', 'tl', 'su', 'is', 'et', 'lv', 'af', 'bn', 'oc', 'ur', 'kk', 'ne', 'mwl', 'hi', 'en-gb', 'mhr', 'mn', 'zh', 'sl', 'mr', 'ml', 'so', 'sah', 'fo', 'zh-hk', 'ast' ];
echo 'wpcom locales -- ' . count( $wpcom_locales_with_over_1k_translations_at_march_2015 ) . "\n";

// Locales from Languages menu of a fresh WordPress.org install in early March 2016.
$wporg_site_languages_menu_at_march_2016 = [ 'en', 'ar', 'ary', 'az', 'azb', 'bg_BG', 'bn_BD', 'bs_BA', 'ca', 'ceb', 'cy', 'da_DK', 'de_CH', 'de_DE', 'de_DE_formal', 'el', 'en_AU', 'en_CA', 'en_GB', 'en_NZ', 'en_ZA', 'eo', 'es_AR', 'es_CL', 'es_CO', 'es_ES', 'es_GT', 'es_MX', 'es_PE', 'es_VE', 'et', 'eu', 'fa_IR', 'fi', 'fr_BE', 'fr_CA', 'fr_FR', 'gd', 'gl_ES', 'haz', 'he_IL', 'hi_IN', 'hr', 'hu_HU', 'hy', 'id_ID', 'is_IS', 'it_IT', 'ja', 'ka_GE', 'ko_KR', 'lt_LT', 'ms_MY', 'my_MM', 'nb_NO', 'nl_NL', 'nl_NL_formal', 'nn_NO', 'oci', 'pl_PL', 'ps', 'pt_BR', 'pt_PT', 'ro_RO', 'ru_RU', 'sk_SK', 'sl_SI', 'sq', 'sr_RS', 'sv_SE', 'th', 'tl', 'tr_TR', 'ug_CN', 'uk', 'vi', 'zh_CN', 'zh_TW' ];
echo 'wporg locales -- ' . count( $wporg_site_languages_menu_at_march_2016 ) . "\n";

$wp_locales = array_unique( array_merge( $wpcom_locales_with_over_1k_translations_at_march_2015, $wporg_site_languages_menu_at_march_2016 ) );
echo 'combined unique wp locales including English -- ' . count( $wp_locales ) . "\n";

// Load the list of all CLDR locales from its JSON file then delete that file.
$cldr_available_locales_path = __DIR__ . "/data/$cldr_version/availableLocales.json";
$cldr_available_locales_json = json_decode( file_get_contents( $cldr_available_locales_path ), true );
$cldr_available_locales_full = $cldr_available_locales_json['availableLocales']['full'];
unlink( $cldr_available_locales_path );

// Build a list of the CLDR locales that we want to keep, based on mapping from the WP locales.
foreach ( $wp_locales as $wp_locale ) {
	$wp_locale_mapped_to_cldr = $cldr->get_cldr_locale( $wp_locale );
	if ( in_array( $wp_locale_mapped_to_cldr, $cldr_available_locales_full, true ) ) {
		$cldr_locales_to_keep[] = $wp_locale_mapped_to_cldr;
	} else {
		// If there's no language-country locale CLDR file, try falling back to a language-only CLDR file.
		$wp_locale_mapped_to_language_only_cldr = strtok( $wp_locale_mapped_to_cldr, '-_' );
		if ( in_array( $wp_locale_mapped_to_language_only_cldr, $cldr_available_locales_full, true ) ) {
			$cldr_locales_to_keep[] = $wp_locale_mapped_to_language_only_cldr;
		}
	}
}

// Report some stats on locales.
echo 'combined CLDR JSON file locales -- ' . count( $cldr_locales_to_keep ) . "\n";
$cldr_locales_to_keep = array_unique( $cldr_locales_to_keep );
echo 'combined unique CLDR JSON file locales -- ' . count( $cldr_locales_to_keep ) . "\n\n";
asort( $cldr_locales_to_keep );

$locale_files_to_keep = [ 'timeZoneNames.json', 'ca-generic.json', 'dateFields.json', 'territories.json', 'languages.json', 'currencies.json', 'numbers.json' ];
$cldr_json_directory = "./data/$cldr_version/main/";
$deleted_locales = 0;
$retained_locales = 0;
$files_pruned_from_retained_locales = 0;

// Iterate through the CLDR `main` directory, removing locales and files we don't want.
foreach ( new DirectoryIterator( $cldr_json_directory ) as $cldr_main_item ) {
	if ( $cldr_main_item->isDot() ) {
		continue;
	}
	if ( ! in_array( $cldr_main_item->getFileName(), $cldr_locales_to_keep, true ) ) {
		remove_directory( $cldr_main_item->getPathName() );
		$deleted_locales++;
		continue;
	}
	$retained_locales++;
	foreach ( new DirectoryIterator( "{$cldr_main_item->getPathName()}" ) as $cldr_locales_item ) {
		if ( $cldr_locales_item->isDot() ) {
			continue;
		}
		if ( ! in_array( $cldr_locales_item->getFileName(), $locale_files_to_keep, true ) ) {
			unlink( $cldr_locales_item->getPathName() );
			$files_pruned_from_retained_locales++;
			continue;
		}
	}
}

echo "CLDR locales deleted = $deleted_locales\n";
echo "CLDR locales retained = $retained_locales\n";
echo "files pruned from retained CLDR locales = $files_pruned_from_retained_locales\n";

echo "\nWordPress locales without CLDR JSON file:\n";
foreach ( $wp_locales as $wp_locale ) {
	$cldr_locale = $cldr->get_cldr_locale( $wp_locale );
	$cldr_json_file = $cldr->get_cldr_json_file( $cldr_locale, 'territories' );

	if ( empty( $cldr_json_file ) ) {
		$cldr_locale = strtok( $cldr_locale, '-_' );
		$cldr_json_file = $cldr->get_cldr_json_file( $cldr_locale, 'territories' );
	}

	if ( empty( $cldr_json_file ) ) {
		echo "'$wp_locale' -- found no match for CLDR file $cldr_locale\n";
	}
}
