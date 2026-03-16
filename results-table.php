<?php
/**
 * Plugin Name:       Results Table
 * Description:       Creates a table of results
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Mark Treble
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       results-table
 *
 * @package Gbsra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function gbsra_results_table_block_init() {
	register_block_type( __DIR__ . '/build/results-table' );
}
add_action( 'init', 'gbsra_results_table_block_init' );
