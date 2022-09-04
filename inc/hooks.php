<?php
/**
 * WP Filter and Action hook functions
 */
namespace AbandonedBlocks;

/**
 * Filter the query
 *
 * @param object $query
 * @return void
 */
function query_posts( object $query ) {
	if ( \is_admin() || ! \is_main_query() ) {
		return $query;
	}
	if ( \is_home() ) {
		$query->set( 'posts_per_page', \get_option( 'posts_per_page', 24 ) );
		$query->set( 'ignore_sticky_posts', true );
        $query->set( 'post_type', array( 'post' ) );
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'DESC' );
	}
    return $query;
}
\add_filter( 'pre_get_posts', __NAMESPACE__ . '\query_posts', 11 );

/**
 * Modify the content
 *
 * @param string $content
 * @return void
 */
function add_content( string $content ) {
    global $wp_query;
    return $content;
}
// \add_filter( 'the_content', __NAMESPACE__ . '\add_content', 11 );