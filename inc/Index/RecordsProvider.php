<?php
/**
 * An interface for records
 *
 * @package MeiliSearchIndexer
 */

namespace MeiliSearchIndexer\Index;

interface RecordsProvider {
	/**
	 * A function to provide the number of pages based on the number of records
	 * to be included per page
	 *
	 * @param int $perPage The number of records to include per page
	 *
	 * @return int the total pages
	 */
	public function getTotalPagesCount( $perPage );

	/**
	 * The function to get the records for a given page
	 *
	 * @param int $page    the page number
	 * @param int $perPage number of records per page
	 *
	 * @return array
	 */
	public function getRecords( $page, $perPage );

	/**
	 * Function to get records for a given id
	 *
	 * @param mixed $id the id
	 *
	 * @return array
	 */
	public function getRecordsForId( $id );
}
