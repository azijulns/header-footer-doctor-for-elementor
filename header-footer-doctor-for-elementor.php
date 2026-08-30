<?php

/**
 * Plugin Name:       HeaderFooterFlow for Elementor
 * Description:       Build site headers and footers with Elementor and assign them with per-page display rules. Made for the Hello Elementor theme.
 * Version:           1.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * Author:            Azijul Haque
 * Author URI:        https://profiles.wordpress.org/azijulhaque076/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       header-footer-doctor-for-elementor
 * Domain Path:       /languages
 *
 * Elementor tested up to:     4.2.3
 * Elementor Pro tested up to: 4.2.3
 *
 * HeaderFooterFlow for Elementor is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 2 of the License,
 * or (at your option) any later version.
 *
 * @package   HeaderFooterFlow
 * @author    Azijul Haque <azijulblackdev@gmail.com>
 * @copyright 2026 Azijul Haque
 * @license   GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin version. Keep in sync with the `Version` header and readme `Stable tag`.
 *
 * @var string
 */
define( 'HFFLOW_VERSION', '1.1.0' );

/**
 * Absolute path to the main plugin file.
 *
 * @var string
 */
define( 'HFFLOW_FILE', __FILE__ );

/**
 * Absolute filesystem path to the plugin directory, with trailing slash.
 *
 * @var string
 */
define( 'HFFLOW_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Public URL to the plugin directory, with trailing slash.
 *
 * @var string
 */
define( 'HFFLOW_URL', plugin_dir_url( __FILE__ ) );

/**
 * Minimum Elementor version required by this plugin.
 *
 * @var string
 */
define( 'HFFLOW_MIN_ELEMENTOR_VERSION', '3.5.0' );

/**
 * Theme template slug this plugin integrates with.
 *
 * @var string
 */
define( 'HFFLOW_SUPPORTED_THEME', 'hello-elementor' );

require_once HFFLOW_DIR . 'includes/class-plugin.php';

/**
 * Retrieve the plugin singleton, booting it on first call.
 *
 * @since 1.0.0
 *
 * @return \HFFlow\Plugin The shared plugin instance.
 * @api
 */
function hfflow(): \HFFlow\Plugin {
	return \HFFlow\Plugin::instance();
}

hfflow();
