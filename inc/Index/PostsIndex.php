<?php

namespace MeiliSearchIndexer\Index;

use Meilisearch\Client;

/**
 * Ability to interact with the meilisearch index
 */
class PostsIndex extends Index {
	/**
	 * The index name
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Meilisearch Client
	 *
	 * @var Client
	 */
	private $client;

	/**
	 * The records provider
	 *
	 * @var WpQueryRecordsProvider
	 */
	private $records_provider;

	/**
	 * Initialize the required variables
	 *
	 * @param string            $name             Index name
	 * @param Client            $client           The meiliSearch client
	 * @param WpRecordsProvider $records_provider The records provider
	 */
	public function __construct( $name, Client $client, WpRecordsProvider $records_provider ) {
		$this->name             = $name;
		$this->client           = $client;
		$this->records_provider = $records_provider;
	}

	/**
	 * Get the index name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Delete records fro the post
	 *
	 * @param \WP_Post $post The WordPress post
	 */
	public function deleteRecordsForPost( \WP_Post $post ) {
		$records   = $this->records_provider->getRecordsForPost( $post );
		$recordIds = array();

		foreach ( $records as $record ) {
			if ( ! isset( $record['objectID'] ) ) {
				continue;
			}

			$recordIds[] = $record['objectID'];
		}

		if ( empty( $recordIds ) ) {
			return;
		}

		$this->getMeiliIndex()->deleteDocuments( $recordIds );
	}

	/**
	 * Push index records for the post
	 *
	 * @param \WP_Post $post The WordPress post
	 *
	 * @return int
	 */
	public function pushRecordsForPost( \WP_Post $post ) {
		$records           = $this->records_provider->getRecordsForPost( $post );
		$totalRecordsCount = count( $records );

		if ( empty( $totalRecordsCount ) ) {
			return 0;
		}

		$this->getMeiliIndex()->addDocuments( $records );

		return $totalRecordsCount;
	}

	/**
	 * Get the records provider
	 *
	 * @return RecordsProvider
	 */
	public function getRecordsProvider() {
		return $this->records_provider;
	}

	/**
	 * Get the MeiliClient
	 *
	 * @return Client
	 */
	protected function getMeiliClient() {
		return $this->client;
	}
}
