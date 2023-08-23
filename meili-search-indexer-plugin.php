<?php
/**
 * Meili Search Indexer Plugin
 *
 * @package           MeiliSearchIndexer
 * @author            Amartya Gaur
 * @copyright         Copyright 2023 - All rights reserved.
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Meili Search Indexer Plugin
 * Description:       Plugin that allows auto-indexing of articles published
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Tested up to:      6.2.2
 * Text Domain:       meili-search-indexer-plugin
 * Domain Path:       /languages
 * License:           GPL 2.0 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// Define constants
// TODO: move to admin
define( 'MEILI_SEARCH_PLUGIN_VERSION', '1.0.0' );
define( 'MEILI_INSTANCE_HOST', 'http://localhost:7700' );
define( 'MEILI_INSTANCE_API_KEY', 'aSampleMasterKey' );
define( 'MEILI_INSTANCE_INDEX', 'kb' );
