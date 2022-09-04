<?php
/**
 * Setup functions
 */
namespace AbandonedBlocks;

/**
 * Add Google webfonts
 *
 * @return $fonts_url
 */
function fonts_url() {
	if ( ! class_exists( '\WP_Theme_JSON_Resolver_Gutenberg' ) ) {
		return '';
	}

	$theme_data = \WP_Theme_JSON_Resolver_Gutenberg::get_merged_data()->get_settings();
	if ( empty( $theme_data ) || empty( $theme_data['typography'] ) || empty( $theme_data['typography']['fontFamilies'] ) ) {
		return '';
	}

	$font_families = [];
	if ( ! empty( $theme_data['typography']['fontFamilies']['custom'] ) ) {
		foreach( $theme_data['typography']['fontFamilies']['custom'] as $font ) {
			if ( ! empty( $font['google'] ) ) {
				$font_families[] = $font['google'];
			}
		}

	// NOTE: This should be removed once Gutenberg 12.1 lands stably in all environments
	} else if ( ! empty( $theme_data['typography']['fontFamilies']['user'] ) ) {
		foreach( $theme_data['typography']['fontFamilies']['user'] as $font ) {
			if ( ! empty( $font['google'] ) ) {
				$font_families[] = $font['google'];
			}
		}
	// End Gutenberg < 12.1 compatibility patch

	} else {
		if ( ! empty( $theme_data['typography']['fontFamilies']['theme'] ) ) {
			foreach( $theme_data['typography']['fontFamilies']['theme'] as $font ) {
				if ( ! empty( $font['google'] ) ) {
					$font_families[] = $font['google'];
				}
			}
		}
	}
	if ( empty( $font_families ) ) {
		return '';
	}
	return \esc_url_raw( 'https://fonts.googleapis.com/css2?' . implode( '&', array_unique( $font_families ) ) . '&display=swap' );
}

/**
 *
 * Enqueue scripts and styles.
 */
function enqueue_scripts() {
	// Enqueue Google fonts
	\wp_enqueue_style( 'abandoned-fonts', fonts_url(), array(), null );
	\wp_dequeue_style( 'blockbase-ponyfill' );
	\wp_enqueue_style( 'abandoned-styles', \get_stylesheet_directory_uri() . '/build/index.css', array(), \wp_get_theme()->get( 'Version' ) );
	\wp_enqueue_script( 'abandoned-scripts', \get_stylesheet_directory_uri() . '/build/index.js', array(), \wp_get_theme()->get( 'Version' ), true );
}
\add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts', 11 );
