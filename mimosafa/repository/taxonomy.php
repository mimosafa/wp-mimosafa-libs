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
	 * @var boolean
	 */
	protected $_builtin = false;

	/**
	 * @var array
	 */
	private $object_types = [];

	/**
	 * Constructor
	 *
	 * @access protected
	 *
	 * @uses   mimosafa\WP\Repository\Repository::__construct()
	 *
	 * @param  string      $name
	 * @param  null|string $label
	 * @param  null|string $type
	 * @param  array       $args
	 */
	protected function __construct( $name, $label, $type, Array $args ) {
		if ( isset( self::$builtins[$name] ) ) {
			if ( self::$builtins[$name] === 'Taxonomy' ) {
				$this->_builtin = true;
			}
			else {
				unset( self::$instances[$name] );
				return;
			}
		}
		parent::__construct( $name, $label, $type, $args );
	}

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
				add_action( 'registered_post_type', function( $post_type ) {
					if ( in_array( $post_type, $this->object_types, true ) ) {
						register_taxonomy_for_object_type( $this->name, $post_type );
					}
				} );
			}
		}
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
