<?php
/**
 * WordPress Functions
 *
 * @package WordPress
 */
if ( ! function_exists( 'wp_roles' ) ) {
	/**
	 * wp_roles()
	 *
	 * @since 4.3.0
	 * @see   https://developer.wordpress.org/reference/functions/wp_roles/
	 */
	function wp_roles() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		return $wp_roles;
	}
}

/**
 * YAML parser.
 */
if ( ! function_exists( 'yaml_parse_file' ) ) {
	require_once __DIR__ . '/vendor/mustangostang/spyc/Spyc.php';
	function yaml_parse_file( $filename ) {
		return Spyc::YAMLLoad( $filename );
	}
}
