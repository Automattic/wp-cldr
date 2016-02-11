<?php

/* script for pruning unneeded files from https://github.com/unicode-cldr/cldr-json
 *
 * Before executing this script, first run get-cldr-files.sh
 *
*/

include_once 'class.wp-cldr.php';

function remove_directory( $directory ) {
    foreach( glob( "{$directory}/*" ) as $file ) {
        unlink($file);
    }
    rmdir( $directory );
}

// from wpcom as of Feb 2016
$wpcom_locales = array( "af", "als", "am", "ar", "arc", "as", "ast", "av", "ay", "az", "ba", "be", "bg", "bm",
	"bn", "bo", "br", "bs", "ca", "ce", "ckb", "cs", "csb", "cv", "cy", "da", "de", "dv", "dz", "el", "el-po", "en", "en-gb", "eo",
	"es", "et", "eu", "fa", "fi", "fil", "fo", "fr", "fr-be", "fr-ca", "fr-ch", "fur", "fy", "ga", "gd", "gl", "gn", "gu", "he", "hi", "hr", "hu", "hy",
	"ia", "id", "ii", "ilo", "is", "it", "ja", "ka", "km", "kn", "ko", "kk", "ks", "ku", "kv", "ky", "la", "li", "lo", "lv",
	"lt", "mk", "ml", 'mwl', 'mn', 'mr', "ms", "mya", "nah", "nap", "ne", "nds", "nl", "nn", "no", "non", "nv", "oc", "or", "os",
	"pa", "pl", "ps", "pt", "pt-br","qu", "ro", "ru", "rup", "sc", "sd", "si", "sk", "sl", "so", "sq", "sr", "su",
	"sv", "ta", "te", "th", "tl", "tir", "tr", "tt", "ty", "udm", "ug", "uk", "ur", "uz", "vec", "vi", "wa", "xal",
	"yi", "yo", "za", "zh-cn", "zh-tw" );
echo 'wpcom locales -- ' . count( $wpcom_locales ) . "\n";

// from wporg locales.php as of Feb 2016
$wporg_locales = array( 'aa', 'ae', 'af', 'ak', 'am', 'an', 'ar', 'arq', 'ary', 'as', 'ast', 'av', 'ay', 'az',
	'azb', 'az_TR', 'ba', 'bal', 'bcc', 'bel', 'bg_BG', 'bh', 'bi', 'bm', 'bn_BD', 'bo', 'bre', 'bs_BA', 'ca',
	'ce','ceb', 'ch', 'ckb', 'co', 'cr', 'cs_CZ', 'csb', 'cu', 'cv', 'cy', 'da_DK', 'de_DE', 'de_CH', 'dv', 'dzo',
	'ee', 'el-po', 'el', 'art_xemoji', 'en_US', 'en_AU', 'en_CA', 'en_GB', 'en_NZ', 'en_ZA', 'eo', 'es_ES', 'es_AR',
	'es_CL', 'es_CO', 'es_GT', 'es_MX', 'es_PE', 'es_PR', 'es_VE', 'et', 'eu', 'fa_IR', 'fa_AF', 'fuc', 'fi', 'fj',
	'fo', 'fr_FR', 'fr_BE', 'fr_CA', 'fr-ch', 'frp', 'fur', 'fy', 'ga', 'gd', 'gl_ES', 'gn', 'gsw', 'gu', 'ha',
	'haw_US', 'haz', 'he_IL', 'hi_IN', 'hr', 'hu_HU', 'hy', 'ia', 'id_ID', 'ido', 'ike', 'ilo', 'is_IS', 'it_IT',
	'ja', 'jv_ID', 'ka_GE', 'kab', 'kal', 'kin', 'kk', 'km', 'kmr', 'kn', 'ko_KR', 'ks', 'ky_KY', 'la', 'lb_LU',
	'li', 'lin', 'lo', 'lt_LT', 'lv', 'me_ME', 'mg_MG', 'mhr', 'mk_MK', 'ml_IN', 'mn', 'mr', 'mri', 'mrj', 'ms_MY',
	'mwl', 'my_MM', 'ne_NP', 'nb_NO', 'nl_NL', 'nl_BE', 'nn_NO', 'no', 'oci', 'orm', 'ory', 'os', 'pa_IN', 'pl_PL',
	'pt_BR', 'pt_PT', 'ps', 'rhg', 'ro_RO', 'roh', 'ru_RU', 'rue', 'rup_MK', 'sah', 'sa_IN', 'si_LK', 'sk_SK',
	'sl_SI', 'snd', 'so_SO', 'sq', 'sr_RS', 'srd', 'su_ID', 'sv_SE', 'sw', 'szl', 'ta_IN', 'ta_LK', 'tah', 'te', 'tg',
	'th', 'tir', 'tlh', 'tl', 'tr_TR', 'tt_RU', 'tuk', 'twd', 'tzm', 'udm', 'ug_CN', 'uk', 'ur', 'uz_UZ', 'vec', 'vi',
	'wa', 'xmf', 'yi', 'yor', 'zh_CN', 'zh_HK', 'zh-sg', 'zh_TW', 'zh' );
echo 'wporg locales -- ' . count( $wporg_locales ) . "\n";

$wp_locales = array_unique ( array_merge( $wpcom_locales, $wporg_locales ) );
echo 'combined locales -- ' . count( $wp_locales ) . "\n";

$cldr_directories_to_prune = array( 'cldr-localenames-modern/main', 'cldr-numbers-modern/main' );
$files_to_keep = array( 'localeDisplayNames.json', 'territories.json', 'languages.json', 'currencies.json', 'numbers.json' );

// the second parameter, false, tell the class to not use caching which means we can avoid loading wordpress for these tests
$cldr = new WP_CLDR( 'en', false );

foreach ( $wp_locales as $wp_locale ) {
	// work around for inconsistency where Brazilian Portuguese (pt-BR) uses "pt" for its directory name
	if ( 'pt-br' == $wp_locale ) {
		$wp_locales_mapped_to_cldr_directories[] = 'pt';
	} else {
		$wp_locales_mapped_to_cldr_directories[] = $cldr->get_cldr_locale( $wp_locale );
	}
}

foreach ( $cldr_directories_to_prune as $directory ) {
	$dir = "./json/v28.0.2/$directory";
	$cldr_directories = scandir( $dir );
	$deleted_locales = 0;
	$retained_locales = 0;
	$files_pruned_from_retained_locales = 0;

	foreach( $cldr_directories as $cldr_directory ) {
		if ( in_array( $cldr_directory, $wp_locales_mapped_to_cldr_directories ) ) {
			$retained_locales++;
			$locale_directory_files = scandir( "{$dir}/$cldr_directory" );
			foreach( $locale_directory_files as $file ) {
				if ( !in_array( $file, $files_to_keep ) && '.' != $file && '..' != $file ) {
					unlink( "{$dir}/$cldr_directory/$file" );
					$files_pruned_from_retained_locales++;
				}
			}
		} else if ( !in_array( $cldr_directory, array( '.', '..', '.DS_Store') ) ) {
			remove_directory( "$dir/$cldr_directory" );
			$deleted_locales++;
		}
	}

	echo "\n\n$directory:\n";
	echo "  CLDR locales deleted = $deleted_locales\n";
	echo "  CLDR locales retained = $retained_locales\n";
	echo "  files pruned from retained CLDR locales = $files_pruned_from_retained_locales\n";
}
