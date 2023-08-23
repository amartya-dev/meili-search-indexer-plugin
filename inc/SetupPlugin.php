<?php

namespace MeiliSearchIndexer;

use Meilisearch\Client;
use MeiliSearchIndexer\Index\MeiliSearchIndexRepository;
use MeiliSearchIndexer\Index\PostsIndex;
use MeiliSearchIndexer\Posts\PostChangeListener;
use MeiliSearchIndexer\Posts\PostRecordsProvider;

class SetupPlugin {
	public function __construct() {
		\add_action(
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

				$index_repository = new MeiliSearchIndexRepository();
				$meili_client     = new Client( MEILI_INSTANCE_HOST, MEILI_INSTANCE_API_KEY );

				// Register articles index
				$records_provider = new PostRecordsProvider();
				$index            = new PostsIndex(
					MEILI_INSTANCE_INDEX,
					$meili_client,
					$records_provider
				);
				new PostChangeListener( $index );
				$index_repository->add( MEILI_INSTANCE_INDEX, $index );
			}
		);
	}
}
