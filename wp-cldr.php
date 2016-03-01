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
 *
 * @package    wp-cldr
 */

require_once plugin_dir_path( __FILE__ ) . 'class-wp-cldr.php';
require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

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

	if ( isset( $_GET['locale'] ) && 1 < strlen( sanitize_text_field( wp_unslash( $_GET['locale'] ) ) ) ) {
		$locale = sanitize_text_field( wp_unslash( $_GET['locale'] ) );
	} else {
		$locale = get_locale();
	}
	if ( isset( $_GET['country'] ) && 2 === strlen( sanitize_text_field( wp_unslash( $_GET['country'] ) ) ) ) {
		$country = sanitize_text_field( wp_unslash( $_GET['country'] ) );
	} else {
		$country = 'US';
	}
	$cldr = new WP_CLDR( $locale );
	$default = array(
		array(
			'language' => 'en_US',
			'english_name' => 'English (US)',
			'native_name' => 'English (US)',
		),
	);
	$languages = get_available_languages();
	$translations = wp_get_available_translations();
	$locales = array_merge( $default, $translations );
	$territories = $cldr->get_territories_by_locale();
	?>

	<div class="wrap">
	<h1><?php esc_html_e( 'WP CLDR settings and info', 'wp-cldr' ); ?></h1>
	<?php esc_html_e( 'This plugin provides WordPress developers with easy access to localized country/region names, language names, currency names/symbols/usage, and other localization info from the Unicode Common Locale Data Repository (CLDR). Here are some examples:', 'wp-cldr' );
	echo '<br>'; ?>

	<form method="get" name='locale'>
	<table width="60%">
	<tr>
		<th width="40%" align="left"><label><?php esc_html_e( 'WordPress locales', 'wp-cldr' ); ?></label></th>
		<td>
			<?php
			wp_dropdown_languages( array(
				'name'         => 'locale',
				'id'           => 'locale',
				'selected'     => $locale,
				'languages'    => $languages,
			) );
			?>
		</td>
		<td>
			<input type="hidden" name="page" value="wp-cldr">
			<input type="hidden" name="country" value="<?php esc_attr_e( $country ); ?>">
			<?php submit_button( 'Update examples', 'secondary' ); ?>
		</td>
	</tr>
	</table>
	</form>

	<?php

	esc_html_e( 'WordPress locale:', 'wp-cldr' );
	echo ' <code> ' . esc_html( $locale ) . '</code> ';
	if ( isset( $locales[ $locale ]['native_name'] ) ) {
		echo esc_html( $locales[ $locale ]['native_name'] ) . ' / ' . esc_html( $locales[ $locale ]['english_name'] );
	}
	echo '<br>';

	esc_html_e( 'Mapped to CLDR JSON path:', 'wp-cldr' );
	echo ' <code> ' . esc_html( $cldr->get_cldr_locale( $locale ) ) . '</code> ' . esc_html( $cldr->get_language_name( $locale ) ) . ' / ' . esc_html( $cldr->get_language_name( $locale, 'en' ) ) . '<br>';

	$example_territories = array( 'US', 'CN', '002' );
	esc_html_e( 'Example territory names:', 'wp-cldr' );
	foreach ( $example_territories as $territory ) {
		echo ' <code>' . esc_html( $territory ) . '</code> ' . esc_html( $cldr->get_territory_name( $territory ) );
	}
	echo '<br>';

	$example_languages = array( 'en', 'zh_TW', 'en_ZA' );
	esc_html_e( 'Example language names:', 'wp-cldr' );
	foreach ( $example_languages as $language ) {
		echo ' <code>' . esc_html( $language ) . '</code> ' . esc_html( $cldr->get_language_name( $language ) );
	}
	echo '<br>';

	$example_currencies = array( 'USD', 'JPY', 'ZAR' );
	esc_html_e( 'Example currency names and symbols:', 'wp-cldr' );
	foreach ( $example_currencies as $currency ) {
		echo ' <code>' . esc_html( $currency ) . '</code> ' . esc_html( $cldr->get_currency_name( $currency ) ) . ' / ' . esc_html( $cldr->get_currency_symbol( $currency ) );
	}
	echo '<br>';
?>

	<form method="get" name='country'>
	<table width="60%">
	<tr>
		<th width="40%" align="left"><label><?php esc_html_e( 'Countries', 'wp-cldr' ); ?></label></th>
		<td>
			<select name="country" id="country">
				<?php foreach ( $territories as $slug => $name ) {
					if ( 2 === strlen( $slug ) && 'ZZ' !== $slug ) { ?>
						<option value="<?php esc_attr_e( $slug ); ?>"<?php echo (string) $slug === $country ? ' selected="selected"' : ''; ?>>
							<?php esc_attr_e( $name ); ?>
						</option>
				<?php	}
				} ?>
			</select>
		</td>
		<td>
			<input type="hidden" name="page" value="wp-cldr">
			<input type="hidden" name="locale" value="<?php esc_attr_e( $locale ); ?>">
			<?php submit_button( 'Update examples', 'secondary' ); ?>
		</td>
	</tr>
	</table>
	</form>

	<?php

	esc_html_e( 'Country code:', 'wp-cldr' );
	echo ' <code>' . esc_html( $country ) . '</code> ' . esc_html( $cldr->get_territory_name( $country ) ) . ' / ' . esc_html( $cldr->get_territory_name( $country, 'en' ) ) . '<br>';

	esc_html_e( 'Telephone code:', 'wp-cldr' );
	echo ' <code>' . esc_html( $country ) . '</code> ' . esc_html( $cldr->get_telephone_code( $country ) ) . '<br>';

	esc_html_e( 'First day of week:', 'wp-cldr' );
	echo ' <code>' . esc_html( $country ) . '</code>' . esc_html( $cldr->first_day_of_week( $country ) ) . '<br>';

	esc_html_e( 'Most spoken language:', 'wp-cldr' );
	$most_spoken_language = $cldr->get_top_language_spoken( $country );
	$language_name = $cldr->language_name( $most_spoken_language );
	echo ' <code>' . esc_html( $country ) . '</code>' . esc_html( $most_spoken_language ) . ', ' . esc_html( $language_name ) . '<br>';

	esc_html_e( 'Currency:', 'wp-cldr' );
	$currency_code = $cldr->get_currency_for_country( $country );
	$currency_name = $cldr->get_currency_name( $currency_code );
	echo ' <code>' . esc_html( $country ) . '</code>' . esc_html( $currency_code ) . ', ' . esc_html( $currency_name ) . '<br>';
}
?>
