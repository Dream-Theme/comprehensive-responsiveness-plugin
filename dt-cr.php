<?php
/**
 * Plugin Name:       Comprehensive Responsiveness for Block Editor.
 * Requires at least: 6.5
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Dream Theme
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dt-cr
 *
 * @package           DtCr
 */

use DtCr\Plugin;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/plugin.php';

defined( 'ABSPATH' ) || exit;

define( 'DT_CR_FILE', __FILE__ );
define( 'DT_CR_DIR', plugin_dir_path( DT_CR_FILE ) );
define( 'DT_CR_BASE', plugin_basename( DT_CR_FILE ) );
define( 'DT_CR_URL', plugins_url( '/', DT_CR_FILE ) );
define( 'DT_CR_URL_DIST', DT_CR_URL . 'dist/' );
define( 'DT_CR_DIST', DT_CR_DIR . 'dist/' );
define( 'DT_CR_ULR_LIB_ASSETS', DT_CR_URL . 'lib/' );
define( 'DT_CR_BLOCKS_DIR', DT_CR_DIST . 'blocks/' );
define( 'DT_CR_TEMPLATES_DIR', DT_CR_DIR . 'templates/' );
define( 'DT_CR_PLUGIN_ID', 'dt-cr' );

// register uninstall hook inside activate hook
// it has to be on this stage
register_activation_hook( __FILE__, 'dt_cr_plugin_activate' );

function dt_cr_plugin_activate() {
	register_uninstall_hook( __FILE__, array( 'DtCr\Plugin', 'on_uninstall' ) );
}

Plugin::instance();
