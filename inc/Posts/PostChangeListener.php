<?php

namespace MeiliSearchIndexer\Posts;

use MeiliSearchIndexer\PostsIndex;

/**
 * Listen to post changes and update the index accordingly
 */
class PostChangeListener {
	/**
	 * The post index instance
	 *
	 * @var PostsIndex
	 */
	private $index;

	/**
	 * Declare the post type
	 *
	 * @var string
	 */
	private $post_type = 'post';

	/**
	 * Constructor, accepts the index and add hooks
	 *
	 * @param PostsIndex $index The post index
	 */
	public function __construct( PostsIndex $index ) {
		$this->index = $index;
		\add_action( 'save_post', array( $this, 'pushRecords' ), 10, 2 );
		\add_action( 'before_delete_post', array( $this, 'deleteRecords' ) );
		\add_action( 'wp_trash_post', array( $this, 'deleteRecords' ) );
	}

	/**
	 * Push records to index
	 *
	 * @param int      $post_id The post id
	 * @param \WP_Post $post    The WordPress post
	 */
	public function pushRecords( $post_id, $post ) {
		if ( \wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return;
		}

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		if ( 'publish' !== $post->post_status || ! empty( $post->post_password ) ) {
			return $this->deleteRecords( $post_id );
		}

		$this->index->pushRecordsForPost( $post );
	}

	/**
	 * Delete records for a post
	 *
	 * @param int $post_id the WordPress post id
	 */
	public function deleteRecords( $post_id ) {
		$post = get_post( $post_id );
		if ( $post instanceof \WP_Post && $post->post_type === $this->post_type ) {
			$this->index->deleteRecordsForPost( $post );
		}
	}
}
