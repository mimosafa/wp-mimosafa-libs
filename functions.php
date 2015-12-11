<?php
/**
 * Wrapper Functions
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
if ( ! function_exists( 'mimosafa_settings_page_instance' ) ) {
	/**
	 * Get Settings Page Instance
	 *
	 * @access public
	 *
	 * @param  mimosafa\WP\Settings\Options
	 * @param  array $array
	 * @return mimosafa\WP\Settings\Page
	 */
	function mimosafa_settings_page_instance( $option = null, $array = null ) {
		if ( class_exists( 'mimosafa\\WP\\Settings\\Page' ) ) {
			return new mimosafa\WP\Settings\Page( $option, $array );
		}
	}
}

if ( ! function_exists( 'mimosafa_repository_instance' ) ) {
	/**
	 * @access public
	 *
	 * @param  string       $repository
	 * @param  string       $name
	 * @param  string       $id
	 * @param  array|string $args
	 * @return mimosafa\WP\RepositoryRepository|null
	 */
	function mimosafa_repository_instance( $repository, $name, $id = null, $args = [] ) {
		if      ( $repository === 'post_type' ) { $class = 'PostType'; }
		else if ( $repository === 'taxonomy'  ) { $class = 'Taxonomy'; }
		else if ( $repository === 'role'      ) { $class = 'Role';     }
		if ( isset( $class ) ) {
			$class = 'mimosafa\\WP\\Repository\\' . $class;
			if ( class_exists( $class ) ) {
				if ( $instance = $class::getInstance( $name ) ) {
					if ( $id || $args ) {
						// trigger error
					}
					return $instance;
				}
				return $class::init( $name, $id, $args );
			}
		}
	}
}

if ( ! function_exists( 'mimosafa_post_type_instance' ) ) {
	/**
	 * @access public
	 *
	 * @param  string       $name
	 * @param  string       $id
	 * @param  array|string $args
	 * @return mimosafa\WP\RepositoryRepository|null
	 */
	function mimosafa_post_type_instance( $name, $id = null, $args = [] ) {
		return mimosafa_repository_instance( 'post_type', $name, $id, $args );
	}
}

if ( ! function_exists( 'mimosafa_taxonomy_instance' ) ) {
	/**
	 * @access public
	 *
	 * @param  string       $name
	 * @param  string       $id
	 * @param  array|string $args
	 * @return mimosafa\WP\RepositoryRepository|null
	 */
	function mimosafa_taxonomy_instance( $name, $id = null, $args = [] ) {
		return mimosafa_repository_instance( 'taxonomy', $name, $id, $args );
	}
}

if ( ! function_exists( 'mimosafa_role_instance' ) ) {
	/**
	 * @access public
	 *
	 * @param  string       $name
	 * @param  string       $id
	 * @param  array|string $args
	 * @return mimosafa\WP\RepositoryRepository|null
	 */
	function mimosafa_role_instance( $name, $id = null, $args = [] ) {
		return mimosafa_repository_instance( 'role', $name, $id, $args );
	}
}
