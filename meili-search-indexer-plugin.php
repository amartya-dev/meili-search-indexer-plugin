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

namespace MeiliSearchIndexer;

// Define constants
// TODO: move to admin
define( 'MEILI_SEARCH_PLUGIN_VERSION', '1.0.0' );
define( 'MEILI_INSTANCE_HOST', 'http://localhost:7700' );
define( 'MEILI_INSTANCE_API_KEY', 'aSampleMasterKey' );
define( 'MEILI_INSTANCE_INDEX', 'kb' );

add_action(
	'plugins_loaded',
	function () {
		// Composer autoload
		if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
			require __DIR__ . '/vendor/autoload.php';
		} else {
			if ( 'local' === wp_get_environment_type() ) {
				wp_die( esc_html( __( 'Please install the Plugin dependencies.', 'meili-search-indexer-plugin' ) ) );
			}
			return;
		}

		// Local dependencies
		require_once 'inc/PostsIndex.php';
		require_once 'inc/IndexRepository.php';
		require_once 'inc/WpRecordsProvider.php';

		// Post Dependencies
		require_once 'inc/Posts/PostRecordsProvider.php';
		require_once 'inc/Posts/PostChangeListener.php';

		$index_repository = new \MeiliSearchIndexer\IndexRepository();
		$meili_client     = new \Meilisearch\Client( MEILI_INSTANCE_HOST, MEILI_INSTANCE_API_KEY );

		// Register articles index
		$records_provider = new \MeiliSearchIndexer\Posts\PostRecordsProvider();
		$index            = new \MeiliSearchIndexer\PostsIndex(
			MEILI_INSTANCE_INDEX,
			$meili_client,
			$records_provider
		);
		new \MeiliSearchIndexer\Posts\PostChangeListener( $index );
		$index_repository->add( MEILI_INSTANCE_INDEX, $index );
	}
);
