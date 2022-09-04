<?php
/**
 * Custom theme functions
 */
namespace AbandonedBlocks;

/**
 * Get all the include files for the theme.
 *
 * @author Debt Collective
 */
function get_theme_include_files() {
	return array(
		'inc/setup.php', // Theme set up. Should be included first.
		'inc/hooks.php', // Load custom filters and hooks.
		'inc/block-editor/block-renderers.php', // Custom dynamic block renderers for this theme.
	);
}

foreach ( \AbandonedBlocks\get_theme_include_files() as $include ) {
	require \trailingslashit( \get_stylesheet_directory() ) . $include;
}
