<?php

namespace MeiliSearchIndexer\Index;

/**
 * Abstract index class
 */
abstract class Index {
	/**
	 * Get the index name
	 *
	 * @return string
	 */
	abstract public function getName();

	/**
	 * Delete the index.
	 */
	public function delete() {
		$this->getMeiliIndex()->delete();
	}

	/**
	 * Remove all the records from the index.
	 */
	public function clear() {
		$this->getMeiliIndex()->deleteAllDocuments();
	}

	/**
	 * Push records
	 *
	 * @param int           $page          The page
	 * @param int           $perPage       Number of records per page
	 * @param callable|null $batchCallback The batch callback (optional)
	 *
	 * @return int the number of records pushed
	 */
	public function pushRecords( $page, $perPage, $batchCallback = null ) {
		$recordsProvider = $this->getRecordsProvider();
		$totalPagesCount = $recordsProvider->getTotalPagesCount( $perPage );

		$records = $recordsProvider->getRecords( $page, $perPage );
		if ( count( $records ) > 0 ) {
			$this->getMeiliIndex()->addDocuments( $records );
		}

		if ( is_callable( $batchCallback ) ) {
			call_user_func( $batchCallback, $records, $page, $totalPagesCount );
		}

		return count( $records );
	}

	/**
	 * Push records for an id
	 *
	 * @param mixed $id The post id
	 *
	 * @return int
	 */
	public function pushRecordsForId( $id ) {
		$records = $this->getRecordsProvider()->getRecordsForId( $id );
		if ( count( $records ) > 0 ) {
			$this->getMeiliIndex()->addDocuments( $records );
		}

		return count( $records );
	}

	/**
	 * Push all records per page
	 *
	 * @param int           $perPage       Number of records per page
	 * @param callable|null $batchCallback Callback for batch (optional)
	 *
	 * @return int
	 */
	public function pushAllRecords( $perPage, $batchCallback = null ) {
		$recordsProvider   = $this->getRecordsProvider();
		$totalPages        = $recordsProvider->getTotalPagesCount( $perPage );
		$totalRecordsCount = 0;
		for ( $page = 1; $page <= $totalPages; ++$page ) {
			$totalRecordsCount += $this->pushRecords( $page, $perPage, $batchCallback );
		}

		return $totalRecordsCount;
	}

	/**
	 * Delete records by ids
	 *
	 * @param array $recordIds List of ids to delete records
	 *
	 * @return int
	 */
	public function deleteRecordsByIds( array $recordIds ) {
		if ( empty( $recordIds ) ) {
			return 0;
		}
		$this->getMeiliIndex()->deleteDocuments( $recordIds );

		return count( $recordIds );
	}

	/**
	 * RE index
	 *
	 * @param bool          $clearExistingRecords Indicates if we need to clear records
	 * @param int           $perPage              Number of records per page
	 * @param callable|null $batchCallback        Batch callback (optional)
	 *
	 * @return int
	 */
	public function reIndex( $clearExistingRecords = true, $perPage = 500, $batchCallback = null ) {
		if ( true === (bool) $clearExistingRecords ) {
			$this->clear();
		}

		return $this->pushAllRecords( $perPage, $batchCallback );
	}

	/**
	 * Get records from provider
	 *
	 * @return RecordsProvider
	 */
	abstract protected function getRecordsProvider();

	/**
	 * Get the meili client
	 *
	 * @return Client
	 */
	abstract protected function getMeiliClient();

	/**
	 * Get the meili index
	 *
	 * @return mix
	 */
	protected function getMeiliIndex() {
		return $this->getMeiliClient()->index( $this->getName() );
	}
}
