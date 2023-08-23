<?php
namespace MeiliSearchIndexer\Index;

/**
 * Abstract class implementing the provider interface
 */
abstract class WpRecordsProvider implements RecordsProvider {
	/**
	 * Function to get the total page count
	 *
	 * @param  int $perPage the number of records to be included per page
	 * @return int
	 */
	public function getTotalPagesCount( $perPage ) {
		$results = $this->newQuery( array( 'posts_per_page' => (int) $perPage ) );
		return (int) $results->max_num_pages;
	}

	/**
	 * Function to get records of that particular page
	 *
	 * @param int $page    the page number
	 * @param int $perPage the number of records to be included per page
	 */
	public function getRecords( $page, $perPage ) {
		$query = $this->newQuery(
			array(
				'posts_per_page' => $perPage,
				'paged'          => $page,
			)
		);

		return $this->getRecordsForQuery( $query );
	}

	/**
	 * Function to get records for an id
	 *
	 * @param  mixed $id The blog post id
	 * @return array
	 */
	public function getRecordsForId( $id ) {
		$post = get_post( $id );

		if ( ! $post instanceof \WP_Post ) {
			return array();
		}

		return $this->getRecordsForPost( $post );
	}

	/**
	 * Function to get records for a post
	 *
	 * @param \WP_Post $post the WordPress post
	 * @return array
	 */
	abstract public function getRecordsForPost( \WP_Post $post );

	/**
	 * Function to get default query args.
	 *
	 * @return array
	 */
	abstract protected function getDefaultQueryArgs();

	/**
	 * Function to run a WordPress query
	 *
	 * @param array $args the query args
	 * @return \WP_Query
	 */
	private function newQuery( array $args = array() ) {
		$defaultArgs = $this->getDefaultQueryArgs();

		$args  = array_merge( $defaultArgs, $args );
		$query = new \WP_Query( $args );

		return $query;
	}

	/**
	 * Function to get records for a query
	 *
	 * @param \WP_Query $query the WordPress query
	 * @return array
	 */
	private function getRecordsForQuery( \WP_Query $query ) {
		$records = array();
		foreach ( $query->posts as $post ) {
			$post = get_post( $post );
			if ( ! $post instanceof \WP_Post ) {
				continue;
			}
			$records = array_merge( $records, $this->getRecordsForPost( $post ) );
		}
		return $records;
	}
}
