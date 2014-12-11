<?php

/**
 * Extracts data from CLDR XML for a given array of wpcom localization codes; assumes a copy of the `core` CLDR files in the same directory.
 **/

require "class-cldr-parser.php";

$dir = __DIR__;
if ( ! file_exists( "$dir/core/common/main" ) ) {
	echo ( "CLDR data not found at $dir<br>" );
}

//	includes the newly minted magnificent 15 and English
//	once on MC will be array_merge( WPCom_Languages::get_magnificent_non_en_slugs(), array('zh-cn', 'zh-tw', 'ko', 'ar' ) );
$locales =  array( 'en' , 'es' , 'pt-br', 'de', 'fr', 'he', 'ja', 'it', 'nl', 'ru', 'tr', 'id', 'zh-cn' , 'zh-tw' , 'ko' , 'ar' );

/* $locales =  array("aa", "ae", "af", "ak", "am", "an", "ar", "as", "ast", "av", "ay", "az", "azb", "az-tr", "ba", "bal", "bel",
	"bg", "bh", "bi", "bm", "bn", "bo", "br", "bs", "ca", "ce", "ch", "ckb", "co", "cr", "cs", "csb", "cu", "cv", "cy", "da", "de",
	"de-ch", "dv", "dz", "ee", "el-po", "el", "en", "en-au", "en-ca", "en-gb", "eo", "es-ar", "es-cl", "es-mx", "es-pe", "es-pr",
	"es-ve", "es-co", "es", "et", "eu", "fa", "fa-af", "fuc", "fi", "fj", "fo", "fr", "fr-be", "fr-ca", "fr-ch", "fy", "ga", "gd",
	"gl", "gn", "gsw", "gu", "ha", "haw", "haz", "he", "hi", "hr", "hu", "hy", "ia", "id", "ido", "ike", "ilo", "is", "it", "ja",
	"jv", "ka", "rw", "kk", "km", "kn", "ko", "ks", "ku", "ky", "la", "lb", "li", "lo", "lt", "lv", "me", "mg", "mhr", "mk", "ml",
	"mn", "mr", "mri", "mrj", "ms", "mwl", "mya", "ne", "nb", "nl", "nl-be", "nn", "no", "oc", "os", "pa", "pl", "pt-br", "pt", "ps",
	"rhg", "ro", "ru", "ru-ua", "rue", "rup", "sah", "sa-in", "sd", "si", "sk", "sl", "so", "sq", "sr", "srd", "su", "sv", "sw", "ta",
	"ta-lk", "te", "tg", "th", "tir", "tlh", "tl", "tr", "tt", "tuk", "tzm", "udm", "ug", "uk", "ur", "uz", "vec", "vi", "wa", "xmf",
	"yi", "yo", "zh-cn", "zh-hk", "zh-sg", "zh-tw", "zh"); */

