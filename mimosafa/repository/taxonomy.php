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
	 * Object Instances (Singleton Pattern)
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * Prototypes for Taxonomy
	 *
	 * @var array
	 */
	protected static $prototypes = [
		/**
		 * Category
		 */
		'category' => [
			'hierarchical' => true,
			'public'       => true,
			'show_ui'      => true,
			'show_admin_column' => true
		],

		/**
		 * Post Tag
		 */
		'post_tag' => [
		 	'hierarchical' => false,
			'public'       => true,
			'show_ui'      => true,
			'show_admin_column' => true
		]
	];

	/**
	 * @var array
	 */
	private $object_type = [];

	/**
	 * Register Post Type
	 *
	 * @access public
	 */
	public function register() {
		Register::taxonomy( $this->name, array_unique( $this->object_type ), $this->args );
	}

	/**
	 * Extended: Validate Repository Name
	 *
	 * @access protected
	 *
	 * @param  string $var
	 * @return string|null
	 */
	protected static function validateName( $name ) {
		if ( $name = parent::validateName( $name ) ) {
			if ( strlen( $name ) > 32 || @preg_match( '/[0-9]/', $name ) ) {
				$name = null;
			}
		}
		return $name;
	}

	/**
	 * Object Type(s)
	 *
	 * @param  string|array $object_type
	 * @return mimosafa\WP\Repository\Taxonomy
	 */
	public function object_type( $object_type ) {
		if ( is_array( $object_type ) ) {
			$this->object_type = array_merge( $this->object_type, $object_type );
		}
		else if ( ! is_object( $object_type ) ) {
			$this->object_type[] = $object_type;
		}
		return $this;
	}

}
