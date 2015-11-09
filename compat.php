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
