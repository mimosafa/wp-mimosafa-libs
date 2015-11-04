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
		Register::taxonomy( $this->name, array_unique( $this->object_types ), $this->args );
	}

	/**
	 * Object Type(s)
	 *
	 * @param  string|array $object_type
	 * @return mimosafa\WP\Repository\Taxonomy
	 */
	public function object_type( $object_type ) {
		$object_type = (array) $object_type;
		foreach ( $object_type as &$type ) {
			if ( $instance = PostType::getInstance( $type ) ) {
				$type = $instance->name;
			}
		}
		$this->object_type = array_merge( $this->object_type, $object_type );
		return $this;
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
		if ( $repository = self::getRepos( $repository ) ) {
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