function wpcom_locale_babelfish( $wpcom_locale, $exception_type ) {
	// this is first pass at a master table that tracks exceptions to locale codes and other data
	// a more comprehensive version of $mapped_locales
	// also lays the groundwork for one possible approach to locale fallbacks
	$wpcom_locale_exceptions =  array(
		'ae' => array( 'CLDR' => 'none' ), // Avestan
		'als' => array( 'CLDR' => 'none' ), // Alemannic German (includes gsw, gct, swg, wae)
		'an' => array( 'CLDR' => 'none' ), // Aragonese
		'ar' => array( 'top-country' => 'eg' ),
		'arc' => array( 'CLDR' => 'none' ), // Aramaic
		'ast' => array( 'mysql-collation' => 'utf8_spanish2_ci' ),
		'av' => array( 'CLDR' => 'none' ), // Avaric
		'ay' => array( 'CLDR' => 'none' ), // Aymara
		'az-tr' => array( 'CLDR' => 'none', 'fallback' => 'az' ),
		'azb' => array( 'CLDR' => 'none' ), // South Azerbaijani
		'ba' => array( 'CLDR' => 'none' ), // Bashkir
		'bal' => array( 'CLDR' => 'none' ), // Catalan (Balear)
		'bel' => array( 'CLDR' => 'none' ), // Belarusian
		'bh' => array( 'CLDR' => 'none' ), // Bihari
		'bi' => array( 'CLDR' => 'none' ), // Bislama
		'ce' => array( 'CLDR' => 'none' ), // Chechen
		'ch' => array( 'CLDR' => 'none' ), // Chamorro
		'ckb' => array( 'CLDR' => 'none' ), // Sorani Kurdish
		'co' => array( 'CLDR' => 'none' ), // Corsican
		'cr' => array( 'CLDR' => 'none' ), // Cree
		'cs' => array( 'mysql-collation' => 'utf8_czech_ci' ),
		'csb' => array( 'CLDR' => 'none' ), // Kashubian
		'cu' => array( 'CLDR' => 'none' ), // Church Slavic
		'cv' => array( 'CLDR' => 'none' ), // Chuvash
		'da' => array( 'mysql-collation' => 'utf8_danish_ci' ),
		'de' => array( 'top-country' => 'de', 'google-play' => 'de-DE', 'mysql-collation' => 'utf8_unicode_ci'  ),
		'de-ch' => array( 'CLDR' => 'de_CH', ),
		'dv' => array( 'CLDR' => 'none' ), // Divehi
		'el-po' => array( 'CLDR' => 'none' ), // Greek Polytonic
		'en-au' => array( 'CLDR' => 'en_AU' ),
		'en-ca' => array( 'CLDR' => 'en_CA' ),
		'en-gb' => array( 'CLDR' => 'en_GB' ),
		'eo' => array( 'mysql-collation' => 'utf8_esperanto_ci' ),
		'es' => array( 'google-play' => 'es-ES', 'mysql-collation' => 'utf8_spanish_ci' ),
		'es-ar' => array( 'CLDR' => 'es_AR', 'top-country' => 'ar', 'fallback' => 'es', 'mysql-collation' => 'utf8_spanish_ci' ),
		'es-cl' => array( 'CLDR' => 'es_CL', 'top-country' => 'cl', 'fallback' => 'es', 'mysql-collation' => 'utf8_spanish_ci' ),
		'es-co' => array( 'CLDR' => 'es_CO', 'top-country' => 'co', 'fallback' => 'es', 'mysql-collation' => 'utf8_spanish_ci' ),
		'es-mx' => array( 'CLDR' => 'es_MX', 'top-country' => 'mx', 'fallback' => 'es', 'mysql-collation' => 'utf8_spanish_ci' ),
		'es-pe' => array( 'CLDR' => 'es_PE', 'top-country' => 'pe', 'fallback' => 'es', 'mysql-collation' => 'utf8_spanish_ci' ),
		'es-pr' => array( 'CLDR' => 'es_PR', 'top-country' => 'pr', 'fallback' => 'es', 'mysql-collation' => 'utf8_spanish_ci' ),
		'es-ve' => array( 'CLDR' => 'es_VE', 'top-country' => 've', 'fallback' => 'es', 'mysql-collation' => 'utf8_spanish_ci' ),
		'et' => array( 'mysql-collation' => 'utf8_estonian_ci' ),
		'fa' => array( 'mysql-collation' => 'utf8_persian_ci' ),
		'fa-af' => array( 'CLDR' => 'fa_AF', 'fallback' => 'fa', 'mysql-collation' => 'utf8_persian_ci'  ),
		'fj' => array( 'CLDR' => 'none' ), // Fijian
		'fr' => array( 'top-country' => 'fr', 'google-play' => 'fr-FR' ),
		'fr-be' => array( 'CLDR' => 'fr_BE' , 'fallback' => 'fr' ),
		'fr-ca' => array( 'CLDR' => 'fr_CA' , 'fallback' => 'fr' ),
		'fr-ch' => array( 'CLDR' => 'fr_CH' , 'fallback' => 'fr' ),
		'gl' => array( 'mysql-collation' => 'utf8_spanish2_ci' ),
		'gn' => array( 'CLDR' => 'none' ), // Guarani
		'gsw' => array( 'CLDR' => 'gsw' ),
		'haz' => array( 'CLDR' => 'none' ), // Hazaragi
		'he' => array( 'gtrans' => 'iw' , 'top-country' => 'il' ),
		'hu' => array( 'mysql-collation' => 'utf8_hungarian_ci' ),
		'id' => array( 'top-country' => 'id' ),
		'ido' => array( 'CLDR' => 'none' ), // Ido
		'ike' => array( 'CLDR' => 'none' ), // Inuktitut
		'ilo' => array( 'CLDR' => 'none' ), // Iloko
		'is' => array( 'mysql-collation' => 'utf8_icelandic_ci' ),
		'it' => array( 'google-play' => 'it-IT', 'top-country' => 'it' ),
		'ja' => array( 'top-country' => 'jp' , 'google-play' => 'ja-JP' ),
		'jv' => array( 'CLDR' => 'none' ), // Javanese
		'ko' => array( 'top-country' => 'kr' , 'google-play' => 'ko-KR' ),
		'ku' => array( 'CLDR' => 'none' ), // Kurdish
		'kv' => array( 'CLDR' => 'none' ), // Komi
		'la' => array( 'CLDR' => 'none' ), // Latin
		'lb' => array( 'CLDR' => 'none' ), // Luxembourgish
		'li' => array( 'CLDR' => 'none' ), // Limburgish
		'lt' => array( 'mysql-collation' => 'utf8_lithuanian_ci' ),
		'lv' => array( 'mysql-collation' => 'utf8_latvian_ci' ),
		'me' => array( 'CLDR' => 'none' ), // Montenegrin
		'mhr' => array( 'CLDR' => 'none' ), // Mari (Meadow)
		'mri' => array( 'CLDR' => 'none', 'lang_code_iso_639_1' => 'mi' ), // Maori
		'mrj' => array( 'CLDR' => 'none' ), // Mari (Hill)
		'mwl' => array( 'CLDR' => 'none' ), // Mirandese
		'mya' => array( 'CLDR' => 'none' ), // 
		'nah' => array( 'CLDR' => 'none' ), // Nahuatl
		'nap' => array( 'CLDR' => 'none' ), // Neapolitan 
		'nb' => array( 'mysql-collation' => 'utf8_danish_ci' ),
		'nds' => array( 'CLDR' => 'none' , 'fallback' => 'de'), // Low German
		'nl' => array( 'top-country' => 'nl', 'google-play' => 'nl-NL' ),
		'nl-be' => array( 'CLDR' => 'nl_BE', 'fallback' => 'nl' ),
		'nn' => array( 'mysql-collation' => 'utf8_danish_ci' ),
		'no' => array( 'CLDR' => 'nb', 'mysql-collation' => 'utf8_danish_ci' ), // Norweigan
		'non' => array( 'CLDR' => 'none' ), // Old Norse
		'nv' => array( 'CLDR' => 'none' ), // Navajo
		'oc' => array( 'CLDR' => 'none' ), // Occitan
		'pl' => array( 'google-play' => 'pl-PL', 'mysql-collation' => 'utf8_polish_ci'  ),
		'pt-br' => array( 'CLDR' => 'pt', 'gtrans' => 'pt', 'top-country' => 'br' ),
		'qu' => array( 'CLDR' => 'none' ), // Quechua
		'rhg' => array( 'CLDR' => 'none' ), // Rohingya
		'ro' => array( 'mysql-collation' => 'utf8_romanian_ci' ),
		'ru' => array( 'top-country' => 'ru', 'google-play' => 'ru-RU' ),
		'ru-ua' => array( 'CLDR' => 'ru_UA', 'fallback' => 'ru' ), // Russian (Ukraine)
		'rue' => array( 'CLDR' => 'none', 'fallback' => 'uk' ), // Rusyn
		'rup' => array( 'CLDR' => 'none' ), // Aromanian
		'sa-in' => array( 'CLDR' => 'none' ), // Sanskrit
		'sd' => array( 'CLDR' => 'none' ), // Sindhi
		'si' => array( 'mysql-collation' => 'utf8_sinhala_ci' ),
		'sk' => array( 'mysql-collation' => 'utf8_slovak_ci' ),
		'sl' => array( 'mysql-collation' => 'utf8_slovenian_ci' ),
		'srd' => array( 'CLDR' => 'none' ), // Sardinian
		'su' => array( 'CLDR' => 'none' ), // Sudanese
		'sv' => array( 'google-play' => 'sv-SE', 'mysql-collation' => 'utf8_swedish_ci' ),
		'ta-lk' => array( 'CLDR' => 'none', 'fallback' => 'ta' ), // Tamil (Sri Lanka)
		'tir' => array( 'CLDR' => 'ti' ), // Tigrinya
		'tl' => array( 'CLDR' => 'none' ), // 
		'tlh' => array( 'CLDR' => 'none' ), // Klingon
		'tr' => array( 'top-country' => 'tr', 'google-play' => 'tr-TR', 'mysql-collation' => 'utf8_turkish_ci' ),
		'tt' => array( 'CLDR' => 'none', 'fallback' => 'ru' ), // Tatar
		'tuk' => array( 'CLDR' => 'none' ), // Turkmen
		'ty' => array( 'CLDR' => 'none' ), // Tahitian (also tah)
		'udm' => array( 'CLDR' => 'none' ), // Udmurt
		'vec' => array( 'CLDR' => 'none' ), // Venetian
		'wa' => array( 'CLDR' => 'none' ), // Walloon
		'xal' => array( 'CLDR' => 'none' ), // Kalmyk
		'xmf' => array( 'CLDR' => 'none' ), // Mingrelian
		'yi' => array( 'CLDR' => 'none' ), // Yiddish
		'za' => array( 'CLDR' => 'none' ), // Zhuang
		'zh' => array( 'CLDR' => 'zh_Hans' ), // Zhuang
		'zh-cn' => array( 'CLDR' => 'zh', 'gtrans' => "zh-CN" , 'top-country' => 'cn' , 'google-play' => 'zh-CN' ),
		'zh-hk' => array( 'CLDR' => 'zh_Hant' , 'fallback' => 'zh-tw', 'top-country' => 'hk' ),
		'zh-sg' => array( 'CLDR' => 'zh_Hans' , 'fallback' => 'zh-cn', 'top-country' => 'sg' ),
		'zh-tw' => array( 'CLDR' => 'zh_Hant' , 'fallback' => 'zh-cn', 'gtrans' => "zh-TW" ,'top-country' => 'tw' ),
	);

	if ( isset( $wpcom_locale_exceptions[$wpcom_locale][$exception_type] ) ) {
		return $wpcom_locale_exceptions[$wpcom_locale][$exception_type];
	} else {
		return $wpcom_locale;
	}
}

foreach ( $locales as $locale ) {

	$input = "$dir/core/common/main/" . wpcom_locale_babelfish ( $locale , 'CLDR' ) . ".xml";

	if ( file_exists( $input ) ) {
		$outputFileName = ( "$dir/cldr/cldr-" . $locale . '.php' );
		$s2 = new CLDR_Parser();
		$s2->parse( $input, $outputFileName, $locale );
	} else {
		echo ( "Input file $input not found<br>" );
	}
}
?>