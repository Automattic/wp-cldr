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

With the plugin active, WordPress developers will have access across nearly 100 WordPress locales to localized data items including:
- localized names for territories including ISO 3166 country codes and UN M.49 region codes
- localized currency names and symbols for ISO 4317 currency codes
- information on currency usage in different countries
- localized language names for ISO 639 language codes
- localized calendar information including the first day of the week in different countries
- telephone codes for different countries

The plugin includes support for high volume applications. It includes two layers of caching (in-memory arrays and the WordPress object cache). It is currently used on WordPress.com.

CLDR is a library of localization data managed by Unicode. It emphasizes [common, everyday usage] (http://cldr.unicode.org/translation/country-names) and is available in over 700 language-region locales. It is [updated every six months] (http://cldr.unicode.org/index/downloads) and used by [all major software systems] (http://cldr.unicode.org/#TOC-Who-uses-CLDR-). CLDR data is licensed under [Unicode's data files and software license] (http://unicode.org/copyright.html#Exhibit1) which is on [the list of approved GPLv2 compatible licenses] (https://www.gnu.org/philosophy/license-list.html#Unicode).

The plugin currently includes CLDR data for WordPress.org locales including aa, ae, af, ak, am, an, ar, arq, ary, as, ast, av, ay, az, azb, az_TR, ba, bal, bcc, bel, bg_BG, bh, bi, bm, bn_BD, bo, bre, bs_BA, ca, ce,ceb, ch, ckb, co, cr, cs_CZ, csb, cu, cv, cy, da_DK, de_DE, de_CH, dv, dzo, ee, el-po, el, art_xemoji, en_US, en_AU, en_CA, en_GB, en_NZ, en_ZA, eo, es_ES, es_AR, es_CL, es_CO, es_GT, es_MX, es_PE, es_PR, es_VE, et, eu, fa_IR, fa_AF, fuc, fi, fj, fo, fr_FR, fr_BE, fr_CA, fr-ch, frp, fur, fy, ga, gd, gl_ES, gn, gsw, gu, ha, haw_US, haz, he_IL, hi_IN, hr, hu_HU, hy, ia, id_ID, ido, ike, ilo, is_IS, it_IT, ja, jv_ID, ka_GE, kab, kal, kin, kk, km, kmr, kn, ko_KR, ks, ky_KY, la, lb_LU, li, lin, lo, lt_LT, lv, me_ME, mg_MG, mhr, mk_MK, ml_IN, mn, mr, mri, mrj, ms_MY, mwl, my_MM, ne_NP, nb_NO, nl_NL, nl_BE, nn_NO, no, oci, orm, ory, os, pa_IN, pl_PL, pt_BR, pt_PT, ps, rhg, ro_RO, roh, ru_RU, rue, rup_MK, sah, sa_IN, si_LK, sk_SK, sl_SI, snd, so_SO, sq, sr_RS, srd, su_ID, sv_SE, sw, szl, ta_IN, ta_LK, tah, te, tg, th, tir, tlh, tl, tr_TR, tt_RU, tuk, twd, tzm, udm, ug_CN, uk, ur, uz_UZ, vec, vi, wa, xmf, yi, yor, zh_CN, zh_HK, zh-sg, zh_TW, zh.

More information in [detailed API documentation] (https://automattic.github.io/wp-cldr/class-WP_CLDR.html).

Please follow along with or contribute to the development of this plugin at https://github.com/Automattic/wp-cldr.

== Installation ==

1. Upload the folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Where's the menu item / interface? =

This is a developer-focused plugin and does not have an interface. In order to integrate plugin functions into your code, see the API documentation at https://automattic.github.io/wp-cldr.

= Where can I report issues? =

Open up a new issue on Github at https://github.com/Automattic/wp-cldr/issues.

== Changelog ==

= 1.0 (Feb 29, 2016) =

* initial release into plugin repo



<?php
/**
 * Plugin Name: WP CLDR
 * Description: Use CLDR localization data in WordPress.
 * Plugin URI:  https://github.com/Automattic/wp-cldr
 * Author:      Automattic
 * Author URI:  https://automattic.com
 * Version:     1.0
 * Text Domain: wp-cldr
 * Domain Path: /languages/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

require_once plugin_dir_path( __FILE__ ) . 'class.wp-cldr.php';
