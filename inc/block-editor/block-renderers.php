<?php
/**
 * Setup functions
 */

/**
 * Renders the `core/post-template` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the output of the query, structured using the layout defined by the block's inner blocks.
 */
function abandoned_blocks_render_post_template( $attributes, $content, $block ) {
	$page_key = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$page     = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];

	// Use global query if needed.
	$use_global_query = ( isset( $block->context['query']['inherit'] ) && $block->context['query']['inherit'] );
	if ( $use_global_query ) {
		global $wp_query;
		$query = clone $wp_query;
	} else {
		$query_args = \build_query_vars_from_query_block( $block, $page );
		$query      = new WP_Query( $query_args );
	}

	if ( ! $query->have_posts() ) {
		return '';
	}

	if ( \block_core_post_template_uses_featured_image( $block->inner_blocks ) ) {
		\update_post_thumbnail_cache( $query );
	}

	$classnames = '';
	if ( isset( $block->context['displayLayout'] ) && isset( $block->context['query'] ) ) {
		if ( isset( $block->context['displayLayout']['type'] ) && 'flex' === $block->context['displayLayout']['type'] ) {
			$classnames = "is-flex-container columns-{$block->context['displayLayout']['columns']}";
		}
	}

	$wrapper_attributes = \get_block_wrapper_attributes( array( 'class' => $classnames ) );

	$content = '';
	while ( $query->have_posts() ) {
		$query->the_post();

		// Get an instance of the current Post Template block.
		$block_instance = $block->parsed_block;

		// Set the block name to one that does not correspond to an existing registered block.
		// This ensures that for the inner instances of the Post Template block, we do not render any block supports.
		$block_instance['blockName'] = 'core/null';

		// Render the inner blocks of the Post Template block with `dynamic` set to `false` to prevent calling
		// `render_callback` and ensure that no wrapper markup is included.
		$block_content = (
			new WP_Block(
				$block_instance,
				array(
					'postType' => \get_post_type(),
					'postId'   => \get_the_ID(),
				)
			)
		)->render( array( 'dynamic' => false ) );

		// Wrap the render inner blocks in a `li` element with the appropriate post classes.
		$post_classes = implode( ' ', \get_post_class( 'wp-block-post' ) );
		$content     .= '<article class="' . \esc_attr( $post_classes ) . '">' . $block_content . '</article>';
	}

	/*
	 * Use this function to restore the context of the template tags
	 * from a secondary query loop back to the main query loop.
	 * Since we use two custom loops, it's safest to always restore.
	*/
	\wp_reset_postdata();

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content
	);
}

/**
 * Renders the `core/post-featured-image` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the featured image for the current post.
 */
function abandoned_blocks_render_post_featured_image( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}
	$post_ID = $block->context['postId'];

	$show_caption = isset( $attributes['showCaption'] ) && $attributes['showCaption'];
	$is_lightbox  = isset( $attributes['isLightbox'] ) && $attributes['isLightbox'];
	$is_link      = isset( $attributes['isLink'] ) && $attributes['isLink'];
	$size_slug    = isset( $attributes['sizeSlug'] ) ? $attributes['sizeSlug'] : 'post-thumbnail';
	$post_title   = trim( strip_tags( \get_the_title( $post_ID ) ) );
	$attr         = array();

	if ( $is_link ) {
		$attr['alt'] = $post_title;
	}

	$featured_image = \get_the_post_thumbnail( $post_ID, $size_slug, $attr );
	if ( ! $featured_image ) {
		return '';
	}

	if ( $show_caption && ( $caption = \get_the_post_thumbnail_caption( $post_ID ) ) ) {
		$figcaption = sprintf( '<figcaption class="post-media-caption">%s</figcaption>', \wp_kses_post( $caption ) );
	} else {
		$figcaption = '';
	}

	$wrapper_attributes = \get_block_wrapper_attributes();
	if ( $is_link ) {
		$link_target = ! empty( $attributes['linkTarget'] ) ? ' target="' . esc_attr( $attributes['linkTarget'] ) . '"' : '';
		$rel         = $is_lightbox ? 'rel="gallery"' : '';

		$featured_image_src = \get_the_post_thumbnail_url( $post_ID, 'full', $attr );
		$featured_image     = sprintf(
			'<a href="%1$s" target="%2$s" %3$s%4$s>%5$s</a>%6$s',
			$is_lightbox ? $featured_image_src : \get_the_permalink( $post_ID ),
			\esc_attr( $link_target ),
			$rel,
			$is_lightbox ? ' class="foobox post-media-link"' : 'post-media-link',
			$featured_image,
			$figcaption
		);
	}

	$has_width  = ! empty( $attributes['width'] );
	$has_height = ! empty( $attributes['height'] );
	if ( ! $has_height && ! $has_width ) {
		return "<figure $wrapper_attributes>$featured_image</figure>";
	}

	if ( $has_width ) {
		$wrapper_attributes = \get_block_wrapper_attributes( array( 'style' => "width:{$attributes['width']};" ) );
	}

	if ( $has_height ) {
		$image_styles = "height:{$attributes['height']};";
		if ( ! empty( $attributes['scale'] ) ) {
			$image_styles .= "object-fit:{$attributes['scale']};";
		}
		$featured_image = str_replace( 'src=', 'style="' . \esc_attr( $image_styles ) . '" src=', $featured_image );
	}

	return "<figure $wrapper_attributes>$featured_image</figure>";
}

/**
 * Modify render callback
 *
 * @param array  $settings
 * @param string $name
 * @return array $settings
 */
function abandoned_blocks_render_callbacks( $settings, $name ) {
	if ( $name == 'core/post-template' ) {
		$settings['render_callback'] = 'abandoned_blocks_render_post_template';
	} elseif ( $name == 'core/post-featured-image' ) {
		$settings['render_callback'] = 'abandoned_blocks_render_post_featured_image';
	}
	return $settings;
}
\add_filter( 'register_block_type_args', 'abandoned_blocks_render_callbacks', null, 2 );
