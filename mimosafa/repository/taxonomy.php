<?php
namespace mimosafa\WP\Repository;
/**
 * Taxonomy Definition Class
 *
 * @access public
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
class Taxonomy extends Repository {

	/**
	 * @var array
	 */
	private $object_types = [];

	/**
	 * Register Post Type
	 *
	 * @access public
	 */
	public function register() {
		if ( ! $this->_builtin ) {
			Register::taxonomy( $this->name, array_unique( $this->object_types ), $this->args );
		}
		else {
			if ( $this->object_types ) {
				$filter = function( $obj ) {
					if ( post_type_exists( $obj ) ) {
						/**
						 * Built-in Post Type
						 */
						register_taxonomy_for_object_type( $this->name, $obj );
						return false;
					}
					return true;
				};
				$this->object_types = array_filter( $this->object_types, $filter );
				/**
				 * Custom Post Type
				 */
				add_action( 'registered_post_type', function( $post_type ) {
					if ( in_array( $post_type, $this->object_types, true ) ) {
						register_taxonomy_for_object_type( $this->name, $post_type );
					}
				} );
			}
		}
	}

	/**
	 * Attach Other Repositories for Taxonomy
	 *
	 * @access public
	 *
	 * @param  string|mimosafa\Repository\Repos $repository
	 * @return mimosafa\WP\Repository\Taxonomy
	 */
	public function attach( $repository ) {
		if ( $repository = self::getRepository( $repository ) ) {
			if ( $repository instanceof PostType ) {
				$name = $repository->name;
				if ( ! in_array( $name, $this->object_types, true ) ) {
					$this->object_types[] = $repository->name;
				}
			}
		}
		return $this;
	}

}
