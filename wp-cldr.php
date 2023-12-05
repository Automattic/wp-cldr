<?php
/**
 * Plugin Name: WP CLDR
 * Description: Gives WordPress developers easy access to localized country, region, language, currency, time zone, and calendar info from the <a href="http://cldr.unicode.org/">Unicode Common Locale Data Repository</a>. See <a href="http://automattic.github.io/wp-cldr/class-WP_CLDR.html">API documentation</a>.
 * Plugin URI:  https://github.com/Automattic/wp-cldr
 * Author:      Automattic
 * Author URI:  https://automattic.com
 * Version:     1.2
 * Text Domain: wp-cldr
 * Domain Path: /languages
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package    wp-cldr
 */

declare( strict_types=1 );

require_once plugin_dir_path( __FILE__ ) . 'class-wp-cldr.php';
require_once ABSPATH . 'wp-admin/includes/translation-install.php';

add_action( 'admin_menu', 'wp_cldr_menu' );

/**
 * Gets the wp-admin menu info.
 */
function wp_cldr_menu() {
	add_options_page(
		'WP CLDR Options',
		'WP CLDR',
		'manage_options',
		'wp-cldr',
		'wp_cldr_settings'
	);
}

/**
 * Gets the settings and examples page for the plugin.
 */
function wp_cldr_settings() {

	// Load the text domain for the plugin (loading here not earlier because translations are only needed for this page).
	load_plugin_textdomain( 'wp-cldr', false, basename( __DIR__ ) . '/languages/' );

	$locale = get_locale();
	if ( isset( $_GET['locale'] ) && 1 < strlen( sanitize_text_field( wp_unslash( $_GET['locale'] ) ) ) ) {
		$locale = sanitize_text_field( wp_unslash( $_GET['locale'] ) );
	}
	$country = 'US';
	if ( isset( $_GET['country'] ) && 2 === strlen( sanitize_text_field( wp_unslash( $_GET['country'] ) ) ) ) {
		$country = sanitize_text_field( wp_unslash( $_GET['country'] ) );
	}

	$cldr = new WP_CLDR( $locale, false );
	$default = [
		[
			'language' => 'en_US',
			'english_name' => 'English (US)',
			'native_name' => 'English (US)',
		],
	];
	$languages = get_available_languages();
	$translations = wp_get_available_translations();
	$locales = array_merge( $default, $translations );
	?>

	<div class="wrap">
	<h1><?php esc_html_e( 'WP CLDR examples and info', 'wp-cldr' ); ?></h1>
	<?php esc_html_e( 'This plugin gives WordPress developers easy access to localized country, region, language, currency, time zone, and calendar info from the Unicode Common Locale Data Repository.', 'wp-cldr' ); ?>
	<h2><?php esc_html_e( 'Examples', 'wp-cldr' ); ?></h2>
	<form method="get" name='locale'>
	<table width="75%">
	<tr>
		<th width="175" align="left"><label><?php esc_html_e( 'WordPress locale:', 'wp-cldr' ); ?></label></th>
		<td width="425" align="left">
			<?php
			wp_dropdown_languages(
				[
					'name' => 'locale',
					'id' => 'locale',
					'selected' => $locale,
					'languages' => $languages,
				]
			);
			?>
		</td>
		<td>
			<input type="hidden" name="page" value="wp-cldr">
			<input type="hidden" name="country" value="<?php esc_attr_e( $country ); ?>">
			<?php submit_button( 'Update examples', 'secondary', '', false ); ?>
		</td>
	</tr>
	</table>
	</form>

	<?php

	echo '<i>';
	esc_html_e( 'WordPress locale code', 'wp-cldr' );
	echo '</i> — ';
	echo '<code> ' . esc_html( $locale ) . '</code> ';
	if ( isset( $locales[ $locale ]['native_name'] ) ) {
		echo esc_html( $locales[ $locale ]['native_name'] );
		if ( $locales[ $locale ]['english_name'] !== $locales[ $locale ]['native_name'] ) {
			echo ' / ' . esc_html( $locales[ $locale ]['english_name'] );
		}
	}
	echo '<br>';

	$mapped_cldr_locale = WP_CLDR::get_best_available_cldr_json_locale( $locale, 'territories' );
	if ( empty( $mapped_cldr_locale ) ) {
		esc_html_e( 'CLDR files not available for this WordPress locale.', 'wp-cldr' );
		echo '<br>';
	} else {
		echo '<i>';
		esc_html_e( 'Mapped to CLDR JSON path', 'wp-cldr' );
		echo '</i> — ';
		$locale_language_name = $cldr->get_language_name( $mapped_cldr_locale );
		echo '<code> ' . esc_html( $mapped_cldr_locale ) . '</code> ' . esc_html( $locale_language_name );
		$english_language_name = $cldr->get_language_name( $mapped_cldr_locale, 'en-US' );
		if ( $english_language_name !== $locale_language_name ) {
			echo ' / ' . esc_html( $english_language_name );
		}
		echo '<br>';

		echo '<i>';
		esc_html_e( 'Example country names', 'wp-cldr' );
		echo '</i> —';
		$example_countries = [ 'US', 'CN', 'BR' ];
		foreach ( $example_countries as $example_country ) {
			echo ' <code>' . esc_html( $example_country ) . '</code> ' . esc_html( $cldr->get_territory_name( $example_country ) );
		}
		echo '<br>';

		echo '<i>';
		esc_html_e( 'Example region names', 'wp-cldr' );
		echo '</i> —';
		$example_regions = [ '001', '142', '419' ];
		foreach ( $example_regions as $example_region ) {
			echo ' <code>' . esc_html( $example_region ) . '</code> ' . esc_html( $cldr->get_territory_name( $example_region ) );
		}
		echo '<br>';

		echo '<i>';
		esc_html_e( 'Example language names', 'wp-cldr' );
		echo '</i> —';
		$example_languages = [ 'en_US', 'zh_TW', 'pt_BR' ];
		foreach ( $example_languages as $language ) {
			echo ' <code>' . esc_html( $language ) . '</code> ' . esc_html( $cldr->get_language_name( $language ) );
		}
		echo '<br>';

		echo '<i>';
		esc_html_e( 'Example currency names / symbols', 'wp-cldr' );
		echo '</i> —';
		$example_currencies = [ 'USD', 'JPY', 'ZAR' ];
		foreach ( $example_currencies as $currency ) {
			echo ' <code>' . esc_html( $currency ) . '</code> ' . esc_html( $cldr->get_currency_name( $currency ) ) . ' / ' . esc_html( $cldr->get_currency_symbol( $currency ) );
		}
		echo '<br>';
		echo '<i>';
		esc_html_e( 'Example time zone cities', 'wp-cldr' );
		echo '</i> —';
		$example_time_zones = [ 'America/Los_Angeles', 'Asia/Hong_Kong', 'Europe/Paris' ];
		foreach ( $example_time_zones as $city ) {
			echo ' <code>' . esc_html( $city ) . '</code> ' . esc_html( $cldr->get_time_zone_city( $city ) );
		}
		echo '<br>';
	}

	?>

	<br>
	<form method="get" name='country'>
	<table width="75%">
	<tr>
		<th width="175" align="left"><label><?php esc_html_e( 'Country:', 'wp-cldr' ); ?></label></th>
		<td width="425" align="left">
			<select name="country" id="country">
				<?php
				foreach ( $cldr->get_territories() as $slug => $name ) {
					if ( 2 === strlen( $slug ) && 'ZZ' !== $slug ) {
						?>
						<option value="<?php esc_attr_e( $slug ); ?>"<?php echo (string) $slug === $country ? ' selected="selected"' : ''; ?>>
							<?php esc_attr_e( $name ); ?>
						</option>
						<?php
					}
				}
				?>
			</select>
		</td>
		<td>
			<input type="hidden" name="page" value="wp-cldr">
			<input type="hidden" name="locale" value="<?php esc_attr_e( $locale ); ?>">
			<?php submit_button( 'Update examples', 'secondary', '', false ); ?>
		</td>
	</tr>
	</table>
	</form>

	<?php

	echo '<i>';
	esc_html_e( 'Country code', 'wp-cldr' );
	echo '</i> — ';
	$locale_territory_name = $cldr->get_territory_name( $country );
	echo '<code>' . esc_html( $country ) . '</code> ' . esc_html( $locale_territory_name );
	$english_territory_name = $cldr->get_territory_name( $country, 'en-US' );
	if ( $english_territory_name !== $locale_territory_name ) {
		echo ' / ' . esc_html( $cldr->get_territory_name( $country, 'en-US' ) );
	}
	echo '<br>';

	echo '<i>';
	esc_html_e( 'First day of week', 'wp-cldr' );
	echo '</i> — ';
	echo esc_html( $cldr->get_first_day_of_week( $country ) ) . '<br>';

	echo '<i>';
	esc_html_e( 'Most spoken language', 'wp-cldr' );
	echo '</i> — ';
	$most_spoken_language = $cldr->get_most_spoken_language( $country );
	$language_name = $cldr->get_language_name( $most_spoken_language );
	echo '<code>' . esc_html( $most_spoken_language ) . '</code> / ' . esc_html( $language_name ) . '<br>';

	echo '<i>';
	esc_html_e( 'Currency', 'wp-cldr' );
	echo '</i> — ';
	$currency_code = $cldr->get_currency_for_country( $country );
	$currency_name = $cldr->get_currency_name( $currency_code );
	echo '<code>' . esc_html( $currency_code ) . '</code> / ' . esc_html( $currency_name ) . '<br>';

	echo '<i>';
	esc_html_e( 'Population', 'wp-cldr' );
	echo '</i> — ';
	$territory_info = $cldr->get_territory_info( $country );
	$population = $territory_info['_population'];
	if ( class_exists( 'NumberFormatter' ) ) {
		// Use locale formatting rules.
		$fmt = new NumberFormatter( $locale, NumberFormatter::DECIMAL );
		echo esc_html( $fmt->format( $population ) ) . '<br>';
	} else {
		// Use default formatting rules.
		echo esc_html( number_format( $population ) );
	}
	?>
	<h2><?php esc_html_e( 'Version information', 'wp-cldr' ); ?></h2>
	<?php
	$plugin_info = get_plugin_data( __FILE__ );
	?>
	<ul>
		<li><?php echo esc_html( sprintf( __( 'WP CLDR plugin: %s', 'wp-cldr' ), $plugin_info['Version'] ) ); ?></li>
		<li><?php echo esc_html( sprintf( __( 'CLDR: %s', 'wp-cldr' ), WP_CLDR::CLDR_VERSION ) ); ?></li>
	</ul>
	<h2><?php esc_html_e( 'External links', 'wp-cldr' ); ?> <span class="dashicons dashicons-external"></span></h2>
	<ul>
		<li><a href="http://cldr.unicode.org/" target="_blank">Unicode Common Locale Data Repository</a></li>
		<li><a href="https://github.com/Automattic/wp-cldr" target="_blank"><?php esc_html_e( 'WP CLDR plugin GitHub repo', 'wp-cldr' ); ?></a></li>
		<li><a href="https://automattic.github.io/wp-cldr/class-WP_CLDR.html" target="_blank"><?php esc_html_e( 'WP CLDR plugin detailed API documentation', 'wp-cldr' ); ?></a></li>
	</ul>
	<?php
}
