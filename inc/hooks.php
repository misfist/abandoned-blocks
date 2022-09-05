<?php
/**
 * WP Filter and Action hook functions
 */
namespace AbandonedBlocks;

/**
 * Filter the query
 *
 * @see https://developer.wordpress.org/reference/hooks/pre_get_posts/
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
		// $query->set( 'ignore_sticky_posts', false );
		$query->set( 'post_type', array( 'post' ) );
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
	}
	return $query;
}
\add_filter( 'pre_get_posts', __NAMESPACE__ . '\query_posts', 11 );

/**
 * Remove text before archive
 *
 * @see https://developer.wordpress.org/reference/hooks/get_the_archive_title/
 *
 * @param string $title
 * @return string $title
 */
function archive_title( $title ) {
	if ( \is_category() ) {
		$title = \single_cat_title( '', false );
	} elseif ( \is_tag() ) {
		$title = \single_tag_title( '', false );
	} elseif ( \is_author() ) {
		$title = '<span class="vcard">' . \get_the_author() . '</span>';
	} elseif ( \is_post_type_archive() ) {
		$title = \post_type_archive_title( '', false );
	} elseif ( \is_tax() ) {
		$title = \single_term_title( '', false );
	}

	return $title;
}
\add_filter( 'get_the_archive_title', __NAMESPACE__ . '\archive_title' );

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
