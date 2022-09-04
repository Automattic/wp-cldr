<?php
/** Script for pruning unneeded files from https://github.com/unicode-cldr/cldr-json
 *
 * Before executing this script, first do "bash get-cldr-files.sh"
 *
 * @package wp-cldr
 */

declare( strict_types=1 );

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
$wpcom_locales_with_over_1k_translations_at_september_2022 = [ 'az', 'de', 'de-ch', 'ja', 'id', 'pt-br', 'it', 'he', 'es', 'fr', 'nl', 'tr', 'hu', 'ru', 'ko', 'ar', 'sq', 'fr-ca', 'fa', 'zh-tw', 'zh-cn', 'zh-sg', 'sv', 'ga', 'el', 'fi', 'gl', 'bs', 'pt', 'ca', 'sr', 'hr', 'pl', 'nn', 'el-po', 'da', 'gd', 'th', 'cs', 'eo', 'ckb', 'mya', 'cy', 'ro', 'te', 'no', 'bg', 'sk', 'lt', 'ug', 'fr-be', 'vi', 'nb', 'ms', 'km', 'uk', 'eu', 'ky', 'br', 'es-pr', 'es-mx', 'es-cl', 'fr-ch', 'si', 'ta', 'as', 'mk', 'tl', 'su', 'is', 'et', 'lv', 'af', 'bn', 'oc', 'ur', 'kk', 'ne', 'mwl', 'hi', 'en-gb', 'mhr', 'mn', 'zh', 'sl', 'mr', 'ml', 'so', 'sah', 'fo', 'zh-hk', 'ast', 'skr', 'cv', 'kab', 'uz', 'ka', 'pa', 'gu', 'oci', 'bo', 'bel', 'sw', 'hy', 'ps', 'lo', 'kmr', 'bal', 'me', 'kn', 'kir', 'an', 'dv'];
echo 'wpcom locales -- ' . count( $wpcom_locales_with_over_1k_translations_at_september_2022 ) . "\n";

// Locales from Languages menu of a fresh WordPress.org install in early September 2022.
$wporg_site_languages_menu_at_september_2022 = [ 'en', 'af', 'am', 'arg', 'ar', 'ary', 'as', 'az', 'azb', 'bel', 'bg_BG', 'bn_BD', 'bo', 'bs_BA', 'ca', 'ceb', 'cs_CZ',  'cy', 'da_DK', 'de_CH', 'de_DE', 'de_CH_informal', 'de_DE_formal', 'de_AT', 'dsb', 'dzo', 'el', 'en_AU', 'en_CA', 'en_GB', 'en_NZ', 'en_ZA', 'eo', 'es_AR', 'es_CL', 'es_CR', 'es_CO', 'es_DO', 'es_ES', 'es_EC', 'es_GT', 'es_MX', 'es_PE', 'es_PR', 'es_UY', 'es_VE', 'et', 'eu', 'fa_AF', 'fa_IR', 'fi', 'fr_BE', 'fr_CA', 'fr_FR', 'fur', 'gd', 'gu', 'gl_ES', 'haz', 'he_IL', 'hi_IN', 'hr', 'hsb', 'hu_HU', 'hy', 'id_ID', 'is_IS', 'it_IT', 'ja', 'jv_ID', 'ka_GE', 'kab', 'kk', 'km', 'kn', 'ko_KR', 'ckb', 'lt_LT', 'lv', 'mk_MK', 'ml_IN', 'mn', 'mr', 'ms_MY', 'my_MM', 'nb_NO', 'ne_NP', 'nl_BE', 'nl_NL', 'nl_NL_formal', 'nn_NO', 'oci', 'pa_IN', 'pl_PL', 'ps', 'pt_AO', 'pt_PT_ao90', 'pt_BR', 'pt_PT', 'rhg', 'ro_RO', 'ru_RU', 'sah', 'snd', 'si_LK', 'sk_SK', 'skr', 'sl_SI', 'sq', 'sr_RS', 'sv_SE', 'sw', 'szl', 'ta_IN', 'ta_LK', 'te', 'th', 'tl', 'tr_TR', 'tt_RU', 'tah', 'ug_CN', 'uk', 'ur', 'uz_UZ', 'vi', 'zh_CN', 'zh_TW', 'zh_HK' ];
echo 'wporg locales -- ' . count( $wporg_site_languages_menu_at_september_2022 ) . "\n";

$wp_locales = array_unique( array_merge( $wpcom_locales_with_over_1k_translations_at_september_2022, $wporg_site_languages_menu_at_september_2022 ) );
echo 'combined unique wp locales including English -- ' . count( $wp_locales ) . "\n";

// Load the list of all CLDR locales from its JSON file then delete that file.
$cldr_available_locales_path = __DIR__ . "/data/$cldr_version/availableLocales.json";
$cldr_available_locales_json = json_decode( file_get_contents( $cldr_available_locales_path ), true );
$cldr_available_locales_full = $cldr_available_locales_json['availableLocales']['full'];
unlink( $cldr_available_locales_path );

// Build a list of the CLDR locales that we want to keep, based on mapping from the WP locales.
$cldr_locales_to_keep = [];
foreach ( $wp_locales as $wordpress_locale ) {
	$wp_locale_mapped_to_cldr = $cldr->get_cldr_locale( $wordpress_locale );
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

$locale_files_to_keep = [ 'timeZoneNames.json', 'territories.json', 'languages.json', 'currencies.json' ];
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
echo "files pruned from retained CLDR locales = $files_pruned_from_retained_locales\n\n";

echo "\nWordPress locales without CLDR JSON file:\n";
foreach ( $wp_locales as $wordpress_locale ) {
	$cldr_locale = $cldr->get_cldr_locale( $wordpress_locale );
	$cldr_json_file = $cldr->get_cldr_json_file( $cldr_locale, 'territories' );

	if ( empty( $cldr_json_file ) ) {
		$cldr_locale = strtok( $cldr_locale, '-_' );
		$cldr_json_file = $cldr->get_cldr_json_file( $cldr_locale, 'territories' );
	}

	if ( empty( $cldr_json_file ) ) {
		echo "'$wordpress_locale' -- found no match for CLDR file $cldr_locale\n";
	}
}

$supplemental_files_to_keep = [ 'currencyData.json', 'territoryContainment.json', 'territoryInfo.json', 'weekData.json' ];
$cldr_supplemental_directory = "./data/$cldr_version/supplemental/";
$deleted_supplemental_files = 0;
$retained_supplemental_files = 0;

// Iterate through the CLDR `supplemental` directory, removing files we don't want.
foreach ( new DirectoryIterator( $cldr_supplemental_directory ) as $cldr_supplemental_file ) {
	if ( $cldr_supplemental_file->isDot() ) {
		continue;
	}
	if ( ! in_array( $cldr_supplemental_file->getFileName(), $supplemental_files_to_keep, true ) ) {
		unlink( $cldr_supplemental_file->getPathName() );
		$deleted_supplemental_files++;
		continue;
	}
	$retained_supplemental_files++;
}
echo "\nCLDR supplemental files deleted = $deleted_supplemental_files\n";
echo "CLDR supplemental files retained = $retained_supplemental_files\n";
